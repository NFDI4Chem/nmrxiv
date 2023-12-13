import { Link } from "@inertiajs/vue3";
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/24/solid";
import { resolveComponent, mergeProps, withCtx, createTextVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    ChevronLeftIcon,
    ChevronRightIcon,
    Link
  },
  props: ["pages"],
  setup() {
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_ChevronRightIcon = resolveComponent("ChevronRightIcon");
  const _component_Link = resolveComponent("Link");
  _push(`<nav${ssrRenderAttrs(mergeProps({
    class: "hidden sm:flex",
    "aria-label": "Breadcrumb"
  }, _attrs))}><ol role="list" class="flex items-center space-x-4"><!--[-->`);
  ssrRenderList($props.pages, (page, index) => {
    _push(`<li><div class="flex items-center">`);
    if (index > 0) {
      _push(ssrRenderComponent(_component_ChevronRightIcon, {
        class: "flex-shrink-0 h-5 w-5 text-gray-400 mr-4",
        "aria-hidden": "true"
      }, null, _parent));
    } else {
      _push(`<!---->`);
    }
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route(page.route, page.id),
      class: "text-sm font-medium text-gray-500 hover:text-gray-700",
      "aria-current": page.current ? "page" : void 0
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate(page.name)}`);
        } else {
          return [
            createTextVNode(toDisplayString(page.name), 1)
          ];
        }
      }),
      _: 2
    }, _parent));
    _push(`</div></li>`);
  });
  _push(`<!--]--></ol></nav>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/BreadCrumbs.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const BreadCrumbs = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  BreadCrumbs as B
};
