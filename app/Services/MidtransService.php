<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransService
{
    private function baseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    private function client(): PendingRequest
    {
        return Http::withBasicAuth((string) config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->asJson()
            ->timeout(15);
    }

    /**
     * Buat transaksi QRIS. Aman dipanggil berulang, kalau QR masih aktif tidak dibuat ulang.
     */
    public function createQris(Booking $booking): void
    {
        if ($booking->qris_url) {
            return;
        }

        $amount  = (int) round($booking->total_price);
        $orderId = $booking->bookingID . '-' . time(); // unik per percobaan, order_id tidak boleh dipakai ulang

        $res = $this->client()->post($this->baseUrl() . '/v2/charge', [
            'payment_type'        => 'qris',
            'transaction_details' => ['order_id' => $orderId, 'gross_amount' => $amount],
            'item_details'        => [[
                'id'       => $booking->villaID,
                'price'    => $amount,
                'quantity' => 1,
                'name'     => Str::limit('Booking ' . $booking->villa->name, 50, ''),
            ]],
            'customer_details'    => [
                'first_name' => Str::limit($booking->guest_name, 100, ''),
                'phone'      => $booking->guest_phone,
            ],
        ]);

        $data   = $res->json() ?? [];
        $action = collect($data['actions'] ?? [])->firstWhere('name', 'generate-qr-code');

        if (!in_array($data['status_code'] ?? null, ['200', '201'], true) || !$action) {
            Log::error('Midtrans QRIS gagal dibuat', ['booking' => $booking->bookingID, 'response' => $data]);
            throw new \RuntimeException('QRIS gagal dibuat.');
        }

        $booking->update([
            'payment_order_id' => $orderId,
            'qris_url'         => $action['url'],
            'expires_at'       => now()->addMinutes(15), // kunci tanggal diperpanjang sejak QR dibuat
        ]);
    }

    /**
     * Cek status ke Midtrans. Kalau lunas, booking jadi approved. Kalau QR hangus, QR dilepas supaya bisa dibuat ulang.
     */
    public function sync(Booking $booking): void
    {
        if ($booking->status !== 'pending' || !$booking->payment_order_id) {
            return;
        }

        try {
            $res = $this->client()->get($this->baseUrl() . '/v2/' . $booking->payment_order_id . '/status');
        } catch (\Throwable $e) {
            report($e);
            return;
        }

        $data   = $res->json() ?? [];
        $status = $data['transaction_status'] ?? null;
        $paid   = (int) round((float) ($data['gross_amount'] ?? 0));

        if ($status === 'settlement' && $paid === (int) round($booking->total_price)) {
            $this->markPaid($booking);
        } elseif (in_array($status, ['expire', 'cancel', 'deny'], true)) {
            $booking->update(['payment_order_id' => null, 'qris_url' => null]);
        }
    }

    /**
     * Batalkan QR yang belum dibayar di sisi Midtrans (best effort).
     */
    public function cancelQris(Booking $booking): void
    {
        if (!$booking->payment_order_id) {
            return;
        }

        try {
            $this->client()->post($this->baseUrl() . '/v2/' . $booking->payment_order_id . '/cancel');
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function markPaid(Booking $booking): void
    {
        // Kalau pembayaran masuk setelah waktu habis, pastikan tanggalnya belum diambil booking lain
        if ($booking->expires_at?->isPast()) {
            $taken = Booking::where('villaID', $booking->villaID)
                ->where('bookingID', '!=', $booking->bookingID)
                ->locking()
                ->where('check_in', '<', $booking->check_out)
                ->where('check_out', '>', $booking->check_in)
                ->exists();

            if ($taken) {
                Log::critical('Pembayaran masuk tapi tanggal sudah terambil', ['booking' => $booking->bookingID]);
                return;
            }
        }

        $booking->update(['status' => 'approved', 'paid_at' => now()]);
    }
}