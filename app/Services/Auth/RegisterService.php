<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class RegisterService extends BaseCurrentService
{
    private function executeSave($value)
    {
        return [
            'user' => null
        ];
    }

    public function run()
    {
        $dirtyValue = request()->all();
        $user = User::create([
            'email' => Arr::get($dirtyValue, 'email'),
            'password' => Arr::get($dirtyValue, 'password'),
            'name' => Arr::get($dirtyValue, 'name'),
        ]);
        $user->sendEmailVerificationNotification();
        return $this->showResponse($user, 'email verification link sent on your email, please check your email and verify');
    }
}
