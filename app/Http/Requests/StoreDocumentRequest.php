<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ownership check di controller
    }

    public function rules(): array
    {
        return [
            'bookingID'  => 'required|string|exists:bookings,bookingID',
            'file'       => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'File harus berformat JPG, PNG, atau PDF.',
            'file.max'   => 'Ukuran file maksimal 2MB.',
            'doc_type.in'=> 'Tipe dokumen tidak valid.',
        ];
    }
}
