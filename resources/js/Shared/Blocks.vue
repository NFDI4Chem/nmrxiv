<template>
  <div class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
    <div>
      <span class="rounded-lg inline-flex p-3 bg-sky-50 text-sky-700 ring-4 ring-white">
        <!-- Icon component getting conditionally rendered on the basis of icon name passed -->
        <icon v-if="name === 'users'" name="users" />
        <icon v-if="name === 'announcements'" name="announcements" />
      </span>
    </div>
    <!-- Heading and Description section -->
    <div class="mt-8">
      <h3 class="text-lg font-medium">
        <Link :href="route(path)" class="focus:outline-none">
        <span class="absolute inset-0" aria-hidden="true"></span>
        {{ heading }}
        </Link>
      </h3>
      <p class="mt-2 text-sm text-gray-500">
        {{ description }}
      </p>
    </div>
    <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
      <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
        <path
          d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" />
      </svg>
    </span>
  </div>
</template>

<script>
/**
 * Block component
 * 
 * @param 
 *  heading
 *  description
 *  name
 *  path
 */
  import AppLayout from "@/Layouts/AppLayout.vue";
  import { Link } from "@inertiajs/inertia-vue3";
  import Icon from "@/Shared/Icon.vue";

  export default {
    components: {
      Link,
      AppLayout,
      Icon,
    },
    props:{
      heading: String,
      description: String,
      name: String,
      path: String

    }
  };
</script>
