<template>
    <jet-dialog-modal
        :show="createProjectDialog"
        @close="createProjectDialog = false"
    >
        <template #title> New Project </template>

        <template #content>
            <div class="relative z-0 mt-1 rounded-lg cursor-pointer">
                <div
                    class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6"
                >
                    <div class="sm:col-span-6">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        >
                            Name
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input
                                id="name"
                                v-model="createProjectForm.name"
                                type="text"
                                name="name"
                                autocomplete="off"
                                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                            />
                        </div>
                        <jet-input-error
                            :message="createProjectForm.errors.name"
                            class="mt-2"
                        />
                    </div>
                    <div class="sm:col-span-6">
                        <TabGroup v-slot="{ $selectedIndex }">
                            <description-tabs></description-tabs>
                            <TabPanels class="mt-2">
                                <TabPanel class="p-0.5 -m-0.5 rounded-lg">
                                    <label for="comment" class="sr-only"
                                        >Comment</label
                                    >
                                    <div>
                                        <textarea
                                            id="description"
                                            v-model="
                                                createProjectForm.description
                                            "
                                            name="description"
                                            placeholder="Describe this project in atleast 20 characters."
                                            rows="3"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel class="p-0.5 -m-0.5 rounded-lg">
                                    <div class="border-b">
                                        <div
                                            class="mx-px mt-px px-3 pt-2 pb-12"
                                        >
                                            <span
                                                v-if="
                                                    createProjectForm.description ==
                                                    ''
                                                "
                                                class="text-gray-400 text-sm font-medium"
                                            >
                                                Nothing to preview
                                            </span>
                                            <span v-else>
                                                <div
                                                    class="prose"
                                                    v-html="
                                                        md(
                                                            createProjectForm.description
                                                        )
                                                    "
                                                ></div>
                                            </span>
                                        </div>
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                        <jet-input-error
                            :message="createProjectForm.errors.description"
                            class="mt-2"
                        />
                        <label class="block text-sm font-medium text-gray-700"
                            ><small
                                ><svg
                                    aria-hidden="true"
                                    height="16"
                                    viewBox="0 0 16 16"
                                    version="1.1"
                                    width="16"
                                    data-view-component="true"
                                    class="octicon octicon-markdown v-align-bottom inline"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.69C0 12.48.52 13 1.15 13h13.69c.64 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"
                                    ></path>
                                </svg>
                                Styling with Markdown is
                                <span
                                    @click="
                                        createProjectForm.description =
                                            'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
                                    "
                                    >supported</span
                                ></small
                            >
                        </label>
                    </div>
                    <div class="sm:col-span-6">
                        <SwitchGroup as="div" class="flex items-center">
                            <Switch
                                v-model="createProjectForm.is_public"
                                :class="[
                                    createProjectForm.is_public
                                        ? 'bg-green-600'
                                        : 'bg-gray-200',
                                    'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500',
                                ]"
                            >
                                <span
                                    :class="[
                                        createProjectForm.is_public
                                            ? 'translate-x-5'
                                            : 'translate-x-0',
                                        'pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            createProjectForm.is_public
                                                ? 'opacity-0 ease-out duration-100'
                                                : 'opacity-100 ease-in duration-200',
                                            'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity',
                                        ]"
                                        aria-hidden="true"
                                    >
                                        <LockClosedIcon
                                            class="h-3 w-3 text-gray-400 inline"
                                        />
                                    </span>
                                    <span
                                        :class="[
                                            createProjectForm.is_public
                                                ? 'opacity-100 ease-in duration-200'
                                                : 'opacity-0 ease-out duration-100',
                                            'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity',
                                        ]"
                                        aria-hidden="true"
                                    >
                                        <GlobeEuropeAfricaIcon
                                            class="h-3 w-3 text-gray-400 inline"
                                        />
                                    </span>
                                </span>
                            </Switch>
                            <span class="flex-grow flex ml-4 flex-col">
                                <span v-if="!createProjectForm.is_public">
                                    <SwitchLabel
                                        as="span"
                                        class="text-sm font-medium text-gray-900"
                                        passive
                                    >
                                        Private</SwitchLabel
                                    ><br />
                                    <SwitchDescription
                                        as="div"
                                        class="text-xs text-gray-500 leading-none pr-4"
                                        >Visibility of the project only, not the
                                        underlying studies</SwitchDescription
                                    >
                                </span>
                                <span v-else>
                                    <SwitchLabel
                                        as="span"
                                        class="text-sm font-medium text-gray-900"
                                        passive
                                        >Public</SwitchLabel
                                    ><br />
                                    <SwitchDescription
                                        as="div"
                                        class="text-xs text-gray-500 leading-none pr-4"
                                        >Visibility of the project only, not the
                                        underlying studies</SwitchDescription
                                    >
                                </span>
                            </span>
                        </SwitchGroup>
                    </div>
                </div>
                <div
                    class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-1"
                >
                    <div>
                        <select-rich
                            v-model:selected="createProjectForm.license"
                            label="License"
                            :items="licenses"
                        />
                        <jet-input-error
                            :message="createProjectForm.errors.license"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="toggleCreateProjectDialog">
                Cancel
            </jet-secondary-button>

            <jet-button
                class="ml-2"
                :class="{ 'opacity-25': createProjectForm.processing }"
                :disabled="createProjectForm.processing"
                @click="createProject"
            >
                Save
            </jet-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import { LockClosedIcon, GlobeEuropeAfricaIcon } from "@heroicons/vue/24/solid";

import { Link } from "@inertiajs/inertia-vue3";
import {
    Tab,
    TabGroup,
    TabPanel,
    TabPanels,
    Switch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
} from "@headlessui/vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { ref } from "vue";

import SelectRich from "@/Shared/SelectRich.vue";
import axios from "axios";
import DescriptionTabs from "@/Shared/DescriptionTabs.vue";
export default {
    components: {
        Tab,
        TabGroup,
        TabPanel,
        TabPanels,
        Switch,
        SwitchDescription,
        SwitchGroup,
        SwitchLabel,
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        Link,
        JetInputError,
        SelectRich,
        GlobeEuropeAfricaIcon,
        LockClosedIcon,
        DescriptionTabs,
    },

    props: [],

    data() {
        return {
            createProjectForm: this.$inertia.form({
                _method: "POST",
                name: "",
                description: "",
                error_message: null,
                team_id: null,
                owner_id: null,
                color: null,
                starred: null,
                is_public: ref(false),
                license: null,
            }),
            createProjectDialog: false,
            licenses: [],
        };
    },

    mounted() {
        const initialise = () => {
            this.toggleCreateProjectDialog();
        };
        this.emitter.all.set("openProjectCreateDialog", [initialise]);
    },

    methods: {
        createProject() {
            this.createProjectForm.owner_id = this.$page.props.user.id;
            this.createProjectForm.team_id =
                this.$page.props.user.current_team.id;
            this.createProjectForm.post(route("dashboard.project.create"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.createProjectDialog = false;
                    this.createProjectForm.reset();
                },
                onError: (err) => console.error(err),
            });
        },
        toggleCreateProjectDialog() {
            this.createProjectDialog = !this.createProjectDialog;
            this.createProjectForm.clearErrors();
            if (this.$page.props.licenses) {
                this.licenses = this.$page.props.licenses;
            } else {
                axios.get(route("licenses")).then((res) => {
                    this.licenses = res.data;
                    this.$page.props.licenses = res.data;
                });
            }
        },
    },
};
</script>
