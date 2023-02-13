<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Support\Facades\Auth;
use App\Traits\SearchableTrait;
class BaseLookupModel extends Model
{
    use SearchableTrait;

    protected $guarded = [];
    protected $searchableColumns = [];
    
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
