<?php

namespace App\Services\MainTransaction\Transformer;

use App\Models\MainTransaction;
use App\Models\Stakeholder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use League\Fractal;

class ParentTransformer extends BaseTransformer
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
            'doc_no' => Arr::get($item, 'doc_no'),
            'volume' => $this->getVolume($item),
            'measurement' => Arr::get($item, 'measurement'),
            'price' => Arr::get($item, 'price'),
            'type' => Arr::get($item, 'type'),
            'packaging' => Arr::get($item, 'packaging'),
            'status_humanize' => Arr::get($item, 'status_humanize'),
            'receiver' => $item->receiverParent,
            'sender' => $item->sender,
            'province_distribution' => $item->province,
            'city_distribution' => $item->city,
        ];
    }
}
