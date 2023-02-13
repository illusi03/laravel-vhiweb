<?php

namespace App\Http\Controllers;

use App\Services\User\UpdatePasswordService;
use App\Services\User\IndexService;

class UserController extends Controller
{
    private $updatePasswordService;
    private $indexService;

    function __construct(
        UpdatePasswordService $updatePasswordService,
        IndexService $indexService
    ) {
        $this->updatePasswordService = $updatePasswordService;
        $this->indexService = $indexService;
    }

    public function index()
    {
        return $this->indexService->run();
    }

    public function updatePasswordSelf()
    {
        request()->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed|min:4',
        ]);
        return $this->updatePasswordService->run();
    }
}
