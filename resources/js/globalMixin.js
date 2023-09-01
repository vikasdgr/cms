//useComp.js

//import ref function to define reactive properties
import {ref} from 'vue';

export default function globalMixin(){
    const base_url = ref (CMS.base_url);

    function isLinkActive(arr) {
        const urls = arr;
        const currentUrl = window.location.href;

        const isCurrentUrlInUrls = urls.some(url => {
          const urlRegExp = new RegExp('(^|\/)' + url.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + '(\/|$)', 'i');
          return urlRegExp.test(currentUrl);
        });

        return isCurrentUrlInUrls;
      }
    function isLinkActive1(arr) {

        const urls = arr;
        const currentUrl = window.location.href

        const urlRegExp = new RegExp(urls.join('|'), 'i')
        const isCurrentUrlInUrls = urls.some(url => urlRegExp.test(currentUrl))

        return isCurrentUrlInUrls;
        let active = false;
        arr.forEach(element => {
            if(route().current(element) ){
                active = true;
            }
        });
        return active;
    }


    function copyProperties(source_obj,target_obj,include_function = 'N') {
        for (let field in source_obj) {
            if(target_obj.hasOwnProperty(field)) {
                if(include_function == 'Y' || typeof(target_obj[field]) != 'function') {
                    target_obj[field] = source_obj[field];
                }
            }
        }
    }

    function refreshComponent(ref,showComp,time=200){
        ref[showComp] =  !ref[showComp] ;
        setTimeout(() => {
            ref[showComp] = !ref[showComp];
        }, time);
    }

    function joinArraytoString(arr,join_term= ', ', add_and = true) {
        const input = arr;
        if(input.length == 0){
            return "";
        }
        else if(input.length == 1){
            return input[0];
        }
        if (add_and) {
            const last = input.pop();

            return  input.join(join_term) + ' and ' + last;
        }
        else {
            return input.join(join_term)
        }

    }

    function snakeCaseToString(key) {   // if input is abc_kbc output will be Abc Kbc
        var str =  key.replace(/_/g, " ");
        str = str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()});
        return str;
    }

    function changeDateFormat(date,format = 'MM-DD-YYYY',desired_format='MMMM DD, YYYY'){
        if(moment(date,format).isValid())
            return moment(date,format).format(desired_format);
        else
            return "";
    }


    function canAny(all_permissions,check_permission){
        return all_permissions.some(permission => check_permission.includes(permission));
    }

    function showPopMessage(msg="",title="",icon="success",timer='1500',showConfirmButton =false ,position='center'){
        if(timer != 0){
            Swal.fire({
                position: position,
                text:msg,
                icon: icon,
                title:title,
                showConfirmButton: showConfirmButton,
                timer: timer,
            });
        }
        else{
            Swal.fire({
                position: position,
                text:msg,
                icon: icon,
                title:title,
                showConfirmButton: showConfirmButton,
            });
        }

    }

    function showConfirmationPopMessage(title='Do you want to save the changes?',callback,showDenyButton=true,showCancelButton=true,confirmButtonText='Yes',denyButtonText="No",allowOutsideClick=true,allowEscapeKey=true){
        Swal.fire({
            title: title,
            showDenyButton: showDenyButton,
            showCancelButton: showCancelButton,
            confirmButtonText: confirmButtonText,
            denyButtonText: denyButtonText,
            allowOutsideClick: allowOutsideClick,
            allowEscapeKey:allowEscapeKey,
            customClass: {
              cancelButton: 'order-1 right-gap',
              confirmButton: 'order-2',
              denyButton: 'order-3',
            }
          }).then((result) => {
            if (result.isConfirmed) {
                callback(true);
            } else if (result.isDenied) {
                callback(false);
            }
          })
    }

    function showUpperFormMessage(title='',msg='',color='green',fa_icon = 'fa-check-circle',auto_close =true,timer=5000,showClose=false){
        var random_number = Math.floor(10000 + Math.random() * 90000);
            var ele =     "<div class='form-msg-ele_"+random_number+ " bg-"+color+"-100 border border-l-4  border-"+color+"-400 text-"+color+"-700 px-4 py-2 rounded relative mb-2' role='alert'>"
                +"<i class='fa "+fa_icon+" text-2xl mr-2'></i> "
                +"<strong class='font-bold mb-1'>"+title +"! </strong>"
                +"<span class='inline-block ml-2 mb-1'> "+msg+"</span>"
                if(showClose)
                +"<i class='fa fa-close absolute top-0 bottom-0 right-0 px-4 py-3 text-xl'></i>"
                // +"<span class='absolute top-0 bottom-0 right-0 px-4 py-3'>"
                // +"<svg class='fill-current h-6 w-6 text-"+color+"-500' role='button' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'><title>Close</title><path d='M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z'/></svg>"
                // +"</span>"
           +"</div>"
        $($(".form")[0]).prepend(ele);
        $(".form-msg-ele_"+random_number).slideDown("slow");
        window.scrollTo(0,0);
        setTimeout(()=>{
            $(".form-msg-ele_"+random_number).fadeOut("slow");
            $(".form-msg-ele_"+random_number).css({ top: '0px' });
            if($('.serverError') && $('.serverError')[0]){
                $('.serverError').remove();
            }
        },timer);

        //fa-exclamation-triangle for error

    }


    //INPUT NUMBERS VALIDATIONS
    function convertInputValueToNumber(event, strict_number=true ){ // strict number true so "" passed is converted to 0
        var inputElement = event.target;
        var inputValue = inputElement.value;
        if(strict_number){
            if(inputValue === null || inputValue === ''){
                inputValue = 0;
            }
        }

        // Convert the input value to an integer
            var intValue = parseFloat(inputValue);            // so that 002 converted to 2

        // Update the input element's value with the integer value
        inputElement.value = intValue;
    }

    function convertInputValueZeroToBlank(event){
        var inputElement = event.target;
        var inputValue = inputElement.value;
        if(inputValue == 0){
            inputElement.value = '';
        }
    }

    function validateNumber(event) {
        let keyCode = event.keyCode;
        let specialKeys = [46];
        if ((keyCode < 48 || keyCode > 57 ) && keyCode != 46) {
            event.preventDefault();
        }
    }



    function getRandomNumber(){
        return Math.floor(Math.random() * 100000)+'A'+Math.floor(Math.random() * 1000000);
    }



    return {
        base_url,
        isLinkActive,
        joinArraytoString,
        refreshComponent,
        copyProperties,
        snakeCaseToString,
        changeDateFormat,
        showUpperFormMessage,
        convertInputValueToNumber,   //Can only be applied on InPUT EVENT directly not through function //neha
        convertInputValueZeroToBlank,  //Can only be applied on InPUT EVENT directly not through function
        validateNumber,
        canAny,
        getRandomNumber
    }
}
