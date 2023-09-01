
<script setup>
    import { usePage } from '@inertiajs/vue3';
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import ProblemsSelect from '@/Pages/ProjectComponents/SelectComponents/ProblemsSelect.vue';
    import ResolvingActionSelect from '@/Pages/ProjectComponents/SelectComponents/ResolvingActionSelect.vue';
    import MaintenanceSelect from '@/Pages/ProjectComponents/SelectComponents/MaintenanceSelect.vue';
    import CaseCard from '@/Pages/ProjectComponents/Services/CaseCard.vue'; // Update import path
    import RadioInput from '@/Pages/CustomComponents/Forms/RadioInput.vue';
    import CheckboxInput from '@/Pages/CustomComponents/Forms/CheckboxInput.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../globalMixin';
    import { Inertia } from '@inertiajs/inertia';
    const { back } = usePage();
    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','case']);
    const form = reactive( new Form({
            form_id: 0,
            case_id:0,
            service_date:CMS.today,
            service_time:'',
            remarks:'',
            service_type:'N',
            technician_name:'',
            status:'',
            problems:[],
            maintenances:[],
            resolving_actions:[]
            // problem_ids:[],
            // resolving_ids:[],
            // maintenance_ids:[]
    }));
    const data = reactive({ create_url:'services' ,  showProb :true, showMain:true, showResolve:true,case:{ case_no:'', status:'',open_date:'', work_types:'', work_order_types:'' ,case_id:0}, machine:{
        name_code_no:'', serial_no:'', department_name:'', brand_name:'', area_name:'', machine_model:'', machine_type:'',

    }});
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Servcie Details':'Add Service Details');

    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        if(props.case){
            setCaseDetails();
        }
        if(props.form_id > 0){
            getService();
        }
    });
    const submitForm = () =>{
        form['postForm'](base_url.value + '/'+data.create_url)
        .then(function(response){
            console.log(response);
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                if(response.service)
                window.open(base_url.value+'/service-print/'+response.service.id, '_blank');
                emit('resetForm');

            }
        })
        .catch(function(error){
        });
    }
    const getService = () =>{
        axios.get(base_url.value+'/services/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let department = response.data.department;
                form.name = department.name;
            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
        });
    }

    const setCaseDetails = () =>{
        form.case_id= props.case.id;
        data.case_id= props.case.id;
        data.case.case_no = props.case.case_no;
        data.case.status = props.case.status;
        data.case.open_date = props.case.open_date;
        data.case.work_types = props.case.work_types;
        data.case.work_order_types = props.case.work_order_types;
        if( data.case.work_types == 'Installation'){
            form.service_type = 'I';
        }
        else if( data.case.work_types == 'Maintenance'){
            form.service_type = 'M';
        }
        else{
            form.service_type = 'R';
        }
        if(props.case.machine){
            let machine = props.case.machine;
            data.machine.name_code_no =machine.name_code_no;
            data.machine.serial_no =machine.serial_no;
            if(machine.department){
                data.machine.department_name =machine.department.name;
            }
            if(machine.brand){
                data.machine.brand_name =machine.brand.name;
            }
            if(machine.area){
                data.machine.area_name =machine.area.name;
            }
            if(machine.machine_model){
                data.machine.machine_model =machine.machine_model.model_no;
            }
            if(machine.machine_type){
                data.machine.machine_type =machine.machine_type.name;
            }
        }
    }

    const goToBack = ()=>{
        window.history.back();
    }

const updateProblem = (id,index,selected_data)=>{
        const prob= form.problems.find(item => item.problem_id == id);
        if(!prob){
            form.problems.push({id:0,is_remarks:'N',remarks:'',name:selected_data['name'],problem_id:id});
        }
        refreshComponent(data,'showProb');
    }
    const updateMaintenance =  (id,index,selected_data)=>{
        const prob= form.maintenances.find(item => item.maintenance_id == id);
        if(!prob){
            form.maintenances.push({id:0,is_remarks:'N',remarks:'',name:selected_data['name'],maintenance_id:id});
        }
        refreshComponent(data,'showMain');
    }
    const updateResolvingAction =  (id,index,selected_data)=>{
        const prob= form.resolving_actions.find(item => item.resolving_action_id == id);
        if(!prob){
            form.resolving_actions.push({id:0,is_remarks:'N',remarks:'',name:selected_data['name'],resolving_action_id:id});
        }
        refreshComponent(data,'showResolve');
    }

    const removeItem = (type,index)=>{
        form[type].splice(index,1);
    }


</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="grid grid-rows-3 grid-flow-col grid-cols-4 gap-4 ">
            <div class="row-span-3 grid-cols-1">
                <case-card :case="data.case" :machine="data.machine"></case-card>
            </div>
            <div class="col-span-3">
                <fieldset class="border rounded bg-gray-50 p-4 mb-1 mt-4">
                    <legend  class="text-base rounded font-semibold bg-teal-600 text-white px-3" v-text="form.service_type == 'I'?'Installation':(form.service_type == 'M'?'Maintenance Details ':'Service Details')" ></legend>
                     <div class="flex flex-wrap items-end -mx-3">
                        <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                            <div class="mb-2" >
                                <InputLabel for="service_date" :value="form.service_type == 'I'?'Installation Date':'Service Date '" />
                                <date-picker v-model="form.service_date" class-name="service_date" :error="form.errors.get('service_date') ? true :false"></date-picker>
                                <InputError class="mt-2" :message="form.errors.get('service_date')" />
                            </div>
                        </div>
                         <div class="w-full max-w-full px-3  lg:w-2/4 md:w-2/4">
                            <div class="mb-2" >
                                <InputLabel for="technician_name" value="Technician Name" />
                                 <TextInput v-model="form.technician_name" type="text" required   :error="form.errors.get('technician_name') ? true :false" />
                                <InputError class="mt-2" :message="form.errors.get('technician_name')" />
                            </div>
                        </div>
                     </div>
                     <div class="flex flex-wrap items-end -mx-3" v-if="form.service_type == 'R'">
                        <div class="w-full max-w-full px-3  lg:w-full md:w-full">
                            <div class="mb-2" v-if="data.showProb" >
                                <InputLabel for="problem_ids" value="Issues" />
                                <problems-select  @updateProblem="updateProblem"></problems-select>
                                <InputError class="mt-2" :message="form.errors.get('problem_ids')" />
                            </div>
                        </div>
                     </div>
                    <div class=" ml-4 md:ml-8 lg:ml-12 " v-for="(problem,index) in form['problems']" :key="problem.problem_id" >
                        <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                            <p class="flex items-end text-sm text-indigo-500" v-text="index+1 +') '+problem['name']"> </p>
                            <div class="flex">
                                <CheckboxInput v-model:checked="problem.is_remarks"    true-value="Y" false-value="N" :id="'problem_is_remarks'+index">more details</CheckboxInput>
                                <i  class="mr-1 fas fa-trash text-red-400 ml-4 mt-1" aria-hidden="true" @click="removeItem('problems',index)"></i>
                            </div>
                        </div>
                        <TextAreaInput  v-if="problem.is_remarks == 'Y'" v-model="problem.remarks"></TextAreaInput>
                    </div>
                    <div class="flex flex-wrap items-end -mx-3" v-if="form.service_type == 'M'">
                        <div class="w-full max-w-full px-3  lg:w-full md:w-full">
                            <div class="mb-2" v-if="data.showMain">
                                <InputLabel for="service_date" value="Maintenance Steps" />
                                <maintenance-select @updateMaintenance="updateMaintenance"></maintenance-select>
                                <InputError class="mt-2" :message="form.errors.get('maintenance_ids')" />
                            </div>
                        </div>
                    </div>
                    <div class=" ml-4 md:ml-8 lg:ml-12 " v-for="(maintenance,index) in form['maintenances']" :key="maintenance.maintenance_id" >
                        <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                            <p class="flex items-end text-sm text-indigo-500" v-text="index+1 +') '+maintenance['name']"> </p>
                            <div class="flex">
                                <CheckboxInput v-model:checked="maintenance.is_remarks"    true-value="Y" false-value="N" :id="'maintenance_is_remarks'+index">more details</CheckboxInput>
                                <i  class="mr-1 fas fa-trash text-red-400 ml-4 mt-1" aria-hidden="true" @click="removeItem('maintenances',index)"></i>
                            </div>
                        </div>
                        <TextAreaInput  v-if="maintenance.is_remarks == 'Y'" v-model="maintenance.remarks"></TextAreaInput>
                    </div>
                    <div class="flex flex-wrap items-end -mx-3" v-if="form.service_type == 'R'">
                        <div class="w-full max-w-full px-3  lg:w-full md:w-full">
                            <div class="mb-2" v-if="data.showResolve">
                                <InputLabel for="service_date" value="Resolving Actions" />
                                <resolving-action-select   @updateResolvingAction="updateResolvingAction"></resolving-action-select>
                                <InputError class="mt-2" :message="form.errors.get('resolving_ids')" />
                            </div>
                        </div>
                    </div>
                    <div class=" ml-4 md:ml-8 lg:ml-12 " v-for="(resolving_action,index) in form['resolving_actions']" :key="resolving_action.resolving_action_id" >
                        <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                            <p class="flex items-end text-sm text-indigo-500" v-text="index+1 +') '+resolving_action['name']"> </p>
                            <div class="flex">
                                <CheckboxInput v-model:checked="resolving_action.is_remarks"    true-value="Y" false-value="N" :id="'resolving_action_is_remarks'+index">more details</CheckboxInput>
                                <i  class="mr-1 fas fa-trash text-red-400 ml-4 mt-1" aria-hidden="true" @click="removeItem('resolving_actions',index)"></i>
                            </div>
                        </div>
                        <TextAreaInput  v-if="resolving_action.is_remarks == 'Y'" v-model="resolving_action.remarks"></TextAreaInput>
                    </div>
                    <div class="flex flex-wrap items-end -mx-3">
                        <div class="w-full max-w-full px-3  lg:w-full md:w-full">
                            <div class="mb-2" >
                                <InputLabel for="remarks" value="Remarks" />
                                <TextAreaInput v-model="form.remarks" type="text" required   :error="form.errors.get('remarks') ? true :false" />
                                <InputError class="mt-2" :message="form.errors.get('remarks')" />
                            </div>
                        </div>
                     </div>
                    <div class="w-full max-w-full px-3  lg:w-full md:w-full ">
                        <div class="mb-4">
                            <InputLabel for="status" value="Status" />
                            <div class="flex">
                            <RadioInput  class="mr-4 ml-4" value="C" v-model:checked="form.status" name="status" id="statusM">Completed & Closed</RadioInput>
                            <RadioInput  class="mr-4 ml-3" value="F" v-model:checked="form.status" name="status" id="statusF">Required Follow-Up</RadioInput>
                            </div>
                            <InputError class="mt-2" :message="form.errors.get('status')" />
                        </div>
                    </div>
                    <div class="flex flex-wrap items-end -mx-3">
                        <div class="w-full max-w-full px-3 lg:w-full md:w-full ">
                            <div class="mb-4">
                                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                                <ButtonComp @buttonClicked="goToBack()" type="cancel">Cancel</ButtonComp>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="row-span-2 col-span-2">
            </div>
        </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
