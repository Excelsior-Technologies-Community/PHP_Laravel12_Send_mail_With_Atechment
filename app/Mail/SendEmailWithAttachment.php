<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailWithAttachment extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $attachmentPath;
    public $attachmentName;
    public $attachmentPaths;
    public $trackingToken;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $body, $attachmentPath = null, $attachmentName = null, array $attachmentPaths = [], $trackingToken = null)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
        $this->attachmentPaths = $attachmentPaths;
        $this->trackingToken = $trackingToken;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send-attachment',
            with: [
                'trackingUrl' => $this->trackingToken ? route('email.track.pixel', $this->trackingToken) : null,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->attachmentPaths)) {
            foreach ($this->attachmentPaths as $fileItem) {
                $path = is_array($fileItem) ? ($fileItem['path'] ?? '') : $fileItem;
                $name = is_array($fileItem) ? ($fileItem['name'] ?? basename($path)) : basename($path);

                if (file_exists($path)) {
                    $attachments[] = Attachment::fromPath($path)
                        ->as($name)
                        ->withMime(mime_content_type($path) ?: 'application/octet-stream');
                }
            }
        } elseif ($this->attachmentPath && file_exists($this->attachmentPath)) {
            $attachments[] = Attachment::fromPath($this->attachmentPath)
                ->as($this->attachmentName ?? basename($this->attachmentPath))
                ->withMime(mime_content_type($this->attachmentPath) ?: 'application/octet-stream');
        }

        return $attachments;
    }
}