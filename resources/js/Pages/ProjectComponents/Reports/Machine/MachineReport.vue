<script setup>
import { ref, onMounted, reactive } from 'vue';
import { usePage ,router} from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import CaseFilter from '@/Pages/ProjectComponents/Reports/Case/CaseFilter.vue'; // Update import path
import globalMixin from '../../../../globalMixin';

const page = usePage();
const { base_url,canAny } = globalMixin();
const props = defineProps([]);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    random:Utilities.getRandomNumber()
});

const filter_form = reactive(
    new Form({
        machine_type_id:0,
        model_id:0,
        department_id:0,
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
    setTable();
});

const setTable = () => {
    state.table = $('#service_list').DataTable({
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
                    // columns: ':not(:last-child)', // Exclude the last column
                },
                filename:function(){
                    return 'Housekeeping Machines Report (From Date :' +filter_form.from_date+' To Date :'+filter_form.to_date+') ';
              },
                title: function () {
                    var str = 'Housekeeping Machines Report (From Date :' +filter_form.from_date+' To Date :'+filter_form.to_date+') ';
                    return str;
                },
            },
             {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                exportOptions: {
                    orthogonal: 'export',
                    // columns: ':not(:last-child)', // Exclude the last column
                },
                customize: function (doc) {
                     doc.content[1].table.body.forEach(row => {
                        row.forEach(cell => {
                            cell.alignment = 'center';
                        });
                    });
                    doc.content[1].table.widths =
                        Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            }


        ],
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "350px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value + "/machines-report", // Update URL
            "type": "GET",
             "data":function(c){
                c.report = true;
                c.machine_type_id = filter_form.machine_type_id;
                c.model_id = filter_form.model_id;
                c.department_id = filter_form.department_id;
                c.from_date = filter_form.from_date;
                c.to_date = filter_form.to_date;
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
                title: 'Machine Serial No',
                data: 'serial_no',
            },
               {
                title: 'Machine Code',
                data: 'name_code_no',
            },
              {
                title: 'Brand',
                data: 'brand_name',
            },
             {
                title: 'Machine Type',
                data: 'machine_type',
            },
            {
                title: 'Model',
                data: 'model_no',
            },
             {
                title: 'Department',
                data: 'department_name',
            },
             {
                title: 'Installation Date',
                data: 'installation_date',
            },
            {
                title: 'Warranty Upto Date',
                data: 'warranty_upto_date',
            },
              {
                title: 'No. of cases generated',
                data: 'no_cases',
                 "render": function(data, type, row, meta) {
                    return "<a   data-item-id="+ row.id + " class='font-bold cursor-pointer text-sky-600 text-base services' >"+data+"</a>";
                }
            },
             {
                title: 'No. of Services',
                data: 'no_services',
                 "render": function(data, type, row, meta) {
                    return "<a   data-item-id="+ row.id + " class='font-bold cursor-pointer text-sky-600 text-base services' >"+data+"</a>";
                }
            },
        ],

    });
};

</script>

<template>
    <AppLayout title="Machine Report ">
        <case-filter :key="state.random" :form="filter_form" @reloadTable="resetForm" v-show="state.formOpen == false">
        </case-filter>
        <div>
            <case-form v-if="state.formOpen" @reloadTable="resetForm" :form_id="state.form_id">
            </case-form>
            <ListWrapper :title="'Machines  Report ( '+filter_form.from_date +' - '+filter_form.to_date+' )' ">
                <template #table>
                    <table id="service_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>
