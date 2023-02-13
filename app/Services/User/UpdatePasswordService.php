<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UpdatePasswordService extends BaseCurrentService
{
    public function run()
    {
        $oldPassword = request()->post('old_password');
        $newPassword = request()->post('password');
        $user = User::updatePasswordSelf($oldPassword, $newPassword);
        if (!$user) return $this->showResponseError('wrong old password');
        return $this->showResponse($user);
    }
}
