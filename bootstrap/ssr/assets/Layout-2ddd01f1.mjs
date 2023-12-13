import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { Link } from "@inertiajs/vue3";
import { ArrowDownTrayIcon } from "@heroicons/vue/24/solid";
import { D as DOIBadge } from "./DOIBadge-164e4f67.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, openBlock, createBlock, createCommentVNode, toDisplayString, createTextVNode, Fragment, renderList, renderSlot, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrRenderList, ssrRenderSlot } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
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
const _sfc_main = {
  components: {
    AppLayout,
    Link,
    ArrowDownTrayIcon,
    DOIBadge
  },
  props: ["study"],
  data() {
    return {
      tabs: []
    };
  },
  computed: {},
  mounted() {
  },
  methods: {}
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({
    title: $props.study.name
  }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}><div class="border-b"${_scopeId}><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2"${_scopeId}>`);
        if ($props.study && $props.study.owner) {
          _push2(`<img class="inline h-7 w-7 rounded-full"${ssrRenderAttr("src", $props.study.owner.profile_photo_url)}${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<p class="inline ml-3 text-xs font-bold text-gray-500 uppercase"${_scopeId}><a class="text-gray-900"${_scopeId}>${ssrInterpolate($props.study.owner.first_name + " " + $props.study.owner.last_name)}</a><span class="block md:inline ml-10 md:ml-0"${_scopeId}> updated on <time${_scopeId}>${ssrInterpolate(_ctx.formatDate($props.study.updated_at))}</time></span></p><div class="flex mt-2 md:inline"${_scopeId}>`);
        if ($props.study.download_url) {
          _push2(`<div class="float-left md:float-right"${_scopeId}><a class="md:ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"${ssrRenderAttr("href", $props.study.download_url)}${_scopeId}> Download </a></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="float-left md:float-right"${_scopeId}><div class="flex-shrink-0"${_scopeId}>`);
        if ($props.study.stats) {
          _push2(`<span class="relative z-0 inline-flex shadow-sm rounded-full"${_scopeId}><button type="button" class="relative inline-flex items-center px-1 py-1 rounded-l-full border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"${_scopeId}></path></svg></button><a class="-ml-px relative inline-flex items-center px-4 py-1 rounded-r-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"${_scopeId}>${ssrInterpolate($props.study.stats.likes)}</a></span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div></div></div><img class="h-32 w-full object-cover lg:h-48" src="https://images.unsplash.com/photo-1637625854255-d893202554f4?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&amp;ixlib=rb-1.2.1&amp;auto=format&amp;fit=crop&amp;w=1950&amp;q=80"${_scopeId}></div>`);
      } else {
        return [
          createVNode("div", null, [
            createVNode("div", { class: "border-b" }, [
              createVNode("div", { class: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2" }, [
                $props.study && $props.study.owner ? (openBlock(), createBlock("img", {
                  key: 0,
                  class: "inline h-7 w-7 rounded-full",
                  src: $props.study.owner.profile_photo_url
                }, null, 8, ["src"])) : createCommentVNode("", true),
                createVNode("p", { class: "inline ml-3 text-xs font-bold text-gray-500 uppercase" }, [
                  createVNode("a", { class: "text-gray-900" }, toDisplayString($props.study.owner.first_name + " " + $props.study.owner.last_name), 1),
                  createVNode("span", { class: "block md:inline ml-10 md:ml-0" }, [
                    createTextVNode(" updated on "),
                    createVNode("time", null, toDisplayString(_ctx.formatDate($props.study.updated_at)), 1)
                  ])
                ]),
                createVNode("div", { class: "flex mt-2 md:inline" }, [
                  $props.study.download_url ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "float-left md:float-right"
                  }, [
                    createVNode("a", {
                      class: "md:ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500",
                      href: $props.study.download_url
                    }, " Download ", 8, ["href"])
                  ])) : createCommentVNode("", true),
                  createVNode("div", { class: "float-left md:float-right" }, [
                    createVNode("div", { class: "flex-shrink-0" }, [
                      $props.study.stats ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "relative z-0 inline-flex shadow-sm rounded-full"
                      }, [
                        createVNode("button", {
                          type: "button",
                          class: "relative inline-flex items-center px-1 py-1 rounded-l-full border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50",
                          onClick: ($event) => _ctx.toggleUpVote()
                        }, [
                          (openBlock(), createBlock("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            class: "h-5 w-5",
                            viewBox: "0 0 20 20",
                            fill: "currentColor"
                          }, [
                            createVNode("path", {
                              "fill-rule": "evenodd",
                              d: "M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z",
                              "clip-rule": "evenodd"
                            })
                          ]))
                        ], 8, ["onClick"]),
                        createVNode("a", { class: "-ml-px relative inline-flex items-center px-4 py-1 rounded-r-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" }, toDisplayString($props.study.stats.likes), 1)
                      ])) : createCommentVNode("", true)
                    ])
                  ])
                ])
              ])
            ]),
            createVNode("img", {
              class: "h-32 w-full object-cover lg:h-48",
              src: "https://images.unsplash.com/photo-1637625854255-d893202554f4?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
            })
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<main class="flex-1 relative z-0 overflow-y-auto focus:outline-none xl:order-last"${_scopeId}><div${_scopeId}><div class="mt-6 sm:mt-2 2xl:mt-5"${_scopeId}><div class="border-b border-gray-200"${_scopeId}><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"${_scopeId}><nav class="-mb-px flex space-x-8" aria-label="Tabs"${_scopeId}><!--[-->`);
        ssrRenderList($data.tabs, (tab) => {
          _push2(ssrRenderComponent(_component_Link, {
            key: tab.name,
            href: $props.study.public_url + "?tab=" + tab.name,
            class: [
              _ctx.selectedTab == tab.name ? "border-pink-500 text-gray-900" : "",
              "cursor-pointer capitalize text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            ],
            "aria-current": "page"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`${ssrInterpolate(tab.name)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(tab.name), 1)
                ];
              }
            }),
            _: 2
          }, _parent2, _scopeId));
        });
        _push2(`<!--]--></nav></div></div></div><div class="bg-white"${_scopeId}>`);
        ssrRenderSlot(_ctx.$slots, "sample-content", {}, null, _push2, _parent2, _scopeId);
        _push2(`</div></div></main>`);
      } else {
        return [
          createVNode("main", { class: "flex-1 relative z-0 overflow-y-auto focus:outline-none xl:order-last" }, [
            createVNode("div", null, [
              createVNode("div", { class: "mt-6 sm:mt-2 2xl:mt-5" }, [
                createVNode("div", { class: "border-b border-gray-200" }, [
                  createVNode("div", { class: "max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                    createVNode("nav", {
                      class: "-mb-px flex space-x-8",
                      "aria-label": "Tabs"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList($data.tabs, (tab) => {
                        return openBlock(), createBlock(_component_Link, {
                          key: tab.name,
                          href: $props.study.public_url + "?tab=" + tab.name,
                          class: [
                            _ctx.selectedTab == tab.name ? "border-pink-500 text-gray-900" : "",
                            "cursor-pointer capitalize text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                          ],
                          "aria-current": "page"
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(tab.name), 1)
                          ]),
                          _: 2
                        }, 1032, ["href", "class"]);
                      }), 128))
                    ])
                  ])
                ])
              ]),
              createVNode("div", { class: "bg-white" }, [
                renderSlot(_ctx.$slots, "sample-content")
              ])
            ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Sample/Layout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SampleLayout = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  SampleLayout as default
};
