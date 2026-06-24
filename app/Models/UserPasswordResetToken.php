<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model representing password reset tokens for users.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $email
 * @property string $token
 * @property bool $opened
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordResetToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordResetToken whereEmail(string $email)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordResetToken whereToken(string $token)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPasswordResetToken whereOpened(bool $opened)
 */
class UserPasswordResetToken extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql-user';
    protected $table = 'user.user_password_reset_tokens';
    protected $fillable = [
        'email',
        'token',
        'opened',
    ];
    protected $casts = [
        'opened' => 'boolean',
    ];
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the user associated with this password reset token.
     *
     * @return BelongsTo<User, UserPasswordResetToken>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
