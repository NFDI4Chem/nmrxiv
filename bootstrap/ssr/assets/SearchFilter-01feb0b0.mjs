import { D as Dropdown } from "./AnnouncementBanner-3ebe3621.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, renderSlot, openBlock, createBlock, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrRenderSlot, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    Dropdown
  },
  props: {
    modelValue: String,
    maxWidth: {
      type: Number,
      default: 300
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_dropdown = resolveComponent("dropdown");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex items-center" }, _attrs))}><div class="flex w-full bg-white shadow rounded">`);
  _push(ssrRenderComponent(_component_dropdown, {
    "auto-close": false,
    class: "px-4 md:px-6 rounded-l border-r hover:bg-gray-100 focus:border-white focus:shadow-outline focus:z-10",
    placement: "bottom-start"
  }, {
    dropdown: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="mt-2 px-4 py-6 w-screen shadow-xl bg-white rounded" style="${ssrRenderStyle({ maxWidth: `${$props.maxWidth}px` })}"${_scopeId}>`);
        ssrRenderSlot(_ctx.$slots, "default", {}, null, _push2, _parent2, _scopeId);
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", {
            class: "mt-2 px-4 py-6 w-screen shadow-xl bg-white rounded",
            style: { maxWidth: `${$props.maxWidth}px` }
          }, [
            renderSlot(_ctx.$slots, "default")
          ], 4)
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="flex items-baseline"${_scopeId}><span class="text-gray-700 hidden md:inline"${_scopeId}>Filter</span><svg class="w-2 h-2 fill-gray-700 md:ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 961.243 599.998"${_scopeId}><path d="M239.998 239.999L0 0h961.243L721.246 240c-131.999 132-240.28 240-240.624 239.999-.345-.001-108.625-108.001-240.624-240z"${_scopeId}></path></svg></div>`);
      } else {
        return [
          createVNode("div", { class: "flex items-baseline" }, [
            createVNode("span", { class: "text-gray-700 hidden md:inline" }, "Filter"),
            (openBlock(), createBlock("svg", {
              class: "w-2 h-2 fill-gray-700 md:ml-2",
              xmlns: "http://www.w3.org/2000/svg",
              viewBox: "0 0 961.243 599.998"
            }, [
              createVNode("path", { d: "M239.998 239.999L0 0h961.243L721.246 240c-131.999 132-240.28 240-240.624 239.999-.345-.001-108.625-108.001-240.624-240z" })
            ]))
          ])
        ];
      }
    }),
    _: 3
  }, _parent));
  _push(`<input class="relative w-full border-0 px-6 py-3 rounded-r focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Search…"${ssrRenderAttr("value", $props.modelValue)}></div><button class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500" type="button"> Reset </button></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SearchFilter.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SearchFilter = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  SearchFilter as S
};
