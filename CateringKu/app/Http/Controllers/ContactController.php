<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Contact', [
            'authUser' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create([
            'name' => $user->name,
            'email' => $user->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Pesan berhasil dikirim! Kami akan segera menghubungi Anda.');
    }
}
