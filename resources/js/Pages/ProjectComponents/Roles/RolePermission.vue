
<script setup>
    import { ref, onMounted ,reactive,nextTick } from 'vue';
    import { Inertia } from '@inertiajs/inertia';

    import AppLayout from '@/Layouts/AppLayout.vue';
    import CheckboxInput from '@/Pages/CustomComponents/Forms/CheckboxInput.vue';
    import TitleComponent from '@/Pages/CustomComponents/Others/TitleComponent.vue';
    import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
     import UserNav from '@/Pages/ProjectComponents/Users/UserNav.vue';

     import globalMixin from '../../../globalMixin';
    const props = defineProps([ 'role', 'opt_groups', 'permissions','data']);
    const {base_url} = globalMixin();

    onMounted(() => {
        props.permissions.forEach(id =>{
            $("#opt-permission-"+id).prop("checked",true);
             var checked_all = checkIfAllCheckBoxesAreCheckedInGroup(id);
            $("#opt-group-"+checked_all[1].id).prop("checked",checked_all[0]);
        });

    });

    const allCheckboxInGroupStatusChange = (e,id) =>{
        if($(e.target).is(':checked')){
            makeAllCheckboxChangeInGroup(true,id);
        }
        else{
            makeAllCheckboxChangeInGroup(false,id);
        }
    }

    const innerCheckBoxChecked = (e,id) =>{
        if($(e.target).is(':checked')){
            props.permissions.push(id);
        }
        else{
            let index = props.permissions.indexOf(id);
            props.permissions.splice(index,1);
        }
        var checked_all = checkIfAllCheckBoxesAreCheckedInGroup(id);
        $("#opt-group-"+checked_all[1].id).prop("checked",checked_all[0]);
    }

    const checkIfAllCheckBoxesAreCheckedInGroup = (id) =>{
        var group = null;
        var checked_all = true;
        props.opt_groups.forEach(opt_group=> {
            if(opt_group.permissions.find(arr => arr.id == id)){
                group = opt_group;
            }
        });

        group.permissions.forEach(element => {
            if(props.permissions.indexOf(element.id) < 0 ){
                checked_all = false;
            }
        });

        return [checked_all, group];
    }

    const makeAllCheckboxChangeInGroup = (status,id) =>{
        let group = props.opt_groups.find(arr=>arr.id === id);
        console.log(status,id,group);
        if(group){
            group.permissions.forEach(element => {
                if(status){
                    props.permissions.push(element.id);
                }
                else{
                    let ind = props.permissions.indexOf(element.id);
                    props.permissions.splice(ind,1);
                }
                $("#opt-permission-"+element.id).prop("checked",status);
            });
        }
    }

    const submitForm = () =>{
        axios.post(base_url.value+'/roles/'+ props.role.id +'/permissions', {
            'permissions':props.permissions
        })
        .then(function(response){
            Utilities.showPopMessage("Your data has been saved successfully!")
            visitRolesPage();
        })
        .catch(function(error){});
    }


    const visitRolesPage = () =>{
        Inertia.visit(base_url.value+'/roles');
    }

</script>

<template>
      <AppLayout title="Permissions" :data="data">
        <UserNav></UserNav>
        <!-- <template #header>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                    Permissions
            </span>
        </template> -->


        <ListWrapper title="Roles Permissions">
              <TitleComponent>
            <template #right-content>
                <ButtonComp @buttonClicked="submitForm" >
                        <span> Update</span>
                </ButtonComp>
                <ButtonComp @buttonClicked="visitRolesPage">
                    Back
                </ButtonComp>
            </template>
        </TitleComponent>
                <template #table>
                    <table id="roles_list" width="100%" class="row-border stripe">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="py-4 px-6">
                                {{role.name}}
                            </th>
                            <th scope="col" class="py-4 px-6">
                                Permission
                            </th>
                            <th scope="col" class="py-4 px-6">
                            </th>
                        </tr>
                    </thead>
                    <tbody v-for="(g, index) in props.opt_groups" :key="index">
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 bg-indigo-500 bg-opacity-25">
                                <td  colspan="2" class="py-4 px-6 font-bold text-gray-900 whitespace-nowrap dark:text-white text-center ">
                                    {{ g.opt_group }}
                                </td>
                                <td class="py-4 px-6">
                                    <CheckboxInput  :id="'opt-group-'+g.id" :value="g.id"  v-on:changed="allCheckboxInGroupStatusChange($event, g.id)"></CheckboxInput>
                                </td>
                            </tr>
                            <tr  v-for="(p, index) in g.permissions" :key="index" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ index+1 }}
                                </td>
                                <td class="py-4 px-6">
                                    {{ p.label }}
                                </td>
                                <td class="py-4 px-6">
                                    <CheckboxInput :id="'opt-permission-'+p.id"  type="checkbox" :value="p.id" v-on:changed="innerCheckBoxChecked($event, p.id)"></CheckboxInput>
                                </td>

                            </tr>
                        </tbody>
                        <tfoot>
                            <ButtonComp @buttonClicked="submitForm" type="save">
                                    <span> Update</span>
                            </ButtonComp>
                            <ButtonComp @buttonClicked="visitRolesPage"  type="cancel">
                                Back
                            </ButtonComp>
                        </tfoot>
                    </table>
                </template>
        </ListWrapper>

    </AppLayout>
</template>


<style>

</style>


