<script setup>
import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
import MachineSelect from '@/Pages/ProjectComponents/SelectComponents/MachineSelect.vue';
import DepartmentSelect from '@/Pages/ProjectComponents/SelectComponents/DepartmentSelect.vue';
import AreaSelect from '@/Pages/ProjectComponents/SelectComponents/AreaSelect.vue';
import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
import InputError from '@/Components/InputError.vue';
import { ref, computed, onMounted, reactive } from 'vue';
import globalMixin from '../../../globalMixin';

const { base_url ,copyProperties,refreshComponent} = globalMixin();
const props = defineProps(['form_id','machine_id']);
const form = reactive(
    new Form({
        form_id:0,
        machine_id:'',
        case_no:'',
        open_date:CMS.today,
        closed_date:'',
        work_types:'',
        work_order_types:'',
        status:'',
        description:'',
        generated_user_id:'',
    })
);
const data = reactive({ create_url: 'machine-cases',show:true, departmentInitials:[], showDeptarea:true,
    departmentSelected :[],
    areaInitials :[],
    areaSelected :[],
    machineInitials:[],
    machineSelected:[],
    showMachine:true
 }); // Update URL
const pageTitle = computed(() =>
    props.form_id > 0 ? 'Update ' : 'Schedule Service'
);

const emit = defineEmits(['resetForm']);
onMounted(() => {
    if(props.machine_id > 0){
        getMachine(props.machine_id);
    }
    if (props.form_id > 0) {
        getService(); // Update function name
    }
});
const submitForm = () => {
    form['postForm'](data.create_url)
        .then(function (response) {
            console.log(response);
            if (response.success) {
                emit('resetForm');
                Utilities.showPopMessage(
                    'Your data has been saved successfully!'
                );
            }
        })
        .catch(function (error) {});
};
const getMachine = (id) => {
    axios.get(base_url.value + '/machine-details/' + id ) // Update URL
    .then(function (response) {
        if (response.data.success) {
            let machine = response.data.machine;
            copyProperties(machine,form);
            if(props.machine_id > 0){
                form.machine_id = props.machine_id;
                data.machineInitials = [{'id':machine.id ,'text':machine.serial_no}];
                data.machineSelected = [machine.id];
                refreshComponent(data,'show');
            }
            if (machine.department) {
                data.departmentInitials = [{'id': machine.department.id, 'text': machine.department.name}];
                data.departmentSelected = [machine.department.id];
            }
            if (machine.area) {
                data.areaInitials = [{'id': machine.area.id, 'text': machine.area.name}];
                data.areaSelected = [machine.area.id];
            }
            // refreshComponent(data,'show');
            refreshComponent(data,'showDeptarea');
        }
        form.form_id = props.form_id;
    })
    .catch(function (error) {});
};
const getService = () => {
    axios.get(base_url.value + '/machine-cases/' + props.form_id + '/edit') // Update URL
    .then(function (response) {
        if (response.data.success) {
            let service = response.data.service;
            copyProperties(service,form);
            form.form_id = service.id;
            if(service.machine){
                data.machineInitials = [{'id':service.machine.id ,'text':service.machine.serial_no}];
                data.machineSelected = [service.machine.id];
            }
            if (service.machine.department) {
                data.departmentInitials = [{'id': service.machine.department.id, 'text': service.machine.department.name}];
                data.departmentSelected = [service.machine.department.id];
            }
            if (service.machine.area) {
                data.areaInitials = [{'id': service.machine.area.id, 'text': service.machine.area.name}];
                data.areaSelected = [service.machine.area.id];
            }

            refreshComponent(data,'show');
            refreshComponent(data,'showDeptarea');

        }
        form.form_id = props.form_id;
    })
    .catch(function (error) {});
};


</script>

<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
             <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-2" >
                    <InputLabel for="open_date" value="Date" />
                    <date-picker v-model="form.open_date" class-name="open_date" :error="form.errors.get('open_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('open_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-2/4 md:w-1/4">
                <div class="mb-2" v-if="data.show" >
                    <InputLabel for="machine_id" value="Machine"/>
                    <machine-select :disabled="props.machine_id > 0" v-model="form.machine_id" :initials="data.machineInitials" :selected="data.machineSelected" @updateMachine="getMachine"></machine-select>
                    <InputError class="mt-2" :message="form.errors.get('machine_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4" v-if="form.form_id > 0">
                <div class="mb-2">
                    <InputLabel for="case_no" value="Service No.(Auto generated)" />
                    <TextInput v-model="form.case_no" disabled type="text" required autofocus :error="form.errors.get('case_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('case_no')" />
                </div>
            </div>

            <!-- Department Select -->
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-2" v-if="data.showDeptarea">
                     <InputLabel for="department_id" value="Department" />
                    <department-select :disabled="true" v-model="form.department_id" :initials="data.departmentInitials" :selected="data.departmentSelected"></department-select>
                    <InputError class="mt-2" :message="form.errors.get('department_id')" />
                </div>
            </div>

            <!-- Area Select -->
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-2" v-if="data.showDeptarea">
                    <InputLabel for="area_id" value="Area" />
                    <area-select :disabled="true" v-model="form.area_id" :department_id="form.department_id" :initials="data.areaInitials" :selected="data.areaSelected"></area-select>
                    <InputError class="mt-2" :message="form.errors.get('area_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-2" >
                    <InputLabel for="work_types" value="Service Type" />
                    <SelectInput v-model="form.work_types" :options ="[{'id':'Installation','text':'Installation'},{'id':'Maintenance','text':'Maintenance'},{'id':'BreakDown','text':'BreakDown'}]"  :error="form.errors.get('work_types') ? true :false" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('work_types')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-2" >
                    <InputLabel for="work_order_types" value="Work Order Type" />
                    <SelectInput v-model="form.work_order_types" :options ="[{'id':'Installation','text':'Installation'},{'id':'Repair','text':'Repair'},{'id':'General Service','text':'General Service'}]"  :error="form.errors.get('work_order_types') ? true :false" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('work_order_types')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-2" >
                    <InputLabel for="description" value="Description" />
                    <TextAreaInput v-model="form.description"></TextAreaInput>
                    <InputError class="mt-2" :message="form.errors.get('description')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-2">
                    <ButtonComp @buttonClicked="submitForm" type="save" > Save </ButtonComp>
                    <ButtonComp @buttonClicked="emit('resetForm')" type="cancel" > Cancel </ButtonComp>
                </div>
            </div>
        </div>

    </FormWrapper>
        </div>
</template>

<style>

</style>
