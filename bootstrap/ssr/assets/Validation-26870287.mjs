import { A as AppLayout, V as Validation$1 } from "./AppLayout-b93d1c11.mjs";
import { Link } from "@inertiajs/vue3";
import { resolveComponent, mergeProps, withCtx, createTextVNode, toDisplayString, createVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
import "@vueuse/core";
import "@heroicons/vue/24/outline";
import "@headlessui/vue";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "./Button-8d6976fd.mjs";
import "@sipec/vue3-tags-input";
import "vue3-slider";
import "openchemlib/full.js";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./InputError-6ae0b65c.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
const _sfc_main = {
  components: {
    Link,
    AppLayout,
    Validation: Validation$1
  },
  props: ["project", "validation"]
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  const _component_Validation = resolveComponent("Validation", true);
  _push(ssrRenderComponent(_component_app_layout, mergeProps({
    title: $props.project.name
  }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("dashboard.projects", $props.project.id)
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($props.project.name)}`);
            } else {
              return [
                createTextVNode(toDisplayString($props.project.name), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(` / Validation </div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest" }, [
                    createVNode(_component_Link, {
                      href: _ctx.route("dashboard.projects", $props.project.id)
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString($props.project.name), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createTextVNode(" / Validation ")
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="py-8 px-10"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Validation, {
          project: $props.project,
          validation: $props.validation.report
        }, null, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "py-8 px-10" }, [
            createVNode(_component_Validation, {
              project: $props.project,
              validation: $props.validation.report
            }, null, 8, ["project", "validation"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Project/Validation.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Validation = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Validation as default
};
