<template>
    <div
        v-if="study"
        class="flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden"
    >
        <div>
            <ul role="list">
                <li class="col-span-1 divide-y divide-gray-200 cursor-pointer">
                    <div
                        class="bg-white rounded-t-md flex justify-center items-center"
                    >
                        <span>
                            <Depictor
                                class="py-2"
                                :modelValue="
                                    study.molecules[0].CANONICAL_SMILES
                                "
                                :showDownload="false"
                            ></Depictor>
                        </span>
                    </div>
                </li>
            </ul>
            <div class="bg-white"></div>
        </div>
        <div class="flex-1 border-t bg-white p-3 flex flex-col justify-between">
            <small v-if="study.identifier" class="text-gray-500"
                >#{{ study.identifier }}</small
            >
            <Link :href="study.public_url">
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
import { Link } from "@inertiajs/vue3";
import Depictor from "@/Shared/Depictor.vue";
export default {
    components: {
        LockClosedIcon,
        LockOpenIcon,
        EnvelopeIcon,
        PencilIcon,
        Link,
        Depictor,
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
