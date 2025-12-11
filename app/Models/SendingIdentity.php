<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SendingIdentity extends Model
{
    /** @use HasFactory<\Database\Factories\SendingIdentityFactory> */
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'name',
        'from_email',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'send_mode',
        'imap_host',
        'imap_port',
        'imap_username',
        'imap_password',
        'imap_encryption',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'smtp_password' => 'encrypted',
        'imap_password' => 'encrypted',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
