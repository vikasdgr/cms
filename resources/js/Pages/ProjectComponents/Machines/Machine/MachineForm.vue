<script setup>
import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
import BrandSelect from '@/Pages/ProjectComponents/SelectComponents/BrandSelect.vue';
import MachineTypeSelect from '@/Pages/ProjectComponents/SelectComponents/MachineTypeSelect.vue';
import MachineModelSelect from '@/Pages/ProjectComponents/SelectComponents/MachineModelSelect.vue';
import DepartmentSelect from '@/Pages/ProjectComponents/SelectComponents/DepartmentSelect.vue';
import AreaSelect from '@/Pages/ProjectComponents/SelectComponents/AreaSelect.vue';
import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
import InputError from '@/Components/InputError.vue';
import { ref, computed, onMounted, reactive } from 'vue';
import globalMixin from '../../../../globalMixin';

const { base_url ,copyProperties,refreshComponent} = globalMixin();
const props = defineProps(['form_id']);
const form = reactive(
    new Form({
        form_id: 0,
        name: '',
        name_code_no:'',
        serial_no:'',
        brand_id:'',
        model_id:'',
        machine_type_id:'',
        department_id:'',
        area_id:'',
        buy_date:'',
        installation_date:'',
        warranty_upto_date:'',
    })
);
const data = reactive({ create_url: 'machines',show:true, modelInitials:[], modelSelected:[], machineTypeInitials:[], machineTypeSelected:[], departmentInitials:[], departmentSelected:[], areaInitials:[], areaSelected:[],
brandInitials:[],
brandSelected:[],showbrandType:true
 }); // Update URL
const pageTitle = computed(() =>
    props.form_id > 0 ? 'Update Machine' : 'Add Machine'
);

const emit = defineEmits(['resetForm']);
onMounted(() => {
    if (props.form_id > 0) {
        getMachine(); // Update function name
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
const getMachine = () => {
    axios.get(base_url.value + '/machines/' + props.form_id + '/edit') // Update URL
    .then(function (response) {
        if (response.data.success) {
            let machine = response.data.machine;
            copyProperties(machine,form);
            form.form_id = machine.id;
            if(machine.machine_model){
                data.modelInitials = [{'id':machine.machine_model.id ,'text':machine.machine_model.model_no}];
                data.modelSelected = [machine.machine_model.id];
            }
            if (machine.machine_type) {
                data.machineTypeInitials = [{'id': machine.machine_type.id, 'text': machine.machine_type.name}];
                data.machineTypeSelected = [machine.machine_type.id];
            }

            if (machine.department) {
                data.departmentInitials = [{'id': machine.department.id, 'text': machine.department.name}];
                data.departmentSelected = [machine.department.id];
            }
            if (machine.brand) {
                data.brandInitials = [{'id': machine.brand.id, 'text': machine.brand.name}];
                data.brandSelected = [machine.brand.id];
            }

            if (machine.area) {
                data.areaInitials = [{'id': machine.area.id, 'text': machine.area.name}];
                data.areaSelected = [machine.area.id];
            }

            refreshComponent(data,'show');
        refreshComponent(data,'showbrandType');

        }
        form.form_id = props.form_id;
    })
    .catch(function (error) {});
};

const updateMachineModel =(id)=>{
    axios.get(base_url.value+'/machine-models/'+id+'/edit')
    .then(function(response){
        let machine_model = response.data.machine_model;
         if (machine_model.machine_type) {
            data.machineTypeInitials = [{'id': machine_model.machine_type.id, 'text': machine_model.machine_type.name}];
            data.machineTypeSelected = [machine_model.machine_type.id];
            form.machine_type_id = machine_model.machine_type.id;
        }
        if (machine_model.brand) {
            data.brandInitials = [{'id': machine_model.brand.id, 'text': machine_model.brand.name}];
            data.brandSelected = [machine_model.brand.id];
            form.brand_id = machine_model.brand.id;

        }
        refreshComponent(data,'showbrandType');
    })
    .catch(function(error){
        console.log(error);
    })
}
</script>

<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4">
                    <InputLabel for="serial_no" value="Serial No." />
                    <TextInput v-model="form.serial_no" type="text" required autofocus :error="form.errors.get('serial_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('serial_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4" v-if="form.form_id > 0">
                <div class="mb-4">
                    <InputLabel for="name_code_no" value="Code No.(Auto generated)" />
                    <TextInput v-model="form.name_code_no" disabled type="text" required autofocus :error="form.errors.get('name_code_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name_code_no')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4"  v-if="data.show">
                    <InputLabel for="model_id" value="Model No." />
                    <machine-model-select v-model="form.model_id" :initials="data.modelInitials" :selected ="data.modelSelected" @updateMachineModel="updateMachineModel" ></machine-model-select>
                    <InputError class="mt-2" :message="form.errors.get('model_id')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4" v-if="data.showbrandType">
                    <InputLabel for="brand_id" value="Brand" />
                    <brand-select v-model="form.brand_id" :disabled="true"  :initials="data.brandInitials" :selected="data.brandSelected"></brand-select>
                    <InputError class="mt-2" :message="form.errors.get('brand_id')" />
                </div>
            </div>
             <!-- Machine Type Select -->
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4" v-if="data.showbrandType">
                     <InputLabel for="machine_type_id" value="Machine Type" />
                    <machine-type-select :disabled="true"  v-model="form.machine_type_id" :initials="data.machineTypeInitials" :selected="data.machineTypeSelected"></machine-type-select>
                    <InputError class="mt-2" :message="form.errors.get('machine_type_id')" />
                </div>
            </div>

            <!-- Department Select -->
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4" v-if="data.show">
                     <InputLabel for="department_id" value="Department" />
                    <department-select v-model="form.department_id" :initials="data.departmentInitials" :selected="data.departmentSelected"></department-select>
                    <InputError class="mt-2" :message="form.errors.get('department_id')" />
                </div>
            </div>

            <!-- Area Select -->
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="area_id" value="Area" />
                    <area-select v-model="form.area_id" :department_id="form.department_id" :initials="data.areaInitials" :selected="data.areaSelected"></area-select>
                    <InputError class="mt-2" :message="form.errors.get('area_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4" >
                    <InputLabel for="buy_date" value="Buy Date" />
                    <date-picker v-model="form.buy_date" class-name="buy_date" :error="form.errors.get('buy_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('buy_date')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4" >
                    <InputLabel for="warranty_upto_date" value="Warranty Date" />
                    <date-picker v-model="form.warranty_upto_date" class-name="warranty_upto_date" :error="form.errors.get('warranty_upto_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('warranty_upto_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4" >
                    <InputLabel for="installation_date" value="Installation Date" />
                    <date-picker v-model="form.installation_date" class-name="installation_date" :error="form.errors.get('installation_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('installation_date')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4">
                <div class="mb-4">
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
