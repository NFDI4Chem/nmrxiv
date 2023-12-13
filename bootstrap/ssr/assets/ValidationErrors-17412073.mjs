import { mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  computed: {
    errors() {
      return this.$page.props.errors;
    },
    hasErrors() {
      return Object.keys(this.errors).length > 0;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  if ($options.hasErrors) {
    _push(`<div${ssrRenderAttrs(mergeProps({
      class: "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative",
      role: "alert"
    }, _attrs))}><strong class="font-medium pb-0">Something went wrong.</strong><span class="block sm:inline"><ul class="mt-3 list-disc list-inside text-sm text-red-600"><!--[-->`);
    ssrRenderList($options.errors, (error, key) => {
      _push(`<li>${ssrInterpolate(error)}</li>`);
    });
    _push(`<!--]--></ul></span></div>`);
  } else {
    _push(`<!---->`);
  }
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/ValidationErrors.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const JetValidationErrors = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  JetValidationErrors as J
};
