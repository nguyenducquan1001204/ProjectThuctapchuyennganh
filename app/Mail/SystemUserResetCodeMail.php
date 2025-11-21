<?php

namespace App\Mail;

use App\Models\SystemUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemUserResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public SystemUser $user;
    public string $code;

    public function __construct(SystemUser $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Mã xác nhận đặt lại mật khẩu')
            ->view('emails.system_user_reset_code');
    }
}


