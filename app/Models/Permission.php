<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    use HasFactory;

    protected $guard_name = 'web';
    protected $hidden = [
        'created_at', 'updated_at', 'pivot', 'guard_name', 'id',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
