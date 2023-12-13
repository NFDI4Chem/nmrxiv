import { A as AppLayout, F as FileSystemBrowser, V as Validation, S as SpectraEditor } from "./AppLayout-b93d1c11.mjs";
import { Link, router } from "@inertiajs/vue3";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { mergeProps, useSSRContext, resolveComponent, ref, withCtx, createTextVNode, openBlock, createBlock, createVNode, createCommentVNode, toDisplayString, Fragment, renderList, withDirectives, vModelText } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderStyle, ssrRenderList, ssrInterpolate, ssrRenderClass, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { TrashIcon, PencilIcon, ArrowDownOnSquareStackIcon, EyeIcon, EyeSlashIcon } from "@heroicons/vue/24/solid";
import { D as Depictor2D } from "./Depictor2D-b60253ae.mjs";
import OCL$1 from "openchemlib/full.js";
import slider from "vue3-slider";
import VueTagsInput from "@sipec/vue3-tags-input";
import "ontology-elements/dist/index.js";
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
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
const _sfc_main$3 = {};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs) {
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "bg-white mt-4 border-t" }, _attrs))}><div class="relative mx-auto px-12 py-8"><div class="relative"><div><div class="mx-auto text-left font-medium text-gray-900"><p class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"> Basic Concepts </p><p class="mt-3"> Submission to nmrXiv is divided into 3 steps <br> 1) File upload <br> 2) Auto - Processing, Assignments and Validation <br> 3) Publish <br><br> In nmrXiv, data is organised as <b class="text-teal-700">projects</b>. Consider a project is equivalent to your publication with the corresponding NMR data to be uploaded to nmrXiv. A project can have multiple samples. <b class="text-teal-700">A sample study corresponds to a group of NMR experiments of one sample</b>, e.g. 1H, 13C, APT, COSY HSQC, HMBC, NOESY in Bruker Format or (another study) in another format such as JCAMP-DX / Varian. <b class="text-teal-700">Each NMR measurement in a sample study is referred to as a dataset</b>, e.g. 1H NMR or COSY (are each a dataset). You can also publish samples on their own with out creating projects. </p><div class="w-full"><img src="/img/primer.png" class="h-[calc(40vh)] mx-auto"></div><p class="text-md"> nmrXiv allows you to upload NMR raw data from any NMR instrument. We can currently auto-detect Bruker/Varian/JOEL formats &amp; JCAMP files and will support more raw &amp; processed file formats soon. Once you upload your raw or processed NMR data, nmrXiv will auto-generate the samples and datasets for you based on the uploaded folder structure. <i><a class="text-md text-teal-700" target="_blank" href="https://docs.nmrxiv.org/submission-guides/submission-process.html">Please read our documentation for more detailed documentation about submission process.</a></i></p></div></div></div></div></section>`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Primer.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const Primer = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender$3]]);
const _sfc_main$2 = {
  components: {},
  props: {
    molecule: String,
    width: {
      type: Number,
      default: 300
    },
    height: {
      type: Number,
      default: 300
    },
    showDownload: {
      type: Boolean,
      default: false
    },
    identifier: String
  },
  data() {
    return {
      results: []
    };
  },
  computed: {
    encodedSmiles() {
      return encodeURIComponent(this.molecule);
    }
  },
  mounted() {
  },
  methods: {}
};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "mx-auto w-100" }, _attrs))}><iframe${ssrRenderAttr("width", $props.width - 60)}${ssrRenderAttr("height", $props.height - 65)}${ssrRenderAttr("src", _ctx.$page.props.CM_API + "depict/3D?smiles=" + $options.encodedSmiles)} frameborder="0"></iframe>`);
  if ($props.showDownload) {
    _push(`<div class="cursor-pointer mt-1 text-sm text-gray-900 float-right"><a style="${ssrRenderStyle({ "box-shadow": "none" })}" class="hover:text-gray-600">Download Molfile(3D)</a></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Depictor3D.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const Depictor3D = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$1 = {
  components: {
    Depictor2D,
    Depictor3D
  },
  props: {
    modelValue: String,
    width: {
      type: Number,
      default: 300
    },
    height: {
      type: Number,
      default: 300
    },
    readonly: {
      type: Boolean,
      default: true
    },
    CIP: {
      type: Boolean,
      default: false
    },
    showDownload: {
      type: Boolean,
      default: false
    },
    identifier: String
  },
  data() {
    return {
      selectedTab: "2D",
      depictionSelection: "2D",
      tabs: ["2D", "3D"],
      edit: false,
      editor: null
    };
  },
  methods: {
    loadStructureEditor(id) {
      this.edit = true;
      this.$nextTick(() => {
        this.editor = OCL$1.StructureEditor.createSVGEditor(id, 1);
        if (this.modelValue) {
          this.editor.setSmiles(this.modelValue);
        }
      });
    },
    save() {
      this.$emit("update:modelValue", this.editor.getSmiles());
      this.edit = false;
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Depictor2D = resolveComponent("Depictor2D");
  const _component_Depictor3D = resolveComponent("Depictor3D");
  if (!$data.edit) {
    _push(`<div${ssrRenderAttrs(_attrs)}><div><div class="sm:hidden"><label for="tabs" class="sr-only">Select a tab</label><select id="tabs" name="tabs" class="block w-full rounded-md border-gray-300 focus:border-secondary-dark focus:ring-secondary-dark"><!--[-->`);
    ssrRenderList($data.tabs, (tab) => {
      _push(`<option${ssrRenderAttr("value", tab)}>${ssrInterpolate(tab)}</option>`);
    });
    _push(`<!--]--></select></div><div class="hidden sm:block"><nav class="flex space-x-2 pl-3" aria-label="Tabs"><!--[-->`);
    ssrRenderList($data.tabs, (tab) => {
      _push(`<div class="${ssrRenderClass([
        $data.selectedTab == tab ? "bg-gray-500 text-white" : "",
        "border hover:cursor-pointer text-gray-500 hover:text-gray-700 rounded-md px-3 py-2 text-sm font-medium"
      ])}">${ssrInterpolate(tab)}</div>`);
    });
    _push(`<!--]-->`);
    if (!$props.readonly) {
      _push(`<div class="hover:cursor-pointer text-gray-500 hover:text-gray-700 rounded-md px-3 py-2 text-sm font-medium"> edit </div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</nav></div></div>`);
    if ($data.selectedTab == "2D") {
      _push(`<div>`);
      _push(ssrRenderComponent(_component_Depictor2D, {
        "c-i-p": $props.CIP,
        molecule: $props.modelValue,
        "show-download": $props.showDownload,
        identifier: $props.identifier,
        height: $props.height,
        width: $props.width
      }, null, _parent));
      _push(`</div>`);
    } else {
      _push(`<!---->`);
    }
    if ($data.selectedTab == "3D") {
      _push(`<div>`);
      _push(ssrRenderComponent(_component_Depictor3D, {
        molecule: $props.modelValue,
        "show-download": $props.showDownload,
        identifier: $props.identifier,
        height: $props.height,
        width: $props.width
      }, null, _parent));
      _push(`</div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<div${ssrRenderAttrs(mergeProps({ class: "mb-2" }, _attrs))}><div id="structureEditor" class="w-full border rounded-md" style="${ssrRenderStyle({ "height": "400px" })}"></div><br><a class="border hover:cursor-pointer text-gray-500 hover:text-gray-700 rounded-md px-3 py-2 text-sm font-medium">SAVE</a></div>`);
  }
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Depictor.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Depictor = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    AppLayout,
    Link,
    JetInputError,
    JetSecondaryButton,
    JetButton,
    Primer,
    FileSystemBrowser,
    TrashIcon,
    PencilIcon,
    ArrowDownOnSquareStackIcon,
    EyeIcon,
    EyeSlashIcon,
    Validation,
    SpectraEditor,
    Depictor,
    Depictor2D,
    slider,
    VueTagsInput
  },
  props: ["draft_id"],
  setup() {
    return {};
  },
  data() {
    return {
      returnUrl: "/dashboard",
      loading: false,
      loadingStep: false,
      spectraLoadingStatus: false,
      spectraLoadingMessage: null,
      showCompoundDetails: true,
      currentDraft: null,
      drafts: [],
      draftForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        tags: [],
        tag: "",
        tags_array: [],
        owner_id: null
      }),
      project: null,
      studies: null,
      studiesToImport: [],
      studySpecies: null,
      inprogressStudies: [],
      selectedStudy: null,
      studyForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        tags: [],
        species: [],
        tag: "",
        tags_array: []
      }),
      displaySamplesSummaryInfo: true,
      querySample: null,
      validation: null,
      validationStatus: true,
      smiles: "",
      percentage: 100,
      editor: null,
      showPrimer: false,
      busy: false,
      steps: [
        {
          id: "1",
          step: "tour-step-submission-header",
          name: "Files Upload",
          description: "Vitae sed mi luctus laoreet.",
          href: "#",
          status: "upcoming"
        },
        {
          id: "2",
          step: "v-step-20",
          name: "Assignments & Metadata",
          description: "Cursus semper viverra.",
          href: "#",
          status: "upcoming"
        },
        {
          id: "3",
          name: "Complete ~ Community Standards",
          description: "Penatibus eu quis ante.",
          href: "#",
          status: "upcoming"
        }
      ],
      step: 1,
      errorMessage: null,
      filesErrorMessage: null,
      createDatasetForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        team_id: null,
        owner_id: null,
        color: null,
        starred: null,
        project_id: this.project ? this.project.id : null,
        is_public: ref(false)
      })
    };
  },
  computed: {
    currentStep() {
      return this.steps.filter((s) => s.status == "current")[0];
    },
    primed() {
      return this.$page.props.auth.user.primed;
    },
    importPendingSamples() {
      return this.studiesToImport.filter((f) => f.status == false);
    },
    spectraCount() {
      let i = 0;
      this.studies.forEach((s) => {
        i = i + s.datasets.length;
      });
      return i;
    },
    moleculesCount() {
      let i = 0;
      this.studies.forEach((s) => {
        i = i + s.sample.molecules.length;
      });
      return i;
    },
    getMax() {
      if (this.selectedStudy) {
        let totalCount = 0;
        this.selectedStudy.sample.molecules.forEach((mol) => {
          totalCount += parseInt(
            mol.pivot.percentage_composition ? mol.pivot.percentage_composition : 0
          );
        });
        return 100 - totalCount;
      } else {
        return 100;
      }
    }
  },
  mounted() {
    const urlSearchParams = new URLSearchParams(window.location.search);
    const params = Object.fromEntries(urlSearchParams.entries());
    this.step = params["step"] ? params["step"] : "1";
    this.querySample = params["sample"] ? params["sample"] : null;
    this.fetchDrafts().then((response) => {
      this.drafts = response.data.drafts;
      this.sharedDrafts = response.data.sharedDrafts;
      if (this.draft_id) {
        if (this.draft_id != "null") {
          let selectedDraft = this.drafts.find(
            (d) => d.id == this.draft_id
          );
          if (!selectedDraft) {
            selectedDraft = this.sharedDrafts.find(
              (d) => d.id == this.draft_id
            );
          }
          if (selectedDraft) {
            this.selectDraft(selectedDraft);
          } else {
            if (response.data.default.id == this.draft_id) {
              this.selectDraft(response.data.default);
            }
          }
          this.loading = false;
        } else {
          alert(
            "Could not find the draft. Redirecting to the upload page."
          );
          return router.visit("upload");
        }
      } else {
        this.defaultDraft = response.data.default;
        if (this.drafts.length == 0) {
          this.selectDraft(this.defaultDraft);
          this.loading = false;
        } else {
          this.loading = false;
        }
      }
    });
    this.showPrimer = !this.primed;
    let localItems = this.findLocalItems("show_compound_details");
    if (localItems.length > 0) {
      this.showCompoundDetails = JSON.parse(localItems[0].val);
    }
  },
  methods: {
    toggleCompoundDetails() {
      this.showCompoundDetails = !this.showCompoundDetails;
      localStorage.setItem(
        "show_compound_details",
        this.showCompoundDetails
      );
    },
    showSamplesSummary() {
      this.spectraLoadingStatus = false;
      this.displaySamplesSummaryInfo = true;
      this.selectedStudy = null;
      this.setQueryStringParameter("sample", null);
    },
    fetchDrafts() {
      this.loading = true;
      return axios.get("/dashboard/drafts");
    },
    selectDraft(draft) {
      this.currentDraft = draft;
      this.draftForm.name = this.currentDraft.name;
      this.draftForm.description = this.currentDraft.description;
      let tags = [];
      this.file = null;
      this.draftForm.tags = [];
      this.draftForm.owner_id = this.$page.props.auth.user.id;
      if (this.currentDraft.tags) {
        this.currentDraft.tags.forEach((t) => {
          tags.push({
            text: t.name["en"]
          });
        });
        this.draftForm.tags = tags;
      }
      this.$nextTick(() => {
        if (this.step == "2") {
          this.showPrimer = false;
          this.loadSamplesSummary();
        } else {
          this.selectStep(1);
        }
      });
    },
    createNewDraft() {
      if (!this.primed) {
        this.showPrimer = true;
        this.newDraft();
      } else {
        this.newDraft();
      }
    },
    newDraft() {
      if (this.defaultDraft) {
        this.defaultDraft.name = "Untitled Project (Draft: " + this.defaultDraft.key.split("-")[0] + ")";
        this.selectDraft(this.defaultDraft);
      } else {
        this.fetchDrafts().then((response) => {
          this.defaultDraft = response.data.default;
          this.loading = false;
          this.defaultDraft.name = "Untitled Project (Draft: " + this.defaultDraft.key.split("-")[0] + ")";
          this.selectDraft(this.defaultDraft);
        });
      }
    },
    updateDraft(e) {
      this.currentDraft.name = e.target.innerText;
      this.draftForm.errors = [];
      axios.put(
        "/dashboard/drafts/" + this.currentDraft.id,
        this.currentDraft
      ).then((response) => {
        this.currentDraft = response.data;
        this.draftForm.name = this.currentDraft.name;
      });
    },
    closeDraft() {
      this.fetchValidations().then(() => {
        if (this.validationStatus) {
          this.loadingStep = true;
          axios.post(
            "/dashboard/drafts/" + this.currentDraft.id + "/complete",
            {}
          ).catch(() => {
            this.loadingStep = false;
          }).then((response) => {
            this.project = response.data.project;
            this.validation = this.parseJSON(
              response.data.validation.report
            );
            if (this.project) {
              window.location = "/publish/" + this.currentDraft.id;
            }
          });
        } else {
          this.showSamplesSummary();
          alert(
            "Samples validation failed: Please provide all meta data to proceed"
          );
        }
      });
    },
    selectStep(id) {
      this.steps.forEach((step) => {
        if (parseInt(step.id) < id) {
          step.status = "complete";
        } else if (parseInt(step.id) == id) {
          step.status = "current";
        } else {
          step.status = "upcoming";
        }
      });
      if (id == 1) {
        this.$nextTick(function() {
          this.setQueryStringParameter(
            "draft_id",
            this.currentDraft.id
          );
          this.setQueryStringParameter("step", 1);
          this.step = "1";
          if (this.$refs.fsbRef) {
            this.$refs.fsbRef.annotate();
          }
        });
      } else if (id == 2) {
        this.$nextTick(function() {
          this.setQueryStringParameter("step", 2);
          this.step = "2";
          if (this.querySample) {
            let i = 0;
            this.studies.forEach((s) => {
              if (s.id == this.querySample) {
                this.selectStudy(s, i);
              }
              i = i + 1;
            });
          }
          this.fetchValidations();
          this.updateAutoImportList();
        });
      }
    },
    setQueryStringParameter(name, value) {
      const params = new URLSearchParams(window.location.search);
      if (value) {
        params.set(name, value);
      } else {
        params.delete(name);
      }
      window.history.replaceState(
        {},
        "",
        decodeURIComponent(`${window.location.pathname}?${params}`)
      );
    },
    hidePrimer() {
      axios.post("/primer/skip").then(() => {
        router.reload({
          only: ["user", "user.permissions", "user.roles"]
        });
      });
    },
    skipPrimer() {
      this.showPrimer = false;
      this.selectStep(1);
    },
    filesLoading(e) {
      this.loadingStep = e;
    },
    updateLoadingStatus(status) {
      this.loading = status;
    },
    process() {
      this.errorMessage = null;
      let foldersExist = false;
      this.$refs.fsbRef.file.children.forEach((fso) => {
        if (fso.has_children) {
          foldersExist = true;
        }
      });
      if (foldersExist) {
        this.hasStudies(this.$refs.fsbRef.file);
        this.loadSamplesSummary();
      }
      if (this.$refs.fsbRef.file && this.$refs.fsbRef.file.children.length > 0 && foldersExist && this.studiesExist) {
        this.loadingStep = true;
        this.draftForm.owner_id = this.$page.props.auth.user.id;
        this.draftForm.tags_array = this.draftForm.tags.map(
          (a) => a.text
        );
      } else {
        if (this.$refs.fsbRef.file.children.length > 0 && !foldersExist) {
          this.filesErrorMessage = "Spectra files needs to be organised into folders. Please create a folder corresponding to each sample and add all your NMR spectroscopic experiment output files are added to the corresponding folders";
        } else if (this.$refs.fsbRef.file.children.length <= 0) {
          this.filesErrorMessage = "Please upload spectral data to proceed.";
        } else if (!this.studiesExist) {
          this.filesErrorMessage = "Please upload spectral data to proceed.";
        } else {
          this.filesErrorMessage = "Please make sure you fill in all the required data before you proceed";
        }
      }
    },
    loadSamplesSummary() {
      this.fetchProjectDetails().then(
        (response) => {
          this.loadingStep = false;
          this.project = response.data.project;
          this.studies = response.data.studies;
          if (this.project && this.studies && this.studies.length > 0) {
            this.loadingStep = false;
            this.selectStep(2);
            this.inprogressStudies = this.studies.filter(
              (study) => study.internal_status != "complete"
            );
            if (this.inprogressStudies.length > 0) {
              this.checkStudyStatus();
            }
          } else {
            if (this.studies.length == 0) {
              this.loadingStep = false;
            }
          }
        },
        (error) => {
          this.loadingStep = false;
          Object.keys(error.response.data.errors).forEach((key) => {
            error.response.data.errors[key] = error.response.data.errors[key].join(", ");
          });
          this.draftForm.errors = error.response.data.errors;
          this.draftForm.error_message = error.response.data.message;
          this.draftForm.hasErrors = true;
          Object.keys(this.draftForm.errors).forEach((key) => {
            if (!this.errorMessage) {
              this.errorMessage = "<b class='capitalize'>" + key + "</b>: " + this.draftForm.errors[key] + "</br>";
            } else {
              this.errorMessage += "<b class='capitalize'>" + key + "</b>: " + this.draftForm.errors[key] + "</br>";
            }
          });
        }
      );
    },
    checkStudyStatus() {
      setTimeout(() => {
        this.fetchProjectDetails().then((response) => {
          this.loadingStep = false;
          this.project = response.data.project;
          this.studies = response.data.studies;
          this.fetchValidations();
          this.spectraLoadingStatus = false;
          this.inprogressStudies = this.studies.filter(
            (study) => study.internal_status != "complete"
          );
          if (this.inprogressStudies.length > 0) {
            this.checkStudyStatus();
          } else {
            this.autoImport();
          }
        });
      }, 3e4);
    },
    editData(e) {
      let i = 0;
      this.studies.forEach((s) => {
        if (s.name == e.model.name) {
          this.selectStudy(s, i);
        }
        i = i + 1;
      });
    },
    hasStudies(file) {
      if (file.model_type == "study") {
        this.studiesExist = true;
      }
      if (file.has_children && file.children) {
        file.children.forEach((fso) => {
          this.hasStudies(fso);
        });
      }
    },
    selectStudy(study, index, datasetIndex = null) {
      if (!this.busy) {
        if (study.internal_status == "complete") {
          this.selectedStudyIndex = index;
          this.selectedStudy = study;
          this.setQueryStringParameter("sample", study.id);
          this.studyForm.name = this.selectedStudy.name;
          this.studyForm.description = this.selectedStudy.description.replace(/<\/br>/g, " ");
          this.studyForm.species = JSON.parse(
            this.selectedStudy.species
          ) ? JSON.parse(this.selectedStudy.species) : [];
          let tags = [];
          this.selectedStudy.tags.forEach((t) => {
            tags.push({
              text: t.name["en"]
            });
          });
          this.studyForm.tags = tags;
          let editorState = JSON.parse(
            JSON.stringify(this.displaySamplesSummaryInfo)
          );
          if (this.displaySamplesSummaryInfo) {
            this.displaySamplesSummaryInfo = false;
          }
          this.$nextTick(() => {
            if (editorState) {
              if (this.$refs.spectraEditorREF) {
                this.$refs.spectraEditorREF.registerEvents();
              }
            }
            this.editor = OCL.StructureEditor.createSVGEditor(
              "structureSearchEditor",
              1
            );
          });
          if (this.studyForm.description == "") {
            if (study.has_nmrium) {
              this.autoGenerateDescription();
            }
          }
          if (this.selectedStudy && this.selectedStudy.sample.molecules.length == 0) {
            this.autoImportMolecularData(this.selectedStudy);
          }
          if (!datasetIndex) {
            this.selectedDSIndex = 0;
          } else {
            this.selectedDSIndex = datasetIndex;
          }
        }
      }
    },
    fetchProjectDetails() {
      return axios.post(
        "/dashboard/drafts/" + this.currentDraft.id + "/process",
        this.draftForm
      );
    },
    fetchValidations() {
      return axios.get("/projects/" + this.project.id + "/validation").then((response) => {
        this.validation = response.data.report;
        this.validationStatus = true;
        this.validation.project.studies.forEach((study) => {
          if (study.status == false) {
            this.validationStatus = false;
          }
        });
      });
    },
    updateAutoImportList() {
      this.studiesToImport = [];
      this.studies.forEach((study) => {
        if (!study.has_nmrium) {
          this.studiesToImport.push({
            projectSlug: this.project.slug,
            study,
            status: false
          });
        }
      });
    },
    spectraLoading(e) {
      this.spectraLoadingStatus = e.status;
      this.spectraLoadingMessage = e.message;
    },
    updateTags(e) {
      this.busy = true;
      if (!e.type) {
        this.studyForm.tags = e;
      }
      this.studyForm.tags_array = this.studyForm.tags.map((a) => a.text);
      this.saveStudyDetails();
    },
    saveStudyDetails() {
      this.busy = true;
      this.spectraLoading({
        status: true,
        message: "Saving updates"
      });
      this.loadingStep = true;
      axios.put(
        "/dashboard/studies/" + this.selectedStudy.id + "/update",
        this.studyForm
      ).catch((error) => {
        this.loadingStep = false;
        this.spectraLoading({
          status: false,
          message: "Update save error"
        });
        this.busy = false;
        Object.keys(error.response.data.errors).forEach((key) => {
          error.response.data.errors[key] = error.response.data.errors[key].join(", ");
        });
        this.studyForm.errors = error.response.data.errors;
        this.studyForm.error_message = error.response.data.message;
        this.studyForm.hasErrors = true;
      }).then((response) => {
        if (response) {
          this.busy = false;
          this.spectraLoading({
            status: false,
            message: "Updates saved"
          });
          this.loadingStep = false;
          this.studies[this.selectedStudyIndex] = response.data;
          this.studyForm.hasErrors = false;
        }
      });
    },
    deleteMolecule(mol) {
      axios.delete(
        "/dashboard/studies/" + this.selectedStudy.id + "/molecule/" + mol.id
      ).then((res) => {
        this.selectedStudy.sample.molecules = res.data;
        this.smiles = "";
        this.percentage = 0;
        this.editor.setSmiles("");
      });
    },
    editMolecule(mol) {
      this.editor.setSmiles(mol.canonical_smiles);
      this.percentage = parseInt(mol.pivot.percentage_composition);
      axios.delete(
        "/dashboard/studies/" + this.selectedStudy.id + "/molecule/" + mol.id
      ).then((res) => {
        this.selectedStudy.sample.molecules = res.data;
      });
    },
    saveMolecule(mol, study) {
      if (!study) {
        study = this.selectedStudy;
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
    associateMoleculeToStudy(mol, study) {
      axios.post("/dashboard/studies/" + study.id + "/molecule", {
        InChI: mol.inchi,
        InChIKey: mol.inchikey,
        percentage: this.percentage,
        mol: mol.standardized_mol,
        canonical_smiles: mol.canonical_smiles
      }).then((res) => {
        study.sample.molecules = res.data;
        this.smiles = "";
        this.percentage = this.getMax;
        this.editor.setSmiles("");
      });
    },
    standardizeMolecules(mol) {
      return axios.post(
        "https://api.naturalproducts.net/latest/chem/standardize",
        mol
      );
    },
    autoImportMolecularData(study) {
      axios.get("/dashboard/studies/" + study.id + "/annotations").then((response) => {
        let nmredataFiles = response.data;
        nmredataFiles.forEach((file) => {
          let username = this.$page.props.team && this.$page.props.team.owner ? this.$page.props.team.owner.username : this.project.owner.username;
          let url = this.url + "/" + username + "/studies/" + study.id + "/file/" + file.slug;
          axios.get(url).then((response2) => {
            axios.post(
              "https://dev.api.naturalproducts.net/latest/chem/standardize",
              response2.data
            ).then((res) => {
              let standard_inchi = res.data.inchi;
              let molExists = false;
              study.sample.molecules.forEach((mol) => {
                if (mol.standard_inchi == standard_inchi) {
                  molExists = true;
                }
              });
              if (!molExists) {
                this.saveMolecule(res.data, study);
              }
            });
          });
        });
      });
    },
    autoGenerateDescription() {
      let desc = "This dataset contains NMR spectra obtained for the sample -" + this.selectedStudy.name + "</br>";
      axios.get(
        "/dashboard/studies/" + this.selectedStudy.id + "/nmriumInfo"
      ).then((response) => {
        let nmrium_info = response.data;
        if (nmrium_info) {
          nmrium_info.data.spectra.forEach((spectra) => {
            Object.keys(spectra.info).forEach((key) => {
              desc = desc + key + ": " + spectra.info[key] + "</br>";
            });
          });
          this.studyForm.description = desc.replace(
            /<\/br>/g,
            " "
          );
          this.saveStudyDetails();
        }
      });
    },
    autoImport() {
      this.spectraLoadingStatus = true;
      this.updateAutoImportList();
      if (this.studiesToImport.length > 0) {
        this.fetchNMRium();
      } else {
        console.log(
          "Nothing to import: NMRium spectra JSON already exists"
        );
        this.spectraLoadingStatus = false;
      }
    },
    fetchNMRium() {
      let studyDetails = this.importPendingSamples.length > 0 ? this.importPendingSamples[0] : null;
      if (studyDetails) {
        this.spectraLoadingStatus = true;
        let url = null;
        if (studyDetails.study.download_url) {
          url = studyDetails.study.download_url;
        } else {
          let username = this.$page.props.team.owner ? this.$page.props.team.owner.username : this.project.owner.username;
          url = this.url + "/" + username + "/datasets/" + this.project.slug + "/" + studyDetails.study.slug;
        }
        this.spectraLoadingMessage = "<br/> <small><i>Pending: " + (this.studies.length - this.importPendingSamples.length) + "/" + this.studies.length + "</i></small> <br/> Processing Spectra from Sample: " + studyDetails.study.slug + " <br/> Spectra URL: " + url;
        axios.post("https://nodejsdev.nmrxiv.org/spectra-parser", {
          urls: [url],
          snapshot: false
        }).then((response) => {
          let parsedSpectra = response.data.data;
          parsedSpectra.spectra.forEach((spec) => {
            delete spec["data"];
            delete spec["meta"];
            delete spec["originalData"];
            delete spec["originalInfo"];
          });
          let version = parsedSpectra.version;
          delete parsedSpectra["version"];
          let molecules = parsedSpectra.molecules;
          if (molecules.length > 0) {
            molecules.forEach((mol) => {
              this.standardizeMolecules(mol.molfile).then(
                (res) => {
                  this.associateMoleculeToStudy(
                    res.data,
                    studyDetails.study
                  );
                }
              );
            });
          }
          axios.post(
            "/dashboard/studies/" + studyDetails.study.id + "/nmriumInfo",
            {
              data: parsedSpectra,
              version
            }
          ).then(() => {
            this.loadingStep = false;
            this.studiesToImport.filter(
              (f) => f.study.id == studyDetails.study.id
            )[0].status = true;
            this.autoImportMolecularData(
              studyDetails.study
            );
            this.fetchNMRium();
          }).catch(() => {
            this.loadingStep = false;
            this.studiesToImport.filter(
              (f) => f.study.id == studyDetails.study.id
            )[0].status = true;
            this.fetchNMRium();
          });
        }).catch(() => {
          this.loadingStep = false;
          let study = this.studiesToImport.filter(
            (f) => f.study.id == studyDetails.study.id
          )[0];
          if (study) {
            study.status = true;
            this.fetchNMRium();
          }
        });
      } else {
        this.fetchProjectDetails().then((response) => {
          this.loadingStep = false;
          this.project = response.data.project;
          this.studies = response.data.studies;
          this.fetchValidations();
          this.spectraLoadingStatus = false;
        });
      }
    },
    updateSpecies(species) {
      if (species && species != "") {
        this.studyForm.species.push(species);
        this.saveStudyDetails();
        this.studySpecies = "";
      }
    },
    removeSpecies(index) {
      if (index > -1) {
        this.studyForm.species.splice(index, 1);
        this.saveStudyDetails();
      }
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
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_primer = resolveComponent("primer");
  const _component_file_system_browser = resolveComponent("file-system-browser");
  const _component_EyeIcon = resolveComponent("EyeIcon");
  const _component_EyeSlashIcon = resolveComponent("EyeSlashIcon");
  const _component_Depictor2D = resolveComponent("Depictor2D");
  const _component_Validation = resolveComponent("Validation");
  const _component_SpectraEditor = resolveComponent("SpectraEditor");
  const _component_Depictor = resolveComponent("Depictor");
  const _component_TrashIcon = resolveComponent("TrashIcon");
  const _component_PencilIcon = resolveComponent("PencilIcon");
  const _component_slider = resolveComponent("slider");
  const _component_vue_tags_input = resolveComponent("vue-tags-input");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Submit Data" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="absolute inset-0 bg-gradient-to-r from-[#36b49f] to-[#DBFF75] opacity-40 [mask-image:radial-gradient(farthest-side_at_top,white,transparent)] dark:from-[#36b49f]/30 dark:to-[#DBFF75]/30 dark:opacity-100"${_scopeId}><svg aria-hidden="true" class="absolute inset-x-0 inset-y-[-50%] h-[200%] w-full skew-y-[-18deg] fill-black/40 stroke-black/50 mix-blend-overlay dark:fill-white/2.5 dark:stroke-white/5"${_scopeId}><defs${_scopeId}><pattern id=":r99:" width="72" height="56" patternUnits="userSpaceOnUse" x="-12" y="4"${_scopeId}><path d="M.5 56V.5H72" fill="none"${_scopeId}></path></pattern></defs><rect width="100%" height="100%" stroke-width="0" fill="url(#:r99:)"${_scopeId}></rect><svg x="-12" y="4" class="overflow-visible"${_scopeId}><rect stroke-width="0" width="73" height="57" x="288" y="168"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="144" y="56"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="504" y="168"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="720" y="336"${_scopeId}></rect></svg></svg></div><div class="border-b bg-white relative px-6 py-4"${_scopeId}><div class="w-full sm:flex sm:items-center sm:justify-between"${_scopeId}><div${_scopeId}>`);
        if ($options.currentStep) {
          _push2(`<span class="ml-14 text-sm font-bold text-teal-600 group-hover:text-teal-800"${_scopeId}>Step `);
          if ($options.currentStep.id) {
            _push2(`<span${_scopeId}>${ssrInterpolate($options.currentStep.id)}</span>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(` / 3 - `);
          if ($options.currentStep.id == "1") {
            _push2(`<span${_scopeId}>`);
            if ($data.showPrimer && $data.currentDraft) {
              _push2(`<span${_scopeId}> Introduction </span>`);
            } else {
              _push2(`<span${_scopeId}>File Upload</span>`);
            }
            _push2(`</span>`);
          } else {
            _push2(`<!---->`);
          }
          if ($options.currentStep.id == "2") {
            _push2(`<span${_scopeId}> Auto Processing, Assignments and Validation </span>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<h3 class="text-sm text-gray-700 uppercase font-bold tracking-widest"${_scopeId}>`);
        if ($options.currentStep) {
          _push2(`<span${_scopeId}>`);
          if ($data.step == "1") {
            _push2(`<span${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Link, {
              class: "mr-2 ml-1 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
              href: "/upload"
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
            _push2(`</span>`);
          } else {
            _push2(`<span${_scopeId}><a class="cursor-pointer mr-2 ml-1 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}> ← </a></span>`);
          }
          _push2(`</span>`);
        } else {
          _push2(`<span${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Link, {
            class: "mr-2 ml-1 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
            href: "/upload"
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
          _push2(`</span>`);
        }
        if ($options.currentStep && $data.currentDraft) {
          _push2(`<span${_scopeId}><p class="inline focus:outline-none focus:ring-0 focus:bg-gray-100 p-2 rounded-md" contenteditable${_scopeId}>${ssrInterpolate($data.currentDraft.name)}</p>`);
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.draftForm.errors.name,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</span>`);
        } else {
          _push2(`<span${_scopeId}> Submit data to nmrXiv </span>`);
        }
        _push2(`</h3></div><div class="mt-3 sm:ml-4 sm:mt-0"${_scopeId}>`);
        if ($data.showPrimer && $data.currentDraft) {
          _push2(`<div${_scopeId}><div class="float-left"${_scopeId}><div class="relative mt-2 flex items-start"${_scopeId}><div class="flex h-5 items-center"${_scopeId}><input id="comments" aria-describedby="comments-description" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"${_scopeId}></div><div class="ml-3 text-sm mr-5"${_scopeId}><label for="comments" class="font-medium text-gray-700"${_scopeId}>Don&#39;t show this again</label></div></div></div>`);
          _push2(ssrRenderComponent(_component_jet_button, {
            class: ["ml-2 float-right", {
              "opacity-25": $data.createDatasetForm.processing
            }],
            onClick: ($event) => $options.skipPrimer()
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Proceed `);
              } else {
                return [
                  createTextVNode(" Proceed ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<div${_scopeId}>`);
          if (!$options.currentStep) {
            _push2(`<span${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Link, {
              class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
              href: $data.returnUrl
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Cancel `);
                } else {
                  return [
                    createTextVNode(" Cancel ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span>`);
          } else {
            _push2(`<span${_scopeId}>`);
            if ($options.currentStep.id == "1") {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_Link, {
                class: "mx-2 inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                href: $data.returnUrl
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Cancel `);
                  } else {
                    return [
                      createTextVNode(" Cancel ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_component_jet_button, {
                id: "tour-step-proceed-from-step-1",
                class: ["ml-2", {
                  "opacity-25": $data.createDatasetForm.processing
                }],
                disabled: $data.createDatasetForm.processing || $data.loading || $data.loadingStep,
                onClick: ($event) => $options.process()
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    if ($data.loadingStep) {
                      _push3(`<span${_scopeId2}><svg class="animate-spin -ml-1 mr-3 h-2 w-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId2}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId2}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId2}></path></svg></span>`);
                    } else {
                      _push3(`<!---->`);
                    }
                    _push3(` Proceed `);
                  } else {
                    return [
                      $data.loadingStep ? (openBlock(), createBlock("span", { key: 0 }, [
                        (openBlock(), createBlock("svg", {
                          class: "animate-spin -ml-1 mr-3 h-2 w-2 text-white",
                          xmlns: "http://www.w3.org/2000/svg",
                          fill: "none",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("circle", {
                            class: "opacity-25",
                            cx: "12",
                            cy: "12",
                            r: "10",
                            stroke: "currentColor",
                            "stroke-width": "4"
                          }),
                          createVNode("path", {
                            class: "opacity-75",
                            fill: "currentColor",
                            d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                          })
                        ]))
                      ])) : createCommentVNode("", true),
                      createTextVNode(" Proceed ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</span>`);
            } else if ($options.currentStep.id == "2") {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_jet_secondary_button, {
                class: ["ml-2 float-left", {
                  "opacity-25": $data.createDatasetForm.processing
                }],
                disabled: $data.createDatasetForm.processing,
                onClick: ($event) => $options.selectStep(1)
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Back `);
                  } else {
                    return [
                      createTextVNode(" Back ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_component_jet_button, {
                id: "tour-step-proceed-from-step-2",
                class: ["ml-2", {
                  "opacity-25": $data.createDatasetForm.processing
                }],
                disabled: $data.createDatasetForm.processing || $data.loading || $data.loadingStep,
                onClick: ($event) => $options.closeDraft()
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    if ($data.loadingStep) {
                      _push3(`<span${_scopeId2}><svg class="animate-spin -ml-1 mr-3 h-2 w-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId2}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId2}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId2}></path></svg></span>`);
                    } else {
                      _push3(`<!---->`);
                    }
                    _push3(` Proceed `);
                  } else {
                    return [
                      $data.loadingStep ? (openBlock(), createBlock("span", { key: 0 }, [
                        (openBlock(), createBlock("svg", {
                          class: "animate-spin -ml-1 mr-3 h-2 w-2 text-white",
                          xmlns: "http://www.w3.org/2000/svg",
                          fill: "none",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("circle", {
                            class: "opacity-25",
                            cx: "12",
                            cy: "12",
                            r: "10",
                            stroke: "currentColor",
                            "stroke-width": "4"
                          }),
                          createVNode("path", {
                            class: "opacity-75",
                            fill: "currentColor",
                            d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                          })
                        ]))
                      ])) : createCommentVNode("", true),
                      createTextVNode(" Proceed ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</span>`);
            } else if ($options.currentStep.id == "3") {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_Link, {
                id: "tour-step-finish",
                class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                href: _ctx.route("dashboard")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Finish `);
                  } else {
                    return [
                      createTextVNode(" Finish ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</span>`);
          }
          _push2(`</div>`);
        }
        _push2(`</div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "absolute inset-0 bg-gradient-to-r from-[#36b49f] to-[#DBFF75] opacity-40 [mask-image:radial-gradient(farthest-side_at_top,white,transparent)] dark:from-[#36b49f]/30 dark:to-[#DBFF75]/30 dark:opacity-100" }, [
            (openBlock(), createBlock("svg", {
              "aria-hidden": "true",
              class: "absolute inset-x-0 inset-y-[-50%] h-[200%] w-full skew-y-[-18deg] fill-black/40 stroke-black/50 mix-blend-overlay dark:fill-white/2.5 dark:stroke-white/5"
            }, [
              createVNode("defs", null, [
                createVNode("pattern", {
                  id: ":r99:",
                  width: "72",
                  height: "56",
                  patternUnits: "userSpaceOnUse",
                  x: "-12",
                  y: "4"
                }, [
                  createVNode("path", {
                    d: "M.5 56V.5H72",
                    fill: "none"
                  })
                ])
              ]),
              createVNode("rect", {
                width: "100%",
                height: "100%",
                "stroke-width": "0",
                fill: "url(#:r99:)"
              }),
              (openBlock(), createBlock("svg", {
                x: "-12",
                y: "4",
                class: "overflow-visible"
              }, [
                createVNode("rect", {
                  "stroke-width": "0",
                  width: "73",
                  height: "57",
                  x: "288",
                  y: "168"
                }),
                createVNode("rect", {
                  "stroke-width": "0",
                  width: "73",
                  height: "57",
                  x: "144",
                  y: "56"
                }),
                createVNode("rect", {
                  "stroke-width": "0",
                  width: "73",
                  height: "57",
                  x: "504",
                  y: "168"
                }),
                createVNode("rect", {
                  "stroke-width": "0",
                  width: "73",
                  height: "57",
                  x: "720",
                  y: "336"
                })
              ]))
            ]))
          ]),
          createVNode("div", { class: "border-b bg-white relative px-6 py-4" }, [
            createVNode("div", { class: "w-full sm:flex sm:items-center sm:justify-between" }, [
              createVNode("div", null, [
                $options.currentStep ? (openBlock(), createBlock("span", {
                  key: 0,
                  class: "ml-14 text-sm font-bold text-teal-600 group-hover:text-teal-800"
                }, [
                  createTextVNode("Step "),
                  $options.currentStep.id ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString($options.currentStep.id), 1)) : createCommentVNode("", true),
                  createTextVNode(" / 3 - "),
                  $options.currentStep.id == "1" ? (openBlock(), createBlock("span", { key: 1 }, [
                    $data.showPrimer && $data.currentDraft ? (openBlock(), createBlock("span", { key: 0 }, " Introduction ")) : (openBlock(), createBlock("span", { key: 1 }, "File Upload"))
                  ])) : createCommentVNode("", true),
                  $options.currentStep.id == "2" ? (openBlock(), createBlock("span", { key: 2 }, " Auto Processing, Assignments and Validation ")) : createCommentVNode("", true)
                ])) : createCommentVNode("", true),
                createVNode("h3", { class: "text-sm text-gray-700 uppercase font-bold tracking-widest" }, [
                  $options.currentStep ? (openBlock(), createBlock("span", { key: 0 }, [
                    $data.step == "1" ? (openBlock(), createBlock("span", { key: 0 }, [
                      createVNode(_component_Link, {
                        class: "mr-2 ml-1 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        href: "/upload"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" ← ")
                        ]),
                        _: 1
                      })
                    ])) : (openBlock(), createBlock("span", { key: 1 }, [
                      createVNode("a", {
                        class: "cursor-pointer mr-2 ml-1 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        onClick: ($event) => $options.selectStep(1)
                      }, " ← ", 8, ["onClick"])
                    ]))
                  ])) : (openBlock(), createBlock("span", { key: 1 }, [
                    createVNode(_component_Link, {
                      class: "mr-2 ml-1 inline-flex items-center px-2.5 py-1 text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                      href: "/upload"
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" ← ")
                      ]),
                      _: 1
                    })
                  ])),
                  $options.currentStep && $data.currentDraft ? (openBlock(), createBlock("span", { key: 2 }, [
                    createVNode("p", {
                      class: "inline focus:outline-none focus:ring-0 focus:bg-gray-100 p-2 rounded-md",
                      contenteditable: "",
                      onBlur: ($event) => $options.updateDraft($event)
                    }, toDisplayString($data.currentDraft.name), 41, ["onBlur"]),
                    createVNode(_component_jet_input_error, {
                      message: $data.draftForm.errors.name,
                      class: "mt-2"
                    }, null, 8, ["message"])
                  ])) : (openBlock(), createBlock("span", { key: 3 }, " Submit data to nmrXiv "))
                ])
              ]),
              createVNode("div", { class: "mt-3 sm:ml-4 sm:mt-0" }, [
                $data.showPrimer && $data.currentDraft ? (openBlock(), createBlock("div", { key: 0 }, [
                  createVNode("div", { class: "float-left" }, [
                    createVNode("div", {
                      class: "relative mt-2 flex items-start",
                      onChange: ($event) => $options.hidePrimer()
                    }, [
                      createVNode("div", { class: "flex h-5 items-center" }, [
                        createVNode("input", {
                          id: "comments",
                          "aria-describedby": "comments-description",
                          name: "comments",
                          type: "checkbox",
                          class: "h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        })
                      ]),
                      createVNode("div", { class: "ml-3 text-sm mr-5" }, [
                        createVNode("label", {
                          for: "comments",
                          class: "font-medium text-gray-700"
                        }, "Don't show this again")
                      ])
                    ], 40, ["onChange"])
                  ]),
                  createVNode(_component_jet_button, {
                    class: ["ml-2 float-right", {
                      "opacity-25": $data.createDatasetForm.processing
                    }],
                    onClick: ($event) => $options.skipPrimer()
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Proceed ")
                    ]),
                    _: 1
                  }, 8, ["class", "onClick"])
                ])) : (openBlock(), createBlock("div", { key: 1 }, [
                  !$options.currentStep ? (openBlock(), createBlock("span", { key: 0 }, [
                    createVNode(_component_Link, {
                      class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                      href: $data.returnUrl
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" Cancel ")
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ])) : (openBlock(), createBlock("span", { key: 1 }, [
                    $options.currentStep.id == "1" ? (openBlock(), createBlock("span", { key: 0 }, [
                      createVNode(_component_Link, {
                        class: "mx-2 inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        href: $data.returnUrl
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Cancel ")
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_component_jet_button, {
                        id: "tour-step-proceed-from-step-1",
                        class: ["ml-2", {
                          "opacity-25": $data.createDatasetForm.processing
                        }],
                        disabled: $data.createDatasetForm.processing || $data.loading || $data.loadingStep,
                        onClick: ($event) => $options.process()
                      }, {
                        default: withCtx(() => [
                          $data.loadingStep ? (openBlock(), createBlock("span", { key: 0 }, [
                            (openBlock(), createBlock("svg", {
                              class: "animate-spin -ml-1 mr-3 h-2 w-2 text-white",
                              xmlns: "http://www.w3.org/2000/svg",
                              fill: "none",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("circle", {
                                class: "opacity-25",
                                cx: "12",
                                cy: "12",
                                r: "10",
                                stroke: "currentColor",
                                "stroke-width": "4"
                              }),
                              createVNode("path", {
                                class: "opacity-75",
                                fill: "currentColor",
                                d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                              })
                            ]))
                          ])) : createCommentVNode("", true),
                          createTextVNode(" Proceed ")
                        ]),
                        _: 1
                      }, 8, ["class", "disabled", "onClick"])
                    ])) : $options.currentStep.id == "2" ? (openBlock(), createBlock("span", { key: 1 }, [
                      createVNode(_component_jet_secondary_button, {
                        class: ["ml-2 float-left", {
                          "opacity-25": $data.createDatasetForm.processing
                        }],
                        disabled: $data.createDatasetForm.processing,
                        onClick: ($event) => $options.selectStep(1)
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Back ")
                        ]),
                        _: 1
                      }, 8, ["class", "disabled", "onClick"]),
                      createVNode(_component_jet_button, {
                        id: "tour-step-proceed-from-step-2",
                        class: ["ml-2", {
                          "opacity-25": $data.createDatasetForm.processing
                        }],
                        disabled: $data.createDatasetForm.processing || $data.loading || $data.loadingStep,
                        onClick: ($event) => $options.closeDraft()
                      }, {
                        default: withCtx(() => [
                          $data.loadingStep ? (openBlock(), createBlock("span", { key: 0 }, [
                            (openBlock(), createBlock("svg", {
                              class: "animate-spin -ml-1 mr-3 h-2 w-2 text-white",
                              xmlns: "http://www.w3.org/2000/svg",
                              fill: "none",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("circle", {
                                class: "opacity-25",
                                cx: "12",
                                cy: "12",
                                r: "10",
                                stroke: "currentColor",
                                "stroke-width": "4"
                              }),
                              createVNode("path", {
                                class: "opacity-75",
                                fill: "currentColor",
                                d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                              })
                            ]))
                          ])) : createCommentVNode("", true),
                          createTextVNode(" Proceed ")
                        ]),
                        _: 1
                      }, 8, ["class", "disabled", "onClick"])
                    ])) : $options.currentStep.id == "3" ? (openBlock(), createBlock("span", { key: 2 }, [
                      createVNode(_component_Link, {
                        id: "tour-step-finish",
                        class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        href: _ctx.route("dashboard")
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Finish ")
                        ]),
                        _: 1
                      }, 8, ["href"])
                    ])) : createCommentVNode("", true)
                  ]))
                ]))
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="relative"${_scopeId}>`);
        if (!$data.loading) {
          _push2(`<div${_scopeId}><div class="mx-auto"${_scopeId}>`);
          if ($data.drafts.length > 0 && !$data.currentDraft) {
            _push2(`<div class="px-12"${_scopeId}><div class="my-8 mx-12 mx-auto max-w-none"${_scopeId}><div class="bg-white border rounded-lg shadow-md"${_scopeId}><div class="border-b rounded-t-lg bg-gray-50 border-gray-200 px-4 py-5 sm:px-6"${_scopeId}><div class="-ml-4 -mt-4 flex flex-wrap items-center justify-between sm:flex-nowrap"${_scopeId}><div class="ml-4 mt-4"${_scopeId}><h3 class="text-base font-semibold leading-6 text-gray-900"${_scopeId}> Drafts </h3><p class="mt-1 text-sm text-gray-500"${_scopeId}> Please select one of the drafts below to continue or start a new submission </p></div><div class="ml-4 mt-4 flex-shrink-0"${_scopeId}><button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"${_scopeId}> + Create New </button></div></div></div><ul role="list" class="overflow-y-scroll h-[calc(100vh-290px)]"${_scopeId}><!--[-->`);
            ssrRenderList($data.drafts, (draft) => {
              _push2(`<li class="hover:cursor-pointer border-b hover:bg-gray-50 px-5 py-4"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_Link, {
                href: _ctx.route("upload", {
                  draft_id: draft.id
                })
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="flex items-center space-x-4"${_scopeId2}><div class="flex-1 min-w-0 mr-auto max-w-2xl"${_scopeId2}><p class="text-lg font-large text-black truncate"${_scopeId2}><b${_scopeId2}>${ssrInterpolate(draft.name)}</b></p><p class="text-sm font-medium text-gray-700 truncate pr-10"${_scopeId2}>${ssrInterpolate(draft.description)}</p><p class="text-sm font-medium text-gray-500 truncate"${_scopeId2}> ID: ${ssrInterpolate(draft.key)} · Created at: ${ssrInterpolate(_ctx.formatDateTime(
                      draft.created_at
                    ))}</p></div><div${_scopeId2}><svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId2}><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"${_scopeId2}></path></svg></div></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "flex items-center space-x-4" }, [
                        createVNode("div", { class: "flex-1 min-w-0 mr-auto max-w-2xl" }, [
                          createVNode("p", { class: "text-lg font-large text-black truncate" }, [
                            createVNode("b", null, toDisplayString(draft.name), 1)
                          ]),
                          createVNode("p", { class: "text-sm font-medium text-gray-700 truncate pr-10" }, toDisplayString(draft.description), 1),
                          createVNode("p", { class: "text-sm font-medium text-gray-500 truncate" }, " ID: " + toDisplayString(draft.key) + " · Created at: " + toDisplayString(_ctx.formatDateTime(
                            draft.created_at
                          )), 1)
                        ]),
                        createVNode("div", null, [
                          (openBlock(), createBlock("svg", {
                            class: "h-5 w-5 flex-none text-gray-400",
                            viewBox: "0 0 20 20",
                            fill: "currentColor",
                            "aria-hidden": "true"
                          }, [
                            createVNode("path", {
                              "fill-rule": "evenodd",
                              d: "M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z",
                              "clip-rule": "evenodd"
                            })
                          ]))
                        ])
                      ])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</li>`);
            });
            _push2(`<!--]--></ul></div></div></div>`);
          } else {
            _push2(`<div${_scopeId}>`);
            if ($data.showPrimer) {
              _push2(`<div class="pt-6"${_scopeId}><div class="h-[calc(100vh-235px)] overflow-scroll"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_primer, null, null, _parent2, _scopeId));
              _push2(`</div></div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if ($options.currentStep && $data.currentDraft) {
                _push2(`<div${_scopeId}>`);
                if ($options.currentStep.id == "1") {
                  _push2(`<div id="submission-dropzone" class="border-gray-100"${_scopeId}><div class="mx-5 pt-4"${_scopeId}><small class="cursor-pointer"${_scopeId}>Draft ID: ${ssrInterpolate($data.currentDraft.key)}</small><div class="text-red-600"${_scopeId}>${$data.filesErrorMessage}</div></div><div class="relative border bg-white mt-3"${_scopeId}><div id="tour-step-upload-spectra"${_scopeId}>`);
                  _push2(ssrRenderComponent(_component_file_system_browser, {
                    ref: "fsbRef",
                    readonly: false,
                    draft: $data.currentDraft,
                    height: "h-[calc(100vh-385px)]",
                    onLoading: $options.filesLoading
                  }, null, _parent2, _scopeId));
                  _push2(`</div></div>`);
                  _push2(ssrRenderComponent(_component_jet_input_error, {
                    message: $data.draftForm.errors.studies,
                    class: "mt-2"
                  }, null, _parent2, _scopeId));
                  _push2(`</div>`);
                } else {
                  _push2(`<!---->`);
                }
                if ($options.currentStep.id == "2") {
                  _push2(`<div${_scopeId}><div class="h-[calc(100vh-135px)] overflow-hidden"${_scopeId}><div class="flex-1 flex"${_scopeId}><nav aria-label="Sections" class="flex-shrink-0 w-64 h-[calc(100vh-135px)] overflow-y-hidden bg-white border-r border-gray-200 md:flex md:flex-col"${_scopeId}><div class="${ssrRenderClass([
                    $data.displaySamplesSummaryInfo ? "bg-gray-800 text-white" : "text-dark",
                    "cursor-pointer border-gray-200 px-4 py-3 border-b text-left text-sm font-medium text-gray-500 tracking-wider flex-shrink-0 border-b border-blue-gray-200 hover:bg-gray-800 hover:text-white"
                  ])}"${_scopeId}><a${_scopeId}> SUMMARY </a></div><div class="border-gray-200 px-4 py-3 border-b bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider flex-shrink-0 border-b border-blue-gray-200"${_scopeId}>${ssrInterpolate(_ctx.pluralize(
                    "SAMPLE",
                    $data.studies.length
                  ))} (${ssrInterpolate($data.studies.length)}) <div class="float-right cursor-pointer tooltip"${_scopeId}>`);
                  if (!$data.showCompoundDetails) {
                    _push2(ssrRenderComponent(_component_EyeIcon, { class: "w-4 h-4 mr-1 text-gray-600 hover:text-gray-500" }, null, _parent2, _scopeId));
                  } else {
                    _push2(ssrRenderComponent(_component_EyeSlashIcon, { class: "w-4 h-4 mr-1 text-gray-600 hover:text-gray-500" }, null, _parent2, _scopeId));
                  }
                  _push2(`<div class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextleft"${_scopeId}>`);
                  if (!$data.showCompoundDetails) {
                    _push2(`<span${_scopeId}>Show Compound details</span>`);
                  } else {
                    _push2(`<span${_scopeId}>Hide Compound details</span>`);
                  }
                  _push2(`</div></div></div><div id="tour-step-side-panel-studies" style="${ssrRenderStyle({ "height": "74vh", "overflow": "scroll !important" })}"${_scopeId}><!--[-->`);
                  ssrRenderList($data.studies, (study, $index) => {
                    _push2(`<a class="${ssrRenderClass([
                      $data.selectedStudy && $data.selectedStudy.id == study.id ? "bg-gray-800 text-white" : "hover:bg-gray-200 hover:bg-opacity-50",
                      "cursor-pointer flex py-4 border-b-4 border-blue-gray-200"
                    ])}"${_scopeId}><div class="cursor-pointer px-4 text-sm w-full"${_scopeId}>`);
                    if (study.internal_status == "complete") {
                      _push2(`<div class="font-medium text-blue-gray-900"${_scopeId}>`);
                      if ($data.showCompoundDetails && study.sample.molecules[0]) {
                        _push2(`<div class="border-b border-gray-100 mb-4 border"${_scopeId}>`);
                        _push2(ssrRenderComponent(_component_Depictor2D, {
                          class: "-py-4 -px-4 rounded-md",
                          molecule: study.sample.molecules[0].canonical_smiles
                        }, null, _parent2, _scopeId));
                        _push2(`</div>`);
                      } else {
                        _push2(`<!---->`);
                      }
                      _push2(`<a${_scopeId}>${ssrInterpolate(study.name)}</a>`);
                      if (study.sample.molecules.length > 0) {
                        _push2(`<span class="float-right"${_scopeId}><img class="flex-shrink-0 -mt-0.5 h-6 w-6 text-blue-gray-400" src="https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png" alt=""${_scopeId}></span>`);
                      } else {
                        _push2(`<!---->`);
                      }
                      _push2(`<div class="mt-1 text-blue-gray-500"${_scopeId}><!--[-->`);
                      ssrRenderList(study.datasets, (ds) => {
                        _push2(`<span${_scopeId}><div class="${ssrRenderClass([
                          study.has_nmrium ? "bg-green-100 text-gray-800" : "bg-gray-100 text-gray-800",
                          "mb-0.5 inline-flex truncate break-words items-center px-3 py-0.5 rounded-full text-xs font-medium mr-1"
                        ])}"${_scopeId}><span${_scopeId}>${ssrInterpolate(ds.name)} `);
                        if (ds.type) {
                          _push2(`<span${_scopeId}>(${ssrInterpolate(ds.type)})</span>`);
                        } else {
                          _push2(`<!---->`);
                        }
                        _push2(`</span></div></span>`);
                      });
                      _push2(`<!--]--></div></div>`);
                    } else {
                      _push2(`<div${_scopeId}><div class="p-4 max-w-sm w-full mx-auto"${_scopeId}><div class="animate-pulse flex space-x-4"${_scopeId}><div class="flex-1 space-y-6 py-1"${_scopeId}><div class="h-2 bg-slate-200"${_scopeId}></div><div class="space-y-3"${_scopeId}><div class="grid grid-cols-3 gap-4"${_scopeId}><div class="h-2 bg-slate-200 rounded col-span-2"${_scopeId}></div><div class="h-2 bg-slate-200 rounded col-span-1"${_scopeId}></div></div><div class="h-2 bg-slate-200 rounded"${_scopeId}></div></div></div></div></div></div>`);
                    }
                    _push2(`</div></a>`);
                  });
                  _push2(`<!--]--></div></nav><div class="flex-1 px-2 bg-white"${_scopeId}>`);
                  if ($data.displaySamplesSummaryInfo) {
                    _push2(`<div${_scopeId}><div class="-mx-2 border-b border-b-gray-900/10 lg:border-t lg:border-t-gray-900/5"${_scopeId}><dl class="mx-auto grid max-w-7xl grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:px-2 xl:px-0"${_scopeId}><div class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8"${_scopeId}><dt class="text-sm font-medium leading-6 text-gray-500"${_scopeId}> Samples </dt><dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900"${_scopeId}>${ssrInterpolate($data.studies.length)}</dd></div><div class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 sm:border-l"${_scopeId}><dt class="text-sm font-medium leading-6 text-gray-500"${_scopeId}> Spectra </dt><dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900"${_scopeId}>${ssrInterpolate($options.spectraCount)}</dd></div><div class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 lg:border-l"${_scopeId}><dt class="text-sm font-medium leading-6 text-gray-500"${_scopeId}> Molecules </dt><dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900"${_scopeId}>${ssrInterpolate($options.moleculesCount)}</dd></div>`);
                    if ($data.inprogressStudies.length == 0) {
                      _push2(`<div class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 sm:border-l"${_scopeId}><dt class="text-sm font-medium leading-6 text-gray-500"${_scopeId}> Samples Validation </dt><dd class="w-full flex-none"${_scopeId}>`);
                      if ($data.validationStatus) {
                        _push2(`<span${_scopeId}><span class="text-green-600 text-3xl font-medium leading-10 tracking-tight text-gray-900"${_scopeId}> Success </span></span>`);
                      } else {
                        _push2(`<span${_scopeId}><span class="text-yellow-600 text-3xl font-medium leading-10 tracking-tight text-gray-900"${_scopeId}> Incomplete </span><span class="text-sm text-red-500 tracking-tight"${_scopeId}> Meta data is missing. Please check the validation report below. </span></span>`);
                      }
                      _push2(`</dd></div>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(`</dl></div><div class="overflow-y-scroll h-[calc(100vh-153px)] mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"${_scopeId}>`);
                    if ($data.inprogressStudies.length > 0) {
                      _push2(`<div${_scopeId}><div class="rounded-md bg-red-50 p-4 mb-12"${_scopeId}><div class="flex"${_scopeId}><div class="flex-shrink-0"${_scopeId}><svg class="animate-spin -ml-1 h-5 w-5 text-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId}></path></svg></div><div class="ml-3"${_scopeId}><h3 class="text-sm font-medium text-red-800"${_scopeId}> Processing uploaded data: Please wait for your samples to be processed. </h3><div class="mt-2 text-sm text-red-700"${_scopeId}><ul role="list" class="list-disc space-y-1 pl-5"${_scopeId}><li${_scopeId}> Processed: ${ssrInterpolate($data.studies.length - $data.inprogressStudies.length)} out of ${ssrInterpolate($data.studies.length)} samples </li></ul></div></div></div></div></div>`);
                    } else {
                      _push2(`<div${_scopeId}>`);
                      if ($options.importPendingSamples.length > 0) {
                        _push2(`<div class="mb-4"${_scopeId}><div class="bg-white border shadow sm:rounded-lg"${_scopeId}><div class="px-4 py-5 sm:p-6"${_scopeId}><h3 class="text-base font-semibold leading-6 text-gray-900"${_scopeId}> Spectra Metadata Not Found </h3><div class="mt-2 max-w-xl text-sm text-gray-500"${_scopeId}><p${_scopeId}> Some important Spectra metadata are needed. Would you like us to automatically import the missing Spectra information and kickstart the processing? </p></div><div class="mt-5"${_scopeId}><button type="button" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500"${_scopeId}> Import now </button></div></div></div></div>`);
                      } else {
                        _push2(`<!---->`);
                      }
                      _push2(`</div>`);
                    }
                    _push2(`<div class="mx-auto grid mt-10 max-w-2xl grid-cols-1 grid-rows-1 items-start gap-x-8 gap-y-8 lg:mx-0 lg:max-w-none lg:grid-cols-3"${_scopeId}><div class="lg:col-span-3 mb-24 pb-24"${_scopeId}>`);
                    if ($data.validation && $data.inprogressStudies.length == 0) {
                      _push2(`<div${_scopeId}><button class="px-3 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ml-2 float-right hover:bg-gray-200 hover:text-gray-900"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2 inline"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"${_scopeId}></path></svg> Refresh </button>`);
                      _push2(ssrRenderComponent(_component_Validation, {
                        project: $data.project,
                        validation: $data.validation,
                        mode: "study",
                        onUpdate: $options.editData
                      }, null, _parent2, _scopeId));
                      _push2(`   </div>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(`</div></div></div></div>`);
                  } else {
                    _push2(`<div${_scopeId}><div class="mx-auto flex flex-col md:px-0 xl:px-0"${_scopeId}><div${_scopeId}><div${_scopeId}><div class="px-4 py-1.5 -mx-2 bg-gray-50 border-b"${_scopeId}><h1 class="text-2xl font-extrabold text-gray-900"${_scopeId}>${ssrInterpolate($data.selectedStudy.name)}</h1></div><div class="overflow-y-scroll h-[calc(100vh-153px)]"${_scopeId}><div class="px-2 sm:px-2 md:px-0 pb-24"${_scopeId}><div class="px-2"${_scopeId}><div class="my-7"${_scopeId}>`);
                    _push2(ssrRenderComponent(_component_SpectraEditor, {
                      ref: "spectraEditorREF",
                      project: $data.project,
                      study: $data.selectedStudy,
                      onLoading: $options.spectraLoading
                    }, null, _parent2, _scopeId));
                    _push2(`</div></div><div class="px-2"${_scopeId}><div class="py-4 max-w-7xl mx-auto"${_scopeId}><div class="sm:flex sm:items-center sm:justify-between"${_scopeId}><h3 class="text-xl font-bold text-gray-900 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Chemical composition </h3></div></div><div class="grid grid-cols-2 gap-2"${_scopeId}><div class="pr-2"${_scopeId}>`);
                    if ($data.selectedStudy.sample.molecules.length > 0) {
                      _push2(`<div class="flow-root"${_scopeId}><ul role="list" class="-mb-8"${_scopeId}><!--[-->`);
                      ssrRenderList($data.selectedStudy.sample.molecules, (molecule) => {
                        _push2(`<li${_scopeId}><div class="relative pb-8"${_scopeId}><span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"${_scopeId}></span><div class="relative flex items-start space-x-3"${_scopeId}>`);
                        if (molecule && molecule.pivot) {
                          _push2(`<div class="relative"${_scopeId}><div class="rounded-full border p-2 z-10 bg-gray-100 text-sm"${_scopeId}>${ssrInterpolate(molecule.pivot.percentage_composition)}% </div></div>`);
                        } else {
                          _push2(`<!---->`);
                        }
                        _push2(`<div class="min-w-0 flex-1"${_scopeId}><div${_scopeId}><div class="text-sm"${_scopeId}><a href="#" class="font-medium text-gray-900"${_scopeId}>${ssrInterpolate(molecule.standard_inchi)}</a></div></div><div class="mt-2 text-sm text-gray-700"${_scopeId}><div class="rounded-md border mb-3"${_scopeId}>`);
                        if (molecule.canonical_smiles) {
                          _push2(`<div${_scopeId}>`);
                          _push2(ssrRenderComponent(_component_Depictor, {
                            class: "py-4 -px-4",
                            "model-value": molecule.canonical_smiles,
                            "show-download": false
                          }, null, _parent2, _scopeId));
                          _push2(`</div>`);
                        } else {
                          _push2(`<!---->`);
                        }
                        _push2(`</div><button class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"${_scopeId}>`);
                        _push2(ssrRenderComponent(_component_TrashIcon, { class: "w-4 h-4 inline mr-1" }, null, _parent2, _scopeId));
                        _push2(`Delete </button><button class="inline-flex ml-2 items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"${_scopeId}>`);
                        _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 inline mr-1" }, null, _parent2, _scopeId));
                        _push2(`Edit </button></div></div></div></div></li>`);
                      });
                      _push2(`<!--]--></ul><div class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"${_scopeId}> Sample chemical composition </div></div>`);
                    } else {
                      _push2(`<div${_scopeId}><div class="text-center my-10 py-10"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"${_scopeId}><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"${_scopeId}></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No structures associated with the sample yet! </h3><p class="mt-1 text-sm text-gray-500"${_scopeId}> Get started by adding a new molecule. </p></div></div>`);
                    }
                    _push2(`</div><div class="pl-2"${_scopeId}><div class="sm:col-span-4"${_scopeId}><label for="email" class="block text-sm font-medium text-gray-700"${_scopeId}> SMILES </label><div class="mt-1 mb-2"${_scopeId}><input id="smiles"${ssrRenderAttr(
                      "value",
                      $data.smiles
                    )} name="smiles" type="text" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"${_scopeId}></div>`);
                    if ($data.smiles && $data.smiles != "") {
                      _push2(`<button class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2"${_scopeId}> Load Structure </button>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.errorMessage,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex justify-center"${_scopeId}><span class="px-2 bg-white text-sm text-gray-500"${_scopeId}> Or </span></div></div><div class="float-right text-xs cursor-pointer hover:text-blue-700"${_scopeId}><a href="https://docs.nmrxiv.org/submission-guides/editor.html" target="_blank"${_scopeId}>Need help? </a></div><div id="structureSearchEditor" class="w-full border my-4 rounded-md" style="${ssrRenderStyle({ "height": "400px" })}"${_scopeId}></div><div class="mt-1 mb-6"${_scopeId}><label for="email" class="block text-sm font-medium text-gray-700"${_scopeId}> Percentage composition (${ssrInterpolate($data.percentage)}%) </label>`);
                    _push2(ssrRenderComponent(_component_slider, {
                      modelValue: $data.percentage,
                      "onUpdate:modelValue": ($event) => $data.percentage = $event,
                      min: 0,
                      max: $options.getMax,
                      height: 10,
                      color: "#000",
                      "track-color": "#999"
                    }, null, _parent2, _scopeId));
                    _push2(`</div><button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}> ADD </button></div></div></div><hr class="mt-4 mx-3"${_scopeId}><div class="px-2 sm:px-2"${_scopeId}><div class="pt-4 xl:col-span-2"${_scopeId}><h1 class="text-xl mb-2 font-bold text-gray-900"${_scopeId}> Sample Details </h1><div class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Sample Description <span class="float-right rounded-full px-2"${_scopeId}> Auto generate </span></label><div class="mt-1"${_scopeId}><textarea id="study-description" name="study-description" rows="3" class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"${_scopeId}>${ssrInterpolate(
                      $data.studyForm.description
                    )}</textarea>`);
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.studyForm.errors.description,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div></div><div class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700"${_scopeId}> Keywords </label><div${_scopeId}>`);
                    _push2(ssrRenderComponent(_component_vue_tags_input, {
                      modelValue: $data.studyForm.tag,
                      "onUpdate:modelValue": ($event) => $data.studyForm.tag = $event,
                      placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                      separators: [
                        ";",
                        ","
                      ],
                      "max-width": "100%",
                      tags: $data.studyForm.tags,
                      onTagsChanged: $options.updateTags
                    }, null, _parent2, _scopeId));
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.studyForm.errors.tags,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div></div></div><div class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700"${_scopeId}> Organism (Optional) </label><div class="mt-2 sm:flex sm:items-start sm:justify-between"${_scopeId}><div class="text-sm text-gray-500 w-full"${_scopeId}><ontology-autocomplete class="rounded-md" format="text"${ssrRenderAttr(
                      "value",
                      $data.studySpecies
                    )} placeholder="Search species"${_scopeId}></ontology-autocomplete></div><div class="mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center"${_scopeId}><button type="button" class="inline-flex items-center gap-x-1.5 py-3 bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"${_scopeId}><svg class="-ml-0.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"${_scopeId}></path></svg> Add </button></div></div><div class="mt-2"${_scopeId}><!--[-->`);
                    ssrRenderList($data.studyForm.species, (species, $index) => {
                      _push2(`<div class="bg-gray-100 text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"${_scopeId}><ontology-term-annotation${ssrRenderAttr(
                        "annotation",
                        species
                      )}${_scopeId}></ontology-term-annotation><span class="cursor-pointer"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 ml-2"${_scopeId}><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"${_scopeId}></path></svg></span></div>`);
                    });
                    _push2(`<!--]--></div></div></div></div></div></div></div></div></div>`);
                  }
                  _push2(`</div></div></div>`);
                  if ($data.spectraLoadingStatus) {
                    _push2(`<div class="w-full h-screen mx-84 px-10 fixed block top-0 left-0 bg-white opacity-90 z-50"${_scopeId}><div role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2"${_scopeId}><svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"${_scopeId}><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"${_scopeId}></path><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"${_scopeId}></path></svg><div class="my-4"${_scopeId}>${$data.spectraLoadingMessage}</div><button type="button" class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"${_scopeId}> Cancel </button></div></div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            }
            _push2(`</div>`);
          }
          _push2(`</div></div>`);
        } else {
          _push2(`<div class="h-[calc(100vh-260px)] text-center py-12"${_scopeId}><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId}></path></svg> Loading... </div>`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "relative" }, [
            !$data.loading ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("div", { class: "mx-auto" }, [
                $data.drafts.length > 0 && !$data.currentDraft ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "px-12"
                }, [
                  createVNode("div", { class: "my-8 mx-12 mx-auto max-w-none" }, [
                    createVNode("div", { class: "bg-white border rounded-lg shadow-md" }, [
                      createVNode("div", { class: "border-b rounded-t-lg bg-gray-50 border-gray-200 px-4 py-5 sm:px-6" }, [
                        createVNode("div", { class: "-ml-4 -mt-4 flex flex-wrap items-center justify-between sm:flex-nowrap" }, [
                          createVNode("div", { class: "ml-4 mt-4" }, [
                            createVNode("h3", { class: "text-base font-semibold leading-6 text-gray-900" }, " Drafts "),
                            createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Please select one of the drafts below to continue or start a new submission ")
                          ]),
                          createVNode("div", { class: "ml-4 mt-4 flex-shrink-0" }, [
                            createVNode("button", {
                              class: "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition",
                              onClick: ($event) => $options.createNewDraft()
                            }, " + Create New ", 8, ["onClick"])
                          ])
                        ])
                      ]),
                      createVNode("ul", {
                        role: "list",
                        class: "overflow-y-scroll h-[calc(100vh-290px)]"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList($data.drafts, (draft) => {
                          return openBlock(), createBlock("li", {
                            key: draft.id,
                            class: "hover:cursor-pointer border-b hover:bg-gray-50 px-5 py-4"
                          }, [
                            createVNode(_component_Link, {
                              href: _ctx.route("upload", {
                                draft_id: draft.id
                              })
                            }, {
                              default: withCtx(() => [
                                createVNode("div", { class: "flex items-center space-x-4" }, [
                                  createVNode("div", { class: "flex-1 min-w-0 mr-auto max-w-2xl" }, [
                                    createVNode("p", { class: "text-lg font-large text-black truncate" }, [
                                      createVNode("b", null, toDisplayString(draft.name), 1)
                                    ]),
                                    createVNode("p", { class: "text-sm font-medium text-gray-700 truncate pr-10" }, toDisplayString(draft.description), 1),
                                    createVNode("p", { class: "text-sm font-medium text-gray-500 truncate" }, " ID: " + toDisplayString(draft.key) + " · Created at: " + toDisplayString(_ctx.formatDateTime(
                                      draft.created_at
                                    )), 1)
                                  ]),
                                  createVNode("div", null, [
                                    (openBlock(), createBlock("svg", {
                                      class: "h-5 w-5 flex-none text-gray-400",
                                      viewBox: "0 0 20 20",
                                      fill: "currentColor",
                                      "aria-hidden": "true"
                                    }, [
                                      createVNode("path", {
                                        "fill-rule": "evenodd",
                                        d: "M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z",
                                        "clip-rule": "evenodd"
                                      })
                                    ]))
                                  ])
                                ])
                              ]),
                              _: 2
                            }, 1032, ["href"])
                          ]);
                        }), 128))
                      ])
                    ])
                  ])
                ])) : (openBlock(), createBlock("div", { key: 1 }, [
                  $data.showPrimer ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "pt-6"
                  }, [
                    createVNode("div", { class: "h-[calc(100vh-235px)] overflow-scroll" }, [
                      createVNode(_component_primer)
                    ])
                  ])) : (openBlock(), createBlock("div", { key: 1 }, [
                    $options.currentStep && $data.currentDraft ? (openBlock(), createBlock("div", { key: 0 }, [
                      $options.currentStep.id == "1" ? (openBlock(), createBlock("div", {
                        key: 0,
                        id: "submission-dropzone",
                        class: "border-gray-100"
                      }, [
                        createVNode("div", { class: "mx-5 pt-4" }, [
                          createVNode("small", { class: "cursor-pointer" }, "Draft ID: " + toDisplayString($data.currentDraft.key), 1),
                          createVNode("div", {
                            class: "text-red-600",
                            innerHTML: $data.filesErrorMessage
                          }, null, 8, ["innerHTML"])
                        ]),
                        createVNode("div", { class: "relative border bg-white mt-3" }, [
                          createVNode("div", { id: "tour-step-upload-spectra" }, [
                            createVNode(_component_file_system_browser, {
                              ref: "fsbRef",
                              readonly: false,
                              draft: $data.currentDraft,
                              height: "h-[calc(100vh-385px)]",
                              onLoading: $options.filesLoading
                            }, null, 8, ["draft", "height", "onLoading"])
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.draftForm.errors.studies,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ])) : createCommentVNode("", true),
                      $options.currentStep.id == "2" ? (openBlock(), createBlock("div", { key: 1 }, [
                        createVNode("div", { class: "h-[calc(100vh-135px)] overflow-hidden" }, [
                          createVNode("div", { class: "flex-1 flex" }, [
                            createVNode("nav", {
                              "aria-label": "Sections",
                              class: "flex-shrink-0 w-64 h-[calc(100vh-135px)] overflow-y-hidden bg-white border-r border-gray-200 md:flex md:flex-col"
                            }, [
                              createVNode("div", {
                                class: [
                                  $data.displaySamplesSummaryInfo ? "bg-gray-800 text-white" : "text-dark",
                                  "cursor-pointer border-gray-200 px-4 py-3 border-b text-left text-sm font-medium text-gray-500 tracking-wider flex-shrink-0 border-b border-blue-gray-200 hover:bg-gray-800 hover:text-white"
                                ],
                                onClick: $options.showSamplesSummary
                              }, [
                                createVNode("a", null, " SUMMARY ")
                              ], 10, ["onClick"]),
                              createVNode("div", { class: "border-gray-200 px-4 py-3 border-b bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider flex-shrink-0 border-b border-blue-gray-200" }, [
                                createTextVNode(toDisplayString(_ctx.pluralize(
                                  "SAMPLE",
                                  $data.studies.length
                                )) + " (" + toDisplayString($data.studies.length) + ") ", 1),
                                createVNode("div", {
                                  class: "float-right cursor-pointer tooltip",
                                  onClick: ($event) => $options.toggleCompoundDetails()
                                }, [
                                  !$data.showCompoundDetails ? (openBlock(), createBlock(_component_EyeIcon, {
                                    key: 0,
                                    class: "w-4 h-4 mr-1 text-gray-600 hover:text-gray-500"
                                  })) : (openBlock(), createBlock(_component_EyeSlashIcon, {
                                    key: 1,
                                    class: "w-4 h-4 mr-1 text-gray-600 hover:text-gray-500"
                                  })),
                                  createVNode("div", { class: "bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextleft" }, [
                                    !$data.showCompoundDetails ? (openBlock(), createBlock("span", { key: 0 }, "Show Compound details")) : (openBlock(), createBlock("span", { key: 1 }, "Hide Compound details"))
                                  ])
                                ], 8, ["onClick"])
                              ]),
                              createVNode("div", {
                                id: "tour-step-side-panel-studies",
                                style: { "height": "74vh", "overflow": "scroll !important" }
                              }, [
                                (openBlock(true), createBlock(Fragment, null, renderList($data.studies, (study, $index) => {
                                  return openBlock(), createBlock("a", {
                                    key: study.slug,
                                    class: [
                                      $data.selectedStudy && $data.selectedStudy.id == study.id ? "bg-gray-800 text-white" : "hover:bg-gray-200 hover:bg-opacity-50",
                                      "cursor-pointer flex py-4 border-b-4 border-blue-gray-200"
                                    ]
                                  }, [
                                    createVNode("div", {
                                      class: "cursor-pointer px-4 text-sm w-full",
                                      onClick: ($event) => $options.selectStudy(
                                        study,
                                        $index
                                      )
                                    }, [
                                      study.internal_status == "complete" ? (openBlock(), createBlock("div", {
                                        key: 0,
                                        class: "font-medium text-blue-gray-900"
                                      }, [
                                        $data.showCompoundDetails && study.sample.molecules[0] ? (openBlock(), createBlock("div", {
                                          key: 0,
                                          class: "border-b border-gray-100 mb-4 border"
                                        }, [
                                          createVNode(_component_Depictor2D, {
                                            class: "-py-4 -px-4 rounded-md",
                                            molecule: study.sample.molecules[0].canonical_smiles
                                          }, null, 8, ["molecule"])
                                        ])) : createCommentVNode("", true),
                                        createVNode("a", null, toDisplayString(study.name), 1),
                                        study.sample.molecules.length > 0 ? (openBlock(), createBlock("span", {
                                          key: 1,
                                          class: "float-right"
                                        }, [
                                          createVNode("img", {
                                            class: "flex-shrink-0 -mt-0.5 h-6 w-6 text-blue-gray-400",
                                            src: "https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png",
                                            alt: ""
                                          })
                                        ])) : createCommentVNode("", true),
                                        createVNode("div", { class: "mt-1 text-blue-gray-500" }, [
                                          (openBlock(true), createBlock(Fragment, null, renderList(study.datasets, (ds) => {
                                            return openBlock(), createBlock("span", {
                                              key: ds.id
                                            }, [
                                              createVNode("div", {
                                                class: [
                                                  study.has_nmrium ? "bg-green-100 text-gray-800" : "bg-gray-100 text-gray-800",
                                                  "mb-0.5 inline-flex truncate break-words items-center px-3 py-0.5 rounded-full text-xs font-medium mr-1"
                                                ]
                                              }, [
                                                createVNode("span", null, [
                                                  createTextVNode(toDisplayString(ds.name) + " ", 1),
                                                  ds.type ? (openBlock(), createBlock("span", { key: 0 }, "(" + toDisplayString(ds.type) + ")", 1)) : createCommentVNode("", true)
                                                ])
                                              ], 2)
                                            ]);
                                          }), 128))
                                        ])
                                      ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                        createVNode("div", { class: "p-4 max-w-sm w-full mx-auto" }, [
                                          createVNode("div", { class: "animate-pulse flex space-x-4" }, [
                                            createVNode("div", { class: "flex-1 space-y-6 py-1" }, [
                                              createVNode("div", { class: "h-2 bg-slate-200" }),
                                              createVNode("div", { class: "space-y-3" }, [
                                                createVNode("div", { class: "grid grid-cols-3 gap-4" }, [
                                                  createVNode("div", { class: "h-2 bg-slate-200 rounded col-span-2" }),
                                                  createVNode("div", { class: "h-2 bg-slate-200 rounded col-span-1" })
                                                ]),
                                                createVNode("div", { class: "h-2 bg-slate-200 rounded" })
                                              ])
                                            ])
                                          ])
                                        ])
                                      ]))
                                    ], 8, ["onClick"])
                                  ], 2);
                                }), 128))
                              ])
                            ]),
                            createVNode("div", { class: "flex-1 px-2 bg-white" }, [
                              $data.displaySamplesSummaryInfo ? (openBlock(), createBlock("div", { key: 0 }, [
                                createVNode("div", { class: "-mx-2 border-b border-b-gray-900/10 lg:border-t lg:border-t-gray-900/5" }, [
                                  createVNode("dl", { class: "mx-auto grid max-w-7xl grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:px-2 xl:px-0" }, [
                                    createVNode("div", { class: "flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8" }, [
                                      createVNode("dt", { class: "text-sm font-medium leading-6 text-gray-500" }, " Samples "),
                                      createVNode("dd", { class: "w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900" }, toDisplayString($data.studies.length), 1)
                                    ]),
                                    createVNode("div", { class: "flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 sm:border-l" }, [
                                      createVNode("dt", { class: "text-sm font-medium leading-6 text-gray-500" }, " Spectra "),
                                      createVNode("dd", { class: "w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900" }, toDisplayString($options.spectraCount), 1)
                                    ]),
                                    createVNode("div", { class: "flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 lg:border-l" }, [
                                      createVNode("dt", { class: "text-sm font-medium leading-6 text-gray-500" }, " Molecules "),
                                      createVNode("dd", { class: "w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900" }, toDisplayString($options.moleculesCount), 1)
                                    ]),
                                    $data.inprogressStudies.length == 0 ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 sm:border-l"
                                    }, [
                                      createVNode("dt", { class: "text-sm font-medium leading-6 text-gray-500" }, " Samples Validation "),
                                      createVNode("dd", { class: "w-full flex-none" }, [
                                        $data.validationStatus ? (openBlock(), createBlock("span", { key: 0 }, [
                                          createVNode("span", { class: "text-green-600 text-3xl font-medium leading-10 tracking-tight text-gray-900" }, " Success ")
                                        ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                          createVNode("span", { class: "text-yellow-600 text-3xl font-medium leading-10 tracking-tight text-gray-900" }, " Incomplete "),
                                          createVNode("span", { class: "text-sm text-red-500 tracking-tight" }, " Meta data is missing. Please check the validation report below. ")
                                        ]))
                                      ])
                                    ])) : createCommentVNode("", true)
                                  ])
                                ]),
                                createVNode("div", { class: "overflow-y-scroll h-[calc(100vh-153px)] mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" }, [
                                  $data.inprogressStudies.length > 0 ? (openBlock(), createBlock("div", { key: 0 }, [
                                    createVNode("div", { class: "rounded-md bg-red-50 p-4 mb-12" }, [
                                      createVNode("div", { class: "flex" }, [
                                        createVNode("div", { class: "flex-shrink-0" }, [
                                          (openBlock(), createBlock("svg", {
                                            class: "animate-spin -ml-1 h-5 w-5 text-red",
                                            xmlns: "http://www.w3.org/2000/svg",
                                            fill: "none",
                                            viewBox: "0 0 24 24"
                                          }, [
                                            createVNode("circle", {
                                              class: "opacity-25",
                                              cx: "12",
                                              cy: "12",
                                              r: "10",
                                              stroke: "currentColor",
                                              "stroke-width": "4"
                                            }),
                                            createVNode("path", {
                                              class: "opacity-75",
                                              fill: "currentColor",
                                              d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            })
                                          ]))
                                        ]),
                                        createVNode("div", { class: "ml-3" }, [
                                          createVNode("h3", { class: "text-sm font-medium text-red-800" }, " Processing uploaded data: Please wait for your samples to be processed. "),
                                          createVNode("div", { class: "mt-2 text-sm text-red-700" }, [
                                            createVNode("ul", {
                                              role: "list",
                                              class: "list-disc space-y-1 pl-5"
                                            }, [
                                              createVNode("li", null, " Processed: " + toDisplayString($data.studies.length - $data.inprogressStudies.length) + " out of " + toDisplayString($data.studies.length) + " samples ", 1)
                                            ])
                                          ])
                                        ])
                                      ])
                                    ])
                                  ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                    $options.importPendingSamples.length > 0 ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "mb-4"
                                    }, [
                                      createVNode("div", { class: "bg-white border shadow sm:rounded-lg" }, [
                                        createVNode("div", { class: "px-4 py-5 sm:p-6" }, [
                                          createVNode("h3", { class: "text-base font-semibold leading-6 text-gray-900" }, " Spectra Metadata Not Found "),
                                          createVNode("div", { class: "mt-2 max-w-xl text-sm text-gray-500" }, [
                                            createVNode("p", null, " Some important Spectra metadata are needed. Would you like us to automatically import the missing Spectra information and kickstart the processing? ")
                                          ]),
                                          createVNode("div", { class: "mt-5" }, [
                                            createVNode("button", {
                                              type: "button",
                                              class: "inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500",
                                              onClick: $options.autoImport
                                            }, " Import now ", 8, ["onClick"])
                                          ])
                                        ])
                                      ])
                                    ])) : createCommentVNode("", true)
                                  ])),
                                  createVNode("div", { class: "mx-auto grid mt-10 max-w-2xl grid-cols-1 grid-rows-1 items-start gap-x-8 gap-y-8 lg:mx-0 lg:max-w-none lg:grid-cols-3" }, [
                                    createVNode("div", { class: "lg:col-span-3 mb-24 pb-24" }, [
                                      $data.validation && $data.inprogressStudies.length == 0 ? (openBlock(), createBlock("div", { key: 0 }, [
                                        createVNode("button", {
                                          class: "px-3 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ml-2 float-right hover:bg-gray-200 hover:text-gray-900",
                                          onClick: $options.fetchValidations
                                        }, [
                                          (openBlock(), createBlock("svg", {
                                            xmlns: "http://www.w3.org/2000/svg",
                                            fill: "none",
                                            viewBox: "0 0 24 24",
                                            "stroke-width": "1.5",
                                            stroke: "currentColor",
                                            class: "w-4 h-4 mr-2 inline"
                                          }, [
                                            createVNode("path", {
                                              "stroke-linecap": "round",
                                              "stroke-linejoin": "round",
                                              d: "M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"
                                            })
                                          ])),
                                          createTextVNode(" Refresh ")
                                        ], 8, ["onClick"]),
                                        createVNode(_component_Validation, {
                                          project: $data.project,
                                          validation: $data.validation,
                                          mode: "study",
                                          onUpdate: $options.editData
                                        }, null, 8, ["project", "validation", "onUpdate"]),
                                        createTextVNode("   ")
                                      ])) : createCommentVNode("", true)
                                    ])
                                  ])
                                ])
                              ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                createVNode("div", { class: "mx-auto flex flex-col md:px-0 xl:px-0" }, [
                                  createVNode("div", null, [
                                    createVNode("div", null, [
                                      createVNode("div", { class: "px-4 py-1.5 -mx-2 bg-gray-50 border-b" }, [
                                        createVNode("h1", { class: "text-2xl font-extrabold text-gray-900" }, toDisplayString($data.selectedStudy.name), 1)
                                      ]),
                                      createVNode("div", { class: "overflow-y-scroll h-[calc(100vh-153px)]" }, [
                                        createVNode("div", { class: "px-2 sm:px-2 md:px-0 pb-24" }, [
                                          createVNode("div", { class: "px-2" }, [
                                            createVNode("div", { class: "my-7" }, [
                                              createVNode(_component_SpectraEditor, {
                                                ref: "spectraEditorREF",
                                                project: $data.project,
                                                study: $data.selectedStudy,
                                                onLoading: $options.spectraLoading
                                              }, null, 8, ["project", "study", "onLoading"])
                                            ])
                                          ]),
                                          createVNode("div", { class: "px-2" }, [
                                            createVNode("div", { class: "py-4 max-w-7xl mx-auto" }, [
                                              createVNode("div", { class: "sm:flex sm:items-center sm:justify-between" }, [
                                                createVNode("h3", { class: "text-xl font-bold text-gray-900 after:content-['*'] after:ml-0.5 after:text-red-500" }, " Chemical composition ")
                                              ])
                                            ]),
                                            createVNode("div", { class: "grid grid-cols-2 gap-2" }, [
                                              createVNode("div", { class: "pr-2" }, [
                                                $data.selectedStudy.sample.molecules.length > 0 ? (openBlock(), createBlock("div", {
                                                  key: 0,
                                                  class: "flow-root"
                                                }, [
                                                  createVNode("ul", {
                                                    role: "list",
                                                    class: "-mb-8"
                                                  }, [
                                                    (openBlock(true), createBlock(Fragment, null, renderList($data.selectedStudy.sample.molecules, (molecule) => {
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
                                                                  createVNode("a", {
                                                                    href: "#",
                                                                    class: "font-medium text-gray-900"
                                                                  }, toDisplayString(molecule.standard_inchi), 1)
                                                                ])
                                                              ]),
                                                              createVNode("div", { class: "mt-2 text-sm text-gray-700" }, [
                                                                createVNode("div", { class: "rounded-md border mb-3" }, [
                                                                  molecule.canonical_smiles ? (openBlock(), createBlock("div", { key: 0 }, [
                                                                    createVNode(_component_Depictor, {
                                                                      class: "py-4 -px-4",
                                                                      "model-value": molecule.canonical_smiles,
                                                                      "show-download": false
                                                                    }, null, 8, ["model-value"])
                                                                  ])) : createCommentVNode("", true)
                                                                ]),
                                                                createVNode("button", {
                                                                  class: "inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500",
                                                                  onClick: ($event) => $options.deleteMolecule(
                                                                    molecule
                                                                  )
                                                                }, [
                                                                  createVNode(_component_TrashIcon, { class: "w-4 h-4 inline mr-1" }),
                                                                  createTextVNode("Delete ")
                                                                ], 8, ["onClick"]),
                                                                createVNode("button", {
                                                                  class: "inline-flex ml-2 items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",
                                                                  onClick: ($event) => $options.editMolecule(
                                                                    molecule
                                                                  )
                                                                }, [
                                                                  createVNode(_component_PencilIcon, { class: "w-4 h-4 inline mr-1" }),
                                                                  createTextVNode("Edit ")
                                                                ], 8, ["onClick"])
                                                              ])
                                                            ])
                                                          ])
                                                        ])
                                                      ]);
                                                    }), 128))
                                                  ]),
                                                  createVNode("div", { class: "rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center" }, " Sample chemical composition ")
                                                ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                                  createVNode("div", { class: "text-center my-10 py-10" }, [
                                                    (openBlock(), createBlock("svg", {
                                                      class: "mx-auto h-12 w-12 text-gray-400",
                                                      fill: "none",
                                                      viewBox: "0 0 24 24",
                                                      stroke: "currentColor",
                                                      "aria-hidden": "true"
                                                    }, [
                                                      createVNode("path", {
                                                        "vector-effect": "non-scaling-stroke",
                                                        "stroke-linecap": "round",
                                                        "stroke-linejoin": "round",
                                                        "stroke-width": "2",
                                                        d: "M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                                                      })
                                                    ])),
                                                    createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No structures associated with the sample yet! "),
                                                    createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Get started by adding a new molecule. ")
                                                  ])
                                                ]))
                                              ]),
                                              createVNode("div", { class: "pl-2" }, [
                                                createVNode("div", { class: "sm:col-span-4" }, [
                                                  createVNode("label", {
                                                    for: "email",
                                                    class: "block text-sm font-medium text-gray-700"
                                                  }, " SMILES "),
                                                  createVNode("div", { class: "mt-1 mb-2" }, [
                                                    withDirectives(createVNode("input", {
                                                      id: "smiles",
                                                      "onUpdate:modelValue": ($event) => $data.smiles = $event,
                                                      name: "smiles",
                                                      type: "text",
                                                      class: "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md",
                                                      onBlur: $options.loadSmiles
                                                    }, null, 40, ["onUpdate:modelValue", "onBlur"]), [
                                                      [
                                                        vModelText,
                                                        $data.smiles
                                                      ]
                                                    ])
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
                                                createVNode("div", { class: "float-right text-xs cursor-pointer hover:text-blue-700" }, [
                                                  createVNode("a", {
                                                    href: "https://docs.nmrxiv.org/submission-guides/editor.html",
                                                    target: "_blank"
                                                  }, "Need help? ")
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
                                                  }, " Percentage composition (" + toDisplayString($data.percentage) + "%) ", 1),
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
                                                  onClick: ($event) => $options.saveMolecule(
                                                    null
                                                  )
                                                }, " ADD ", 8, ["onClick"])
                                              ])
                                            ])
                                          ]),
                                          createVNode("hr", { class: "mt-4 mx-3" }),
                                          createVNode("div", { class: "px-2 sm:px-2" }, [
                                            createVNode("div", { class: "pt-4 xl:col-span-2" }, [
                                              createVNode("h1", { class: "text-xl mb-2 font-bold text-gray-900" }, " Sample Details "),
                                              createVNode("div", { class: "mb-3" }, [
                                                createVNode("label", {
                                                  for: "description",
                                                  class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                                                }, [
                                                  createTextVNode(" Sample Description "),
                                                  createVNode("span", {
                                                    class: "float-right rounded-full px-2",
                                                    onClick: ($event) => $options.autoGenerateDescription()
                                                  }, " Auto generate ", 8, ["onClick"])
                                                ]),
                                                createVNode("div", { class: "mt-1" }, [
                                                  withDirectives(createVNode("textarea", {
                                                    id: "study-description",
                                                    "onUpdate:modelValue": ($event) => $data.studyForm.description = $event,
                                                    name: "study-description",
                                                    rows: "3",
                                                    class: "block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md",
                                                    onBlur: $options.saveStudyDetails
                                                  }, null, 40, ["onUpdate:modelValue", "onBlur"]), [
                                                    [
                                                      vModelText,
                                                      $data.studyForm.description
                                                    ]
                                                  ]),
                                                  createVNode(_component_jet_input_error, {
                                                    message: $data.studyForm.errors.description,
                                                    class: "mt-2"
                                                  }, null, 8, ["message"])
                                                ])
                                              ]),
                                              createVNode("div", { class: "mb-3" }, [
                                                createVNode("label", {
                                                  for: "description",
                                                  class: "block text-sm font-medium text-gray-700"
                                                }, " Keywords "),
                                                createVNode("div", null, [
                                                  createVNode(_component_vue_tags_input, {
                                                    modelValue: $data.studyForm.tag,
                                                    "onUpdate:modelValue": ($event) => $data.studyForm.tag = $event,
                                                    placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                                                    separators: [
                                                      ";",
                                                      ","
                                                    ],
                                                    "max-width": "100%",
                                                    tags: $data.studyForm.tags,
                                                    onTagsChanged: $options.updateTags
                                                  }, null, 8, ["modelValue", "onUpdate:modelValue", "tags", "onTagsChanged"]),
                                                  createVNode(_component_jet_input_error, {
                                                    message: $data.studyForm.errors.tags,
                                                    class: "mt-2"
                                                  }, null, 8, ["message"])
                                                ])
                                              ])
                                            ]),
                                            createVNode("div", { class: "mb-3" }, [
                                              createVNode("label", {
                                                for: "description",
                                                class: "block text-sm font-medium text-gray-700"
                                              }, " Organism (Optional) "),
                                              createVNode("div", { class: "mt-2 sm:flex sm:items-start sm:justify-between" }, [
                                                createVNode("div", { class: "text-sm text-gray-500 w-full" }, [
                                                  createVNode("ontology-autocomplete", {
                                                    class: "rounded-md",
                                                    format: "text",
                                                    value: $data.studySpecies,
                                                    placeholder: "Search species",
                                                    onChange: ($event) => $data.studySpecies = $event.detail[0],
                                                    onBlur: ($event) => $options.updateSpecies(
                                                      $data.studySpecies
                                                    )
                                                  }, null, 40, ["value", "onChange", "onBlur"])
                                                ]),
                                                createVNode("div", { class: "mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center" }, [
                                                  createVNode("button", {
                                                    type: "button",
                                                    class: "inline-flex items-center gap-x-1.5 py-3 bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50",
                                                    onClick: ($event) => $options.updateSpecies(
                                                      $data.studySpecies
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
                                                (openBlock(true), createBlock(Fragment, null, renderList($data.studyForm.species, (species, $index) => {
                                                  return openBlock(), createBlock("div", {
                                                    key: $index,
                                                    class: "bg-gray-100 text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"
                                                  }, [
                                                    createVNode("ontology-term-annotation", {
                                                      annotation: species
                                                    }, null, 8, ["annotation"]),
                                                    createVNode("span", {
                                                      class: "cursor-pointer",
                                                      onClick: ($event) => $options.removeSpecies(
                                                        $index
                                                      )
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
                                          ])
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ]))
                            ])
                          ])
                        ]),
                        $data.spectraLoadingStatus ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "w-full h-screen mx-84 px-10 fixed block top-0 left-0 bg-white opacity-90 z-50"
                        }, [
                          createVNode("div", {
                            role: "status",
                            class: "absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2"
                          }, [
                            (openBlock(), createBlock("svg", {
                              "aria-hidden": "true",
                              class: "w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600",
                              viewBox: "0 0 100 101",
                              fill: "none",
                              xmlns: "http://www.w3.org/2000/svg"
                            }, [
                              createVNode("path", {
                                d: "M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z",
                                fill: "currentColor"
                              }),
                              createVNode("path", {
                                d: "M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z",
                                fill: "currentFill"
                              })
                            ])),
                            createVNode("div", {
                              innerHTML: $data.spectraLoadingMessage,
                              class: "my-4"
                            }, null, 8, ["innerHTML"]),
                            createVNode("button", {
                              type: "button",
                              class: "inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto",
                              onClick: $options.showSamplesSummary
                            }, " Cancel ", 8, ["onClick"])
                          ])
                        ])) : createCommentVNode("", true)
                      ])) : createCommentVNode("", true)
                    ])) : createCommentVNode("", true)
                  ]))
                ]))
              ])
            ])) : (openBlock(), createBlock("div", {
              key: 1,
              class: "h-[calc(100vh-260px)] text-center py-12"
            }, [
              (openBlock(), createBlock("svg", {
                class: "animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline",
                xmlns: "http://www.w3.org/2000/svg",
                fill: "none",
                viewBox: "0 0 24 24"
              }, [
                createVNode("circle", {
                  class: "opacity-25",
                  cx: "12",
                  cy: "12",
                  r: "10",
                  stroke: "currentColor",
                  "stroke-width": "4"
                }),
                createVNode("path", {
                  class: "opacity-75",
                  fill: "currentColor",
                  d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                })
              ])),
              createTextVNode(" Loading... ")
            ]))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Upload.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Upload = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Upload as default
};
