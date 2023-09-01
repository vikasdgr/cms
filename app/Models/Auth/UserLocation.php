<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
use App\Models\Auth\User;
use App\Models\Masters\Location;

class UserLocation extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = 'user_locations';
    protected $fillable = ['user_id', 'loc_id'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'loc_id', 'id');
    }
}
