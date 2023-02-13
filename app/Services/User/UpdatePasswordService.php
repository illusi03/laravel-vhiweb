<?php

namespace App\Services\User;

use App\Models\User;

class UpdatePasswordService extends BaseCurrentService
{
    public function run()
    {
        $oldPassword = request()->post('old_password');
        $newPassword = request()->post('password');
        $user = User::updatePasswordSelf($oldPassword, $newPassword);
        if (!$user) return $this->showResponseError('wrong old password');
        return $this->showResponse($user);
    }
}
