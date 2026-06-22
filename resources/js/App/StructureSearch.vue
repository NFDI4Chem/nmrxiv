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
            v-if="mode == 'button'"
            class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-full text-white bg-gray-900 hover:bg-gray-800 transition-all duration-200"
            @click="openDialog(true)"
        >
            <MagnifyingGlassIcon class="h-5 w-5 mr-2" aria-hidden="true" />
            Search Structure
        </button>
        <TransitionRoot
            :show="open"
            as="template"
            appear
            @after-leave="query = ''"
        >
            <HeadlessUiDialog
                as="div"
                class="relative z-10"
                @close="open = false"
            >
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
                        class="fixed inset-0 bg-gray-500/40 backdrop-blur-xl transition-opacity"
                    />
                </TransitionChild>

                <div
                    class="fixed inset-0 z-10 flex items-center justify-center p-4"
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
                            class="mx-auto flex h-[90vh] w-[95vw] max-w-6xl transform flex-col overflow-hidden rounded-3xl bg-white shadow-2xl transition-all"
                        >
                            <!-- Main Content -->
                            <div class="min-h-0 flex-1 overflow-hidden">
                                <div
                                    class="mx-auto flex h-full min-h-0 max-w-6xl flex-col px-4 py-4"
                                >
                                    <StructureEditorContent
                                        v-model:search-type="type"
                                        editor-id="structureSearchEditor"
                                        @ready="onStructureEditorReady"
                                    />
                                </div>
                            </div>

                            <!-- Footer with Actions -->
                            <div
                                class="flex shrink-0 items-center justify-between border-t border-gray-100 bg-gray-50 px-4 py-4"
                            >
                                <a
                                    href="#"
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                    @click.prevent="openDialog(false)"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
                                    @click.prevent="search"
                                >
                                    Search
                                </a>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </HeadlessUiDialog>
        </TransitionRoot>
    </div>
</template>

<script>
import { ref } from "vue";
import { loadOpenChemLib } from "@/Utils/structureEditor";
import {
    Dialog as HeadlessUiDialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { BeakerIcon, MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import StructureEditorContent from "@/Shared/StructureEditorContent.vue";

export default {
    components: {
        HeadlessUiDialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
        BeakerIcon,
        MagnifyingGlassIcon,
        StructureEditorContent,
    },
    props: {
        mode: {
            type: String,
            default: "icon",
        },
    },
    data() {
        return {
            editor: null,
            open: ref(false),
            query: ref(""),
            smiles: null,
            type: "exact",
        };
    },
    computed: {},
    mounted() {},
    methods: {
        async onStructureEditorReady(editor) {
            this.editor = editor;

            const url = new URL(window.location.href);
            const querySmiles = url.searchParams.get("query");
            const queryType = url.searchParams.get("type");

            if (
                queryType &&
                ["exact", "substructure", "similarity"].includes(queryType)
            ) {
                this.type = queryType;
            }

            if (querySmiles) {
                try {
                    const OCL = await loadOpenChemLib();
                    this.editor.setMolFile(
                        OCL.Molecule.fromSmiles(
                            decodeURIComponent(querySmiles)
                        ).toMolfile()
                    );
                } catch (error) {
                    console.error("Error loading structure from query:", error);
                }
            }
        },
        openDialog(value) {
            this.open = value;
        },
        search() {
            this.$page.props.query = this.editor.getSmiles();
            this.$page.props.queryType = this.type;

            const params = new URLSearchParams({
                scope: "compounds",
                query: this.editor.getSmiles(),
                type: this.type,
            });
            window.location = `/search?${params.toString()}`;
        },
    },
};
</script>
