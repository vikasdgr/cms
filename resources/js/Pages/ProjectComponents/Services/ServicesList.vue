<script setup>
import { ref, onMounted, reactive,computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import ServiceForm from '@/Pages/ProjectComponents/Services/ServiceForm.vue'; // Update import path
import CaseCard from '@/Pages/ProjectComponents/Services/CaseCard.vue'; // Update import path
import ServiceCard from '@/Pages/ProjectComponents/Services/ServiceCard.vue'; // Update import path
import globalMixin from '../../../globalMixin';
const page = usePage();
const { base_url,canAny } = globalMixin();
const props = defineProps(['case','services']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    random:Utilities.getRandomNumber()
});

const data = reactive({ create_url:'services' , machine:{
    name_code_no:'', serial_no:'', department_name:'', brand_name:'', area_name:'', machine_model:'', machine_type:'',

}});
const pageTitle = computed(() => 'Services Details');
onMounted(() => {
    if(props.case){
        setCaseDetails();
    }
});

const setCaseDetails = () =>{
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
</script>

<template>

    <AppLayout :title="pageTitle">
        <FormWrapper :title="'Case No. : '+ props.case.case_no">
        <div>
            <div class="grid grid-rows-3 grid-flow-col grid-cols-4 gap-4 ">
                <div class="row-span-3 grid-cols-1">
                     <case-card :case="props.case" :machine ="data.machine"></case-card>
                </div>
                <div class="col-span-3" v-if="props.services.length > 0">
                    <ol class="relative border-l border-gray-200 dark:border-gray-700" >
                        <service-card v-for="service in props.services" :key="service.id" :service="service"></service-card>
                    </ol>

                </div>
                <div class="col-span-3" v-else>
                     <div class="bg-white p-8 rounded shadow-md flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500 mb-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM7 9a1 1 0 00-2 0v2a1 1 0 102 0V9zm6 0a1 1 0 10-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-gray-500 text-lg mb-4">No Service yet.</p>
                        <Link class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" :href="base_url+'/services/'+props.case.id" >
                           Add Service
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        </FormWrapper>
    </AppLayout>
</template>
