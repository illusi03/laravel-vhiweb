<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Services\Permission\IndexService;

class PermissionController extends Controller
{
    private $indexService;

    public function __construct(
        IndexService $indexService
    ) {
        $this->indexService = $indexService;
        // $this->middleware('role_or_permission:superadmin|roles.show|roles.self', ['only' => ['index']]);
    }

    public function index()
    {
        return $this->indexService->run();
    }
}
