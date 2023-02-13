<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class IndexService extends BaseCurrentService
{
    public function run()
    {
        $roles = collect(Role::orderBy('updated_at', 'desc')->get())->map(function ($obj) {
            $created_at = $obj->created_at->format('Y-m-d H:i:s');
            $updated_at = $obj->updated_at->format('Y-m-d H:i:s');
            $permissions = $this->getPermissionsFromRole($obj->name);
            $isDefault = false;
            $setting = Setting::whereKeyColumn('default_role')->first();
            $defaultRole = Arr::get($setting, 'value_column');
            if ($defaultRole == $obj->name) $isDefault = true;
            $returnNya = [
                'name' => $obj->name,
                'is_default' => $isDefault,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
                'permissions' => $permissions,
            ];
            return $returnNya;
        });
        return $this->showResponse($roles);
    }
}
