<script setup>
import { ref, onMounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
import MachineForm from '@/Pages/ProjectComponents/Machines/Machine/MachineForm.vue'; // Update import path
import MachineFilter from '@/Pages/ProjectComponents/Machines/Machine/MachineFilter.vue'; // Update import path
import CaseForm from '@/Pages/ProjectComponents/Case/CaseForm.vue'; // Update import path
import DepartmentChange from '@/Pages/ProjectComponents/Machines/Machine/DepartmentChange.vue'; // Update import path


import globalMixin from '../../../../globalMixin';

const { base_url,canAny } = globalMixin();
const props = defineProps([]);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    showCaseForm:false,
    changeDepartment:false
});

const filter_form = reactive(
    new Form({
        brand_id:'',
        model_id:'',
        machine_type_id:'',
        department_id:'',
        area_id:'',
    })
);

const resetForm = () => {
    state.form_id = 0;
    state.table.ajax.reload(null, false);
    state.formOpen = false;
    state.showCaseForm = false;
    state.changeDepartment=false;
};

const editMachine = (id) => {
    state.formOpen = true;
    state.form_id = id;
};

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#machine_list').DataTable({
        dom: 'Bfrtip',
        lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        buttons: [
            'pageLength',
            {
                extend: 'excelHtml5',
                header: true,
                footer: true,
                exportOptions: {
                    orthogonal: 'export',
                    columns: ':not(:last-child)', // Exclude the last column
                },
                filename:function(){
                    return 'Housekeeping Machines';
                },
                title: function () {
                    var str = 'Housekeeping Machines';
                    return str;
                },
            },
             {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                exportOptions: {
                    orthogonal: 'export',
                    columns: ':not(:last-child)', // Exclude the last column
                },
                 customize: function (doc) {
                    doc.content[1].table.widths =
                        Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            }


        ],
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollX: true,
        scrollY: "350px",
        "iDisplayLength": 10,
        scrollCollapse: false,
         pageLength: 10,
        fixedColumns: {
            left: 2,
            right: 1
        },
        "ajax": {
            "url": base_url.value + "/machines-list", // Update URL
            "type": "GET",
             "data":function(c){
                c.brand_id= filter_form.brand_id,
                c.model_id= filter_form.model_id,
                c.machine_type_id= filter_form.machine_type_id,
                c.department_id= filter_form.department_id,
                c.area_id= filter_form.area_id
            }
        },
        'createdRow': function(row, data, dataIndex) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        columns: [{
                title: 'Sr no.',
                data: 'id',
                "render": function(data, type, row, meta) {
                    var str = meta.row + parseInt(meta.settings.json.start) + 1;
                    return str;
                }
            },
            {
                title: 'Machine Serial No.',
                data: 'serial_no',
            },
              {
                title: 'Brand',
                data: 'brand_name',
            },
              {
                title: 'Model',
                data: 'model_no',
            },
             {
                title: 'Machine Type',
                data: 'machine_type',
            },
             {
                title: 'Department',
                data: 'department_name',
            },
             {
                title: 'Area',
                data: 'area',
            },
             {
                title: 'Buy Date',
                data: 'buy_date',
            },
            {
                title: 'Installation Date',
                data: 'installation_date',
            },
              {
                title: 'Warranty Date',
                data: 'warranty_upto_date',
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                render: function(data, type, row, meta) {
                    var str=  '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                        str +=  '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  generate-case">Schedule Service </button>';
                        str +=  '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  change-department">Change Department </button>';
                    return str;
                }
            }
        ],
        "drawCallback": function(settings) {
            $(".edit-item").on('click', function(e) {
                state.formOpen = false;
                setTimeout(function(){
                    editMachine(e.target.dataset.itemId);
                },200);
            });
              $(".generate-case").on('click', function(e) {
                state.formOpen = false;
                setTimeout(function(){
                    state.showCaseForm = true;
                    state.form_id = e.target.dataset.itemId;
                },200);
            });
             $(".change-department").on('click', function(e) {
                state.changeDepartment = false;
                state.formOpen = false;
                setTimeout(function(){
                    state.changeDepartment = true;
                    state.form_id = e.target.dataset.itemId;
                },200);
            });
        }
    });
    $('thead > tr> th:nth-child(11)').css({ 'min-width': '300px' });
};
</script>

<template>
    <AppLayout title="Housekeeping Machines">
        <Modal :show="state.showCaseForm" max-width="5xl">
            <case-form :machine_id="state.form_id" @resetForm="resetForm"></case-form>
        </Modal>
        <Modal :show="state.changeDepartment" max-width="4xl">
            <department-change v-if="state.changeDepartment" :machine_id="state.form_id" @resetForm="resetForm">
            </department-change>
        </Modal>
        <machine-filter :form="filter_form" @reloadTable="resetForm" v-show="state.formOpen == false">
        </machine-filter>
        <div>
            <machine-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id">
            </machine-form>

            <ListWrapper title="Housekeeping Machines List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false">
                        <i class="fa-solid fa-plus mr-2"></i> Add Machine
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="machine_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>
