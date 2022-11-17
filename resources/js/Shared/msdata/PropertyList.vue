<template>
  <div v-for="(val,key) in data" class="grid grid-cols-3">
    <div class="text-lg font-bold text-gray-900">{{key}}:</div>
    <div class="text-lg text-gray-900 col-span-2">{{val}}</div>
  </div>
</template>
<script>
export default {
  name: 'PropertyList',
  props: {
    data: {}
  }
}
</script>
