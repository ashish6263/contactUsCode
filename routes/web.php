<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactForm;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/form',);
Route::get('/', [ContactForm::class, 'index'])->name('contact.form');
Route::post('/submit', [ContactForm::class, 'submit'])->name('contact.submit');