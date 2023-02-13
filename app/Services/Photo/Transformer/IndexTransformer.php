<?php

namespace App\Services\Photo\Transformer;

use App\Models\Photo;
use App\Services\BaseTransformer;

class IndexTransformer extends BaseTransformer
{
    public function transform(Photo $item)
    {
        $createdAtConverted = $this->transformDateTime($item->created_at);
        $updatedAtConverted = $this->transformDateTime($item->updated_at);
        $result = [
            'id' => (int) $item->id,
            'caption' => $item->caption,
            'count_like' => $item->count_like,
            'url' => $item->getMedia('images')?->last()?->getUrl(),
            'url_preview' => $item->getMedia('images')?->last()?->getUrl('preview'),
            'created_at' => $createdAtConverted,
            'updated_at' => $updatedAtConverted,
        ];
        return $result;
    }
}
