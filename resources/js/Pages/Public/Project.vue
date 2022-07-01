<template>
  <app-layout :title="project.name">
    <template #header>
      <div class="bg-white border-b">
        <div class="px-12">
          <div class="flex flex-nowrap justify-between py-6">
            <div>
              <div>
                <img
                  class="h-32 w-full object-cover lg:h-48"
                  src="https://images.unsplash.com/photo-1444628838545-ac4016a5418a?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
                  alt=""
                />
              </div>
              <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="-mt-12 sm:-mt-16 sm:flex sm:items-end sm:space-x-5">
                  <div class="flex">
                    <img
                      class="h-24 w-24 rounded-full ring-4 ring-white sm:h-32 sm:w-32"
                      src="https://images.unsplash.com/photo-1463453091185-61582044d556?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80"
                      alt=""
                    />
                  </div>
                  <div
                    class="mt-6 sm:flex-1 sm:min-w-0 sm:flex sm:items-center sm:justify-end sm:space-x-6 sm:pb-1"
                  >
                    <div class="sm:hidden 2xl:block mt-6 min-w-0 flex-1">
                      <h1 class="text-2xl font-bold text-gray-900 truncate">
                        Ricardo Cooper
                      </h1>
                    </div>
                    <div
                      class="mt-6 flex flex-col justify-stretch space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4"
                    >
                      <button
                        type="button"
                        class="inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500"
                      >
                        <!-- Heroicon name: solid/mail -->
                        <svg
                          class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                          aria-hidden="true"
                        >
                          <path
                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                          />
                          <path
                            d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                          />
                        </svg>
                        <span>Message</span>
                      </button>
                      <button
                        type="button"
                        class="inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500"
                      >
                        <!-- Heroicon name: solid/phone -->
                        <svg
                          class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                          aria-hidden="true"
                        >
                          <path
                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                          />
                        </svg>
                        <span>Call</span>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="hidden sm:block 2xl:hidden mt-6 min-w-0 flex-1">
                  <h1 class="text-2xl font-bold text-gray-900 truncate">
                    Ricardo Cooper
                  </h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <div>
      <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none xl:order-last">
        <article>
          <!-- Profile header -->

          <!-- Tabs -->
          <div class="mt-6 sm:mt-2 2xl:mt-5">
            <div class="border-b border-gray-200">
              <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                  <!-- Current: "border-pink-500 text-gray-900", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
                  <a
                    href="#"
                    class="border-pink-500 text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    aria-current="page"
                  >
                    Profile
                  </a>

                  <a
                    href="#"
                    class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                  >
                    Calendar
                  </a>

                  <a
                    href="#"
                    class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                  >
                    Recognition
                  </a>
                </nav>
              </div>
            </div>
          </div>

          <!-- Description list -->
          <div class="mt-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                <dd class="mt-1 text-sm text-gray-900">(555) 123-4567</dd>
              </div>

              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900">ricardocooper@example.com</dd>
              </div>

              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Title</dt>
                <dd class="mt-1 text-sm text-gray-900">Senior Front-End Developer</dd>
              </div>

              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Team</dt>
                <dd class="mt-1 text-sm text-gray-900">Product Development</dd>
              </div>

              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Location</dt>
                <dd class="mt-1 text-sm text-gray-900">San Francisco</dd>
              </div>

              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Sits</dt>
                <dd class="mt-1 text-sm text-gray-900">Oasis, 4th floor</dd>
              </div>

              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Salary</dt>
                <dd class="mt-1 text-sm text-gray-900">$145,000</dd>
              </div>

              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Birthday</dt>
                <dd class="mt-1 text-sm text-gray-900">June 8, 1990</dd>
              </div>

              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">About</dt>
                <dd class="mt-1 max-w-prose text-sm text-gray-900 space-y-5">
                  <p>
                    Tincidunt quam neque in cursus viverra orci, dapibus nec tristique.
                    Nullam ut sit dolor consectetur urna, dui cras nec sed. Cursus risus
                    congue arcu aenean posuere aliquam.
                  </p>
                  <p>
                    Et vivamus lorem pulvinar nascetur non. Pulvinar a sed platea rhoncus
                    ac mauris amet. Urna, sem pretium sit pretium urna, senectus vitae.
                    Scelerisque fermentum, cursus felis dui suspendisse velit pharetra.
                    Augue et duis cursus maecenas eget quam lectus. Accumsan vitae
                    nascetur pharetra rhoncus praesent dictum risus suspendisse.
                  </p>
                </dd>
              </div>
            </dl>
          </div>

          <!-- Team member list -->
          <div class="mt-8 max-w-5xl mx-auto px-4 pb-12 sm:px-6 lg:px-8">
            <h2 class="text-sm font-medium text-gray-500">Team members</h2>
            <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div
                class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
              >
                <div class="flex-shrink-0">
                  <img
                    class="h-10 w-10 rounded-full"
                    src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt=""
                  />
                </div>
                <div class="flex-1 min-w-0">
                  <a href="#" class="focus:outline-none">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Leslie Alexander</p>
                    <p class="text-sm text-gray-500 truncate">Co-Founder / CEO</p>
                  </a>
                </div>
              </div>

              <div
                class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
              >
                <div class="flex-shrink-0">
                  <img
                    class="h-10 w-10 rounded-full"
                    src="https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt=""
                  />
                </div>
                <div class="flex-1 min-w-0">
                  <a href="#" class="focus:outline-none">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Michael Foster</p>
                    <p class="text-sm text-gray-500 truncate">Co-Founder / CTO</p>
                  </a>
                </div>
              </div>

              <div
                class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
              >
                <div class="flex-shrink-0">
                  <img
                    class="h-10 w-10 rounded-full"
                    src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt=""
                  />
                </div>
                <div class="flex-1 min-w-0">
                  <a href="#" class="focus:outline-none">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Dries Vincent</p>
                    <p class="text-sm text-gray-500 truncate">
                      Manager, Business Relations
                    </p>
                  </a>
                </div>
              </div>

              <div
                class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
              >
                <div class="flex-shrink-0">
                  <img
                    class="h-10 w-10 rounded-full"
                    src="https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt=""
                  />
                </div>
                <div class="flex-1 min-w-0">
                  <a href="#" class="focus:outline-none">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">Lindsay Walton</p>
                    <p class="text-sm text-gray-500 truncate">Front-end Developer</p>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </article>
      </main>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
  components: {
    AppLayout,
  },
  props: ["project"],
};
</script>
