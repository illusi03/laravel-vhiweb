<?php

namespace App\Exports\Excels;

use Illuminate\Support\Facades\Auth;

class BaseExport
{
    public function checkPermission($permissionName)
    {
        $permissionsCollection = Auth::user()->getAllPermissions();
        $permissions = collect($permissionsCollection)->map(function ($obj, $permissionName) {
            return $obj->name;
        });
        $permissionFiltered = $permissions->filter(
            function ($value, $key) use ($permissionName) {
                return $value === $permissionName;
            }
        );
        return $permissionFiltered->count() > 0;
    }
}
