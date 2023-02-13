<?php

namespace App\Http\Controllers;

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
use App\Services\ForgotPassword\ForgotPasswordService;
use App\Services\ForgotPassword\UpdatePasswordService;

class ForgotPasswordController extends Controller
{
    private $forgotPasswordService;
    private $updatePasswordService;

    public function __construct(
        ForgotPasswordService $forgotPasswordService,
        UpdatePasswordService $updatePasswordService
    ) {
        $this->forgotPasswordService = $forgotPasswordService;
        $this->updatePasswordService = $updatePasswordService;
    }

    public function forgotPassword()
    {
        request()->validate([
            'email' => 'required|exists:users,email',
        ]);
        return $this->forgotPasswordService->run();
    }

    public function updatePassword()
    {
        request()->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ]);
        return $this->updatePasswordService->run();
    }
}
