<?php

namespace App\Services\Auth;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use Exception;
use App\Models\User;
use App\Models\Stakeholder;
use App\Models\Village;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use App\Exceptions\CustomValidationException;
use App\Services\Auth\UpdateWithApiKeyService;

class RegisterWithApiKeyService extends BaseCurrentService
{
    private function saveUser($value)
    {
        $password = Arr::get($value, 'password');
        if ($password) $password = Hash::make($password);
        $type = Arr::get($value, 'type');
        $userValue = [
            'name' => Arr::get($value, 'name'),
            'username' => Arr::get($value, 'username'),
            'email' => Arr::get($value, 'email'),
            'type' => $type,
            'password' => $password
        ];

        $user = User::create($userValue);
        if (!$user) {
            throw new Exception("Error save user", 1);
        }
        if ($type == 'produsen-cpo') {
            $user->syncRoles(3);
        } else if ($type == 'produsen-migor') {
            $user->syncRoles(4);
        }
        return $user;
    }

    private function saveStakeholder($value)
    {
        $stakeholderValue = [
            'application_id' => Arr::get($value, 'application_id'),
            'name' => Arr::get($value, 'name'),
            'address' => Arr::get($value, 'address'),
            'nib' => Arr::get($value, 'nib'),
            'npwp' => Arr::get($value, 'npwp'),
            'pic_name' => Arr::get($value, 'pic_name'),
            'pic_telp' => Arr::get($value, 'pic_telp'),
            'pic_email' => Arr::get($value, 'pic_email'),
            'type' => Arr::get($value, 'type'),
            'user_id' => Arr::get($value, 'user_id'),
            'province_id' => Arr::get($value, 'province_id'),
            'city_id' => Arr::get($value, 'city_id'),
            'district_id' => Arr::get($value, 'district_id'),
            'village_id' => Arr::get($value, 'village_id'),
            'uuid' => "checkin:migor-" . Str::uuid(),
            'status' => 1,
            'kind' => 'company'
        ];
        $stakeholder = Stakeholder::create($stakeholderValue);
        if (!$stakeholder) {
            throw new Exception("Error save stakeholder", 1);
        }
        return $stakeholder;
    }

    private function executeSave($value)
    {
        $user = $this->saveUser($value);
        $value['user_id'] = $user->id;
        $stakeholder = $this->saveStakeholder($value);
        return [
            'user' => $user,
            'stakeholder' => $stakeholder
        ];
    }

    private function checkValidationUsername()
    {
        $dirtyValue = request()->all();
        $username = Arr::get($dirtyValue, 'username');
        $user = User::whereUsername($username)->first();
        if ($user) {
            throw new CustomValidationException('username', 'The username has already been taken.');
        }
        $userDeleted = User::onlyTrashed()->whereUsername($username)->first();
        if ($userDeleted) {
            throw new CustomValidationException('username', 'The username has already been taken from deleted user.');
        }
        return true;
    }

    private function isExistUsername()
    {
        $dirtyValue = request()->all();
        $username = Arr::get($dirtyValue, 'username');
        $user = User::withTrashed()->whereUsername($username)->first();
        if (!$user) return false;
        return true;
    }

    protected function attemptedResponseCustom($additionalData = [])
    {
        $currentUser = User::getCurrent();
        $token = $this->createToken();
        $cookie = $this->getCookieDetails($token);
        $isAdditionalData = is_array($additionalData) & !empty($additionalData);
        $res = [
            'token' => $token,
        ];
        if ($isAdditionalData) $res = array_merge($additionalData, $res);
        return $this->showResponse($res)->cookie(
            $cookie['name'],
            $cookie['value'],
            $cookie['minutes'],
            $cookie['path'],
            $cookie['domain'],
            $cookie['secure'],
            $cookie['httponly']
        );
    }

    private function getEmailOrPicEmail()
    {
        $dirtyValue = request()->all();
        $email = Arr::get($dirtyValue, 'email');
        $picEmail = Arr::get($dirtyValue, 'pic_email');
        if (!$picEmail) $picEmail = $email;
        if (!$email) $email = $picEmail;
        return [
            'email' => $email,
            'pic_email' => $picEmail
        ];
    }

    public function run()
    {
        $updateService = app(UpdateWithApiKeyService::class);
        $dirtyValue = request()->all();
        $name = Arr::get($dirtyValue, 'name');
        if ($name) {
            $dirtyValue['name'] = strtoupper($name);
        }
        unset($dirtyValue['province_id']);
        unset($dirtyValue['city_id']);
        unset($dirtyValue['district_id']);
        unset($dirtyValue['village_id']);
        $dirtyValue['province_id'] = $this->getProvinceCodeByReq();
        $dirtyValue['city_id'] = $this->getCityCodeByReq();
        $dirtyValue['district_id'] = $this->getDistrictCodeByReq();
        $dirtyValue['village_id'] = $this->getVillageCodeByReq();
        $customEmail = $this->getEmailOrPicEmail();
        $dirtyValue['email'] = Arr::get($customEmail, 'email');
        $dirtyValue['pic_email'] = Arr::get($customEmail, 'pic_email');
        DB::beginTransaction();
        try {
            $isExistUsername = $this->isExistUsername();
            $resultExecute = null;
            if ($isExistUsername) {
                $username = Arr::get($dirtyValue, 'username');
                $resultExecute = $updateService->executeUpdate($username, $dirtyValue);
            } else {
                $resultExecute = $this->executeSave($dirtyValue);
            }
            $user = Arr::get($resultExecute, 'user');
            $stakeholder = Arr::get($resultExecute, 'stakeholder');
            $userId = $user->id;
            $isAttempted = Auth::loginUsingId($userId, true);
            if ($isAttempted) {
                $uuid = Arr::get($stakeholder, 'uuid');
                $additionalResult = [
                    'stakeholder_uuid' => $uuid
                ];
                DB::commit();
                return $this->attemptedResponseCustom($additionalResult);
            } else {
                throw new Exception("Cannot get token and attempt user", 1);
            }
        } catch (CustomValidationException $e) {
            DB::rollBack();
            return $this->showResponseError($e->getDataResponse());
        } catch (Exception $e) {
            DB::rollBack();
            return $this->showResponseError($e->getMessage());
        }
    }
}
