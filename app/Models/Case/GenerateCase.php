<?php

namespace App\Models\Masters;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class GenerateCase extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "cases";
    protected $connection = 'mysql';
    protected $fillable = [
        'machine_id',
        'open_date',
        'closed_date',
        'work_types',
        'work_order_types',
        'status',
        'description',
        'generated_user_id',
    ];

}
