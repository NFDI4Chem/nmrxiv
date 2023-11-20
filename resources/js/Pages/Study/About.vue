<template>
    <div>
        <study-content
            model="study"
            :project="project"
            :study="study"
            :team="team"
            :members="members"
            :available-roles="availableRoles"
            :study-permissions="studyPermissions"
            :study-role="studyRole"
            current="About"
        >
            <template #study-section>
                <div class="divide-y divide-gray-200 sm:col-span-9">
                    <div class="py-3 px-4 sm:p-6 lg:pb-8">
                        <div class="mt-0">
                            <div class="mb-4">
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 flex items-center"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="w-full border-t border-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="relative flex items-center justify-between"
                                    >
                                        <span
                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                        >
                                            Keywords
                                        </span>
                                        <button
                                            v-if="canUpdateStudy"
                                            type="button"
                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                            @click="openStudyDetailsPane"
                                        >
                                            <PencilIcon
                                                class="w-4 h-4 mr-1 text-gray-600"
                                            />
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </div>
                                <dd
                                    class="mt-1 text-md text-gray-900 space-y-5"
                                >
                                    <p>
                                        <span
                                            v-for="tag in study.tags"
                                            :key="tag.id"
                                            class="mr-2"
                                        >
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"
                                            >
                                                <svg
                                                    class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                                                    fill="currentColor"
                                                    viewBox="0 0 8 8"
                                                >
                                                    <circle
                                                        cx="4"
                                                        cy="4"
                                                        r="3"
                                                    />
                                                </svg>
                                                {{ tag.name["en"] }}
                                            </span>
                                        </span>
                                    </p>
                                </dd>
                            </div>
                            <div class="mb-4">
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 flex items-center"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="w-full border-t border-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="relative flex items-center justify-between"
                                    >
                                        <span
                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                        >
                                            License
                                        </span>
                                        <button
                                            v-if="canUpdateStudy"
                                            type="button"
                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                            @click="openStudyDetailsPane"
                                        >
                                            <PencilIcon
                                                class="w-4 h-4 mr-1 text-gray-600"
                                            />
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <dd
                                        v-if="license"
                                        class="text-md text-gray-900 space-y-5"
                                    >
                                        <p
                                            style="max-width: 100ch !important"
                                            class="prose mt-1 text-sm text-blue-gray-500"
                                        >
                                            {{ license.title }}
                                            <ToolTip
                                                v-if="study.license_id"
                                                class="inline h-4 w-4 ml-0"
                                                :text="license.description"
                                            ></ToolTip>
                                        </p>
                                    </dd>
                                </div>
                            </div>
                            <div class="mb-4">
                                <!-- <div class="relative">
                                    <div
                                        class="absolute inset-0 flex items-center"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="w-full border-t border-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="relative flex items-center justify-between"
                                    >
                                        <span
                                            class="pr-3 text-lg bg-white font-medium text-gray-500"
                                        >
                                            Sample details
                                        </span>
                                        <button
                                            v-if="canUpdateStudy"
                                            type="button"
                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                            @click="openStudyDetailsPane"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 mr-2"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                                />
                                            </svg>
                                            <span>Save</span>
                                        </button>
                                    </div>
                                </div> -->
                                <div>
                                    <!-- <section
                                        aria-labelledby="activity-title"
                                        class="mt-2 xl:mt-2"
                                    >
                                        <div>
                                            <div class="pt-3">
                                                <div>
                                                    <label
                                                        for="about"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        Sample Collection
                                                        Protocol
                                                    </label>
                                                    <div class="mt-1">
                                                        <textarea
                                                            id="about"
                                                            readonly
                                                            name="about"
                                                            rows="3"
                                                            class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                                        ></textarea>
                                                    </div>
                                                    <p
                                                        class="mt-2 text-sm text-gray-500"
                                                    >
                                                        Describe your sample
                                                        collection protocol.
                                                        Protocol parameters can
                                                        be provided below.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </section> -->
                                    <div>
                                        <div class="py-4 max-w-7xl mx-auto">
                                            <div
                                                class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between"
                                            >
                                                <h3
                                                    class="text-lg leading-6 font-medium text-gray-900"
                                                >
                                                    Sample chemical composition
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="pr-2">
                                                <div
                                                    v-if="
                                                        study.sample.molecules
                                                            .length > 0
                                                    "
                                                    class="flow-root"
                                                >
                                                    <ul
                                                        role="list"
                                                        class="-mb-8"
                                                    >
                                                        <li
                                                            v-for="molecule in study
                                                                .sample
                                                                .molecules"
                                                            :key="
                                                                molecule.standard_inchi
                                                            "
                                                        >
                                                            <div
                                                                class="relative pb-8"
                                                            >
                                                                <span
                                                                    class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                                                    aria-hidden="true"
                                                                ></span>
                                                                <div
                                                                    class="relative flex items-start space-x-3"
                                                                >
                                                                    <div
                                                                        v-if="
                                                                            molecule &&
                                                                            molecule.pivot
                                                                        "
                                                                        class="relative"
                                                                    >
                                                                        <div
                                                                            class="rounded-full border p-2 z-10 bg-gray-100 text-sm"
                                                                        >
                                                                            {{
                                                                                molecule
                                                                                    .pivot
                                                                                    .percentage_composition
                                                                            }}%
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="min-w-0 flex-1"
                                                                    >
                                                                        <div>
                                                                            <div
                                                                                class="text-sm"
                                                                            >
                                                                                <a
                                                                                    class="break-all font-medium text-gray-900"
                                                                                    >{{
                                                                                        molecule.standard_inchi
                                                                                    }}</a
                                                                                >
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="mt-2 text-sm text-gray-700"
                                                                        >
                                                                            <div
                                                                                class="rounded-md border my-3 flex justify-center items-center"
                                                                            >
                                                                                <Depictor2D
                                                                                    :molecule="
                                                                                        molecule.canonical_smiles
                                                                                    "
                                                                                ></Depictor2D>
                                                                            </div>
                                                                        </div>
                                                                        <button
                                                                            v-if="
                                                                                canUpdateStudy
                                                                            "
                                                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                                            @click="
                                                                                deleteMolecule(
                                                                                    molecule
                                                                                )
                                                                            "
                                                                        >
                                                                            <TrashIcon
                                                                                class="w-4 h-4 inline mr-1"
                                                                            ></TrashIcon
                                                                            >Delete
                                                                        </button>
                                                                        <button
                                                                            v-if="
                                                                                canUpdateStudy
                                                                            "
                                                                            class="inline-flex ml-2 items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                                            @click="
                                                                                editMolecule(
                                                                                    molecule
                                                                                )
                                                                            "
                                                                        >
                                                                            <PencilIcon
                                                                                class="w-4 h-4 inline mr-1"
                                                                            ></PencilIcon
                                                                            >Edit
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <div
                                                        class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"
                                                    >
                                                        Sample chemical
                                                        composition
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <div
                                                        class="text-center my-10 py-10"
                                                    >
                                                        <h3
                                                            class="mt-2 text-sm font-medium text-gray-900"
                                                        >
                                                            No structures
                                                            associated with the
                                                            sample!
                                                        </h3>
                                                        <p
                                                            v-if="
                                                                !study.is_public
                                                            "
                                                            class="mt-1 text-sm text-gray-500"
                                                        >
                                                            Get started by
                                                            adding a new
                                                            molecule.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                v-if="canUpdateStudy"
                                                class="pl-2"
                                            >
                                                <div class="sm:col-span-4">
                                                    <label
                                                        for="email"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        SMILES
                                                        <div class="tooltip">
                                                            <InformationCircleIcon
                                                                class="w-5 h-5 inline"
                                                            ></InformationCircleIcon
                                                            ><span
                                                                class="w-64 bg-gray-900 text-white p-4 shadow-lg rounded-md tooltiptext"
                                                                >Simplified
                                                                molecular-input
                                                                line-entry
                                                                system (SMILES)
                                                                is a
                                                                specification in
                                                                the form of a
                                                                line notation
                                                                for describing
                                                                the structure of
                                                                chemical species
                                                                using short
                                                                ASCII strings.
                                                                For more
                                                                information
                                                                please checkout
                                                                <a
                                                                    href="https://en.wikipedia.org/wiki/Simplified_molecular-input_line-entry_system"
                                                                    class="text-gray-400"
                                                                    target="_blank"
                                                                    >here</a
                                                                >
                                                                and
                                                                <a
                                                                    href="https://www.daylight.com/dayhtml/doc/theory/theory.smiles.html"
                                                                    class="text-gray-400"
                                                                    target="_blank"
                                                                    >here</a
                                                                >.</span
                                                            >
                                                        </div>
                                                    </label>
                                                    <div class="mt-1 mb-2">
                                                        <input
                                                            id="smiles"
                                                            v-model="smiles"
                                                            name="smiles"
                                                            type="text"
                                                            class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                            @blur="loadSmiles"
                                                        />
                                                        <button
                                                            v-if="
                                                                smiles &&
                                                                smiles != ''
                                                            "
                                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2"
                                                            @click="loadSmiles"
                                                        >
                                                            Load Structure
                                                        </button>
                                                        <jet-input-error
                                                            :message="
                                                                errorMessage
                                                            "
                                                            class="mt-2"
                                                        />
                                                    </div>
                                                </div>
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-0 flex items-center"
                                                        aria-hidden="true"
                                                    >
                                                        <div
                                                            class="w-full border-t border-gray-300"
                                                        />
                                                    </div>
                                                    <div
                                                        class="relative flex justify-center"
                                                    >
                                                        <span
                                                            class="px-2 bg-white text-sm text-gray-500"
                                                        >
                                                            Or
                                                        </span>
                                                    </div>
                                                </div>
                                                <span
                                                    class="float-right text-xs cursor-pointer hover:text-blue-700"
                                                    ><a
                                                        href="https://docs.nmrxiv.org/submission-guides/editor.html"
                                                        target="_blank"
                                                        >Need help?</a
                                                    ></span
                                                >
                                                <div
                                                    id="structureSearchEditor"
                                                    class="w-full border my-4 rounded-md"
                                                    style="height: 400px"
                                                />
                                                <div class="mt-1 mb-6">
                                                    <label
                                                        for="email"
                                                        class="block text-sm font-medium text-gray-700"
                                                    >
                                                        Percentage composition
                                                        ({{ percentage }}%)
                                                        <ToolTip
                                                            class="inline h-4 w-12 ml-0"
                                                            text="If the molecule percentage is unknown, please keep it as 0."
                                                        ></ToolTip>
                                                    </label>
                                                    <slider
                                                        v-model="percentage"
                                                        :min="0"
                                                        :max="getMax"
                                                        :height="10"
                                                        color="#000"
                                                        track-color="#999"
                                                    />
                                                </div>
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                    @click="saveMolecule(null)"
                                                >
                                                    ADD
                                                </button>
                                            </div>
                                        </div>
                                        <div v-if="study">
                                            <div
                                                v-if="
                                                    study.molecules &&
                                                    study.molecules.length == 0
                                                "
                                            >
                                                <template>
                                                    <button
                                                        type="button"
                                                        class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                                    >
                                                        <svg
                                                            class="mx-auto h-12 w-12 text-gray-400"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            stroke="currentColor"
                                                            fill="none"
                                                            viewBox="0 0 48 48"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"
                                                            />
                                                        </svg>
                                                        <span
                                                            class="mt-2 block text-sm font-medium text-gray-900"
                                                        >
                                                            Create a new
                                                            database
                                                        </span>
                                                    </button>
                                                </template>
                                            </div>
                                            <div
                                                v-if="
                                                    study.molecules &&
                                                    study.molecules.length > 0
                                                "
                                            >
                                                Mols
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 flex items-center"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="w-full border-t border-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="relative flex items-center justify-between"
                                    >
                                        <span
                                            class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"
                                        >
                                            Description
                                        </span>
                                        <button
                                            v-if="canUpdateStudy"
                                            type="button"
                                            class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                            @click="openStudyDetailsPane"
                                        >
                                            <PencilIcon
                                                class="w-4 h-4 mr-1 text-gray-600"
                                            />
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </div>
                                <dd
                                    class="mt-1 text-md text-gray-900 space-y-5"
                                >
                                    <p
                                        style="max-width: 100ch !important"
                                        class="prose mt-1 text-sm text-blue-gray-500"
                                        v-html="md(study.description)"
                                    ></p>
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </study-content>
    </div>
</template>

<script>
import {
    PlusSmallIcon,
    TrashIcon,
    PencilIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/solid";
import StudyContent from "@/Pages/Study/Content.vue";
import slider from "vue3-slider";
import OCL from "openchemlib/full";
import ToolTip from "@/Shared/ToolTip.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import Depictor2D from "@/Shared/Depictor2D.vue";
export default {
    components: {
        StudyContent,
        ToolTip,
        PlusSmallIcon,
        slider,
        TrashIcon,
        PencilIcon,
        InformationCircleIcon,
        JetInputError,
        Depictor2D,
    },
    props: [
        "study",
        "project",
        "team",
        "members",
        "availableRoles",
        "studyPermissions",
        "studyRole",
        "license",
    ],
    setup() {},
    data() {
        return {
            smiles: "",
            percentage: 0,
            editor: "",
            errorMessage: "",
        };
    },
    computed: {
        getMax() {
            if (this.study) {
                let totalCount = 0;
                this.study.sample.molecules.forEach((mol) => {
                    totalCount += parseInt(mol.pivot.percentage_composition);
                });
                return 100 - totalCount;
            } else {
                return 100;
            }
        },
        canUpdateStudy() {
            return this.studyPermissions
                ? this.studyPermissions.canUpdateStudy
                : false;
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.editor = OCL.StructureEditor.createSVGEditor(
                "structureSearchEditor",
                1
            );
        });
    },
    methods: {
        openStudyDetailsPane() {
            this.emitter.emit("openStudyDetails", {});
        },
        loadSmiles() {
            this.errorMessage = "";
            if (this.smiles && this.smiles != "") {
                try {
                    let mol = OCL.Molecule.fromSmiles(this.smiles);
                    this.editor.setSmiles(this.smiles);
                } catch (e) {
                    this.errorMessage = "The entered SMILES is not valid.";
                }
            }
        },
        getSVGString(molecule) {
            if (molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            }
        },
        deleteMolecule(mol) {
            axios
                .delete(
                    "/dashboard/studies/" +
                        this.study.id +
                        "/molecule/" +
                        mol.id
                )
                .then((res) => {
                    this.study.sample.molecules = res.data;
                    this.smiles = "";
                    this.percentage = 0;
                    this.editor.setSmiles("");
                });
        },
        editMolecule(mol) {
            this.editor.setSmiles(mol.canonical_smiles);
            this.percentage = parseInt(mol.pivot.percentage_composition);
            axios
                .delete(
                    "/dashboard/studies/" +
                        this.study.id +
                        "/molecule/" +
                        mol.id
                )
                .then((res) => {
                    this.study.sample.molecules = res.data;
                });
        },
        saveMolecule(mol, study) {
            if (!study) {
                study = this.study;
            }
            if (!mol) {
                let mol = this.editor.getMolFile();
                this.standardizeMolecules(mol).then((res) => {
                    this.associateMoleculeToStudy(res.data, study);
                });
            } else {
                this.associateMoleculeToStudy(mol, study);
            }
        },
        standardizeMolecules(mol) {
            return axios.post(
                "https://api.naturalproducts.net/latest/chem/standardize",
                mol
            );
        },
        associateMoleculeToStudy(mol, study) {
            axios
                .post("/dashboard/studies/" + study.id + "/molecule", {
                    InChI: mol.inchi,
                    InChIKey: mol.inchikey,
                    percentage: 0,
                    mol: mol.standardized_mol,
                    canonical_smiles: mol.canonical_smiles,
                })
                .then((res) => {
                    study.sample.molecules = res.data;
                    this.smiles = "";
                    this.percentage = 0;
                    // this.editor.setSmiles("");
                });
        },
    },
};
</script>
