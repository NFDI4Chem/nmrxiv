<template>
    <div
        v-if="study"
        class="flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden"
    >
        <div class="pt-2 px-2">
            <ul role="list">
                <li class="col-span-1 divide-y divide-gray-200 cursor-pointer">
                    <div
                        class="bg-white rounded-t-md flex justify-center items-center"
                    >
                        <span>
                            <Depictor
                                class="py-2"
                                :modelValue="
                                    study.sample.molecules[0].CANONICAL_SMILES
                                "
                                :showDownload="false"
                            ></Depictor>
                        </span>
                    </div>
                </li>
            </ul>
            <div class="bg-white"></div>
        </div>
        <div
            class="flex-1 border-t bg-white p-3 pb-2 flex flex-col justify-between"
        >
            <small v-if="study.identifier" class="text-gray-500"
                >#{{ study.identifier }}</small
            >
            <Link :href="route('dashboard.studies', [study.id])">
                <div
                    class="flex items-center font-bold text-lg text-gray-600 truncate ellipsis"
                >
                    {{ study.name }}
                </div>
            </Link>
        </div>
        <div class="px-3 pb-3 pt-0 h-28 overflow-scroll">
            <!-- <label
                class="block mb-2 text-xs text-gray-700, block text-sm font-medium text-gray-700"
            >
                SPECTRA
            </label> -->
            <span v-for="(ds, $dsIndex) in study.datasets" :key="$dsIndex">
                <div
                    :class="[
                        study.has_nmrium || ds.has_nmrium
                            ? 'bg-green-100 text-gray-800'
                            : 'bg-gray-100 text-gray-800',
                        'mb-0.5 inline-flex truncate break-words items-center px-3 py-0.5 rounded-full text-xs font-medium mr-1',
                    ]"
                >
                    <a
                        >{{ ds.name }}
                        <span v-if="ds.type">({{ ds.type }})</span></a
                    >
                </div>
            </span>
        </div>
        <div class="px-3 border-t py-1">
            <div class="pt-1">
                <div class="float-left">
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
                <div class="float-right p-1 text-xs text-gray-600">
                    <span class="text-gray-400">Last updated on</span>
                    {{ formatDate(study.updated_at) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    LockClosedIcon,
    StarIcon,
    LockOpenIcon,
    PencilIcon,
    EnvelopeIcon,
} from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import Depictor from "@/Shared/Depictor.vue";

export default {
    components: {
        LockClosedIcon,
        LockOpenIcon,
        EnvelopeIcon,
        PencilIcon,
        StarIcon,
        Link,
        Depictor,
    },
    props: ["study"],
    setup() {},
    data() {
        return {
            selectedPreviewIndex: 0,
        };
    },
    methods: {
        getSVGString(molecule) {
            if (molecule && molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            }
            //  else {
            //     console.log(molecule);
            // }
        },
    },
};
</script>
