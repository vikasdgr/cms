<?php

namespace App\Http\Controllers\Users;

use Inertia\Inertia;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Models\Auth\UserRole;
use App\Models\Auth\UserSite;
use App\Models\Masters\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\Auth\Preference;
use App\Models\Auth\UserDepartment;
use App\Models\Masters\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // if($request->headers->has('x-inertia')){
            return Inertia::render('ProjectComponents/Users/UserList', [
                'roles' => Role::all()->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                }),
            ]);
        // }

    }

    public function userslist(Request $request){
        $count = User::count();
        $filteredCount = $count;

        $users = User::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $users = $users->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $users = $users->orderBy($field_name, $asc_desc);
        }
        else{
            $users = $users->orderBy('id','DESC');

        }

        $users = $users->take($request->length);
        $filteredCount = $users->count();
        if ($request->start > 0) {
            $users->skip($request->start);
        }
        $users = $users->select()->with('roles','departments')->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $users,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function validateForm($request){
        $rules =[
            'name' => 'required|max:255',
            'role_id' => 'required|integer|min:0',
            'email' => 'required|email|max:255|unique:users,email,' . $request['form_id'],
        ];
        if($request->form_id ==0){
            $rules+=[
                'password' => 'required|min:6|confirmed',
            ];
        }
        else if(isset($request->password) &&  $request->password != ''){
            $rules+=[
                'password' => 'required|min:6|confirmed',
            ];
        }
        $this->validate($request, $rules);


    }

    public function store(Request $request){
        $this->validateForm($request);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request['password']),
        ]);
        $user = UserRole::create([
            'user_id' => $user->id,
            'role_id' =>  $request->role_id
        ]);

        // $user = UserProfile::create([
        //     'user_id' => $user->id,
        //     'leave_date' => $request->leave_date,
        //     'joining_date' => $request->joining_date,
        // ]);

        return reply(true,[
            'user'=>$user
        ]);
    }

    public function edit($id){
        $user = User::findOrFail($id);
        if($user){
            $user->load('roles');
        }
        return reply(true,[
            'user'=>$user ]);
    }

    public function update(Request $request,$id){
        $this->validateForm($request);
        $user = User::findOrFail($id);
        $user->name = $request['name'];
        $user->email = $request['email'];
        if (isset($request['password']) && $request['password']  != '') {
            $user->password = Hash::make($request['password']);
        }

        $user->update();
        if (UserRole::where('user_id', $user->id)->first()) {
            $user_role = UserRole::where('user_id', $user->id)->first()->update(['role_id' => $request->role_id]);
        } else {
            $user = UserRole::create([
                'user_id' => $user->id,
                'role_id' =>  $request->role_id
            ]);
        }
        return reply(true,[
            'user'=>$user ]);
    }


    public function departments($userid)
    {
        $user = User::findOrFail($userid);
        $user_departments = $user->departments;
        return Inertia::render('ProjectComponents/Users/UserDepartments', [
            'user'=>$user,
            'user_departments'=>$user_departments
        ]);
    }


    public function UserDepartments(Request $request){
        $user_id = $request->user_id;
        $user = User::findOrFail($user_id);
        $old_ids = $user->user_departments->pluck('id')->toArray();

        $user_depart = new Collection();
        foreach($request->department_ids as $user_dept){
            $user_c = UserDepartment::firstOrNew(['user_id'=>$user_id,'department_id'=>$user_dept]);
            $user_c->department_id = $user_dept;
            $user_c->user_id = $user_id;
            $user_depart->add($user_c);
        }

        $new_ids = $user_depart->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);


        DB::beginTransaction();
            $user->user_departments()->saveMany($user_depart);
            UserDepartment::whereIn('id',$detach)->delete();
        DB::commit();

        return reply(true,[
            'user_depart' =>$user_depart
        ]);
    }

    public function removeDepartments(Request $request)
    {
        $this->validate($request, [
            'remove_ids' => 'required'
        ]);
        $user = User::findOrFail($request->user_id);
        $user->departments()->detach($request->remove_ids);
        $user_departments = $user->departments;
        $departments = $this->getDepartments($request->user_id);
        return reply(true,[
            'user_departments'=>$user_departments,
            'departments'=>$departments,

        ]);
    }

    public function getDepartments($user_id){
        $userid = $user_id;
        $department = Department::whereNotIn('id', function ($q) use ($userid) {
                    $q->select('department_id')->from('user_departments')->where('user_id', '=', $userid);
                })->orderBy('name')->get(['id', 'name']);
        return $department;
    }

    public function savePreferences(Request $request)
    {
        $preferences  = Preference::firstOrNew(['user_id' => auth()->user()->id,'para_name'=>'sidebar']);
        $preferences->para_value = $request->sidebar;
        $preferences->save();
        return reply(true);
    }




    public function activeDeactive(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->active = $request->status;
        $user->save();
        return reply(true,[
            'user'=>$user,
        ]);
    }

}
