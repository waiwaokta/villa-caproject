<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Data tamu
            'guest_name'   => 'required|string|max:255',
            'guest_phone'  => ['required', 'string', 'max:15', 'regex:/^[0-9+\-\s]+$/'],
            'guest_ktp'    => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],

            // Tipe booking
            'wismaID'      => 'required|string|exists:wismas,wismaID',
            'user_type'    => 'required|in:pln,umum',

            // booking_type cuma relevan kalau user_type = umum
            'booking_type' => 'required_if:user_type,umum|nullable|in:perorangan,instansi',

            // Tanggal
            'check_in'     => 'required|date|after_or_equal:today',
            'check_out'    => 'required|date|after:check_in',

            // Kondisional — instansi (umum) butuh data ini
            'inst_name'    => 'required_if:booking_type,instansi|nullable|string|max:255',
            'inst_npwp'    => 'required_if:booking_type,instansi|nullable|string|max:20',

            // Kondisional — PLN butuh employee_id
            'employee_id'  => 'required_if:user_type,pln|nullable|string|max:20',

            // Dokumen — bukti bayar wajib SEMUA kombinasi
            'doc_bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // Dokumen — KTP wajib untuk umum (perorangan & instansi)
            'doc_ktp' => 'required_if:user_type,umum|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // Dokumen — NPWP wajib untuk umum-instansi
            'doc_npwp' => 'required_if:booking_type,instansi|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // Dokumen — ID Card PLN wajib untuk PLN
            'doc_id_pln' => 'required_if:user_type,pln|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // Dokumen — KTP ATAU NPWP untuk PLN (minimal salah satu)
            'doc_ktp_or_npwp_pln' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    /**
     * Validasi tambahan yang tidak bisa diekspresikan lewat rules() biasa —
     * khusus kasus "KTP ATAU NPWP, minimal salah satu" untuk user_type PLN.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->user_type === 'pln') {
                $hasKtp  = $this->hasFile('doc_ktp');
                $hasNpwp = $this->hasFile('doc_npwp');

                if (!$hasKtp && !$hasNpwp) {
                    $validator->errors()->add(
                        'doc_ktp',
                        'Wajib unggah KTP atau NPWP (pilih salah satu) untuk pegawai/pensiunan PLN.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'guest_ktp.size'           => 'Nomor KTP harus 16 digit.',
            'guest_ktp.regex'          => 'Nomor KTP hanya boleh angka.',
            'guest_phone.regex'        => 'Format nomor telepon tidak valid.',
            'doc_ktp.required_if'      => 'Foto KTP wajib diunggah.',
            'doc_npwp.required_if'     => 'Foto NPWP wajib diunggah.',
            'doc_id_pln.required_if'   => 'ID Card Pegawai/Pensiunan PLN wajib diunggah.',
            'doc_bukti_bayar.required' => 'Bukti pelunasan wajib diunggah.',
            'check_in.after_or_equal'  => 'Tanggal check-in tidak boleh sebelum hari ini.',
            'check_out.after'          => 'Tanggal check-out harus setelah check-in.',
        ];
    }
}