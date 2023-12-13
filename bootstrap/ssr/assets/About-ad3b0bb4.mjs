import { PlusSmallIcon, TrashIcon, PencilIcon, InformationCircleIcon } from "@heroicons/vue/24/solid";
import StudyContent from "./Content-5913e7b1.mjs";
import slider from "vue3-slider";
import OCL from "openchemlib/full.js";
import { T as ToolTip } from "./ToolTip-cf9882a3.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { D as Depictor2D } from "./Depictor2D-b60253ae.mjs";
import { resolveComponent, withCtx, createVNode, openBlock, createBlock, createCommentVNode, Fragment, renderList, createTextVNode, toDisplayString, withDirectives, vModelText, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrInterpolate, ssrRenderStyle, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./Layout-114f561c.mjs";
import "./AppLayout-b93d1c11.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@inertiajs/vue3";
import "@vueuse/core";
import "@heroicons/vue/24/outline";
import "@headlessui/vue";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "./Button-8d6976fd.mjs";
import "@sipec/vue3-tags-input";
import "./SelectRich-b58dcc3f.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
import "./Details-f3d36d90.mjs";
import "./ActionMessage-3207b224.mjs";
import "./Activity-90a526a4.mjs";
import "vue3-colorpicker";
/* empty css                 */import "./AccessDialogue-1fdd1bde.mjs";
import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Input-f9a8cf77.mjs";
import "./Label-c5144ba4.mjs";
import "./SectionBorder-7d1f222a.mjs";
import "./Citation-80069afd.mjs";
import "axios";
const _sfc_main = {
  components: {
    StudyContent,
    ToolTip,
    PlusSmallIcon,
    slider,
    TrashIcon,
    PencilIcon,
    InformationCircleIcon,
    JetInputError,
    Depictor2D
  },
  props: [
    "study",
    "project",
    "team",
    "members",
    "availableRoles",
    "studyPermissions",
    "studyRole",
    "license"
  ],
  setup() {
  },
  data() {
    return {
      smiles: "",
      percentage: 0,
      editor: "",
      errorMessage: ""
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
      return this.studyPermissions ? this.studyPermissions.canUpdateStudy : false;
    }
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
      axios.delete(
        "/dashboard/studies/" + this.study.id + "/molecule/" + mol.id
      ).then((res) => {
        this.study.sample.molecules = res.data;
        this.smiles = "";
        this.percentage = 0;
        this.editor.setSmiles("");
      });
    },
    editMolecule(mol) {
      this.editor.setSmiles(mol.canonical_smiles);
      this.percentage = parseInt(mol.pivot.percentage_composition);
      axios.delete(
        "/dashboard/studies/" + this.study.id + "/molecule/" + mol.id
      ).then((res) => {
        this.study.sample.molecules = res.data;
      });
    },
    saveMolecule(mol, study) {
      if (!study) {
        study = this.study;
      }
      if (!mol) {
        let mol2 = this.editor.getMolFile();
        this.standardizeMolecules(mol2).then((res) => {
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
      axios.post("/dashboard/studies/" + study.id + "/molecule", {
        InChI: mol.inchi,
        InChIKey: mol.inchikey,
        percentage: 0,
        mol: mol.standardized_mol,
        canonical_smiles: mol.canonical_smiles
      }).then((res) => {
        study.sample.molecules = res.data;
        this.smiles = "";
        this.percentage = 0;
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_study_content = resolveComponent("study-content");
  const _component_PencilIcon = resolveComponent("PencilIcon");
  const _component_ToolTip = resolveComponent("ToolTip");
  const _component_Depictor2D = resolveComponent("Depictor2D");
  const _component_TrashIcon = resolveComponent("TrashIcon");
  const _component_InformationCircleIcon = resolveComponent("InformationCircleIcon");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_slider = resolveComponent("slider");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_study_content, {
    model: "study",
    project: $props.project,
    study: $props.study,
    team: $props.team,
    members: $props.members,
    "available-roles": $props.availableRoles,
    "study-permissions": $props.studyPermissions,
    "study-role": $props.studyRole,
    current: "About"
  }, {
    "study-section": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="divide-y divide-gray-200 sm:col-span-9"${_scopeId}><div class="py-3 px-4 sm:p-6 lg:pb-8"${_scopeId}><div class="mt-0"${_scopeId}><div class="mb-4"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"${_scopeId}> Keywords </span>`);
        if ($options.canUpdateStudy) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><dd class="mt-1 text-md text-gray-900 space-y-5"${_scopeId}><p${_scopeId}><!--[-->`);
        ssrRenderList($props.study.tags, (tag) => {
          _push2(`<span class="mr-2"${_scopeId}><span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"${_scopeId}><svg class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400" fill="currentColor" viewBox="0 0 8 8"${_scopeId}><circle cx="4" cy="4" r="3"${_scopeId}></circle></svg> ${ssrInterpolate(tag.name["en"])}</span></span>`);
        });
        _push2(`<!--]--></p></dd></div><div class="mb-4"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"${_scopeId}> License </span>`);
        if ($options.canUpdateStudy) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div${_scopeId}>`);
        if ($props.license) {
          _push2(`<dd class="text-md text-gray-900 space-y-5"${_scopeId}><p style="${ssrRenderStyle({ "max-width": "100ch !important" })}" class="prose mt-1 text-sm text-blue-gray-500"${_scopeId}>${ssrInterpolate($props.license.title)} `);
          if ($props.study.license_id) {
            _push2(ssrRenderComponent(_component_ToolTip, {
              class: "inline h-4 w-4 ml-0",
              text: $props.license.description
            }, null, _parent2, _scopeId));
          } else {
            _push2(`<!---->`);
          }
          _push2(`</p></dd>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="mb-4"${_scopeId}><div${_scopeId}><div${_scopeId}><div class="py-4 max-w-7xl mx-auto"${_scopeId}><div class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between"${_scopeId}><h3 class="text-lg leading-6 font-medium text-gray-900"${_scopeId}> Sample chemical composition </h3></div></div><div class="grid grid-cols-2 gap-2"${_scopeId}><div class="pr-2"${_scopeId}>`);
        if ($props.study.sample.molecules.length > 0) {
          _push2(`<div class="flow-root"${_scopeId}><ul role="list" class="-mb-8"${_scopeId}><!--[-->`);
          ssrRenderList($props.study.sample.molecules, (molecule) => {
            _push2(`<li${_scopeId}><div class="relative pb-8"${_scopeId}><span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"${_scopeId}></span><div class="relative flex items-start space-x-3"${_scopeId}>`);
            if (molecule && molecule.pivot) {
              _push2(`<div class="relative"${_scopeId}><div class="rounded-full border p-2 z-10 bg-gray-100 text-sm"${_scopeId}>${ssrInterpolate(molecule.pivot.percentage_composition)}% </div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="min-w-0 flex-1"${_scopeId}><div${_scopeId}><div class="text-sm"${_scopeId}><a class="break-all font-medium text-gray-900"${_scopeId}>${ssrInterpolate(molecule.standard_inchi)}</a></div></div><div class="mt-2 text-sm text-gray-700"${_scopeId}><div class="rounded-md border my-3 flex justify-center items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Depictor2D, {
              molecule: molecule.canonical_smiles
            }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
            if ($options.canUpdateStudy) {
              _push2(`<button class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_TrashIcon, { class: "w-4 h-4 inline mr-1" }, null, _parent2, _scopeId));
              _push2(`Delete </button>`);
            } else {
              _push2(`<!---->`);
            }
            if ($options.canUpdateStudy) {
              _push2(`<button class="inline-flex ml-2 items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 inline mr-1" }, null, _parent2, _scopeId));
              _push2(`Edit </button>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></div></li>`);
          });
          _push2(`<!--]--></ul><div class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"${_scopeId}> Sample chemical composition </div></div>`);
        } else {
          _push2(`<div${_scopeId}><div class="text-center my-10 py-10"${_scopeId}><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No structures associated with the sample! </h3>`);
          if (!$props.study.is_public) {
            _push2(`<p class="mt-1 text-sm text-gray-500"${_scopeId}> Get started by adding a new molecule. </p>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div>`);
        }
        _push2(`</div>`);
        if ($options.canUpdateStudy) {
          _push2(`<div class="pl-2"${_scopeId}><div class="sm:col-span-4"${_scopeId}><label for="email" class="block text-sm font-medium text-gray-700"${_scopeId}> SMILES <div class="tooltip"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_InformationCircleIcon, { class: "w-5 h-5 inline" }, null, _parent2, _scopeId));
          _push2(`<span class="w-64 bg-gray-900 text-white p-4 shadow-lg rounded-md tooltiptext"${_scopeId}>Simplified molecular-input line-entry system (SMILES) is a specification in the form of a line notation for describing the structure of chemical species using short ASCII strings. For more information please checkout <a href="https://en.wikipedia.org/wiki/Simplified_molecular-input_line-entry_system" class="text-gray-400" target="_blank"${_scopeId}>here</a> and <a href="https://www.daylight.com/dayhtml/doc/theory/theory.smiles.html" class="text-gray-400" target="_blank"${_scopeId}>here</a>.</span></div></label><div class="mt-1 mb-2"${_scopeId}><input id="smiles"${ssrRenderAttr("value", $data.smiles)} name="smiles" type="text" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"${_scopeId}>`);
          if ($data.smiles && $data.smiles != "") {
            _push2(`<button class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2"${_scopeId}> Load Structure </button>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.errorMessage,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div></div><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex justify-center"${_scopeId}><span class="px-2 bg-white text-sm text-gray-500"${_scopeId}> Or </span></div></div><span class="float-right text-xs cursor-pointer hover:text-blue-700"${_scopeId}><a href="https://docs.nmrxiv.org/submission-guides/editor.html" target="_blank"${_scopeId}>Need help?</a></span><div id="structureSearchEditor" class="w-full border my-4 rounded-md" style="${ssrRenderStyle({ "height": "400px" })}"${_scopeId}></div><div class="mt-1 mb-6"${_scopeId}><label for="email" class="block text-sm font-medium text-gray-700"${_scopeId}> Percentage composition (${ssrInterpolate($data.percentage)}%) `);
          _push2(ssrRenderComponent(_component_ToolTip, {
            class: "inline h-4 w-12 ml-0",
            text: "If the molecule percentage is unknown, please keep it as 0."
          }, null, _parent2, _scopeId));
          _push2(`</label>`);
          _push2(ssrRenderComponent(_component_slider, {
            modelValue: $data.percentage,
            "onUpdate:modelValue": ($event) => $data.percentage = $event,
            min: 0,
            max: $options.getMax,
            height: 10,
            color: "#000",
            "track-color": "#999"
          }, null, _parent2, _scopeId));
          _push2(`</div><button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}> ADD </button></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
        if ($props.study) {
          _push2(`<div${_scopeId}>`);
          if ($props.study.molecules && $props.study.molecules.length == 0) {
            _push2(`<div${_scopeId}><template${_scopeId}><button type="button" class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"${_scopeId}></path></svg><span class="mt-2 block text-sm font-medium text-gray-900"${_scopeId}> Create a new database </span></button></template></div>`);
          } else {
            _push2(`<!---->`);
          }
          if ($props.study.molecules && $props.study.molecules.length > 0) {
            _push2(`<div${_scopeId}> Mols </div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div><div class="mb-4"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"${_scopeId}> Description </span>`);
        if ($options.canUpdateStudy) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><dd class="mt-1 text-md text-gray-900 space-y-5"${_scopeId}><p style="${ssrRenderStyle({ "max-width": "100ch !important" })}" class="prose mt-1 text-sm text-blue-gray-500"${_scopeId}>${_ctx.md($props.study.description)}</p></dd></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "divide-y divide-gray-200 sm:col-span-9" }, [
            createVNode("div", { class: "py-3 px-4 sm:p-6 lg:pb-8" }, [
              createVNode("div", { class: "mt-0" }, [
                createVNode("div", { class: "mb-4" }, [
                  createVNode("div", { class: "relative" }, [
                    createVNode("div", {
                      class: "absolute inset-0 flex items-center",
                      "aria-hidden": "true"
                    }, [
                      createVNode("div", { class: "w-full border-t border-gray-300" })
                    ]),
                    createVNode("div", { class: "relative flex items-center justify-between" }, [
                      createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500" }, " Keywords "),
                      $options.canUpdateStudy ? (openBlock(), createBlock("button", {
                        key: 0,
                        type: "button",
                        class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        onClick: $options.openStudyDetailsPane
                      }, [
                        createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                        createVNode("span", null, "Edit")
                      ], 8, ["onClick"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("dd", { class: "mt-1 text-md text-gray-900 space-y-5" }, [
                    createVNode("p", null, [
                      (openBlock(true), createBlock(Fragment, null, renderList($props.study.tags, (tag) => {
                        return openBlock(), createBlock("span", {
                          key: tag.id,
                          class: "mr-2"
                        }, [
                          createVNode("span", { class: "inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800" }, [
                            (openBlock(), createBlock("svg", {
                              class: "-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400",
                              fill: "currentColor",
                              viewBox: "0 0 8 8"
                            }, [
                              createVNode("circle", {
                                cx: "4",
                                cy: "4",
                                r: "3"
                              })
                            ])),
                            createTextVNode(" " + toDisplayString(tag.name["en"]), 1)
                          ])
                        ]);
                      }), 128))
                    ])
                  ])
                ]),
                createVNode("div", { class: "mb-4" }, [
                  createVNode("div", { class: "relative" }, [
                    createVNode("div", {
                      class: "absolute inset-0 flex items-center",
                      "aria-hidden": "true"
                    }, [
                      createVNode("div", { class: "w-full border-t border-gray-300" })
                    ]),
                    createVNode("div", { class: "relative flex items-center justify-between" }, [
                      createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500" }, " License "),
                      $options.canUpdateStudy ? (openBlock(), createBlock("button", {
                        key: 0,
                        type: "button",
                        class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        onClick: $options.openStudyDetailsPane
                      }, [
                        createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                        createVNode("span", null, "Edit")
                      ], 8, ["onClick"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("div", null, [
                    $props.license ? (openBlock(), createBlock("dd", {
                      key: 0,
                      class: "text-md text-gray-900 space-y-5"
                    }, [
                      createVNode("p", {
                        style: { "max-width": "100ch !important" },
                        class: "prose mt-1 text-sm text-blue-gray-500"
                      }, [
                        createTextVNode(toDisplayString($props.license.title) + " ", 1),
                        $props.study.license_id ? (openBlock(), createBlock(_component_ToolTip, {
                          key: 0,
                          class: "inline h-4 w-4 ml-0",
                          text: $props.license.description
                        }, null, 8, ["text"])) : createCommentVNode("", true)
                      ])
                    ])) : createCommentVNode("", true)
                  ])
                ]),
                createVNode("div", { class: "mb-4" }, [
                  createVNode("div", null, [
                    createVNode("div", null, [
                      createVNode("div", { class: "py-4 max-w-7xl mx-auto" }, [
                        createVNode("div", { class: "pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between" }, [
                          createVNode("h3", { class: "text-lg leading-6 font-medium text-gray-900" }, " Sample chemical composition ")
                        ])
                      ]),
                      createVNode("div", { class: "grid grid-cols-2 gap-2" }, [
                        createVNode("div", { class: "pr-2" }, [
                          $props.study.sample.molecules.length > 0 ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "flow-root"
                          }, [
                            createVNode("ul", {
                              role: "list",
                              class: "-mb-8"
                            }, [
                              (openBlock(true), createBlock(Fragment, null, renderList($props.study.sample.molecules, (molecule) => {
                                return openBlock(), createBlock("li", {
                                  key: molecule.standard_inchi
                                }, [
                                  createVNode("div", { class: "relative pb-8" }, [
                                    createVNode("span", {
                                      class: "absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200",
                                      "aria-hidden": "true"
                                    }),
                                    createVNode("div", { class: "relative flex items-start space-x-3" }, [
                                      molecule && molecule.pivot ? (openBlock(), createBlock("div", {
                                        key: 0,
                                        class: "relative"
                                      }, [
                                        createVNode("div", { class: "rounded-full border p-2 z-10 bg-gray-100 text-sm" }, toDisplayString(molecule.pivot.percentage_composition) + "% ", 1)
                                      ])) : createCommentVNode("", true),
                                      createVNode("div", { class: "min-w-0 flex-1" }, [
                                        createVNode("div", null, [
                                          createVNode("div", { class: "text-sm" }, [
                                            createVNode("a", { class: "break-all font-medium text-gray-900" }, toDisplayString(molecule.standard_inchi), 1)
                                          ])
                                        ]),
                                        createVNode("div", { class: "mt-2 text-sm text-gray-700" }, [
                                          createVNode("div", { class: "rounded-md border my-3 flex justify-center items-center" }, [
                                            createVNode(_component_Depictor2D, {
                                              molecule: molecule.canonical_smiles
                                            }, null, 8, ["molecule"])
                                          ])
                                        ]),
                                        $options.canUpdateStudy ? (openBlock(), createBlock("button", {
                                          key: 0,
                                          class: "inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",
                                          onClick: ($event) => $options.deleteMolecule(
                                            molecule
                                          )
                                        }, [
                                          createVNode(_component_TrashIcon, { class: "w-4 h-4 inline mr-1" }),
                                          createTextVNode("Delete ")
                                        ], 8, ["onClick"])) : createCommentVNode("", true),
                                        $options.canUpdateStudy ? (openBlock(), createBlock("button", {
                                          key: 1,
                                          class: "inline-flex ml-2 items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",
                                          onClick: ($event) => $options.editMolecule(
                                            molecule
                                          )
                                        }, [
                                          createVNode(_component_PencilIcon, { class: "w-4 h-4 inline mr-1" }),
                                          createTextVNode("Edit ")
                                        ], 8, ["onClick"])) : createCommentVNode("", true)
                                      ])
                                    ])
                                  ])
                                ]);
                              }), 128))
                            ]),
                            createVNode("div", { class: "rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center" }, " Sample chemical composition ")
                          ])) : (openBlock(), createBlock("div", { key: 1 }, [
                            createVNode("div", { class: "text-center my-10 py-10" }, [
                              createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No structures associated with the sample! "),
                              !$props.study.is_public ? (openBlock(), createBlock("p", {
                                key: 0,
                                class: "mt-1 text-sm text-gray-500"
                              }, " Get started by adding a new molecule. ")) : createCommentVNode("", true)
                            ])
                          ]))
                        ]),
                        $options.canUpdateStudy ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "pl-2"
                        }, [
                          createVNode("div", { class: "sm:col-span-4" }, [
                            createVNode("label", {
                              for: "email",
                              class: "block text-sm font-medium text-gray-700"
                            }, [
                              createTextVNode(" SMILES "),
                              createVNode("div", { class: "tooltip" }, [
                                createVNode(_component_InformationCircleIcon, { class: "w-5 h-5 inline" }),
                                createVNode("span", { class: "w-64 bg-gray-900 text-white p-4 shadow-lg rounded-md tooltiptext" }, [
                                  createTextVNode("Simplified molecular-input line-entry system (SMILES) is a specification in the form of a line notation for describing the structure of chemical species using short ASCII strings. For more information please checkout "),
                                  createVNode("a", {
                                    href: "https://en.wikipedia.org/wiki/Simplified_molecular-input_line-entry_system",
                                    class: "text-gray-400",
                                    target: "_blank"
                                  }, "here"),
                                  createTextVNode(" and "),
                                  createVNode("a", {
                                    href: "https://www.daylight.com/dayhtml/doc/theory/theory.smiles.html",
                                    class: "text-gray-400",
                                    target: "_blank"
                                  }, "here"),
                                  createTextVNode(".")
                                ])
                              ])
                            ]),
                            createVNode("div", { class: "mt-1 mb-2" }, [
                              withDirectives(createVNode("input", {
                                id: "smiles",
                                "onUpdate:modelValue": ($event) => $data.smiles = $event,
                                name: "smiles",
                                type: "text",
                                class: "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md",
                                onBlur: $options.loadSmiles
                              }, null, 40, ["onUpdate:modelValue", "onBlur"]), [
                                [vModelText, $data.smiles]
                              ]),
                              $data.smiles && $data.smiles != "" ? (openBlock(), createBlock("button", {
                                key: 0,
                                class: "inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2",
                                onClick: $options.loadSmiles
                              }, " Load Structure ", 8, ["onClick"])) : createCommentVNode("", true),
                              createVNode(_component_jet_input_error, {
                                message: $data.errorMessage,
                                class: "mt-2"
                              }, null, 8, ["message"])
                            ])
                          ]),
                          createVNode("div", { class: "relative" }, [
                            createVNode("div", {
                              class: "absolute inset-0 flex items-center",
                              "aria-hidden": "true"
                            }, [
                              createVNode("div", { class: "w-full border-t border-gray-300" })
                            ]),
                            createVNode("div", { class: "relative flex justify-center" }, [
                              createVNode("span", { class: "px-2 bg-white text-sm text-gray-500" }, " Or ")
                            ])
                          ]),
                          createVNode("span", { class: "float-right text-xs cursor-pointer hover:text-blue-700" }, [
                            createVNode("a", {
                              href: "https://docs.nmrxiv.org/submission-guides/editor.html",
                              target: "_blank"
                            }, "Need help?")
                          ]),
                          createVNode("div", {
                            id: "structureSearchEditor",
                            class: "w-full border my-4 rounded-md",
                            style: { "height": "400px" }
                          }),
                          createVNode("div", { class: "mt-1 mb-6" }, [
                            createVNode("label", {
                              for: "email",
                              class: "block text-sm font-medium text-gray-700"
                            }, [
                              createTextVNode(" Percentage composition (" + toDisplayString($data.percentage) + "%) ", 1),
                              createVNode(_component_ToolTip, {
                                class: "inline h-4 w-12 ml-0",
                                text: "If the molecule percentage is unknown, please keep it as 0."
                              })
                            ]),
                            createVNode(_component_slider, {
                              modelValue: $data.percentage,
                              "onUpdate:modelValue": ($event) => $data.percentage = $event,
                              min: 0,
                              max: $options.getMax,
                              height: 10,
                              color: "#000",
                              "track-color": "#999"
                            }, null, 8, ["modelValue", "onUpdate:modelValue", "max"])
                          ]),
                          createVNode("button", {
                            type: "button",
                            class: "inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                            onClick: ($event) => $options.saveMolecule(null)
                          }, " ADD ", 8, ["onClick"])
                        ])) : createCommentVNode("", true)
                      ]),
                      $props.study ? (openBlock(), createBlock("div", { key: 0 }, [
                        $props.study.molecules && $props.study.molecules.length == 0 ? (openBlock(), createBlock("div", { key: 0 }, [
                          createVNode("template", null, [
                            createVNode("button", {
                              type: "button",
                              class: "relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            }, [
                              (openBlock(), createBlock("svg", {
                                class: "mx-auto h-12 w-12 text-gray-400",
                                xmlns: "http://www.w3.org/2000/svg",
                                stroke: "currentColor",
                                fill: "none",
                                viewBox: "0 0 48 48",
                                "aria-hidden": "true"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  "stroke-width": "2",
                                  d: "M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"
                                })
                              ])),
                              createVNode("span", { class: "mt-2 block text-sm font-medium text-gray-900" }, " Create a new database ")
                            ])
                          ])
                        ])) : createCommentVNode("", true),
                        $props.study.molecules && $props.study.molecules.length > 0 ? (openBlock(), createBlock("div", { key: 1 }, " Mols ")) : createCommentVNode("", true)
                      ])) : createCommentVNode("", true)
                    ])
                  ])
                ]),
                createVNode("div", { class: "mb-4" }, [
                  createVNode("div", { class: "relative" }, [
                    createVNode("div", {
                      class: "absolute inset-0 flex items-center",
                      "aria-hidden": "true"
                    }, [
                      createVNode("div", { class: "w-full border-t border-gray-300" })
                    ]),
                    createVNode("div", { class: "relative flex items-center justify-between" }, [
                      createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500" }, " Description "),
                      $options.canUpdateStudy ? (openBlock(), createBlock("button", {
                        key: 0,
                        type: "button",
                        class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        onClick: $options.openStudyDetailsPane
                      }, [
                        createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                        createVNode("span", null, "Edit")
                      ], 8, ["onClick"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("dd", { class: "mt-1 text-md text-gray-900 space-y-5" }, [
                    createVNode("p", {
                      style: { "max-width": "100ch !important" },
                      class: "prose mt-1 text-sm text-blue-gray-500",
                      innerHTML: _ctx.md($props.study.description)
                    }, null, 8, ["innerHTML"])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/About.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const About = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  About as default
};
