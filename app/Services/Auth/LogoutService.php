<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LogoutService extends BaseCurrentService
{
    public function run()
    {
        $tmpUser = User::getCurrent();
        $cookie = $this->clearToken();
        return $this->showResponse($tmpUser)->withCookie($cookie);
    }
}
