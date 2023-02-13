<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class DefaultService extends BaseCurrentService
{
    public function run()
    {
        $roleName = request()->post('name');
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            return $this->showResponseError('role name has not found');
        }
        $setting = Setting::whereKeyColumn('default_role')->first();
        $setting->value_column = $role->name;
        $setting->save();
        $role->touch();
        return $this->showResponse($setting);
    }
}
