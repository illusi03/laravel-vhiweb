<?php

namespace App\Services\MainTransaction\Transformer;

use App\Models\MainTransaction;
use App\Models\Stakeholder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use League\Fractal;

class BaseTransformer extends Fractal\TransformerAbstract
{
    protected function getVolume($item)
    {
        $volume = Arr::get($item, 'volume');
        return $volume;
    }
}
