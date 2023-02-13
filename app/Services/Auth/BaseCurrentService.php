<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;

class BaseCurrentService extends BaseService
{
    public function __construct() {
    }

    protected function getCookieDetails($token)
    {
        return [
            'name' => '_token',
            'value' => $token,
            'minutes' => 1440,
            'path' => null,
            'domain' => null,
            // 'secure' => true, // for production
            'secure' => false, // for localhost
            'httponly' => true,
        ];
    }

    protected function createToken()
    {
        return User::getCurrent()
            ->createToken('Personal-access-token')
            ->accessToken;
    }

    protected function clearToken()
    {
        request()->user()->token()->revoke();
        return Cookie::forget('_token');
    }

    protected function attemptedResponse($additionalData = [])
    {
        $currentUser = User::getCurrent();
        $token = $this->createToken();
        $cookie = $this->getCookieDetails($token);
        $isAdditionalData = is_array($additionalData) & !empty($additionalData);
        $res = [
            'user' => $currentUser,
            'token' => $token,
        ];
        if ($isAdditionalData) $res = array_merge($additionalData, $res);
        return $this->showResponse($res)->cookie(
            $cookie['name'],
            $cookie['value'],
            $cookie['minutes'],
            $cookie['path'],
            $cookie['domain'],
            $cookie['secure'],
            $cookie['httponly']
        );
    }
}
