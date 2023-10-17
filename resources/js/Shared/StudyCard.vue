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
            <Link :href="route('dashboard.studies', [study.id])">
                <div class="flex items-center font-bold text-lg text-gray-600">
                    <span class="">
                        <!-- Commented now to avoid to lighten the query, will be added later with cache. -->
                        <!--  <StarIcon
                        :class="[
                            study.is_bookmarked
                                ? 'text-yellow-400'
                                : 'text-gray-200',
                            'h-6 w-6 flex-shrink-0 -ml-1 mr-1',
                        ]"
                    ></StarIcon> -->
                    </span>
                    {{ study.name }}
                </div>
                <div class="pt-1">
                    <div class="float-left">
                        <div
                            v-if="study.is_public"
                            class="flex items-center mt-1"
                        >
                            <svg
                                class="h-3 w-3 mr-1 text-green-400 inline"
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
import { Link } from "@inertiajs/vue3";
import { StarIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        LockClosedIcon,
        LockOpenIcon,
        EnvelopeIcon,
        PencilIcon,
        StarIcon,
        Link,
    },
    props: ["study"],
    setup() {},
    data() {
        return {
            selectedPreviewIndex: 0,
        };
    },
    methods: {},
};
</script>
