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
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"
                />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
                <TransitionChild
                    as="template"
                    enter="ease-out duration-300"
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
                                <section
                                    class="h-1/2 overflow-hidden bg-gray-50 overflow-hidden py-12"
                                >
                                    <div
                                        class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
                                    >
                                        <svg
                                            class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2"
                                            width="404"
                                            height="404"
                                            fill="none"
                                            viewBox="0 0 404 404"
                                            role="img"
                                            aria-labelledby="svg-nmrxiv"
                                        >
                                            <title id="svg-nmrxiv">
                                                nmrxiv
                                            </title>
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
                                                <jet-application-logo
                                                    class="block h-14 w-auto"
                                                />
                                            </div>
                                            <blockquote class="mt-10">
                                                <div
                                                    class="max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900"
                                                >
                                                    <p class="mt-3 text-lg">
                                                        Welcome!
                                                        <span class="text-xl"
                                                            >nmr<b>Xiv</b></span
                                                        >
                                                        support scientists in
                                                        their efforts to
                                                        collect, store, process,
                                                        analyse, disclose and
                                                        re-use Nuclear Magnetic
                                                        Resonance Spectroscopy
                                                        research data. Ready to
                                                        get started?
                                                    </p>
                                                </div>
                                            </blockquote>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div v-if="currentStep == 2">
                                <section class="h-1/2 overflow-hidden py-12">
                                    <div
                                        class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
                                    >
                                        <svg
                                            class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2"
                                            width="404"
                                            height="404"
                                            fill="none"
                                            viewBox="0 0 404 404"
                                            role="img"
                                            aria-labelledby="svg-nmrxiv"
                                        >
                                            <title id="svg-nmrxiv">
                                                nmrxiv
                                            </title>
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
                                            <blockquote class="mt-10">
                                                <div
                                                    class="max-w-3xl mx-auto text-center text-xl leading-9 font-medium text-gray-900"
                                                >
                                                    <p class="mt-3 text-lg">
                                                        <span class="text-xl"
                                                            >nmr<b>Xiv</b></span
                                                        >
                                                        as a part of
                                                        <a
                                                            class="text-indigo-600 font-bold"
                                                            href="https://nfdi4chem.de"
                                                            target="_blank"
                                                            >NFDI4Chem</a
                                                        >, focuses on the
                                                        implementation and
                                                        adaptation of existing
                                                        and new infrastructure,
                                                        necessary to capture
                                                        data early in the life
                                                        cycle​ and to further
                                                        manage, analyse and
                                                        store associated
                                                        information. Checkout
                                                        <a
                                                            class="text-indigo-600"
                                                            target="_blank"
                                                            href="https://www.chemotion.net/chemotionsaurus/index.html"
                                                            >Chemotion</a
                                                        >
                                                        (Electronic Lab
                                                        Notebook) or
                                                        <a
                                                            href="https://github.com/NFDI4Chem/nmrxiv-cli"
                                                            class="text-indigo-600"
                                                            target="_blank"
                                                            >nmrXiv&nbsp;CLI </a
                                                        >(Command Line
                                                        Interface) for a
                                                        seamless integration
                                                        into your Lab
                                                        infrastructure.
                                                    </p>
                                                </div>
                                            </blockquote>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div v-if="currentStep == 3">
                                <section class="h-1/2 overflow-hidden py-12">
                                    <div
                                        class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
                                    >
                                        <svg
                                            class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2"
                                            width="404"
                                            height="404"
                                            fill="none"
                                            viewBox="0 0 404 404"
                                            role="img"
                                            aria-labelledby="svg-nmrxiv"
                                        >
                                            <title id="svg-nmrxiv">
                                                nmrxiv
                                            </title>
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
                                            <p class="mt-3 text-lg">
                                                <span class="text-xl"
                                                    >nmr<b>Xiv</b></span
                                                >
                                                is 100% open source, so you’re
                                                free to dig through the source
                                                to see exactly how it works. See
                                                something that needs to be
                                                improved? Just send us a
                                                request.<br />
                                                <small
                                                    >Chat bubble is on the
                                                    bottom left corner or email
                                                    us at
                                                    <a :href="mailTo"
                                                        >{{ mailFromAddress }}
                                                    </a></small
                                                >
                                            </p>
                                            <div class="mt-8">
                                                <div class="inline-flex">
                                                    <a
                                                        target="_blank"
                                                        href="https://github.com/NFDI4Chem/nmrxiv"
                                                        class="inline-flex mr-4 items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                                    >
                                                        Source code
                                                        <ArrowTopRightOnSquareIcon
                                                            class="-mr-1 ml-3 h-5 w-5 text-gray-400"
                                                            aria-hidden="true"
                                                        />
                                                    </a>
                                                    <a
                                                        target="_blank"
                                                        href="https://docs.nmrxiv.org"
                                                        class="inline-flex items-center justify-center px-5 py-3 border text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50"
                                                    >
                                                        Visit the docs
                                                        <ArrowTopRightOnSquareIcon
                                                            class="-mr-1 ml-3 h-5 w-5 text-gray-400"
                                                            aria-hidden="true"
                                                        />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div v-if="currentStep == 4">
                                <div
                                    class="max-w-7xl mx-auto text-center py-12 px-4 sm:px-6 lg:py-16 lg:px-8"
                                >
                                    <h2
                                        class="text-base font-semibold uppercase tracking-wider"
                                    >
                                        All set to get started!
                                    </h2>
                                    <p
                                        class="my-2 font-bold tracking-tight sm:text-2xl"
                                    >
                                        Start uploading your spectral data
                                        today.
                                    </p>
                                    <p>
                                        Click "Get started" and upload your data
                                        or if you want a guided tour of the
                                        dashboard press the "Dashboard Tour"
                                        button below.
                                    </p>
                                    <div class="mt-8 flex justify-center">
                                        <div
                                            class="inline-flex rounded-md shadow"
                                        >
                                            <a
                                                refs="next"
                                                class="cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                                                @click="onboardingComplete"
                                            >
                                                Get started
                                            </a>
                                        </div>
                                        <div class="ml-3 inline-flex">
                                            <a
                                                class="cursor-pointer inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
                                                @click="onboardAndStartTour"
                                            >
                                                Dashboard Tour
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-4">
                                <div class="p-4 py-4">
                                    <JetSecondaryButton
                                        v-if="currentStep != 1"
                                        class="font-bold"

                                        @click="selectStep(currentStep - 1)"
                                    >
                                        <ArrowSmallLeftIcon
                                            class="mr-1.5 w-5"
                                        />back</JetSecondaryButton
                                    >
                                    <JetSecondaryButton
                                    v-else
                                    class="font-bold"
                                        @click="onboardingComplete">
                                        Skip
                                    </JetSecondaryButton>

                               
                                </div>
                                <div class="col-span-2">
                                    <nav
                                        class="flex items-center justify-center p-4 py-6"
                                        aria-label="Progress"
                                    >
                                        <p class="text-sm font-medium">
                                            Step
                                            {{
                                                steps.findIndex(
                                                    (step) =>
                                                        step.status ===
                                                        "current"
                                                ) + 1
                                            }}
                                            of
                                            {{ steps.length }}
                                        </p>
                                        <ol
                                            role="list"
                                            class="ml-8 flex items-center space-x-5"
                                        >
                                            <li
                                                v-for="step in steps"
                                                :key="step.name"
                                            >
                                                <a
                                                    class="cursor-pointer"
                                                    @click="selectStep(step.id)"
                                                >
                                                    <span
                                                        v-if="
                                                            step.status ===
                                                            'complete'
                                                        "
                                                        :href="step.href"
                                                        class="block w-2.5 h-2.5 bg-indigo-600 rounded-full hover:bg-indigo-600"
                                                    >
                                                        <span class="sr-only">{{
                                                            step.name
                                                        }}</span>
                                                    </span>
                                                    <span
                                                        v-else-if="
                                                            step.status ===
                                                            'current'
                                                        "
                                                        :href="step.href"
                                                        class="relative flex items-center justify-center"
                                                        aria-current="step"
                                                    >
                                                        <span
                                                            class="absolute w-5 h-5 p-px flex"
                                                            aria-hidden="true"
                                                        >
                                                            <span
                                                                class="w-full h-full rounded-full bg-indigo-200"
                                                            />
                                                        </span>
                                                        <span
                                                            class="relative block w-2.5 h-2.5 bg-indigo-600 rounded-full"
                                                            aria-hidden="true"
                                                        />
                                                        <span class="sr-only">{{
                                                            step.name
                                                        }}</span>
                                                    </span>
                                                    <span
                                                        v-else
                                                        :href="step.href"
                                                        class="block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                                                    >
                                                        <span class="sr-only">{{
                                                            step.name
                                                        }}</span>
                                                    </span>
                                                </a>
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="p-4 py-4 text-right">
                                    <div v-if="currentStep < steps.length">
                                        <div v-if="currentStep == 1">
                                            <JetSecondaryButton
                                                class="bg-indigo-600 text-white font-bold"
                                                autofocus
                                                @click="
                                                    selectStep(currentStep + 1)
                                                ">Let's go
                                                <ArrowSmallRightIcon
                                                        class="w-5"
                                                        aria-hidden="true"
                                                    /></JetSecondaryButton>
                                            
                                        </div>
                                        <div v-else>
                                            <JetSecondaryButton
                                            ref="next"
                                                class="bg-indigo-600 text-white font-bold"
                                                autofocus
                                                @click="
                                                    selectStep(currentStep + 1)
                                                ">Next<ArrowSmallRightIcon
                                                        class="w-5"
                                                        aria-hidden="true"
                                                    /></JetSecondaryButton>
                                            
                                        </div>
                                    </div>
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
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";

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

import {
    ArrowSmallLeftIcon,
    ArrowSmallRightIcon,
} from "@heroicons/vue/24/solid";
import { ArrowTopRightOnSquareIcon } from "@heroicons/vue/24/outline";
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
        JetSecondaryButton,
        ArrowTopRightOnSquareIcon,
        ArrowSmallLeftIcon,
        ArrowSmallRightIcon,
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
    computed: {
        currentStep() {
            return this.steps.find((s) => s.status == "current").id;
        },

        mailFromAddress() {
            return String(this.$page.props.mailFromAddress);
        },

        mailTo() {
            return "mailto:" + String(this.$page.props.mailFromAddress);
        },
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

                    this.$nextTick(() => {
                        this.$refs.next.focus();
                    });
                } else {
                    step.status = "upcoming";
                }
            });
        },
        onboardingComplete() {
            this.open = false;
            axios.post("/onboarding/complete").then((resp) => {
                Inertia.reload({
                    only: ["user", "user.permissions", "user.roles"],
                });
            });
        },
        onboardAndStartTour() {
            this.onboardingComplete();
            this.$tours["appTour"].start();
        },
    },
};
</script>
