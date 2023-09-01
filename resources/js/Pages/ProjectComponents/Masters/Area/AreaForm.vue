<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import DepartmentUserSelect from '@/Pages/ProjectComponents/SelectComponents/DepartmentUserSelect.vue';
    import { ref, computed, onMounted, reactive } from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url } = globalMixin();
    const props = defineProps(['form_id', 'departments']); // Change 'states' to 'departments'
    const form = reactive(new Form({
        form_id: 0,
        name: '',
        department_id: 0, // Add 'department_id',
        area_user_id:0,
        person_contact_no:''
    }));
    const data = reactive({ create_url: 'areas',userInitials:[],userSelected:[] ,show:true}); // Change 'cities' to 'areas'
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Area' : 'Add Area'); // Change 'City' to 'Area'

    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        if (props.form_id > 0) {
            getArea(); // Change 'getCity' to 'getArea'
        }
    });
    const submitForm = () => {
        form['postForm'](data.create_url)
            .then(function(response){
                console.log(response);
                if(response.success){
                    emit('resetForm');
                    Utilities.showPopMessage("Your data has been saved successfully!");
                }
            })
            .catch(function(error){
            });
    }
    const getArea = () => { // Change 'getCity' to 'getArea'
        axios.get(base_url.value + '/areas/' + props.form_id + '/edit') // Change 'cities' to 'areas'
        .then(function(response){
            if(response.data.success){
                let area = response.data.area; // Change 'city' to 'area'
                form.name = area.name; // Change 'city.name' to 'area.name'
                form.department_id = area.department_id; // Add this line
                if(area.area_user){
                    data.userInitials = [{'id':area.area_user.id,'text':area.area_user.name}];
                    data.userSelected = [area.area_user.id];
                    Utilities.refreshComponent(data,'show');
                }
            }
            form.form_id = props.form_id;
        })
        .catch(function(error){
        });
    }
</script>

<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" :required="true"/>
                    <TextInput v-model="form.name" type="text" required  autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="department_id" value="Department" :required="true"/> <!-- Change 'state_id' to 'department_id' -->
                    <SelectInput v-model="form.department_id" :options="props.departments" :error="form.errors.get('department_id') ? true :false"></SelectInput> <!-- Change 'states_options' to 'departments_options' -->
                    <InputError class="mt-2" :message="form.errors.get('department_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="area_user_id" value="Area Concerned user" :required="false"/>
                    <department-user-select :initials="data.userInitials" :selected="data.userSelected" :department_id ="form.department_id" v-model="form.area_user_id"></department-user-select>
                    <InputError class="mt-2" :message="form.errors.get('area_user_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="person_contact_no" value="Concerned Person Contact" :required="false"/>
                    <TextInput v-model="form.person_contact_no" type="text" required   :error="form.errors.get('person_contact_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('person_contact_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                    <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
                </div>
            </div>
        </div>
    </FormWrapper>
</div>
</template>

<style>
/* Add your styles here */
</style>
