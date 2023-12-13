import { M as Modal } from "./Modal-b2fb4cf2.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, renderSlot, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderSlot } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    Modal
  },
  props: {
    show: {
      default: false
    },
    maxWidth: {
      default: "2xl"
    },
    closeable: {
      default: true
    }
  },
  emits: ["close"],
  methods: {
    close() {
      this.$emit("close");
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_modal = resolveComponent("modal");
  _push(ssrRenderComponent(_component_modal, mergeProps({
    show: $props.show,
    "max-width": $props.maxWidth,
    closeable: $props.closeable,
    onClose: $options.close
  }, _attrs), {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="px-6 py-4"${_scopeId}><div class="text-lg"${_scopeId}>`);
        ssrRenderSlot(_ctx.$slots, "title", {}, null, _push2, _parent2, _scopeId);
        _push2(`</div><div class="mt-4"${_scopeId}>`);
        ssrRenderSlot(_ctx.$slots, "content", {}, null, _push2, _parent2, _scopeId);
        _push2(`</div></div><div class="px-6 py-4 bg-gray-100 rounded-b-lg text-right"${_scopeId}>`);
        ssrRenderSlot(_ctx.$slots, "footer", {}, null, _push2, _parent2, _scopeId);
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "px-6 py-4" }, [
            createVNode("div", { class: "text-lg" }, [
              renderSlot(_ctx.$slots, "title")
            ]),
            createVNode("div", { class: "mt-4" }, [
              renderSlot(_ctx.$slots, "content")
            ])
          ]),
          createVNode("div", { class: "px-6 py-4 bg-gray-100 rounded-b-lg text-right" }, [
            renderSlot(_ctx.$slots, "footer")
          ])
        ];
      }
    }),
    _: 3
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/DialogModal.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const JetDialogModal = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  JetDialogModal as J
};
