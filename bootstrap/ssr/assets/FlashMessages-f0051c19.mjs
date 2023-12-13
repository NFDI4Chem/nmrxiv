import { CheckCircleIcon, ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import { XMarkIcon } from "@heroicons/vue/24/solid";
import { resolveComponent, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    CheckCircleIcon,
    XMarkIcon,
    ExclamationTriangleIcon
  },
  data() {
    return {
      show: true,
      timeout: null
    };
  },
  watch: {
    "$page.props.flash": {
      deep: true,
      handler() {
        this.show = true;
        this.timeout = setTimeout(() => this.show = false, 5e3);
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_CheckCircleIcon = resolveComponent("CheckCircleIcon");
  const _component_XMarkIcon = resolveComponent("XMarkIcon");
  const _component_ExclamationTriangleIcon = resolveComponent("ExclamationTriangleIcon");
  _push(`<div${ssrRenderAttrs(mergeProps({
    "aria-live": "assertive",
    class: "fixed inset-0 flex items-end px-4 py-6 z-10 pointer-events-none sm:p-6 sm:items-start"
  }, _attrs))}>`);
  if (_ctx.$page.props.flash.success && $data.show) {
    _push(`<div class="w-full flex flex-col items-center space-y-4 sm:items-end"><div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"><div class="p-4"><div class="flex items-start"><div class="flex-shrink-0">`);
    _push(ssrRenderComponent(_component_CheckCircleIcon, {
      class: "h-6 w-6 text-green-400",
      "aria-hidden": "true"
    }, null, _parent));
    _push(`</div><div class="ml-3 w-0 flex-1 pt-0.5"><p class="text-sm font-medium text-gray-900">${ssrInterpolate(_ctx.$page.props.flash.success)}</p></div><div class="ml-4 flex-shrink-0 flex"><button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"><span class="sr-only">Close</span>`);
    _push(ssrRenderComponent(_component_XMarkIcon, {
      class: "h-5 w-5",
      "aria-hidden": "true"
    }, null, _parent));
    _push(`</button></div></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  if (_ctx.$page.props.flash.error && $data.show) {
    _push(`<div class="w-full flex flex-col items-center space-y-4 sm:items-end"><div class="max-w-sm w-full bg-red-100 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"><div class="p-4"><div class="flex items-start"><div class="flex-shrink-0">`);
    _push(ssrRenderComponent(_component_ExclamationTriangleIcon, {
      class: "h-6 w-6 text-red-400",
      "aria-hidden": "true"
    }, null, _parent));
    _push(`</div><div class="ml-3 w-0 flex-1 pt-0.5"><p class="text-sm font-medium text-gray-900">${ssrInterpolate(_ctx.$page.props.flash.error)}</p></div><div class="ml-4 flex-shrink-0 flex"><button class="bg-red-100 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"><span class="sr-only">Close</span>`);
    _push(ssrRenderComponent(_component_XMarkIcon, {
      class: "h-5 w-5",
      "aria-hidden": "true"
    }, null, _parent));
    _push(`</button></div></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/FlashMessages.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const FlashMessages = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  FlashMessages as F
};
