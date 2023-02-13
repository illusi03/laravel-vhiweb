<?php

namespace App\Traits;

use App\Models\User;
use stdClass;
use Illuminate\Support\Facades\Auth;

trait UserTraits
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

    /*
  public function getDetailUser($userId) {
    $user = User::find($userId);
    if (!$user) {
      return new stdClass();
    }
    $permissions = User::find($userId)->getAllPermissions();
    $roles = User::find($userId)->roles;
    $user['roles'] = collect($roles)->map(function ($obj) {
      return $obj->name;
    });
    $user['permissions'] = collect($permissions)->map(function ($obj) {
      return $obj->name;
    });
    return $user;
  }
  */
}
