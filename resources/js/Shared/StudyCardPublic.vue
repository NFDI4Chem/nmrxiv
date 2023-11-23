<template>
    <div
        v-if="study"
        class="flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden"
    >
        <Link :href="study.public_url">
            <div>
                <!-- <ul role="list">
                    <li
                        class="col-span-1 divide-y divide-gray-200 cursor-pointer"
                    >
                        <div
                            class="h-48 bg-white rounded-t-md flex justify-center items-center"
                        >
                            <span
                                v-if="
                                    study.molecules &&
                                    study.molecules.length > 0
                                "
                            >
                                <Depictor2D
                                    class="py-2"
                                    :molecule="
                                        study.molecules[0].canonical_smiles
                                    "
                                    :show-download="false"
                                ></Depictor2D>
                            </span>
                        </div>
                    </li>
                </ul> -->
                <!-- <div class="h-48">
                

                    <span v-if="study.photo_url && study.photo_url != ''">
                        <img
                            class="h-48 w-full rounded-t-md"
                            v-if="study.photo_url"
                            :src="study.photo_url"
                            alt="study.name"
                        />
                    </span>
                    <span
                        v-if="
                            study.study_preview_urls &&
                            study.study_preview_urls.length > 0
                        "
                    >
                        <span v-for="url in study.study_preview_urls">
                            <img
                                class="h-48 w-full rounded-t-md"
                                :src="url"
                                alt="study.name"
                            />
                        </span>
                    </span>
                </div>
                <div class="bg-white"></div> -->
                <div>
                    <span
                        v-if="
                            study.study_preview_urls &&
                            study.study_preview_urls.length > 0
                        "
                    >
                        <span v-if="study.study_preview_urls.length == 1">
                            <img
                                class="h-48 w-full rounded-t-md"
                                :src="study.study_preview_urls[0]"
                                alt=""
                            />
                        </span>
                        <span v-else>
                            <div class="relative">
                                <span
                                    v-for="(
                                        url, index
                                    ) in study.study_preview_urls"
                                    :key="url"
                                >
                                    <span v-if="index == selectedPreviewIndex">
                                        <img
                                            class="h-48 w-full rounded-t-md"
                                            :src="url"
                                            alt=""
                                        />
                                    </span>
                                </span>

                                <div
                                    class="absolute w-full py-2.5 bottom-0 inset-x-0 pl-4 text-white text-xs text-center leading-4"
                                >
                                    <div>
                                        <ol
                                            role="list"
                                            class="flex items-center space-x-2"
                                        >
                                            <li
                                                v-for="(
                                                    url, index
                                                ) in study.study_preview_urls"
                                                :key="index"
                                            >
                                                <a
                                                    v-if="
                                                        index ===
                                                        selectedPreviewIndex
                                                    "
                                                    class="block w-2.5 h-2.5 bg-teal-200 rounded-full hover:bg-teal-400"
                                                    @click.prevent="
                                                        selectedPreviewIndex =
                                                            index
                                                    "
                                                >
                                                </a>
                                                <a
                                                    v-else
                                                    class="cursor-pointer block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                                                    @click.prevent="
                                                        selectedPreviewIndex =
                                                            index
                                                    "
                                                >
                                                </a>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <span
                                class="bg-gradient-to-r from-cyan-500 to-blue-500"
                            >
                            </span>
                        </span>
                    </span>
                    <span v-else>
                        <img
                            class="h-48 w-full rounded-t-md"
                            src="https://via.placeholder.com/340x180/FFF/f1f1f4?text=No preview"
                            alt=""
                        />
                    </span>
                </div>
            </div>
            <div
                class="flex-1 bg-white p-3 border-t mt-1 flex flex-col justify-between"
            >
                <div>
                    <small
                        v-if="study.identifier"
                        class="float-left text-gray-500"
                        >#{{ study.identifier }}</small
                    >
                    <div class="float-right">
                        <div v-if="study.is_public" class="flex items-center">
                            <LockOpenIcon class="w-3 h-3 mr-1 text-green-600" />
                            <p class="text-xs text-gray-600">Public</p>
                        </div>
                        <div v-else class="flex items-center">
                            <LockClosedIcon
                                class="w-3 h-3 mr-1 text-teal-600"
                            />
                            <p class="text-xs text-gray-600">Private</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1">
                    <p
                        class="text-lg mt-2 font-semibold text-gray-900 line-clamp-1"
                    >
                        {{ study.name }}
                    </p>
                    <div class="mt-1 h-14 overflow-hidden">
                        <span
                            v-for="type in study.experiment_types"
                            :key="type"
                            class="mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                        >
                            {{ type }}
                        </span>
                    </div>
                </div>

                <div class="pt-1">
                    <div class="text-xs text-gray-600">
                        <span class="text-gray-400">Last updated on</span>
                        {{ formatDate(study.updated_at) }}
                    </div>
                </div>
            </div>
        </Link>
    </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/24/solid";
import { LockOpenIcon } from "@heroicons/vue/24/solid";
import { PencilIcon } from "@heroicons/vue/24/solid";
import { EnvelopeIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import Depictor2D from "@/Shared/Depictor2D.vue";
export default {
    components: {
        LockClosedIcon,
        LockOpenIcon,
        EnvelopeIcon,
        PencilIcon,
        Link,
        Depictor2D,
    },
    props: ["study", "project"],
    setup() {},
    data() {
        return {
            selectedPreviewIndex: 0,
        };
    },
    methods: {},
};
</script>
