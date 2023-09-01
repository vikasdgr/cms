<script setup>
import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
import MachineTypeSelect from '@/Pages/ProjectComponents/SelectComponents/MachineTypeSelect.vue';
import BrandSelect from '@/Pages/ProjectComponents/SelectComponents/BrandSelect.vue';
import AttachmentUploadModal from '@/Pages/AttachmentComponents/AttachmentUploadModal.vue';
import InputError from '@/Components/InputError.vue';
import { ref, computed, onMounted, reactive } from 'vue';
import globalMixin from '../../../../globalMixin';

const { base_url ,refreshComponent} = globalMixin();
const props = defineProps(['form_id']);
const form = reactive(
    new Form({
        form_id: 0,
        model_no: '',
        machine_type_id:0,
        brand_id:0,
        resources:[]
    })
);
const data = reactive({ create_url: 'machine-models' ,show:true,showAttachment:false,machineTypeInitials:[],machineTypeSelected:[], brandInitials:[], brandSelected:[]}); // Update URL
const pageTitle = computed(() =>
    props.form_id > 0 ? 'Update Machine Model' : 'Add Machine Model'
);

const emit = defineEmits(['resetForm']);
onMounted(() => {
    if (props.form_id > 0) {
        getMachineModel(); // Update function name
    }
});
const submitForm = () => {
    form['postForm'](data.create_url)
        .then(function (response) {
            console.log(response);
            if (response.success) {
                emit('resetForm');
                Utilities.showPopMessage(
                    'Your data has been saved successfully!'
                );
            }
        })
        .catch(function (error) {});
};
const getMachineModel = () => {
    axios
        .get(base_url.value + '/machine-models/' + props.form_id + '/edit') // Update URL
        .then(function (response) {
            if (response.data.success) {
                let machine_model = response.data.machine_model;
                form.model_no = machine_model.model_no;
                 if (machine_model.machine_type) {
                    data.machineTypeInitials = [{'id': machine_model.machine_type.id, 'text': machine_model.machine_type.name}];
                    data.machineTypeSelected = [machine_model.machine_type.id];
                }
                if (machine_model.brand) {
                    data.brandInitials = [{'id': machine_model.brand.id, 'text': machine_model.brand.name}];
                    data.brandSelected = [machine_model.brand.id];
                }
                form.resources = machine_model.resources;
                refreshComponent(data,'show');
            }
            form.form_id = props.form_id;
        })
        .catch(function (error) {});
};

const updateResources = (save= false,files) =>{
    try{
        form.resources = files;
        data.showAttachment = false;
    }
    catch(error){
        console.log('error',error)
    }

}
</script>

<template>
<div>
    <FormWrapper :title="pageTitle">
        <attachment-upload-modal v-if="data.showAttachment"  title="Upload Image"
            @updateStatus="data.showAttachment = false"
            @updateResources="updateResources"
            :initial-resources ="form.resources"
            :show-description ="false"
            :number-attachments="1"
            :errors= "form.errors"
            >
        </attachment-upload-modal>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3  lg:w-1/3 md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="model_no" value="Model No." />
                    <TextInput v-model="form.model_no" type="text" required autofocus :error="form.errors.get('model_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('model_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  lg:w-1/3 md:w-1/3">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="machine_type_id" value="Machine Type" />
                    <machine-type-select v-model="form.machine_type_id" :initials="data.machineTypeInitials" :selected="data.machineTypeSelected"></machine-type-select>
                    <InputError class="mt-2" :message="form.errors.get('machine_type_id')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  lg:w-1/3 md:w-1/3">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="brand_id" value="Brand" />
                    <brand-select v-model="form.brand_id" :initials="data.brandInitials" :selected="data.brandSelected"></brand-select>
                    <InputError class="mt-2" :message="form.errors.get('brand_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  lg:w-full md:w-full">
                <div class="mb-4">
                    <ButtonComp @buttonClicked="submitForm" type="save" class="mr-2" > Save </ButtonComp>
                    <ButtonComp @buttonClicked="data.showAttachment = true"  class="mr-2" type="save">Upload Attachment</ButtonComp>
                    <ButtonComp @buttonClicked="emit('resetForm')" type="cancel" > Cancel </ButtonComp>
                </div>
            </div>
        </div>
    </FormWrapper>
</div>
</template>

<style>

</style>
