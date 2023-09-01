<script setup>
import { onMounted, ref ,computed} from 'vue';

const props = defineProps({
    modelValue: [String,Number],
    class:{
        default:'w-full',
        type:String
    },
    error:Boolean,
    success:Boolean,
    type:{
        default:'text'
    },
    placeholder:{
        default:''
    }
});

const classes = computed(() => {
    return props.success ? props.class + ' focus:shadow-primary-outline text-sm leading-5.6 ease block appearance-none rounded-lg border border-solid border-2 border-green-500 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none'
            :  props.error ? props.class + ' focus:shadow-primary-outline text-sm leading-5.6 ease block appearance-none rounded-lg border border-solid border-red-400 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none'
            :  props.class + ' focus:shadow-primary-outline text-sm leading-5.6 ease block appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none';
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        :type="type"
        :class="classes"
        :value="modelValue"
        :placeholder="placeholder"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
