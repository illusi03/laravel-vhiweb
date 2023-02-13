<?php

namespace App\Services\Photo;

use App\Models\Photo;
use Spatie\Fractalistic\Fractal;
use App\Services\Photo\Transformer\IndexTransformer;

class ShowService extends BaseCurrentService
{
    public function run($id)
    {
        $photo = Photo::onlySelf()
            ->with(['creator', 'updater'])
            ->findOrFail($id);
        return $this->showResponse($photo);
    }
}
