<?php

namespace App\Http\Controllers\Other;

use App\Http\Controllers\Controller;
use App\Models\Machine\Machine;
use App\Models\Machine\MachineLocationHistory;
use App\Models\Masters\Brand;
use App\Models\Masters\Department;
use App\Models\Masters\MachineModel;
use App\Models\Masters\MachineType;
use App\Models\Services\MachineCase;
use App\Models\Services\MachineService;
use App\Printings\ServicePrint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultiPurposeController extends Controller
{
    public function getDepartments(Request $request){
        $userid = $request->user_id;
        $departments = Department::whereIn('id', function ($q) use ($userid) {
            $q->select('department_id')
                ->from('user_departments')
                ->where('user_id', '=', $userid);
        })
        ->orderBy('name')
        ->get(['id', 'name']);
        return reply(true, [
            'departments' => $departments,
        ]);
    }

    public function changeDepartment(Request $request){
        $rules = [
            'machine_id'=>'required|exists:machines,id',
        ];

        if(!$request->new_dept_id > 0 && !$request->new_area_id > 0){
            $rules +=[
                'new_dept_id' =>'required|exists:departments,id'
            ];
        }
        $this->validate($request,$rules);

        $machine = Machine::findOrFail($request->machine_id);
        if($request->new_dept_id > 0){
            $machine->department_id =$request->new_dept_id;
            $machine->area_id = 0;
        }
        if($request->new_area_id > 0){
            $machine->area_id =$request->new_area_id;
        }

        $change_department = new MachineLocationHistory();
        $change_department->machine_id = $request['machine_id'];
        $change_department->previous_department_id = $machine['department_id'];
        $change_department->previous_area_id = $machine['area_id'];
        $change_department->current_department_id = $machine['new_dept_id'];
        $change_department->current_area_id = $machine['new_area_id'];
        $change_department->change_date = getToday();


        DB::beginTransaction();
            $change_department->save();
            $machine->save();
        DB::commit();

        return reply(true);

    }


    public function servicePrint($service_id){
        $service = MachineService::findOrFail($service_id);
        $print = new ServicePrint();
        $pdf = $print->makepdf($service);
        $name = "Service".$service['service_no'].$service['service_date'].".pdf";
        $pdf->Output($name, 'I');
    }


    public function getMachineDetails($machine_id){
        $machine = Machine::whereId($machine_id)->with(['department','area'])->first();
        return reply(true, [
            'machine' => $machine,
        ]);
    }


    public function getMachinesData(){
        $no_of_machines = Machine::count();
        $no_of_services = MachineService::count();
        $no_of_cases = MachineCase::count();
        $no_pending = MachineCase::where('status','P')->count();



        $machine_types = MachineType::with('models.brand')->get();
        $brands = Brand::get();
        $models = MachineModel::with('machine_image','brand')->get();
        $departments = Department::with('areas')->get();

        $data=[
            'no_of_machines' => $no_of_machines,
            'no_of_services' => $no_of_services,
            'no_of_cases' => $no_of_cases,
            'no_pending' => $no_pending,
            'machineTypes'=> $machine_types,
            'departments'=> $departments,
            'brands' => $brands

        ];

        $c_data = [];
        $total_rows = MachineModel::count();

        $types = [
            [
                'name'=>'Name',
                'type'=>'caption',
                'id'=>0,
                'span'=> 2,
                'rowspan'=>1
            ],
        ];
        foreach($machine_types as $machine_type){
            $row= [
                'name'=>$machine_type['name'],
                'type'=>'machine_type',
                'id'=>$machine_type['id'],
                'span'=> MachineModel::where('machine_type_id',$machine_type['id'])->count(),
                'rowspan'=>1
            ];
            $types []= $row;
        }

        $c_data []= $types;

        $bran = [
            [
                'name'=>'Brand',
                'type'=>'caption',
                'id'=>0,
                'span'=> 2,
                'rowspan'=>1
            ],
        ];
        foreach($models as $model){
            $row= [
                'name'=>$model['brand']['name'],
                'type'=>'brand',
                'id'=>$model['brand'],
                'image'=>$model['brand'],
                'span'=> 1,
                'rowspan'=>1
            ];
            $bran []= $row;
        }
        // foreach($brands as $brand){
        //     $row= [
        //         'name'=>$brand['name'],
        //         'type'=>'brand',
        //         'id'=>$brand['id'],
        //         'span'=> MachineModel::where('brand_id',$brand['id'])->count(),
        //         'rowspan'=>1
        //     ];
        //     $bran []= $row;
        // }
        $c_data[] = $bran;

        $mods = [
            [
                'name'=>'Model',
                'type'=>'caption',
                'id'=>0,
                'span'=> 2,
                'rowspan'=>1
            ],
        ];
        foreach($models as $model){
            $row= [
                'name'=>$model['model_no'],
                'type'=>'model',
                'id'=>$model['id'],
                'image'=>$model['id'],
                'span'=> 1,
                'rowspan'=>1
            ];
            $mods []= $row;
        }

        $c_data []= $mods;

        $c_data []= [
            [
                'name'=>'Department',
                'type'=>'caption',
                'id'=>0,
                'span'=> 1,
                'rowspan'=>1
            ],
            [
                'name'=>'Area',
                'type'=>'caption',
                'id'=>0,
                'span'=> 1,
                'rowspan'=>1
            ],
            [
                'name'=>' ',
                'type'=>'caption',
                'id'=>0,
                'span'=>$models->count() ,
                'rowspan'=>1
            ]
        ];

        foreach($departments as $department){
            $dept = [[
                'name' =>$department['name'],
                'type'=>'department',
                'id'=>$department['id'],
                'span'=> 1,
                'rowspan'=>$department->areas()->count()
            ]];
            foreach($department->areas as $area){
                $dept [] =[
                    'name' =>$area['name'],
                    'type'=>'area',
                    'id'=>$area['id'],
                    'span'=> 1,
                    'rowspan'=>1
                ];
                foreach($mods as $key=>$mod){
                    if($key > 0){
                        $count = Machine::where('model_id', $mod['id'])->where('department_id',$department['id'])->where('area_id',$area['id'])->count();
                        $dept [] =[
                            'name' =>$count,
                            'type'=>'count',
                            'id'=>0,
                            'span'=> 1,
                            'rowspan'=>1
                        ];
                    }

                }
                $c_data[] = $dept;
                $dept = [];
            }
            // $c_data []= [$row];

        }

        // dd($c_data[26]);

        return reply(true,[
            'data'=>$data,
            'table_data'=>$c_data
        ]);
    }
}
