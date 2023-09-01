/8

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import DepartmentForm from '@/Pages/ProjectComponents/Masters/Department/DepartmentForm.vue';
// import EmployeeNav from '@/Pages/ProjectComponents/Masters/MastersNav.vue';
import globalMixin from '../../../../globalMixin';

const { base_url} = globalMixin();
const props = defineProps([]);
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

const editDepartment = (id) => {
    state.formOpen = true;
    state.form_id = id;
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#department_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollX: true,
        scrollY: "350px",
        "iDisplayLength": 10,
        scrollCollapse: false,
         pageLength: 10,
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},

        fixedColumns: {
            left: 2,
            right: 1
        },
        "ajax": {
            "url": base_url.value+"/departments-list",
            "type": "GET",
        },
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        columns: [{
                title: 'Sr no.',
                data: 'id',
                "render": function (data, type, row, meta) {
                    var str = meta.row + parseInt(meta.settings.json.start) +1;
                    return  str;
                }
            },
            {
                title: 'Name',
                data: 'name',
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                render: function (data, type, row, meta) {
                     return '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").on('click', function (e) {
                state.formOpen = false;
                editDepartment(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Departments">
         <!-- <EmployeeNav>
        </EmployeeNav> -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Department
            </h2>
        </template>
        <div>
            <Department-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
            >
            </Department-form>
            <ListWrapper title="Departments List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false">
                        <i class="fa-solid fa-plus mr-2"></i> New Department
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="department_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

