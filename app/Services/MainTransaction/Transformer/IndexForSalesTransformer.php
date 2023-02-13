<?php

namespace App\Services\MainTransaction\Transformer;

use App\Models\MainTransaction;
use App\Models\Stakeholder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use League\Fractal;

class IndexForSalesTransformer extends BaseTransformer
{
    protected $defaultIncludes = [
        'parent'
    ];

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
        $type = Arr::get($item, 'type');
        $resultValue = [
            'id' => (int) Arr::get($item, 'id'),
            'uuid' => Arr::get($item, 'uuid'),
            'doc_no' => Arr::get($item, 'doc_no'),
            'po_number' => Arr::get($item, 'po_number'),
            'date_send' => Arr::get($item, 'date_send'),
            'time_send' => Arr::get($item, 'time_send'),
            'volume' => $this->getVolume($item),
            'measurement' => Arr::get($item, 'measurement'),
            'date_received' => Arr::get($item, 'date_received'),
            'price' => Arr::get($item, 'price'),
            'transportation' => Arr::get($item, 'transportation'),
            'transportation_description' => Arr::get($item, 'description'),
            'type' => $type,
            'packaging' => Arr::get($item, 'packaging'),
            'status_humanize' => Arr::get($item, 'status_humanize'),
            'created_at' => $createdAtConverted,
            'receiver' => $item->receiver,
            'sender' => $item->sender,
            'province_distribution' => $item->province,
            'city_distribution' => $item->city,
        ];
        if(strtolower($type) == 'cpo') {
            $resultValue = array_merge($resultValue, [
                'volume_conversion' => Arr::get($item, 'volume_conversion')
            ]);
        }
        if ($item->parent === null) {
            $parentVal = [
                'parent' => null
            ];
            $resultValue = array_merge($resultValue, $parentVal);
        }
        return $resultValue;
    }

    public function includeParent(MainTransaction $mainTransaction)
    {
        $parent = $mainTransaction->parent;
        if ($parent === null) return null;
        return $this->item($parent, new ParentTransformer);
    }
}
