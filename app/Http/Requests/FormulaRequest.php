<?php

namespace App\Http\Requests;

use App\Models\Masters\EmpPayAllFormula;
use App\Models\Masters\EmpPayAllowanceFormula;
use App\Models\Masters\EmpPayDeductionFormula;
use App\Models\Masters\Formula;
use App\Models\Masters\FormulaDetail;
use App\Models\Site\SiteDeductionFormula;
use App\Models\Site\SitePayAllowanceFormula;
use App\Models\Site\SitePayDeductionFormula;
use App\Models\Site\SiteAllowanceFormula;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class FormulaRequest extends FormRequest
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
        return [
            'formula_details'=>'Array|min:1',
            'formula_details.*.var_type' => 'required|in:F,V,CV',
            'formula_details.*.var_name' => 'required_if:formula_details.*.var_type,V,CV',
            'formula_details.*.step_formula' => 'required|in:A,S,M,D,P,R',
            'formula_details.*.var_formula' => 'required_if:formula_details.*.var_type,CV',
            'formula_details.*.allowance_id' => 'required_if:formula_details.*.var_name,Allowance,formula_details.*.var_type,V,formula_details.*.var_type,CV',
        ];
    }

    public function save(){


        $parent_var_name = 'emp_pay_ded_id';
        $class_name = EmpPayDeductionFormula::class;

        if($this->type == "site"){
            $class_name = SiteAllowanceFormula::class;
            $parent_var_name = 'site_all_id';
        }
        else if($this->type == "siteded"){
            $class_name = SiteDeductionFormula::class;
            $parent_var_name = 'site_ded_id';
        }
        else if($this->type == "desig"){
            $class_name = SitePayAllowanceFormula::class;
            $parent_var_name = 'site_pay_all_id';
        }
        else if($this->type == "desigded"){
            $class_name = SitePayDeductionFormula::class;
            $parent_var_name = 'site_pay_ded_id';
        }
        else if($this->type == "gen"){
            $class_name = FormulaDetail::class;
            $parent_var_name = 'formula_id';
        }
        else if($this->type == "emp"){
            $class_name = EmpPayAllowanceFormula::class;
            $parent_var_name = 'emp_pay_all_id';
        }
        else{
            $class_name = EmpPayDeductionFormula::class;
            $parent_var_name = 'emp_pay_all_id';
        }

        $old_ids = $class_name::where($parent_var_name, $this->parent_id)->pluck('id')->toArray();
        $formula_details = new Collection();
        $count_no = 1;
        foreach ($this->formula_details as $det) {
            $formula = $class_name::findOrNew($det['id']);
            $formula->fill($det);
            $formula->step_no = $count_no;
            $formula->type = $det['var_type'];
            if ($det['var_type'] == 'F') {
                $formula->fix_amt = $det['var_amt'];
                $formula->var_name = "";
                $formula->var_formula = "";
                $formula->allowance_id = 0;
            } elseif ($det['var_type'] == 'CV') {
                $formula->var_amt = $det['var_amt'];
            } else {
                $formula->var_formula = "";
                $formula->var_amt = 0;
                $formula->fix_amt = 0;
            }
            $formula[$parent_var_name] = $this->parent_id;
            $formula_details->add($formula);
            $count_no++;
        }


        $formula_new_det_ids = $formula_details->pluck('id')->toArray();
        $detach = array_diff($old_ids,$formula_new_det_ids);

        DB::beginTransaction();
            foreach($formula_details as $f_det){
                $f_det->save();
            }
            $class_name::whereIn('id',$detach)->delete();
        DB::commit();

         //'step_no','all_id', 'type', 'varname', 'fixamt', 'varformula', 'varamt', 'stepformula'



        // $formula = Formula::findOrFail($this->formula_id);
        // if(!$formula){
        //     throw ValidationException::withMessages(['formula_id' => 'Formula does not exist']);
        // }
        // $formula_old_det_ids = $formula->formula_details()->pluck('id')->toArray();
        // $formula_details = new Collection();
        // $count_no = 1;
        // foreach($this->formula_details as $formula_det){
        //     if($this->formula_type == 'gen'){
        //         $formula_detail = FormulaDetail::findOrNew($formula_det['id']);
        //     }
        //     $formula_detail->fill($formula_det);
        //     $formula_detail->step_no = $count_no;
        //     $formula_details->add($formula_detail);
        // }

        // $formula_new_det_ids = $formula_details->pluck('id')->toArray();
        // $detach = array_diff($formula_old_det_ids,$formula_new_det_ids);


        // DB::beginTransaction();
        //     $formula->save();
        //     $formula->formula_details()->saveMany($formula_details);
        //     FormulaDetail::whereIn('id',$detach);
        // DB::commit();

        return reply(true,[
            'formula' =>true
        ]);
    }

}
