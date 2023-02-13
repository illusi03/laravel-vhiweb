<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Support\Arr;
use App\Traits\SearchableTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaseModel extends Model
{
    use SoftDeletes, SoftCascadeTrait, SearchableTrait, HasFactory;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at',
    ];
    protected $hidden = [
        'deleted_at', 'deleted_by'
    ];
    protected $dates = ['deleted_at'];
    protected $fieldUser = ['id', 'name', 'email'];
    protected $searchableColumns = [];
    protected $casts = [
        'additional_data' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        $isLoggedIn = Auth::check();
        $userId = null;
        if ($isLoggedIn) $userId = Arr::get(Auth::user(), 'id');
        // Default Scope Order By Id
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
        static::retrieved(function ($model) {
            // Code Here
        });
        self::creating(function ($model) use ($userId) {
            $model->created_by = $userId;
        });
        self::created(function ($model) {
            // Code Here
        });
        self::updating(function ($model) use ($userId) {
            // $model->updated_by = $userId;
            // $model->save();
        });
        self::updated(function ($model) {
            // Code Here
        });
        self::saving(function ($model) {
            // Code Here
        });
        self::saved(function ($model) {
            // Code Here
        });
        self::deleting(function ($model) use ($userId) {
            // $model->deleted_by = $userId;
            // $model->save();
        });
        self::deleted(function ($model) {
            // Code Here
        });
        self::restoring(function ($model) {
            // $model->deleted_by = null;
            // $model->save();
        });
        self::restored(function ($model) use ($userId) {
            // Code Here
        });
        self::replicating(function ($model) {
            // Code Here
        });
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select($this->fieldUser);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->select($this->fieldUser);
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->select($this->fieldUser);
    }

    public function scopeGetCreatorUpdater($query)
    {
        return $query->with(['creator', 'updater']);
    }

    public function scopeOnlySelf($query, $attribute = 'created_by')
    {
        $user = Auth::user();
        if (!$user) return $query;
        $userId = Auth::user()->id;
        return $query->where($attribute, $userId);
    }

    public function scopeQueryAll($query)
    {
        return $query->with(['creator', 'updater']);
    }

    public function scopeQueryDetail($query)
    {
        return $query->with(['creator', 'updater']);
    }
}
