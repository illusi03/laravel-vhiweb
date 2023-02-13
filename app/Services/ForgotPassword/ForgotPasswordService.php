<?php

namespace App\Services\ForgotPassword;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;

class ForgotPasswordService extends BaseCurrentService
{
    public function run()
    {
        $reqEmail = request()->email;
        $response = Password::sendResetLink(['email' => $reqEmail]);
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->showResponse('a reset link has been sent to your email address.');
            case Password::INVALID_USER:
                return $this->showResponseError('invalid email');
            default:
                return $this->showResponseError('reset link has been sended into your email, please check !');
        }
    }
}
