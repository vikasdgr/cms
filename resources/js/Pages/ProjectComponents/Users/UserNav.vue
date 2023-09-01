

<script setup>
    import { Link } from '@inertiajs/vue3';
    import UpperNavLink from '@/Pages/CustomComponents/Layout/UpperNavLink.vue';
    import globalMixin from '../../../globalMixin';
    const { isLinkActive,canAny} = globalMixin();
</script>
<template>
<ul class="flex ml-5 mb-2">
    <li class="m-0" v-if="canAny($page.props.granted_permissions,['users'])" >
        <UpperNavLink :href="$page.props.base_url +'/users'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['users'])">
            Users
        </UpperNavLink>

    </li>
    <li class="m-0" v-if="canAny($page.props.granted_permissions,['roles' ])" >
        <UpperNavLink :href="$page.props.base_url +'/roles'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['roles','permissions'])">
            Roles
        </UpperNavLink>
    </li>

</ul>
</template>
