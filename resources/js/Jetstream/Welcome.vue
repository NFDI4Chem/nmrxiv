<template>
  <div>
    <div class="px-12 py-8 mx-auto max-w-4xl">
      <div>
        <div class="flex items-baseline justify-between">
          <div>
            <h2 class="text-lg">Projects</h2>
            <div class="mt-2 text-sm text-gray-700">
              <div class="max-w-2xl">
                Each team may house a variety of projects. 
                Projects can be versioned, command invocations, and
                metrics.
              </div>
            </div>
          </div>
          <div class="flex-shrink-0 ml-4">
            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              New Project
            </button>
            <!---->
            <!---->
          </div>
        </div>
        <div class="mt-4">
          <div class="px-6 py-4 bg-white shadow-md rounded-lg">
            <div class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                class="h-6 w-6"
              >
                <path
                  d="M3 6l9 4v12l-9-4V6zm14-3v2c0 1.1-2.24 2-5 2s-5-.9-5-2V3c0 1.1 2.24 2 5 2s5-.9 5-2z"
                  class="fill-current text-gray-400"
                ></path>
                <polygon
                  points="21 6 12 10 12 22 21 18"
                  class="fill-current text-gray-600"
                ></polygon>
              </svg>
              <div
                class="
                  ml-3
                  font-semibold
                  text-sm text-gray-600
                  uppercase
                  tracking-wider
                "
              >
                Create Your First Project
              </div>
            </div>
            <div class="mt-3 max-w-2xl text-sm text-gray-700">
              nmrXiv is organized around projects. Projects can contain as many
              studies as you wish and each study receives its very own URL. To
              learn more, check out our documentation.
            </div>
          </div>
        </div>
      </div>
      <!---->
    </div>
  </div>
</template>

<script>
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";

export default {
  components: {
    JetApplicationLogo,
  },
};
</script>
