<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignMessage extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignMessageFactory> */
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'campaign_id',
        'contact_id',
        'to_email',
        'message_id',
        'sent_at',
        'open_count',
        'first_open_at',
        'click_count',
        'last_click_at',
        'unsubscribe_at',
        'unsubscribe_token',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'first_open_at' => 'datetime',
        'last_click_at' => 'datetime',
        'unsubscribe_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
