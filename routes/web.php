<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;

// Email routes
Route::get('/send-email', [EmailController::class, 'showEmailForm'])->name('email.form');
Route::post('/send-email', [EmailController::class, 'sendEmailWithAttachment'])->name('send.email');
Route::get('/send-test-email', [EmailController::class, 'sendEmailProgrammatically'])->name('send.email.programmatically');