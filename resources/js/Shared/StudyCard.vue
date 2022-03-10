<template>
  <div
    class="
      h-full
      w-full
      lg:max-w-full lg:flex
      border-r border-b border-l border-l
      lg:border-l lg:border-t lg:border-indigo-400
      bg-white
      rounded-lg
      lg:rounded-b lg:rounded-r
      p-4
      flex flex-col
      justify-between
      leading-normal
      transition
      ease-in-out
      delay-150
      hover:-translate-y-1 hover:scale-100 hover:bg-indigo-100
      duration-300
    "
  >
    <div class="mb-8">
      <p v-if="isPublic" class="text-sm text-gray-600 flex items-center">
        <LockOpenIcon class="w-3 h-3 text-green-600" />
        <span class="ml-2 text-indigo-700">Public</span>
      </p>
      <p v-else class="text-sm text-gray-600 flex items-center">
        <LockClosedIcon class="w-3 h-3" />
        <span class="ml-2 text-indigo-700">Private</span>
      </p>
      <div class="text-gray-900 font-bold text-xl mb-2">{{ name }}</div>
      <p class="text-gray-700 text-base">{{ description }}</p>
    </div>
    <div class="flex items-center">
      <div class="text-sm">
        <p class="text-indigo-400">Created on</p>
        <p class="text-gray-700">{{ created_at }}</p>
        <p class="text-gray-700 leading-none">{{ created_by }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/solid";
import { LockOpenIcon } from "@heroicons/vue/solid";

export default {
  components: {
    LockClosedIcon,
    LockOpenIcon,
  },
  setup() {},
  props: {
    isPublic: Boolean,
    name: String,
    description: String,
    created_by: String,
    created_at: Date,
  },
  data() {
    return {};
  },
  methods: {},
};
</script>