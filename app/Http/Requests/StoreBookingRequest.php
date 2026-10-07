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
            'villaID'     => 'required|string|exists:villas,villaID',

            'guest_name'  => 'required|string|max:255',
            'guest_phone' => ['required', 'string', 'max:15', 'regex:/^[0-9+\-\s]+$/'],

            'check_in'    => 'required|date|after_or_equal:today',
            'check_out'   => 'required|date|after:check_in',

            // Dokumen: hanya KTP, bukti bayar datang dari payment gateway
            'doc_ktp'     => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Validasi tambahan: nomor WhatsApp minimal 10 digit (angka saja, tanpa spasi atau tanda).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $digits = preg_replace('/\D/', '', (string) $this->guest_phone);

            if (strlen($digits) < 10) {
                $validator->errors()->add('guest_phone', 'Nomor WhatsApp minimal 10 digit.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'villaID'     => 'villa',
            'guest_name'  => 'nama lengkap',
            'guest_phone' => 'nomor WhatsApp',
            'check_in'    => 'tanggal check-in',
            'check_out'   => 'tanggal check-out',
            'doc_ktp'     => 'foto KTP',
        ];
    }

    public function messages(): array
    {
        return [
            'required'                => ':attribute wajib diisi.',
            'villaID.exists'          => 'Villa tidak ditemukan.',
            'guest_phone.regex'       => 'Format nomor WhatsApp tidak valid.',
            'check_in.after_or_equal' => 'Tanggal check-in tidak boleh sebelum hari ini.',
            'check_out.after'         => 'Tanggal check-out harus setelah check-in.',
            'doc_ktp.mimes'           => 'Foto KTP harus berformat JPG atau PNG.',
            'doc_ktp.max'             => 'Ukuran foto KTP maksimal 2MB.',
        ];
    }
}