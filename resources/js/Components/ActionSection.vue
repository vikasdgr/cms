<script setup>
import SectionTitle from './SectionTitle.vue';
</script>

<template>
    <div class="bg-white rounded md:grid md:grid-cols-3 px-4 py-5 shadow">
        <SectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2 border rounded">
            <div class="px-4 py-5 sm:p-6 bg-white sm:rounded-lg">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>
