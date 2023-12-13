import { A as AppLayout, C as Create } from "./AppLayout-b93d1c11.mjs";
import TeamProjects from "./Index-a64ae412.mjs";
import { O as Onboarding } from "./Onboarding-31fdf998.mjs";
import { useMagicKeys } from "@vueuse/core";
import { computed, getCurrentInstance, watchEffect, resolveComponent, mergeProps, withCtx, createTextVNode, createVNode, openBlock, createBlock, toDisplayString, Fragment, renderList, createCommentVNode, useSSRContext } from "vue";
import { Link } from "@inertiajs/vue3";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
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
import "axios";
const { meta, u } = useMagicKeys();
const _sfc_main = {
  components: {
    AppLayout,
    computed,
    TeamProjects,
    Create,
    Onboarding,
    Link
  },
  props: ["user", "team", "projects", "teamRole", "filters"],
  setup() {
    const app = getCurrentInstance();
    const openDatasetCreateDialog = (data) => {
      app.appContext.config.globalProperties.emitter.emit(
        "openDatasetCreateDialog",
        data
      );
    };
    watchEffect(() => {
      if (meta.value && u.value) {
        openDatasetCreateDialog({
          draft_id: null
        });
      }
    });
    return {
      openDatasetCreateDialog
    };
  },
  computed: {
    mailFromAddress() {
      return String(this.$page.props.mailFromAddress);
    },
    mailTo() {
      return "mailto:" + String(this.$page.props.mailFromAddress);
    }
  },
  mounted() {
    if (this.filters.action == "submission") {
      this.emitter.emit("openDatasetCreateDialog", {
        draft_id: this.filters.draft_id
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  const _component_team_projects = resolveComponent("team-projects");
  const _component_create = resolveComponent("create");
  const _component_onboarding = resolveComponent("onboarding");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Dashboard" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"${_scopeId}>`);
        if ($props.team.personal_team) {
          _push2(`<div${_scopeId}>Your</div>`);
        } else {
          _push2(`<div${_scopeId}>${ssrInterpolate($props.user.current_team.name)}</div>`);
        }
        _push2(`  Dashboard </div>`);
        if ($props.team.users) {
          _push2(`<div class="flex mt-3 flex-row-reverse justify-end"${_scopeId}><!--[-->`);
          ssrRenderList($props.team.users, (user) => {
            _push2(`<img class="w-8 h-8 -mr-2 rounded-full border-2 border-white"${ssrRenderAttr("src", user.profile_photo_url)}${ssrRenderAttr("alt", user.name)}${_scopeId}>`);
          });
          _push2(`<!--]--><img class="w-8 h-8 -mr-2 rounded-full border-2 border-white"${ssrRenderAttr("src", $props.team.owner.profile_photo_url)}${ssrRenderAttr("alt", $props.team.owner.name)}${_scopeId}></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
        if (!$props.team.personal_team) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Link, {
            href: "/teams/" + $props.user.current_team.id,
            class: "text-sm text-gray-800 font-bold"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Team Settings `);
              } else {
                return [
                  createTextVNode(" Team Settings ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest" }, [
                    $props.team.personal_team ? (openBlock(), createBlock("div", { key: 0 }, "Your")) : (openBlock(), createBlock("div", { key: 1 }, toDisplayString($props.user.current_team.name), 1)),
                    createTextVNode("  Dashboard ")
                  ]),
                  $props.team.users ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "flex mt-3 flex-row-reverse justify-end"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList($props.team.users, (user) => {
                      return openBlock(), createBlock("img", {
                        key: user.id,
                        class: "w-8 h-8 -mr-2 rounded-full border-2 border-white",
                        src: user.profile_photo_url,
                        alt: user.name
                      }, null, 8, ["src", "alt"]);
                    }), 128)),
                    createVNode("img", {
                      class: "w-8 h-8 -mr-2 rounded-full border-2 border-white",
                      src: $props.team.owner.profile_photo_url,
                      alt: $props.team.owner.name
                    }, null, 8, ["src", "alt"])
                  ])) : createCommentVNode("", true)
                ]),
                !$props.team.personal_team ? (openBlock(), createBlock("div", { key: 0 }, [
                  createVNode(_component_Link, {
                    href: "/teams/" + $props.user.current_team.id,
                    class: "text-sm text-gray-800 font-bold"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Team Settings ")
                    ]),
                    _: 1
                  }, 8, ["href"])
                ])) : createCommentVNode("", true)
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
            team: $props.team,
            "team-role": $props.teamRole,
            mode: "create",
            projects: $props.projects
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<div${_scopeId}><div class="max-w-lg my-6 py-6 mx-auto"${_scopeId}><div class="text-center"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"${_scopeId}><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"${_scopeId}></path></svg><h3 class="mt-2 text-md font-medium text-gray-900"${_scopeId}> You have no <b${_scopeId}>projects</b> or <b${_scopeId}>samples</b> yet </h3>`);
          if (_ctx.editableTeamRole) {
            _push2(`<div class="mt-2"${_scopeId}><p class="mb-1 text-sm text-gray-500"${_scopeId}> Get started by uploading your data. </p>`);
            _push2(ssrRenderComponent(_component_create, {
              class: "mt-5",
              mode: "button"
            }, null, _parent2, _scopeId));
            _push2(`<span class="float-center text-xs cursor-pointer hover:text-blue-700 mt-2"${_scopeId}><a href="https://docs.nmrxiv.org/submission-guides/submission-process.html" target="_blank"${_scopeId}>Need Help? </a></span></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div></div>`);
        }
        _push2(`<div class="px-12 py-8 mx-auto max-w-4xl"${_scopeId}><ul role="list" class="mt-6 border-b border-gray-200 divide-y divide-gray-200"${_scopeId}><li${_scopeId}><div class="relative group py-4 flex items-start space-x-3"${_scopeId}><div class="flex-shrink-0"${_scopeId}><span class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-pink-500"${_scopeId}><svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"${_scopeId}></path></svg></span></div><div class="min-w-0 flex-1"${_scopeId}><div class="text-sm font-medium text-gray-900"${_scopeId}><a id="tour-step-submission-guide" href="https://docs.nmrxiv.org/introduction/intro" target="_blank"${_scopeId}><span class="absolute inset-0" aria-hidden="true"${_scopeId}></span> Get started! How to use nmrXiv? </a></div><p class="text-sm text-gray-500"${_scopeId}> Documentation for using nmrXiv. Explore, learn and archive NMR datasets. </p></div><div class="flex-shrink-0 self-center"${_scopeId}><svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg></div></div></li><li${_scopeId}><div class="relative group py-4 flex items-start space-x-3"${_scopeId}><div class="flex-shrink-0"${_scopeId}><span class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-purple-500"${_scopeId}><svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"${_scopeId}></path></svg></span></div><div class="min-w-0 flex-1"${_scopeId}><div class="text-sm font-medium text-gray-900"${_scopeId}><a id="tour-step-api" href="https://docs.nmrxiv.org/developer-guides/api.html" target="_blank"${_scopeId}><span class="absolute inset-0" aria-hidden="true"${_scopeId}></span> Public API Documentation </a></div><p class="text-sm text-gray-500"${_scopeId}> Search, interact and download NMR Datasets as a part of your software or your data science workflow. </p></div><div class="flex-shrink-0 self-center"${_scopeId}><svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg></div></div></li><li${_scopeId}><div class="relative group py-4 flex items-start space-x-3"${_scopeId}><div class="flex-shrink-0"${_scopeId}><span class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-yellow-500"${_scopeId}><svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"${_scopeId}></path></svg></span></div><div class="min-w-0 flex-1"${_scopeId}><div class="text-sm font-medium text-gray-900"${_scopeId}><a id="tour-step-spectra-challenge" href="/"${_scopeId}><span class="absolute inset-0" aria-hidden="true"${_scopeId}></span> Challenges </a></div><p class="text-sm text-gray-500"${_scopeId}> Structure elucidation challenges are designed for researchers at all different stages of their careers. </p></div><div class="flex-shrink-0 self-center"${_scopeId}><svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg></div></div></li></ul><div class="mt-6 flex"${_scopeId}><a id="tour-step-get-in-touch"${ssrRenderAttr("href", $options.mailTo)} class="text-sm font-medium text-indigo-600 hover:text-indigo-500"${_scopeId}>Or get in touch<span aria-hidden="true"${_scopeId}> →</span></a></div></div>`);
        _push2(ssrRenderComponent(_component_onboarding, null, null, _parent2, _scopeId));
      } else {
        return [
          $props.projects.length > 0 ? (openBlock(), createBlock("div", {
            key: 0,
            class: "px-12 py-8 mx-auto max-w-4xl"
          }, [
            createVNode(_component_team_projects, {
              team: $props.team,
              "team-role": $props.teamRole,
              mode: "create",
              projects: $props.projects
            }, null, 8, ["team", "team-role", "projects"])
          ])) : (openBlock(), createBlock("div", { key: 1 }, [
            createVNode("div", { class: "max-w-lg my-6 py-6 mx-auto" }, [
              createVNode("div", { class: "text-center" }, [
                (openBlock(), createBlock("svg", {
                  class: "mx-auto h-12 w-12 text-gray-400",
                  fill: "none",
                  viewBox: "0 0 24 24",
                  stroke: "currentColor",
                  "aria-hidden": "true"
                }, [
                  createVNode("path", {
                    "vector-effect": "non-scaling-stroke",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                  })
                ])),
                createVNode("h3", { class: "mt-2 text-md font-medium text-gray-900" }, [
                  createTextVNode(" You have no "),
                  createVNode("b", null, "projects"),
                  createTextVNode(" or "),
                  createVNode("b", null, "samples"),
                  createTextVNode(" yet ")
                ]),
                _ctx.editableTeamRole ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "mt-2"
                }, [
                  createVNode("p", { class: "mb-1 text-sm text-gray-500" }, " Get started by uploading your data. "),
                  createVNode(_component_create, {
                    class: "mt-5",
                    mode: "button"
                  }),
                  createVNode("span", { class: "float-center text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                    createVNode("a", {
                      href: "https://docs.nmrxiv.org/submission-guides/submission-process.html",
                      target: "_blank"
                    }, "Need Help? ")
                  ])
                ])) : createCommentVNode("", true)
              ])
            ])
          ])),
          createVNode("div", { class: "px-12 py-8 mx-auto max-w-4xl" }, [
            createVNode("ul", {
              role: "list",
              class: "mt-6 border-b border-gray-200 divide-y divide-gray-200"
            }, [
              createVNode("li", null, [
                createVNode("div", { class: "relative group py-4 flex items-start space-x-3" }, [
                  createVNode("div", { class: "flex-shrink-0" }, [
                    createVNode("span", { class: "inline-flex items-center justify-center h-10 w-10 rounded-lg bg-pink-500" }, [
                      (openBlock(), createBlock("svg", {
                        class: "h-6 w-6 text-white",
                        xmlns: "http://www.w3.org/2000/svg",
                        fill: "none",
                        viewBox: "0 0 24 24",
                        "stroke-width": "2",
                        stroke: "currentColor",
                        "aria-hidden": "true"
                      }, [
                        createVNode("path", {
                          "stroke-linecap": "round",
                          "stroke-linejoin": "round",
                          d: "M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                        })
                      ]))
                    ])
                  ]),
                  createVNode("div", { class: "min-w-0 flex-1" }, [
                    createVNode("div", { class: "text-sm font-medium text-gray-900" }, [
                      createVNode("a", {
                        id: "tour-step-submission-guide",
                        href: "https://docs.nmrxiv.org/introduction/intro",
                        target: "_blank"
                      }, [
                        createVNode("span", {
                          class: "absolute inset-0",
                          "aria-hidden": "true"
                        }),
                        createTextVNode(" Get started! How to use nmrXiv? ")
                      ])
                    ]),
                    createVNode("p", { class: "text-sm text-gray-500" }, " Documentation for using nmrXiv. Explore, learn and archive NMR datasets. ")
                  ]),
                  createVNode("div", { class: "flex-shrink-0 self-center" }, [
                    (openBlock(), createBlock("svg", {
                      class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                      xmlns: "http://www.w3.org/2000/svg",
                      viewBox: "0 0 20 20",
                      fill: "currentColor",
                      "aria-hidden": "true"
                    }, [
                      createVNode("path", {
                        "fill-rule": "evenodd",
                        d: "M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z",
                        "clip-rule": "evenodd"
                      })
                    ]))
                  ])
                ])
              ]),
              createVNode("li", null, [
                createVNode("div", { class: "relative group py-4 flex items-start space-x-3" }, [
                  createVNode("div", { class: "flex-shrink-0" }, [
                    createVNode("span", { class: "inline-flex items-center justify-center h-10 w-10 rounded-lg bg-purple-500" }, [
                      (openBlock(), createBlock("svg", {
                        class: "h-6 w-6 text-white",
                        xmlns: "http://www.w3.org/2000/svg",
                        fill: "none",
                        viewBox: "0 0 24 24",
                        "stroke-width": "2",
                        stroke: "currentColor",
                        "aria-hidden": "true"
                      }, [
                        createVNode("path", {
                          "stroke-linecap": "round",
                          "stroke-linejoin": "round",
                          d: "M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        })
                      ]))
                    ])
                  ]),
                  createVNode("div", { class: "min-w-0 flex-1" }, [
                    createVNode("div", { class: "text-sm font-medium text-gray-900" }, [
                      createVNode("a", {
                        id: "tour-step-api",
                        href: "https://docs.nmrxiv.org/developer-guides/api.html",
                        target: "_blank"
                      }, [
                        createVNode("span", {
                          class: "absolute inset-0",
                          "aria-hidden": "true"
                        }),
                        createTextVNode(" Public API Documentation ")
                      ])
                    ]),
                    createVNode("p", { class: "text-sm text-gray-500" }, " Search, interact and download NMR Datasets as a part of your software or your data science workflow. ")
                  ]),
                  createVNode("div", { class: "flex-shrink-0 self-center" }, [
                    (openBlock(), createBlock("svg", {
                      class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                      xmlns: "http://www.w3.org/2000/svg",
                      viewBox: "0 0 20 20",
                      fill: "currentColor",
                      "aria-hidden": "true"
                    }, [
                      createVNode("path", {
                        "fill-rule": "evenodd",
                        d: "M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z",
                        "clip-rule": "evenodd"
                      })
                    ]))
                  ])
                ])
              ]),
              createVNode("li", null, [
                createVNode("div", { class: "relative group py-4 flex items-start space-x-3" }, [
                  createVNode("div", { class: "flex-shrink-0" }, [
                    createVNode("span", { class: "inline-flex items-center justify-center h-10 w-10 rounded-lg bg-yellow-500" }, [
                      (openBlock(), createBlock("svg", {
                        class: "h-6 w-6 text-white",
                        xmlns: "http://www.w3.org/2000/svg",
                        fill: "none",
                        viewBox: "0 0 24 24",
                        "stroke-width": "2",
                        stroke: "currentColor",
                        "aria-hidden": "true"
                      }, [
                        createVNode("path", {
                          "stroke-linecap": "round",
                          "stroke-linejoin": "round",
                          d: "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        })
                      ]))
                    ])
                  ]),
                  createVNode("div", { class: "min-w-0 flex-1" }, [
                    createVNode("div", { class: "text-sm font-medium text-gray-900" }, [
                      createVNode("a", {
                        id: "tour-step-spectra-challenge",
                        href: "/"
                      }, [
                        createVNode("span", {
                          class: "absolute inset-0",
                          "aria-hidden": "true"
                        }),
                        createTextVNode(" Challenges ")
                      ])
                    ]),
                    createVNode("p", { class: "text-sm text-gray-500" }, " Structure elucidation challenges are designed for researchers at all different stages of their careers. ")
                  ]),
                  createVNode("div", { class: "flex-shrink-0 self-center" }, [
                    (openBlock(), createBlock("svg", {
                      class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                      xmlns: "http://www.w3.org/2000/svg",
                      viewBox: "0 0 20 20",
                      fill: "currentColor",
                      "aria-hidden": "true"
                    }, [
                      createVNode("path", {
                        "fill-rule": "evenodd",
                        d: "M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z",
                        "clip-rule": "evenodd"
                      })
                    ]))
                  ])
                ])
              ])
            ]),
            createVNode("div", { class: "mt-6 flex" }, [
              createVNode("a", {
                id: "tour-step-get-in-touch",
                href: $options.mailTo,
                class: "text-sm font-medium text-indigo-600 hover:text-indigo-500"
              }, [
                createTextVNode("Or get in touch"),
                createVNode("span", { "aria-hidden": "true" }, " →")
              ], 8, ["href"])
            ])
          ]),
          createVNode(_component_onboarding)
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Dashboard.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Dashboard = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Dashboard as default
};
