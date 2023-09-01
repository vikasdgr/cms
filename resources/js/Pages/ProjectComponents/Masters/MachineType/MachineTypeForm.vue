<script setup>
import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';

import InputError from '@/Components/InputError.vue';
import { ref, computed, onMounted, reactive } from 'vue';
import globalMixin from '../../../../globalMixin';

const { base_url } = globalMixin();
const props = defineProps(['form_id']);
const form = reactive(
    new Form({
        form_id: 0,
        name: '',
    })
);
const data = reactive({ create_url: 'machine-types' }); // Update URL
const pageTitle = computed(() =>
    props.form_id > 0 ? 'Update Machine Type' : 'Add Machine Type'
);

const emit = defineEmits(['resetForm']);
onMounted(() => {
    if (props.form_id > 0) {
        getMachineType(); // Update function name
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
const getMachineType = () => {
    axios
        .get(base_url.value + '/machine-types/' + props.form_id + '/edit') // Update URL
        .then(function (response) {
            if (response.data.success) {
                let machine_type = response.data.machine_type; // Update variable name
                form.name = machine_type.name; // Update variable name
            }
            form.form_id = props.form_id;
        })
        .catch(function (error) {});
};
</script>

<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/2 md:w-1/2 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        :error="form.errors.get('name') ? true :false"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.get('name')"
                    />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <ButtonComp
                        @buttonClicked="submitForm"
                        type="save"
                    >
                        Save
                    </ButtonComp>
                    <ButtonComp
                        @buttonClicked="emit('resetForm')"
                        type="cancel"
                    >
                        Cancel
                    </ButtonComp>
                </div>
            </div>
        </div>
    </FormWrapper>
</div>
</template>

<style>

</style>
