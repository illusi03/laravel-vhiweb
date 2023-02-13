<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class UpdateService extends BaseCurrentService
{
    public function run($roleName)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        if ($roleName === 'superadmin') return $this->showResponseError('cannot mutate superadmin role');
        $role = Role::where('name', $roleName)->first();
        if (!$role) return $this->showResponseError('role name has not found');
        $role->touch();
        $permissions = request()->permissions;
        DB::table('role_has_permissions')->where('role_id', '=', $role->id)->delete();
        foreach ($permissions as $permission) {
            $role->givePermissionTo($permission);
        }
        unset($role['permissions']); // Remove Array Assosiatif
        $role['permissions'] = $this->getPermissionsFromRole($role->name);
        return $this->showResponse($role);
    }
}
