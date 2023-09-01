<?php

namespace App\Http\Requests;

use App\Models\Attachment\SharedResource;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeDependent;
use App\Models\Supervisor\SupervisorEmployee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EmployeeRequest extends FormRequest
{
    protected $join_date = null;
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
        next_emp_code($this->site_id);
        $today = Carbon::today();
        $dt = $today->subDays(4);
        $this->join_date = $dt;
        $join = Carbon::createFromFormat('d-m-Y', $this->joining_date);

        $rules =  [
            // 'emp_code' => 'required|string|max:100|unique:employees,emp_code,' . $this->form_id,
            'emp_name' => 'required|max:50',
            'father' => 'required|max:50',
            'gender' => 'required',
            'dob' => 'required',
            'site_id' => 'required|exists:sites,id',
            'dept_id' => 'required',
            'desig_id' => 'required',
            // 'wage_reg_no' => 'sometimes|numeric',
            'bank_code' => 'nullable|alpha_num|min:11|max:11|regex:/(^[A-Za-z0-9 ]+$)+/',
            'qualification' => 'nullable|string|max:150',
            'refer_by' => 'required|in:Internal Placement Team,Internal Contractual Team,Client/Company,Sub Contractor,Other',
            'refer_by_name' => 'required|string|max:100',
            'refer_by_add' => 'required|string|max:255',
            'acc_no' => 'nullable|regex:/(^[A-Za-z0-9 ]+$)+/',
            // 'bank_code' => 'nullable|string|max:11|',
            'mobile_no' => 'required|min:10|max:10|regex:/(^[A-Za-z0-9 ]+$)+/',
            'joining_date' => 'required',
            'aadhar_no' => 'required|min:12|max:12|regex:/(^[A-Za-z0-9 ]+$)+/',
            'esi_no'  =>  'nullable|min:10|max:10|regex:/(^[A-Za-z0-9 ]+$)+/',
            'uan_no'  =>  'nullable|min:12|max:12|regex:/(^[A-Za-z0-9 ]+$)+/',
            'per_contact'  =>  'nullable|min:10|max:10|regex:/(^[A-Za-z0-9 ]+$)+/',
            'alt_no'  =>  'nullable|min:10|max:10|regex:/(^[A-Za-z0-9 ]+$)+/',
            'pf_no'  =>  'nullable|max:22',
            'pwf_no'  =>  'nullable|max:20',
            'state_id' => 'required|exists:states,id',

        ];

        if ($this->ot_applicable == 'Y') {
            $rules += [
                'ot_hours' => 'required',
                'ot_26day'=>'required',
                'ot_calc' => 'required_without:ot_rate',
                'ot_rate' => 'required_without:ot_calc',
                'esi_ot' => 'required',
                'esi_ot2' => 'required',
            ];
        }

        if ($this->ag_comm_applicable == 'Y') {
            $rules += [
                'ag_comm_type' => 'required',
                'ag_comm_ot_type' => 'required',
                'ag_comm_amt' => 'nullable|numeric|required_if:ag_comm_type,A,ag_comm_type,M,ag_comm_type,V',
                'ag_comm_per' => 'nullable|numeric|required_if:ag_comm_type,P|max:100',
                'ag_comm_on' => 'required_if:ag_comm_type,P',
                'ag_comm_ot_per' => 'nullable|numeric|required_if:ag_comm_ot_type,P|max:100',
                'ag_comm_amt' => 'nullable|numeric|required_if:ag_comm_ot_type,A,ag_comm_ot_type,M,ag_comm_ot_type,V',
            ];
        }
        if ($this->bonus_applicable == 'Y') {
            $rules += [
                'formula_id_bonus' => 'required|integer|not_in:0',
                'bonus_prec' => 'required|numeric',
                'formula_id_bonus_days' => 'required|integer|not_in:0'
            ];
        }


        if ($join->lessThan($dt)) {
            $rules += [
                'joining_date' => 'required',
            ];
        }

        if (isDate80orMoreYearsOld($this->dob))
            $rules += ['under_age' => 'required',];

        if (Gate::denies('ignore-kyc')) {
            if (isset($employee) && strlen($employee->acc_no) > 0) {
                if ($employee->acc_no != $this->acc_no) {
                    $rules += ['acc_no_kyc' => "required"];
                }
            }
            if (isset($employee) && strlen($employee->bank_code) > 0) {
                if ($employee->bank_code != $this->bank_code) {
                    $rules['bank_code_kyc'] = "required";
                }
            }
            if (isset($employee) && strlen($employee->pan_no) > 0) {
                if ($employee->pan_no != $this->pan_no) {
                    $rules['pan_no_kyc'] = "required";
                }
            }
            if (isset($employee) && strlen($employee->pf_no) > 0) {
                if ($employee->pf_no != $this->pf_no) {
                    $rules['pf_no_kyc'] = "required";
                }
            }
            if (isset($employee) && strlen($employee->esi_no) > 0) {
                if ($employee->esi_no != $this->esi_no) {
                    $rules['esi_no_kyc'] = "required";
                }
            }
            if (isset($employee) && strlen($employee->uan_no) > 0) {
                if ($employee->uan_no != $this->uan_no) {
                    $rules['uan_no_kyc'] = "required";
                }
            }
            if (isset($employee) && strlen($employee->pf_date) > 0) {
                if ($employee->pf_date != $this->pf_date) {
                    $rules['pf_date_kyc'] = "required";
                }
            }
        }
        if ($this->bonus_applicable == 'Y') {
            $rules['formula_id_bonus'] = "required|integer|not_in:0";
            $rules['bonus_prec'] = "required|numeric";
            $rules['formula_id_bonus_days'] = "required|integer|not_in:0";
        }
        if ($this->employee_id > 0) {
            $rules['emp_code'] = "required|max:20|unique:employees,emp_code," . $this->employee_id;
        }
        if ($this->bank_code) {
            $ifsc = substr($this->bank_code, 0, 4);
            foreach (getInvalidIfscCode() as $ifsc_code) {
                if ($ifsc == $ifsc_code) {
                    $rules += [
                        'ifsc_msg' => 'required',
                    ];
                }
            }
        }
        return $rules;
    }

    public function save(){
        $employee = Employee::findOrNew($this->form_id);
        $employee->fill($this->all());
        $employee->s_no = 0;
        if($this->form_id == 0){
            $generated_no = next_emp_code($this->site_id);
            $employee->emp_code =$generated_no;
            $employee->wage_reg_no = getNextSeriesNumber('wage_reg_no_' . $this->site_id);
        }
        $employee_old_det_ids = $employee->employee_dependents()->pluck('id')->toArray();
        $employee_dependents = new Collection();
        foreach($this->employee_dependents as $dependent){
            $dep = EmployeeDependent::findOrNew($dependent['id']);
            $dep->fill($dependent);
            $employee_dependents->add($dep);
        }

        $emp_new_det_ids = $employee_dependents->pluck('id')->toArray();
        $detach = array_diff($employee_old_det_ids,$emp_new_det_ids);


        $old_resource_ids = $employee->resources()->pluck('id')->toArray();
        $resources = new Collection();
              foreach($this->resources as $resource){
                  $resource_d = SharedResource::findOrNew($resource['id']);
                  $resource_d->resourceable_type = Employee::class;
                  $resource_d->fill($resource);
                  $resources->add($resource_d);
              }
        $new_resource_ids = $resources->pluck('id')->toArray();
        $detach = array_diff($old_resource_ids,$new_resource_ids);

        DB::beginTransaction();
            $employee->save();
            $employee->employee_dependents()->saveMany($employee_dependents);
            EmployeeDependent::whereIn('id',$detach);
            $employee->resources()->saveMany($resources);
            SharedResource::whereIn('id',$detach)->delete();

        DB::commit();


        return reply(true, [
            'employee' => $employee
        ]);
    }
}
