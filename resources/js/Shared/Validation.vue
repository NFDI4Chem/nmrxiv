<template>
    <div>
        <h1 class="text-xl font-extrabold text-gray-400">
            Here’s how your project compares to
            <a
                target="_blank"
                class="text-blue-800"
                href="https://www.nfdi4chem.de/index.php/reports-from-minimum-information-standards-workshops"
                >recommended community standards</a
            >.
        </h1>
        <div
            class="items-center text-sm mt-6 mb-3 text-gray-700 uppercase font-bold tracking-widest"
        >
            Check list
            <div class="float-right">
                <small>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5 inline text-red-700 mr-1"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        /></svg
                    >Missing
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5 inline text-yellow-700 mr-1 ml-3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                        /></svg
                    >Optional
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5 inline text-green-700 mr-1 ml-3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    Complete</small
                >
            </div>
        </div>
        <div class="overflow-hidden bg-white shadow sm:rounded-md">
            <ul
                v-if="validation"
                role="list"
                class="divide-y border-t divide-gray-200"
            >
                <li>
                    <a target="_blank" class="block">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center border-b">
                                <ValidationStatus
                                    :status="validation.project.status"
                                ></ValidationStatus>
                                <p
                                    class="truncate text-md font-bold text-indigo-600"
                                >
                                    Project
                                </p>
                            </div>
                            <div
                                class="flex items-center mt-2 hover:bg-gray-100 rounded-md px-2"
                            >
                                <ValidationStatus
                                    :status="validation.project.title"
                                ></ValidationStatus>
                                <p
                                    class="truncate text-md font-bold text-dark-600"
                                >
                                    Title
                                </p>
                                <a
                                    target="_blank"
                                    v-if="!getStatus(validation.project.title)"
                                    :href="
                                        route(
                                            'dashboard.projects',
                                            project.id
                                        ) + '?edit=title'
                                    "
                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                >
                                    Edit
                                </a>
                            </div>
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <ValidationStatus
                                    :status="validation.project.description"
                                ></ValidationStatus>
                                <p
                                    class="truncate text-md font-bold text-dark-600"
                                >
                                    Description
                                </p>
                                <a
                                    target="_blank"
                                    :href="
                                        route(
                                            'dashboard.projects',
                                            project.id
                                        ) + '?edit=description'
                                    "
                                    v-if="
                                        !getStatus(
                                            validation.project.description
                                        )
                                    "
                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                >
                                    Edit
                                </a>
                            </div>
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <ValidationStatus
                                    :status="validation.project.keywords"
                                ></ValidationStatus>
                                <p
                                    class="truncate text-md font-bold text-dark-600"
                                >
                                    Keywords
                                </p>
                                <a
                                    target="_blank"
                                    :href="
                                        route(
                                            'dashboard.projects',
                                            project.id
                                        ) + '?edit=keywords'
                                    "
                                    v-if="
                                        !getStatus(validation.project.keywords)
                                    "
                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                >
                                    Edit
                                </a>
                            </div>
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <ValidationStatus
                                    :status="validation.project.citations"
                                ></ValidationStatus>
                                <p
                                    class="truncate text-md font-bold text-dark-600"
                                >
                                    Citations
                                </p>
                                <a
                                    target="_blank"
                                    :href="
                                        route(
                                            'dashboard.projects',
                                            project.id
                                        ) + '?edit=citation'
                                    "
                                    v-if="
                                        !getStatus(validation.project.citations)
                                    "
                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                >
                                    Edit
                                </a>
                            </div>
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <ValidationStatus
                                    :status="validation.project.authors"
                                ></ValidationStatus>
                                <p
                                    class="truncate text-md font-bold text-dark-600"
                                >
                                    Authors
                                </p>
                                <a
                                    target="_blank"
                                    :href="
                                        route(
                                            'dashboard.projects',
                                            project.id
                                        ) + '?edit=authors'
                                    "
                                    v-if="
                                        !getStatus(validation.project.authors)
                                    "
                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                >
                                    Edit
                                </a>
                            </div>
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <ValidationStatus
                                    :status="validation.project.license"
                                ></ValidationStatus>
                                <p
                                    class="truncate text-md font-bold text-dark-600"
                                >
                                    License
                                </p>
                                <a
                                    target="_blank"
                                    :href="
                                        route(
                                            'dashboard.projects',
                                            project.id
                                        ) + '?edit=license'
                                    "
                                    v-if="
                                        !getStatus(validation.project.license)
                                    "
                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                >
                                    Edit
                                </a>
                            </div>
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <ValidationStatus
                                    :status="validation.project.image"
                                ></ValidationStatus>
                                <p
                                    class="truncate text-md font-bold text-dark-600"
                                >
                                    Project profile image
                                </p>
                                <a
                                    target="_blank"
                                    :href="
                                        route(
                                            'dashboard.projects',
                                            project.id
                                        ) + '?edit=profile_image'
                                    "
                                    v-if="!getStatus(validation.project.image)"
                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                >
                                    Edit
                                </a>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a target="_blank" class="block">
                        <div class="px-4 py-4 sm:px-6">
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <p
                                    class="truncate text-md font-bold text-indigo-600"
                                >
                                    Studies
                                </p>
                            </div>
                            <div
                                v-for="study in validation.project.studies"
                                :key="study.id"
                            >
                                <div>
                                    <div class="mx-auto w-full py-2">
                                        <Disclosure v-slot="{ open }">
                                            <DisclosureButton
                                                :class="[
                                                    study.status
                                                        ? 'bg-gray-100 hover:bg-gray-200'
                                                        : 'bg-red-100 hover:bg-red-200',
                                                    'flex w-full rounded-md px-4 py-2 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75',
                                                ]"
                                            >
                                                <ValidationStatus
                                                    class="-my-2"
                                                    :status="study.status"
                                                ></ValidationStatus>
                                                <span> {{ study.name }}</span>
                                                <ChevronUpIcon
                                                    :class="
                                                        !open
                                                            ? 'rotate-180 transform'
                                                            : ''
                                                    "
                                                    class="h-5 w-5 text-gray-500"
                                                />
                                            </DisclosureButton>
                                            <DisclosurePanel
                                                class="px-4 pt-4 pb-2 text-sm text-gray-500"
                                            >
                                                <div
                                                    class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                >
                                                    <ValidationStatus
                                                        :status="study.title"
                                                    ></ValidationStatus>
                                                    <p
                                                        class="truncate text-md text-dark-600"
                                                    >
                                                        Study title
                                                    </p>
                                                    <span
                                                        v-if="
                                                            !getStatus(
                                                                study.title
                                                            )
                                                        "
                                                        class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                    >
                                                        Edit
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                >
                                                    <ValidationStatus
                                                        :status="
                                                            study.description
                                                        "
                                                    ></ValidationStatus>
                                                    <p
                                                        class="truncate text-md text-dark-600"
                                                    >
                                                        Study description
                                                    </p>
                                                    <span
                                                        v-if="
                                                            !getStatus(
                                                                study.description
                                                            )
                                                        "
                                                        class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                    >
                                                        Edit
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                >
                                                    <ValidationStatus
                                                        :status="study.keywords"
                                                    ></ValidationStatus>
                                                    <p
                                                        class="truncate text-md text-dark-600"
                                                    >
                                                        Study keywords
                                                    </p>
                                                    <a
                                                        v-if="
                                                            !getStatus(
                                                                study.keywords
                                                            )
                                                        "
                                                        class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                        :href="
                                                            route(
                                                                'dashboard.studies',
                                                                study.id
                                                            )
                                                        "
                                                    >
                                                        Edit
                                                    </a>
                                                </div>
                                                <div
                                                    class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                >
                                                    <ValidationStatus
                                                        :status="study.sample"
                                                    ></ValidationStatus>
                                                    <p
                                                        class="truncate text-md text-dark-600"
                                                    >
                                                        Study sample details
                                                    </p>
                                                    <span
                                                        v-if="
                                                            !getStatus(
                                                                study.sample
                                                            )
                                                        "
                                                        class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                    >
                                                        Edit
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                >
                                                    <ValidationStatus
                                                        :status="
                                                            study.composition
                                                        "
                                                    ></ValidationStatus>
                                                    <p
                                                        class="truncate text-md text-dark-600"
                                                    >
                                                        Sample Composition
                                                    </p>
                                                    <a
                                                        v-if="
                                                            !getStatus(
                                                                study.composition
                                                            )
                                                        "
                                                        :href="
                                                            route(
                                                                'dashboard.studies',
                                                                study.id
                                                            )
                                                        "
                                                        class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                    >
                                                        Edit
                                                    </a>
                                                </div>
                                                <div
                                                    class="flex items-center mt-2"
                                                >
                                                    <p
                                                        class="truncate text-md text-dark-600 mb-3"
                                                    >
                                                        Datasets
                                                    </p>
                                                </div>
                                                <div
                                                    class="mb-2"
                                                    v-for="dataset in study.datasets"
                                                    :key="dataset.id"
                                                >
                                                    <Disclosure
                                                        v-slot="{ open }"
                                                    >
                                                        <DisclosureButton
                                                            :class="[
                                                                dataset.status
                                                                    ? 'bg-gray-100 hover:bg-gray-200'
                                                                    : 'bg-red-100 hover:bg-red-200',
                                                                'flex w-full rounded-md px-4 py-2 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75',
                                                            ]"
                                                        >
                                                            <ValidationStatus
                                                                class="-my-2"
                                                                :status="
                                                                    dataset.status
                                                                "
                                                            ></ValidationStatus>
                                                            <span>{{
                                                                dataset.name
                                                            }}</span>
                                                            <ChevronUpIcon
                                                                :class="
                                                                    !open
                                                                        ? 'rotate-180 transform'
                                                                        : ''
                                                                "
                                                                class="h-5 w-5 text-gray-500"
                                                            />
                                                        </DisclosureButton>
                                                        <DisclosurePanel
                                                            class="px-4 pt-4 pb-2 text-sm text-gray-500"
                                                        >
                                                            <div
                                                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                            >
                                                                <ValidationStatus
                                                                    :status="
                                                                        dataset.files
                                                                    "
                                                                ></ValidationStatus>
                                                                <p
                                                                    class="truncate text-md text-dark-600"
                                                                >
                                                                    Files
                                                                </p>
                                                                <span
                                                                    v-if="
                                                                        !getStatus(
                                                                            dataset.files
                                                                        )
                                                                    "
                                                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                                >
                                                                    Edit
                                                                </span>
                                                            </div>
                                                            <div
                                                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                            >
                                                                <ValidationStatus
                                                                    :status="
                                                                        dataset.nmrium_info
                                                                    "
                                                                ></ValidationStatus>
                                                                <p
                                                                    class="truncate text-md text-dark-600"
                                                                >
                                                                    Spectra
                                                                    <small
                                                                        v-if="
                                                                            dataset.nmrium_info.split(
                                                                                '|'
                                                                            )[0] !==
                                                                            'true'
                                                                        "
                                                                    >
                                                                        <br />
                                                                        Please
                                                                        load and
                                                                        confirm
                                                                        the
                                                                        spectra
                                                                        uploaded.
                                                                    </small>
                                                                </p>
                                                                <a
                                                                    target="_blank"
                                                                    v-if="
                                                                        !getStatus(
                                                                            dataset.nmrium_info
                                                                        )
                                                                    "
                                                                    :href="
                                                                        route(
                                                                            'dashboard.study.datasets',
                                                                            study.id
                                                                        ) +
                                                                        '?dsid=' +
                                                                        dataset.id
                                                                    "
                                                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                                >
                                                                    Edit
                                                                </a>
                                                            </div>
                                                            <div
                                                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                            >
                                                                <ValidationStatus
                                                                    :status="
                                                                        dataset.assay
                                                                    "
                                                                ></ValidationStatus>
                                                                <p
                                                                    class="truncate text-md text-dark-600"
                                                                >
                                                                    Assay (Meta
                                                                    data)
                                                                </p>
                                                                <span
                                                                    v-if="
                                                                        !getStatus(
                                                                            dataset.assay
                                                                        )
                                                                    "
                                                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                                >
                                                                    Edit
                                                                </span>
                                                            </div>
                                                            <div
                                                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                                                            >
                                                                <ValidationStatus
                                                                    :status="
                                                                        dataset.assignments
                                                                    "
                                                                ></ValidationStatus>
                                                                <p
                                                                    class="truncate text-md text-dark-600"
                                                                >
                                                                    Assignments
                                                                </p>
                                                                <a
                                                                    v-if="
                                                                        !getStatus(
                                                                            dataset.assignments
                                                                        )
                                                                    "
                                                                    class="cursor-pointer bg-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                                                    :href="
                                                                        route(
                                                                            'dashboard.study.datasets',
                                                                            study.id
                                                                        ) +
                                                                        '?dsid=' +
                                                                        dataset.id
                                                                    "
                                                                >
                                                                    Edit
                                                                </a>
                                                            </div>
                                                        </DisclosurePanel>
                                                    </Disclosure>
                                                </div>
                                            </DisclosurePanel>
                                        </Disclosure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li v-if="validation.errors && validation.errors.length > 0">
                    <a target="_blank" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <p
                                    class="truncate text-md font-bold text-red-600"
                                >
                                    Errors
                                </p>
                                <div class="ml-2 flex flex-shrink-0">
                                    <ValidationStatus
                                        :status="validation.errors.length == 0"
                                    ></ValidationStatus>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li v-if="validation.missing && validation.missing.length > 0">
                    <a target="_blank" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div
                                class="flex items-center hover:bg-gray-100 rounded-md px-2"
                            >
                                <p
                                    class="truncate text-md font-bold text-yellow-600"
                                >
                                    Warnings
                                </p>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import ValidationStatus from "@/Shared/ValidationStatus.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { ChevronUpIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        ValidationStatus,
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
        ChevronUpIcon,
    },
    props: {
        project: Object,
        validation: Object,
    },
    methods: {
        getStatus(value) {
            if (typeof value == "boolean") {
                return value;
            }
            return value.split("|")[0] == "true";
        },
    },
};
</script>
