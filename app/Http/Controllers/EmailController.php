<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendEmailWithAttachment;
use App\Models\EmailHistory;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class EmailController extends Controller
{
    /**
     * Show Email Form
     */
    public function showEmailForm()
    {
        $templates = EmailTemplate::where('status', 'Active')->get();

        return view('email-form', compact('templates'));
    }

    /**
     * Send Email with Multi-Attachment & Zip Bundler & Tracking Token
     */
    public function sendEmailWithAttachment(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:25600', // 25MB max
            'attachments.*' => 'nullable|file|max:25600',
            'template_id' => 'nullable',
            'scheduled_at' => 'nullable|date',
            'zip_attachments' => 'nullable|boolean',
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentPaths = [];
        $savedAttachmentsInfo = [];
        $isZipped = false;
        $trackingToken = Str::random(32);

        try {
            /*
            |--------------------------------------------------------------------------
            | Apply Email Template
            |--------------------------------------------------------------------------
            */
            if ($request->template_id) {
                $template = EmailTemplate::find($request->template_id);
                if ($template) {
                    $request->subject = $template->subject;
                    $request->message = $template->body;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Handle Single & Multiple Attachments
            |--------------------------------------------------------------------------
            */
            $filesToProcess = [];
            if ($request->hasFile('attachments')) {
                $filesToProcess = $request->file('attachments');
            } elseif ($request->hasFile('attachment')) {
                $filesToProcess = [$request->file('attachment')];
            }

            if (!empty($filesToProcess)) {
                $shouldZip = $request->boolean('zip_attachments') || count($filesToProcess) > 1;

                if ($shouldZip && count($filesToProcess) >= 1) {
                    // Create Zip Bundle
                    $isZipped = true;
                    $zipName = 'bundle_' . time() . '_' . Str::random(6) . '.zip';
                    $zipRelativePath = 'attachments/' . $zipName;
                    $zipFullPath = storage_path('app/public/' . $zipRelativePath);

                    if (!file_exists(dirname($zipFullPath))) {
                        mkdir(dirname($zipFullPath), 0755, true);
                    }

                    $zip = new ZipArchive();
                    if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                        foreach ($filesToProcess as $file) {
                            $originalName = $file->getClientOriginalName();
                            $zip->addFile($file->getPathname(), $originalName);
                            $savedAttachmentsInfo[] = [
                                'name' => $originalName,
                                'size' => $file->getSize(),
                            ];
                        }
                        $zip->close();
                    }

                    $attachmentPath = $zipFullPath;
                    $attachmentName = $zipName;
                    $attachmentPaths = [['path' => $zipFullPath, 'name' => $zipName]];
                } else {
                    // Save Individual Files Permanently for History & Download
                    foreach ($filesToProcess as $file) {
                        $storedName = time() . '_' . Str::random(6) . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('attachments', $storedName, 'public');
                        $fullPath = storage_path('app/public/' . $path);

                        $attachmentPaths[] = [
                            'path' => $fullPath,
                            'name' => $file->getClientOriginalName(),
                            'stored_name' => $storedName,
                        ];

                        $savedAttachmentsInfo[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'stored_name' => $storedName,
                        ];
                    }

                    if (!empty($attachmentPaths)) {
                        $attachmentPath = $attachmentPaths[0]['path'];
                        $attachmentName = $attachmentPaths[0]['name'];
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Scheduled Email
            |--------------------------------------------------------------------------
            */
            if ($request->scheduled_at) {
                EmailHistory::create([
                    'email' => $request->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'attachment' => $attachmentName,
                    'multiple_attachments' => $savedAttachmentsInfo,
                    'is_zipped' => $isZipped,
                    'status' => 'Pending',
                    'type' => 'scheduled',
                    'scheduled_at' => $request->scheduled_at,
                    'tracking_token' => $trackingToken,
                ]);

                return back()->with('success', 'Email scheduled successfully with attachments.');
            }

            /*
            |--------------------------------------------------------------------------
            | Instant Mail Sending
            |--------------------------------------------------------------------------
            */
            Mail::to($request->email)
                ->send(new SendEmailWithAttachment(
                    $request->subject,
                    $request->message,
                    $attachmentPath,
                    $attachmentName,
                    $attachmentPaths,
                    $trackingToken
                ));

            EmailHistory::create([
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'attachment' => $attachmentName,
                'multiple_attachments' => $savedAttachmentsInfo,
                'is_zipped' => $isZipped,
                'status' => 'Sent',
                'type' => 'instant',
                'sent_at' => now(),
                'tracking_token' => $trackingToken,
            ]);

            return back()->with('success', 'Email sent successfully with tracking pixel and attachments!');

        } catch (\Exception $e) {
            EmailHistory::create([
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'attachment' => $attachmentName,
                'multiple_attachments' => $savedAttachmentsInfo,
                'is_zipped' => $isZipped,
                'status' => 'Failed',
                'type' => 'instant',
                'sent_at' => now(),
                'tracking_token' => $trackingToken,
            ]);

            return back()->with('error', 'Mail Delivery Failure: ' . $e->getMessage());
        }
    }

    /**
     * 1-Pixel Open Tracking Endpoint
     */
    public function trackOpen($token)
    {
        $history = EmailHistory::where('tracking_token', $token)->first();

        if ($history) {
            $history->update([
                'opened_at' => $history->opened_at ?? now(),
                'last_opened_at' => now(),
                'open_count' => $history->open_count + 1,
            ]);
        }

        // Return 1x1 transparent GIF image
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($gif, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate, private',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Track & Download Attachment
     */
    public function downloadAttachment($id)
    {
        $history = EmailHistory::findOrFail($id);

        $history->increment('attachment_downloads');

        if ($history->attachment) {
            $path = storage_path('app/public/attachments/' . $history->attachment);
            if (file_exists($path)) {
                return response()->download($path, $history->attachment);
            }
        }

        if (!empty($history->multiple_attachments)) {
            $first = $history->multiple_attachments[0] ?? null;
            if ($first && isset($first['stored_name'])) {
                $path = storage_path('app/public/attachments/' . $first['stored_name']);
                if (file_exists($path)) {
                    return response()->download($path, $first['name'] ?? $first['stored_name']);
                }
            }
        }

        return back()->with('error', 'Attachment file not found on disk.');
    }

    /**
     * Dashboard with Tracking Analytics
     */
    public function dashboard()
    {
        $totalEmails = EmailHistory::count();
        $sentEmails = EmailHistory::where('status', 'Sent')->count();
        $failedEmails = EmailHistory::where('status', 'Failed')->count();
        $todayEmails = EmailHistory::whereDate('created_at', today())->count();

        $openedEmails = EmailHistory::whereNotNull('opened_at')->count();
        $openRate = $sentEmails > 0 ? round(($openedEmails / $sentEmails) * 100, 1) : 0;
        $totalDownloads = EmailHistory::sum('attachment_downloads');

        return view('dashboard', compact(
            'totalEmails',
            'sentEmails',
            'failedEmails',
            'todayEmails',
            'openedEmails',
            'openRate',
            'totalDownloads'
        ));
    }

    /**
     * Email History Studio
     */
    public function history(Request $request)
    {
        $query = EmailHistory::query();

        if ($request->search) {
            $query->where('email', 'like', '%' . $request->search . '%')
                ->orWhere('subject', 'like', '%' . $request->search . '%');
        }

        $emails = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('history', compact('emails'));
    }

    /**
     * Delete Email
     */
    public function destroy($id)
    {
        EmailHistory::findOrFail($id)->delete();

        return back()->with('success', 'Email history deleted.');
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
        $subject = "Laravel Test Email with Tracking Pixel";
        $message = "This email was sent programmatically with open tracking enabled.";
        $trackingToken = Str::random(32);

        $attachmentPath = storage_path('app/public/sample.txt');
        if (!file_exists($attachmentPath)) {
            Storage::put('public/sample.txt', 'Laravel Sample Attachment Document');
            $attachmentPath = storage_path('app/public/sample.txt');
        }

        try {
            Mail::to($toEmail)->send(
                new SendEmailWithAttachment(
                    $subject,
                    $message,
                    $attachmentPath,
                    "sample.txt",
                    [],
                    $trackingToken
                )
            );

            EmailHistory::create([
                'email' => $toEmail,
                'subject' => $subject,
                'message' => $message,
                'attachment' => 'sample.txt',
                'status' => 'Sent',
                'type' => 'instant',
                'sent_at' => now(),
                'tracking_token' => $trackingToken,
            ]);

            return response()->json([
                'message' => 'Email sent programmatically with tracking pixel.',
                'tracking_token' => $trackingToken
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