<template>
    <jet-dialog-modal
        :show="createStudyDialog"
        @close="createStudyDialog = false"
    >
        <template #title>
            {{ project.name }} <span class="text-gray-400">></span> New Study
        </template>

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
                                v-model="createStudyForm.name"
                                type="text"
                                name="name"
                                autocomplete="off"
                                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                            />
                        </div>
                        <jet-input-error
                            :message="createStudyForm.errors.name"
                            class="mt-2"
                        />
                    </div>
                    <div class="sm:col-span-6">
                        <TabGroup v-slot="{ $selectedIndex }">
                            <TabList class="flex items-center">
                                <Tab
                                    v-slot="{ selected }"
                                    as="template"
                                    class="after:content-['*'] after:ml-0.5 after:text-red-500"
                                >
                                    <button
                                        :class="[
                                            selected
                                                ? 'text-gray-900 bg-gray-100 hover:bg-gray-200'
                                                : 'text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100',
                                            'px-3 py-1.5 border border-transparent text-sm font-medium rounded-md',
                                        ]"
                                    >
                                        Write
                                    </button>
                                </Tab>
                                <Tab v-slot="{ selected }" as="template">
                                    <button
                                        :class="[
                                            selected
                                                ? 'text-gray-900 bg-gray-100 hover:bg-gray-200'
                                                : 'text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100',
                                            'ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md',
                                        ]"
                                    >
                                        Preview
                                    </button>
                                </Tab>
                            </TabList>
                            <TabPanels class="mt-2">
                                <TabPanel class="p-0.5 -m-0.5 rounded-lg">
                                    <label for="comment" class="sr-only"
                                        >Comment</label
                                    >
                                    <div>
                                        <textarea
                                            id="description"
                                            v-model="
                                                createStudyForm.description
                                            "
                                            name="description"
                                            placeholder="Describe this sample in at least 20 characters."
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
                                                    createStudyForm.description ==
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
                                                            createStudyForm.description
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
                            :message="createStudyForm.errors.description"
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
                                        createStudyForm.description =
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
                                v-model="createStudyForm.is_public"
                                :class="[
                                    createStudyForm.is_public
                                        ? 'bg-green-600'
                                        : 'bg-gray-200',
                                    'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500',
                                ]"
                            >
                                <span
                                    :class="[
                                        createStudyForm.is_public
                                            ? 'translate-x-5'
                                            : 'translate-x-0',
                                        'pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            createStudyForm.is_public
                                                ? 'opacity-0 ease-out duration-100'
                                                : 'opacity-100 ease-in duration-200',
                                            'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity',
                                        ]"
                                        aria-hidden="true"
                                    >
                                        <svg
                                            id="Capa_1"
                                            class="h-3 w-3 text-gray-400 inline"
                                            version="1.1"
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                            x="0px"
                                            y="0px"
                                            viewBox="0 0 512 512"
                                            style="
                                                enable-background: new 0 0 512
                                                    512;
                                            "
                                            xml:space="preserve"
                                        >
                                            <g>
                                                <g>
                                                    <path
                                                        d="M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32
                                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333
                                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292
                                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51
                                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667
                                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64
                                                s85.333,38.281,85.333,85.333V192z"
                                                    />
                                                </g>
                                            </g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                        </svg>
                                    </span>
                                    <span
                                        :class="[
                                            createStudyForm.is_public
                                                ? 'opacity-100 ease-in duration-200'
                                                : 'opacity-0 ease-out duration-100',
                                            'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity',
                                        ]"
                                        aria-hidden="true"
                                    >
                                        <svg
                                            class="h-3 w-3 text-green-400 inline"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 64 64"
                                            width="512"
                                            height="512"
                                        >
                                            <g id="globe">
                                                <path
                                                    d="M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z"
                                                />
                                                <path
                                                    d="M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z"
                                                />
                                                <path
                                                    d="M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z"
                                                />
                                            </g>
                                        </svg>
                                    </span>
                                </span>
                            </Switch>
                            <span class="flex-grow flex ml-4 flex-col">
                                <span v-if="!createStudyForm.is_public">
                                    <SwitchLabel
                                        as="span"
                                        class="text-sm font-medium text-gray-900"
                                        passive
                                    >
                                        Private</SwitchLabel
                                    >
                                </span>
                                <span v-else>
                                    <SwitchLabel
                                        as="span"
                                        class="text-sm font-medium text-gray-900"
                                        passive
                                        >Public</SwitchLabel
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
                            v-model:selected="createStudyForm.license"
                            label="License"
                            :items="licenses"
                        />
                        <jet-input-error
                            :message="createStudyForm.errors.license"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="toggleCreateStudyDialog">
                Cancel
            </jet-secondary-button>

            <jet-button
                class="ml-2"
                :class="{ 'opacity-25': createStudyForm.processing }"
                :disabled="createStudyForm.processing"
                @click="createStudy"
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
import { CheckCircleIcon, ChevronRightIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import {
    AtSymbolIcon,
    CodeBracketIcon,
    LinkIcon,
} from "@heroicons/vue/24/solid";
import JetInputError from "@/Jetstream/InputError.vue";
import { ref } from "vue";
import {
    Switch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
} from "@headlessui/vue";
import SelectRich from "@/Shared/SelectRich.vue";
import { inject } from "vue";

export default {
    components: {
        Tab,
        TabGroup,
        TabList,
        TabPanel,
        TabPanels,
        Switch,
        SwitchDescription,
        SwitchGroup,
        SwitchLabel,
        AtSymbolIcon,
        CodeBracketIcon,
        LinkIcon,
        JetDialogModal,
        JetSecondaryButton,
        JetButton,
        Link,
        CheckCircleIcon,
        ChevronRightIcon,
        JetInputError,
        SelectRich,
    },

    props: ["project"],

    data() {
        return {
            createStudyForm: this.$inertia.form({
                _method: "POST",
                name: "",
                description: "",
                error_message: null,
                team_id: null,
                owner_id: null,
                color: null,
                starred: null,
                project_id: this.project ? this.project.id : null,
                is_public: ref(false),
                license: null,
            }),
            createStudyDialog: false,
            licenses: [],
        };
    },

    mounted() {
        this.emitter.on("openStudyCreateDialog", () => {
            this.createStudyDialog = true;
        });
    },
    methods: {
        createStudy() {
            this.createStudyForm.owner_id = this.$page.props.auth.user.id;
            this.createStudyForm.team_id =
                this.$page.props.auth.user.current_team.id;
            this.createStudyForm.project_id = this.project.id;
            this.createStudyForm.post(route("dashboard.study.create"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.createStudyDialog = false;
                    this.createStudyForm.reset();
                },
                onError: (err) => console.error(err),
            });
        },
        toggleCreateStudyDialog() {
            this.createStudyDialog = !this.createStudyDialog;
            this.createStudyForm.clearErrors();
            if (this.$page.props.licenses) {
                this.licenses = this.$page.props.licenses;
                this.createStudyForm.license = this.licenses.find(
                    (l) => l.id == this.project.license_id
                );
            } else {
                axios.get(route("licenses")).then((res) => {
                    this.licenses = res.data;
                    this.$page.props.licenses = res.data;
                    this.createStudyForm.license = this.licenses.find(
                        (l) => l.id == this.project.license_id
                    );
                });
            }
        },
    },
};
</script>
