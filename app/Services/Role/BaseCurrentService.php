<?php

namespace App\Services\Role;

use App\Services\BaseService;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class BaseCurrentService extends BaseService
{
    public function __construct()
    {
    }

    protected function getPermissionsFromRole($roleName)
    {
        $permissions = Role::where('name', $roleName)->firstOrFail()->permissions;
        $permissions = $permissions->map(function ($obj) {
            return $obj->name;
        });
        return $permissions;
    }

    protected function roleResponse($role, $isFail = false)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        return $this->showResponse($role, $isFail);
    }
}
