<template>
  <TransitionRoot :show="open" as="template" appear>
    <Dialog as="div" class="relative z-10">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 scale-95"
          enter-to="opacity-100 scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 scale-100"
          leave-to="opacity-0 scale-95"
        >
          <DialogPanel
            class="mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
          >
            <div>
              <div v-if="currentStep == 1">
                <section class="h-1/2 overflow-hidden bg-gray-50 overflow-hidden py-12">
                  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <svg
                      class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2"
                      width="404"
                      height="404"
                      fill="none"
                      viewBox="0 0 404 404"
                      role="img"
                      aria-labelledby="svg-nmrxiv"
                    >
                      <title id="svg-nmrxiv">nmrxiv</title>
                      <defs>
                        <pattern
                          id="ad119f34-7694-4c31-947f-5c9d249b21f3"
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
                        height="404"
                        fill="url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                      />
                    </svg>

                    <div class="relative">
                      <div class="flex justify-center">
                        <jet-application-logo class="block h-14 w-auto" />
                      </div>
                      <blockquote class="mt-10">
                        <div
                          class="max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900"
                        >
                          <p class="mt-3 text-lg">
                            Welcome! Lorem ipsum dolor sit amet consectetur adipisicing
                            elit. Nemo expedita voluptas culpa sapiente alias molestiae.
                            Numquam corrupti in laborum sed rerum et corporis.&rdquo;
                          </p>
                        </div>
                      </blockquote>
                    </div>
                  </div>
                </section>
              </div>
              <div v-if="currentStep == 2">
                <section class="h-1/2 overflow-hidden py-12">
                  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <svg
                      class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2"
                      width="404"
                      height="404"
                      fill="none"
                      viewBox="0 0 404 404"
                      role="img"
                      aria-labelledby="svg-nmrxiv"
                    >
                      <title id="svg-nmrxiv">nmrxiv</title>
                      <defs>
                        <pattern
                          id="ad119f34-7694-4c31-947f-5c9d249b21f3"
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
                        height="404"
                        fill="url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                      />
                    </svg>
                    <div class="relative text-center">
                      <div class="flex justify-center">
                        <img
                          class="rounded-md"
                          style="height: 200px"
                          src="/img/illustration1.jpg"
                        />
                      </div>
                      <blockquote class="mt-10">
                        <div
                          class="max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900"
                        >
                          <p class="mt-3 text-lg">
                            Welcome! Lorem ipsum dolor sit amet consectetur adipisicing
                            elit. Nemo expedita voluptas culpa sapiente alias molestiae.
                            Numquam corrupti in laborum sed rerum et corporis.&rdquo;
                          </p>
                        </div>
                      </blockquote>
                      <small
                        ><i
                          ><a href="https://www.freepik.com/vectors/science-research"
                            >Science research vector created by storyset -
                            www.freepik.com</a
                          ></i
                        ></small
                      >
                    </div>
                  </div>
                </section>
              </div>
              <div v-if="currentStep == 3">
                <div
                  class="h-1/2 bg-gray-50 overflow-hidden max-w-7xl mx-auto text-center py-12 px-4 sm:px-6"
                >
                  <h2 class="text-base font-semibold uppercase tracking-wider">
                    SUPPORT
                  </h2>
                  <p class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">
                    We’re here to help
                  </p>
                  <p class="mt-3 text-lg">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Et, egestas
                    tempus tellus etiam sed. Quam a scelerisque amet ullamcorper eu enim
                    et fermentum, augue. Aliquet amet volutpat quisque ut interdum
                    tincidunt duis.
                  </p>
                  <div class="mt-8">
                    <div class="inline-flex rounded-md shadow">
                      <a
                        target="_blank"
                        href="https://docs.nmrxiv.org"
                        class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                      >
                        Visit the docs
                        <ExternalLinkIcon
                          class="-mr-1 ml-3 h-5 w-5 text-gray-400"
                          aria-hidden="true"
                        />
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="currentStep == 4">
                <div
                  class="max-w-7xl mx-auto text-center py-12 px-4 sm:px-6 lg:py-16 lg:px-8"
                >
                  <h2
                    class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"
                  >
                    <span class="block text-gray-500">All set to get started!</span>
                    <span class="block">Start uploading your spectral data today.</span>
                  </h2>
                  <div class="mt-8 flex justify-center">
                    <div class="inline-flex rounded-md shadow">
                      <a
                        @click="onboardingComplete"
                        class="cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                      >
                        Get started
                      </a>
                    </div>
                    <div class="ml-3 inline-flex">
                      <a
                        @click="onboardAndStartTour"
                        class="cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
                      >
                        Dashboard Tour
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-4 gap-4">
                <div class="p-4 py-4">
                  <button
                    @click="selectStep(currentStep - 1)"
                    v-if="currentStep != 1"
                    type="button"
                    class="py-2 px-3"
                  >
                    <div class="flex flex-row align-middle">
                      <svg
                        class="w-5 mr-2"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                          clip-rule="evenodd"
                        ></path>
                      </svg>
                      <p class="ml-2">Prev</p>
                    </div>
                  </button>
                  <button
                    @click="onboardingComplete"
                    type="button"
                    class="py-2 px-3 border rounded-md"
                    v-else
                  >
                    <div class="flex flex-row align-middle">
                      <p class="mx-2">Skip</p>
                    </div>
                  </button>
                </div>
                <div class="col-span-2">
                  <nav
                    class="flex items-center justify-center p-4 py-6"
                    aria-label="Progress"
                  >
                    <p class="text-sm font-medium">
                      Step
                      {{ steps.findIndex((step) => step.status === "current") + 1 }} of
                      {{ steps.length }}
                    </p>
                    <ol role="list" class="ml-8 flex items-center space-x-5">
                      <li v-for="step in steps" :key="step.name">
                        <a class="cursor-pointer" @click="selectStep(step.id)">
                          <span
                            v-if="step.status === 'complete'"
                            :href="step.href"
                            class="block w-2.5 h-2.5 bg-indigo-600 rounded-full hover:bg-indigo-900"
                          >
                            <span class="sr-only">{{ step.name }}</span>
                          </span>
                          <span
                            v-else-if="step.status === 'current'"
                            :href="step.href"
                            class="relative flex items-center justify-center"
                            aria-current="step"
                          >
                            <span class="absolute w-5 h-5 p-px flex" aria-hidden="true">
                              <span class="w-full h-full rounded-full bg-indigo-200" />
                            </span>
                            <span
                              class="relative block w-2.5 h-2.5 bg-indigo-600 rounded-full"
                              aria-hidden="true"
                            />
                            <span class="sr-only">{{ step.name }}</span>
                          </span>
                          <span
                            v-else
                            :href="step.href"
                            class="block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                          >
                            <span class="sr-only">{{ step.name }}</span>
                          </span>
                        </a>
                      </li>
                    </ol>
                  </nav>
                </div>
                <div class="p-4 py-4 text-right">
                  <button
                    v-if="currentStep < steps.length"
                    @click="selectStep(currentStep + 1)"
                    type="button"
                    class="py-2 px-3"
                  >
                    <div class="flex flex-row align-middle">
                      <span class="mr-2">Next</span>
                      <svg
                        class="w-5 ml-2"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        ></path>
                      </svg>
                    </div>
                  </button>
                </div>
              </div>
            </div>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import JetApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import { computed, ref } from "vue";
import {
  Combobox,
  ComboboxInput,
  ComboboxOptions,
  ComboboxOption,
  Dialog,
  DialogPanel,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";

import { SearchIcon } from "@heroicons/vue/solid";
import {
  DocumentAddIcon,
  FolderIcon,
  FolderAddIcon,
  HashtagIcon,
  ExternalLinkIcon,
  TagIcon,
} from "@heroicons/vue/outline";
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";

export default {
  components: {
    JetApplicationLogo,
    computed,
    ref,
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
    SearchIcon,
    DocumentAddIcon,
    FolderIcon,
    FolderAddIcon,
    HashtagIcon,
    TagIcon,
    ExternalLinkIcon,
  },
  props: [],
  data() {
    return {
      open: ref(false),
      steps: [
        { id: 1, name: "Step 1", href: "#", status: "current" },
        { id: 2, name: "Step 2", href: "#", status: "upcoming" },
        { id: 3, name: "Step 3", href: "#", status: "upcoming" },
        { id: 4, name: "Step 4", href: "#", status: "upcoming" },
      ],
    };
  },
  mounted() {
    if (!this.$page.props.user.onboarded) {
      this.open = true;
    }
  },
  methods: {
    selectStep(id) {
      this.steps.forEach((step) => {
        if (parseInt(step.id) < id) {
          step.status = "complete";
        } else if (parseInt(step.id) == id) {
          step.status = "current";
        } else {
          step.status = "upcoming";
        }
      });
    },
    onboardingComplete() {
      this.open = false;
      axios.post("/onboarding/complete").then((resp) => {
        Inertia.reload({ only: ["user"] });
      });
    },
    onboardAndStartTour() {
      this.onboardingComplete();
      this.$tours["appTour"].start();
    },
  },
  computed: {
    currentStep() {
      return this.steps.find((s) => s.status == "current").id;
    },
  },
};
</script>
