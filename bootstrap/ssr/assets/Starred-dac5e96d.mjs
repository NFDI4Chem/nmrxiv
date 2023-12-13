import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import TeamProjects from "./Index-a64ae412.mjs";
import { Link } from "@inertiajs/vue3";
import { S as StudyCard } from "./StudyCard-ce624709.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, openBlock, createBlock, Fragment, renderList, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderList } from "vue/server-renderer";
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
import "./Depictor2D-b60253ae.mjs";
const _sfc_main = {
  components: {
    AppLayout,
    TeamProjects,
    StudyCard,
    Link
  },
  props: ["user", "team", "projects", "studies"]
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_team_projects = resolveComponent("team-projects");
  const _component_Link = resolveComponent("Link");
  const _component_study_card = resolveComponent("study-card");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Dashboard" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}><div class="flex flex-nowrap justify-between"${_scopeId}><div class="lg:flex lg:items-center lg:justify-between"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><nav class="flex" aria-label="Breadcrumb"${_scopeId}><ol role="list" class="flex items-center space-x-4"${_scopeId}><li${_scopeId}><div class="flex"${_scopeId}><a class="text-sm font-medium text-gray-500 hover:text-gray-700"${_scopeId}>Dashboard</a></div></li><li${_scopeId}><div class="flex items-center"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="flex-shrink-0 h-5 w-5 text-gray-400"${_scopeId}><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg><a class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"${_scopeId}>Starred</a></div></li></ol></nav><h2 class="mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl"${_scopeId}> Starred </h2></div></div></div></div></div></div></div>`);
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
                                createVNode("a", { class: "ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" }, "Starred")
                              ])
                            ])
                          ])
                        ]),
                        createVNode("h2", { class: "mt-2 text-2xl font-bold break-words leading-7 text-gray-900 sm:text-3xl" }, " Starred ")
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
        _push2(`<div class="px-12 py-8 mx-auto max-w-4xl"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_team_projects, {
          mode: "listing",
          projects: $props.projects
        }, {
          emptyText: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<div class="text-center py-12"${_scopeId2}><svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId2}><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"${_scopeId2}></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId2}> No Starred Projects. </h3><p class="mt-1 text-sm text-gray-500"${_scopeId2}> Add stars to projects or datasets to find it easily later. </p></div>`);
            } else {
              return [
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
                      d: "M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                    })
                  ])),
                  createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No Starred Projects. "),
                  createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Add stars to projects or datasets to find it easily later. ")
                ])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
        if ($props.studies.length > 0) {
          _push2(`<div class="px-12 border-t py-8 mx-auto max-w-4xl"${_scopeId}><div${_scopeId}><h2 class="text-lg"${_scopeId}>Studies</h2></div><div class="flex items-baseline justify-between"${_scopeId}><div class="py-8 mx-auto max-w-4xl"${_scopeId}><div class="mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-3 lg:max-w-7xl"${_scopeId}><!--[-->`);
          ssrRenderList($props.studies, (study) => {
            _push2(`<div${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Link, {
              href: _ctx.route("dashboard.studies", [study.id])
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(_component_study_card, { study }, null, _parent3, _scopeId2));
                } else {
                  return [
                    createVNode(_component_study_card, { study }, null, 8, ["study"])
                  ];
                }
              }),
              _: 2
            }, _parent2, _scopeId));
            _push2(`</div>`);
          });
          _push2(`<!--]--></div></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          createVNode("div", { class: "px-12 py-8 mx-auto max-w-4xl" }, [
            createVNode(_component_team_projects, {
              mode: "listing",
              projects: $props.projects
            }, {
              emptyText: withCtx(() => [
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
                      d: "M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                    })
                  ])),
                  createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No Starred Projects. "),
                  createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Add stars to projects or datasets to find it easily later. ")
                ])
              ]),
              _: 1
            }, 8, ["projects"])
          ]),
          $props.studies.length > 0 ? (openBlock(), createBlock("div", {
            key: 0,
            class: "px-12 border-t py-8 mx-auto max-w-4xl"
          }, [
            createVNode("div", null, [
              createVNode("h2", { class: "text-lg" }, "Studies")
            ]),
            createVNode("div", { class: "flex items-baseline justify-between" }, [
              createVNode("div", { class: "py-8 mx-auto max-w-4xl" }, [
                createVNode("div", { class: "mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-3 lg:max-w-7xl" }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($props.studies, (study) => {
                    return openBlock(), createBlock("div", {
                      key: study.uuid
                    }, [
                      createVNode(_component_Link, {
                        href: _ctx.route("dashboard.studies", [study.id])
                      }, {
                        default: withCtx(() => [
                          createVNode(_component_study_card, { study }, null, 8, ["study"])
                        ]),
                        _: 2
                      }, 1032, ["href"])
                    ]);
                  }), 128))
                ])
              ])
            ])
          ])) : createCommentVNode("", true)
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Starred.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Starred = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Starred as default
};
