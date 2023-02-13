<?php

namespace App\Services\Auth;

use App\Repositories\UserRepository;
use Illuminate\Support\Arr;

class ChangePassService extends BaseCurrentService
{
    public function run()
    {
        $values = request()->all();
        return $this->showResponseMaintenance();
    }
}
