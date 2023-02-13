<?php

namespace App\Traits;

use App\Models\User;
use stdClass;
use Illuminate\Support\Facades\Auth;

trait SearchableTrait
{    
    public function scopeSearch($query, $keyword)
    {
        if ($keyword && $this->searchableColumns) {
            $query->whereLike($this->searchableColumns, $keyword);
        }
    }
}
