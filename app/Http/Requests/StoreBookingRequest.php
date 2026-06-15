<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;    

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ownership check di controller
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
            'booking_type' => 'required|in:perorangan,instansi',

            // Tanggal
            'check_in'     => 'required|date|after_or_equal:today',
            'check_out'    => 'required|date|after:check_in',

            // Kondisional
            'employee_id'  => 'required_if:user_type,pln|nullable|string|max:20',
            'inst_name'    => 'required_if:booking_type,instansi|nullable|string|max:255',
            'inst_npwp'    => 'required_if:booking_type,instansi|nullable|string|max:20',

            // Dokumen wajib
            'doc_ktp'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'doc_bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // Dokumen kondisional
            'doc_id_pln'   => 'required_if:user_type,pln|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'doc_npwp'     => 'required_if:booking_type,instansi|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'guest_ktp.size'          => 'Nomor KTP harus 16 digit.',
            'guest_ktp.regex'         => 'Nomor KTP hanya boleh angka.',
            'guest_phone.regex'       => 'Format nomor telepon tidak valid.',
            'doc_ktp.required'        => 'Foto KTP wajib diunggah.',
            'doc_bukti_bayar.required'=> 'Bukti pembayaran wajib diunggah.',
            'doc_id_pln.required_if'  => 'ID PLN wajib diunggah untuk pegawai PLN.',
            'doc_npwp.required_if'    => 'NPWP wajib diunggah untuk booking instansi.',
            'check_in.after_or_equal' => 'Tanggal check-in tidak boleh sebelum hari ini.',
            'check_out.after'         => 'Tanggal check-out harus setelah check-in.',
        ];
    }
}