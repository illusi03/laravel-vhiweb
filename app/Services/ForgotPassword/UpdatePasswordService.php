<?php

namespace App\Services\ForgotPassword;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;

class UpdatePasswordService extends BaseCurrentService
{
    public function run()
    {
        $status = Password::reset(
            request()->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
                $user->setRememberToken(Str::random(60));
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return $this->showResponse(null, 'success reset password, please login with use new password !');
        } else {
            return $this->showResponseError('reset token has expired / not match');
        }
    }
}
