<template>
    <div
        v-if="study"
        class="flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden"
    >
        <Link :href="study.public_url">
            <div>
                <ul role="list">
                    <li
                        class="col-span-1 divide-y divide-gray-200 cursor-pointer"
                    >
                        <div
                            class="h-64 bg-white rounded-t-md flex justify-center items-center"
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
                                        study.molecules[0].CANONICAL_SMILES
                                    "
                                    :show-download="false"
                                ></Depictor2D>
                            </span>
                        </div>
                    </li>
                </ul>
                <div class="bg-white"></div>
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
