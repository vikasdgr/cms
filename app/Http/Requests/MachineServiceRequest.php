<?php

namespace App\Http\Requests;

use App\Models\Services\MachineCase;
use App\Models\Services\MachineService;
use App\Models\Services\ServiceCapturedProblem;
use App\Models\Services\ServiceMaintenance;
use App\Models\Services\ServiceResolvingAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MachineServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'service_date' =>'required|date_format:d-m-Y',
            'technician_name'=>'required|max:120',
            'case_id'=>'required|exists:cases,id',
            'status' =>'required|in:C,F'
        ];

        if($this->service_type == 'I' || $this->status == 'F'){
            $rules += [
                'remarks' =>'required|max:1500',
            ];
        }
        if($this->service_type == 'M'){
            $rules += [
                'maintenances' =>'required|Array|min:1',
            ];
        }
        if($this->service_type == 'R'){
            $rules += [
                'problems' =>'required|Array|min:1',
                'resolving_actions' =>'required|Array|min:1',
            ];
        }
        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'status.required' => 'Please provide the Case Status.',
            'maintenance_ids.required' => 'Please add Maintenance actions.',
            'problem_ids.required' => 'Please add the detected issues.',
            'resolving_ids.required' => 'Please add the resolving actions.',
        ];
        return $messages;
    }

    public function save()
    {
        $case = MachineCase::findOrFail($this->case_id);
        $service = MachineService::findOrNew($this->form_id);
        $service->fill($this->all());
        $problem_old_ids = $service->service_problems()->pluck('id')->toArray();
        $maintenance_old_ids = $service->service_maintenances()->pluck('id')->toArray();
        $resolving_old_ids = $service->service_resolving_actions()->pluck('id')->toArray();

        $problems_coll = new Collection();
        $maintenances_coll = new Collection();
        $resolving_actions_coll = new Collection();

        foreach($this->problems as $problem){
            $prob = ServiceCapturedProblem::firstOrNew(['service_id'=>$service['id'],'problem_id'=> $problem['problem_id']]);
            $prob->is_remarks = $problem['is_remarks'];
            $prob->remarks = $problem['remarks'];
            $problems_coll->add($prob);
        }

        foreach($this->maintenances as $maintenance){
            $main = ServiceMaintenance::firstOrNew(['service_id'=>$service['id'],'maintenance_id'=> $maintenance['maintenance_id']]);
            $main->is_remarks = $maintenance['is_remarks'];
            $main->remarks = $maintenance['remarks'];
            $maintenances_coll->add($main);
        }

        foreach($this->resolving_actions as $resolving_action){
            $resolving_action = ServiceResolvingAction::firstOrNew(['service_id'=>$service['id'],'resolving_action_id'=> $resolving_action['resolving_action_id']]);
            $resolving_action->is_remarks = $resolving_action['is_remarks'];
            $resolving_action->remarks = $resolving_action['remarks'];
            $resolving_actions_coll->add($resolving_action);
        }
        $new_detach_prob_ids =$problems_coll->pluck('id')->toArray();
        $new_detach_main_ids =$maintenances_coll->pluck('id')->toArray();
        $new_detach_res_ids =$resolving_actions_coll->pluck('id')->toArray();

        $detach_prob = array_diff($new_detach_prob_ids,$problem_old_ids);
        $detach_main = array_diff($new_detach_main_ids,$maintenance_old_ids);
        $detach_res = array_diff($new_detach_res_ids,$resolving_old_ids);

        $case->status =$service->status;
        if($case->status == 'C'){
            $case->closed_date = getToday();
        }
        if($this->form_id == 0){
            $completed_service = MachineService::where('case_id', $this->case_id)->where('status','C')->first();
            if($completed_service){
                throw ValidationException::withMessages(['gen_msg'=>'Case has already been closed. you can not add follow-up service']);
            }
            $parent_service = MachineService::where('case_id', $this->case_id)->where('parent_service_id',0)->first();
            if($parent_service){
                $service['parent_service_id']  = $parent_service['id'];
            }
        }

        DB::beginTransaction();
            $case->save();
            if($this->form_id == 0){
                $no_generate =  'SR -' . getNextSeriesNumber('services');
                $service['service_no']  = $no_generate;
            }
            $service['service_time']  = getNowTime();
            $service->save();
            $service->service_problems()->saveMany($problems_coll);
            $service->service_maintenances()->saveMany($maintenances_coll);
            $service->service_resolving_actions()->saveMany($resolving_actions_coll);
            ServiceCapturedProblem::whereIn('id',$detach_prob)->delete();
            ServiceMaintenance::whereIn('id',$detach_main)->delete();
            ServiceResolvingAction::whereIn('id',$detach_res)->delete();
        DB::commit();

        return reply(true,[
            'service'=>$service
        ]);
    }
}


