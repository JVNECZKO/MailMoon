<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignFactory> */
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'sending_identity_id',
        'contact_list_id',
        'template_id',
        'name',
        'subject',
        'html_content',
        'track_opens',
        'track_clicks',
        'enable_unsubscribe',
        'send_interval_seconds',
        'status',
        'scheduled_at',
        'sending_window_enabled',
        'sending_window_start',
        'sending_window_end',
        'sending_window_schedule',
    ];

    protected $casts = [
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
        'enable_unsubscribe' => 'boolean',
        'scheduled_at' => 'datetime',
        'send_interval_seconds' => 'integer',
        'sending_window_enabled' => 'boolean',
        'sending_window_start' => 'string',
        'sending_window_end' => 'string',
        'sending_window_schedule' => 'array',
    ];

    public function sendingIdentity(): BelongsTo
    {
        return $this->belongsTo(SendingIdentity::class);
    }

    public function contactList(): BelongsTo
    {
        return $this->belongsTo(ContactList::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }
}
