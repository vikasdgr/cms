<template>
</template>

<script>
export default {
  props: {
    department: Object,
    machineTypes: Array,
    getCount: Function
  },
  computed: {
    rowspan() {
      return this.department.areas.length * 2;
    }
  }
};
</script>
