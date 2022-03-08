<template>
  <div v-if='show' class="relative bg-indigo-200 border-b border-gray-800">
    <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
      <div class="pr-16 sm:text-center sm:px-16">
        <p class="text-sm text-dark">
          <span class="hidden md:inline" v-html = "message"></span>
        </p>
      </div>
      <div class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pt-1 sm:pr-2 sm:items-start">
        <button @click="show=false" type="button" class="flex p-2 rounded-md focus:outline-none hover:bg-indigo-300 focus:ring-2 focus:ring-white">
          <span class="sr-only">Dismiss</span>
          <XIcon class="h-4 w-4 text-dark" aria-hidden="true" />
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { XIcon } from '@heroicons/vue/outline'

export default {
  components: {
    XIcon,
  },
  props:{
      message: String,
  },
  data(){
      return{
          show: true,
      }
  }
}
</script>