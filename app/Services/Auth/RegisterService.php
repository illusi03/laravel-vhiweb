<?php

namespace App\Services\Auth;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class RegisterService extends BaseCurrentService
{
    private function executeSave($value)
    {
        return [
            'user' => null
        ];
    }

    public function run()
    {
        $dirtyValue = request()->all();
        DB::beginTransaction();
        try {
            $resultExecute = $this->executeSave($dirtyValue);
            
            $user = Arr::get($resultExecute, 'user');
            $userId = $user->id;
            $isAttempted = Auth::loginUsingId($userId, true);
            if ($isAttempted) {
                DB::commit();
                return $this->attemptedResponseCustom();
            } else {
                throw new Exception("Cannot get token and attempt user", 1);
            }
        }  catch (Exception $e) {
            DB::rollBack();
            return $this->showResponseServerError($e->getMessage());
        }
    }
}
