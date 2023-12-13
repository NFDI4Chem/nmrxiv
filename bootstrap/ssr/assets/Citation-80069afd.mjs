import axios from "axios";
import { mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {},
  props: ["doi", "model"],
  data() {
    return {
      formats: {
        APA: "apa",
        Harvard: "harvard-cite-them-right",
        MLA: "modern-language-association",
        Vancouver: "vancouver",
        Chicago: "chicago-fullnote-bibliography",
        IEEE: "ieee"
      },
      selectedFormat: "APA",
      citationText: null
    };
  },
  computed: {
    dataciteURL() {
      return this.$page.props.dataciteURL ? String(this.$page.props.dataciteURL) + "/dois/" : "https://api.datacite.org/dois/";
    }
  },
  watch: {
    doi(newDOI, oldDOI) {
      if (newDOI != oldDOI) {
        this.queryDataCite();
      }
    }
  },
  mounted() {
    if (this.doi) {
      this.queryDataCite();
    }
  },
  methods: {
    queryDataCite() {
      let config = {
        headers: {
          Accept: "text/x-bibliography; charset=utf-8"
        }
      };
      axios.get(
        this.dataciteURL + this.doi + "?style=" + this.formats[this.selectedFormat],
        config
      ).then(
        (response) => {
          this.citationText = response.data;
        },
        (error) => {
          console.log(error);
        }
      );
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  if ($data.citationText) {
    _push(`<div${ssrRenderAttrs(mergeProps({ class: "p-4" }, _attrs))}><div class="rounded-md bg-blue-50 p-4"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M19 10.5a8.5 8.5 0 11-17 0 8.5 8.5 0 0117 0zM8.25 9.75A.75.75 0 019 9h.253a1.75 1.75 0 011.709 2.13l-.46 2.066a.25.25 0 00.245.304H11a.75.75 0 010 1.5h-.253a1.75 1.75 0 01-1.709-2.13l.46-2.066a.25.25 0 00-.245-.304H9a.75.75 0 01-.75-.75zM10 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg></div><div class="ml-3 w-full"><p class="text-md text-blue-700"> Cite this ${ssrInterpolate($props.model)} <select class="-mt-2 -mr-2 block rounded-md border-gray-300 py-1 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm float-right"><option name="citation" value="APA"${ssrIncludeBooleanAttr(Array.isArray($data.selectedFormat) ? ssrLooseContain($data.selectedFormat, "APA") : ssrLooseEqual($data.selectedFormat, "APA")) ? " selected" : ""}>APA</option><option name="citation" value="Harvard"${ssrIncludeBooleanAttr(Array.isArray($data.selectedFormat) ? ssrLooseContain($data.selectedFormat, "Harvard") : ssrLooseEqual($data.selectedFormat, "Harvard")) ? " selected" : ""}> Harvard </option><option name="citation" value="MLA"${ssrIncludeBooleanAttr(Array.isArray($data.selectedFormat) ? ssrLooseContain($data.selectedFormat, "MLA") : ssrLooseEqual($data.selectedFormat, "MLA")) ? " selected" : ""}>MLA</option><option name="citation" value="Vancouver"${ssrIncludeBooleanAttr(Array.isArray($data.selectedFormat) ? ssrLooseContain($data.selectedFormat, "Vancouver") : ssrLooseEqual($data.selectedFormat, "Vancouver")) ? " selected" : ""}> Vancouver </option><option name="citation" value="Chicago"${ssrIncludeBooleanAttr(Array.isArray($data.selectedFormat) ? ssrLooseContain($data.selectedFormat, "Chicago") : ssrLooseEqual($data.selectedFormat, "Chicago")) ? " selected" : ""}> Chicago </option><option name="citation" value="IEEE"${ssrIncludeBooleanAttr(Array.isArray($data.selectedFormat) ? ssrLooseContain($data.selectedFormat, "IEEE") : ssrLooseEqual($data.selectedFormat, "IEEE")) ? " selected" : ""}>IEEE</option></select></p><p class="text-md font-bold mt-1 text-gray-900">${$data.citationText}</p></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Citation.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Citation = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Citation as C
};
