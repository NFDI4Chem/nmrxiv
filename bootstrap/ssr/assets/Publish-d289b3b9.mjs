import { A as AppLayout, C as Create, S as SpectraEditor, V as Validation } from "./AppLayout-b93d1c11.mjs";
import TeamProjects from "./Index-a64ae412.mjs";
import { O as Onboarding } from "./Onboarding-31fdf998.mjs";
import { Link } from "@inertiajs/vue3";
import { resolveComponent, mergeProps, withCtx, createVNode, toDisplayString, useSSRContext, computed, ref, createTextVNode, openBlock, createBlock, withDirectives, vModelText, Fragment, renderList, createCommentVNode, vModelCheckbox } from "vue";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import axios from "axios";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import VueTagsInput from "@sipec/vue3-tags-input";
import slider from "vue3-slider";
import Datepicker from "@vuepic/vue-datepicker";
/* empty css                */import { M as ManageAuthor, a as ManageCitation } from "./ManageCitation-00abf851.mjs";
import { T as ToolTip } from "./ToolTip-cf9882a3.mjs";
import { C as Citation } from "./Citation-80069afd.mjs";
import { A as AuthorCard, C as CitationCard } from "./CitationCard-1c6db289.mjs";
import { LockClosedIcon, LockOpenIcon, EnvelopeIcon, PencilIcon, StarIcon, XCircleIcon, ClipboardDocumentIcon, QuestionMarkCircleIcon, ExclamationTriangleIcon, TrashIcon, PlayIcon, PauseIcon, ArrowDownOnSquareStackIcon, BellAlertIcon, CalendarIcon } from "@heroicons/vue/24/solid";
import { D as Depictor2D } from "./Depictor2D-b60253ae.mjs";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderClass, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { S as SelectRich } from "./SelectRich-b58dcc3f.mjs";
import { T as ToggleButton } from "./ToggleButton-fec39d54.mjs";
import "ontology-elements/dist/index.js";
import { J as JetConfirmationModal } from "./ConfirmationModal-d0b47856.mjs";
import { J as JetSuccessButton } from "./SuccessButton-6c2ecb3a.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@vueuse/core";
import "@heroicons/vue/24/outline";
import "@headlessui/vue";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Modal-b2fb4cf2.mjs";
import "openchemlib/full.js";
import "dropzone";
import "axios-retry";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
import "./LoadingButton-8ae902b0.mjs";
import "vuedraggable";
const _sfc_main$1 = {
  components: {
    LockClosedIcon,
    LockOpenIcon,
    EnvelopeIcon,
    PencilIcon,
    StarIcon,
    Link,
    Depictor2D
  },
  props: ["study"],
  setup() {
  },
  data() {
    return {
      selectedPreviewIndex: 0
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
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Depictor2D = resolveComponent("Depictor2D");
  const _component_Link = resolveComponent("Link");
  const _component_LockClosedIcon = resolveComponent("LockClosedIcon");
  if ($props.study) {
    _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden" }, _attrs))}><div class="pt-2 px-2"><ul role="list"><li class="col-span-1 divide-y divide-gray-200 cursor-pointer"><div class="bg-white rounded-t-md flex justify-center items-center"><span>`);
    _push(ssrRenderComponent(_component_Depictor2D, {
      class: "py-2",
      molecule: $props.study.sample.molecules[0].canonical_smiles,
      "show-download": false
    }, null, _parent));
    _push(`</span></div></li></ul><div class="bg-white"></div></div><div class="flex-1 border-t bg-white p-3 pb-2 flex flex-col justify-between">`);
    if ($props.study.identifier) {
      _push(`<small class="text-gray-500">#${ssrInterpolate($props.study.identifier)}</small>`);
    } else {
      _push(`<!---->`);
    }
    _push(ssrRenderComponent(_component_Link, {
      href: "/upload?step=2&draft_id=" + $props.study.draft_id + "&sample=" + $props.study.id
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="flex items-center font-bold text-lg text-gray-600 truncate ellipsis"${_scopeId}>${ssrInterpolate($props.study.name)}</div>`);
        } else {
          return [
            createVNode("div", { class: "flex items-center font-bold text-lg text-gray-600 truncate ellipsis" }, toDisplayString($props.study.name), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div><div class="px-3 pb-3 pt-0 h-28 overflow-auto"><!--[-->`);
    ssrRenderList($props.study.datasets, (ds, $dsIndex) => {
      _push(`<span><div class="${ssrRenderClass([
        $props.study.has_nmrium || ds.has_nmrium ? "bg-green-100 text-gray-800" : "bg-gray-100 text-gray-800",
        "mb-0.5 inline-flex truncate break-words items-center px-3 py-0.5 rounded-full text-xs font-medium mr-1"
      ])}"><a>${ssrInterpolate(ds.name)} `);
      if (ds.type) {
        _push(`<span>(${ssrInterpolate(ds.type)})</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</a></div></span>`);
    });
    _push(`<!--]--></div><div class="px-3 border-t py-1"><div class="pt-1"><div class="float-left">`);
    if ($props.study.is_public) {
      _push(`<div class="flex items-center mt-1"><svg class="h-3 w-3 mr-1 text-green-400 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="512" height="512"><g id="globe"><path d="M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z"></path><path d="M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z"></path><path d="M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z"></path></g></svg><p class="text-xs text-gray-600">Public</p></div>`);
    } else {
      _push(`<div class="flex items-center mt-1">`);
      _push(ssrRenderComponent(_component_LockClosedIcon, { class: "w-3 h-3 mr-1 text-teal-600" }, null, _parent));
      _push(`<p class="text-xs text-gray-600">Private</p></div>`);
    }
    _push(`</div><div class="float-left p-1 text-xs text-gray-600"><span class="text-gray-400">Last updated on</span> ${ssrInterpolate(_ctx.formatDate($props.study.updated_at))}</div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/StudyInfo.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const StudyInfo = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    ManageAuthor,
    AuthorCard,
    ManageCitation,
    Citation,
    ToolTip,
    Datepicker,
    VueTagsInput,
    slider,
    SelectRich,
    AppLayout,
    computed,
    TeamProjects,
    Create,
    Onboarding,
    Link,
    JetInputError,
    JetSecondaryButton,
    JetButton,
    XCircleIcon,
    ClipboardDocumentIcon,
    QuestionMarkCircleIcon,
    ExclamationTriangleIcon,
    TrashIcon,
    PlayIcon,
    PauseIcon,
    PencilIcon,
    ArrowDownOnSquareStackIcon,
    BellAlertIcon,
    SpectraEditor,
    Validation,
    ToggleButton,
    StarIcon,
    CalendarIcon,
    StudyInfo,
    CitationCard,
    JetConfirmationModal,
    JetSuccessButton
  },
  props: ["user", "team", "project", "teamRole", "draft"],
  setup() {
    const manageAuthorElement = ref(null);
    const manageCitationElement = ref(null);
    return {
      manageAuthorElement,
      manageCitationElement
    };
  },
  data() {
    return {
      publishForm: this.$inertia.form({
        _method: "POST",
        project: {
          name: "",
          description: "",
          error_message: null,
          tags: [],
          tag: "",
          tags_array: [],
          owner_id: null,
          species: []
        },
        conditions: false,
        terms: false,
        enableProjectMode: false,
        releaseDate: this.setReleaseDate()
      }),
      licenses: null,
      license: null,
      returnUrl: "/dashboard",
      errors: null,
      projectSpecies: "",
      status: "draft",
      validation: null,
      showPublishConfirmationModal: false
    };
  },
  computed: {
    url() {
      return String(this.$page.props.url);
    },
    getMax() {
      if (this.selectedStudy) {
        let totalCount = 0;
        this.selectedStudy.sample.molecules.forEach((mol) => {
          totalCount += parseInt(mol.pivot.percentage_composition);
        });
        return 100 - totalCount;
      } else {
        return 100;
      }
    },
    primed() {
      return this.$page.props.auth.user.primed;
    },
    currentTab() {
      return this.tabs.find((t) => t.current);
    }
  },
  mounted() {
    if (this.draft) {
      this.publishForm.project.name = this.project.name ? this.project.name : this.draft.name;
      this.publishForm.enableProjectMode = this.draft.project_enabled;
      this.publishForm.project.description = this.project.description;
      let tags = [];
      this.project.tags.forEach((t) => {
        tags.push({
          text: t.name["en"]
        });
      });
      this.publishForm.project.tags = tags;
      this.license = this.project.license;
      this.status = this.project.status && this.project.status != "" ? this.project.status : "draft";
      this.publishForm.project.species = JSON.parse(this.project.species) ? JSON.parse(this.project.species) : [];
    }
    this.loadLicenses();
    this.$nextTick(() => {
      const urlSearchParams = new URLSearchParams(window.location.search);
      const params = Object.fromEntries(urlSearchParams.entries());
      let edit = params["edit"];
      if (edit == "citation") {
        this.toggleManageCitation();
      } else if (edit == "title") {
        this.scrollTo(document.getElementById("project-name"));
      } else if (edit == "description") {
        this.scrollTo(document.getElementById("project-desc"));
      } else if (edit == "keywords") {
        this.scrollTo(document.getElementById("project-keywords"));
      } else if (edit == "organism") {
        this.scrollTo(document.getElementById("project-organism"));
      } else if (edit == "license") {
        this.scrollTo(document.getElementById("license"));
      } else if (edit == "release") {
        this.scrollTo(document.getElementById("release"));
      } else if (edit == "authors") {
        this.toggleManageAuthor();
      }
    });
  },
  methods: {
    scrollTo(element) {
      this.$nextTick(() => {
        element.classList.add("shake");
        setTimeout(function() {
          element.classList.remove("shake");
        }, 2500);
      });
    },
    updateDraft() {
      axios.put("/dashboard/drafts/" + this.draft.id, {
        project_enabled: this.publishForm.enableProjectMode ? 1 : 0
      });
    },
    updateProject() {
      if (this.publishForm.project.tag && this.publishForm.project.tag != "") {
        let exists = false;
        this.publishForm.project.tags.forEach((t) => {
          if (t.text == this.publishForm.project.tag) {
            exists = true;
          }
        });
        if (!exists) {
          this.publishForm.project.tags.push({
            text: this.publishForm.project.tag
          });
          this.publishForm.project.tag = "";
        }
      }
      this.loadingStep = true;
      axios.put(route("dashboard.project.update", this.project.id), {
        name: this.publishForm.project.name,
        description: this.publishForm.project.description,
        tags: this.publishForm.project.tags,
        tags_array: this.publishForm.project.tags ? this.publishForm.project.tags.map((a) => a.text) : [],
        license_id: this.license ? this.license.id : null,
        species: this.publishForm.project.species,
        release_date: this.publishForm.releaseDate
      });
    },
    updateSpecies(species) {
      if (species && species != "") {
        this.publishForm.project.species.push(species);
        this.projectSpecies = "";
        this.updateProject();
      }
    },
    removeSpecies(index) {
      if (index > -1) {
        this.publishForm.project.species.splice(index, 1);
      }
    },
    getTarget(id) {
      var target = null;
      if (id) {
        target = "_blank";
      }
      return target;
    },
    getCitationLink(doi) {
      var link = "#";
      if (doi) {
        link = "https://doi.org/" + doi;
      }
      return link;
    },
    setReleaseDate() {
      var current_date = /* @__PURE__ */ new Date();
      var relase_date = /* @__PURE__ */ new Date();
      relase_date.setDate(current_date.getDate());
      return relase_date;
    },
    loadLicenses() {
      if (this.$page.props.licenses) {
        this.licenses = this.$page.props.licenses;
      } else {
        axios.get(route("licenses")).then((res) => {
          this.licenses = res.data;
          this.$page.props.licenses = res.data;
        });
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
    toggleManageAuthor() {
      this.manageAuthorElement.toggleDialog();
    },
    toggleManageCitation() {
      this.manageCitationElement.toggleDialog();
    },
    publish() {
      this.showPublishConfirmationModal = false;
      if (this.publishForm.conditions && this.publishForm.terms) {
        this.errors = null;
        axios.post(
          route("dashboard.project.publish", this.project.id),
          this.publishForm
        ).catch((err) => {
          this.errors = err.response.data.errors;
          this.validation = err.response.data.validation.report;
        }).then((response) => {
          this.status = response.data.project.status;
        });
      }
    }
    // trackProject() {
    //     axios
    //         .get("/projects/status/" + this.project.id + "/queue")
    //         .then((response) => {
    //             this.status = response.data.status;
    //             if (this.status != "complete") {
    //                 setTimeout(() => this.trackProject(), 10000);
    //             } else {
    //                 return this.$inertia.visit(
    //                     this.route("dashboard.projects", [this.project.id])
    //                 );
    //             }
    //         });
    // },
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  const _component_toggle_button = resolveComponent("toggle-button");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_vue_tags_input = resolveComponent("vue-tags-input");
  const _component_PencilIcon = resolveComponent("PencilIcon");
  const _component_citation_card = resolveComponent("citation-card");
  const _component_author_card = resolveComponent("author-card");
  const _component_study_info = resolveComponent("study-info");
  const _component_Datepicker = resolveComponent("Datepicker");
  const _component_select_rich = resolveComponent("select-rich");
  const _component_Validation = resolveComponent("Validation");
  const _component_manage_author = resolveComponent("manage-author");
  const _component_manage_citation = resolveComponent("manage-citation");
  const _component_jet_confirmation_modal = resolveComponent("jet-confirmation-modal");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_success_button = resolveComponent("jet-success-button");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Publish Data" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-6"${_scopeId}><div class="py-4"${_scopeId}><div${_scopeId}><span class="ml-12 text-sm font-bold text-teal-600 group-hover:text-teal-800"${_scopeId}>Step 3 / 3 - <span${_scopeId}> Publish data </span></span><div class="w-full sm:flex sm:items-center sm:justify-between"${_scopeId}><h3 class="text-sm text-gray-700 uppercase font-bold tracking-widest"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          class: "ml-1 mr-2 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
          href: "/upload?draft_id=" + $props.draft.id + "&step=2"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` ← `);
            } else {
              return [
                createTextVNode(" ← ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`<span${_scopeId}><p class="inline"${_scopeId}><b${_scopeId}>${ssrInterpolate($props.draft.name)}</b></p></span></h3></div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-6" }, [
              createVNode("div", { class: "py-4" }, [
                createVNode("div", null, [
                  createVNode("span", { class: "ml-12 text-sm font-bold text-teal-600 group-hover:text-teal-800" }, [
                    createTextVNode("Step 3 / 3 - "),
                    createVNode("span", null, " Publish data ")
                  ]),
                  createVNode("div", { class: "w-full sm:flex sm:items-center sm:justify-between" }, [
                    createVNode("h3", { class: "text-sm text-gray-700 uppercase font-bold tracking-widest" }, [
                      createVNode(_component_Link, {
                        class: "ml-1 mr-2 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        href: "/upload?draft_id=" + $props.draft.id + "&step=2"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" ← ")
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode("span", null, [
                        createVNode("p", { class: "inline" }, [
                          createVNode("b", null, toDisplayString($props.draft.name), 1)
                        ])
                      ])
                    ])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="mb-10"${_scopeId}>`);
        if ($data.status == "draft") {
          _push2(`<div${_scopeId}><div id="project-details" class="p-4"${_scopeId}><div class="p-8"${_scopeId}><div${_scopeId}><label class="block tracking-wider text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"${_scopeId}>`);
          if ($data.publishForm.enableProjectMode) {
            _push2(`<small${_scopeId}>PUBLISH AS PROJECT</small>`);
          } else {
            _push2(`<small${_scopeId}>PUBLISH SAMPLES</small>`);
          }
          _push2(`</label>`);
          _push2(ssrRenderComponent(_component_toggle_button, {
            enabled: $data.publishForm.enableProjectMode,
            "onUpdate:enabled": ($event) => $data.publishForm.enableProjectMode = $event,
            onBlur: $options.updateDraft
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
          if ($data.publishForm.enableProjectMode) {
            _push2(`<div${_scopeId}><div class="p-4 bg-gray-100 rounded-md"${_scopeId}><div id="project-name" class="mb-3"${_scopeId}><label for="project-name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Project Name </label><div class="mt-1"${_scopeId}><input${ssrRenderAttr("value", $data.publishForm.project.name)} type="text" name="project-name" class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"${_scopeId}></div>`);
            _push2(ssrRenderComponent(_component_jet_input_error, {
              message: $data.publishForm.errors.name,
              class: "mt-2"
            }, null, _parent2, _scopeId));
            _push2(`</div><div id="project-desc" class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}><span${_scopeId}>Project Description </span></label><div class="mt-1"${_scopeId}><textarea id="description" name="project-description" placeholder="Describe this project" rows="3" class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"${_scopeId}>${ssrInterpolate(
              $data.publishForm.project.description
            )}</textarea></div>`);
            _push2(ssrRenderComponent(_component_jet_input_error, {
              message: $data.publishForm.errors.description,
              class: "mt-2"
            }, null, _parent2, _scopeId));
            _push2(`</div><div id="project-keywords" class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Keywords </label><div${_scopeId}>`);
            _push2(ssrRenderComponent(_component_vue_tags_input, {
              modelValue: $data.publishForm.project.tag,
              "onUpdate:modelValue": ($event) => $data.publishForm.project.tag = $event,
              placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
              separators: [";", ","],
              "max-width": "100%",
              tags: $data.publishForm.project.tags,
              onBlur: $options.updateProject,
              onTagsChanged: (newTags) => $data.publishForm.project.tags = newTags
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_component_jet_input_error, {
              message: $data.publishForm.errors.tags,
              class: "mt-2"
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}><div id="project-organism" class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700"${_scopeId}> Organism (Optional) </label><div class="mt-2 sm:flex sm:items-start sm:justify-between"${_scopeId}><div class="text-sm text-gray-500 w-full"${_scopeId}><ontology-autocomplete class="rounded-md" format="text"${ssrRenderAttr("value", $data.projectSpecies)} placeholder="Search species"${_scopeId}></ontology-autocomplete></div><div class="mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center"${_scopeId}><button type="button" class="inline-flex items-center gap-x-1.5 py-3 bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"${_scopeId}><svg class="-ml-0.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"${_scopeId}></path></svg> Add </button></div></div><div class="mt-2"${_scopeId}><!--[-->`);
            ssrRenderList($data.publishForm.project.species, (species, $index) => {
              _push2(`<div class="bg-gray-100 border text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"${_scopeId}><ontology-term-annotation${ssrRenderAttr("annotation", species)}${_scopeId}></ontology-term-annotation><span class="cursor-pointer"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 ml-2"${_scopeId}><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"${_scopeId}></path></svg></span></div>`);
            });
            _push2(`<!--]--></div></div></div><div class="mb-2"${_scopeId}><div class="relative pl-2"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Citation </span><button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
            _push2(`<span${_scopeId}>Edit</span></button></div></div><dd class="mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto"${_scopeId}><div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"${_scopeId}><div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_citation_card, {
              citations: $props.project.citations
            }, null, _parent2, _scopeId));
            _push2(`</div></div></dd></div><div class="mb-2"${_scopeId}><div class="relative pl-2"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Author </span><button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
            _push2(`<span${_scopeId}>Edit</span></button></div></div><dd class="mt-2 text-md text-gray-900 space-y-5"${_scopeId}><div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_author_card, {
              authors: $props.project.authors
            }, null, _parent2, _scopeId));
            _push2(`</div></dd></div></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`<div class="mt-3"${_scopeId}><label class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"${_scopeId}>${ssrInterpolate(_ctx.pluralize("SAMPLE", $props.project.studies.length))} (${ssrInterpolate($props.project.studies.length)}) </label><div${_scopeId}><div class="mt-4 mb-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl"${_scopeId}><!--[-->`);
          ssrRenderList($props.project.studies, (study) => {
            _push2(`<div${_scopeId}>`);
            _push2(ssrRenderComponent(_component_study_info, { study }, null, _parent2, _scopeId));
            _push2(`</div>`);
          });
          _push2(`<!--]--></div></div></div><div id="release" class="mt-3"${_scopeId}><label class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"${_scopeId}> Release Date </label>`);
          _push2(ssrRenderComponent(_component_Datepicker, {
            modelValue: $data.publishForm.releaseDate,
            "onUpdate:modelValue": [($event) => $data.publishForm.releaseDate = $event, $options.updateProject]
          }, null, _parent2, _scopeId));
          _push2(`<p class="mt-1 text-sm text-gray-500"${_scopeId}> Publish your data now or choose a release date to auto publish your project to public. </p></div><div class="mt-5"${_scopeId}><div id="license" class="mb-4"${_scopeId}><div${_scopeId}><div class="mt-6 grid grid-cols-1 gap-x-4 sm:grid-cols-1"${_scopeId}>`);
          if ($data.licenses) {
            _push2(`<div${_scopeId}><span class="float-right text-xs cursor-pointer hover:text-blue-700 mt-2"${_scopeId}><a target="_blank" href="https://docs.nmrxiv.org/submission-guides/licenses"${_scopeId}>How to choose the right license?</a></span>`);
            _push2(ssrRenderComponent(_component_select_rich, {
              selected: $data.license,
              "onUpdate:selected": [($event) => $data.license = $event, $options.updateProject],
              label: "License",
              items: $data.licenses
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div></div></div><div class="mt-5"${_scopeId}><h3 class="text-lg font-bold text-gray-400"${_scopeId}> Terms &amp; Conditions </h3><div class="mt-3"${_scopeId}><div class="ml-2"${_scopeId}><div class="flex items-top"${_scopeId}><input id="conditions"${ssrIncludeBooleanAttr(Array.isArray($data.publishForm.conditions) ? ssrLooseContain($data.publishForm.conditions, null) : $data.publishForm.conditions) ? " checked" : ""} type="checkbox" class="rounded mt-1 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="conditions"${_scopeId}><div class="ml-2 text-sm"${_scopeId}>`);
          if ($data.publishForm.enableProjectMode) {
            _push2(`<span${_scopeId}> I understand once the project is published, all the underlying samples and spectra will also be made public and agree to make this data persistently available in the nmrXiv platform. </span>`);
          } else {
            _push2(`<span${_scopeId}> I understand once the samples are published, all the underlying spectra will also be made public and agree to make this data persistently available in the nmrXiv platform. </span>`);
          }
          _push2(`</div></div></div></div><div class="mt-2"${_scopeId}><div class="ml-2"${_scopeId}><div class="flex items-center"${_scopeId}><input id="terms"${ssrIncludeBooleanAttr(Array.isArray($data.publishForm.terms) ? ssrLooseContain($data.publishForm.terms, null) : $data.publishForm.terms) ? " checked" : ""} type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="terms"${_scopeId}><div class="ml-2 text-sm"${_scopeId}> I agree to the <a target="_blank"${ssrRenderAttr("href", _ctx.route("terms.show"))} class="underline text-sm text-gray-600 hover:text-gray-900"${_scopeId}>Terms of Service</a> and <a target="_blank"${ssrRenderAttr("href", _ctx.route("policy.show"))} class="underline text-sm text-gray-600 hover:text-gray-900"${_scopeId}>Privacy Policy</a> and hereby also grant nmrXiv permissions to distribute the datasets (and meta-data) under the specified license. </div></div></div></div></div></div><div${_scopeId}><div id="publish-details"${_scopeId}> </div><div class="px-8 pb-8 pt-0"${_scopeId}><button type="button" class="${ssrRenderClass([
            !$data.publishForm.terms || !$data.publishForm.conditions ? "bg-gray-200 cursor-not-allowed" : "bg-green-600 hover:bg-green-700",
            "inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto sm:text-sm"
          ])}"${ssrIncludeBooleanAttr(
            !$data.publishForm.terms && !$data.publishForm.conditions
          ) ? " disabled" : ""}${_scopeId}> Publish </button>`);
          _push2(ssrRenderComponent(_component_Link, {
            type: "button",
            class: "mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm",
            href: _ctx.route("dashboard")
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Not right yet `);
              } else {
                return [
                  createTextVNode(" Not right yet ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
          if ($data.errors) {
            _push2(`<div${_scopeId}><div class="rounded-md bg-red-50 p-4 mx-4 mb-4"${_scopeId}><div${_scopeId}><div class="flex"${_scopeId}><div class="flex-shrink-0"${_scopeId}><svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"${_scopeId}></path></svg></div><div class="ml-3"${_scopeId}><h3 class="text-sm font-medium text-red-800"${_scopeId}> Error publishing your project </h3><div class="mt-2 text-sm text-red-700"${_scopeId}><ul role="list" class="list-disc space-y-1 pl-5"${_scopeId}><li${_scopeId}>${ssrInterpolate($data.errors)}</li></ul></div></div></div>`);
            if ($data.validation) {
              _push2(`<div class="mt-6"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_Validation, {
                project: $props.project,
                validation: $data.validation,
                draft: $props.draft.id
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div><div class="mx-4"${_scopeId}><div class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"${_scopeId}><b${_scopeId}>Whats next?</b><div${_scopeId}><p${_scopeId}> Upon clicking publish, your project is submitted to our queue system for automatic processing. Once successfully processed, your data is assigned with stable identifiers, and DOIs are generated. You will receive an email with citation details and other helpful information to share your datasets. </p></div></div></div>`);
          if ($props.project) {
            _push2(`<div${_scopeId}>`);
            _push2(ssrRenderComponent(_component_manage_author, {
              ref: "manageAuthorElement",
              project: $props.project
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_manage_citation, {
              ref: "manageCitationElement",
              project: $props.project
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div>`);
        } else {
          _push2(`<div class="mt-24 mx-auto max-w-3xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"${_scopeId}><div class="py-16"${_scopeId}><div class="text-center"${_scopeId}><div class="m-3 relative clear-both border-dotted border-2 border-gray-300 rounded-lg"${_scopeId}><span class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white transition ease-in-out duration-150 cursor-not-allowed" disabled=""${_scopeId}><h1 class="capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl"${_scopeId}>${ssrInterpolate($data.status)}</h1></span></div>`);
          _push2(ssrRenderComponent(_component_Link, {
            type: "button",
            href: _ctx.route("dashboard"),
            class: "inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Go to Dashboard `);
              } else {
                return [
                  createTextVNode(" Go to Dashboard ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div></div><div class="w-full"${_scopeId}><div class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"${_scopeId}><b${_scopeId}>Whats next?</b><div${_scopeId}><p${_scopeId}> Please allow some time to process your submission. You will recieve an email once your submission is processed. Upon publishing you will also receive an email with citation details and other helpful information to share your datasets. </p></div></div></div></div>`);
        }
        _push2(ssrRenderComponent(_component_jet_confirmation_modal, {
          show: $data.showPublishConfirmationModal,
          onClose: ($event) => $data.showPublishConfirmationModal = false
        }, {
          title: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Are you sure you want to publish? `);
            } else {
              return [
                createTextVNode(" Are you sure you want to publish? ")
              ];
            }
          }),
          content: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Once the data is published you will no longer be able to change the data uploaded! If published as a project, you may add more compounds (spectra) to the project later. `);
            } else {
              return [
                createTextVNode(" Once the data is published you will no longer be able to change the data uploaded! If published as a project, you may add more compounds (spectra) to the project later. ")
              ];
            }
          }),
          footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_jet_secondary_button, {
                onClick: ($event) => $data.showPublishConfirmationModal = false
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Cancel `);
                  } else {
                    return [
                      createTextVNode(" Cancel ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_jet_success_button, {
                class: "ml-2",
                onClick: $options.publish
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Publish Now `);
                  } else {
                    return [
                      createTextVNode(" Publish Now ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_jet_secondary_button, {
                  onClick: ($event) => $data.showPublishConfirmationModal = false
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                createVNode(_component_jet_success_button, {
                  class: "ml-2",
                  onClick: $options.publish
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Publish Now ")
                  ]),
                  _: 1
                }, 8, ["onClick"])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "mb-10" }, [
            $data.status == "draft" ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("div", {
                id: "project-details",
                class: "p-4"
              }, [
                createVNode("div", { class: "p-8" }, [
                  createVNode("div", null, [
                    createVNode("label", { class: "block tracking-wider text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700" }, [
                      $data.publishForm.enableProjectMode ? (openBlock(), createBlock("small", { key: 0 }, "PUBLISH AS PROJECT")) : (openBlock(), createBlock("small", { key: 1 }, "PUBLISH SAMPLES"))
                    ]),
                    createVNode(_component_toggle_button, {
                      enabled: $data.publishForm.enableProjectMode,
                      "onUpdate:enabled": ($event) => $data.publishForm.enableProjectMode = $event,
                      onBlur: $options.updateDraft
                    }, null, 8, ["enabled", "onUpdate:enabled", "onBlur"])
                  ]),
                  $data.publishForm.enableProjectMode ? (openBlock(), createBlock("div", { key: 0 }, [
                    createVNode("div", { class: "p-4 bg-gray-100 rounded-md" }, [
                      createVNode("div", {
                        id: "project-name",
                        class: "mb-3"
                      }, [
                        createVNode("label", {
                          for: "project-name",
                          class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, " Project Name "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            "onUpdate:modelValue": ($event) => $data.publishForm.project.name = $event,
                            type: "text",
                            name: "project-name",
                            class: "block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md",
                            onBlur: $options.updateProject
                          }, null, 40, ["onUpdate:modelValue", "onBlur"]), [
                            [vModelText, $data.publishForm.project.name]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.publishForm.errors.name,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", {
                        id: "project-desc",
                        class: "mb-3"
                      }, [
                        createVNode("label", {
                          for: "description",
                          class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, [
                          createVNode("span", {
                            onClick: ($event) => $data.publishForm.project.description = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore"
                          }, "Project Description ", 8, ["onClick"])
                        ]),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("textarea", {
                            id: "description",
                            "onUpdate:modelValue": ($event) => $data.publishForm.project.description = $event,
                            name: "project-description",
                            placeholder: "Describe this project",
                            rows: "3",
                            class: "block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md",
                            onBlur: $options.updateProject
                          }, null, 40, ["onUpdate:modelValue", "onBlur"]), [
                            [
                              vModelText,
                              $data.publishForm.project.description
                            ]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.publishForm.errors.description,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", {
                        id: "project-keywords",
                        class: "mb-3"
                      }, [
                        createVNode("label", {
                          for: "description",
                          class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, " Keywords "),
                        createVNode("div", null, [
                          createVNode(_component_vue_tags_input, {
                            modelValue: $data.publishForm.project.tag,
                            "onUpdate:modelValue": ($event) => $data.publishForm.project.tag = $event,
                            placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                            separators: [";", ","],
                            "max-width": "100%",
                            tags: $data.publishForm.project.tags,
                            onBlur: $options.updateProject,
                            onTagsChanged: (newTags) => $data.publishForm.project.tags = newTags
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "tags", "onBlur", "onTagsChanged"])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.publishForm.errors.tags,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode("div", {
                          id: "project-organism",
                          class: "mb-3"
                        }, [
                          createVNode("label", {
                            for: "description",
                            class: "block text-sm font-medium text-gray-700"
                          }, " Organism (Optional) "),
                          createVNode("div", { class: "mt-2 sm:flex sm:items-start sm:justify-between" }, [
                            createVNode("div", { class: "text-sm text-gray-500 w-full" }, [
                              createVNode("ontology-autocomplete", {
                                class: "rounded-md",
                                format: "text",
                                value: $data.projectSpecies,
                                placeholder: "Search species",
                                onChange: ($event) => $data.projectSpecies = $event.detail[0]
                              }, null, 40, ["value", "onChange"])
                            ]),
                            createVNode("div", { class: "mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center" }, [
                              createVNode("button", {
                                type: "button",
                                class: "inline-flex items-center gap-x-1.5 py-3 bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50",
                                onClick: ($event) => $options.updateSpecies(
                                  $data.projectSpecies
                                )
                              }, [
                                (openBlock(), createBlock("svg", {
                                  class: "-ml-0.5 h-5 w-5 text-gray-400",
                                  viewBox: "0 0 20 20",
                                  fill: "currentColor",
                                  "aria-hidden": "true"
                                }, [
                                  createVNode("path", { d: "M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" })
                                ])),
                                createTextVNode(" Add ")
                              ], 8, ["onClick"])
                            ])
                          ]),
                          createVNode("div", { class: "mt-2" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList($data.publishForm.project.species, (species, $index) => {
                              return openBlock(), createBlock("div", {
                                key: $index,
                                class: "bg-gray-100 border text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"
                              }, [
                                createVNode("ontology-term-annotation", { annotation: species }, null, 8, ["annotation"]),
                                createVNode("span", {
                                  class: "cursor-pointer",
                                  onClick: ($event) => $options.removeSpecies($index)
                                }, [
                                  (openBlock(), createBlock("svg", {
                                    xmlns: "http://www.w3.org/2000/svg",
                                    viewBox: "0 0 24 24",
                                    fill: "currentColor",
                                    class: "w-5 h-5 ml-2"
                                  }, [
                                    createVNode("path", {
                                      "fill-rule": "evenodd",
                                      d: "M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z",
                                      "clip-rule": "evenodd"
                                    })
                                  ]))
                                ], 8, ["onClick"])
                              ]);
                            }), 128))
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mb-2" }, [
                        createVNode("div", { class: "relative pl-2" }, [
                          createVNode("div", {
                            class: "absolute inset-0 flex items-center",
                            "aria-hidden": "true"
                          }, [
                            createVNode("div", { class: "w-full border-t border-gray-300" })
                          ]),
                          createVNode("div", { class: "relative flex items-center justify-between" }, [
                            createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500 after:content-['*'] after:ml-0.5 after:text-red-500" }, " Citation "),
                            createVNode("button", {
                              type: "button",
                              class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                              onClick: $options.toggleManageCitation
                            }, [
                              createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                              createVNode("span", null, "Edit")
                            ], 8, ["onClick"])
                          ])
                        ]),
                        createVNode("dd", { class: "mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto" }, [
                          createVNode("div", { class: "mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2" }, [
                            createVNode("div", { class: "mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2" }, [
                              createVNode(_component_citation_card, {
                                citations: $props.project.citations
                              }, null, 8, ["citations"])
                            ])
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mb-2" }, [
                        createVNode("div", { class: "relative pl-2" }, [
                          createVNode("div", {
                            class: "absolute inset-0 flex items-center",
                            "aria-hidden": "true"
                          }, [
                            createVNode("div", { class: "w-full border-t border-gray-300" })
                          ]),
                          createVNode("div", { class: "relative flex items-center justify-between" }, [
                            createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500 after:content-['*'] after:ml-0.5 after:text-red-500" }, " Author "),
                            createVNode("button", {
                              type: "button",
                              class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                              onClick: $options.toggleManageAuthor
                            }, [
                              createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                              createVNode("span", null, "Edit")
                            ], 8, ["onClick"])
                          ])
                        ]),
                        createVNode("dd", { class: "mt-2 text-md text-gray-900 space-y-5" }, [
                          createVNode("div", { class: "mt-1 grid grid-cols-1 gap-4 sm:grid-cols-3" }, [
                            createVNode(_component_author_card, {
                              authors: $props.project.authors
                            }, null, 8, ["authors"])
                          ])
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  createVNode("div", { class: "mt-3" }, [
                    createVNode("label", { class: "block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700" }, toDisplayString(_ctx.pluralize("SAMPLE", $props.project.studies.length)) + " (" + toDisplayString($props.project.studies.length) + ") ", 1),
                    createVNode("div", null, [
                      createVNode("div", { class: "mt-4 mb-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList($props.project.studies, (study) => {
                          return openBlock(), createBlock("div", {
                            key: study.uuid
                          }, [
                            createVNode(_component_study_info, { study }, null, 8, ["study"])
                          ]);
                        }), 128))
                      ])
                    ])
                  ]),
                  createVNode("div", {
                    id: "release",
                    class: "mt-3"
                  }, [
                    createVNode("label", { class: "block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700" }, " Release Date "),
                    createVNode(_component_Datepicker, {
                      modelValue: $data.publishForm.releaseDate,
                      "onUpdate:modelValue": [($event) => $data.publishForm.releaseDate = $event, $options.updateProject]
                    }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                    createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Publish your data now or choose a release date to auto publish your project to public. ")
                  ]),
                  createVNode("div", { class: "mt-5" }, [
                    createVNode("div", {
                      id: "license",
                      class: "mb-4"
                    }, [
                      createVNode("div", null, [
                        createVNode("div", { class: "mt-6 grid grid-cols-1 gap-x-4 sm:grid-cols-1" }, [
                          $data.licenses ? (openBlock(), createBlock("div", { key: 0 }, [
                            createVNode("span", { class: "float-right text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                              createVNode("a", {
                                target: "_blank",
                                href: "https://docs.nmrxiv.org/submission-guides/licenses"
                              }, "How to choose the right license?")
                            ]),
                            createVNode(_component_select_rich, {
                              selected: $data.license,
                              "onUpdate:selected": [($event) => $data.license = $event, $options.updateProject],
                              label: "License",
                              items: $data.licenses
                            }, null, 8, ["selected", "onUpdate:selected", "items"])
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "mt-5" }, [
                    createVNode("h3", { class: "text-lg font-bold text-gray-400" }, " Terms & Conditions "),
                    createVNode("div", { class: "mt-3" }, [
                      createVNode("div", { class: "ml-2" }, [
                        createVNode("div", { class: "flex items-top" }, [
                          withDirectives(createVNode("input", {
                            id: "conditions",
                            "onUpdate:modelValue": ($event) => $data.publishForm.conditions = $event,
                            type: "checkbox",
                            class: "rounded mt-1 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50",
                            name: "conditions"
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelCheckbox, $data.publishForm.conditions]
                          ]),
                          createVNode("div", { class: "ml-2 text-sm" }, [
                            $data.publishForm.enableProjectMode ? (openBlock(), createBlock("span", { key: 0 }, " I understand once the project is published, all the underlying samples and spectra will also be made public and agree to make this data persistently available in the nmrXiv platform. ")) : (openBlock(), createBlock("span", { key: 1 }, " I understand once the samples are published, all the underlying spectra will also be made public and agree to make this data persistently available in the nmrXiv platform. "))
                          ])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "mt-2" }, [
                      createVNode("div", { class: "ml-2" }, [
                        createVNode("div", { class: "flex items-center" }, [
                          withDirectives(createVNode("input", {
                            id: "terms",
                            "onUpdate:modelValue": ($event) => $data.publishForm.terms = $event,
                            type: "checkbox",
                            class: "rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50",
                            name: "terms"
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelCheckbox, $data.publishForm.terms]
                          ]),
                          createVNode("div", { class: "ml-2 text-sm" }, [
                            createTextVNode(" I agree to the "),
                            createVNode("a", {
                              target: "_blank",
                              href: _ctx.route("terms.show"),
                              class: "underline text-sm text-gray-600 hover:text-gray-900"
                            }, "Terms of Service", 8, ["href"]),
                            createTextVNode(" and "),
                            createVNode("a", {
                              target: "_blank",
                              href: _ctx.route("policy.show"),
                              class: "underline text-sm text-gray-600 hover:text-gray-900"
                            }, "Privacy Policy", 8, ["href"]),
                            createTextVNode(" and hereby also grant nmrXiv permissions to distribute the datasets (and meta-data) under the specified license. ")
                          ])
                        ])
                      ])
                    ])
                  ])
                ]),
                createVNode("div", null, [
                  createVNode("div", { id: "publish-details" }, " "),
                  createVNode("div", { class: "px-8 pb-8 pt-0" }, [
                    createVNode("button", {
                      type: "button",
                      class: [
                        !$data.publishForm.terms || !$data.publishForm.conditions ? "bg-gray-200 cursor-not-allowed" : "bg-green-600 hover:bg-green-700",
                        "inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto sm:text-sm"
                      ],
                      disabled: !$data.publishForm.terms && !$data.publishForm.conditions,
                      onClick: ($event) => $data.showPublishConfirmationModal = true
                    }, " Publish ", 10, ["disabled", "onClick"]),
                    createVNode(_component_Link, {
                      type: "button",
                      class: "mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm",
                      href: _ctx.route("dashboard")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" Not right yet ")
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  $data.errors ? (openBlock(), createBlock("div", { key: 0 }, [
                    createVNode("div", { class: "rounded-md bg-red-50 p-4 mx-4 mb-4" }, [
                      createVNode("div", null, [
                        createVNode("div", { class: "flex" }, [
                          createVNode("div", { class: "flex-shrink-0" }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-5 w-5 text-red-400",
                              xmlns: "http://www.w3.org/2000/svg",
                              viewBox: "0 0 20 20",
                              fill: "currentColor",
                              "aria-hidden": "true"
                            }, [
                              createVNode("path", {
                                "fill-rule": "evenodd",
                                d: "M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z",
                                "clip-rule": "evenodd"
                              })
                            ]))
                          ]),
                          createVNode("div", { class: "ml-3" }, [
                            createVNode("h3", { class: "text-sm font-medium text-red-800" }, " Error publishing your project "),
                            createVNode("div", { class: "mt-2 text-sm text-red-700" }, [
                              createVNode("ul", {
                                role: "list",
                                class: "list-disc space-y-1 pl-5"
                              }, [
                                createVNode("li", null, toDisplayString($data.errors), 1)
                              ])
                            ])
                          ])
                        ]),
                        $data.validation ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "mt-6"
                        }, [
                          createVNode(_component_Validation, {
                            project: $props.project,
                            validation: $data.validation,
                            draft: $props.draft.id
                          }, null, 8, ["project", "validation", "draft"])
                        ])) : createCommentVNode("", true)
                      ])
                    ])
                  ])) : createCommentVNode("", true)
                ]),
                createVNode("div", { class: "mx-4" }, [
                  createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, [
                    createVNode("b", null, "Whats next?"),
                    createVNode("div", null, [
                      createVNode("p", null, " Upon clicking publish, your project is submitted to our queue system for automatic processing. Once successfully processed, your data is assigned with stable identifiers, and DOIs are generated. You will receive an email with citation details and other helpful information to share your datasets. ")
                    ])
                  ])
                ]),
                $props.project ? (openBlock(), createBlock("div", { key: 0 }, [
                  createVNode(_component_manage_author, {
                    ref: "manageAuthorElement",
                    project: $props.project
                  }, null, 8, ["project"]),
                  createVNode(_component_manage_citation, {
                    ref: "manageCitationElement",
                    project: $props.project
                  }, null, 8, ["project"])
                ])) : createCommentVNode("", true)
              ])
            ])) : (openBlock(), createBlock("div", {
              key: 1,
              class: "mt-24 mx-auto max-w-3xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
            }, [
              createVNode("div", { class: "py-16" }, [
                createVNode("div", { class: "text-center" }, [
                  createVNode("div", { class: "m-3 relative clear-both border-dotted border-2 border-gray-300 rounded-lg" }, [
                    createVNode("span", {
                      class: "inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm rounded-md text-sky-500 bg-white transition ease-in-out duration-150 cursor-not-allowed",
                      disabled: ""
                    }, [
                      createVNode("h1", { class: "capitalize text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl" }, toDisplayString($data.status), 1)
                    ])
                  ]),
                  createVNode(_component_Link, {
                    type: "button",
                    href: _ctx.route("dashboard"),
                    class: "inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Go to Dashboard ")
                    ]),
                    _: 1
                  }, 8, ["href"])
                ])
              ]),
              createVNode("div", { class: "w-full" }, [
                createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, [
                  createVNode("b", null, "Whats next?"),
                  createVNode("div", null, [
                    createVNode("p", null, " Please allow some time to process your submission. You will recieve an email once your submission is processed. Upon publishing you will also receive an email with citation details and other helpful information to share your datasets. ")
                  ])
                ])
              ])
            ])),
            createVNode(_component_jet_confirmation_modal, {
              show: $data.showPublishConfirmationModal,
              onClose: ($event) => $data.showPublishConfirmationModal = false
            }, {
              title: withCtx(() => [
                createTextVNode(" Are you sure you want to publish? ")
              ]),
              content: withCtx(() => [
                createTextVNode(" Once the data is published you will no longer be able to change the data uploaded! If published as a project, you may add more compounds (spectra) to the project later. ")
              ]),
              footer: withCtx(() => [
                createVNode(_component_jet_secondary_button, {
                  onClick: ($event) => $data.showPublishConfirmationModal = false
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                createVNode(_component_jet_success_button, {
                  class: "ml-2",
                  onClick: $options.publish
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Publish Now ")
                  ]),
                  _: 1
                }, 8, ["onClick"])
              ]),
              _: 1
            }, 8, ["show", "onClose"])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Publish.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Publish = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Publish as default
};
