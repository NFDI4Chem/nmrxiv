<template>
  <div v-if='show' class="relative bg-indigo-200 border-b border-gray-800 animate-pulse">
    <div class="max-w-7xl mx-auto py-1 px-1 sm:px-4 lg:px-6">
        <p class="text-sm text-dark font-semibold">
          <span class="hidden md:inline sm:text-justify " v-html = "message"></span>
        </p>
      <div class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pt-1 sm:pr-2 sm:items-start">
        <button @click="show=false" type="button" class="flex p-1 rounded-md focus:outline-none hover:bg-indigo-300 focus:ring-2 focus:ring-white">
          <span class="sr-only">Dismiss</span>
          <XIcon class="h-3 w-3 text-dark" aria-hidden="true" />
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