<?php

namespace App\Models\Auth;

use App\Models\Masters\Department;
use App\Models\Site\Site;
use App\Models\Masters\Location;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    public function hasRole($role)
    {
        if (is_string($role)) {
            $collection = $this->roles->filter(function ($item) use ($role) {
                return strtolower($item['name']) == strtolower($role);
            });
            return count($collection) ? true : false;
        }

        return !!$role->intersect($this->roles)->count();
    }

    public function isAdmin()
    {
        if ($this->hasRole('Admin')) {
            return true;
        }
        return false;
    }


    public function departments()
    {
        return $this->belongsToMany(Department::class, 'user_departments', 'user_id', 'department_id');
    }

    public function user_departments()
    {
        return $this->hasMany(UserDepartment::class, 'user_id', 'id');
    }

}
