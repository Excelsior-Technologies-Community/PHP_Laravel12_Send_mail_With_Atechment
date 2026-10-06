<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailHistory extends Model
{
    protected $fillable = [
        'email',
        'subject',
        'message',
        'attachment',
        'status',
        'sent_at',
        'type',
        'scheduled_at',
        'tracking_token',
        'opened_at',
        'open_count',
        'last_opened_at',
        'attachment_downloads',
        'multiple_attachments',
        'is_zipped',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'opened_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'multiple_attachments' => 'array',
        'is_zipped' => 'boolean',
    ];
}
