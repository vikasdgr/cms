<script setup>
import { ref, onMounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import AreaForm from '@/Pages/ProjectComponents/Masters/Area/AreaForm.vue'; // Change this to the correct path for the AreaForm component
import globalMixin from '../../../../globalMixin';

const { base_url, canAny,refreshComponent } = globalMixin();
const page = usePage();
const props = defineProps(['departments']); // Change 'states' to 'departments'
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
});

const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.table.ajax.reload(null, false);
    // Inertia.reload();
}

const editArea = (id) => { // Change the function name to 'editArea'
    state.formOpen = true;
    state.form_id = id;
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#areas_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollX: true,
        scrollY: "350px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value + "/areas-list", // Change this URL to the correct endpoint for areas
            "type": "GET",
        },
        'createdRow': function (row, data, dataIndex) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        columns: [
            {
                title: 'Sr no.',
                data: 'id',
                "render": function (data, type, row, meta) {
                    var str = meta.row + parseInt(meta.settings.json.start) + 1;
                    return str;
                }
            },
            {
                title: 'Name',
                data: 'name',
            },
            {
                title: 'Department',
                 "render": function (data, type, row, meta) {
                    var str = row.department ? row.department.name:'';
                    return str;
                }
            },
            {
                title: 'Area User',
                 "render": function (data, type, row, meta) {
                    var str = row.area_user ? row.area_user.name:'';
                    return str;
                }
            },
              {
                title: 'Area User Contact No.', data:'person_contact_no'

            },
            // Other columns...
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                visible: canAny(page.props.granted_permissions, ['areas-modify']),
                render: function (data, type, row, meta) {
                    return '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700" aria-hidden="true"></i> Edit </button>';
                }
            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").on('click', function (e) {
                state.formOpen = false;
                setTimeout(function(){
                    editArea(e.target.dataset.itemId); // Change the function name to 'editArea'
                },100);

            });
        }
    });
}
</script>
<template>
    <AppLayout title="Areas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Area Lists
            </h2>
        </template>
        <div>
            <area-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id" :departments="departments"
            >
            </area-form>
            <ListWrapper title="Areas List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false">
                        <i class="fa-solid fa-plus mr-2"></i> New Area
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="areas_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>
