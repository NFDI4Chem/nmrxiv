<template>
    <div>
        <button
            type="button"
            class="px-4 shadow py-3 rounded-full bg-white p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"
            @click="openDialog(true)"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
                class="mr-3 ml-2 h-6 w-6"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"
                ></path>
            </svg>
        </button>
        <TransitionRoot
            :show="open"
            as="template"
            @after-leave="query = ''"
            appear
        >
            <Dialog as="div" class="relative z-10" @close="open = false">
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
                            class="mx-auto max-w-3xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
                        >
                            <div
                                class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6"
                            >
                                <h3
                                    class="text-lg font-medium leading-6 text-gray-900"
                                >
                                    Structure Search
                                </h3>
                                <!-- <p class="mt-1 text-sm text-gray-500">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit quam corrupti
                  consectetur.
                </p> -->
                            </div>
                            <div class="p-4">
                                <div
                                    id="structureSearchEditor"
                                    class="w-full border rounded-md"
                                    style="height: 400px"
                                />
                                <div class="mt-6">
                                    <fieldset class="mt-6">
                                        <legend
                                            class="contents text-base font-medium text-gray-900"
                                        >
                                            Select search type
                                        </legend>
                                        <!-- <p class="text-sm text-gray-500">
                      These are delivered via SMS to your mobile phone.
                    </p> -->
                                        <div class="mt-4 space-y-4">
                                            <div class="flex items-center">
                                                <label
                                                    for="search-type-exact"
                                                    class="block cursor-pointer text-sm font-medium text-gray-700"
                                                >
                                                    <input
                                                        id="search-type-exact"
                                                        name="search-type"
                                                        v-model="type"
                                                        value="exact"
                                                        type="radio"
                                                        class="mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                                    />
                                                    Exact match</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <label
                                                    for="search-type-sub"
                                                    class="block cursor-pointer text-sm font-medium text-gray-700"
                                                    ><input
                                                        id="search-type-sub"
                                                        name="search-type"
                                                        v-model="type"
                                                        type="radio"
                                                        value="substructure"
                                                        class="mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                                    />Substructure Search</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <label
                                                    for="search-type-similar"
                                                    class="block cursor-pointer text-sm font-medium text-gray-700"
                                                    ><input
                                                        id="search-type-similar"
                                                        v-model="type"
                                                        name="search-type"
                                                        value="similarity"
                                                        type="radio"
                                                        class="mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                                    />Similarity Search
                                                    (tanimoto_threshold=0.5)</label
                                                >
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex justify-end">
                                    <button
                                        @click="openDialog(false)"
                                        type="button"
                                        class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        class="ml-3 inline-flex justify-center rounded-md bg-gray-700 text-white py-2 px-4 text-sm font-medium shadow-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"
                                        @click="search"
                                    >
                                        Search
                                    </button>
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
import { ref } from "vue";
import OCL from "openchemlib/full";
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

export default {
    components: {
        Combobox,
        ComboboxInput,
        ComboboxOptions,
        ComboboxOption,
        Dialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
    },
    props: [],
    computed: {},
    data() {
        return {
            editor: "",
            open: ref(false),
            query: ref(""),
            smiles: null,
            type: "exact",
        };
    },
    mounted() {},
    methods: {
        openDialog(value) {
            this.open = value;
            if (value) {
                this.$nextTick(() => {
                    this.editor = OCL.StructureEditor.createSVGEditor(
                        "structureSearchEditor",
                        1
                    );
                });
            }
        },
        search() {
            this.$page.props.query = this.editor.getSmiles();
            this.$page.props.queryType = this.type;

            window.location =
                "/compounds/?query=" +
                encodeURI(this.editor.getSmiles()) +
                "&type=" +
                this.type;
        },
    },
};
</script>
