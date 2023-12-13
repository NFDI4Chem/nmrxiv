import { ssrRenderAttrs, ssrRenderClass, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
import { useSSRContext } from "vue";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {},
  props: ["doi", "color"],
  data() {
    return {
      isVisible: false
    };
  },
  methods: {
    copyDOIToClipboard(event) {
      let targetInput = event.target.parentElement.getElementsByTagName("input")[0];
      if (targetInput) {
        this.isVisible = true;
        setTimeout(() => {
          this.isVisible = false;
        }, 2500);
        this.copyToClipboard(this.doi, targetInput);
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  if ($props.doi) {
    _push(`<div><div class="inline-flex items-center cursor-pointer"><span class="inline-flex items-center rounded-l-md border-r bg-gray-800 px-2 py-1 text-sm font-medium text-white">DOI:</span><span class="${ssrRenderClass([
      $props.color ? $props.color : "bg-blue-100",
      "inline-flex items-center rounded-r-md px-2 py-1 text-sm font-medium text-gray-900"
    ])}">${ssrInterpolate($props.doi)}</span><input readonly type="hidden"${ssrRenderAttr("value", $props.doi)} class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300">`);
    if ($data.isVisible) {
      _push(`<span class="text-gray-500 text-xs ml-2">Copied to clipboard!</span>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/DOIBadge.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const DOIBadge = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  DOIBadge as D
};
