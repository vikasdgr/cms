<?php

namespace App\Models\Services;

use App\Models\Masters\Maintenance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ServiceMaintenance extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "service_maintenances";
    protected $connection = 'mysql';
    protected $fillable = [
        'case_id',
        'service_id',
        'maintenance_id',
        'is_remarks',
        'remarks',
    ];

    public function maintenance(){
        return $this->belongsTo(Maintenance::class,'maintenance_id');
    }
}
