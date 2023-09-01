<?php

namespace App\Models\Machine;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class MachineLocationHistory extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "machines_location_history";
    protected $connection = 'mysql';
    protected $fillable = [
        'machine_id',
        'model_id',
        'previous_area_id',
        'current_area_id',
        'previous_department_id',
        'current_department_id',
        'change_date',
    ];




    public function getChangeDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setChangeDateAttribute($date)
    {
        $this->attributes['change_date'] = setDateAttribute($date);
    }

}
