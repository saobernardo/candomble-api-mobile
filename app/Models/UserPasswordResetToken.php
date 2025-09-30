<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientPasswordResetToken extends Model
{
    use SoftDeletes;

    protected $table = 'client_password_reset_tokens';
    protected $fillable = [
        'email',
        'token',
        'sent',
    ];
    protected $casts = [
        'sent' => 'boolean',
    ];
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'email_verified_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
