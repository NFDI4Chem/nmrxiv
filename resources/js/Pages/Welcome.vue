
<template>
  <Head title="Welcome" />
  <!--
  This example requires Tailwind CSS v2.0+ 
  
  This example requires some changes to your config:
  
  ```
  // tailwind.config.js
  const colors = require('tailwindcss/colors')
  
  module.exports = {
    // ...
    theme: {
      extend: {
        colors: {
          rose: colors.rose,
        },
      },
    },
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
  ```
-->
  <div class="bg-white">
    <main>
      <!-- Hero section -->
      <div class="pt-8 overflow-hidden sm:pt-12 lg:relative lg:py-48">
        <div
          class="
            mx-auto
            max-w-md
            px-4
            sm:max-w-3xl
            sm:px-6
            lg:px-8
            lg:max-w-7xl
            lg:grid lg:grid-cols-2
            lg:gap-24
          "
        >
          <div>
            <div>
              <jet-application-mark class="block h-12 w-auto" />
            </div>
            <div class="mt-10">
              <div class="mt-6 sm:max-w-xl">
                <h1
                  class="
                    text-4xl
                    font-extrabold
                    text-gray-900
                    tracking-tight
                    sm:text-5xl
                  "
                >
                  nmrXiv
                </h1>
                <p class="mt-6 text-xl text-gray-500">
                  New, highly visible, and consensus-driven NMR data repository and computational platform. The ultimate goal is to accelerate broader coordination and data sharing among natural product (NP) researchers by enabling storage, management, sharing and analysis of NMR data.Test
                </p>
              </div>
              <div class="mt-10">
                <a
                  href="/login"
                  class="
                    rounded-md
                    border border-transparent
                    px-5
                    py-3
                    bg-rose-500
                    text-base
                    font-medium
                    text-white
                    shadow
                    hover:bg-rose-600
                    focus:outline-none
                    focus:ring-2 focus:ring-rose-500 focus:ring-offset-2
                    sm:px-10
                  "
                >
                  Login
                </a>
                <a
                  href="/register"
                  class="
                    rounded-md
                    border border-transparent
                    px-5
                    ml-2
                    py-3
                    bg-white-500
                    text-base
                    font-medium
                    text-dark
                    shadow
                    hover:bg-white-600
                    focus:outline-none
                    focus:ring-2 focus:ring-rose-500 focus:ring-offset-2
                    sm:px-10
                  "
                >
                  Register
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="sm:mx-auto sm:max-w-3xl sm:px-6">
          <div
            class="
              py-12
              sm:relative
              sm:mt-12
              sm:py-16
              lg:absolute
              lg:inset-y-0
              lg:right-0
              lg:w-1/2
            "
          >
            <div class="hidden sm:block">
              <div
                class="
                  absolute
                  inset-y-0
                  left-1/2
                  w-screen
                  bg-gray-50
                  rounded-l-3xl
                  lg:left-80
                  lg:right-0
                  lg:w-full
                "
              ></div>
              <svg
                class="absolute top-8 right-1/2 -mr-3 lg:m-0 lg:left-0"
                width="404"
                height="392"
                fill="none"
                viewBox="0 0 404 392"
              >
                <defs>
                  <pattern
                    id="837c3e70-6c3a-44e6-8854-cc48c737b659"
                    x="0"
                    y="0"
                    width="20"
                    height="20"
                    patternUnits="userSpaceOnUse"
                  >
                    <rect
                      x="0"
                      y="0"
                      width="4"
                      height="4"
                      class="text-gray-200"
                      fill="currentColor"
                    />
                  </pattern>
                </defs>
                <rect
                  width="404"
                  height="392"
                  fill="url(#837c3e70-6c3a-44e6-8854-cc48c737b659)"
                />
              </svg>
            </div>
            <div
              class="
                relative
                pl-4
                -mr-40
                sm:mx-auto
                sm:max-w-3xl
                sm:px-0
                lg:max-w-none
                lg:h-full
                lg:pl-12
              "
            >
              <img
                class="
                  w-full
                  rounded-md
                  shadow-xl
                  ring-1 ring-black ring-opacity-5
                  lg:h-full
                  lg:w-auto
                  lg:max-w-none
                "
                src="/img/website.jpg"
                alt=""
              />
            </div>
          </div>
        </div>
      </div>
      <!-- CTA section -->
      <div class="relative mt-24 sm:mt-32 sm:py-16">
        <div aria-hidden="true" class="hidden sm:block">
          <div
            class="absolute inset-y-0 left-0 w-1/2 bg-gray-50 rounded-r-3xl"
          ></div>
          <svg
            class="absolute top-8 left-1/2 -ml-3"
            width="404"
            height="392"
            fill="none"
            viewBox="0 0 404 392"
          >
            <defs>
              <pattern
                id="8228f071-bcee-4ec8-905a-2a059a2cc4fb"
                x="0"
                y="0"
                width="20"
                height="20"
                patternUnits="userSpaceOnUse"
              >
                <rect
                  x="0"
                  y="0"
                  width="4"
                  height="4"
                  class="text-gray-200"
                  fill="currentColor"
                />
              </pattern>
            </defs>
            <rect
              width="404"
              height="392"
              fill="url(#8228f071-bcee-4ec8-905a-2a059a2cc4fb)"
            />
          </svg>
        </div>
        <div
          class="
            mx-auto
            max-w-md
            px-4
            sm:max-w-3xl
            sm:px-6
            lg:max-w-7xl
            lg:px-8
          "
        >
          <div
            class="
              relative
              rounded-2xl
              px-6
              py-10
              bg-rose-500
              overflow-hidden
              shadow-xl
              sm:px-12
              sm:py-20
            "
          >
            <div
              aria-hidden="true"
              class="absolute inset-0 -mt-72 sm:-mt-32 md:mt-0"
            >
              <svg
                class="absolute inset-0 h-full w-full"
                preserveAspectRatio="xMidYMid slice"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 1463 360"
              >
                <path
                  class="text-rose-400 text-opacity-40"
                  fill="currentColor"
                  d="M-82.673 72l1761.849 472.086-134.327 501.315-1761.85-472.086z"
                />
                <path
                  class="text-rose-600 text-opacity-40"
                  fill="currentColor"
                  d="M-217.088 544.086L1544.761 72l134.327 501.316-1761.849 472.086z"
                />
              </svg>
            </div>
            <div class="relative">
              <div class="sm:text-center">
                <h2
                  class="
                    text-3xl
                    font-extrabold
                    text-white
                    tracking-tight
                    sm:text-4xl
                  "
                >
                  Get notified when we&rsquo;re launching.
                </h2>
              </div>
              <form action="#" class="mt-12 sm:mx-auto sm:max-w-lg sm:flex">
                <div class="min-w-0 flex-1">
                  <label for="cta-email" class="sr-only">Email address</label>
                  <input
                    id="cta-email"
                    type="email"
                    class="
                      block
                      w-full
                      border border-transparent
                      rounded-md
                      px-5
                      py-3
                      text-base text-gray-900
                      placeholder-gray-500
                      shadow-sm
                      focus:outline-none
                      focus:border-transparent
                      focus:ring-2
                      focus:ring-white
                      focus:ring-offset-2
                      focus:ring-offset-rose-500
                    "
                    placeholder="Enter your email"
                  />
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-3">
                  <button
                    type="submit"
                    class="
                      block
                      w-full
                      rounded-md
                      border border-transparent
                      px-5
                      py-3
                      bg-gray-900
                      text-base
                      font-medium
                      text-white
                      shadow
                      hover:bg-black
                      focus:outline-none
                      focus:ring-2
                      focus:ring-white
                      focus:ring-offset-2
                      focus:ring-offset-rose-500
                      sm:px-10
                    "
                  >
                    Notify me
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer section -->
    <footer class="mt-24 bg-gray-900 sm:mt-12">
      <div
        class="
          mx-auto
          max-w-md
          py-12
          px-4
          overflow-hidden
          sm:max-w-3xl
          sm:px-6
          lg:max-w-7xl
          lg:px-8
        "
      >
        <nav
          class="-mx-5 -my-2 flex flex-wrap justify-center"
          aria-label="Footer"
        >
          <div class="px-5 py-2">
            <a href="#" class="text-base text-gray-400 hover:text-gray-300">
              About
            </a>
          </div>

          <div class="px-5 py-2">
            <a href="#" class="text-base text-gray-400 hover:text-gray-300">
              Docs
            </a>
          </div>

          <div class="px-5 py-2">
            <a href="#" class="text-base text-gray-400 hover:text-gray-300">
              Privacy
            </a>
          </div>

          <div class="px-5 py-2">
            <a href="#" class="text-base text-gray-400 hover:text-gray-300">
              Terms
            </a>
          </div>

          <div class="px-5 py-2">
            <a href="#" class="text-base text-gray-400 hover:text-gray-300">
              Cookies
            </a>
          </div>
        </nav>
        <p class="mt-8 text-center text-base text-gray-400">
          &copy; 2021 nmrXiv. All rights reserved.
        </p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
</style>

<script>
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetApplicationMark from '@/Jetstream/ApplicationMark.vue'

export default {
  components: {
    Head,
    Link,
    JetApplicationMark
  },

  props: {
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
  },
};
</script>
