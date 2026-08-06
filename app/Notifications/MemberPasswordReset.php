<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * The password reset email a member receives.
 *
 * Laravel's own would send them to /reset-password, which is the committee's
 * side of the house. This points at the member reset page and says how long the
 * link lasts, because the alternative is a support message an hour later.
 */
class MemberPasswordReset extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $minutes = config('auth.passwords.alumni.expire', 60);

        $url = route('member.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Reset your RCMAA password')
            ->greeting('Assalamu alaikum, '.$notifiable->full_name_en)
            ->line('Someone asked to reset the password on your Rajshahi College Mathematics Alumni Association account.')
            ->action('Choose a new password', $url)
            ->line("This link stops working in {$minutes} minutes.")
            ->line('If you did not ask for this, you can ignore this email — your password stays as it is.')
            ->salutation('— RCMAA Reunion 2026');
    }
}
