<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmailTemplateController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===============================
// Dashboard
// ===============================
Route::get('/', [EmailController::class, 'dashboard'])->name('dashboard');

// ===============================
// Send Email
// ===============================
Route::get('/send-email', [EmailController::class, 'showEmailForm'])
    ->name('email.form');

Route::post('/send-email', [EmailController::class, 'sendEmailWithAttachment'])
    ->name('send.email');

Route::get('/send-test-email', [EmailController::class, 'sendEmailProgrammatically'])
    ->name('send.email.programmatically');

// ===============================
// Email History
// ===============================
Route::get('/email-history', [EmailController::class, 'history'])
    ->name('email.history');

// ===============================
// Delete Email
// ===============================
Route::delete('/email-history/{id}', [EmailController::class, 'destroy'])
    ->name('email.delete');

// ===============================
// Clear All History
// ===============================
Route::delete('/email-history-clear', [EmailController::class, 'clearHistory'])
    ->name('email.clear');

Route::get('/templates', [EmailController::class, 'templates'])
    ->name('templates.index');

Route::post('/templates',[EmailTemplateController::class, 'store'])
    ->name('templates.store');

Route::delete('/templates/{id}',[EmailTemplateController::class, 'destroy'])
    ->name('templates.delete');

// ===============================
// Live Email Tracking Radar & Attachment Download
// ===============================
Route::get('/email-tracker/pixel/{token}', [EmailController::class, 'trackOpen'])
    ->name('email.track.pixel');

Route::get('/email-tracker/download/{id}', [EmailController::class, 'downloadAttachment'])
    ->name('email.attachment.download');