<?php

namespace App\Models\Services;

use App\Models\Masters\ResolvingAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ServiceResolvingAction extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "service_resolving_actions";
    protected $connection = 'mysql';
    protected $fillable = [
        'case_id',
        'service_id',
        'resolving_action_id',
        'is_remarks',
        'remarks',
    ];

    public function resolving_action(){
        return $this->belongsTo(ResolvingAction::class,'resolving_action_id');
    }
}
