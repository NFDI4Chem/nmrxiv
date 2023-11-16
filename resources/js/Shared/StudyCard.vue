<template>
    <div
        v-if="study"
        class="flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden"
    >
        <div class="relative overflow-hidden bg-white-200">
            <div class="pt-2 px-2">
                <ul role="list">
                    <li
                        class="col-span-1 divide-y divide-gray-200 cursor-pointer"
                    >
                        <div
                            class="bg-white rounded-t-md flex justify-center items-center"
                        >
                            <span
                                v-if="
                                    study.molecules[0] &&
                                    study.molecules[0].canonical_smiles
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
                            <span v-else>
                                <div class="h-64">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center"
                                    >
                                        <h2 class="text-gray-200 font-bold">
                                            <i
                                                >-- molecule(s) information
                                                missing --</i
                                            >
                                        </h2>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </li>
                </ul>
                <div class="bg-white"></div>
            </div>
            <!-- <span
                v-if="
                    study.photo_url ||
                    (study.study_preview_urls &&
                        study.study_preview_urls.length > 0) ||
                    (study.sample &&
                        study.sample.molecules &&
                        study.sample.molecules.length > 0)
                "
            >
                <ul v-if="study.molecules.length > 0" role="list">
                    <li
                        class="col-span-1 divide-y divide-gray-200 cursor-pointer"
                    >
                        <div
                            class="bg-white absolute top-0 left-0 bg-white opacity-80 item-center w-full rounded-t-md flex justify-center items-center"
                        >
                            <span
                                ><div
                                    v-html="getSVGString(study.molecules[0])"
                                ></div
                            ></span>
                        </div>
                    </li>
                </ul>

                <img
                    class="h-48 w-full rounded-t-md shadow-lg"
                    v-if="study.photo_url"
                    :src="study.photo_url"
                    alt="study.name"
                />
            </span>
            <span v-else>
                <img
                    class="12rem w-full rounded-t-md shadow-lg"
                    src="https://via.placeholder.com/340x180/FFF/f1f1f4?text=No preview"
                    alt=""
                />
            </span> -->
        </div>
        <div class="flex-1 border-t bg-white p-3 flex flex-col justify-between">
            <div>
                <small v-if="study.identifier" class="text-gray-500 float-left"
                    >#{{ study.identifier }}</small
                >
                <div class="float-right">
                    <div v-if="study.is_public" class="flex items-center mt-1">
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
                        <LockClosedIcon class="w-3 h-3 mr-1 text-teal-600" />
                        <p class="text-xs text-gray-600">Private</p>
                    </div>
                </div>
            </div>
            <Link :href="route('dashboard.studies', [study.id])">
                <div
                    class="flex items-center font-bold truncate text-lg text-gray-600"
                >
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
                    <div class="text-xs text-gray-600">
                        <span class="text-gray-400">Last updated on</span>
                        {{ formatDate(study.updated_at) }}
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>

<script>
import { LockClosedIcon, LockOpenIcon } from "@heroicons/vue/24/solid";
import { PencilIcon } from "@heroicons/vue/24/solid";
import { EnvelopeIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import { StarIcon } from "@heroicons/vue/24/solid";
import Depictor2D from "@/Shared/Depictor2D.vue";
export default {
    components: {
        LockClosedIcon,
        LockOpenIcon,
        EnvelopeIcon,
        PencilIcon,
        StarIcon,
        Link,
        Depictor2D,
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
