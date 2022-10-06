<template>
    <div
        v-if="study"
        class="flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden"
    >
        <div class="pt-5 px-5 bg-gray-200">
            <span
                v-if="
                    study.study_preview_urls &&
                    study.study_preview_urls.length > 0
                "
            >
                <span v-if="study.study_preview_urls.length == 1">
                    <img
                        class="h-36 w-full rounded-t-md shadow-lg"
                        :src="study.study_preview_urls[0]"
                        alt=""
                    />
                </span>
                <span v-else>
                    <div class="relative">
                        <span
                            v-for="(url, index) in study.study_preview_urls"
                            :key="url"
                        >
                            <span v-if="index == selectedPreviewIndex">
                                <img
                                    class="h-36 w-full rounded-t-md shadow-lg"
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
                                                index === selectedPreviewIndex
                                            "
                                            class="block w-2.5 h-2.5 bg-teal-200 rounded-full hover:bg-teal-400"
                                            @click.prevent="
                                                selectedPreviewIndex = index
                                            "
                                        >
                                        </a>
                                        <a
                                            v-else
                                            class="cursor-pointer block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                                            @click.prevent="
                                                selectedPreviewIndex = index
                                            "
                                        >
                                        </a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <span class="bg-gradient-to-r from-cyan-500 to-blue-500">
                    </span>
                </span>
            </span>
            <span v-else>
                <img
                    class="h-36 w-full rounded-t-md shadow-lg"
                    src="https://via.placeholder.com/340x180/FFF/f1f1f4?text=No preview"
                    alt=""
                />
            </span>
        </div>
        <div class="flex-1 border-t bg-white p-3 flex flex-col justify-between">
            <small v-if="study.identifier" class="text-gray-500"
                >#{{ study.identifier }}</small
            >
            <Link
                :href="study.public_url"
            >
                <div class="flex-1">
                    <p class="text-lg font-semibold text-gray-900 line-clamp-1">
                        {{ study.name }}
                    </p>
                </div>
                <div class="pt-1">
                    <div class="float-left">
                        <div
                            v-if="study.is_public"
                            class="flex items-center mt-1"
                        >
                            <LockOpenIcon class="w-3 h-3 mr-1 text-green-600" />
                            <p class="text-xs text-gray-600">Public</p>
                        </div>
                        <div v-else class="flex items-center mt-1">
                            <LockClosedIcon
                                class="w-3 h-3 mr-1 text-teal-600"
                            />
                            <p class="text-xs text-gray-600">Private</p>
                        </div>
                    </div>
                    <div class="float-right p-1 text-xs text-gray-600">
                        <span class="text-gray-400">Last updated on</span>
                        {{ formatDate(study.updated_at) }}
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/24/solid";
import { LockOpenIcon } from "@heroicons/vue/24/solid";
import { PencilIcon } from "@heroicons/vue/24/solid";
import { EnvelopeIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        LockClosedIcon,
        LockOpenIcon,
        EnvelopeIcon,
        PencilIcon,
        Link,
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
