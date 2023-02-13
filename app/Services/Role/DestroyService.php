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
        if (!$tmpRole) {
            return $this->roleResponse('role not found', true);
        }
        $currentUser = Auth::user();
        $currentRoles = $currentUser->getRoleNames()->toArray();
        if (in_array($name, $currentRoles)) {
            return $this->roleResponse('cannot delete current role', true);
        }
        $setting = Setting::whereKeyColumn('defaultRole')->first();
        $defaultRole = Arr::get($setting, 'value_column');
        if ($name == $defaultRole) {
            return $this->roleResponse('cannot delete default role', true);
        }
        if ($name == 'superadmin') {
            return $this->roleResponse('cannot mutate superadmin role', true);
        }
        $forShow = $tmpRole;
        $forShow['permissions'] = $this->getPermissionsFromRole($name);
        $tmpRole->delete();
        return $this->roleResponse([
            'message' => 'role has deleted',
            'data' => $forShow,
        ]);
    }
}
