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
            . "Ada tamu baru yang melakukan booking dan menunggu persetujuan Anda.\n\n"
            . "━━━━━━━━━━━━━━━━━\n"
            . "*Kode Booking* : {$booking->bookingID}\n"
            . "*Villa*        : {$booking->villa->name}\n"
            . "*Nama Tamu*    : {$booking->guest_name}\n"
            . "*Check-in*      : " . Carbon::parse($booking->check_in)->format('d M Y') . "\n"
            . "*Check-out*     : " . Carbon::parse($booking->check_out)->format('d M Y') . "\n"
            . "━━━━━━━━━━━━━━━━━\n\n"
            . "Silakan cek dan proses di panel admin.\n\n"
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

    public function sendApproved(Booking $booking): bool
    {
        $message = $this->templateApproved($booking);
        return $this->send($booking->guest_phone, $message);
    }

    public function sendRejected(Booking $booking): bool
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

    private function templateApproved(Booking $booking): string
    {
        return implode("\n", [
            "Halo *{$booking->guest_name}*!",
            "",
            "Terima kasih telah mempercayakan kebutuhan penginapan Anda kepada kami. Kami dengan senang hati menginformasikan bahwa booking Anda telah *DISETUJUI*.",
            "",
            "Berikut detail booking Anda:",
            "━━━━━━━━━━━━━━━━━",
            "*Villa*         : {$booking->villa->name}",
            "*Kode Booking*  : {$booking->bookingID}",
            "*Check-in*      : " . Carbon::parse($booking->check_in)->format('d M Y') . " (mulai pukul 14:00)",
            "*Check-out*     : " . Carbon::parse($booking->check_out)->format('d M Y') . " (sebelum pukul 12:00)",
            "*Lama Menginap* : {$booking->total_nights} malam",
            "*Tanggal Booking*   : {$booking->created_at->format('d M Y, H:i')}",
            "━━━━━━━━━━━━━━━━━",
            "",
            "Mohon tunjukkan kode booking Anda kepada petugas saat tiba di lokasi. Pastikan Anda membawa dokumen identitas yang sesuai dengan data yang telah didaftarkan.",
            "",
            "Atas perhatian dan kepercayaan Anda, kami ucapkan terima kasih. Semoga perjalanan Anda menyenangkan.",
            "",
            "_Layanan Penginapan Terpercaya_",
        ]);
    }

    private function templateRejected(Booking $booking): string
    {
        return implode("\n", [
            "Halo *{$booking->guest_name}*!",
            "",
            "Terima kasih telah mempercayakan kebutuhan penginapan Anda kepada kami. Setelah melalui proses peninjauan, kami mohon maaf menginformasikan bahwa booking Anda *DITOLAK*.",
            "",
            "Berikut detail booking Anda:",
            "━━━━━━━━━━━━━━━━━",
            "*Villa*         : {$booking->villa->name}",
            "*Kode Booking*  : {$booking->bookingID}",
            "*Check-in*      : " . Carbon::parse($booking->check_in)->format('d M Y'),
            "*Check-out*     : " . Carbon::parse($booking->check_out)->format('d M Y'),
            "*Lama Menginap* : {$booking->total_nights} malam",
            "*Tanggal Booking*   : {$booking->created_at->format('d M Y, H:i')}",
            "━━━━━━━━━━━━━━━━━",
            "",
            "*Alasan Penolakan:*",
            "_{$booking->reject_desc}_",
            "",
            "Anda dapat melakukan booking ulang dengan melengkapi persyaratan yang diperlukan. Apabila ada pertanyaan lebih lanjut, silakan hubungi kami.",
            "",
            "Atas perhatian dan pengertian Anda, kami ucapkan terima kasih.",
            "",
            "_Layanan Penginapan Terpercaya_",
        ]);
    }
}