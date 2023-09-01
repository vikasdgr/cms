<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
// import Welcome from '@/Components/Welcome.vue';
import MachineSummary from '@/Pages/MachineSummary.vue';

  import {  onMounted} from 'vue';

  //TO SOLVE CSRF ISSUE ON PAGE AFTER FIRST LOGIN
  const props = defineProps(['csrf']);
    onMounted(() => {
        $('meta[name="csrf-token"]').attr('content',props.csrf);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN':props.csrf
            }
        });
    });
</script>

<template>
    <AppLayout title="Machine Summary">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Machine Summary
            </h2>
        </template>

        <div class="w-full mx-auto sm:px-6 lg:px-6">
            <!-- <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"> -->
                <!-- <Welcome /> -->
                <MachineSummary></MachineSummary>
            <!-- </div> -->
        </div>
    </AppLayout>
</template>
