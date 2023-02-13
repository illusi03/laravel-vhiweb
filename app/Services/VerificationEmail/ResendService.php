<?php

namespace App\Services\VerificationEmail;

use App\Models\User;

class ResendService extends BaseCurrentService
{
    public function run()
    {
        $userId = request()->route('id');
        $user = User::whereId($userId)->first();
        if (!$user) return $this->showResponseNotFound();
        if ($user->hasVerifiedEmail()) {
            return $this->showResponseError('email already verified');
        }
        $user->sendEmailVerificationNotification();
        return $this->showResponse($user,  'email verification link sent on your email, please check your email and verify');
    }
}
