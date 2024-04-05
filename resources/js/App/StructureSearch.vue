<template>
    <div>
        <button
            v-if="mode == 'icon'"
            type="button"
            class="px-4 shadow py-3 rounded-full bg-white p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2 tooltip"
            @click="openDialog(true)"
        >
            <BeakerIcon
                class="mr-3 ml-2 h-6 w-6 text-teal-300"
                aria-hidden="true"
            />
            <span
                class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"
                >Structure Search</span
            >
        </button>
        <button
            @click="openDialog(true)"
            class="flex items-center justify-center px-4 py-3 border border-1 text-base font-medium rounded-full shadow-sm text-white bg-teal-500 sm:px-8"
            v-if="mode == 'button'"
        >
            <MagnifyingGlassIcon class="h-5 w-5" aria-hidden="true" />&nbsp;
            Search Structure
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
                                    Search Structure
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
import { BeakerIcon, MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import ToolTip from "@/Shared/ToolTip.vue";

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
        BeakerIcon,
        MagnifyingGlassIcon,
        ToolTip,
    },
    props: {
        mode: {
            type: String,
            default: "icon",
        },
    },
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
