<?php

namespace App\Models\Services;

use App\Models\Auth\User;
use App\Models\Machine\Machine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class MachineCase extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "cases";
    protected $connection = 'mysql';
    protected $fillable = [
        'case_no',
        'machine_id',
        'open_date',
        'closed_date',
        'work_types',
        'work_order_types',
        'status',
        'description',
        'generated_user_id',
    ];


    public function machine(){
        return $this->belongsTo(Machine::class,'machine_id');
    }
    public function getOpenDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setOpenDateAttribute($date)
    {
        $this->attributes['open_date'] = setDateAttribute($date);
    }

    public function getClosedDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setClosedDateAttribute($date)
    {
        $this->attributes['closed_date'] = setDateAttribute($date);
    }

    public function generated_user(){
        return $this->belongsTo(User::class,'generated_user_id');
    }
}
