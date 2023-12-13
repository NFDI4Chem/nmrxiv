import { Link } from "@inertiajs/vue3";
import StudyCreate from "./Create-05bb83de.mjs";
import { S as StudyCard } from "./StudyCard-ce624709.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { ArrowLongLeftIcon, ArrowLongRightIcon } from "@heroicons/vue/24/solid";
import { resolveComponent, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderStyle, ssrRenderList, ssrRenderComponent, ssrRenderClass } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "@headlessui/vue";
import "./InputError-6ae0b65c.mjs";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./Depictor2D-b60253ae.mjs";
import "openchemlib/full.js";
const _sfc_main = {
  components: {
    Link,
    StudyCreate,
    StudyCard,
    JetButton,
    ArrowLongLeftIcon,
    ArrowLongRightIcon
  },
  props: {
    project: {
      default: null,
      type: Object
    },
    role: {
      default: null,
      type: String
    },
    teamRole: {
      default: null,
      type: String
    }
  },
  data() {
    return {
      loading: false,
      studies: [],
      query: ""
    };
  },
  mounted() {
    if (this.studies.length == 0) {
      if (this.project) {
        this.loading = true;
        this.fetchStudies(
          route("dashboard.project.studies", this.project.id)
        );
      }
    }
  },
  methods: {
    openDatasetCreateDialog() {
      this.emitter.emit("openDatasetCreateDialog", {
        draft_id: this.project.draft_id,
        return_url: "/projects/" + this.project.id
      });
    },
    fetchStudies(url) {
      axios.get(url).then((response) => {
        this.loading = false;
        this.studies = response.data;
      });
    },
    update(link) {
      if (!link) {
        link = {};
        link["url"] = route("dashboard.project.studies", this.project.id) + "?page=1";
      }
      if (link.url) {
        this.loading = true;
        this.executeQuery(link);
      }
    },
    reset() {
      let link = {};
      link["url"] = route("dashboard.project.studies", this.project.id) + "?page=1";
      this.query = "";
      this.loading = true;
      this.executeQuery(link);
    },
    executeQuery(link) {
      this.fetchStudies(link.url + "&search=" + this.query);
    },
    openStudyCreateDialog() {
      this.emitter.emit("openStudyCreateDialog", {});
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_study_card = resolveComponent("study-card");
  _push(`<div${ssrRenderAttrs(_attrs)}><div class="flex items-baseline justify-between"><div><h2 class="text-lg">Samples</h2><div class="float-center text-xs cursor-pointer hover:text-blue-700 mb-2"><a href="https://docs.nmrxiv.org/submission-guides/data-model/sample.html" target="_blank">Learn more about samples </a></div>`);
  if (!$data.loading) {
    _push(`<div class="flex items-center mr-4 w-full"><div class="flex w-full bg-white shadow rounded-full"><input${ssrRenderAttr("value", $data.query)} class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Search…"></div><button class="ml-2 inline-flex items-center rounded-full px-6 py-3 shadow rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500" style="${ssrRenderStyle({ "overflow-anchor": "none" })}"> GO </button><button class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500" style="${ssrRenderStyle({ "overflow-anchor": "none" })}"> Reset </button></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
  if (_ctx.editable && $props.project.draft_id !== null && !$props.project.is_deleted) {
    _push(`<div class="flex-shrink-0 ml-4"><a type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${ssrRenderAttr("href", "/upload?draft_id=" + $props.project.draft_id)}> + Manage Samples </a><div class="text-center text-xs cursor-pointer hover:text-blue-700 mt-2"><a href="https://docs.nmrxiv.org/submission-guides/data-model/project.html#manage-samples" target="_blank">Need help? </a></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
  if (!$data.loading && $data.studies.data) {
    _push(`<div>`);
    if ($data.studies.data.length <= 0 && _ctx.editable) {
      _push(`<div><div class="mt-4 px-12 py-8 mx-auto max-w-4xl"><div class="px-6 py-4 bg-white shadow-md rounded-lg"><div class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6"><path d="M3 6l9 4v12l-9-4V6zm14-3v2c0 1.1-2.24 2-5 2s-5-.9-5-2V3c0 1.1 2.24 2 5 2s5-.9 5-2z" class="fill-current text-gray-400"></path><polygon points="21 6 12 10 12 22 21 18" class="fill-current text-gray-600"></polygon></svg><div class="ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider"> No studies that match the search query </div></div><button type="button" class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 mt-6 focus:ring-offset-2 focus:ring-teal-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-12 w-12 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path></svg><span class="mt-2 block text-sm font-medium text-gray-900"> Empty results. <br>Click here to reset </span></button></div></div></div>`);
    } else {
      _push(`<div><div class="mt-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl"><!--[-->`);
      ssrRenderList($data.studies.data, (study) => {
        _push(`<div>`);
        _push(ssrRenderComponent(_component_study_card, { study }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div>`);
      if ($data.studies.meta && $data.studies.meta.total > $data.studies.meta.per_page) {
        _push(`<div class="block w-100 mt-10"><nav class="border-t border-gray-200 px-4 flex items-center justify-between sm:px-0"><div class="-mt-px w-0 flex-1 flex"> </div><div class="hidden md:-mt-px md:flex"><!--[-->`);
        ssrRenderList($data.studies.meta.links, (link) => {
          _push(`<div class="${ssrRenderClass([
            link.active ? "text-teal-600 border-teal-500" : "",
            "cursor-pointer border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium"
          ])}">${link.label}</div>`);
        });
        _push(`<!--]--></div><div class="-mt-px w-0 flex-1 flex justify-end">   </div></nav></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    }
    _push(`</div>`);
  } else {
    _push(`<div class="text-gray-400"><svg class="animate-spin inline -ml-1 mr-2 h-5 w-5 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading... </div>`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const StudyIndex = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  StudyIndex as default
};
