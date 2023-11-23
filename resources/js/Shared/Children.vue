<template>
    <span v-if="file" class="text-sm">
        <nav class="flex-1 space-y-0" aria-label="Sidebar">
            <Disclosure
                v-slot="{ open = true }"
                as="div"
                :default-open="file.name == '/'"
                class="space-y-1"
            >
                <div
                    style="user-select: none"
                    :class="[
                        $page.props.selectedFileSystemObject &&
                        $page.props.selectedFileSystemObject.relative_url ==
                            file.relative_url
                            ? 'cursor-pointer bg-gray-100'
                            : 'cursor-pointer text-gray-600',
                        'group w-full flex items-center pr-2 py-1 text-left font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                    ]"
                    @click.stop="displaySelected(file)"
                >
                    <DisclosureButton class="w-full text-left truncate ...">
                        <span v-if="file.loading">
                            <svg
                                class="animate-spin mr-3 ml-1 h-5 w-5 text-dark inline"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                        </span>
                        <span v-else>
                            <svg
                                :class="[
                                    open
                                        ? 'text-gray-700 rotate-90'
                                        : 'text-gray-300',
                                    'mr-2 flex-shrink-0 inline h-5 w-5 transform group-hover:text-gray-700 transition-colors ease-in-out duration-150',
                                ]"
                                viewBox="0 0 20 20"
                                aria-hidden="true"
                            >
                                <path
                                    d="M6 6L14 10L6 14V6Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </span>
                        <span
                            :class="[
                                file.status == 'missing' ? 'text-red-800' : '',
                            ]"
                        >
                            <span
                                v-if="file.type == 'directory'"
                                v-html="composeIcon(file)"
                            ></span>
                            <span v-else>
                                <FolderIcon
                                    class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700"
                                    aria-hidden="true"
                                />
                            </span>
                            {{ file.name }}
                        </span>
                    </DisclosureButton>
                </div>
                <DisclosurePanel class="space-y-1">
                    <span v-for="sfile in file.children" :key="sfile.name">
                        <div class="ml-2">
                            <div
                                :class="[
                                    sfile.current
                                        ? 'text-gray-900'
                                        : 'cursor-pointer text-gray-600',
                                    'cursor-pointer group w-full flex items-center font-medium rounded-md',
                                ]"
                                @click.stop="displaySelected(sfile)"
                            >
                                <span v-if="sfile.type == 'directory'">
                                    <span v-if="sfile.has_children">
                                        <Disclosure
                                            v-slot="{ open }"
                                            as="div"
                                            class="space-y-1"
                                        >
                                            <div
                                                :class="[
                                                    $page.props
                                                        .selectedFileSystemObject &&
                                                    $page.props
                                                        .selectedFileSystemObject
                                                        .relative_url ==
                                                        sfile.relative_url
                                                        ? 'cursor-pointer bg-gray-100 text-gray-900'
                                                        : 'cursor-pointer text-gray-600',
                                                    'group w-full flex pr-1 py-1 text-left font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                                                ]"
                                                @click.stop="
                                                    displaySelected(sfile)
                                                "
                                            >
                                                <DisclosureButton
                                                    class="w-full text-left truncate ..."
                                                >
                                                    <span v-if="sfile.loading">
                                                        <svg
                                                            class="animate-spin ml-1 mr-3 h-5 w-5 text-dark inline"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <circle
                                                                class="opacity-25"
                                                                cx="12"
                                                                cy="12"
                                                                r="10"
                                                                stroke="currentColor"
                                                                stroke-width="4"
                                                            ></circle>
                                                            <path
                                                                class="opacity-75"
                                                                fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                            ></path>
                                                        </svg>
                                                    </span>
                                                    <span v-else>
                                                        <svg
                                                            :class="[
                                                                open
                                                                    ? 'text-gray-700 rotate-90'
                                                                    : 'text-gray-300',
                                                                'mr-2 flex-shrink-0 inline h-5 w-5 transform group-hover:text-gray-700 transition-colors ease-in-out duration-150',
                                                            ]"
                                                            viewBox="0 0 20 20"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                d="M6 6L14 10L6 14V6Z"
                                                                fill="currentColor"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <span>
                                                        <span
                                                            :class="[
                                                                sfile.status ==
                                                                'missing'
                                                                    ? 'text-red-800'
                                                                    : '',
                                                                '',
                                                            ]"
                                                            style="
                                                                user-select: none;
                                                            "
                                                        >
                                                            <span
                                                                v-if="
                                                                    sfile.type ==
                                                                    'directory'
                                                                "
                                                                v-html="
                                                                    composeIcon(
                                                                        sfile
                                                                    )
                                                                "
                                                            >
                                                            </span>
                                                            <span v-else>
                                                                <DocumentTextIcon
                                                                    class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700"
                                                                    aria-hidden="true"
                                                                />
                                                            </span>
                                                            {{ sfile.name }}
                                                        </span>
                                                    </span>
                                                </DisclosureButton>
                                            </div>
                                            <DisclosurePanel class="space-y-0">
                                                <div
                                                    v-for="subItem in sfile.children"
                                                    :key="subItem.name"
                                                    as="div"
                                                    class="cursor-pointer group w-full flex pl-4 pr-2 py-0 font-medium text-gray-600 rounded-md"
                                                    @click.stop="
                                                        displaySelected(subItem)
                                                    "
                                                >
                                                    <span
                                                        v-if="
                                                            subItem.type ==
                                                            'directory'
                                                        "
                                                    >
                                                        <children
                                                            :file="subItem"
                                                            :files="
                                                                subItem.children
                                                            "
                                                            :study="study"
                                                            :project="project"
                                                        ></children>
                                                    </span>
                                                    <span
                                                        v-else
                                                        :class="[
                                                            $page.props
                                                                .selectedFileSystemObject &&
                                                            $page.props
                                                                .selectedFileSystemObject
                                                                .relative_url ==
                                                                subItem.relative_url
                                                                ? 'cursor-pointer bg-gray-100'
                                                                : 'cursor-pointer bg-white text-gray-600',
                                                            'p-1 rounded-md truncate ...',
                                                        ]"
                                                    >
                                                        <span
                                                            :class="[
                                                                subItem.status ==
                                                                'missing'
                                                                    ? 'text-red-800'
                                                                    : '',
                                                            ]"
                                                        >
                                                            <DocumentTextIcon
                                                                class="mr-1 inline h-5 w-5 text-gray-700"
                                                                aria-hidden="true"
                                                            />
                                                            {{ subItem.name }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </DisclosurePanel>
                                        </Disclosure>
                                    </span>
                                </span>
                                <span
                                    v-else
                                    :class="[
                                        $page.props.selectedFileSystemObject &&
                                        $page.props.selectedFileSystemObject
                                            .relative_url == sfile.relative_url
                                            ? 'cursor-pointer bg-gray-100'
                                            : 'cursor-pointer bg-white text-gray-600',
                                        'p-1 ml-5 rounded-md truncate ...',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            sfile.status == 'missing'
                                                ? 'text-red-800'
                                                : '',
                                        ]"
                                    >
                                        <DocumentTextIcon
                                            class="inline mr-1 h-5 w-5 text-gray-700"
                                            aria-hidden="true"
                                        />
                                        {{ sfile.name }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </span>
                </DisclosurePanel>
            </Disclosure>
        </nav>
    </span>
</template>

<script>
import StudyContent from "@/Pages/Study/Content.vue";
import { FolderIcon, DocumentTextIcon } from "@heroicons/vue/24/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

export default {
    components: {
        StudyContent,
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
        FolderIcon,
        DocumentTextIcon,
    },
    props: ["study", "project", "file"],
    setup() {
        return {};
    },
    data() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {
        optionClicked1(event) {
            window.alert(JSON.stringify(event));
        },
        handleClick1(event, item) {
            this.$refs.vueSimpleContextMenu1.showMenu(event, item);
        },
        composeIcon(file) {
            if (file.instrument_type) {
                if (file.instrument_type == "bruker") {
                    return '<img class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700 border rounded-md" src="/img/bruker.jpg" alt=""/>';
                } else if (file.instrument_type == "varian") {
                    return '<img class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700 border rounded-md" src="/img/varian.jpeg" alt=""/>';
                } else if (file.instrument_type == "joel") {
                    return '<img class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700 border rounded-md" src="/img/joel.jpg" alt=""/>';
                } else if (file.instrument_type == "jcamp") {
                    return '<img class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700" src="/img/jcamp.png" alt=""/>';
                }
            }

            if (file.model_type == "study") {
                return '<span class="relative inline-flex"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg><span class="flex absolute h-2 w-2 top-0 right-0"><span class="absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span></span></span>';
            }

            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>';
        },
        displaySelected(file) {
            this.$page.props.selectedFileSystemObject = file;
            let sFolder = "/";
            if (this.$page.props.selectedFileSystemObject.name == "/") {
                sFolder = "/";
            } else {
                if (this.$page.props.selectedFileSystemObject.type != "file") {
                    sFolder =
                        this.$page.props.selectedFileSystemObject.relative_url;
                } else {
                    if (
                        this.$page.props.selectedFileSystemObject.parent_id ==
                        null
                    ) {
                        sFolder = "/";
                    } else {
                        sFolder =
                            this.$page.props.selectedFileSystemObject.relative_url.replace(
                                "/" +
                                    this.$page.props.selectedFileSystemObject
                                        .name,
                                ""
                            );
                    }
                }
            }

            this.$page.props.selectedFolder = sFolder;

            if (file.has_children && file.level > 0 && !file.children) {
                file.loading = true;
                axios
                    .get("/api/v1/files/children/" + file.id)
                    .then((response) => {
                        file.children = response.data.files[0].children;
                        file.loading = false;
                    });
            }
        },
    },
};
</script>
