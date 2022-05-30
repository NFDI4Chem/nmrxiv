<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <div
    class="col-span-1 bg-white rounded-lg shadow divide-gray-200 transition ease-in-out delay-150 hover:-translate-y-1 hover:scale-100 duration-300"
  >
    <div class="w-full h-30 flex items-center justify-between p-6 space-x-6">
      <div class="flex-1">
        <div style="min-height: 50px;" class="flex place-content-between space-x-3">
          <h3 class="text-gray-900 text-l font-semibold line-clamp-2">
            {{ name }}
          </h3>
        </div>
        <div style="min-height: 100px;">
          <p v-if="description" class="mt-1 text-gray-500 text-sm line-clamp-4">
            {{ description }}
          </p>
          <p v-else class="mt-1 text-gray-400 text-sm italic line-clamp-4">
            No description provided.
          </p>
        </div>
      </div>
    </div>
    <!-- TODO: Co-authors preview -->
    <!-- <div>
        <div class="flex items-center">
            <img
                class="w-10 h-10 rounded-full mr-4 ml-4 mb-4"
                src="https://images.unsplash.com/photo-1520785643438-5bf77931f493?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=8&amp;w=256&amp;h=256&amp;q=80"
                alt="Avatar of Jonathan Reinink"
            />
            <div class="mr-4 mb-4 ">
                <p class="text-sm font-semibold text-gray-500">Eduardo Benz</p> 
                <p class="text-sm font-medium text-gray-500">{{ created_at }}</p>
            </div>
        </div>
    </div> -->
    <div>
      <div class="-mt-px border-t flex divide-x divide-gray-200">
        <div class="w-0 flex-1 flex">
          <a
            v-if="isPublic"
            class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500"
          >
            <LockOpenIcon class="w-3 h-3 text-green-600" />
            <span class="ml-3">Public</span>
          </a>
          <a
            v-else
            class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500"
          >
            <LockClosedIcon class="w-3 h-3 text-teal-500" />
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
  </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/solid";
import { LockOpenIcon } from "@heroicons/vue/solid";
import { PencilIcon } from "@heroicons/vue/solid";
import { MailIcon, PhoneIcon } from "@heroicons/vue/solid";

export default {
  components: {
    LockClosedIcon,
    LockOpenIcon,
    MailIcon,
    PencilIcon,
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
