<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ProfileService extends BaseCurrentService
{
    public function run()
    {
        $user = User::getCurrent();
        return $this->showResponse($user);
    }
}
