
<script setup>
    import { Link } from '@inertiajs/vue3';
    import SideNavLink from '@/Pages/CustomComponents/Layout/SideNavLink.vue';
    import SideNavDropDown from '@/Pages/CustomComponents/Layout/SideNavDropDown.vue';
    import SideDropDownItemLink from '@/Pages/CustomComponents/Layout/SideDropDownItemLink.vue';
    import ProjectLogo from '@/Pages/CustomComponents/Others/ProjectLogo.vue';
    import globalMixin from '../globalMixin';
    const { isLinkActive,canAny} = globalMixin();
</script>
<template>
    <!-- sidebar -->
    <aside id="drawer-navigation"
        :class="$page.props.sidebar =='closed' ?  'expand iw-sidebar fixed inset-y-0 flex-wrap items-center justify-between block p-0 my-4 overflow-y-auto overflow-x-hidden antialiased transition-transform duration-200 -translate-x-full bg-white border-0 shadow-xl dark:shadow-none dark:bg-slate-850 xl:transition-width w-[220px] ease z-50 xl:ml-6 rounded-2xl xl:left-0 xl:translate-x-0':'iw-sidebar fixed inset-y-0 flex-wrap items-center justify-between block p-0 my-4 overflow-y-auto overflow-x-hidden antialiased transition-transform duration-200 -translate-x-full bg-white border-0 shadow-xl dark:shadow-none dark:bg-slate-850 xl:transition-width w-[220px] ease z-50 xl:ml-6 rounded-2xl xl:left-0 xl:translate-x-0'">
      <ProjectLogo :href="$page.props.base_url +'/dashboard'">
        </ProjectLogo>
        <hr class="h-px mt-0 mb-4 opacity-25 border-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent">
        <div class="items-center block w-auto max-h-screen iw-sidenav grow basis-full" data-accordion="collapse">
            <ul class="flex flex-col pl-0 mb-0">
                <li class="mt-0.5 w-full">
                    <SideNavLink :href="$page.props.base_url +'/dashboard'" icon="fa fa-tv" color-class="text-indigo-500" data-drawer-hide="drawer-navigation">
                        Dashboard
                    </SideNavLink>
                </li>
              <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions, ['departments', 'areas', 'machine-types', 'machine-models', 'problems', 'maintenances', 'resolving-actions', 'brands'])">
                    <SideNavDropDown id-name="#masters" icon="fa fa-building-user" color-class="text-gray-500" :active="isLinkActive(['departments', 'areas', 'machine-types', 'machine-models', 'problems', 'maintenances', 'resolving-actions', 'brands'])">
                        Masters
                    </SideNavDropDown>
                    <ul id="masters" class="py-2 space-y-2 bg-primary/5" v-bind:class="{'hidden': !isLinkActive(['departments', 'areas', 'machine-types', 'machine-models', 'problems', 'maintenances', 'resolving-actions', 'brands'])}">
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions, ['departments'])" :href="$page.props.base_url +'/departments'" :active="isLinkActive(['departments'])">
                            Departments
                        </SideDropDownItemLink>

                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions, ['areas'])" :href="$page.props.base_url +'/areas'" :active="isLinkActive(['areas'])">
                            Areas
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions, ['machine-types'])" :href="$page.props.base_url +'/machine-types'" :active="isLinkActive(['machine-types'])">
                            Machine Types
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions, ['machine-models'])" :href="$page.props.base_url +'/machine-models'" :active="isLinkActive(['machine-models'])">
                            Machine Models
                        </SideDropDownItemLink>
                          <SideDropDownItemLink v-if="canAny($page.props.granted_permissions, ['brands'])" :href="$page.props.base_url +'/brands'" :active="isLinkActive(['brands'])">
                            Brands
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions, ['problems'])" :href="$page.props.base_url +'/problems'" :active="isLinkActive(['problems'])">
                            Problems
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions, ['maintenances'])" :href="$page.props.base_url +'/maintenances'" :active="isLinkActive(['maintenances'])">
                            Maintenances
                        </SideDropDownItemLink>
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions, ['resolving-actions'])" :href="$page.props.base_url +'/resolving-actions'" :active="isLinkActive(['resolving-actions'])">
                            Resolving Actions
                        </SideDropDownItemLink>

                    </ul>
                </li>

                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,['machines'])" :href="$page.props.base_url +'/machines'"  :active="isLinkActive(['machines'])" >
                    <SideNavDropDown id-name="#machines" icon="fa fa-cog" color-class="text-violet-300"  :active="isLinkActive(['machines'])">
                        Machines
                    </SideNavDropDown>
                    <ul id="machines" class="py-2 space-y-2 bg-primary/5"
                         v-bind:class="{'hidden':!isLinkActive(['machines'])}"
                    >   <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['machines'])" :href="$page.props.base_url +'/machines'"  :active="isLinkActive(['machines'])" >
                            Machines
                        </SideDropDownItemLink>
                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['approve-employee'])" :href="$page.props.base_url +'/approve-employee'"  :active="isLinkActive(['approve-employee'])" >
                            Change Machine Location
                        </SideDropDownItemLink>

                    </ul>
                </li>

                 <li class="mt-0.5 w-full">
                    <SideNavLink icon="fa fa-tags" color-class="text-pink-500" v-if="canAny($page.props.granted_permissions,['schedule-services'])" :href="$page.props.base_url +'/machine-cases'"  :active="isLinkActive(['machine-cases'])">
                        Schedule Cases
                    </SideNavLink>
                </li>

                <li class="mt-0.5 w-full"  v-if="canAny($page.props.granted_permissions,['supervisor-employees','change-bank-request','approve-employee','change-supervisor-site-request'])"  >
                    <SideNavDropDown id-name="#super-employees" icon="fa fa-user-tie" color-class="text-violet-300"  :active="isLinkActive(['supervisor-employees','approve-employee','change-supervisor-site-request','change-bank-request'])">
                        Supervisors
                    </SideNavDropDown>
                    <ul id="super-employees" class="py-2 space-y-2 bg-primary/5"
                         v-bind:class="{'hidden':!isLinkActive(['supervisor-employees','change-supervisor-site-request','approve-employee'])}"
                    >   <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['supervisor-employees'])" :href="$page.props.base_url +'/supervisor-employees'"  :active="isLinkActive(['supervisor-employees'])" >
                            Supervisor Employees
                        </SideDropDownItemLink>
                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['approve-employee'])" :href="$page.props.base_url +'/approve-employee'"  :active="isLinkActive(['approve-employee'])" >
                            Approve Supervisors
                        </SideDropDownItemLink>
                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['change-supervisor-site-request'])" :href="$page.props.base_url +'/change-supervisor-site-request'"  :active="isLinkActive(['change-supervisor-site-request'])" >
                            Change  Site Request
                        </SideDropDownItemLink>
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['change-bank-request'])" :href="$page.props.base_url +'/change-bank-request'"  :active="isLinkActive(['change-bank-request'])" >
                            Change  Bank Request
                        </SideDropDownItemLink>
                    </ul>
                </li>
                <li class="mt-0.5 w-full"  v-if="canAny($page.props.granted_permissions,['salary-sheets'])"  >
                    <SideNavDropDown id-name="#salary-sheets" icon="fa fa-inr " color-class="text-cyan-500"  :active="isLinkActive(['salary-sheets'])">
                        Salary
                    </SideNavDropDown>
                    <ul id="salary-sheets" class="py-2 space-y-2 bg-primary/5"
                         v-bind:class="{'hidden':!isLinkActive(['salary-sheets','employee-days'])}"
                    >   <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['salary-sheets'])" :href="$page.props.base_url +'/salary-sheets'"  :active="isLinkActive(['salary-sheets','employee-days'])" >
                            Salary Sheets
                        </SideDropDownItemLink>
                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['approve-employee'])" :href="$page.props.base_url +'/approve-employee'"  :active="isLinkActive(['approve-employee'])" >
                           Advance Salary
                        </SideDropDownItemLink>
                    </ul>
                </li>
                <li class="mt-0.5 w-full">
                    <SideNavDropDown id-name="#reports" icon="fa fa-arrow-up-right-dots" color-class="text-indigo-500"  :active="isLinkActive(['cases-report','machines-report','services-report'])">
                        Reports
                    </SideNavDropDown>
                    <ul id="reports" class="py-2 space-y-2 bg-primary/5"
                         v-bind:class="{'hidden':!isLinkActive(['cases-report','machines-report','services-report','machines-summary-report'])}"
                    >
                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['cases-report'])"  :href="$page.props.base_url +'/cases-report'"  :active="isLinkActive(['cases-report'])" >
                            Cases Report
                        </SideDropDownItemLink>

                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['machines-report'])" :href="$page.props.base_url +'/machines-report'"  :active="isLinkActive(['machines-report'])" >
                           Machines Wise report
                        </SideDropDownItemLink>

                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['services-report'])" :href="$page.props.base_url +'/services-report'"  :active="isLinkActive(['services-report'])" >
                            Services Report
                        </SideDropDownItemLink>
                          <!-- <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['machines-summary-report'])" :href="$page.props.base_url +'/machines-summary-report'"  :active="isLinkActive(['machines-summary-report'])" >
                           Machines Summary Report
                        </SideDropDownItemLink> -->

                    </ul>
                </li>
                <li class="mt-0.5 w-full"  v-if="canAny($page.props.granted_permissions,['accounts'])"  >
                    <SideNavDropDown id-name="#maintenance" icon="fa fa-cog" color-class="text-black-500"  :active="isLinkActive(['accounts','items','banks','pfs'])">
                        Maintenance
                    </SideNavDropDown>
                    <ul id="maintenance" class="py-2 space-y-2 bg-primary/5"
                         v-bind:class="{'hidden':!isLinkActive(['accounts','items','banks','pfs'])}"
                    >
                        <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['accounts'])" :href="$page.props.base_url +'/accounts'"  :active="isLinkActive(['accounts'])" >
                            Accounts
                        </SideDropDownItemLink>

                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['items'])" :href="$page.props.base_url +'/items'"  :active="isLinkActive(['items'])" >
                            Items
                        </SideDropDownItemLink>

                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['banks'])" :href="$page.props.base_url +'/banks'"  :active="isLinkActive(['banks'])" >
                            Banks
                        </SideDropDownItemLink>

                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['invoice-term'])" :href="$page.props.base_url +'/invoice-term'"  :active="isLinkActive(['invoice-term'])" >
                            Invoice Term
                        </SideDropDownItemLink>

                        <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['pfs'])" :href="$page.props.base_url +'/pfs'"  :active="isLinkActive(['pfs'])" >
                            PF
                        </SideDropDownItemLink>
                    </ul>
                </li>
                <!-- <li class="mt-0.5 w-full" >
                      <SideNavDropDown id-name="accounts" icon="fa fa-calendar" color-class="text-cyan-500">
                        Items
                    </SideNavDropDown>
                    <ul id="accounts" class="py-2 space-y-2 bg-primary/5 hidden">
                      <SideDropDownItemLink :href="$page.props.base_url +'/cities'"  >
                            Cities
                        </SideDropDownItemLink>
                         <SideDropDownItemLink :href="$page.props.base_url +'/states'" >
                            States
                        </SideDropDownItemLink>
                         <SideDropDownItemLink :href="$page.props.base_url +'/countries'">
                            Countries
                        </SideDropDownItemLink>
                    </ul>
                </li> -->
                <!-- <li class="mt-0.5 w-full">
                    <SideNavLink :href="$page.props.base_url +' /dashboard'" icon="fa fa-credit-card" color-class="text-emerald-500">
                        Billing
                    </SideNavLink>
                </li> -->
                <li class="mt-0.5 w-full" v-if="canAny($page.props.granted_permissions,['users','roles','permissions'])">
                    <SideNavDropDown id-name="#users" icon="fa fa-users" color-class="text-red-500" :active="isLinkActive(['users','roles','permissions'])">
                        Users & Roles
                    </SideNavDropDown>
                    <ul id="users" class="py-2 space-y-2 bg-primary/5"  v-bind:class="{'hidden':!isLinkActive(['users','roles','permissions'])}">
                       <SideDropDownItemLink v-if="canAny($page.props.granted_permissions,['users'])" :href="$page.props.base_url +'/users'"  :active="isLinkActive(['users'])">
                            Users
                        </SideDropDownItemLink>
                         <SideDropDownItemLink  v-if="canAny($page.props.granted_permissions,['roles'])"  :href="$page.props.base_url +'/roles'" :active="isLinkActive(['roles','permissions'])">
                            Roles
                        </SideDropDownItemLink>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>
    <!-- sidebar end  -->
</template>
<style>

</style>
