<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class VerifyOtpMobileService extends BaseCurrentService
{
    public function run()
    {
        $dirtyValue = request()->all();
        return $this->showResponseMaintenance();
    }
}
