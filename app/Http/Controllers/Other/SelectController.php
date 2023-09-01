<?php

namespace App\Http\Controllers\Other;

use App\Models\Auth\Role;
use App\Models\Site\Site;
use App\Models\Masters\Uqc;
use App\Models\Masters\Bank;
use App\Models\Masters\City;
use App\Models\Site\SiteDay;
use Illuminate\Http\Request;
use App\Models\Auth\UserSite;
use App\Models\Masters\State;
use App\Models\Masters\Letter;
use App\Models\Masters\PfSite;
use App\Models\Masters\Account;
use App\Models\Masters\Company;
use App\Models\Masters\Formula;
use App\Models\Masters\Location;
use App\Models\Employee\Category;
use App\Models\Masters\Allowance;
use App\Models\Masters\Deduction;
use Illuminate\Support\Facades\DB;
use App\Models\Masters\Department;  //CMS
use App\Models\Masters\InvoiceTerm;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Employee\Designation;
use App\Models\Machine\Machine;
use App\Models\Masters\Area;
use App\Models\Masters\Brand;
use App\Models\Masters\MachineModel;
use App\Models\Masters\MachineType;
use App\Models\Masters\Maintenance;
use App\Models\Masters\Problem;
use App\Models\Masters\ResolvingAction;

class SelectController extends Controller
{



    //CMS


    public function departmentsFiltered(Request $request){                      //CMS
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Department::orderBy('name');
        $list->where('name', 'like', $search . '%')->orderBy('name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('departments.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }




    public function departmentUserFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = User::orderBy('users.name')->join('user_departments','user_departments.user_id','=','users.id');
        $list->where('users.name', 'like', $search . '%')->where('user_departments.department_id',$request->department_id);
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('users.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }


    public function machineTypesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = MachineType::orderBy('name');
        $list->where('name', 'like', $search . '%')->orderBy('name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('machine_types.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function machineModelsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = MachineModel::orderBy('model_no');
        $list->where('model_no', 'like', $search . '%')->orderBy('model_no');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('machine_models.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function areasFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Area::orderBy('name');
        $list->where('name', 'like', $search . '%')->orderBy('name');

        if(isset($request->department_id) && $request->department_id > 0){
            $list->where('areas.department_id', '=', $request->department_id);
        }

        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }


        $list = $list->select('areas.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function brandsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Brand::orderBy('name');
        $list->where('name', 'like', $search . '%')->orderBy('name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('brands.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function problemsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Problem::orderBy('name');
        $list->where('name', 'like', $search . '%')->orderBy('name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('problems.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function resolvingActionsFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = ResolvingAction::orderBy('name');
        $list->where('name', 'like', $search . '%')->orderBy('name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('resolving_actions.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }

    public function maintenancesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Maintenance::orderBy('name');
        $list->where('name', 'like', $search . '%')->orderBy('name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('maintenances.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }
    public function RoleFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Role::orderBy('name');
        $list->where('name', 'like', $search . '%')->orderBy('name');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('roles.*')->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);

    }

    public function machinesFiltered(Request $request){
        $rows = 30;
        if ($request->has('term')) {
            $search = $request->has('term') ? $request->input('term', '') : '';
        } else {
            $search = $request->input('search', '');
        }
        $list = Machine::orderBy('serial_no');
        $list->where('serial_no', 'like', $search . '%')->orderBy('serial_no');
        $count_filtered = $list->count();

        if ($request->has('page')) {
            if ($request->page > 1) {
                $list->skip(($request->page - 1) * $rows);
            }
            $list = $list->take($rows);
        }

        $list = $list->select('machines.*')->with(['area','department','machine_model','machine_type','brand'])->get();
        return reply(true, [
            'results' => $list,
            'count_filtered' => $count_filtered
        ]);
    }




}
