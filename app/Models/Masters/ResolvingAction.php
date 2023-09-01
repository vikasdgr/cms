<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ResolvingAction extends Model  //CMS
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "resolving_actions";
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'description',
    ];

}
