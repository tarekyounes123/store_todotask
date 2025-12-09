<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    /**
     * Display the contact form - redirect to landing page since form is on landing
     */
    public function index()
    {
        return redirect('/#contact');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // In a real application, you would send the email here
        // Mail::to('contact@example.com')->send(new ContactFormMail($request->all()));

        // For now, just log the contact form data
        \Log::info('Contact Form Submitted', $request->all());

        return redirect()->back()->with('status', 'Thank you for contacting us! We will get back to you soon.');
    }
}