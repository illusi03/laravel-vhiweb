<?php

namespace App\Services\Permission;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use App\Models\Permission;

class IndexService extends BaseCurrentService
{
    public function run()
    {
        $permissions = collect(Permission::get())->map(function ($obj) {
            return $obj->name;
        });
        return $this->showResponse($permissions);
    }
}
