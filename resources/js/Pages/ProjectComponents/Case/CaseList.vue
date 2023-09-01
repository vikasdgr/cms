<script setup>
import { ref, onMounted, reactive } from 'vue';
import { usePage ,router} from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import CaseForm from '@/Pages/ProjectComponents/Case/CaseForm.vue'; // Update import path
import CaseFilter from '@/Pages/ProjectComponents/Case/MachineCaseFilter.vue'; // Update import path
import globalMixin from '../../../globalMixin';
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
        machine_id:0,
        machine_model_id:'',
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
                    columns: ':not(:last-child)', // Exclude the last column
                },
                filename:function(){
                    return 'Housekeeping Machines Service Cases';
                },
                title: function () {
                    var str = 'Housekeeping Machines Service Cases';
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
            "url": base_url.value + "/machine-cases-list", // Update URL
            "type": "GET",
             "data":function(c){
                c.machine_id = filter_form.machine_id;
                c.model_id = filter_form.machine_model_id;
                c.status = filter_form.status;
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
                title: 'Department',
                data: 'department_name',
            },
            {
                title: 'Area',
                data: 'area',
            },
            {
                title: 'Case No.',
                data: 'case_no',
                "render": function(data, type, row, meta) {
                    return "<a   data-item-id="+ row.id + " class='font-bold cursor-pointer text-sky-600 text-base services' >"+data+"</a>";
                }
            },
            {
                title: 'Work Type',
                data: 'work_types',
            },
             {
                title: 'Scheduled Date',
                data: 'open_date',
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
                title: 'Remarks',
                data: 'description',
            },
            {
                title: 'Status',
                data: 'status',
                "render": function(data, type, row, meta) {
                   let str = data =='P' ? 'Pending':(data == 'C'? 'Closed':data == 'F'?'Follow-Up Required':'');
                    return str;
                }
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                render: function(data, type, row, meta) {
                    var str = '<div class="flex items-end">';
                    if(canAny(page.props.granted_permissions,['schedule-services-modify'])){
                        str+= '<button  data-item-id=' + data + ' class="flex inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    }
                    if(canAny(page.props.granted_permissions,['services']) && (row.status == 'P' || row.status == 'F')){
                        let caption = row.status == 'P' ? 'Service' :'Follow-Up';
                        str +=  '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  case-service">'+caption+' </button>';
                    }

                    str+= '</div>'
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
            $(".case-service").on('click', function(e) {
                router.visit(base_url.value+'/services/'+e.target.dataset.itemId);
            });
             $(".services").on('click', function(e) {
                router.visit(base_url.value+'/services-details/'+e.target.dataset.itemId);
            });
        }
    });
};

</script>

<template>
    <AppLayout title="Housekeeping Machines's Scheduled Service ">
        <case-filter :key="state.random" :form="filter_form" @reloadTable="resetForm" v-show="state.formOpen == false">
        </case-filter>
        <div>
            <case-form v-if="state.formOpen" @reloadTable="resetForm" :form_id="state.form_id">
            </case-form>
            <ListWrapper title="Machine Service Requests">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false">
                        <i class="fa-solid fa-plus mr-2"></i> Schedule Service
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="service_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>
