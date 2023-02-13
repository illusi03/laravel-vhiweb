<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class RegisterService extends BaseCurrentService
{
    public function run()
    {
        $dirtyValue = request()->all();
        $purePass = Arr::get($dirtyValue, 'password');
        $hashedPass = Hash::make($purePass);
        $user = User::create([
            'email' => Arr::get($dirtyValue, 'email'),
            'password' => $hashedPass,
            'name' => Arr::get($dirtyValue, 'name'),
        ]);
        $user->sendEmailVerificationNotification();
        return $this->showResponse($user, 'email verification link sent on your email, please check your email and verify');
    }
}
