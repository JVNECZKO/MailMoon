<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warming extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sending_identity_id',
        'contact_list_id',
        'plan',
        'status',
        'day_current',
        'day_total',
        'daily_target',
        'schedule',
        'subject',
        'body',
        'send_interval_seconds',
        'sent_today',
        'total_sent',
        'started_at',
        'paused_at',
        'finished_at',
        'last_run_at',
    ];

    protected $casts = [
        'schedule' => 'array',
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
        'finished_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    public function sendingIdentity(): BelongsTo
    {
        return $this->belongsTo(SendingIdentity::class);
    }

    public function contactList(): BelongsTo
    {
        return $this->belongsTo(ContactList::class);
    }
}
