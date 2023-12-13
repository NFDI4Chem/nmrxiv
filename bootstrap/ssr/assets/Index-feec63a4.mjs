import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { Link } from "@inertiajs/vue3";
import { I as Icon } from "./Icon-01d9d65b.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, createTextVNode, toDisplayString, useSSRContext, openBlock, createBlock } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { EllipsisVerticalIcon, RectangleStackIcon } from "@heroicons/vue/24/solid";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
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
const _sfc_main$1 = {
  components: {
    Link,
    AppLayout,
    Icon
  },
  props: {
    heading: String,
    description: String,
    name: String,
    path: String
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_icon = resolveComponent("icon");
  const _component_Link = resolveComponent("Link");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500" }, _attrs))}><div><span class="rounded-lg inline-flex p-3 bg-sky-50 text-sky-700 ring-4 ring-white">`);
  if ($props.name === "users") {
    _push(ssrRenderComponent(_component_icon, { name: "users" }, null, _parent));
  } else {
    _push(`<!---->`);
  }
  if ($props.name === "announcements") {
    _push(ssrRenderComponent(_component_icon, { name: "announcements" }, null, _parent));
  } else {
    _push(`<!---->`);
  }
  if ($props.name === "curation") {
    _push(ssrRenderComponent(_component_icon, { name: "spectra" }, null, _parent));
  } else {
    _push(`<!---->`);
  }
  _push(`</span></div><div class="mt-8"><h3 class="text-lg font-medium">`);
  _push(ssrRenderComponent(_component_Link, {
    href: _ctx.route($props.path),
    class: "focus:outline-none"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<span class="absolute inset-0" aria-hidden="true"${_scopeId}></span> ${ssrInterpolate($props.heading)}`);
      } else {
        return [
          createVNode("span", {
            class: "absolute inset-0",
            "aria-hidden": "true"
          }),
          createTextVNode(" " + toDisplayString($props.heading), 1)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</h3><p class="mt-2 text-sm text-gray-500">${ssrInterpolate($props.description)}</p></div><span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true"><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"></path></svg></span></div>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Blocks.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Blocks = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    Link,
    AppLayout,
    Blocks,
    EllipsisVerticalIcon,
    RectangleStackIcon
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_blocks = resolveComponent("blocks");
  const _component_EllipsisVerticalIcon = resolveComponent("EllipsisVerticalIcon");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Console" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"${_scopeId}> Console </div></div><div${_scopeId}> </div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest" }, " Console ")
                ]),
                createVNode("div", null, " ")
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="py-12 px-10"${_scopeId}><div${_scopeId}><div class="rounded-lg bg-gray-200 overflow-hidden shadow divide-y divide-gray-200 sm:divide-y-0 sm:grid sm:grid-cols-2 sm:gap-px"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_blocks, {
          heading: "Users & Profiles",
          description: "Allows managing users including edit/view their permissions.",
          name: "users",
          path: "console.users"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_blocks, {
          heading: "Announcements",
          description: "Manage announcements.",
          name: "announcements",
          path: "console.announcements"
        }, null, _parent2, _scopeId));
        _push2(`<div class="relative group bg-white p-6"${_scopeId}><div${_scopeId}><span class="rounded-lg inline-flex p-3 bg-sky-50 text-sky-700 ring-4 ring-white"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_EllipsisVerticalIcon, {
          class: "h-5 w-5",
          "aria-hidden": "true"
        }, null, _parent2, _scopeId));
        _push2(`</span></div><div class="mt-8"${_scopeId}><h3 class="text-lg font-medium"${_scopeId}><a target="_blank" href="/horizon" class="focus:outline-none"${_scopeId}><span class="absolute inset-0" aria-hidden="true"${_scopeId}></span> Queues </a></h3><p class="mt-2 text-sm text-gray-500"${_scopeId}> Monitor queues and jobs status </p></div><span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true"${_scopeId}><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"${_scopeId}></path></svg></span></div>`);
        _push2(ssrRenderComponent(_component_blocks, {
          heading: "Curation",
          description: "Manage spectra meta-data.",
          name: "curation",
          path: "console.spectra"
        }, null, _parent2, _scopeId));
        _push2(`</div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "py-12 px-10" }, [
            createVNode("div", null, [
              createVNode("div", { class: "rounded-lg bg-gray-200 overflow-hidden shadow divide-y divide-gray-200 sm:divide-y-0 sm:grid sm:grid-cols-2 sm:gap-px" }, [
                createVNode(_component_blocks, {
                  heading: "Users & Profiles",
                  description: "Allows managing users including edit/view their permissions.",
                  name: "users",
                  path: "console.users"
                }),
                createVNode(_component_blocks, {
                  heading: "Announcements",
                  description: "Manage announcements.",
                  name: "announcements",
                  path: "console.announcements"
                }),
                createVNode("div", { class: "relative group bg-white p-6" }, [
                  createVNode("div", null, [
                    createVNode("span", { class: "rounded-lg inline-flex p-3 bg-sky-50 text-sky-700 ring-4 ring-white" }, [
                      createVNode(_component_EllipsisVerticalIcon, {
                        class: "h-5 w-5",
                        "aria-hidden": "true"
                      })
                    ])
                  ]),
                  createVNode("div", { class: "mt-8" }, [
                    createVNode("h3", { class: "text-lg font-medium" }, [
                      createVNode("a", {
                        target: "_blank",
                        href: "/horizon",
                        class: "focus:outline-none"
                      }, [
                        createVNode("span", {
                          class: "absolute inset-0",
                          "aria-hidden": "true"
                        }),
                        createTextVNode(" Queues ")
                      ])
                    ]),
                    createVNode("p", { class: "mt-2 text-sm text-gray-500" }, " Monitor queues and jobs status ")
                  ]),
                  createVNode("span", {
                    class: "pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400",
                    "aria-hidden": "true"
                  }, [
                    (openBlock(), createBlock("svg", {
                      class: "h-6 w-6",
                      xmlns: "http://www.w3.org/2000/svg",
                      fill: "currentColor",
                      viewBox: "0 0 24 24"
                    }, [
                      createVNode("path", { d: "M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" })
                    ]))
                  ])
                ]),
                createVNode(_component_blocks, {
                  heading: "Curation",
                  description: "Manage spectra meta-data.",
                  name: "curation",
                  path: "console.spectra"
                })
              ])
            ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Console/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
