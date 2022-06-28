<template>
  <div>
    <study-content
      model="study"
      :project="project"
      :study="study"
      :team="team"
      :members="members"
      :availableRoles="availableRoles"
      :studyPermissions="studyPermissions"
      :studyRole="studyRole"
      current="About"
    >
      <template #study-section>
        <div class="divide-y divide-gray-200 sm:col-span-9">
          <div class="py-3 px-4 sm:p-6 lg:pb-8">
            <div class="mt-0">
              <div class="mb-4">
                <div class="relative">
                  <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                  </div>
                  <div class="relative flex items-center justify-between">
                    <span class="pr-3 text-lg bg-white font-medium text-gray-500">
                      Description
                    </span>
                    <button
                      type="button"
                      @click="openStudyDetailsPane"
                      class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                        class="w-4 h-4 mr-2 text-gray-600"
                      >
                        <path
                          d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                        ></path>
                      </svg>
                      <span>Edit</span>
                    </button>
                  </div>
                </div>
                <dd class="mt-1 text-md text-gray-900 space-y-5">
                  <p>
                    {{ study.description }}
                  </p>
                </dd>
              </div>
              <div class="mb-4">
                <div class="relative">
                  <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                  </div>
                  <div class="relative flex items-center justify-between">
                    <span class="pr-3 text-lg bg-white font-medium text-gray-500">
                      Tags
                    </span>
                    <button
                      type="button"
                      @click="openStudyDetailsPane"
                      class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                        class="w-4 h-4 mr-2 text-gray-600"
                      >
                        <path
                          d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                        ></path>
                      </svg>
                      <span>Edit</span>
                    </button>
                  </div>
                </div>
                <dd class="mt-1 text-md text-gray-900 space-y-5">
                  <p>
                    <span class="mr-2" v-for="tag in study.tags" :key="tag.id">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"
                      >
                        <svg
                          class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                          fill="currentColor"
                          viewBox="0 0 8 8"
                        >
                          <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ tag.name["en"] }}
                      </span>
                    </span>
                  </p>
                </dd>
              </div>

              <div class="mb-4">
                <div class="relative">
                  <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                  </div>
                  <div class="relative flex items-center justify-between">
                    <span class="pr-3 text-lg bg-white font-medium text-gray-500">
                      Sample details
                    </span>
                    <button
                      type="button"
                      @click="openStudyDetailsPane"
                      class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                </div>
                <div>
                  <section aria-labelledby="activity-title" class="mt-2 xl:mt-2">
                    <div>
                      <div class="pt-3">
                        <div>
                          <label
                            for="about"
                            class="block text-sm font-medium text-gray-700"
                          >
                            Sample Collection Protocol
                          </label>
                          <div class="mt-1">
                            <textarea
                              readonly
                              id="about"
                              name="about"
                              rows="3"
                              class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                            ></textarea>
                          </div>
                          <p class="mt-2 text-sm text-gray-500">
                            Describe your sample collection protocol. Protocol parameters
                            can be provided below.
                          </p>
                        </div>
                      </div>
                    </div>
                  </section>
                  <div>
                    <div class="py-4 max-w-7xl mx-auto">
                      <div
                        class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between"
                      >
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                          Structures
                        </h3>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                      <div class="pr-2">
                        <div v-if="study.sample.molecules.length > 0" class="flow-root">
                          <ul role="list" class="-mb-8">
                            <li
                              v-for="molecule in study.sample.molecules"
                              :key="molecule.STANDARD_INCHI"
                            >
                              <div class="relative pb-8">
                                <span
                                  class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                  aria-hidden="true"
                                ></span>
                                <div class="relative flex items-start space-x-3">
                                  <div v-if="molecule && molecule.pivot" class="relative">
                                    <img
                                      class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                                      :src="
                                        'https://ui-avatars.com/api/?name=' +
                                        (
                                          '0' + molecule.pivot.percentage_composition
                                        ).slice(-2) +
                                        '%2BUser&color=7F9CF5&background=EBF4FF'
                                      "
                                      alt=""
                                    />
                                  </div>
                                  <div class="min-w-0 flex-1">
                                    <div>
                                      <div class="text-sm">
                                        <a href="#" class="font-medium text-gray-900">{{
                                          molecule.STANDARD_INCHI
                                        }}</a>
                                      </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-700">
                                      <div
                                        class="rounded-md border my-3 flex justify-center items-center"
                                      >
                                        <span v-html="getSVGString(molecule)"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </li>
                          </ul>
                        </div>
                        <div v-else>
                          <div class="text-center my-10 py-10">
                            <svg
                              class="mx-auto h-12 w-12 text-gray-400"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                              aria-hidden="true"
                            >
                              <path
                                vector-effect="non-scaling-stroke"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                              />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                              No structures associated with the sample yet!
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                              Get started by adding a new molecule.
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="pl-2">
                        <div class="sm:col-span-4">
                          <label
                            for="email"
                            class="block text-sm font-medium text-gray-700"
                          >
                            SMILES
                          </label>
                          <div class="mt-1 mb-2">
                            <input
                              @blur="loadSmiles"
                              id="smiles"
                              name="smiles"
                              v-model="smiles"
                              type="text"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            />
                          </div>
                        </div>
                        <div class="relative">
                          <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                          >
                            <div class="w-full border-t border-gray-300" />
                          </div>
                          <div class="relative flex justify-center">
                            <span class="px-2 bg-white text-sm text-gray-500"> Or </span>
                          </div>
                        </div>
                        <div
                          class="w-full border my-4 rounded-md"
                          style="height: 400px"
                          id="structureSearchEditor"
                        />
                        <div class="mt-1 mb-6">
                          <label
                            for="email"
                            class="block text-sm font-medium text-gray-700"
                          >
                            Percentage composition ({{ percentage }}%)
                          </label>
                          <slider
                            :min="0"
                            :max="getMax"
                            :height="10"
                            v-model="percentage"
                            color="#000"
                            track-color="#999"
                          />
                        </div>
                        <button
                          @click="saveMolecule"
                          type="button"
                          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                        >
                          SAVE
                        </button>
                      </div>
                    </div>
                    <div v-if="study">
                      <div v-if="study.molecules && study.molecules.length == 0">
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
                            <span class="mt-2 block text-sm font-medium text-gray-900">
                              Create a new database
                            </span>
                          </button>
                        </template>
                      </div>
                      <div v-if="study.molecules && study.molecules.length > 0">Mols</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </study-content>
  </div>
</template>

<script>
import { PlusSmIcon } from "@heroicons/vue/solid";
import StudyContent from "@/Pages/Study/Content.vue";
import { inject } from "vue";
import slider from "vue3-slider";
import OCL from "openchemlib/full";
export default {
  props: [
    "study",
    "project",
    "team",
    "members",
    "availableRoles",
    "studyPermissions",
    "studyRole",
  ],
  components: {
    StudyContent,
    PlusSmIcon,
    slider,
  },
  data() {
    return {
      smiles: "",
      percentage: 1,
      editor: "",
    };
  },
  setup() {
    const emitter = inject("emitter");
    const openStudyDetailsPane = () => {
      emitter.emit("openStudyDetails", {});
    };
    return {
      openStudyDetailsPane,
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.editor = OCL.StructureEditor.createSVGEditor("structureSearchEditor", 1);
    });
  },
  methods: {
    loadSmiles() {
      if (this.smiles && this.smiles != "") {
        this.editor.setSmiles(this.smiles);
      }
    },
    getSVGString(molecule) {
      if (molecule.MOL) {
        let mol = OCL.Molecule.fromMolfile("\n  " + molecule.MOL.replaceAll('"', ""));
        return mol.toSVG(200, 200);
      } else {
        console.log(molecule);
      }
    },
    saveMolecule() {
      let mol = this.editor.getMolFile();
      axios
        .post("https://www.cheminfo.org/webservices/inchi", "molfile=" + mol)
        .then((response) => {
          let InChI = response.data.output.InChI;
          let InChIKey = response.data.output.InChIKey;
          let MF = this.editor.getMolecule().getMolecularFormula().formula;
          axios
            .post("/dashboard/studies/" + this.study.id + "/molecule", {
              InChI: InChI,
              InChIKey: InChIKey,
              percentage: this.percentage,
              formula: MF,
              mol: mol,
            })
            .then((res) => {
              this.study.sample.molecules = res.data;
              this.smiles = "";
              this.percentage = 0;
              this.editor.setSmiles("");
            });
        });
    },
  },
  computed: {
    getMax() {
      if (this.study) {
        let totalCount = 0;
        this.study.sample.molecules.forEach((mol) => {
          totalCount += parseInt(mol.pivot.percentage_composition);
        });
        console.log(totalCount);
        return 100 - totalCount;
      } else {
        return 100;
      }
    },
  },
};
</script>
