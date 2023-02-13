<?php

namespace App\Services\User\Transformer;

use App\Models\User;
use App\Services\BaseTransformer;

class IndexTransformer extends BaseTransformer
{
    public function transform(User $item)
    {
        $createdAtConverted = $this->transformDateTime($item->created_at);
        $updatedAtConverted = $this->transformDateTime($item->updated_at);
        $result = [
            'id' => (int) $item->id,
            'name' => $item->name,
            'email' => $item->email,
            'created_at' => $createdAtConverted,
            'updated_at' => $updatedAtConverted,
        ];
        return $result;
    }
}
