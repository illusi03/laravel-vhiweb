<?php

namespace App\Services\MainTransaction;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\MainTransaction;
use Spatie\Fractalistic\Fractal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Spatie\QueryBuilder\AllowedFilter;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Notification;
use App\Services\MainTransaction\Transformer\IndexTransformer;

class StoreService extends BaseCurrentService
{
    private function generateNumber()
    {
        $userId = auth()->user()->id;
        $code = 'DO';
        $date = date('dmY');
        $dateNow = date('Y-m-d');
        $startDateNow = "$dateNow 00:00:00";
        $endDateNow = "$dateNow 23:59:59";
        $mainTransaction = MainTransaction::whereBetween('created_at', [$startDateNow, $endDateNow])
            ->whereCreatedBy($userId)
            ->latest()
            ->first();
        $number = 0;
        if ($mainTransaction) $number = $mainTransaction->doc_no;
        $numberArr = explode('-', $number);
        $numberInt = (int) Arr::last($numberArr);
        $numberInt++;
        $number = str_pad($numberInt, 5, '0', STR_PAD_LEFT);
        return "$code-$userId-$date-$number";
    }

    private function saveMainTransaction($value)
    {
        $mainTransactionValue = [
            'uuid' => Str::uuid(),
            'doc_no' => $this->generateNumber(),
            'status' => 1,
            'stakeholder_id' => Arr::get($value, 'stakeholder_id'),
            'description' => Arr::get($value, 'description'),
            'publish' => Arr::get($value, 'publish'),
            'po_number' => Arr::get($value, 'po_number'),
            'date_send' => Arr::get($value, 'date_send'),
            'time_send' => Arr::get($value, 'time_send'),
            'volume' => Arr::get($value, 'volume'),
            'measurement' => Arr::get($value, 'measurement'),
            'isFull' => Arr::get($value, 'is_full'),
            'parent_id' => Arr::get($value, 'parent_id'),
            'date_received' => Arr::get($value, 'date_received'),
            'price' => Arr::get($value, 'price'),
            'volume_received' => Arr::get($value, 'volume_received'),
            'volume_conversion' => Arr::get($value, 'volume_conversion'),
            'province_id' => Arr::get($value, 'province_id'),
            'city_id' => Arr::get($value, 'city_id'),
            'transportation' => Arr::get($value, 'transportation'),
            'type' => Arr::get($value, 'type'),
            'price_received' => Arr::get($value, 'price_received'),
            'external_stakeholder' => Arr::get($value, 'external_stakeholder'),
        ];
        $data = MainTransaction::create($mainTransactionValue);
        // Attachment MainTransaction
        $attachmentRequest = Arr::get(request(), 'attachment');
        if ($attachmentRequest) {
            $attachmentName = "TransactionAttachment-$data->id";
            $afterSavedAttachmentName = $this->fileUploadOne($attachmentRequest, $attachmentName, "images");
            $data->attachment_url = $afterSavedAttachmentName;
            $data->save();
        }
        return $data;
    }

    private function executeSave($value)
    {
        $data = $this->saveMainTransaction($value);
        return [
            'main_transaction' => $data,
        ];
    }

    public function run()
    {
        $dirtyValue = request()->all();
        DB::beginTransaction();
        try {
            $resultExecute = $this->executeSave($dirtyValue);
            DB::commit();
            return $this->showResponse($resultExecute);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->showResponseError($e->getMessage());
        }
    }
}
