<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendEmailWithAttachment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmailController extends Controller
{
    /**
     * Show email form
     */
    public function showEmailForm()
    {
        return view('email-form');
    }

    /**
     * Send email with attachment
     */
    public function sendEmailWithAttachment(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
        ]);

        try {
            $attachmentPath = null;
            $attachmentName = null;

            // Handle file upload if exists
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('temp_attachments', $attachmentName, 'public');
                $attachmentPath = storage_path('app/public/' . $attachmentPath);
            }

            // Send email
            Mail::to($request->email)
                ->send(new SendEmailWithAttachment(
                    $request->subject,
                    $request->message,
                    $attachmentPath,
                    $attachmentName
                ));

            // Clean up temporary file
            if ($attachmentPath && file_exists($attachmentPath)) {
                unlink($attachmentPath);
                // Also remove directory if empty
                $dir = dirname($attachmentPath);
                if (is_dir($dir) && count(scandir($dir)) == 2) {
                    rmdir($dir);
                }
            }

            return back()->with('success', 'Email sent successfully!');

        } catch (\Exception $e) {
            // Clean up file if error occurs
            if (isset($attachmentPath) && file_exists($attachmentPath)) {
                unlink($attachmentPath);
            }
            
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Send email without form (programmatically)
     */
    public function sendEmailProgrammatically()
    {
        $toEmail = 'recipient@example.com';
        $subject = 'Test Email with Attachment';
        $message = 'This is a test email with attachment sent from Laravel.';
        $attachmentPath = storage_path('app/public/sample.pdf'); // Your file path
        
        // Check if file exists
        if (!file_exists($attachmentPath)) {
            // Create a sample file for testing
            Storage::put('public/sample.txt', 'This is a sample attachment file content.');
            $attachmentPath = storage_path('app/public/sample.txt');
        }

        try {
            Mail::to($toEmail)
                ->send(new SendEmailWithAttachment(
                    $subject,
                    $message,
                    $attachmentPath,
                    'sample-file.txt'
                ));

            return response()->json(['message' => 'Email sent successfully!']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}