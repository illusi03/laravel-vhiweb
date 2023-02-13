<?php

namespace App\Services\Photo;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Arr;

class DeleteService extends BaseCurrentService
{
    public function run($id)
    {
        $photo = Photo::onlySelf()->findOrFail($id);
        $photo->delete();
        return $this->showResponse($photo, 'data has been deleted');
    }
}
