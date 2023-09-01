<script setup>
import { Head, Link } from '@inertiajs/vue3';
 import {    ref,  computed,  onMounted,    reactive} from 'vue';
 import globalMixin from '../globalMixin';

const { base_url, canAny,refreshComponent } = globalMixin();
    const data = reactive({ data:{} ,table_data:[]});

onMounted(() => {
    getMachineData();
});


    const getMachineData = () =>{
        axios.get(base_url.value+'/dashboard-data')
        .then(function(response){
            if(response.data.data){
                data['data'] = response.data.data;
                data['table_data'] = response.data.table_data;
                console.log(data.data);
            }
        })
        .catch(function(error){
        });
    }

    const machineTypeBrands =(machineType) => {
       return [...new Set(
        machineType.models.map(model => model.brand)
      )];
    }

    function getModelByBrandAndType(brandId, machineTypeId) {
        for (const machineType of data.data.machineTypes) {
            for (const model of machineType.models) {
            if (
                model.brand.id === brandId &&
                model.machine_type_id === machineTypeId
            ) {
                return model ? model.model_no:'';
            }
            }
        }
        return null; // Model not found
    }


    const getModelName = (type,brand_id) =>{
        const model = data.data.machineTypes.flatMap(machine_type => machine_type.models)
                  .find(model => model.brand.id == brand_id && machine_type.id === type);
    }

</script>

<template>
    <Head title="Housekeeping Machines Summary" />

    <div class="w-full px-6 pb-0 mx-auto mb-5">
        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="flex-auto p-4">
                        <div class="flex flex-row -mx-3">
                            <div class="flex-none w-2/3 max-w-full px-3">
                                <div>
                                    <p class="mb-0 font-sans font-semibold leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Total Machines</p>
                                    <h5 class="2xl:text-2xl xl:text-xl lg:text-xl text-xl mb-2 font-bold dark:text-white" v-text="data.data['no_of_machines']"></h5>
                                </div>
                            </div>
                            <div class="px-3 text-right basis-1/3">
                                <div class="w-12 h-12 text-center rounded-full bg-gradient-to-tl from-blue-500 to-violet-500 flex items-center justify-center ml-auto">
                                    <i class="fa fa-robot text-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="flex-auto p-4">
                        <div class="flex flex-row -mx-3">
                            <div class="flex-none w-2/3 max-w-full px-3">
                                <div>
                                    <p class="mb-0 font-sans font-semibold leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Generated Cases</p>
                                    <h5 class="2xl:text-2xl xl:text-xl lg:text-xl text-xl mb-2 font-bold dark:text-white"  v-text="data['data']['no_of_cases']"></h5>
                                </div>
                            </div>
                            <div class="px-3 text-right basis-1/3">
                                <div class="w-12 h-12 text-center rounded-full bg-gradient-to-tl from-red-600 to-orange-600 flex items-center justify-center ml-auto">
                                    <i class="fa fa-bug text-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="flex-auto p-4">
                        <div class="flex flex-row -mx-3">
                            <div class="flex-none w-2/3 max-w-full px-3">
                                <div>
                                    <p class="mb-0 font-sans font-semibold leading-normal uppercase dark:text-white dark:opacity-60 text-sm">No. Of Services</p>
                                    <h5 class="2xl:text-2xl xl:text-xl lg:text-xl text-xl mb-2 font-bold dark:text-white" v-text="data['data']['no_of_services']"></h5>
                                </div>
                            </div>
                            <div class="px-3 text-right basis-1/3">
                                <div class="w-12 h-12 text-center rounded-full bg-gradient-to-tl from-emerald-500 to-teal-400 flex items-center justify-center ml-auto">
                                    <i class="fa-solid fa-hand-sparkles > text-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
                <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="flex-auto p-4">
                        <div class="flex flex-row -mx-3">
                            <div class="flex-none w-2/3 max-w-full px-3">
                                <div>
                                    <p class="mb-0 font-sans font-semibold leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Pending Cases</p>
                                    <h5 class="2xl:text-2xl xl:text-xl lg:text-xl text-xl mb-2 font-bold dark:text-white"  v-text="data['data']['no_pending']"></h5>
                                </div>
                            </div>
                            <div class="px-3 text-right basis-1/3">
                                <div class="w-12 h-12 text-center rounded-full bg-gradient-to-tl from-orange-500 to-yellow-500 flex items-center justify-center ml-auto">
                                    <i class="fa fa-circle-pause text-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h4 class="mb-4 text-3xl font-extrabold text-center text-gray-900 dark:text-white md:text-3xl lg:text-3xl"><span class="text-transparent bg-clip-text bg-gradient-to-r to-emerald-600 text-center from-sky-400">Machines Summary</span></h4>

      <div class="overflow-x-auto relative shadow-md sm:rounded-lg w-full px-6">
        <div class="relative flex flex-col min-w-0 bg-white bg-clip-border border border-solid border-gray-300 rounded break-words">
            <div class="p-5 flex-auto">
                <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <tbody>
                            <tr v-for="(t_data,index) in data.table_data" :key="index" class="bg-white border dark:bg-gray-800 dark:border-gray-700">
                                <td :class="row_data.type=='caption' || row_data.type=='machine_type' || row_data.type=='brand' || row_data.type=='model' || row_data.type == 'count'? 'px-3 py-2 border whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-200 text-center':'px-3 py-2 border whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200'"  v-for="(row_data,ind) in t_data" :key="'r'+ind" :colspan="row_data.span" :rowspan="row_data.rowspan" v-text="row_data['name']">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</template>

<style>
  /* <td v-for="area in department.areas" :key="area.id">{{ area.name }}</td>
          <td v-for="machineType in data['data']['machineTypes']" :key="machineType.id">
            <td v-for="brand in machineTypeBrands(machineType)" :key="brand.id">
              <td v-for="model in brand.models" :key="model.id">
                {{ getCount(department.id, area.id, machineType.id, brand.id, model.id) }}
              </td>
            </td>
          </td> */
</style>
