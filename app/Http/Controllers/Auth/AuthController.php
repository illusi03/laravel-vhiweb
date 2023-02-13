<?php

namespace App\Http\Controllers\Auth;

use App\Services\Auth\ChangePassService;
use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use App\Services\Auth\ProfileService;
use App\Services\Auth\RegisterService;
use App\Http\Controllers\Controller as BaseController;

class AuthController extends BaseController
{
    private $changePassService;
    private $loginService;
    private $logoutService;
    private $profileService;
    private $registerService;

    public function __construct(
        ChangePassService $changePassService,
        LoginService $loginService,
        LogoutService $logoutService,
        ProfileService $profileService,
        RegisterService $registerService
    ) {
        $this->loginService = $loginService;
        $this->changePassService = $changePassService;
        $this->loginService = $loginService;
        $this->logoutService = $logoutService;
        $this->profileService = $profileService;
        $this->registerService = $registerService;
    }

    public function login()
    {
        request()->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        return $this->loginService->run();
    }

    public function register()
    {
        request()->validate([
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'string',
            'telp' => 'string',
            'place_of_birth' => 'string',
            'date_of_birth' => 'date_format:Y-m-d',
            'gender' => 'in:male,female',
        ]);
        return $this->registerService->run();
    }

    public function logout()
    {
        return $this->logoutService->run();
    }

    public function profile()
    {
        return $this->profileService->run();
    }

    public function changePassword()
    {
        request()->validate([
            'user_id' => 'required|string|distinct|exists:users,id,deleted_at,NULL',
            'new_password' => 'required|string',
            'old_password' => 'string',
        ]);
        return $this->changePassService->run();
    }
}
