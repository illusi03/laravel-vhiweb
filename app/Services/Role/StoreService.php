<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class StoreService extends BaseCurrentService
{
    public function run()
    {
        $roleName = request()->name;
        $role = Role::create(['name' => $roleName]);
        if (request()->permissions) {
            foreach (request()->permissions as $permission) {
                $role->givePermissionTo($permission);
            }
        }
        return $this->roleResponse($role);
    }
}
