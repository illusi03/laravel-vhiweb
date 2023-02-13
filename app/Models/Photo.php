<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Photo extends BaseModel implements HasMedia
{
    use InteractsWithMedia;

    protected $searchableColumns = ['caption'];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();
    }

    public function incrementLike()
    {
        $userId = auth()->user()->id;
        $photoLike = PhotoLike::whereUserId($userId)
            ->wherePhotoId($this->id)
            ->first();
        if ($photoLike) return null;
        PhotoLike::create([
            'user_id' => $userId,
            'photo_id' => $this->id,
        ]);
        $this->count_like += 1;
        $this->save();
        return $this;
    }

    public function decrementLike()
    {
        $userId = auth()->user()->id;
        $photoLike = PhotoLike::whereUserId($userId)
            ->wherePhotoId($this->id)
            ->first();
        if (!$photoLike) return null;
        $photoLike->delete();
        $this->count_like -= 1;
        $this->save();
        return $this;
    }
}
