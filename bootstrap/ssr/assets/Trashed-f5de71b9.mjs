import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import TeamProjects from "./Index-a64ae412.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, openBlock, createBlock, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
import "@inertiajs/vue3";
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
    TeamProjects
  },
  props: ["user", "projects"]
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_team_projects = resolveComponent("team-projects");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Dashboard" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}><div class="flex flex-nowrap justify-between"${_scopeId}><div class="lg:flex lg:items-center lg:justify-between"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><nav class="flex" aria-label="Breadcrumb"${_scopeId}><ol role="list" class="flex items-center space-x-4"${_scopeId}><li${_scopeId}><div class="flex"${_scopeId}><a class="text-sm font-medium text-gray-500 hover:text-gray-700"${_scopeId}>Dashboard</a></div></li><li${_scopeId}><div class="flex items-center"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="flex-shrink-0 h-5 w-5 text-gray-400"${_scopeId}><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg><a class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"${_scopeId}>Trash</a></div></li></ol></nav><h2 class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"${_scopeId}> Trash </h2></div></div></div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode("div", { class: "flex flex-nowrap justify-between" }, [
                    createVNode("div", { class: "lg:flex lg:items-center lg:justify-between" }, [
                      createVNode("div", { class: "flex-1 min-w-0" }, [
                        createVNode("nav", {
                          class: "flex",
                          "aria-label": "Breadcrumb"
                        }, [
                          createVNode("ol", {
                            role: "list",
                            class: "flex items-center space-x-4"
                          }, [
                            createVNode("li", null, [
                              createVNode("div", { class: "flex" }, [
                                createVNode("a", { class: "text-sm font-medium text-gray-500 hover:text-gray-700" }, "Dashboard")
                              ])
                            ]),
                            createVNode("li", null, [
                              createVNode("div", { class: "flex items-center" }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  viewBox: "0 0 20 20",
                                  fill: "currentColor",
                                  "aria-hidden": "true",
                                  class: "flex-shrink-0 h-5 w-5 text-gray-400"
                                }, [
                                  createVNode("path", {
                                    "fill-rule": "evenodd",
                                    d: "M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z",
                                    "clip-rule": "evenodd"
                                  })
                                ])),
                                createVNode("a", { class: "ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" }, "Trash")
                              ])
                            ])
                          ])
                        ]),
                        createVNode("h2", { class: "mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl" }, " Trash ")
                      ])
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
        if ($props.projects.length > 0) {
          _push2(`<div class="px-12 py-8 mx-auto max-w-4xl"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_team_projects, {
            mode: "listing",
            projects: $props.projects
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<div${_scopeId}><div class="text-center py-12"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"${_scopeId}></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No items in the trash. </h3><p class="mt-1 text-sm text-gray-500"${_scopeId}> You dont have any Projects, Studies or Datasets in your trash. </p></div></div>`);
        }
      } else {
        return [
          $props.projects.length > 0 ? (openBlock(), createBlock("div", {
            key: 0,
            class: "px-12 py-8 mx-auto max-w-4xl"
          }, [
            createVNode(_component_team_projects, {
              mode: "listing",
              projects: $props.projects
            }, null, 8, ["projects"])
          ])) : (openBlock(), createBlock("div", { key: 1 }, [
            createVNode("div", { class: "text-center py-12" }, [
              (openBlock(), createBlock("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                class: "mx-auto h-24 w-24 text-gray-400",
                fill: "none",
                viewBox: "0 0 24 24",
                stroke: "currentColor",
                "stroke-width": "2"
              }, [
                createVNode("path", {
                  "stroke-linecap": "round",
                  "stroke-linejoin": "round",
                  d: "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                })
              ])),
              createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No items in the trash. "),
              createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " You dont have any Projects, Studies or Datasets in your trash. ")
            ])
          ]))
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Trashed.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Trashed = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Trashed as default
};
