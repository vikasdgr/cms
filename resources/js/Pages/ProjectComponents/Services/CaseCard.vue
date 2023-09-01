s

<script setup>
    import { ref, onMounted, reactive,computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    const props = defineProps(['case','machine']);
    import globalMixin from '../../../globalMixin';
    const { base_url,refreshComponent} = globalMixin();

    const getStatus = (classname= false)=>{
        if(props.case.status == 'F'){
           return  classname ? 'bg-red-100 text-red-800 text-xs font-medium  px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-red-400 border border-red-400':'Follow-Up Required';
        }
        else if(props.case.status == 'C'){
            return classname ? 'bg-green-100 text-green-800 text-xs font-medium  px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-green-400 border border-green-400':'Completed & Closed';
        }
    }
</script>
<template>
      <div class="relative w-full px-4 py-6 bg-white shadow-lg dark:bg-gray-800">
        <span  :class="getStatus(true)"  v-text="getStatus()"></span>
        <p class="text-sm font-semibold text-gray-700 border-b border-gray-200 w-max dark:text-white mt-5" v-text="props.machine ? props.machine.machine_type:''">
        </p>
        <img width="100" height="100" class="mt-2 ml-4" v-if="props.case &&  props.case.machine && props.case.machine.machine_model &&
         props.case.machine.machine_model.machine_image" :src="base_url+'/shared-attachments/'+props.case.machine.machine_model.machine_image.attachment_id"/>

        <div class="flex items-end my-6 space-x-2">
            <Link :href="base_url+'/services-details/'+props.case.id" class="text-2xl semibold text-black dark:text-white" ><span v-text="props.case ? props.case.case_no:''"></span> </Link>
            <span class="flex items-center text-xl semibold text-green-500" v-text="props.case ? props.case.open_date:''"> </span>
        </div>
        <div class="dark:text-white">
            <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                <p> Department</p>
                <div class="flex items-end text-sm text-fuchsia-600" v-text="props.machine ? props.machine.department_name:''">
                </div>
            </div>
            <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                <p> Area</p>
                <div class="flex items-end text-sm text-fuchsia-600" v-text="props.machine ? props.machine.area_name:''">
                </div>
            </div>
            <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                <p> Work Type </p>
                <div class="flex items-end text-sm text-indigo-500" v-text="props.case ? props.case.work_types:''">
                </div>
            </div>
            <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                <p> Serial no. </p>
                <div class="flex items-end text-sm text-indigo-500"  v-text="props.machine ? props.machine.serial_no:''">
                </div>
            </div>
            <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                <p> Machine Code </p>
                <div class="flex items-end text-sm text-indigo-500"  v-text="props.machine ? props.machine.name_code_no:''">
                </div>
            </div>
            <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                <p> Model </p>
                <div class="flex items-end text-sm text-indigo-500"   v-text="props.machine ? props.machine.machine_model:''">
                </div>
            </div>
        </div>
    </div>
</template>

