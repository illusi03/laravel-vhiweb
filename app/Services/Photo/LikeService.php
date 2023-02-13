<?php

namespace App\Services\Photo;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Arr;

class LikeService extends BaseCurrentService
{
    public function run($id)
    {
        $photo = Photo::onlySelf()->findOrFail($id);
        $result = $photo->incrementLike();
        if (!$result) return $this->showResponseError('data already liked');
        return $this->showResponse($photo, 'data has been liked');
    }
}
