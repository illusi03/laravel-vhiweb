<?php

namespace App\Services\Photo;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Arr;

class UnlikeService extends BaseCurrentService
{
    public function run($id)
    {
        $photo = Photo::onlySelf()->findOrFail($id);
        $result = $photo->decrementLike();
        if (!$result) return $this->showResponseError('data already unliked');
        return $this->showResponse($photo, 'data has been unliked');
    }
}
