<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginService extends BaseCurrentService
{
    public function run()
    {
        $credentials = [
            'email' => request()->email,
            'password' => request()->password
        ];
        $attempCredentials = User::checkExist($credentials);
        if (!$attempCredentials) {
            return $this->showResponseError('email has not found');
        }
        $isLogin = Auth::attempt($attempCredentials);
        if (!$isLogin) {
            return $this->showResponseError('email and password has not match');
        }
        $hasVerified = auth()->user()->hasVerifiedEmail();
        if (!$hasVerified) {
            $res = $this->showResponseError('please verify email first');
            $hasUserToken = request()->user()->token();
            if ($hasUserToken) {
                $cookie = $this->clearToken();
                $res->withCookie($cookie);
            }
            return $res;
        }
        return $this->attemptedResponse();
    }
}
