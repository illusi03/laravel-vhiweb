<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Setting extends BaseModel
{
    public function scopeGetValue($query, $key)
    {
        $result = $query->whereKeyColumn($key)->first();
        return Arr::get($result, 'value_column');
    }
}
