<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Notification;

class RegisterMobileService extends BaseCurrentService
{
    public function run()
    {
        $dirtyValue = request()->all();
        return $this->showResponseMaintenance();
    }
}
