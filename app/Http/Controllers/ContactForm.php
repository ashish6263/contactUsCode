<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmationMail;

use App\Models\Contact;
use Illuminate\Support\Facades\Validator;

class ContactForm extends Controller
{
    public function index()
    {
        return view('form');
    }

    public function submit(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|string|max:10',
            'notes' => 'nullable|string',
            // 'g-recaptcha-response' => 'required|captcha',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'notes' => $request->notes,
        ]);

        
        Mail::to($contact->email)->send(new ContactConfirmationMail($contact));

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you for contacting us!'
        ]);
    }
}
