<?php

namespace App\Services\Cooperation\Transformer;

use App\Models\Cooperation;
use App\Models\Stakeholder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use League\Fractal;
use App\Services\User\Transformer\IndexTransformer as UserIndexTransformer;

class IndexTransformer extends BaseTransformer
{
    protected $defaultIncludes = [
        'user'
    ];

    private function transformDateTime($data)
    {
        $timezone = config('app.timezone');
        if (!$data) return $data;
        return Carbon::parse($data)
            ->setTimezone($timezone)
            ->format('Y-m-d H:i:s');
    }

    public function transform(Cooperation $item)
    {
        $createdAtConverted = $this->transformDateTime(Arr::get($item, 'created_at'));
        $resultValue = [
            'id' => (int) Arr::get($item, 'id'),
            'total' => Arr::get($item, 'total'),
            'measurement' => Arr::get($item, 'measurement'),
            'doc_no' => Arr::get($item, 'doc_no'),
            'attachment_url' => config('app.web_url').Arr::get($item, 'attachment_url'),
            'created_at' => $createdAtConverted,
            'exportir' => $item->receiverParent
        ];
        return $resultValue;
    }

    public function includeUser(Cooperation $cooperation)
    {
        $user = $cooperation->user;
        if ($user === null) return null;
        $user = $cooperation->user;
        return $this->item($user, new UserIndexTransformer);
    }
}
