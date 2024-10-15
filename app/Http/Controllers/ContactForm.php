<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmationMail;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;

class ContactForm extends Controller
{
    /**
     * Display the contact form with a random math CAPTCHA question.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $captchaQuestion = $this->generateCaptcha();
        return view('form', [
            'captcha_question' => $captchaQuestion
        ]);
    }

    /**
     * Handle the form submission and validate the CAPTCHA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request)
    {
        // Validate form inputs
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|string|max:10',
            'notes' => 'nullable|string',
            'captcha' => 'required|numeric',
        ]);

        // If validation fails, generate a new CAPTCHA and return errors
        if ($validator->fails()) {
            $newCaptchaQuestion = $this->generateCaptcha();

            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'new_captcha_question' => $newCaptchaQuestion
            ], 422);
        }

        // Validate the CAPTCHA answer
        if ($request->input('captcha') != session('captcha_answer')) {
            $newCaptchaQuestion = $this->generateCaptcha();

            return response()->json([
                'status' => 'error',
                'errors' => ['captcha' => 'Incorrect CAPTCHA answer.'],
                'new_captcha_question' => $newCaptchaQuestion
            ], 422);
        }

        // Create the contact entry in the database
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'notes' => $request->notes,
        ]);

        // Send a confirmation email to the user
        Mail::to($contact->email)->send(new ContactConfirmationMail($contact));

        // Clear the CAPTCHA answer from the session
        $request->session()->forget('captcha_answer');

        // Generate a new CAPTCHA for future submissions
        $newCaptchaQuestion = $this->generateCaptcha();

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you for contacting us!',
            'new_captcha_question' => $newCaptchaQuestion
        ]);
    }

    /**
     * Generate a random math CAPTCHA question and store the answer in the session.
     *
     * @return string
     */
    private function generateCaptcha()
    {
        $number1 = rand(1, 10);
        $number2 = rand(1, 10);
        $operators = ['+', '-', '*'];
        $operator = $operators[array_rand($operators)];

        switch ($operator) {
            case '+':
                $answer = $number1 + $number2;
                break;
            case '-':
                // Ensure no negative answers
                if ($number1 < $number2) {
                    [$number1, $number2] = [$number2, $number1];
                }
                $answer = $number1 - $number2;
                break;
            case '*':
                $answer = $number1 * $number2;
                break;
        }

        // Store the answer in the session
        session(['captcha_answer' => $answer]);

        return "What is {$number1} {$operator} {$number2}?";
    }
}
