<script setup>
    import { ref, onMounted, reactive } from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import UserForm from '@/Pages/ProjectComponents/Users/UserForm.vue';
    import globalMixin from '../../../globalMixin';
    import UserNav from '@/Pages/ProjectComponents/Users/UserNav.vue';


    const props = defineProps(['roles']);
    const {base_url} = globalMixin();

    const addPermissions = (id) =>{
        Inertia.get(base_url.value+'/roles/'+id +'/permissions', { }, { preserveState: true });
    }

    onMounted(() => {
       $('#roles_list').DataTable();
    });
</script>

<template>
    <AppLayout title="Roles">
        <UserNav></UserNav>
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role
            </h2>
        </template> -->
        <div>
            <ListWrapper title="Roles List">
                <template #table>
                    <table id="roles_list" width="100%" class="row-border stripe">
                    <thead class="text-xs text-gray-600 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="py-4 px-6">Sr. no</th>
                            <th scope="col" class="py-4 px-6">Role</th>
                            <th scope="col" class="py-4 px-6">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='(role,index) in roles' :key="role.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="py-4 px-6 text-gray-600 whitespace-nowrap dark:text-white" v-text="index+1"></td>
                            <td class="py-4 px-6 text-gray-600 whitespace-nowrap dark:text-white" v-text="role.name"></td>
                            <td>
                                <ButtonComp @buttonClicked="addPermissions(role.id)" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700"><i class="mr-2 fas fa-shield-halved text-slate-700" aria-hidden="true"></i> Permissions</ButtonComp>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>
