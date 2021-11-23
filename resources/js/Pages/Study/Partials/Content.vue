<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <section class="mx-2 mt-4">
    <div class="bg-white shadow sm:rounded-lg">
      <div class="py-8 xl:py-10">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 xl:grid xl:grid-cols-3">
          <div class="xl:col-span-2 xl:pr-8 xl:border-r xl:border-gray-200">
            <div>
              <div>
                <h2>Description</h2>
                <div class="prose max-w-none">
                  <span v-html="md(study.description)"></span>
                </div>
              </div>
            </div>
            <section aria-labelledby="activity-title" class="mt-8 xl:mt-10">
              <div>
                <div class="divide-y divide-gray-200">
                  <div class="pb-4">
                    <h2 id="activity-title" class="text-lg font-medium text-gray-900">
                      Comments
                    </h2>
                  </div>
                  <div class="pt-6">
                    <div class="flow-root">
                      <ul role="list" class="-mb-8">
                        <li>
                          <div class="relative pb-8">
                            <span
                              class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                              aria-hidden="true"
                            ></span>
                            <div class="relative flex items-start space-x-3">
                              <div class="relative">
                                <img
                                  class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                                  src="https://images.unsplash.com/photo-1520785643438-5bf77931f493?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=8&amp;w=256&amp;h=256&amp;q=80"
                                  alt=""
                                />
                                <span
                                  class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px"
                                >
                                  <svg
                                    class="h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/chat-alt"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                </span>
                              </div>
                              <div class="min-w-0 flex-1">
                                <div>
                                  <div class="text-sm">
                                    <a href="#" class="font-medium text-gray-900"
                                      >Eduardo Benz</a
                                    >
                                  </div>
                                  <p class="mt-0.5 text-sm text-gray-500">
                                    Commented 6d ago
                                  </p>
                                </div>
                                <div class="mt-2 text-sm text-gray-700">
                                  <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing
                                    elit. Tincidunt nunc ipsum tempor purus vitae id.
                                    Morbi in vestibulum nec varius. Et diam cursus quis
                                    sed purus nam.
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </li>
                        <li>
                          <div class="relative pb-8">
                            <div class="relative flex items-start space-x-3">
                              <div class="relative">
                                <img
                                  class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                                  src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=8&amp;w=256&amp;h=256&amp;q=80"
                                  alt=""
                                />

                                <span
                                  class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px"
                                >
                                  <svg
                                    class="h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/chat-alt"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                </span>
                              </div>
                              <div class="min-w-0 flex-1">
                                <div>
                                  <div class="text-sm">
                                    <a href="#" class="font-medium text-gray-900"
                                      >Jason Meyers</a
                                    >
                                  </div>
                                  <p class="mt-0.5 text-sm text-gray-500">
                                    Commented 2h ago
                                  </p>
                                </div>
                                <div class="mt-2 text-sm text-gray-700">
                                  <p>
                                    Lorem ipsum dolor sit amet, consectetur adipiscing
                                    elit. Tincidunt nunc ipsum tempor purus vitae id.
                                    Morbi in vestibulum nec varius. Et diam cursus quis
                                    sed purus nam. Scelerisque amet elit non sit ut
                                    tincidunt condimentum. Nisl ultrices eu venenatis
                                    diam.
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                    <div class="mt-6">
                      <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                          <div class="relative">
                            <img
                              class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                              src="https://images.unsplash.com/photo-1517365830460-955ce3ccd263?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=8&amp;w=256&amp;h=256&amp;q=80"
                              alt=""
                            />

                            <span
                              class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px"
                            >
                              <svg
                                class="h-5 w-5 text-gray-400"
                                x-description="Heroicon name: solid/chat-alt"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                  clip-rule="evenodd"
                                ></path>
                              </svg>
                            </span>
                          </div>
                        </div>
                        <div class="min-w-0 flex-1">
                          <form action="#">
                            <div>
                              <label for="comment" class="sr-only">Comment</label>
                              <textarea
                                id="comment"
                                name="comment"
                                rows="3"
                                class="shadow-sm block w-full focus:ring-gray-900 focus:border-gray-900 sm:text-sm border border-gray-300 rounded-md"
                                placeholder="Leave a comment"
                              ></textarea>
                            </div>
                            <div class="mt-6 flex items-center justify-end space-x-4">
                              <button
                                type="button"
                                class="inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                              >
                                <svg
                                  class="-ml-1 mr-2 h-5 w-5 text-green-500"
                                  x-description="Heroicon name: solid/check-circle"
                                  xmlns="http://www.w3.org/2000/svg"
                                  viewBox="0 0 20 20"
                                  fill="currentColor"
                                  aria-hidden="true"
                                >
                                  <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                  ></path>
                                </svg>
                                <span>Close issue</span>
                              </button>
                              <button
                                type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                              >
                                Comment
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
          <aside class="hidden xl:block xl:pl-8">
            <h2 class="sr-only">Details</h2>
            <div class="space-y-5">
              <div class="flex items-center space-x-2">
                <svg
                  class="h-5 w-5 text-gray-400"
                  x-description="Heroicon name: solid/chat-alt"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
                <span class="text-gray-900 text-sm font-medium">2 comments</span>
              </div>
              <div class="flex items-center space-x-2">
                <svg
                  class="h-5 w-5 text-gray-400"
                  x-description="Heroicon name: solid/calendar"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
                <span class="text-gray-900 text-sm font-medium"
                  >Created on {{ formatDate(study.updated_at) }}</span
                >
              </div>
            </div>
            <div class="mt-6 border-t border-gray-200 py-6 space-y-8">
              <div>
                <h2 class="text-sm font-medium text-gray-500">Authors</h2>
                <ul role="list" class="mt-3 space-y-3">
                  <li class="flex justify-start">
                    <a href="#" class="flex items-center space-x-3">
                      <div class="flex-shrink-0">
                        <img
                          class="h-5 w-5 rounded-full"
                          src="https://images.unsplash.com/photo-1520785643438-5bf77931f493?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=8&amp;w=256&amp;h=256&amp;q=80"
                          alt=""
                        />
                      </div>
                      <div class="text-sm font-medium text-gray-900">Eduardo Benz</div>
                    </a>
                  </li>
                </ul>
              </div>
              <div>
                <h2 class="text-sm font-medium text-gray-500">Species</h2>
                <ul role="list" class="mt-2 leading-8">
                  <li class="inline">
                    <a
                      href="#"
                      class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                    >
                      <div
                        class="absolute flex-shrink-0 flex items-center justify-center"
                      >
                        <span
                          class="h-1.5 w-1.5 rounded-full bg-rose-500"
                          aria-hidden="true"
                        ></span>
                      </div>
                      <div class="ml-3.5 text-sm font-medium text-gray-900">
                        Homo Sapiens
                      </div>
                    </a>
                    <!-- space -->
                  </li>
                  <li class="inline">
                    <a
                      href="#"
                      class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                    >
                      <div
                        class="absolute flex-shrink-0 flex items-center justify-center"
                      >
                        <span
                          class="h-1.5 w-1.5 rounded-full bg-indigo-500"
                          aria-hidden="true"
                        ></span>
                      </div>
                      <div class="ml-3.5 text-sm font-medium text-gray-900">
                        Mus Musculus
                      </div>
                    </a>
                    <!-- space -->
                  </li>
                </ul>
              </div>
              <div>
                <h2 class="text-sm font-medium text-gray-500">Tags</h2>
                <ul role="list" class="mt-2 leading-8">
                  <li class="inline">
                    <a
                      href="#"
                      class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                    >
                      <div
                        class="absolute flex-shrink-0 flex items-center justify-center"
                      >
                        <span
                          class="h-1.5 w-1.5 rounded-full bg-rose-500"
                          aria-hidden="true"
                        ></span>
                      </div>
                      <div class="ml-3.5 text-sm font-medium text-gray-900">2D NMR</div>
                    </a>
                    <!-- space -->
                  </li>
                  <li class="inline">
                    <a
                      href="#"
                      class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5"
                    >
                      <div
                        class="absolute flex-shrink-0 flex items-center justify-center"
                      >
                        <span
                          class="h-1.5 w-1.5 rounded-full bg-indigo-500"
                          aria-hidden="true"
                        ></span>
                      </div>
                      <div class="ml-3.5 text-sm font-medium text-gray-900">
                        Metabolomics
                      </div>
                    </a>
                    <!-- space -->
                  </li>
                </ul>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>
  </section>
  <section class="mt-12">
    <div>
      <div class="mx-2">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div
            class="divide-y divide-gray-200 lg:grid lg:grid-cols-12 lg:divide-y-0 lg:divide-x"
          >
            <aside class="py-6 lg:col-span-3">
              <nav class="space-y-1">
                <a
                  href="#"
                  class="bg-gray-50 border-gray-500 text-gray-700 hover:bg-gray-50 hover:text-gray-700 group border-l-4 px-3 py-2 flex items-center text-sm font-medium"
                  x-state:on="Current"
                  x-state:off="Default"
                  aria-current="page"
                  x-state-description='Current: "bg-teal-50 border-teal-500 text-teal-700 hover:bg-teal-50 hover:text-teal-700", Default: "border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900"'
                >
                  <span class="truncate"> Protocols </span>
                </a>

                <a
                  href="#"
                  class="border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 group border-l-4 px-3 py-2 flex items-center text-sm font-medium"
                  x-state-description='undefined: "bg-teal-50 border-teal-500 text-teal-700 hover:bg-teal-50 hover:text-teal-700", undefined: "border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900"'
                >
                  <span class="truncate"> Assays </span>
                </a>

                <a
                  href="#"
                  class="border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 group border-l-4 px-3 py-2 flex items-center text-sm font-medium"
                  x-state-description='undefined: "bg-teal-50 border-teal-500 text-teal-700 hover:bg-teal-50 hover:text-teal-700", undefined: "border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900"'
                >
                  <span class="truncate"> Metabolite Identification </span>
                </a>

                <a
                  href="#"
                  class="border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 group border-l-4 px-3 py-2 flex items-center text-sm font-medium"
                  x-state-description='undefined: "bg-teal-50 border-teal-500 text-teal-700 hover:bg-teal-50 hover:text-teal-700", undefined: "border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900"'
                >
                  <span class="truncate"> Analysis </span>
                </a>

                <a
                  href="#"
                  class="border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 group border-l-4 px-3 py-2 flex items-center text-sm font-medium"
                  x-state-description='undefined: "bg-teal-50 border-teal-500 text-teal-700 hover:bg-teal-50 hover:text-teal-700", undefined: "border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900"'
                >
                  <span class="truncate"> Files </span>
                </a>

                <a
                  href="#"
                  class="border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 group border-l-4 px-3 py-2 flex items-center text-sm font-medium"
                  x-state-description='undefined: "bg-teal-50 border-teal-500 text-teal-700 hover:bg-teal-50 hover:text-teal-700", undefined: "border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900"'
                >
                  <span class="truncate"> Integrations </span>
                </a>
              </nav>
            </aside>

            <div class="divide-y divide-gray-200 lg:col-span-9">
              <ul role="list" class="space-y-4">
                <li class="bg-white px-4 py-6 shadow sm:p-6">
                  <article aria-labelledby="question-title-81614">
                    <div>
                      <div class="flex space-x-3">
                        <div class="min-w-0 flex-1">
                          <p class="text-md font-medium text-gray-900">
                            <a href="#" class="hover:underline">Sample Collection</a>
                          </p>
                        </div>
                        <div class="flex-shrink-0 self-center flex">
                          <div
                            x-data="Components.menu({ open: false })"
                            x-init="init()"
                            @keydown.escape.stop="
                              open = false;
                              focusButton();
                            "
                            @click.away="onClickAway($event)"
                            class="relative inline-block text-left"
                          >
                            <div>
                              <button
                                type="button"
                                class="-m-2 p-2 rounded-full flex items-center text-gray-400 hover:text-gray-600"
                                id="options-menu-0-button"
                                x-ref="button"
                                @click="onButtonClick()"
                                @keyup.space.prevent="onButtonEnter()"
                                @keydown.enter.prevent="onButtonEnter()"
                                aria-expanded="false"
                                aria-haspopup="true"
                                x-bind:aria-expanded="open.toString()"
                                @keydown.arrow-up.prevent="onArrowUp()"
                                @keydown.arrow-down.prevent="onArrowDown()"
                              >
                                <span class="sr-only">Open options</span>
                                <svg
                                  class="h-5 w-5"
                                  x-description="Heroicon name: solid/dots-vertical"
                                  xmlns="http://www.w3.org/2000/svg"
                                  viewBox="0 0 20 20"
                                  fill="currentColor"
                                  aria-hidden="true"
                                >
                                  <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                  ></path>
                                </svg>
                              </button>
                            </div>

                            <div
                              x-show="open"
                              x-transition:enter="transition ease-out duration-100"
                              x-transition:enter-start="transform opacity-0 scale-95"
                              x-transition:enter-end="transform opacity-100 scale-100"
                              x-transition:leave="transition ease-in duration-75"
                              x-transition:leave-start="transform opacity-100 scale-100"
                              x-transition:leave-end="transform opacity-0 scale-95"
                              class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                              x-ref="menu-items"
                              x-description="Dropdown menu, show/hide based on menu state."
                              x-bind:aria-activedescendant="activeDescendant"
                              role="menu"
                              aria-orientation="vertical"
                              aria-labelledby="options-menu-0-button"
                              tabindex="-1"
                              @keydown.arrow-up.prevent="onArrowUp()"
                              @keydown.arrow-down.prevent="onArrowDown()"
                              @keydown.tab="open = false"
                              @keydown.enter.prevent="
                                open = false;
                                focusButton();
                              "
                              @keyup.space.prevent="
                                open = false;
                                focusButton();
                              "
                              style="display: none"
                            >
                              <div class="py-1" role="none">
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  x-state:on="Active"
                                  x-state:off="Not Active"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 0,
                                    'text-gray-700': !(activeIndex === 0),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-0"
                                  @mouseenter="activeIndex = 0"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/star"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    ></path>
                                  </svg>
                                  <span>Add to favorites</span>
                                </a>
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 1,
                                    'text-gray-700': !(activeIndex === 1),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-1"
                                  @mouseenter="activeIndex = 1"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/code"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                  <span>Embed</span>
                                </a>
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 2,
                                    'text-gray-700': !(activeIndex === 2),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-2"
                                  @mouseenter="activeIndex = 2"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/flag"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                  <span>Report content</span>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-700 space-y-4">
                      <p>
                        For the human studies, midstream urine (~15 ml) samples were
                        collected and frozen from each volunteer. In total, 84 samples
                        were collected from 12 healthy volunteers (7 time points, 8 males
                        and 4 females) and 50 samples from 30 T2DM patients (1-3 time
                        points, 17 males and 13 females) with well-controlled blood
                        glucose maintained at normal concentrations by diet, following the
                        guidelines issued by the American Diabetes Association, rather
                        than medication. T2DM patients agreed to stop treatment with oral
                        anti-diabetic agents during the study. Subjects went through a
                        washout period of 4 wk before sample collection and abstained from
                        alcohol during the study; diet was controlled throughout the
                        study.
                      </p>
                    </div>
                    <div class="mt-6 flex justify-between space-x-8">
                      <div class="flex space-x-6">
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/chat-alt"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">11</span>
                            <span class="sr-only">replies</span>
                          </button>
                        </span>
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/eye"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                              <path
                                fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">2.7k</span>
                            <span class="sr-only">views</span>
                          </button>
                        </span>
                      </div>
                      <div class="flex text-sm">
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/share"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">Share</span>
                          </button>
                        </span>
                      </div>
                    </div>
                  </article>
                </li>
                <li class="bg-white px-4 py-6 shadow sm:p-6">
                  <article aria-labelledby="question-title-81614">
                    <div>
                      <div class="flex space-x-3">
                        <div class="min-w-0 flex-1">
                          <p class="text-md font-medium text-gray-900">
                            <a href="#" class="hover:underline">Extraction</a>
                          </p>
                        </div>
                        <div class="flex-shrink-0 self-center flex">
                          <div
                            x-data="Components.menu({ open: false })"
                            x-init="init()"
                            @keydown.escape.stop="
                              open = false;
                              focusButton();
                            "
                            @click.away="onClickAway($event)"
                            class="relative inline-block text-left"
                          >
                            <div>
                              <button
                                type="button"
                                class="-m-2 p-2 rounded-full flex items-center text-gray-400 hover:text-gray-600"
                                id="options-menu-0-button"
                                x-ref="button"
                                @click="onButtonClick()"
                                @keyup.space.prevent="onButtonEnter()"
                                @keydown.enter.prevent="onButtonEnter()"
                                aria-expanded="false"
                                aria-haspopup="true"
                                x-bind:aria-expanded="open.toString()"
                                @keydown.arrow-up.prevent="onArrowUp()"
                                @keydown.arrow-down.prevent="onArrowDown()"
                              >
                                <span class="sr-only">Open options</span>
                                <svg
                                  class="h-5 w-5"
                                  x-description="Heroicon name: solid/dots-vertical"
                                  xmlns="http://www.w3.org/2000/svg"
                                  viewBox="0 0 20 20"
                                  fill="currentColor"
                                  aria-hidden="true"
                                >
                                  <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                  ></path>
                                </svg>
                              </button>
                            </div>

                            <div
                              x-show="open"
                              x-transition:enter="transition ease-out duration-100"
                              x-transition:enter-start="transform opacity-0 scale-95"
                              x-transition:enter-end="transform opacity-100 scale-100"
                              x-transition:leave="transition ease-in duration-75"
                              x-transition:leave-start="transform opacity-100 scale-100"
                              x-transition:leave-end="transform opacity-0 scale-95"
                              class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                              x-ref="menu-items"
                              x-description="Dropdown menu, show/hide based on menu state."
                              x-bind:aria-activedescendant="activeDescendant"
                              role="menu"
                              aria-orientation="vertical"
                              aria-labelledby="options-menu-0-button"
                              tabindex="-1"
                              @keydown.arrow-up.prevent="onArrowUp()"
                              @keydown.arrow-down.prevent="onArrowDown()"
                              @keydown.tab="open = false"
                              @keydown.enter.prevent="
                                open = false;
                                focusButton();
                              "
                              @keyup.space.prevent="
                                open = false;
                                focusButton();
                              "
                              style="display: none"
                            >
                              <div class="py-1" role="none">
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  x-state:on="Active"
                                  x-state:off="Not Active"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 0,
                                    'text-gray-700': !(activeIndex === 0),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-0"
                                  @mouseenter="activeIndex = 0"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/star"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    ></path>
                                  </svg>
                                  <span>Add to favorites</span>
                                </a>
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 1,
                                    'text-gray-700': !(activeIndex === 1),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-1"
                                  @mouseenter="activeIndex = 1"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/code"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                  <span>Embed</span>
                                </a>
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 2,
                                    'text-gray-700': !(activeIndex === 2),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-2"
                                  @mouseenter="activeIndex = 2"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/flag"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                  <span>Report content</span>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-700 space-y-4">
                      <p>
                        For the human studies, midstream urine (~15 ml) samples were
                        collected and frozen from each volunteer. In total, 84 samples
                        were collected from 12 healthy volunteers (7 time points, 8 males
                        and 4 females) and 50 samples from 30 T2DM patients (1-3 time
                        points, 17 males and 13 females) with well-controlled blood
                        glucose maintained at normal concentrations by diet, following the
                        guidelines issued by the American Diabetes Association, rather
                        than medication. T2DM patients agreed to stop treatment with oral
                        anti-diabetic agents during the study. Subjects went through a
                        washout period of 4 wk before sample collection and abstained from
                        alcohol during the study; diet was controlled throughout the
                        study.
                      </p>
                    </div>
                    <div class="mt-6 flex justify-between space-x-8">
                      <div class="flex space-x-6">
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/chat-alt"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">11</span>
                            <span class="sr-only">replies</span>
                          </button>
                        </span>
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/eye"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                              <path
                                fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">2.7k</span>
                            <span class="sr-only">views</span>
                          </button>
                        </span>
                      </div>
                      <div class="flex text-sm">
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/share"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">Share</span>
                          </button>
                        </span>
                      </div>
                    </div>
                  </article>
                </li>
                <li class="bg-white px-4 py-6 shadow sm:p-6">
                  <article aria-labelledby="question-title-81614">
                    <div>
                      <div class="flex space-x-3">
                        <div class="min-w-0 flex-1">
                          <p class="text-md font-medium text-gray-900">
                            <a href="#" class="hover:underline">NMR</a>
                          </p>
                        </div>
                        <div class="flex-shrink-0 self-center flex">
                          <div
                            x-data="Components.menu({ open: false })"
                            x-init="init()"
                            @keydown.escape.stop="
                              open = false;
                              focusButton();
                            "
                            @click.away="onClickAway($event)"
                            class="relative inline-block text-left"
                          >
                            <div>
                              <button
                                type="button"
                                class="-m-2 p-2 rounded-full flex items-center text-gray-400 hover:text-gray-600"
                                id="options-menu-0-button"
                                x-ref="button"
                                @click="onButtonClick()"
                                @keyup.space.prevent="onButtonEnter()"
                                @keydown.enter.prevent="onButtonEnter()"
                                aria-expanded="false"
                                aria-haspopup="true"
                                x-bind:aria-expanded="open.toString()"
                                @keydown.arrow-up.prevent="onArrowUp()"
                                @keydown.arrow-down.prevent="onArrowDown()"
                              >
                                <span class="sr-only">Open options</span>
                                <svg
                                  class="h-5 w-5"
                                  x-description="Heroicon name: solid/dots-vertical"
                                  xmlns="http://www.w3.org/2000/svg"
                                  viewBox="0 0 20 20"
                                  fill="currentColor"
                                  aria-hidden="true"
                                >
                                  <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                  ></path>
                                </svg>
                              </button>
                            </div>

                            <div
                              x-show="open"
                              x-transition:enter="transition ease-out duration-100"
                              x-transition:enter-start="transform opacity-0 scale-95"
                              x-transition:enter-end="transform opacity-100 scale-100"
                              x-transition:leave="transition ease-in duration-75"
                              x-transition:leave-start="transform opacity-100 scale-100"
                              x-transition:leave-end="transform opacity-0 scale-95"
                              class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                              x-ref="menu-items"
                              x-description="Dropdown menu, show/hide based on menu state."
                              x-bind:aria-activedescendant="activeDescendant"
                              role="menu"
                              aria-orientation="vertical"
                              aria-labelledby="options-menu-0-button"
                              tabindex="-1"
                              @keydown.arrow-up.prevent="onArrowUp()"
                              @keydown.arrow-down.prevent="onArrowDown()"
                              @keydown.tab="open = false"
                              @keydown.enter.prevent="
                                open = false;
                                focusButton();
                              "
                              @keyup.space.prevent="
                                open = false;
                                focusButton();
                              "
                              style="display: none"
                            >
                              <div class="py-1" role="none">
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  x-state:on="Active"
                                  x-state:off="Not Active"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 0,
                                    'text-gray-700': !(activeIndex === 0),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-0"
                                  @mouseenter="activeIndex = 0"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/star"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    ></path>
                                  </svg>
                                  <span>Add to favorites</span>
                                </a>
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 1,
                                    'text-gray-700': !(activeIndex === 1),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-1"
                                  @mouseenter="activeIndex = 1"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/code"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                  <span>Embed</span>
                                </a>
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 2,
                                    'text-gray-700': !(activeIndex === 2),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-2"
                                  @mouseenter="activeIndex = 2"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/flag"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                  <span>Report content</span>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-700 space-y-4">
                      <p>
                        For the human studies, midstream urine (~15 ml) samples were
                        collected and frozen from each volunteer. In total, 84 samples
                        were collected from 12 healthy volunteers (7 time points, 8 males
                        and 4 females) and 50 samples from 30 T2DM patients (1-3 time
                        points, 17 males and 13 females) with well-controlled blood
                        glucose maintained at normal concentrations by diet, following the
                        guidelines issued by the American Diabetes Association, rather
                        than medication. T2DM patients agreed to stop treatment with oral
                        anti-diabetic agents during the study. Subjects went through a
                        washout period of 4 wk before sample collection and abstained from
                        alcohol during the study; diet was controlled throughout the
                        study.
                      </p>
                    </div>
                    <div class="mt-6 flex justify-between space-x-8">
                      <div class="flex space-x-6">
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/chat-alt"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">11</span>
                            <span class="sr-only">replies</span>
                          </button>
                        </span>
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/eye"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                              <path
                                fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">2.7k</span>
                            <span class="sr-only">views</span>
                          </button>
                        </span>
                      </div>
                      <div class="flex text-sm">
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/share"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">Share</span>
                          </button>
                        </span>
                      </div>
                    </div>
                  </article>
                </li>
                <li class="bg-white px-4 py-6 shadow sm:p-6">
                  <article aria-labelledby="question-title-81614">
                    <div>
                      <div class="flex space-x-3">
                        <div class="min-w-0 flex-1">
                          <p class="text-md font-medium text-gray-900">
                            <a href="#" class="hover:underline">NMR Spectroscopy</a>
                          </p>
                        </div>
                        <div class="flex-shrink-0 self-center flex">
                          <div
                            x-data="Components.menu({ open: false })"
                            x-init="init()"
                            @keydown.escape.stop="
                              open = false;
                              focusButton();
                            "
                            @click.away="onClickAway($event)"
                            class="relative inline-block text-left"
                          >
                            <div>
                              <button
                                type="button"
                                class="-m-2 p-2 rounded-full flex items-center text-gray-400 hover:text-gray-600"
                                id="options-menu-0-button"
                                x-ref="button"
                                @click="onButtonClick()"
                                @keyup.space.prevent="onButtonEnter()"
                                @keydown.enter.prevent="onButtonEnter()"
                                aria-expanded="false"
                                aria-haspopup="true"
                                x-bind:aria-expanded="open.toString()"
                                @keydown.arrow-up.prevent="onArrowUp()"
                                @keydown.arrow-down.prevent="onArrowDown()"
                              >
                                <span class="sr-only">Open options</span>
                                <svg
                                  class="h-5 w-5"
                                  x-description="Heroicon name: solid/dots-vertical"
                                  xmlns="http://www.w3.org/2000/svg"
                                  viewBox="0 0 20 20"
                                  fill="currentColor"
                                  aria-hidden="true"
                                >
                                  <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                  ></path>
                                </svg>
                              </button>
                            </div>

                            <div
                              x-show="open"
                              x-transition:enter="transition ease-out duration-100"
                              x-transition:enter-start="transform opacity-0 scale-95"
                              x-transition:enter-end="transform opacity-100 scale-100"
                              x-transition:leave="transition ease-in duration-75"
                              x-transition:leave-start="transform opacity-100 scale-100"
                              x-transition:leave-end="transform opacity-0 scale-95"
                              class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                              x-ref="menu-items"
                              x-description="Dropdown menu, show/hide based on menu state."
                              x-bind:aria-activedescendant="activeDescendant"
                              role="menu"
                              aria-orientation="vertical"
                              aria-labelledby="options-menu-0-button"
                              tabindex="-1"
                              @keydown.arrow-up.prevent="onArrowUp()"
                              @keydown.arrow-down.prevent="onArrowDown()"
                              @keydown.tab="open = false"
                              @keydown.enter.prevent="
                                open = false;
                                focusButton();
                              "
                              @keyup.space.prevent="
                                open = false;
                                focusButton();
                              "
                              style="display: none"
                            >
                              <div class="py-1" role="none">
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  x-state:on="Active"
                                  x-state:off="Not Active"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 0,
                                    'text-gray-700': !(activeIndex === 0),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-0"
                                  @mouseenter="activeIndex = 0"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/star"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    ></path>
                                  </svg>
                                  <span>Add to favorites</span>
                                </a>
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 1,
                                    'text-gray-700': !(activeIndex === 1),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-1"
                                  @mouseenter="activeIndex = 1"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/code"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                  <span>Embed</span>
                                </a>
                                <a
                                  href="#"
                                  class="text-gray-700 flex px-4 py-2 text-sm"
                                  :class="{
                                    'bg-gray-100 text-gray-900': activeIndex === 2,
                                    'text-gray-700': !(activeIndex === 2),
                                  }"
                                  role="menuitem"
                                  tabindex="-1"
                                  id="options-menu-0-item-2"
                                  @mouseenter="activeIndex = 2"
                                  @mouseleave="activeIndex = -1"
                                  @click="
                                    open = false;
                                    focusButton();
                                  "
                                >
                                  <svg
                                    class="mr-3 h-5 w-5 text-gray-400"
                                    x-description="Heroicon name: solid/flag"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                  >
                                    <path
                                      fill-rule="evenodd"
                                      d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                      clip-rule="evenodd"
                                    ></path>
                                  </svg>
                                  <span>Report content</span>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-700 space-y-4">
                      <p>
                        For the human studies, midstream urine (~15 ml) samples were
                        collected and frozen from each volunteer. In total, 84 samples
                        were collected from 12 healthy volunteers (7 time points, 8 males
                        and 4 females) and 50 samples from 30 T2DM patients (1-3 time
                        points, 17 males and 13 females) with well-controlled blood
                        glucose maintained at normal concentrations by diet, following the
                        guidelines issued by the American Diabetes Association, rather
                        than medication. T2DM patients agreed to stop treatment with oral
                        anti-diabetic agents during the study. Subjects went through a
                        washout period of 4 wk before sample collection and abstained from
                        alcohol during the study; diet was controlled throughout the
                        study.
                      </p>
                    </div>
                    <div class="mt-6 flex justify-between space-x-8">
                      <div class="flex space-x-6">
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/chat-alt"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">11</span>
                            <span class="sr-only">replies</span>
                          </button>
                        </span>
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/eye"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                              <path
                                fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">2.7k</span>
                            <span class="sr-only">views</span>
                          </button>
                        </span>
                      </div>
                      <div class="flex text-sm">
                        <span class="inline-flex items-center text-sm">
                          <button
                            type="button"
                            class="inline-flex space-x-2 text-gray-400 hover:text-gray-500"
                          >
                            <svg
                              class="h-5 w-5"
                              x-description="Heroicon name: solid/share"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                              ></path>
                            </svg>
                            <span class="font-medium text-gray-900">Share</span>
                          </button>
                        </span>
                      </div>
                    </div>
                  </article>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  props: ["study"],
  components: {},
};
</script>
