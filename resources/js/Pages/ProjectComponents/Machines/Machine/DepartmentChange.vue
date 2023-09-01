<script setup>
import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
import DepartmentSelect from '@/Pages/ProjectComponents/SelectComponents/DepartmentSelect.vue';
import AreaSelect from '@/Pages/ProjectComponents/SelectComponents/AreaSelect.vue';
import InputError from '@/Components/InputError.vue';
import { ref, computed, onMounted, reactive } from 'vue';
import globalMixin from '../../../../globalMixin';

const { base_url ,copyProperties,refreshComponent} = globalMixin();
const props = defineProps(['machine_id']);
const form = reactive(
    new Form({
        machine_id: 0,
        new_dept_id:0,
        department_name:'',
        area_name:'',
        new_area_id:0
    })
);
const data = reactive({ create_url: 'change-department',showDepartment:true ,showArea:true ,
 }); // Update URL
const pageTitle = computed(() =>
    props.machine_id > 0 ? 'Update Department Change' : 'Add Department Change'
);

const emit = defineEmits(['resetForm']);
onMounted(() => {
    if (props.machine_id > 0) {
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
    axios.get(base_url.value + '/machines/' + props.machine_id + '/edit') // Update URL
    .then(function (response) {
        if (response.data.success) {
            let machine = response.data.machine;
            console.log(machine);
            form.machine_id = machine.id;
            if (machine.department) {
                form.department_name =  machine.department.name;
            }
            if (machine.area) {
                form.area_name =  machine.area.name;
            }

        }
        form.machine_id = props.machine_id;
    })
    .catch(function (error) {
        console.log(error);
    });
};

const updateDepartment=() =>{
     form.new_area_id = 0;
     Utilities.refreshComponent(data,'showArea');
}

</script>

<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3  lg:w-1/2 md:w-1/2">
                <div class="mb-4">
                    <InputLabel for="department_name" value="Current Department" />
                    <TextInput v-model="form.department_name" disabled type="text" required  :error="form.errors.get('department_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('department_name')" />
                </div>
            </div>

            <!-- Department Select -->
            <div class="w-full max-w-full px-3  lg:w-1/2 md:w-1/2">
                <div class="mb-4" v-if="data.showDepartment">
                     <InputLabel for="new_dept_id" value="New Department" />
                    <department-select v-model="form.new_dept_id" :initials="data.departmentInitials" :selected="data.departmentSelected" @updateDepartment="updateDepartment"></department-select>
                    <InputError class="mt-2" :message="form.errors.get('new_dept_id')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  lg:w-1/2 md:w-1/2">
                <div class="mb-4">
                    <InputLabel for="area_name" value="Current Area" />
                    <TextInput v-model="form.area_name" type="text" disabled required autofocus :error="form.errors.get('area_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('area_name')" />
                </div>
            </div>
            <!-- Area Select -->
            <div class="w-full max-w-full px-3  lg:w-1/2 md:w-1/2">
                <div class="mb-4" v-if="data.showArea">
                    <InputLabel for="area_id" value="New Area" />
                    <area-select v-model="form.area_id" :department_id="form.new_dept_id" :initials="data.areaInitials" :selected="data.areaSelected"></area-select>
                    <InputError class="mt-2" :message="form.errors.get('area_id')" />
                </div>
            </div>
        </div>
         <div class="flex flex-wrap items-end -mx-3">
             <div class="w-full max-w-full px-3  lg:w-1/3 md:w-1/3">
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
