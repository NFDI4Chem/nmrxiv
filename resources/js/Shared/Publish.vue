<template>
    <div v-if="project.validation_status">
        <button
            @click="open = true"
            class="ml-4 inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-indigo-700 px-4 py-2 text-base font-medium text-white shadow-sm"
        >
            Publish
        </button>
    </div>
    <div v-else>
        <Link
            :href="route('dashboard.project.validation', [project.id])"
            class="text-gray-900 text-sm font-bold hover:text-blue-700"
        >
            Why can't I publish?
        </Link>
        <button
            :disabled="true"
            class="ml-4 inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-indigo-300 px-4 py-2 text-base font-medium cursor-not-allowed text-white shadow-sm"
        >
            Publish
        </button>
    </div>
    <div>
        <TransitionRoot
            :show="open"
            as="template"
            @after-leave="query = ''"
            appear
        >
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

                <div
                    class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20"
                >
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
                            class="mx-auto max-w-3xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
                        >
                            <div
                                v-if="
                                    status == 'queued' ||
                                    status == 'processing' ||
                                    status == 'complete'
                                "
                            >
                                <div class="py-16">
                                    <div class="text-center">
                                        <p
                                            class="text-sm font-semibold text-indigo-600 uppercase tracking-wide"
                                        >
                                            {{ project.name }}
                                        </p>
                                        <span v-if="status == 'queued'">
                                            <div
                                                class="m-3 relative clear-both border-dotted border-2 border-gray-300 rounded-lg"
                                            >
                                                <span
                                                    class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white dark:bg-slate-800 transition ease-in-out duration-150 cursor-not-allowed dark:ring-slate-200/20"
                                                    disabled=""
                                                    ><h1
                                                        class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                                                    >
                                                        {{ status }}
                                                    </h1></span
                                                >
                                            </div>
                                            <Link
                                                type="button"
                                                :href="route('dashboard')"
                                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            >
                                                Go to Dashboard
                                            </Link>
                                        </span>
                                        <span v-if="status == 'processing'">
                                            <div
                                                class="m-3 relative clear-both border-dotted border-2 border-gray-300 rounded-lg"
                                            >
                                                <span
                                                    class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white dark:bg-slate-800 transition ease-in-out duration-150 cursor-not-allowed dark:ring-slate-200/20"
                                                    disabled=""
                                                    ><h1
                                                        class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                                                    >
                                                        {{ status }}
                                                    </h1></span
                                                ><span
                                                    class="flex absolute h-3 w-3 top-0 right-0 -mt-1 -mr-1"
                                                    ><span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"
                                                    ></span
                                                    ><span
                                                        class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"
                                                    ></span
                                                ></span>
                                            </div>
                                            <Link
                                                type="button"
                                                :href="route('dashboard')"
                                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            >
                                                Go to Dashboard
                                            </Link>
                                        </span>
                                        <span v-if="status == 'complete'">
                                            <div
                                                class="m-3 clear-both relative border-dotted border-2 border-gray-300 rounded-lg"
                                            >
                                                <span
                                                    class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white dark:bg-slate-800 transition ease-in-out duration-150 cursor-not-allowed dark:ring-slate-200/20"
                                                    disabled=""
                                                    ><h1
                                                        class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"
                                                    >
                                                        {{ status }}
                                                    </h1></span
                                                ><span
                                                    class="flex absolute h-3 w-3 top-0 right-0 -mt-1 -mr-1"
                                                    ><span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"
                                                    ></span
                                                    ><span
                                                        class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"
                                                    ></span
                                                ></span>
                                            </div>
                                            <Link
                                                type="button"
                                                :href="route('dashboard')"
                                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            >
                                                Go to Dashboard
                                            </Link>
                                        </span>
                                    </div>
                                </div>
                                <div v-if="status != 'complete'" class="w-full">
                                    <div
                                        class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
                                    >
                                        <b>Whats next?</b>
                                        <div>
                                            <p>
                                                Please allow some time to
                                                process your submission. You
                                                will recieve an email once your
                                                submission is processed. You
                                                will receive an email with
                                                citation details and other
                                                helpful information to share
                                                your datasets.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="p-8">
                                    <div>
                                        <label
                                            class="block tracking-wider text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                                        >
                                            <small>PROJECT NAME</small>
                                        </label>
                                        <h1
                                            class="text-2xl font-extrabold text-gray-900"
                                        >
                                            {{ project.name }}
                                        </h1>
                                    </div>
                                    <div class="mt-3">
                                        <label
                                            class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"
                                        >
                                            Release Date
                                        </label>
                                        <Datepicker
                                            v-model="form.releaseDate"
                                        ></Datepicker>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Publish your data now or choose a
                                            release date to auto publish your
                                            project to public.
                                        </p>
                                    </div>
                                    <div class="mt-5">
                                        <h3
                                            class="text-lg font-bold text-gray-400"
                                        >
                                            Terms & Conditions
                                        </h3>

                                        <div class="mt-3">
                                            <div class="ml-2">
                                                <div class="flex items-top">
                                                    <input
                                                        type="checkbox"
                                                        class="rounded mt-1 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                        id="conditions"
                                                        v-model="
                                                            form.conditions
                                                        "
                                                        name="conditions"
                                                    />
                                                    <div class="ml-2 text-sm">
                                                        I understand once the
                                                        project is published,
                                                        all the underlying
                                                        studies and datasets
                                                        will also be made public
                                                        and agree to make this
                                                        data persistently
                                                        available in this
                                                        location.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="ml-2">
                                                <div class="flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                        id="terms"
                                                        v-model="form.terms"
                                                        name="terms"
                                                    />
                                                    <div class="ml-2 text-sm">
                                                        I agree to the
                                                        <a
                                                            target="_blank"
                                                            :href="
                                                                route(
                                                                    'terms.show'
                                                                )
                                                            "
                                                            class="underline text-sm text-gray-600 hover:text-gray-900"
                                                            >Terms of Service</a
                                                        >
                                                        and
                                                        <a
                                                            target="_blank"
                                                            :href="
                                                                route(
                                                                    'policy.show'
                                                                )
                                                            "
                                                            class="underline text-sm text-gray-600 hover:text-gray-900"
                                                            >Privacy Policy</a
                                                        >
                                                        and hereby also grant
                                                        nmrXiv permissions to
                                                        distribute the datasets
                                                        (and meta-data) under
                                                        the specified license.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="px-8 pb-8 pt-0">
                                        <button
                                            type="button"
                                            :class="[
                                                !form.terms || !form.conditions
                                                    ? 'bg-gray-200 cursor-not-allowed'
                                                    : 'bg-green-600 hover:bg-green-700',
                                                'inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto sm:text-sm',
                                            ]"
                                            @click="publish"
                                            :disabled="
                                                !form.terms && !form.conditions
                                            "
                                        >
                                            Publish
                                        </button>
                                        <button
                                            type="button"
                                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                            @click="open = false"
                                        >
                                            Not right yet
                                        </button>
                                    </div>
                                    <div v-if="errors">
                                        <div
                                            class="rounded-md bg-red-50 p-4 mx-4 mb-4"
                                        >
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <!-- Heroicon name: mini/x-circle -->
                                                    <svg
                                                        class="h-5 w-5 text-red-400"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                        aria-hidden="true"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h3
                                                        class="text-sm font-medium text-red-800"
                                                    >
                                                        Error publishing your
                                                        project
                                                    </h3>
                                                    <div
                                                        class="mt-2 text-sm text-red-700"
                                                    >
                                                        <ul
                                                            role="list"
                                                            class="list-disc space-y-1 pl-5"
                                                        >
                                                            <li>
                                                                {{ errors }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full">
                                    <div
                                        class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"
                                    >
                                        <b>Whats next?</b>
                                        <div>
                                            <p>
                                                Upon clicking publish, your
                                                project is submitted to our
                                                queue system for automatic
                                                processing. Once successfully
                                                processed, your data is assigned
                                                with stable identifiers, and
                                                DOIs are generated. You will
                                                receive an email with citation
                                                details and other helpful
                                                information to share your
                                                datasets.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/20/solid";
import { ChevronRightIcon, UsersIcon } from "@heroicons/vue/24/outline";
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
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
    components: {
        Link,
        MagnifyingGlassIcon,
        ChevronRightIcon,
        UsersIcon,
        Combobox,
        ComboboxInput,
        ComboboxOptions,
        ComboboxOption,
        Dialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
        Datepicker,
    },
    props: ["project"],
    data() {
        return {
            open: ref(false),
            query: ref(""),
            errors: null,
            status: null,
            form: this.$inertia.form({
                _method: "POST",
                conditions: false,
                terms: false,
                releaseDate: this.setReleaseDate(),
            }),
        };
    },
    mounted() {
        if (
            this.project &&
            this.project.status != "" &&
            this.project.status != "complete" &&
            this.project.status != null
        ) {
            this.status = this.project.status;
            this.open = true;
            this.trackProject();
        }
    },
    methods: {
        setReleaseDate() {
            var current_date = new Date();
            var relase_date = new Date();
            relase_date.setDate(current_date.getDate());
            return relase_date;
        },
        publish() {
            if (this.form.conditions && this.form.terms) {
                this.errors = null;
                axios
                    .post(
                        route("dashboard.project.publish", this.project.id),
                        this.form
                    )
                    .catch((err) => {
                        this.errors = err.response.data.errors;
                    })
                    .then((response) => {
                        this.status = response.data.project.status;
                        this.trackProject();
                    });
            }
        },
        trackProject() {
            axios
                .get("/projects/status/" + this.project.id + "/queue")
                .then((response) => {
                    this.status = response.data.status;
                    if (this.status != "complete") {
                        setTimeout(() => this.trackProject(), 10000);
                    } else {
                        return this.$inertia.visit(
                            this.route("dashboard.projects", [this.project.id])
                        );
                    }
                });
        },
    },
};
</script>
