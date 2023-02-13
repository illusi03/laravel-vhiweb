<?php

namespace App\Services\VerificationEmail;

use App\Models\User;

class VerifyService extends BaseCurrentService
{
    public function run()
    {
        $userId = request()->route('id');
        $hasValidSignature = request()->hasValidSignature();
        if (!$hasValidSignature) return $this->showResponseError('has invalid signature / token verify has expired');
        $user = User::whereId($userId)->first();
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return $this->showResponse($user);
    }
}
