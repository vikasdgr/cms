

<script setup>
    import { ref, onMounted, reactive,computed } from 'vue';
    const props = defineProps(['service']);
import globalMixin from '../../../globalMixin';

const { base_url ,copyProperties,refreshComponent} = globalMixin();
    const getStatus =(classname=false)=>{
        if(props.service.status =='C'){
            return classname ? 'bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300 ml-3': 'Closed';
        }
        else if(props.service.status =='F'){
            return  classname ? 'bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 ml-3': 'Follow Up';
        }
    }
    const getServiceType =()=>{
        return props.service.service_type =='R' ? 'Repair':( props.service.service_type =='M' ? 'Maintenance': props.service.service_type =='I' ? 'Installation':'')
    }

</script>
<template>

    <li class="mb-10 ml-6">
            <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                </svg>
            </span>
            <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white"><span v-text="props.service.service_date"></span>
            <span :class="getStatus(true)" v-text="getStatus()"></span></h3>

             <span  v-text="'Service No. : '+service.service_no"></span>
            <time class="block mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500" v-text="'Technician : '+ service.technician_name"></time>
            <p class="mb-2 text-base font-normal text-gray-500 dark:text-gray-400" v-text="'Service Type : '+getServiceType()"></p>

            <div class="flex">
                 <div class="w-1/4 p-1 ">
                 <a :href="base_url+'/service-print/'+service.id" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white mb-5 border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-200 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700"><svg class="w-3.5 h-3.5 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                        <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                        </svg>
                    Download
                </a>
            </div>
            <div class="flex-grow p-1 ">
                    <p  class="px-2" v-text="service.remarks"></p>
            </div>
            </div>
            <div>
                <div  v-if="props.service['service_problems'].length > 0" class="bg-pink-100 text-pink-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-pink-900 dark:text-pink-300 mb-6">Detected Issues</div>
                <div class=" ml-4 md:ml-8 lg:ml-12  " v-for="(service_problem,index) in props.service['service_problems']" :key="service_problem.problem_id" >
                    <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                        <p class="flex items-end text-sm text-indigo-500" v-text="index+1 +')  '+service_problem['problem']['name']"> </p>
                    </div>
                    <p v-if="service_problem['is_remarks'] == 'Y'" class="mb-2" v-text="service_problem['remarks']"></p>
                </div>
            </div>
             <div>
                <div v-if="props.service['service_maintenances'].length > 0" class="bg-yellow-100 text-yellow-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300 mb-6">Maintenance Performed</div>
                <div class=" ml-4 md:ml-8 lg:ml-12  " v-for="(service_maintenance,index) in props.service['service_maintenances']" :key="service_maintenance.problem_id" >
                    <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                        <p class="flex items-end text-sm text-indigo-500" v-text="index+1 +')  '+service_maintenance['maintenance']['name']"> </p>
                    </div>
                </div>
            </div>
            <div>
            <div  v-if="props.service['service_resolving_actions'].length > 0"  class="bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300 mb-6">Resolving Actions</div>
                <div class=" ml-4 md:ml-8 lg:ml-12  " v-for="(service_resolving_action,index) in props.service['service_resolving_actions']" :key="service_resolving_action.problem_id" >
                    <div class="flex items-center justify-between pb-2 mb-2 space-x-12 text-sm border-b border-gray-200 md:space-x-24">
                        <p class="flex items-end text-sm text-indigo-500" v-text="index+1 +')  '+service_resolving_action['resolving_action']['name']"> </p>
                    </div>
                </div>
            </div>

    </li>
</template>

