<script setup>
import { ref, onMounted, reactive,computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import ServiceForm from '@/Pages/ProjectComponents/Services/ServiceForm.vue'; // Update import path
import globalMixin from '../../../globalMixin';
const page = usePage();
const { base_url,canAny } = globalMixin();
const props = defineProps(['case']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    random:Utilities.getRandomNumber()
});
const pageTitle = computed(() =>
    props.case > 0 ? 'Case No.  ' + props.case.case_no + '(Machine : ' + props.case.machine.serial_no+')': 'Schedule Service'
);

const filter_form = reactive(
    new Form({
        machine_id:0,
        model_id:'',
        status:'P',
        from_date:CMS.from_date,
        to_date:CMS.today,
    })
);

const resetForm = () => {
    state.form_id = 0;
    state.table.ajax.reload(null, false);
    state.formOpen = false;
};

const editMachine = (id) => {
    state.formOpen = true;
    state.form_id = id;
};

onMounted(() => {
});


</script>

<template>
    <AppLayout :title="pageTitle">
        <div>
            <service-form :case="props.case">
            </service-form>
        </div>
    </AppLayout>
</template>
