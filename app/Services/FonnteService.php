<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FonnteService
{
    private string $token;
    private string $url;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->url   = config('services.fonnte.url');
    }

    public function sendNewBookingAlert(Booking $booking): void
    {
        $admins = User::where('role', 'admin')
            ->where('notify_new_book', true)
            ->whereNotNull('phone')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $message = $this->buildNewBookingMessage($booking);

        foreach ($admins as $admin) {
            try {
                $this->send($this->normalizePhone($admin->phone), $message);
            } catch (\Exception $e) {
                Log::error('Fonnte gagal kirim notif booking baru ke admin: ' . $e->getMessage());
            }
        }
    }

    private function buildNewBookingMessage(Booking $booking): string
    {
        return "🔔 *Booking Baru Masuk!*\n\n"
            . "Ada tamu baru yang telah melakukan booking villa.\n\n"
            . "━━━━━━━━━━━━━━━\n"
            . "*Kode Booking* : {$booking->bookingID}\n"
            . "*Villa*        : {$booking->villa->name}\n"
            . "*Nama Tamu*    : {$booking->guest_name}\n"
            . "*Check-in*      : " . Carbon::parse($booking->check_in)->format('d M Y') . "\n"
            . "*Check-out*     : " . Carbon::parse($booking->check_out)->format('d M Y') . "\n"
            . "━━━━━━━━━━━━━━━\n\n"
            . "Silakan cek detail di panel admin.\n\n"
            . "_Layanan Penginapan Terpercaya_";
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->url, [
                'target'  => $this->normalizePhone($phone),
                'message' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Fonnte gagal kirim WA', [
                    'phone'    => $phone,
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }

            Log::info('Fonnte WA terkirim', [
                'phone'    => $phone,
                'response' => $response->json(),
            ]);

            return true;

        } catch (\Throwable $e) {
            Log::error('Fonnte exception', [
                'phone'   => $phone,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendPaid(Booking $booking): bool
    {
        return $this->send($booking->guest_phone, $this->templatePaid($booking));
    }

    // ---------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------

    private function normalizePhone(string $phone): string
    {
        // Hapus spasi, strip, tanda plus
        $phone = preg_replace('/[\s\-\+]/', '', $phone);

        // Ganti awalan 0 dengan 62 (format internasional)
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    private function templatePaid(Booking $booking): string
    {
        return implode("\n", [
            "Halo *{$booking->guest_name}*!",
            "",
            "Pembayaran Anda telah kami terima dan booking Anda *TERKONFIRMASI*.",
            "",
            "Berikut detail booking Anda:",
            "━━━━━━━━━━━━━━━",
            "*Villa*         : {$booking->villa->name}",
            "*Kode Booking*  : {$booking->bookingID}",
            "*Check-in*      : " . Carbon::parse($booking->check_in)->format('d M Y') . " (mulai pukul 14:00)",
            "*Check-out*     : " . Carbon::parse($booking->check_out)->format('d M Y') . " (sebelum pukul 12:00)",
            "*Lama Menginap* : {$booking->total_nights} malam",
            "*Total Dibayar* : Rp " . number_format($booking->total_price, 0, ',', '.'),
            "*Dibayar Pada*  : " . $booking->paid_at->timezone('Asia/Jakarta')->format('d M Y, H:i') . " WIB",
            "━━━━━━━━━━━━━━━",
            "",
            "Simpan kode booking Anda. Status dan nota PDF dapat diunduh kapan saja di halaman Cek Booking.",
            "",
            "_Layanan Penginapan Terpercaya_",
        ]);
    }
}