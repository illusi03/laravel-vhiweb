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
use App\Traits\IndonesiaAreaSinasTrait;

class UpdateWithApiKeyService extends BaseCurrentService
{
    use IndonesiaAreaSinasTrait;

    private function updateUser($username, $value)
    {
        $type = Arr::get($value, 'type');
        $user = User::withTrashed()->whereUsername($username)->latest()->first();
        if (!$user) {
            throw new Exception("User not found by username", 1);
        }
        $userValue = [
            'name' => Arr::get($value, 'name'),
            'email' => Arr::get($value, 'email'),
            'type' => $type,
        ];
        $user->update($userValue);
        if ($type == 'produsen-cpo') {
            $user->syncRoles(3);
        } else if ($type == 'produsen-migor') {
            $user->syncRoles(4);
        }
        return $user;
    }

    private function updateStakeholder($userId, $value)
    {
        $stakeholder = Stakeholder::withTrashed()->whereUserId($userId)->latest()->first();
        if (!$stakeholder) {
            throw new Exception("Stakeholder not found by user_id", 1);
        }
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
            'status' => 1,
            'kind' => 'company'
        ];
        $stakeholder->update($stakeholderValue);
        return $stakeholder;
    }

    public function executeUpdate($username, $value)
    {
        $user = $this->updateUser($username, $value);
        $userId = $user->id;
        $value['user_id'] = $userId;
        $stakeholder = $this->updateStakeholder($userId, $value);
        return [
            'user' => $user,
            'stakeholder' => $stakeholder
        ];
    }

    public function run()
    {
        $dirtyValue = request()->all();
        $username = request()->route('username');
        $user = User::withTrashed()->whereUsername($username)->latest()->first();
        if (!$user) {
            return $this->showResponseNotFound();
        }
        unset($dirtyValue['province_id']);
        unset($dirtyValue['city_id']);
        unset($dirtyValue['district_id']);
        unset($dirtyValue['village_id']);
        $dirtyValue['province_id'] = $this->getProvinceCodeByReq();
        $dirtyValue['city_id'] = $this->getCityCodeByReq();
        // $dirtyValue['district_id'] = $this->getDistrictdByReq();
        // $dirtyValue['village_id'] = $this->getVillageByReq();
        DB::beginTransaction();
        try {
            $resultExecute = $this->executeUpdate($username, $dirtyValue);
            $user = Arr::get($resultExecute, 'user');
            $stakeholder = Arr::get($resultExecute, 'stakeholder');
            $result = [
                'user' => $user,
                'stakeholder' => $stakeholder
            ];
            DB::commit();
            return $this->showResponse($result);
        } catch (CustomValidationException $e) {
            DB::rollBack();
            return $this->showResponseError($e->getDataResponse());
        } catch (Exception $e) {
            DB::rollBack();
            return $this->showResponseError($e->getMessage());
        }
    }
}
