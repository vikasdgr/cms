
<script setup>
    import FilterWrapper from '@/Pages/CustomComponents/Sections/FilterWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import MachineTypeSelect from '@/Pages/ProjectComponents/SelectComponents/MachineTypeSelect.vue';
    import BrandSelect from '@/Pages/ProjectComponents/SelectComponents/BrandSelect.vue';
    import MachineModelSelect from '@/Pages/ProjectComponents/SelectComponents/MachineModelSelect.vue';
    import AreaSelect from '@/Pages/ProjectComponents/SelectComponents/AreaSelect.vue';
    import DepartmentSelect from '@/Pages/ProjectComponents/SelectComponents/DepartmentSelect.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form','site']);
    const data = reactive({
            siteInitials:[],
            siteSelected:[],
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
            <div class="w-full max-w-full px-3 lg:w-1/4 md:w-1/4 ">
                <div class="mb-4" >
                    <InputLabel for="department_id" value="Department"/>
                    <department-select v-model="form.department_id" :index="'dept'+data.random"></department-select>
                    <InputError class="mt-2" :message="form.errors.get('department_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 lg:w-1/4 md:w-1/4 ">
                <div class="mb-4" >
                    <InputLabel for="area_id" value="Area"/>
                    <area-select :department_id ="form.department_id" :index="'area'+data.random" v-model="form.area_id"></area-select>
                    <InputError class="mt-2" :message="form.errors.get('area_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 lg:w-1/4 md:w-1/4 ">
                <div class="mb-4" >
                    <InputLabel for="site_id" value="Machine Model"/>
                    <machine-model-select v-model="form.machine_model_id" :index="'model'+data.random"></machine-model-select>
                    <InputError class="mt-2" :message="form.errors.get('site_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 lg:w-1/4 md:w-1/4 ">
                <div class="mb-4" >
                    <InputLabel for="brand_id" value="Brand"/>
                    <brand-select v-model="form.brand_id" :index="'brand'+data.random"></brand-select>
                    <InputError class="mt-2" :message="form.errors.get('brand_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 lg:w-1/4 md:w-1/4 ">
                <div class="mb-4" >
                    <InputLabel for="machine_type_id" value="Machine Type"/>
                    <machine-type-select  v-model="form.machine_type_id" :index="'machine_type'+data.random"></machine-type-select>
                    <InputError class="mt-2" :message="form.errors.get('machine_type_id')" />
                </div>
            </div>

              <!-- <div class="w-full max-w-full px-3 lg:w-1/6 md:w-2/6 ">
                <div class="mb-4">
                    <InputLabel for="state_id" value="Status"/>
                    <SelectInput v-model="form.state_id" :options ="[{'id':'Y','text':'Active'},{'id':'N','text':'Inactive'}]"  :error="form.errors.get('state_id') ? true :false" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('state_id')" />
                </div>
            </div> -->
            <div class="w-full max-w-full px-3 lg:w-1/6 md:w-1/6 ">
             <div class="mb-4">
                <ButtonComp @buttonClicked="emit('reloadTable')" type="save">SHOW</ButtonComp>
             </div>
            </div>
        </div>
<!-- END TABS -->
    </FilterWrapper>

</div>
</template>

<style>

</style>
