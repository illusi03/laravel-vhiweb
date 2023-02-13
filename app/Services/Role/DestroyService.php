<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class DestroyService extends BaseCurrentService
{
    public function run($name)
    {
        $tmpRole = Role::where('name', $name)->first();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        if (!$tmpRole) {
            return $this->showResponseNotFound();
        }
        $currentUser = Auth::user();
        $currentRoles = $currentUser->getRoleNames()->toArray();
        if (in_array($name, $currentRoles)) {
            return $this->showResponseError('cannot delete current role');
        }
        $setting = Setting::whereKeyColumn('default_role')->first();
        $defaultRole = Arr::get($setting, 'value_column');
        if ($name == $defaultRole) {
            return $this->showResponseError('cannot delete default role');
        }
        if ($name == 'superadmin') {
            return $this->showResponseError('cannot mutate superadmin role');
        }
        $forShow = $tmpRole;
        $forShow['permissions'] = $this->getPermissionsFromRole($name);
        $tmpRole->delete();
        return $this->showResponse($forShow);
    }
}
