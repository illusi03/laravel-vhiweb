<?php

namespace App\Http\Controllers\Auth;

use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use App\Services\Auth\ProfileService;
use App\Services\Auth\RegisterService;
use App\Http\Controllers\Controller as BaseController;

class AuthController extends BaseController
{
    private $loginService;
    private $logoutService;
    private $profileService;
    private $registerService;

    public function __construct(
        LoginService $loginService,
        LogoutService $logoutService,
        ProfileService $profileService,
        RegisterService $registerService
    ) {
        $this->loginService = $loginService;
        $this->loginService = $loginService;
        $this->logoutService = $logoutService;
        $this->profileService = $profileService;
        $this->registerService = $registerService;
    }

    public function login()
    {
        request()->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        return $this->loginService->run();
    }

    public function register()
    {
        request()->validate([
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string',
            'name' => 'required|string',
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
}
