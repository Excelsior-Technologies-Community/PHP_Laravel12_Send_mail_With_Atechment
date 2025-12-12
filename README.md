Laravel 12 Email with Attachment

A complete and simple example project demonstrating how to send emails with file attachments in Laravel 12.
Includes web-based email sending, multiple attachments, inline images, queues, and programmatic usage.

Features

Send emails with attachments

Web form for sending emails

Programmatic email sending

Multiple attachments support

Inline image embedding

Attachment file validation (max 10MB)

Clean temporary file management

Bootstrap 5 styled email form

Queue support

Error handling and logging

Quick Start
1. Clone the Repository
git clone https://github.com/yourusername/laravel-email-attachment.git
cd laravel-email-attachment

2. Install Dependencies
composer install
npm install

3. Environment Setup
cp .env.example .env
php artisan key:generate

4. Configure Mail Settings

Edit your .env file.

Mailtrap (Recommended for Development):

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"


Gmail SMTP:

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

Project Structure
app/
 ├── Mail/
 │    └── SendEmailWithAttachment.php
 ├── Http/
 │    └── Controllers/
 │         └── EmailController.php
resources/
 ├── views/
 │    ├── emails/
 │    │    └── send-attachment.blade.php
 │    └── email-form.blade.php
routes/
 └── web.php

Installation Steps
Step 1: Create Mailable
php artisan make:mail SendEmailWithAttachment

Step 2: Create Controller
php artisan make:controller EmailController

Step 3: Add Routes (routes/web.php)
use App\Http\Controllers\EmailController;

Route::get('/send-email', [EmailController::class, 'showEmailForm'])->name('email.form');
Route::post('/send-email', [EmailController::class, 'sendEmailWithAttachment'])->name('send.email');
Route::get('/send-test-email', [EmailController::class, 'sendEmailProgrammatically'])->name('send.email.programmatically');

Usage Examples
1. Send Email via Web Form

Visit:

http://your-app.test/send-email

2. Programmatic Email Sending
use App\Mail\SendEmailWithAttachment;
use Illuminate\Support\Facades\Mail;

$attachmentPath = storage_path('app/public/document.pdf');

Mail::to('recipient@example.com')
    ->send(new SendEmailWithAttachment(
        'Your Subject',
        'Email message body',
        $attachmentPath,
        'document.pdf'
    ));

3. Multiple Attachments

In SendEmailWithAttachment.php:

public function attachments(): array
{
    return [
        Attachment::fromPath($this->attachment1),
        Attachment::fromPath($this->attachment2),
        Attachment::fromStorageDisk('s3', 'path/to/file.pdf'),
    ];
}

4. Inline Images

In Blade:

<img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Logo">

Mail Configuration Options
Mailgun
MAIL_MAILER=mailgun
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-secret

SendGrid
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key

Amazon SES
MAIL_MAILER=ses
SES_KEY=your-aws-key
SES_SECRET=your-aws-secret
SES_REGION=us-east-1

Testing Email Sending
1. Using Mailtrap (Recommended)

Create account

Copy SMTP credentials

Update .env

2. Log Driver (No real emails)
MAIL_MAILER=log


Emails will appear in:

storage/logs/laravel.log

3. Test Command

Create simple command:

php artisan make:command TestEmailCommand

Code Examples
Minimal Example
Route::get('/test-minimal', function() {
    Mail::raw('Hello World!', function($message) {
        $message->to('test@example.com')
                ->subject('Test Email')
                ->attach(storage_path('app/public/test.txt'), [
                    'as' => 'document.txt',
                    'mime' => 'text/plain',
                ]);
    });

    return 'Email sent!';
});

Queue Email Sending
Enable Queue Tables
php artisan queue:table
php artisan migrate

Update Mailable
class SendEmailWithAttachment extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
}

Troubleshooting
Issue	Solution
Connection refused	Check SMTP credentials and port
Attachment not sending	Verify path and permissions
Email goes to spam	Configure SPF, DKIM, DMARC
Slow email sending	Enable queue system
Max execution time	Increase upload/timeout limits
Debugging Tools
php artisan config:clear
php artisan cache:clear


Check mail config:

php artisan tinker
>>> config('mail')


Check logs:

tail -f storage/logs/laravel.log


Queue worker:

php artisan queue:work

Requirements

Laravel 12.x

PHP 8.2+

Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON

Testing

Run all tests:

php artisan test


Create test:

php artisan make:test EmailTest


Run specific test:

php artisan test --filter testEmailWithAttachment

Security Recommendations
File Upload Security

Validate file types

Validate MIME using mime_content_type()

Store uploaded files outside /public

Virus-scan attachments in production

Email Security

Never hardcode SMTP credentials

Use environment variables

Implement rate limiting

Validate recipient email addresses
