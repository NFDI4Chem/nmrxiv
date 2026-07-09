<template>
    <div
        v-if="study"
        class="flex flex-col overflow-hidden rounded-lg border border-gray-200/80 bg-white ring-1 ring-black/5"
    >
        <Link
            :href="
                preview && obfuscationCode
                    ? route('preview', [obfuscationCode, study.id, 'study'])
                    : route('dashboard.studies', [study.id])
            "
            class="flex flex-col outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
        >
            <div class="relative overflow-hidden bg-white px-3 pb-2 pt-3">
                <ul role="list">
                    <li
                        class="col-span-1 cursor-pointer divide-y divide-gray-200"
                    >
                        <div class="relative rounded-t-md bg-white">
                            <div class="flex items-center justify-center">
                                <span
                                    v-if="
                                        molecules[0] &&
                                        molecules[0].canonical_smiles
                                    "
                                >
                                    <Depictor2D
                                        class="py-2"
                                        :molecule="
                                            molecules[0].canonical_smiles
                                        "
                                        :show-download="false"
                                    ></Depictor2D>
                                </span>
                                <span v-else class="block w-full">
                                    <div class="relative h-48 sm:h-56">
                                        <div
                                            class="absolute inset-0 flex items-center justify-center px-3 text-center"
                                        >
                                            <p
                                                class="text-sm font-medium leading-snug text-gray-500 dark:text-gray-400"
                                            >
                                                No chemical structure is linked
                                                to this compound yet.
                                            </p>
                                        </div>
                                    </div>
                                </span>
                            </div>

                            <span
                                class="pointer-events-none absolute right-2 top-2 z-10 inline-flex items-center gap-1 rounded-full bg-white px-1.5 py-0.5 sm:right-2.5 sm:top-2.5 dark:bg-gray-900"
                                :aria-label="
                                    (study.is_public ? 'Public' : 'Private') +
                                    ' compound' +
                                    (study.identifier
                                        ? ', ' + study.identifier
                                        : '')
                                "
                            >
                                <GlobeAltIcon
                                    v-if="study.is_public"
                                    class="h-3.5 w-3.5 shrink-0 text-emerald-600 dark:text-emerald-400"
                                    aria-hidden="true"
                                />
                                <LockClosedIcon
                                    v-else
                                    class="h-3.5 w-3.5 shrink-0 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                />
                                <span
                                    v-if="study.identifier"
                                    class="font-mono text-[11px] font-semibold tabular-nums leading-none tracking-wide text-gray-700 dark:text-gray-200"
                                    >{{ study.identifier }}</span
                                >
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
            <div
                class="flex flex-1 flex-col justify-between gap-3 border-t border-gray-100 bg-white px-5 py-4 sm:px-6"
            >
                <div class="min-w-0">
                    <h3
                        class="truncate text-lg font-semibold leading-snug tracking-tight text-gray-900 sm:text-xl dark:text-gray-100"
                    >
                        {{ study.name }}
                    </h3>
                </div>
                <div class="text-[11px] leading-snug text-gray-500 sm:text-xs">
                    <span class="font-normal text-gray-500 dark:text-gray-400"
                        >Updated</span
                    >
                    <span
                        class="ml-1.5 tabular-nums font-semibold text-gray-800 dark:text-gray-200"
                        >{{ formatRecordTimestamp(study.updated_at) }}</span
                    >
                </div>
            </div>
        </Link>
    </div>
</template>

<script>
import { GlobeAltIcon, LockClosedIcon } from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";
import Depictor2D from "@/Shared/Depictor2D.vue";
export default {
    components: {
        GlobeAltIcon,
        LockClosedIcon,
        Link,
        Depictor2D,
    },
    props: {
        study: {
            type: Object,
            required: true,
        },
        preview: {
            type: Boolean,
            default: false,
        },
        obfuscationCode: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            selectedPreviewIndex: 0,
        };
    },
    computed: {
        molecules() {
            return this.study.molecules
                ? this.study.molecules
                : this.study.sample.molecules;
        },
    },
};
</script>
