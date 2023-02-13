<?php

namespace App\Services\ForgotPassword;

use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;

class BaseCurrentService extends BaseService
{
    public function __construct()
    {
    }
}