<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Traits\SearchableTrait;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use App\Notifications\PasswordReset; // Or the location that you store your notifications (this is default).
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, Notifiable, LogsActivity, SoftDeletes, SoftCascadeTrait, HasJsonRelationships, SearchableTrait;

    // Logging System
    protected static $logName = 'user';
    protected static $logAttributes = ['name', 'email', 'password', 'remember_token', 'telp', 'image_url'];
    protected static $recordEvents = ['created', 'updated', 'deleted'];
    protected static $ignoreChangedAttributes = ['updated_at'];
    protected static $logOnlyDirty = true;
    public static $guard_name = "web"; // For default guardName in user Spatie

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'actual_login_at',
        'actual_login_ip',
        'last_login_at',
        'last_login_ip'
    ];

    protected $searchableColumns = ['name'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot', 'deleted_at', 'deleter_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_notifiable' => 'boolean',
    ];

    protected $dates = ['deleted_at', 'last_login_at'];

    public const TYPE_ADMIN = 'administrator';
    public const TYPE_CUSTOMER = 'customer';

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This model has been ({$eventName})";
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token, $this->email));
    }

    public function scopeFindByEmail($query, $email)
    {
        return $query->whereEmail($email);
    }

    // -- Relations --

    // -- Scopes --


    // -- Builder Function --
    public static function getRoles($id)
    {
        $tmpRoles = User::find($id)->roles;
        if (!$tmpRoles) return false;
        return collect($tmpRoles)->map(function ($obj) {
            return $obj->name;
        });
    }

    protected static function getPermissions($id)
    {
        $tmpPermissions =  User::find($id)->getAllPermissions();
        if (!$tmpPermissions) return false;
        return collect($tmpPermissions)->map(function ($obj) {
            return $obj->name;
        });
    }

    private static function appendRolesAndPermissions($user)
    {
        $id = $user->id;
        $user['roles'] = self::getRoles($id);
        $user['permissions'] = self::getPermissions($id);
        return $user;
    }

    public static function getCurrent()
    {
        $user = Auth::user();
        if (!$user) return null;
        $user = self::appendRolesAndPermissions($user);
        return $user;
    }

    public static function checkExist($credentials)
    {
        $username = Arr::get($credentials, 'username');
        $password = Arr::get($credentials, 'password');
        $user = self::whereUsername($username)->exists();
        if ($user) {
            return [
                'username' => $username,
                'password' => $password
            ];
        } else {
            return false;
        }
    }

    public static function updatePasswordSelf($oldPassword, $newPassword)
    {
        $user = Auth::user();
        if (!$user) return null;
        $oldPasswordUser = $user->password;
        $isSameOldPassword = Hash::check($oldPassword, $oldPasswordUser);
        if (!$isSameOldPassword) return null;
        $newHashedPassword = Hash::make($newPassword);
        $user->password = $newHashedPassword;
        $user->save();
        return $user;
    }

    public function stakeholder()
    {
        return $this->hasOne(Stakeholder::class);
    }

    public function scopeStakeholderCreatedAtBetween(Builder $query, $from, $to): Builder
    {
        $from = $from . ' 00:00:00';
        $to = $to . ' 23:59:59';
        return $query->with([
            'stakeholder',
        ])->whereHas('stakeholder', function ($qStakeholder) use ($from, $to) {
            $qStakeholder->whereBetween(DB::raw('created_at'), [$from, $to]);
        });
    }
}