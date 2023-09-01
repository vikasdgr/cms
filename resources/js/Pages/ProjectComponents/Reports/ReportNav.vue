

<script setup>
    import { Link } from '@inertiajs/vue3';
    import UpperNavLink from '@/Pages/CustomComponents/Layout/UpperNavLink.vue';
    import globalMixin from '../../../globalMixin';
    const { isLinkActive,canAny} = globalMixin();
</script>
<template>
<ul class="flex ml-5 mb-2">
    <li class="m-0"  >
        <UpperNavLink :href="$page.props.base_url +'/cases-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['cases-report'])">
            Cases Report
        </UpperNavLink>

    </li>
    <li class="m-0">
        <UpperNavLink :href="$page.props.base_url +'/machines-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['machines-report'])">
           Machine Wise Report
        </UpperNavLink>
    </li>
    <li class="m-0" >
        <UpperNavLink :href="$page.props.base_url +'/services-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['services-report'])">
           Service Report
        </UpperNavLink>
    </li>
    <li class="m-0" >
        <UpperNavLink :href="$page.props.base_url +'/machines-summary-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['machines-summary-report'])">
           Machine Summary Report
        </UpperNavLink>
    </li>
</ul>
</template>
