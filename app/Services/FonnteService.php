<?php

namespace App\Services;

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

    public function sendApproved(\App\Models\Booking $booking): bool
    {
        $message = $this->templateApproved($booking);
        return $this->send($booking->guest_phone, $message);
    }

    public function sendRejected(\App\Models\Booking $booking): bool
    {
        $message = $this->templateRejected($booking);
        return $this->send($booking->guest_phone, $message);
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

    private function templateApproved(\App\Models\Booking $booking): string
    {
        return implode("\n", [
            "Halo *{$booking->guest_name}*!",
            "",
            "Terima kasih telah mempercayakan kebutuhan penginapan Anda kepada *Wisma PLN*. Kami dengan senang hati menginformasikan bahwa booking Anda telah *DISETUJUI*.",
            "",
            "Berikut detail booking Anda:",
            "━━━━━━━━━━━━━━━━━━",
            "*Wisma*         : {$booking->wisma->name}",
            "*Kode Booking*  : {$booking->bookingID}",
            "*Check-in*      : " . Carbon::parse($booking->check_in)->format('d M Y'),
            "*Check-out*     : " . Carbon::parse($booking->check_out)->format('d M Y'),
            "*Lama Menginap* : {$booking->total_nights} malam",
            "*Tanggal Booking*   : {$booking->created_at->format('d M Y, H:i')}",
            "━━━━━━━━━━━━━━━━━━",
            "",
            "Mohon tunjukkan kode booking Anda kepada petugas saat tiba di lokasi. Pastikan Anda membawa dokumen identitas yang sesuai dengan data yang telah didaftarkan.",
            "",
            "Atas perhatian dan kepercayaan Anda, kami ucapkan terima kasih. Semoga perjalanan Anda menyenangkan.",
            "",
            "_Wisma PLN - Layanan Penginapan Terpercaya_",
        ]);
    }

    private function templateRejected(\App\Models\Booking $booking): string
    {
        return implode("\n", [
            "Halo *{$booking->guest_name}*!",
            "",
            "Terima kasih telah mempercayakan kebutuhan penginapan Anda kepada *Wisma PLN*. Setelah melalui proses peninjauan, kami mohon maaf menginformasikan bahwa booking Anda *DITOLAK*.",
            "",
            "Berikut detail booking Anda:",
            "━━━━━━━━━━━━━━━━━━",
            "*Wisma*         : {$booking->wisma->name}",
            "*Kode Booking*  : {$booking->bookingID}",
            "*Check-in*      : " . Carbon::parse($booking->check_in)->format('d M Y'),
            "*Check-out*     : " . Carbon::parse($booking->check_out)->format('d M Y'),
            "*Lama Menginap* : {$booking->total_nights} malam",
            "*Tanggal Booking*   : {$booking->created_at->format('d M Y, H:i')}",
            "━━━━━━━━━━━━━━━━━━",
            "",
            "*Alasan Penolakan:*",
            "_{$booking->reject_desc}_",
            "",
            "Anda dapat melakukan booking ulang dengan melengkapi persyaratan yang diperlukan. Apabila ada pertanyaan lebih lanjut, silakan hubungi kami.",
            "",
            "Atas perhatian dan pengertian Anda, kami ucapkan terima kasih.",
            "",
            "_Wisma PLN - Layanan Penginapan Terpercaya_",
        ]);
    }
}