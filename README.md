# PHP_Laravel12_Send_mail_With_Atechment

A complete and simple Laravel 12 example project demonstrating how to send emails with file attachments.  
Includes a web-based email form, multiple attachments, inline images, queues, programmatic email sending, and logging support.

---

## Features

- Send emails with attachments  
- Web form for sending emails  
- Programmatic email sending  
- Multiple attachments support  
- Inline image embedding  
- Attachment file validation (max 10MB)  
- Clean temporary file handling  
- Bootstrap 5 email form  
- Queue support  
- Error handling and logging  

---

## Images

<img width="1201" height="886" alt="image" src="https://github.com/user-attachments/assets/d51b657d-8148-4dc4-8840-71faadf229e1" />


## Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/laravel-email-attachment.git
cd laravel-email-attachment
2. Install Dependencies
bash
Copy code
composer install
npm install
3. Environment Setup
bash
Copy code
cp .env.example .env
php artisan key:generate
Configure Mail Settings
Edit your .env file.

Mailtrap (Recommended for local development)
ini
Copy code
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
Gmail SMTP
ini
Copy code
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
Project Structure
css
Copy code
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
bash
Copy code
php artisan make:mail SendEmailWithAttachment
Step 2: Create Controller
bash
Copy code
php artisan make:controller EmailController
Step 3: Add Routes (routes/web.php)
php
Copy code
use App\Http\Controllers\EmailController;

Route::get('/send-email', [EmailController::class, 'showEmailForm'])->name('email.form');
Route::post('/send-email', [EmailController::class, 'sendEmailWithAttachment'])->name('send.email');
Route::get('/send-test-email', [EmailController::class, 'sendEmailProgrammatically'])->name('send.email.programmatically');
Usage Examples
1. Send Email via Web Form
Visit:

perl
Copy code
http://your-app.test/send-email
2. Programmatic Email Sending
php
Copy code
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
3. Multiple Attachments Example
Inside SendEmailWithAttachment.php:

php
Copy code
public function attachments(): array
{
    return [
        Attachment::fromPath($this->attachment1),
        Attachment::fromPath($this->attachment2),
        Attachment::fromStorageDisk('s3', 'path/to/file.pdf'),
    ];
}
4. Inline Images in Blade Email
html
Copy code
<img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Logo">
Mail Configuration Options
Mailgun
ini
Copy code
MAIL_MAILER=mailgun
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-secret
SendGrid
ini
Copy code
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
Amazon SES
ini
Copy code
MAIL_MAILER=ses
SES_KEY=your-aws-key
SES_SECRET=your-aws-secret
SES_REGION=us-east-1
Testing Email Sending
1. Using Mailtrap (Recommended)
Create Mailtrap account

Copy SMTP credentials

Update .env

2. Log Driver (No real emails sent)
ini
Copy code
MAIL_MAILER=log
Emails will appear in:

pgsql
Copy code
storage/logs/laravel.log
3. Test Command
Create a simple test command:

bash
Copy code
php artisan make:command TestEmailCommand
Code Examples
Minimal Example
php
Copy code
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
bash
Copy code
php artisan queue:table
php artisan migrate
Update Mailable
php
Copy code
class SendEmailWithAttachment extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
}
Troubleshooting
Issue	Solution
Connection refused	Check SMTP credentials and port
Attachment not sending	Verify file path and permissions
Email goes to spam	Configure SPF, DKIM, DMARC
Slow email sending	Use queues
Max execution time	Increase upload/timeout limits

Debugging Tools
bash
Copy code
php artisan config:clear
php artisan cache:clear
Check mail config:

bash
Copy code
php artisan tinker
>>> config('mail');
Logs:

bash
Copy code
tail -f storage/logs/laravel.log
Queue worker:

cpp
Copy code
php artisan queue:work
Requirements
Laravel 12.x

PHP 8.2+

Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON

Testing
Run full test suite:

bash
Copy code
php artisan test
Create test file:

bash
Copy code
php artisan make:test EmailTest
Run specific test:

bash
Copy code
php artisan test --filter testEmailWithAttachment
Security Recommendations
File Upload Security
Validate file types

Validate MIME: mime_content_type()

Store uploaded files outside /public

Virus-scan attachments in production

Email Security
Never hardcode SMTP credentials

Use environment variables

Rate-limit email sending

Validate recipient email addresses

