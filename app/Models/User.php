<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 *
 * Represents a system user.
 *
 * @property int $id
 * @property string $email The user email address
 * @property string|null $cpf The Brazilian CPF document
 * @property string|null $rg The Brazilian RG document
 * @property string $full_name The user's full name
 * @property bool $activated Indicates whether the user is active
 * @property string $password The hashed user password
 * @property Carbon|null $email_verified_at The email verification timestamp
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql-user';
    protected $table = 'user.user';
    protected $fillable = [
        'email',
        'full_name',
        'cpf',
        'rg',
        'activated',
        'google_id',
        'facebook_id',
        'apple_id',
    ];
    protected $hidden = [
        'password',
    ];
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'email_verified_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
