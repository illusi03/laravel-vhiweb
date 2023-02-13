<?php

namespace App\Services;

use League\Fractal;
use Illuminate\Support\Carbon;

class BaseTransformer extends Fractal\TransformerAbstract
{
    protected function transformDateTime($data)
    {
        $timezone = config('app.timezone');
        if (!$data) return $data;
        return Carbon::parse($data)
            ->setTimezone($timezone)
            ->format('Y-m-d H:i:s');
    }
}
