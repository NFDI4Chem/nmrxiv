import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import throttle from "lodash/throttle.js";
import mapValues from "lodash/mapValues.js";
import pickBy from "lodash/pickBy.js";
import { S as SearchFilter } from "./SearchFilter-01feb0b0.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { B as BreadCrumbs } from "./BreadCrumbs-a9158bdf.mjs";
import { Link } from "@inertiajs/vue3";
import { resolveComponent, mergeProps, withCtx, createVNode, withDirectives, openBlock, createBlock, Fragment, renderList, toDisplayString, vModelSelect, createTextVNode, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList, ssrInterpolate, ssrRenderClass } from "vue/server-renderer";
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
    users: Array,
    filters: Object,
    roles: Array
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        role: this.filters.role,
        trashed: this.filters.trashed
      },
      currentlyManagingRole: false,
      managingRoleFor: null,
      updateRoleForm: this.$inertia.form({
        role: null,
        error_message: null
      }),
      pages: [
        { name: "Console", route: "console", current: false },
        { name: "Users", route: "console.users", current: true }
      ]
    };
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form);
        this.$inertia.replace(
          this.route(
            "console.users",
            Object.keys(query).length ? query : { remember: "forget" }
          )
        );
      }, 150),
      deep: true
    }
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    manageRole(user) {
      this.updateRoleForm.error_message = null;
      if (user.role[0]) {
        this.updateRoleForm.role = user.role[0];
      } else {
        this.updateRoleForm.role = null;
      }
      this.managingRoleFor = user;
      this.currentlyManagingRole = true;
    },
    updateRole() {
      this.updateRoleForm.put(
        route("console.users.update-role", [this.managingRoleFor]),
        {
          preserveScroll: true,
          onSuccess: () => this.currentlyManagingRole = false,
          onError: (data) => {
            this.updateRoleForm.error_message = data.error_message;
          }
        }
      );
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_bread_crumbs = resolveComponent("bread-crumbs");
  const _component_search_filter = resolveComponent("search-filter");
  const _component_Link = resolveComponent("Link");
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_button = resolveComponent("jet-button");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Users & Permissions" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_bread_crumbs, { pages: $data.pages }, null, _parent2, _scopeId));
        _push2(`<div class="mt-2 md:flex md:items-center md:justify-between"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"${_scopeId}> Users &amp; Permissions </div></div></div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode(_component_bread_crumbs, { pages: $data.pages }, null, 8, ["pages"]),
                  createVNode("div", { class: "mt-2 md:flex md:items-center md:justify-between" }, [
                    createVNode("div", { class: "flex-1 min-w-0" }, [
                      createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3" }, " Users & Permissions ")
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
        _push2(`<div class="py-12 px-10"${_scopeId}><div class="mb-6 flex justify-between items-center"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_search_filter, {
          modelValue: $data.form.search,
          "onUpdate:modelValue": ($event) => $data.form.search = $event,
          class: "w-full max-w-md mr-4",
          onReset: $options.reset
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<label class="block text-sm font-medium text-gray-700"${_scopeId2}>Role:</label><select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"${_scopeId2}><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.role) ? ssrLooseContain($data.form.role, null) : ssrLooseEqual($data.form.role, null)) ? " selected" : ""}${_scopeId2}>-- no filter --</option><!--[-->`);
              ssrRenderList($props.roles, (role) => {
                _push3(`<option${ssrRenderAttr("value", role.name)}${ssrIncludeBooleanAttr($data.form.role == role.name) ? " selected" : ""}${_scopeId2}>${ssrInterpolate(role.name)}</option>`);
              });
              _push3(`<!--]--></select>`);
            } else {
              return [
                createVNode("label", { class: "block text-sm font-medium text-gray-700" }, "Role:"),
                withDirectives(createVNode("select", {
                  "onUpdate:modelValue": ($event) => $data.form.role = $event,
                  class: "mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                }, [
                  createVNode("option", { value: null }, "-- no filter --"),
                  (openBlock(true), createBlock(Fragment, null, renderList($props.roles, (role) => {
                    return openBlock(), createBlock("option", {
                      key: role.id,
                      value: role.name,
                      selected: $data.form.role == role.name
                    }, toDisplayString(role.name), 9, ["value", "selected"]);
                  }), 128))
                ], 8, ["onUpdate:modelValue"]), [
                  [vModelSelect, $data.form.role]
                ])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_Link, {
          class: "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition",
          href: _ctx.route("console.users.create")
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<span${_scopeId2}>Create</span>  <span class="hidden md:inline"${_scopeId2}>User</span>`);
            } else {
              return [
                createVNode("span", null, "Create"),
                createTextVNode("  "),
                createVNode("span", { class: "hidden md:inline" }, "User")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div><div${_scopeId}><div class="bg-white rounded-md shadow overflow-x-auto"${_scopeId}><table class="min-w-full divide-y divide-gray-200"${_scopeId}><thead class="bg-gray-50"${_scopeId}><tr${_scopeId}><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"${_scopeId}> Name </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"${_scopeId}> Status </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"${_scopeId}> Role </th><th scope="col" class="relative px-6 py-3"${_scopeId}><span class="sr-only"${_scopeId}>Edit</span></th></tr></thead><tbody class="bg-white divide-y divide-gray-200"${_scopeId}><!--[-->`);
        ssrRenderList($props.users, (user) => {
          _push2(`<tr${_scopeId}><td class="px-6 py-4 whitespace-nowrap"${_scopeId}><div class="flex items-center"${_scopeId}><div class="flex-shrink-0"${_scopeId}><img class="rounded-full h-10 w-10 object-cover"${ssrRenderAttr("src", user.profile_photo_url)} alt=""${_scopeId}></div><div class="ml-4"${_scopeId}><div class="text-sm font-medium text-gray-900"${_scopeId}>${ssrInterpolate(user.first_name)} ${ssrInterpolate(user.last_name)}</div><div class="text-sm text-gray-500"${_scopeId}>${ssrInterpolate(user.email)}</div><div class="text-sm text-teal-500"${_scopeId}>${ssrInterpolate(user.orcid_id)}</div></div></div></td><td class="px-6 py-4 whitespace-nowrap"${_scopeId}>`);
          if (user.verified_at) {
            _push2(`<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"${_scopeId}> Verified </span>`);
          } else {
            _push2(`<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-green-800"${_scopeId}> Not verified </span>`);
          }
          _push2(`</td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"${_scopeId}>`);
          if ($props.roles.length) {
            _push2(`<button class="ml-2 text-sm text-gray-400 underline"${_scopeId}><span class="px-2 inline-flex text-xs leading-5 font-semibold capitalize rounded-full bg-indigo-100 text-green-800"${_scopeId}>${ssrInterpolate(user.role[0] ? user.role[0] : "User")}</span></button>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Link, {
            href: _ctx.route("console.users.edit", user.id),
            class: "text-indigo-600 hover:text-indigo-900"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`Edit`);
              } else {
                return [
                  createTextVNode("Edit")
                ];
              }
            }),
            _: 2
          }, _parent2, _scopeId));
          _push2(`</td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"${_scopeId}>`);
          if (_ctx.$page.props.auth.user.id != user.id) {
            _push2(ssrRenderComponent(_component_Link, {
              href: _ctx.route(
                "console.users.impersonate",
                user.id
              ),
              class: "text-indigo-600 hover:text-indigo-900"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`Impersonate <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline"${_scopeId2}><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    createTextVNode("Impersonate "),
                    (openBlock(), createBlock("svg", {
                      xmlns: "http://www.w3.org/2000/svg",
                      fill: "none",
                      viewBox: "0 0 24 24",
                      "stroke-width": "1.5",
                      stroke: "currentColor",
                      class: "w-5 h-5 inline"
                    }, [
                      createVNode("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        d: "M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5"
                      })
                    ]))
                  ];
                }
              }),
              _: 2
            }, _parent2, _scopeId));
          } else {
            _push2(`<!---->`);
          }
          _push2(`</td></tr>`);
        });
        _push2(`<!--]--></tbody></table></div></div>`);
        _push2(ssrRenderComponent(_component_jet_dialog_modal, {
          show: $data.currentlyManagingRole,
          onClose: ($event) => $data.currentlyManagingRole = false
        }, {
          title: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Manage Role `);
            } else {
              return [
                createTextVNode(" Manage Role ")
              ];
            }
          }),
          content: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              if ($data.managingRoleFor) {
                _push3(`<div${_scopeId2}><div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"${_scopeId2}><!--[-->`);
                ssrRenderList($props.roles, (role, i) => {
                  _push3(`<button type="button" class="${ssrRenderClass([{
                    "border-t border-gray-200 rounded-t-none": i > 0,
                    "rounded-b-none": i !== Object.keys($props.roles).length - 1
                  }, "relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"])}"${_scopeId2}><div class="${ssrRenderClass({
                    "opacity-50": $data.updateRoleForm.role && $data.updateRoleForm.role !== role.name
                  })}"${_scopeId2}><div class="flex items-center"${_scopeId2}><div class="${ssrRenderClass([{
                    "font-semibold": $data.updateRoleForm.role === role.key
                  }, "text-sm text-gray-600 capitalize"])}"${_scopeId2}>${ssrInterpolate(role.name)}</div>`);
                  if ($data.updateRoleForm.role === role.name) {
                    _push3(`<svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"${_scopeId2}><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId2}></path></svg>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`</div><div class="mt-2 text-xs text-gray-600"${_scopeId2}>${ssrInterpolate(role.description)}</div></div></button>`);
                });
                _push3(`<!--]--></div>`);
                if ($data.updateRoleForm.error_message) {
                  _push3(`<div class="my-2"${_scopeId2}>${ssrInterpolate($data.updateRoleForm.error_message)}</div>`);
                } else {
                  _push3(`<!---->`);
                }
                _push3(`</div>`);
              } else {
                _push3(`<!---->`);
              }
            } else {
              return [
                $data.managingRoleFor ? (openBlock(), createBlock("div", { key: 0 }, [
                  createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer" }, [
                    (openBlock(true), createBlock(Fragment, null, renderList($props.roles, (role, i) => {
                      return openBlock(), createBlock("button", {
                        key: role.key,
                        type: "button",
                        class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                          "border-t border-gray-200 rounded-t-none": i > 0,
                          "rounded-b-none": i !== Object.keys($props.roles).length - 1
                        }],
                        onClick: ($event) => $data.updateRoleForm.role = role.name
                      }, [
                        createVNode("div", {
                          class: {
                            "opacity-50": $data.updateRoleForm.role && $data.updateRoleForm.role !== role.name
                          }
                        }, [
                          createVNode("div", { class: "flex items-center" }, [
                            createVNode("div", {
                              class: ["text-sm text-gray-600 capitalize", {
                                "font-semibold": $data.updateRoleForm.role === role.key
                              }]
                            }, toDisplayString(role.name), 3),
                            $data.updateRoleForm.role === role.name ? (openBlock(), createBlock("svg", {
                              key: 0,
                              class: "ml-2 h-5 w-5 text-green-400",
                              fill: "none",
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              "stroke-width": "2",
                              stroke: "currentColor",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                            ])) : createCommentVNode("", true)
                          ]),
                          createVNode("div", { class: "mt-2 text-xs text-gray-600" }, toDisplayString(role.description), 1)
                        ], 2)
                      ], 10, ["onClick"]);
                    }), 128))
                  ]),
                  $data.updateRoleForm.error_message ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "my-2"
                  }, toDisplayString($data.updateRoleForm.error_message), 1)) : createCommentVNode("", true)
                ])) : createCommentVNode("", true)
              ];
            }
          }),
          footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_jet_secondary_button, {
                onClick: ($event) => $data.currentlyManagingRole = false
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Cancel `);
                  } else {
                    return [
                      createTextVNode(" Cancel ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_jet_button, {
                class: ["ml-2", { "opacity-25": $data.updateRoleForm.processing }],
                disabled: $data.updateRoleForm.processing,
                onClick: $options.updateRole
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Save `);
                  } else {
                    return [
                      createTextVNode(" Save ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_jet_secondary_button, {
                  onClick: ($event) => $data.currentlyManagingRole = false
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                createVNode(_component_jet_button, {
                  class: ["ml-2", { "opacity-25": $data.updateRoleForm.processing }],
                  disabled: $data.updateRoleForm.processing,
                  onClick: $options.updateRole
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Save ")
                  ]),
                  _: 1
                }, 8, ["class", "disabled", "onClick"])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "py-12 px-10" }, [
            createVNode("div", { class: "mb-6 flex justify-between items-center" }, [
              createVNode(_component_search_filter, {
                modelValue: $data.form.search,
                "onUpdate:modelValue": ($event) => $data.form.search = $event,
                class: "w-full max-w-md mr-4",
                onReset: $options.reset
              }, {
                default: withCtx(() => [
                  createVNode("label", { class: "block text-sm font-medium text-gray-700" }, "Role:"),
                  withDirectives(createVNode("select", {
                    "onUpdate:modelValue": ($event) => $data.form.role = $event,
                    class: "mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  }, [
                    createVNode("option", { value: null }, "-- no filter --"),
                    (openBlock(true), createBlock(Fragment, null, renderList($props.roles, (role) => {
                      return openBlock(), createBlock("option", {
                        key: role.id,
                        value: role.name,
                        selected: $data.form.role == role.name
                      }, toDisplayString(role.name), 9, ["value", "selected"]);
                    }), 128))
                  ], 8, ["onUpdate:modelValue"]), [
                    [vModelSelect, $data.form.role]
                  ])
                ]),
                _: 1
              }, 8, ["modelValue", "onUpdate:modelValue", "onReset"]),
              createVNode(_component_Link, {
                class: "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition",
                href: _ctx.route("console.users.create")
              }, {
                default: withCtx(() => [
                  createVNode("span", null, "Create"),
                  createTextVNode("  "),
                  createVNode("span", { class: "hidden md:inline" }, "User")
                ]),
                _: 1
              }, 8, ["href"])
            ]),
            createVNode("div", null, [
              createVNode("div", { class: "bg-white rounded-md shadow overflow-x-auto" }, [
                createVNode("table", { class: "min-w-full divide-y divide-gray-200" }, [
                  createVNode("thead", { class: "bg-gray-50" }, [
                    createVNode("tr", null, [
                      createVNode("th", {
                        scope: "col",
                        class: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      }, " Name "),
                      createVNode("th", {
                        scope: "col",
                        class: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      }, " Status "),
                      createVNode("th", {
                        scope: "col",
                        class: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      }, " Role "),
                      createVNode("th", {
                        scope: "col",
                        class: "relative px-6 py-3"
                      }, [
                        createVNode("span", { class: "sr-only" }, "Edit")
                      ])
                    ])
                  ]),
                  createVNode("tbody", { class: "bg-white divide-y divide-gray-200" }, [
                    (openBlock(true), createBlock(Fragment, null, renderList($props.users, (user) => {
                      return openBlock(), createBlock("tr", {
                        key: user.email
                      }, [
                        createVNode("td", { class: "px-6 py-4 whitespace-nowrap" }, [
                          createVNode("div", { class: "flex items-center" }, [
                            createVNode("div", { class: "flex-shrink-0" }, [
                              createVNode("img", {
                                class: "rounded-full h-10 w-10 object-cover",
                                src: user.profile_photo_url,
                                alt: ""
                              }, null, 8, ["src"])
                            ]),
                            createVNode("div", { class: "ml-4" }, [
                              createVNode("div", { class: "text-sm font-medium text-gray-900" }, toDisplayString(user.first_name) + " " + toDisplayString(user.last_name), 1),
                              createVNode("div", { class: "text-sm text-gray-500" }, toDisplayString(user.email), 1),
                              createVNode("div", { class: "text-sm text-teal-500" }, toDisplayString(user.orcid_id), 1)
                            ])
                          ])
                        ]),
                        createVNode("td", { class: "px-6 py-4 whitespace-nowrap" }, [
                          user.verified_at ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: "px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                          }, " Verified ")) : (openBlock(), createBlock("span", {
                            key: 1,
                            class: "px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-green-800"
                          }, " Not verified "))
                        ]),
                        createVNode("td", { class: "px-6 py-4 whitespace-nowrap text-sm text-gray-500" }, [
                          $props.roles.length ? (openBlock(), createBlock("button", {
                            key: 0,
                            class: "ml-2 text-sm text-gray-400 underline",
                            onClick: ($event) => $options.manageRole(user)
                          }, [
                            createVNode("span", { class: "px-2 inline-flex text-xs leading-5 font-semibold capitalize rounded-full bg-indigo-100 text-green-800" }, toDisplayString(user.role[0] ? user.role[0] : "User"), 1)
                          ], 8, ["onClick"])) : createCommentVNode("", true)
                        ]),
                        createVNode("td", { class: "px-6 py-4 whitespace-nowrap text-right text-sm font-medium" }, [
                          createVNode(_component_Link, {
                            href: _ctx.route("console.users.edit", user.id),
                            class: "text-indigo-600 hover:text-indigo-900"
                          }, {
                            default: withCtx(() => [
                              createTextVNode("Edit")
                            ]),
                            _: 2
                          }, 1032, ["href"])
                        ]),
                        createVNode("td", { class: "px-6 py-4 whitespace-nowrap text-right text-sm font-medium" }, [
                          _ctx.$page.props.auth.user.id != user.id ? (openBlock(), createBlock(_component_Link, {
                            key: 0,
                            href: _ctx.route(
                              "console.users.impersonate",
                              user.id
                            ),
                            class: "text-indigo-600 hover:text-indigo-900"
                          }, {
                            default: withCtx(() => [
                              createTextVNode("Impersonate "),
                              (openBlock(), createBlock("svg", {
                                xmlns: "http://www.w3.org/2000/svg",
                                fill: "none",
                                viewBox: "0 0 24 24",
                                "stroke-width": "1.5",
                                stroke: "currentColor",
                                class: "w-5 h-5 inline"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  d: "M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5"
                                })
                              ]))
                            ]),
                            _: 2
                          }, 1032, ["href"])) : createCommentVNode("", true)
                        ])
                      ]);
                    }), 128))
                  ])
                ])
              ])
            ]),
            createVNode(_component_jet_dialog_modal, {
              show: $data.currentlyManagingRole,
              onClose: ($event) => $data.currentlyManagingRole = false
            }, {
              title: withCtx(() => [
                createTextVNode(" Manage Role ")
              ]),
              content: withCtx(() => [
                $data.managingRoleFor ? (openBlock(), createBlock("div", { key: 0 }, [
                  createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer" }, [
                    (openBlock(true), createBlock(Fragment, null, renderList($props.roles, (role, i) => {
                      return openBlock(), createBlock("button", {
                        key: role.key,
                        type: "button",
                        class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                          "border-t border-gray-200 rounded-t-none": i > 0,
                          "rounded-b-none": i !== Object.keys($props.roles).length - 1
                        }],
                        onClick: ($event) => $data.updateRoleForm.role = role.name
                      }, [
                        createVNode("div", {
                          class: {
                            "opacity-50": $data.updateRoleForm.role && $data.updateRoleForm.role !== role.name
                          }
                        }, [
                          createVNode("div", { class: "flex items-center" }, [
                            createVNode("div", {
                              class: ["text-sm text-gray-600 capitalize", {
                                "font-semibold": $data.updateRoleForm.role === role.key
                              }]
                            }, toDisplayString(role.name), 3),
                            $data.updateRoleForm.role === role.name ? (openBlock(), createBlock("svg", {
                              key: 0,
                              class: "ml-2 h-5 w-5 text-green-400",
                              fill: "none",
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              "stroke-width": "2",
                              stroke: "currentColor",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                            ])) : createCommentVNode("", true)
                          ]),
                          createVNode("div", { class: "mt-2 text-xs text-gray-600" }, toDisplayString(role.description), 1)
                        ], 2)
                      ], 10, ["onClick"]);
                    }), 128))
                  ]),
                  $data.updateRoleForm.error_message ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "my-2"
                  }, toDisplayString($data.updateRoleForm.error_message), 1)) : createCommentVNode("", true)
                ])) : createCommentVNode("", true)
              ]),
              footer: withCtx(() => [
                createVNode(_component_jet_secondary_button, {
                  onClick: ($event) => $data.currentlyManagingRole = false
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                createVNode(_component_jet_button, {
                  class: ["ml-2", { "opacity-25": $data.updateRoleForm.processing }],
                  disabled: $data.updateRoleForm.processing,
                  onClick: $options.updateRole
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Save ")
                  ]),
                  _: 1
                }, 8, ["class", "disabled", "onClick"])
              ]),
              _: 1
            }, 8, ["show", "onClose"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Console/Users/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
