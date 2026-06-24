<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a password recovery request is created.
 *
 * Contains the email information and recovery link required
 * to send the password reset email.
 *
 * @property string $to Recipient email address.
 * @property string $from Sender email address.
 * @property string $passwordRecoveryLink Generated password recovery link.
 * @property User $user User requesting password recovery.
 */
class PasswordRecoveryRequestedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $to  Recipient email address.
     * @param  string  $from  Sender email address.
     * @param  string  $passwordRecoveryLink  Generated password recovery URL.
     * @param  User  $access  User or client requesting password recovery.
     */
    public function __construct(
        public string $to,
        public string $from,
        public string $passwordRecoveryLink,
        public User $access
    ) {}
}
