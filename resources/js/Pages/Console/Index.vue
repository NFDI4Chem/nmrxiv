<template>
  <app-layout title="Console">
    <template #header>
      <div>
        <div
          class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
        >
          Console
        </div>
      </div>
      <div>&nbsp;</div>
    </template>
    <div class="py-12 px-10">
      <div>
        <div
          class="rounded-lg bg-gray-200 overflow-hidden shadow divide-y divide-gray-200 sm:divide-y-0 sm:grid sm:grid-cols-2 sm:gap-px"
        >
          <div
            class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
          >
            <div>
              <span
                class="rounded-lg inline-flex p-3 bg-sky-50 text-sky-700 ring-4 ring-white"
              >
                <svg
                  class="h-6 w-6"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  aria-hidden="true"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>
              </span>
            </div>
            <div class="mt-8">
              <h3 class="text-lg font-medium">
                <Link :href="route('users')" class="focus:outline-none">
                  <span class="absolute inset-0" aria-hidden="true"></span>
                  Users & Permissions
                </Link>
              </h3>
              <p class="mt-2 text-sm text-gray-500">
                Allows managing users including edit/view their permissions.
              </p>
            </div>
            <span
              class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400"
              aria-hidden="true"
            >
              <svg
                class="h-6 w-6"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"
                />
              </svg>
            </span>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    Link,
    AppLayout,
  }
};
</script>
