<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendEmailWithAttachment;
use App\Models\EmailHistory;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmailController extends Controller
{
    /**
     * Show Email Form
     */
    public function showEmailForm()
    {
        $templates = EmailTemplate::where('status','Active')->get();

        return view('email-form', compact('templates'));
    }


    /**
     * Send Email
     */
    public function sendEmailWithAttachment(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
            'template_id' => 'nullable',
            'scheduled_at' => 'nullable|date'
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        try {

            /*
            |--------------------------------------------------------------------------
            | Apply Email Template
            |--------------------------------------------------------------------------
            */

            if($request->template_id)
            {

                $template = EmailTemplate::find( $request->template_id);

                if($template)
                {
                    $request->subject = $template->subject;
                    $request->message =  $template->body;

                }

            }

            if ($request->hasFile('attachment')) {

                $file = $request->file('attachment');

                $attachmentName = time() . '_' . $file->getClientOriginalName();

                $path = $file->storeAs(
                    'temp_attachments',
                    $attachmentName,
                    'public'
                );

                $attachmentPath = storage_path('app/public/' . $path);
            }

            /*
            |--------------------------------------------------------------------------
            | Scheduled Email
            |--------------------------------------------------------------------------
            */

            if($request->scheduled_at)
            {
                EmailHistory::create([
                    'email'=>$request->email,
                    'subject'=>$request->subject,
                    'message'=>$request->message,
                    'attachment'=>$attachmentName,
                    'status'=>'Pending',
                    'type'=>'scheduled',
                    'scheduled_at'=>$request->scheduled_at

                ]);

                return back()->with('success','Email scheduled successfully.');

            }

            Mail::to($request->email)
                ->send(new SendEmailWithAttachment(
                    $request->subject,
                    $request->message,
                    $attachmentPath,
                    $attachmentName
                ));

            EmailHistory::create([
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'attachment' => $attachmentName,
                'status' => 'Sent',
                'type'=>'instant',
                'sent_at' => now(),
            ]);

            if ($attachmentPath && file_exists($attachmentPath)) {
                unlink($attachmentPath);
            }

            return back()->with('success', 'Email sent successfully.');

        } catch (\Exception $e) {

            EmailHistory::create([
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'attachment' => $attachmentName,
                'status' => 'Failed',
                'type'=>'instant',
                'sent_at' => now(),
            ]);

            if ($attachmentPath && file_exists($attachmentPath)) {
                unlink($attachmentPath);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $totalEmails = EmailHistory::count();

        $sentEmails = EmailHistory::where('status', 'Sent')->count();

        $failedEmails = EmailHistory::where('status', 'Failed')->count();

        $todayEmails = EmailHistory::whereDate(
            'created_at',
            today()
        )->count();

        return view('dashboard', compact(
            'totalEmails',
            'sentEmails',
            'failedEmails',
            'todayEmails'
        ));
    }

    /**
     * Email History
     */
    public function history(Request $request)
    {
        $query = EmailHistory::query();

        if ($request->search) {

            $query->where('email', 'like', '%' . $request->search . '%')
                ->orWhere('subject', 'like', '%' . $request->search . '%');
        }

        $emails = $query
            ->oldest()
            ->paginate(3)
            ->withQueryString();

        return view('history', compact('emails'));
    }

    /**
     * Delete Email
     */
    public function destroy($id)
    {
        EmailHistory::findOrFail($id)->delete();

        return back()->with('success', 'Email deleted successfully.');
    }

    /**
     * Delete All Emails
     */
    public function clearHistory()
    {
        EmailHistory::truncate();

        return back()->with('success', 'All email history deleted.');
    }

    /**
     * Programmatically Send Email
     */
    public function sendEmailProgrammatically()
    {
        $toEmail = "recipient@example.com";

        $subject = "Laravel Test Email";

        $message = "This email was sent programmatically.";

        $attachmentPath = storage_path('app/public/sample.pdf');

        if (!file_exists($attachmentPath)) {

            Storage::put(
                'public/sample.txt',
                'Laravel Sample Attachment'
            );

            $attachmentPath = storage_path('app/public/sample.txt');
        }

        try {

            Mail::to($toEmail)->send(
                new SendEmailWithAttachment(
                    $subject,
                    $message,
                    $attachmentPath,
                    "sample.txt"
                )
            );

            return response()->json([
                'message' => 'Email sent successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

        /**
     * Email Templates Page
     */
    public function templates()
    {

        $templates = EmailTemplate::latest()->get();

        return view('templates.index', compact('templates'));

    }
}