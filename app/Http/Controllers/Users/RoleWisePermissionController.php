<?php

namespace App\Http\Controllers\Users;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Auth\Permission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RoleWisePermissionController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('ProjectComponents/Users/RoleWisePermissionReport');
    }

    public function getData(Request $request){
        $permissions = Permission::join('permission_groups', 'permissions.opt_group_id', '=', 'permission_groups.id')
                        ->leftJoin('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
                        ->leftJoin('role_user', 'permission_role.role_id', '=', 'role_user.role_id')
                        ->leftJoin('roles', 'permission_role.role_id', '=', 'roles.id')
                        ->leftJoin('users', 'role_user.user_id', '=', 'users.id')
                        ->select(['permission_groups.opt_group','permissions.label','users.name','permission_groups.order_no','roles.name','roles.id as role_id'])
                        ->orderBy('permission_groups.id')->orderBy('permissions.label');
                    //    ->where('users.active','=','Y')
        if ($request->role_id > 0) {
            $permissions = $permissions->where(DB::raw('roles.id'),$request->role_id);
        }
        $permissions =   $permissions->get();
        return reply('OK', ['data'=> $permissions]);
    }
}
