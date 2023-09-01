<script setup>
    import { ref } from 'vue';
    import Search from '@/Pages/CustomComponents/Others/Search.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import NavLink from '@/Components/NavLink.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
    import { Inertia } from '@inertiajs/inertia';
    const props = defineProps(['title']);

</script>
<template>
    <nav>
        <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
            <!-- <li class="leading-normal text-sm">
                <a class="text-white opacity-50  after:content-['/']" href="#">Pages </a>
            </li> -->
            <slot></slot>
            <li class="text-sm pl-2 font-bold capitalize leading-normal text-white before:float-left before:pr-2 before:text-white" aria-current="page" v-text="props.title"></li>
        </ol>

    </nav>
</template>


<style>

</style>
