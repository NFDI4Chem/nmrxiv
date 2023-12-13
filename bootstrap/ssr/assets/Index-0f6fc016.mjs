import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { S as SearchFilter } from "./SearchFilter-01feb0b0.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { B as BreadCrumbs } from "./BreadCrumbs-a9158bdf.mjs";
import { Link } from "@inertiajs/vue3";
import { resolveComponent, mergeProps, withCtx, createVNode, openBlock, createBlock, createTextVNode, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
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
import "@sipec/vue3-tags-input";
import "vue3-slider";
import "openchemlib/full.js";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./InputError-6ae0b65c.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
const _sfc_main = {
  components: {
    AppLayout,
    SearchFilter,
    JetDialogModal,
    JetSecondaryButton,
    JetButton,
    Link,
    BreadCrumbs
  },
  props: {
    users: Array
  },
  data() {
    return {
      pages: [
        { name: "Console", route: "console", current: false },
        { name: "Curation", route: "console.spectra", current: true }
      ]
    };
  },
  watch: {},
  methods: {}
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_bread_crumbs = resolveComponent("bread-crumbs");
  const _component_Link = resolveComponent("Link");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Curation" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_bread_crumbs, { pages: $data.pages }, null, _parent2, _scopeId));
        _push2(`<div class="mt-2 md:flex md:items-center md:justify-between"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"${_scopeId}> Curate Spectra Meta-data </div></div></div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode(_component_bread_crumbs, { pages: $data.pages }, null, 8, ["pages"]),
                  createVNode("div", { class: "mt-2 md:flex md:items-center md:justify-between" }, [
                    createVNode("div", { class: "flex-1 min-w-0" }, [
                      createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3" }, " Curate Spectra Meta-data ")
                    ])
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
        _push2(`<div class="p-10"${_scopeId}><div class="border divide-y divide-gray-200 overflow-hidden rounded-lg bg-gray-200 shadow sm:grid sm:grid-cols-2 sm:gap-px sm:divide-y-0"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, { href: "/admin/snapshots" }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<div class="rounded-tl-lg rounded-tr-lg sm:rounded-tr-none group relative bg-white p-6"${_scopeId2}><div${_scopeId2}><span class="inline-flex rounded-lg p-3 bg-teal-50 text-teal-700 ring-4 ring-white"${_scopeId2}><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"${_scopeId2}><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"${_scopeId2}></path></svg></span></div><div class="mt-8"${_scopeId2}><h3 class="text-base font-semibold leading-6 text-gray-900"${_scopeId2}><a class="focus:outline-none"${_scopeId2}><span class="absolute inset-0" aria-hidden="true"${_scopeId2}></span> Import spectra snapshots </a></h3><p class="mt-2 text-sm text-gray-500"${_scopeId2}> Imports missing spectra snapshots for spectra card previews </p></div><span class="pointer-events-none absolute right-6 top-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true"${_scopeId2}><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"${_scopeId2}><path d="M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z"${_scopeId2}></path></svg></span></div>`);
            } else {
              return [
                createVNode("div", { class: "rounded-tl-lg rounded-tr-lg sm:rounded-tr-none group relative bg-white p-6" }, [
                  createVNode("div", null, [
                    createVNode("span", { class: "inline-flex rounded-lg p-3 bg-teal-50 text-teal-700 ring-4 ring-white" }, [
                      (openBlock(), createBlock("svg", {
                        xmlns: "http://www.w3.org/2000/svg",
                        fill: "none",
                        viewBox: "0 0 24 24",
                        "stroke-width": "1.5",
                        stroke: "currentColor",
                        class: "w-6 h-6"
                      }, [
                        createVNode("path", {
                          "stroke-linecap": "round",
                          "stroke-linejoin": "round",
                          d: "M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                        })
                      ]))
                    ])
                  ]),
                  createVNode("div", { class: "mt-8" }, [
                    createVNode("h3", { class: "text-base font-semibold leading-6 text-gray-900" }, [
                      createVNode("a", { class: "focus:outline-none" }, [
                        createVNode("span", {
                          class: "absolute inset-0",
                          "aria-hidden": "true"
                        }),
                        createTextVNode(" Import spectra snapshots ")
                      ])
                    ]),
                    createVNode("p", { class: "mt-2 text-sm text-gray-500" }, " Imports missing spectra snapshots for spectra card previews ")
                  ]),
                  createVNode("span", {
                    class: "pointer-events-none absolute right-6 top-6 text-gray-300 group-hover:text-gray-400",
                    "aria-hidden": "true"
                  }, [
                    (openBlock(), createBlock("svg", {
                      class: "h-6 w-6",
                      fill: "currentColor",
                      viewBox: "0 0 24 24"
                    }, [
                      createVNode("path", { d: "M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" })
                    ]))
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></div>`);
      } else {
        return [
          createVNode("div", { class: "p-10" }, [
            createVNode("div", { class: "border divide-y divide-gray-200 overflow-hidden rounded-lg bg-gray-200 shadow sm:grid sm:grid-cols-2 sm:gap-px sm:divide-y-0" }, [
              createVNode(_component_Link, { href: "/admin/snapshots" }, {
                default: withCtx(() => [
                  createVNode("div", { class: "rounded-tl-lg rounded-tr-lg sm:rounded-tr-none group relative bg-white p-6" }, [
                    createVNode("div", null, [
                      createVNode("span", { class: "inline-flex rounded-lg p-3 bg-teal-50 text-teal-700 ring-4 ring-white" }, [
                        (openBlock(), createBlock("svg", {
                          xmlns: "http://www.w3.org/2000/svg",
                          fill: "none",
                          viewBox: "0 0 24 24",
                          "stroke-width": "1.5",
                          stroke: "currentColor",
                          class: "w-6 h-6"
                        }, [
                          createVNode("path", {
                            "stroke-linecap": "round",
                            "stroke-linejoin": "round",
                            d: "M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                          })
                        ]))
                      ])
                    ]),
                    createVNode("div", { class: "mt-8" }, [
                      createVNode("h3", { class: "text-base font-semibold leading-6 text-gray-900" }, [
                        createVNode("a", { class: "focus:outline-none" }, [
                          createVNode("span", {
                            class: "absolute inset-0",
                            "aria-hidden": "true"
                          }),
                          createTextVNode(" Import spectra snapshots ")
                        ])
                      ]),
                      createVNode("p", { class: "mt-2 text-sm text-gray-500" }, " Imports missing spectra snapshots for spectra card previews ")
                    ]),
                    createVNode("span", {
                      class: "pointer-events-none absolute right-6 top-6 text-gray-300 group-hover:text-gray-400",
                      "aria-hidden": "true"
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "h-6 w-6",
                        fill: "currentColor",
                        viewBox: "0 0 24 24"
                      }, [
                        createVNode("path", { d: "M20 4h1a1 1 0 00-1-1v1zm-1 12a1 1 0 102 0h-2zM8 3a1 1 0 000 2V3zM3.293 19.293a1 1 0 101.414 1.414l-1.414-1.414zM19 4v12h2V4h-2zm1-1H8v2h12V3zm-.707.293l-16 16 1.414 1.414 16-16-1.414-1.414z" })
                      ]))
                    ])
                  ])
                ]),
                _: 1
              })
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Console/Spectra/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
