<?php

namespace App\Services\MainTransaction\Transformer;

use App\Models\MainTransaction;
use App\Models\Stakeholder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use League\Fractal;
use App\Services\Cooperation\Transformer\IndexForMainTransactionTransformer as CooperationIndexTransformer;
use Illuminate\Support\Facades\Log;

class IndexTransformer extends BaseTransformer
{
    protected $defaultIncludes = [
        'parent',
        'cooperation',
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

        $resultValue = [
            'id' => (int) Arr::get($item, 'id'),
            'uuid' => Arr::get($item, 'uuid'),
            'doc_no' => Arr::get($item, 'doc_no'),
            'po_number' => Arr::get($item, 'po_number'),
            'date_send' => Arr::get($item, 'date_send'),
            'time_send' => Arr::get($item, 'time_send'),
            'attachment_url' => config('app.web_url').Arr::get($item, 'attachment_url'),
            'volume' => Arr::get($item, 'volume'),
            'measurement' => Arr::get($item, 'measurement'),
            'price' => Arr::get($item, 'price'),
            'volume_received' => Arr::get($item, 'volume_received'),
            'date_received' => Arr::get($item, 'date_received'),
            'volume_conversion' => Arr::get($item, 'volume_conversion'),
            'transportation' => Arr::get($item, 'transportation'),
            'transportation_description' => Arr::get($item, 'description'),
            'type' => Arr::get($item, 'type'),
            'packaging' => Arr::get($item, 'packaging'),
            'price_received' => Arr::get($item, 'price_received'),
            'status_humanize' => Arr::get($item, 'status_humanize'),
            'created_at' => $createdAtConverted,
            'province_distribution' => $item->province,
            'city_distribution' => $item->city,
            'receiver' => $item->receiver,
            'sender' => $item->sender,

        ];
        if ($item->parent === null) {
            $parentVal = [
                'parent' => null
            ];
            $resultValue = array_merge($resultValue, $parentVal);
        }
        if ($item->cooperationRelation === null) {
            $cooperationVal = [
                'cooperation' => null
            ];
            $resultValue = array_merge($resultValue, $cooperationVal);
        }
        return $resultValue;
    }

    public function includeParent(MainTransaction $mainTransaction)
    {
        $parent = $mainTransaction->parent;
        if ($parent === null) return null;
        return $this->item($parent, new ParentTransformer());
    }

    public function includeCooperation(MainTransaction $mainTransaction)
    {
        $cooperationRelation = $mainTransaction->cooperationRelation;
        if ($cooperationRelation === null) return null;
        return $this->item($cooperationRelation, new CooperationIndexTransformer());
    }
}
