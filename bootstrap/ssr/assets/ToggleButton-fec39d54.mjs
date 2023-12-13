import { resolveComponent, mergeProps, withCtx, createVNode, useSSRContext } from "vue";
import { Switch } from "@headlessui/vue";
import { ssrRenderComponent, ssrRenderClass } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    Switch
  },
  props: {
    enabled: Boolean
  },
  emits: ["update:enabled"],
  computed: {
    proxyEnabled: {
      get() {
        return this.enabled;
      },
      set(val) {
        this.$emit("update:enabled", val);
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Switch = resolveComponent("Switch");
  _push(ssrRenderComponent(_component_Switch, mergeProps({
    modelValue: $options.proxyEnabled,
    "onUpdate:modelValue": ($event) => $options.proxyEnabled = $event,
    class: "flex-shrink-0 group relative rounded-full inline-flex items-center justify-center h-5 w-10 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
  }, _attrs), {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<span class="sr-only"${_scopeId}>Use setting</span><span aria-hidden="true" class="pointer-events-none absolute bg-white w-full h-full rounded-md"${_scopeId}></span><span aria-hidden="true" class="${ssrRenderClass([
          $props.enabled ? "bg-green-200" : "bg-red-100",
          "pointer-events-none absolute h-4 w-9 mx-auto rounded-full transition-colors ease-in-out duration-200"
        ])}"${_scopeId}></span><span aria-hidden="true" class="${ssrRenderClass([
          $props.enabled ? "translate-x-5" : "translate-x-0",
          "pointer-events-none absolute left-0 inline-block h-5 w-5 border border-gray-200 rounded-full bg-gray-200 shadow transform ring-0 transition-transform ease-in-out duration-200"
        ])}"${_scopeId}></span>`);
      } else {
        return [
          createVNode("span", { class: "sr-only" }, "Use setting"),
          createVNode("span", {
            "aria-hidden": "true",
            class: "pointer-events-none absolute bg-white w-full h-full rounded-md"
          }),
          createVNode("span", {
            "aria-hidden": "true",
            class: [
              $props.enabled ? "bg-green-200" : "bg-red-100",
              "pointer-events-none absolute h-4 w-9 mx-auto rounded-full transition-colors ease-in-out duration-200"
            ]
          }, null, 2),
          createVNode("span", {
            "aria-hidden": "true",
            class: [
              $props.enabled ? "translate-x-5" : "translate-x-0",
              "pointer-events-none absolute left-0 inline-block h-5 w-5 border border-gray-200 rounded-full bg-gray-200 shadow transform ring-0 transition-transform ease-in-out duration-200"
            ]
          }, null, 2)
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/ToggleButton.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const ToggleButton = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  ToggleButton as T
};
