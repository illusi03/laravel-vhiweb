<?php

namespace App\Services\User\Transformer;

use App\Models\User;
use App\Models\Stakeholder;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use League\Fractal;
use App\Services\Stakeholder\Transformer\IndexForUserTransformer as StakeholderIndexTransformer;

class IndexTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'profile'
    ];

    private function transformDateTime($data)
    {
        $timezone = config('app.timezone');
        if (!$data) return $data;
        return Carbon::parse($data)
            ->setTimezone($timezone)
            ->format('Y-m-d H:i:s');
    }

    public function transform(User $item)
    {
        $createdAtConverted = $this->transformDateTime(Arr::get($item, 'created_at'));
        $updatedAtConverted = $this->transformDateTime(Arr::get($item, 'updated_at'));
        $result = [
            'id' => (int) Arr::get($item, 'id'),
            'name' => Arr::get($item, 'name'),
            'email' => Arr::get($item, 'email'),
            'type' => Arr::get($item, 'type'),
            'created_at' => $createdAtConverted,
            'updated_at' => $updatedAtConverted,
        ];
        if ($item->stakeholder === null) {
            $result = array_merge($result, ['stakeholder' => null]);
        }
        return $result;
    }

    public function includeProfile(User $user)
    {
        $stakeholder = $user->stakeholder;
        if ($stakeholder === null) return null;
        $stakeholder = $user->stakeholder;
        return $this->item($stakeholder, new StakeholderIndexTransformer);
    }
}
