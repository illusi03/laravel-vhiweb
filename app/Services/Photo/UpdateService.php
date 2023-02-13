<?php

namespace App\Services\Photo;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Arr;

class UpdateService extends BaseCurrentService
{
    public function run($id)
    {
        $dirtyValue = request()->all();
        $photo = Photo::onlySelf()->findOrFail($id);
        $photo->caption = Arr::get($dirtyValue, 'caption');
        $photo->save();
        $photo->addMediaFromRequest('image')->toMediaCollection('images');
        return $this->showResponse($photo, 'data has been updated');
    }
}
