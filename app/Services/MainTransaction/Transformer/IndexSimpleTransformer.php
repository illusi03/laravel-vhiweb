<?php

namespace App\Services\MainTransaction\Transformer;

use App\Models\MainTransaction;
use App\Models\Stakeholder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use League\Fractal;

class IndexSimpleTransformer extends Fractal\TransformerAbstract
{
    private function transformDateTime($data)
    {
        $timezone = config('app.timezone');
        if (!$data) return $data;
        return Carbon::parse($data)
            ->setTimezone($timezone)
            ->format('Y-m-d H:i:s');
    }

    public function transform(MainTransaction $item)
    {
        $createdAtConverted = $this->transformDateTime(Arr::get($item, 'created_at'));
        $updatedAtConverted = $this->transformDateTime(Arr::get($item, 'updated_at'));
        return [
            'id' => (int) Arr::get($item, 'id'),
            'uuid' => Arr::get($item, 'uuid'),
            'stakeholder_id' => Arr::get($item, 'stakeholder_id'),
            'publish' => Arr::get($item, 'publish'),
            'doc_no' => Arr::get($item, 'doc_no'),
            'po_number' => Arr::get($item, 'po_number'),
            'date_send' => Arr::get($item, 'date_send'),
            'attachment_url' => Arr::get($item, 'attachment_url'),
            'time_send' => Arr::get($item, 'time_send'),
            'volume' => Arr::get($item, 'volume'),
            'measurement' => Arr::get($item, 'measurement'),
            'is_full' => Arr::get($item, 'isFull'),
            'parent_id' => Arr::get($item, 'parent_id'),
            'date_received' => Arr::get($item, 'date_received'),
            'price' => Arr::get($item, 'price'),
            'volume_received' => Arr::get($item, 'volume_received'),
            'volume_conversion' => Arr::get($item, 'volume_conversion'),
            'province_id' => Arr::get($item, 'province_id'),
            'city_id' => Arr::get($item, 'city_id'),
            'transportation' => Arr::get($item, 'transportation'),
            'transportation_description' => Arr::get($item, 'description'),
            'type' => Arr::get($item, 'type'),
            'packaging' => Arr::get($item, 'packaging'),
            'price_received' => Arr::get($item, 'price_received'),
            'external_stakeholder' => Arr::get($item, 'external_stakeholder'),
            'status_humanize' => Arr::get($item, 'status_humanize'),
            'created_at' => $createdAtConverted,
            'created_by' => Arr::get($item, 'created_by'),
            'updated_at' => $updatedAtConverted,
            'updated_by' => Arr::get($item, 'updated_by'),
            'receiver' => $item->receiver,
            'sender' => $item->sender,
            'province' => $item->province,
            'city' => $item->city,
        ];
    }
}
