
<script setup>
import { ref, onMounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import UserForm from '@/Pages/ProjectComponents/Users/UserForm.vue';
import globalMixin from '../../../globalMixin';
import UserNav from '@/Pages/ProjectComponents/Users/UserNav.vue';
import { usePage} from '@inertiajs/vue3';

const { base_url,canAny} = globalMixin();
const props = defineProps(['roles']);
const page = usePage();
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

const editUser = (id) => {
    state.formOpen = true;
    state.form_id = id;
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#users_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "350px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/users-list",
            "type": "GET",
        },
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        columns: [{
                title: 'Sr no.',
                data: 'id',
                "render": function (data, type, row, meta) {
                    var index = meta.row + parseInt(meta.settings.json.start);
                    return index + 1;
                }
            },
            {
                title: 'Name',
                data: 'name',
            },
            {
                title: 'Email',
                data: 'email',
            },
             {
                title: 'Role',
                data:'id',
                "render": function (data, type, row, meta) {
                      return row.roles &&  row.roles.length >0  && row.roles[0] ?   row.roles[0].name:'';
                }
            },
             {
                title: 'Departments',
                data:'id',
                 "render": function (data, type, row, meta) {
                    var departments = [];
                    row.departments.forEach(element => {
                        if(element.branch){
                            departments.push(element.name);
                        }
                    });
                    let dept_str = Utilities.joinArrayAsString(departments);
                    var str= dept_str;
                   return str;
                }
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                "render": function (data, type, row, meta) {
                    let str = '';
                        str += '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700"  data-item-id=' + data + '  aria-hidden="true"></i> Edit </button>';
                        if(canAny(page.props.granted_permissions,['user-sites'])){
                            str += "<a href='" + 'users/' + data + '/departments'+ " ' class='inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item' target = _blank><i class='mr-2 fas fa-pencil-alt  text-slate-700'  aria-hidden='true'></i> Departments</a>";
                        }

                        if(canAny(page.props.granted_permissions,['user-active-deactive'])){
                            if(row.active == 'N'){
                                str += '<button  data-item-id=' + data + ' data-status="Y"  data-type="Active" class="inline-block text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-orange-500 border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 active">Active</button>';
                            }
                            else{
                                str += '<button  data-item-id=' + data + ' data-status="N" data-type="De-Active" class="inline-block text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-red-500 border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 active">Deactive</button>';
                            }
                        }


                        return str;
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").on('click', function (e) {
                state.formOpen = false;
                editUser(e.target.dataset.itemId);
            });

            $(".active").on('click', function (e) {
                activeDactive(e.target.dataset.itemId,e.target.dataset.status,e.target.dataset.type);
            });

        }
    });

    const activeDactive = (id,status,type)=>{
        let con = confirm('Are you sure you want to '+type+' user ?');
        if(con){
            axios.get(base_url.value+'/users/active-deactive/', {params:{user_id:id,status:status}})
            .then(function(response){
                if(response.data.success){
                    let user = response.data.user;
                    resetForm();
                }
            })
            .catch(function(error){
            });
        }

    }
}

</script>

<template>
    <AppLayout title="Users">
        <UserNav></UserNav>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                State
            </h2>
        </template>
        <div>
            <user-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
            :roles="roles">
            </user-form>
            <ListWrapper title="Users List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false">
                        <i class="fa-solid fa-plus mr-2"></i> New User
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="users_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

