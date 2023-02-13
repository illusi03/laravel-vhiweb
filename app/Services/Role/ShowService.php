<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class ShowService extends BaseCurrentService
{
    public function run()
    {
        $roleName = request()->get('name');
        if (!Role::where('name', $roleName)->exists()) {
            return $this->showResponse([]);
        }
        $permissions = $permissions = $this->getPermissionsFromRole($roleName);
        $role = Role::where('name', '=', $roleName)->firstOrFail();
        $role['permissions'] = $permissions;
        return $this->showResponse($role);
    }
}
