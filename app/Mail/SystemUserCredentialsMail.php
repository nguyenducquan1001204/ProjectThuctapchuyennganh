<?php

namespace App\Mail;

use App\Models\SystemUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemUserCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public SystemUser $user;
    public string $plainPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(SystemUser $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Thông tin tài khoản hệ thống giáo viên')
            ->view('emails.system_user_credentials');
    }
}


