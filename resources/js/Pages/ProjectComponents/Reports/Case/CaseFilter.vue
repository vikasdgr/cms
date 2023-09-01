
<script setup>
    import FilterWrapper from '@/Pages/CustomComponents/Sections/FilterWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import MachineTypeSelect from '@/Pages/ProjectComponents/SelectComponents/MachineTypeSelect.vue';
    import MachineModelSelect from '@/Pages/ProjectComponents/SelectComponents/MachineModelSelect.vue';
    import DepartmentSelect from '@/Pages/ProjectComponents/SelectComponents/DepartmentSelect.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';

    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form']);
    const data = reactive({
            show:true,
            random:Utilities.getRandomNumber()
    });
    const pageTitle = computed(() =>  'Filter');
    const emit = defineEmits(['reloadTable']);

    onMounted(() => {

    });

</script>


<template>
<div>
    <FilterWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
             <div class="w-full max-w-full px-3 lg:w-1/6 md:w-1/6 ">
                <div class="mb-4" >
                    <InputLabel for="machine_type_id" value=" Type"/>
                    <machine-type-select v-model="form.machine_type_id" :index="'machine_type'+data.random"></machine-type-select>
                    <InputError class="mt-2" :message="form.errors.get('machine_type_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 lg:w-1/6 md:w-1/6 ">
                <div class="mb-4" >
                    <InputLabel for="model_id" value="Machine Model"/>
                    <machine-model-select v-model="form.model_id" :index="'model'+data.random"></machine-model-select>
                    <InputError class="mt-2" :message="form.errors.get('model_id')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 lg:w-1/6 md:w-1/6 ">
                <div class="mb-4" >
                    <InputLabel for="department_id" value="Department"/>
                    <department-select v-model="form.department_id" :index="'model'+data.random"></department-select>
                    <InputError class="mt-2" :message="form.errors.get('department_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-1/6 md:w-1/6">
                <div class="mb-4" >
                    <InputLabel for="from_date" value="From Date" />
                    <date-picker v-model="form.from_date" class-name="from_date" :error="form.errors.get('from_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('from_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-1/6 md:w-1/6">
                <div class="mb-4" >
                    <InputLabel for="to_date" value="To Date" />
                    <date-picker v-model="form.to_date" class-name="to_date" :error="form.errors.get('to_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('to_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 lg:w-3/6 md:w-3/6 ">
             <div class="mb-4">
                <ButtonComp @buttonClicked="emit('reloadTable')" type="save" class="mr-2">SHOW</ButtonComp>
             </div>
            </div>
        </div>
<!-- END TABS -->
    </FilterWrapper>

</div>
</template>

<style>

</style>
