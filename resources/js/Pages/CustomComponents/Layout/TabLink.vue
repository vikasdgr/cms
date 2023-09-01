<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    icon: String,
    id:String,
    type: {
        type:String,
        default:'button'
    },
    colorClass: String,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center justify-center p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 group'
        : 'inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 group';
});

const icon_classes = computed(() => {
    return 'relative top-0 leading-normal mr-2 '+props.colorClass+' '+ props.icon +' text-sm';
});
</script>

<template>
    <span :id="props.id" :class="classes" v-if="props.type=='button'">
           <i :class="icon_classes"></i>
            <slot />
    </span>
    <Link :href="href" :class="classes" v-else>
        <!-- <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5"> -->
            <!-- <i :class="icon_classes"></i> -->
        <!-- </div> -->
        <!-- <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">  -->
             <!-- <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
                    </svg> -->
            <i :class="icon_classes"></i>
             <slot />
            <!-- </span> -->
    </Link>
</template>
