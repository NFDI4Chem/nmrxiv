<template>
  <div v-if="study" class="flex flex-col rounded-lg shadow-lg transition ease-in-out delay-150 hover:-translate-y-1 hover:scale-100 duration-300 overflow-hidden">
    <div style="min-height: 140px;" class="flex-1 bg-white p-3 flex flex-col justify-between">
      <div class="flex-1">
        <a :href="'/studys/' + study.slug" class="block">
          <p class="text-lg font-semibold text-gray-900 line-clamp-2">
            {{ study.name }}
          </p>
          <p class="mt-2 text-sm text-gray-500 line-clamp-4">
            {{ study.description }}
          </p>
        </a>
      </div>
    </div>
    <div class="-mt-px border-t flex divide-x divide-gray-200">
      <div class="w-0 flex-1 flex">
        <a
          v-if="study.is_public"
          class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500"
        >
          <LockOpenIcon class="w-3 h-3 text-green-600" />
          <span class="ml-3">Public</span>
        </a>
        <a
          v-else
          class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500"
        >
          <LockClosedIcon class="w-3 h-3 text-teal-600" />
          <span class="ml-1">Private</span>
        </a>
      </div>
      <div class="-ml-px w-0 flex-1 flex">
        <a
          class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500"
        >
          <PencilIcon class="w-3 h-3 text-green-600" />
          <span class="ml-3">Edit</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/solid";
import { LockOpenIcon } from "@heroicons/vue/solid";
import { PencilIcon } from "@heroicons/vue/solid";
import { MailIcon } from "@heroicons/vue/solid";

export default {
  components: {
    LockClosedIcon,
    LockOpenIcon,
    MailIcon,
    PencilIcon,
  },
  setup() {},
  props: ["study"],
  data() {
    return {};
  },
  methods: {},
};
</script>
