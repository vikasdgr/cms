<script setup>
   import {   ref,  computed, onMounted, onBeforeUnmount,  reactive} from 'vue';
    import globalMixin from '../../../globalMixin';
    const { base_url} = globalMixin();
    const emit = defineEmits(['updateMachine','update:modelValue']);

    const props = defineProps(
        {
            modelValue: [String,Number],
            index: {default:-1 ,type: [String,Number]},
            initials: {default: () => [],type: Array},
            selected: {default: () => [],type: Array},
            url:{default:'machines/filtered',type: String},
            getIndex: {default:false , type: Boolean},
            disabled: {default:false , type: Boolean},
            customClass: {default:'selectItem',type:String},
            focus: {default:false, type: Boolean},
            enableNew: {default:false, type:Boolean},
            placeholder:{default:'Select Machine',type:String},
            type:{default:'all',type:String},
            multiple:{default:false,type:Boolean},
            allowClear:{default:true,type:Boolean},
            error:{default:false}
        }
    );
    const selected_data = ref(0);

    const classes = computed(() => {
        return props.error ? 'machine_'+props.index+' border-red-400 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full'
                            :'machine_'+props.index+' border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full';
    });

    onMounted(() => {
        intialiseSelect();
    });


    onBeforeUnmount(() => {
        $('.machine_'+props.index).select2('destroy');
        // console.log( $('.machine_'+props.index));
    });
    const intialiseSelect = ()=>{
        var itemComponent = $('.machine_'+props.index);
        var item = itemComponent.select2({
            dropdownAutoWidth : true,
            placeholder: props.placeholder,
            allowClear:props.allowClear,
            width: '100%',
            data:props.initials,
            dropdownParent:$('body'),
            ajax: {
                method:'POST',
                url: function() {
                    return base_url.value+'/'+props.url;
                },
                delay: 250,
                dataType: 'json',
                cache: true,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        type : props.type,
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data,params) {
                    params.page = params.page || 1;
                    // Tranforms the top-level key of the response object from 'salesmen' to 'results'
                    data.results.forEach(function(ele){
                        ele.text = ele.serial_no;
                    });
                    // this is a mondatory object that's why i added here
                    data.pagination = {
                        "more": (params.page * 30) < data.count_filtered
                    };
                    return data;
                }
            },
            templateResult: setName,
            templateSelection: selection,
        })
        .on('change', function () {
              console.log("select2 change ")
            var item = $('.machine_'+ props.index).val();
            if(props.multiple == false){
               emit('update:modelValue', item);
            }
            else if(props.multiple == true){
               emit('updateMachine',item,props.index);
            }
        })
        .on('select2:select', function (e) {
            console.log("select2 Select ")
            var item = $('.machine_'+ props.index).val();
            selected_data.value = e.params.data;
            if( selected_data.value != {}){
               emit('updateMachine',item,props.index,selected_data.value);
            }
        })
        .on('select2:clear',function(e){
            var item = $('.machine_'+ props.index).val();
            if(props.multiple == false){
                var val = item ? item :0;
               emit('input', val);
            }
        });

        if(typeof(props.index) !="undefined"){
            $('.machine_'+props.index).val(props.selected).change();
        }
    }

    const selection = (details) =>{
        if (!details.id) { return details.text; }
        var $details = $('<div class="flex flex-wrap -mx-3"><div class="w-full md:w-1/1 px-3 md:mb-0">' + details.text + '</div></div>');
        return $details;
    }

    const  setName= (details) =>{
        if (!details.id) { return details.text; }
        var $details = $('<div class="flex flex-wrap -mx-3"><div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">' + details.text + '</div><div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">' + details.machine_model.model_no + '</div><div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">' + details.department.name + '</div></div>');
        return $details;
    }

</script>
<template>
    <select
        :multiple="multiple"
        :disabled="disabled"
        :class="classes"
        :value="modelValue"
        :key="props.index"
        @input="$emit('update:modelValue', $event.target.value)"
    >
    </select>

</template>
