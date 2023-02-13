<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cooperation extends BaseModel
{
    protected $table = 'cooperations';

    public function receiverParent()
    {
        return $this->belongsTo(Stakeholder::class, 'stakeholder_id')
            ->select([
                'id',
                'name',
                'nib',
                'uuid',
                'province_id',
                'city_id',
                'district_id',
                'village_id'
            ])->with(['province', 'city', 'district', 'village']);
    }

    public function stakeholder()
    {
        return $this->belongsTo(Stakeholder::class, 'stakeholder_id')
            ->select([
                'id',
                'name',
                'nib',
                'uuid',
                'application_id',
                'province_id',
                'city_id',
                'district_id',
                'village_id'
            ])->with(['province', 'city', 'district', 'village']);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeQueryIsActive($query)
    {
        return $query->where(['status' => true]);
    }

    public function scopeQueryList($query)
    {
        return $query->queryIsActive()->with([
            'receiverParent',
            'user'
        ])->whereHas('receiverParent', function ($qReceiverParent) {
            $qReceiverParent->where('type', 'exportir');
        });
    }
}
