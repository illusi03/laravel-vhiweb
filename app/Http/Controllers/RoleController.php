<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use App\Services\Role\IndexService;
use App\Services\Role\DefaultService;
use App\Services\Role\DestroyService;
use App\Services\Role\ShowService;
use App\Services\Role\StoreService;
use App\Services\Role\UpdateService;

class RoleController extends Controller
{
    private $indexService;
    private $defaultService;
    private $destroyService;
    private $showService;
    private $storeService;
    private $updateService;

    public function __construct(
        IndexService $indexService,
        DefaultService $defaultService,
        DestroyService $destroyService,
        ShowService $showService,
        StoreService $storeService,
        UpdateService $updateService
    ) {
        $roleSU = "role_or_permission:superadmin|";
        // $this->middleware("$roleSU |roles.show|roles.self", ['only' => ['index', 'show']]);
        $this->middleware("$roleSU |roles.create", ['only' => ['store']]);
        $this->middleware("$roleSU |roles.update", ['only' => ['update', 'default']]);
        $this->middleware("$roleSU |roles.delete", ['only' => ['destroy']]);
        $this->indexService = $indexService;
        $this->defaultService = $defaultService;
        $this->destroyService = $destroyService;
        $this->showService = $showService;
        $this->storeService = $storeService;
        $this->updateService = $updateService;
    }

    public function index()
    {
        return $this->indexService->run();
    }

    public function show()
    {
        return $this->showService->run();
    }

    public function update($roleName)
    {
        request()->validate([
            // 'name' => 'required|string|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|distinct|exists:permissions,name',
        ]);
        return $this->updateService->run($roleName);
    }

    public function default()
    {
        request()->validate([
            'name' => 'required|string|exists:roles,name',
        ]);
        return $this->defaultService->run();
    }

    public function store()
    {
        request()->validate([
            'name' => 'required|string|regex:/^\S*$/u|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|distinct|exists:permissions,name',
        ]);
        return $this->storeService->run();
    }

    public function destroy($name)
    {
        return $this->destroyService->run($name);
    }
}
