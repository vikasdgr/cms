
<script setup>
import { ref, onMounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import globalMixin from '../../../globalMixin';
import UserNav from '@/Pages/ProjectComponents/Users/UserNav.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import FormWrapper from "@/Pages/CustomComponents/Sections/FormWrapper.vue";
  import DepartmentSelect from '@/Pages/ProjectComponents/SelectComponents/DepartmentSelect.vue';

const { base_url,refreshComponent} = globalMixin();
// const data = reactive({ create_url:'site-wise-user-list' });
const props = defineProps(['user','user_departments']);

const form  = reactive(new Form({
    user_id:props.user.id,
    department_ids:[]
}));
const state = reactive({
    active: 'Y',
    table: null,
    user_id:'0',
    user_departments:[],
    create_url:'user-departments',
    showSelect :true,
    departmentInitials:[],
    departmentSelected:[]
});

onMounted(() => {
    state.user_id = props.user.id;
    state.user_departments = props.user_departments;
    getdata();
    setTable();
});


const setTable = () =>{
    state.table = $('#departments_list').DataTable({
        "processing": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "350px",
        scrollCollapse: false,
        pageLength: 10,
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        data: [],
        columns: [

  {
                title: '#',
                data: 'id',
                "render": function (data, type, row, meta) {
                    return  meta.row+1;
                }
            },
            {
                title: 'Department',
                data: 'id',
                "render": function (data, type, row, meta) {
                    return row.name;
                }
            },

        ],
    });
}

const reloadTable = ()=>{
    state.table.clear();
    state.table.rows.add(state.departments).draw();
}



const getdata = ()=>{
    axios.get(base_url.value+'/get-departments/', {params:{user_id:state.user_id}})
        .then(function(response){
            if(response.data.success){
                state.departments = response.data.departments;
                state.departmentInitials = [];
                state.departmentSelected = [];
                state.departments.forEach(dept => {
                    state.departmentInitials.push({'id':dept.id,'text':dept.name});
                    state.departmentSelected.push(dept.id);
                });
                refreshComponent(state,'showSelect');
                reloadTable();
            }
        })
        .catch(function(error){
            console.log(error);
        });
}
  const submitForm = () =>{
        form['postForm'](base_url.value+'/'+state.create_url)
        .then(function(response){
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!");
                getdata();
            }
        })
        .catch(function(error){
        });
    }

const updateDepartments = (ids)=>{
    form.department_ids = ids;
}



</script>

<template>
    <AppLayout :title=" props.user.name+ '\'s Departments'">
        <UserNav></UserNav>
        <div>
            <FormWrapper :title="props.user.name">
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                        <div class="mb-4" v-if="state.showSelect">
                            <InputLabel for="department" value="Departments"/>
                            <department-select @updateDepartment="updateDepartments" :multiple="true"
                            :initials = "state.departmentInitials"
                            :selected = "state.departmentSelected"
                            > </department-select>
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 shrink-0 lg:w-1/2 md:w-1/3 md:flex-0">
                        <div class="mb-4">
                            <ButtonComp @buttonClicked="submitForm" type="save">Update</ButtonComp>
                        </div>
                    </div>

                </div>
            </FormWrapper>
            <div class="flex flex-wrap -mx-3">
                <div class="shrink-0 md:w-full md:flex-0">
                    <ListWrapper :title="props.user.name+':Departments Assigned'">
                        <template #table>
                            <table id="departments_list" width="100%" class="row-border stripe">
                            </table>
                        </template>

                    </ListWrapper>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

