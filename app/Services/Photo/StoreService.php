<?php

namespace App\Services\Photo;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Arr;

class StoreService extends BaseCurrentService
{
    public function run()
    {
        $dirtyValue = request()->all();
        $photo = Photo::create([
            'caption' => Arr::get($dirtyValue, 'caption'),
        ]);
        $photo->addMediaFromRequest('image')->toMediaCollection('images');
        return $this->showResponse($photo);
    }
}
