<template>
    <div
        v-if="study"
        class="flex flex-col rounded-lg shadow-lg transition ease-in-out delay-150 hover:-translate-y-1 hover:scale-100 duration-300 overflow-hidden"
    >
        <div class="flex-1 bg-white p-3 flex flex-col justify-between">
            <div class="flex-1">
                <Link :href="route('dashboard.studies', [study.id])">
                    <p class="text-lg font-semibold text-gray-900 line-clamp-1">
                        {{ study.name }}
                    </p>
                </Link>
                <div v-if="study.is_public" class="flex items-center mt-1">
                    <LockOpenIcon class="w-3 h-3 mr-1 text-green-600" />
                    <p class="text-xs text-gray-600">Public</p>
                </div>
                <div v-else class="flex items-center mt-1">
                    <LockClosedIcon class="w-3 h-3 mr-1 text-teal-600" />
                    <p class="text-xs text-gray-600">Private</p>
                </div>
            </div>
            <div class="pt-1 text-right">
                <div class="text-xs text-gray-600 pr-5">
                    <span class="text-gray-400">Last updated on</span>
                    {{ formatDate(study.updated_at) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { LockClosedIcon } from "@heroicons/vue/solid";
import { LockOpenIcon } from "@heroicons/vue/solid";
import { PencilIcon } from "@heroicons/vue/solid";
import { MailIcon } from "@heroicons/vue/solid";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        LockClosedIcon,
        LockOpenIcon,
        MailIcon,
        PencilIcon,
        Link,
    },
    setup() {},
    props: ["study"],
    data() {
        return {};
    },
    methods: {},
};
</script>
