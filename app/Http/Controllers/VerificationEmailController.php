<?php

namespace App\Http\Controllers;

use App\Services\VerificationEmail\ResendService;
use App\Services\VerificationEmail\VerifyService;

class VerificationEmailController extends Controller
{
    private $resendService;
    private $verifyService;

    public function __construct(
        ResendService $resendService,
        VerifyService $verifyService
    ) {
        $this->resendService = $resendService;
        $this->verifyService = $verifyService;
    }

    public function resend()
    {
        request()->validate([]);
        return $this->resendService->run();
    }

    public function verify()
    {
        request()->validate([]);
        return $this->verifyService->run();
    }
}
