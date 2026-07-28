<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('navbar.kontak');
    }

    public function store(StoreContactRequest $request)
    {
        Mail::to(config('mail.contact_email_to'))
            ->send(new ContactFormMail(
                name: $request->name,
                email: $request->email,
                description: $request->description,
            ));

        return back()->with('success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
    }
}