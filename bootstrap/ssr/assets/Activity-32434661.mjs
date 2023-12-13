import { Dialog, DialogOverlay, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { HeartIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { PencilIcon, PlusSmallIcon } from "@heroicons/vue/24/solid";
import { resolveComponent, mergeProps, withCtx, createVNode, createTextVNode, toDisplayString, openBlock, createBlock, Fragment, renderList, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    Dialog,
    DialogOverlay,
    TransitionChild,
    TransitionRoot,
    HeartIcon,
    PencilIcon,
    PlusSmallIcon,
    XMarkIcon
  },
  props: ["study", "project"],
  data() {
    return {
      open: false,
      audit: []
    };
  },
  updated() {
    this.fetchActivity(this.project);
  },
  methods: {
    toggleDetails() {
      this.open = !this.open;
    },
    fetchActivity(entity) {
      axios.get(route("dashboard.project.activity", entity.id)).then((res) => {
        this.audit = res.data.audit;
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_TransitionRoot = resolveComponent("TransitionRoot");
  const _component_Dialog = resolveComponent("Dialog");
  const _component_TransitionChild = resolveComponent("TransitionChild");
  const _component_DialogOverlay = resolveComponent("DialogOverlay");
  const _component_XMarkIcon = resolveComponent("XMarkIcon");
  _push(ssrRenderComponent(_component_TransitionRoot, mergeProps({
    as: "template",
    show: $data.open
  }, _attrs), {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_Dialog, {
          as: "div",
          class: "fixed inset-0 overflow-hidden z-50",
          onClose: ($event) => $data.open = false
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<div class="absolute inset-0 overflow-hidden"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "ease-in-out duration-500",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "ease-in-out duration-500",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(ssrRenderComponent(_component_DialogOverlay, { class: "absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" }, null, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_DialogOverlay, { class: "absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<div class="fixed inset-y-0 right-0 pl-10 max-w-full flex"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "transform transition ease-in-out duration-500 sm:duration-700",
                "enter-from": "translate-x-full",
                "enter-to": "translate-x-0",
                leave: "transform transition ease-in-out duration-500 sm:duration-700",
                "leave-from": "translate-x-0",
                "leave-to": "translate-x-full"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(`<div class="relative w-96"${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_TransitionChild, {
                      as: "template",
                      enter: "ease-in-out duration-500",
                      "enter-from": "opacity-0",
                      "enter-to": "opacity-100",
                      leave: "ease-in-out duration-500",
                      "leave-from": "opacity-100",
                      "leave-to": "opacity-0"
                    }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<div class="absolute top-0 left-0 -ml-8 pt-4 pr-2 flex sm:-ml-10 sm:pr-4"${_scopeId4}><button type="button" class="rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"${_scopeId4}><span class="sr-only"${_scopeId4}>Close panel</span>`);
                          _push5(ssrRenderComponent(_component_XMarkIcon, {
                            class: "h-6 w-6",
                            "aria-hidden": "true"
                          }, null, _parent5, _scopeId4));
                          _push5(`</button></div>`);
                        } else {
                          return [
                            createVNode("div", { class: "absolute top-0 left-0 -ml-8 pt-4 pr-2 flex sm:-ml-10 sm:pr-4" }, [
                              createVNode("button", {
                                type: "button",
                                class: "rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white",
                                onClick: ($event) => $data.open = false
                              }, [
                                createVNode("span", { class: "sr-only" }, "Close panel"),
                                createVNode(_component_XMarkIcon, {
                                  class: "h-6 w-6",
                                  "aria-hidden": "true"
                                })
                              ], 8, ["onClick"])
                            ])
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                    _push4(`<div class="h-full bg-white p-8 overflow-y-auto"${_scopeId3}><div class="pb-16 space-y-6"${_scopeId3}><div${_scopeId3}><div class="mt-0 flex items-start justify-between"${_scopeId3}><div class="grid grid-cols-1 pt-1 text-left pt-1"${_scopeId3}><div class="text-xs italic text-gray-600 pr-5"${_scopeId3}><span class="text-gray-400"${_scopeId3}>Last updated on</span> ${ssrInterpolate(_ctx.formatDateTime(
                      $props.project.updated_at
                    ))}</div><div class="text-xs italic mt-1 text-gray-600 pr-5"${_scopeId3}><span class="text-gray-400"${_scopeId3}>Created on</span> ${ssrInterpolate(_ctx.formatDateTime(
                      $props.project.created_at
                    ))}</div></div></div></div><div${_scopeId3}><div${_scopeId3}><h2 id="timeline-title" class="text-lg font-medium text-gray-900"${_scopeId3}> Activity </h2><div class="mt-6 flow-root"${_scopeId3}><ul role="list" class="-mb-8"${_scopeId3}><!--[-->`);
                    ssrRenderList($data.audit, (log) => {
                      _push4(`<li${_scopeId3}><div class="relative pb-8"${_scopeId3}><span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"${_scopeId3}></span><div class="relative flex space-x-3"${_scopeId3}><div${_scopeId3}><img${ssrRenderAttr(
                        "src",
                        log.user.profile_photo_url
                      )}${ssrRenderAttr(
                        "alt",
                        log.user.first_name + " " + log.user.last_name
                      )} class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"${_scopeId3}></div><div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4"${_scopeId3}><div${_scopeId3}><a class="font-medium text-gray-900"${_scopeId3}>${ssrInterpolate(log.user.first_name)} ${ssrInterpolate(log.user.last_name)}</a><p class="text-sm text-gray-500"${_scopeId3}>${ssrInterpolate(log.event)}  <a class="font-medium text-gray-900"${_scopeId3}>`);
                      if (log.event == "created" || log.event == "deleted") {
                        _push4(`<span${_scopeId3}>${ssrInterpolate(log.auditable_type.replace(
                          "App\\Models\\",
                          ""
                        ))}</span>`);
                      } else {
                        _push4(`<!---->`);
                      }
                      if (log.event == "updated") {
                        _push4(`<span${_scopeId3}><!--[-->`);
                        ssrRenderList(Object.keys(
                          log.new_values
                        ), (property, $index) => {
                          _push4(`<span${_scopeId3}>`);
                          if ($index > 0) {
                            _push4(`<span${_scopeId3}>,</span>`);
                          } else {
                            _push4(`<!---->`);
                          }
                          _push4(` ${ssrInterpolate(property)}</span>`);
                        });
                        _push4(`<!--]--></span>`);
                      } else {
                        _push4(`<!---->`);
                      }
                      _push4(`</a></p><small${_scopeId3}><time${_scopeId3}>${ssrInterpolate(_ctx.formatDateTime(
                        log.created_at
                      ))}</time></small></div></div></div></div></li>`);
                    });
                    _push4(`<!--]--></ul></div></div></div></div>`);
                    if ($data.audit.length) {
                      _push4(`<small class="text-gray-500"${_scopeId3}><i${_scopeId3}>No logs before <br${_scopeId3}>${ssrInterpolate(_ctx.formatDateTime(
                        $data.audit[$data.audit.length - 1]["created_at"]
                      ))}</i></small>`);
                    } else {
                      _push4(`<!---->`);
                    }
                    _push4(`</div></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "relative w-96" }, [
                        createVNode(_component_TransitionChild, {
                          as: "template",
                          enter: "ease-in-out duration-500",
                          "enter-from": "opacity-0",
                          "enter-to": "opacity-100",
                          leave: "ease-in-out duration-500",
                          "leave-from": "opacity-100",
                          "leave-to": "opacity-0"
                        }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "absolute top-0 left-0 -ml-8 pt-4 pr-2 flex sm:-ml-10 sm:pr-4" }, [
                              createVNode("button", {
                                type: "button",
                                class: "rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white",
                                onClick: ($event) => $data.open = false
                              }, [
                                createVNode("span", { class: "sr-only" }, "Close panel"),
                                createVNode(_component_XMarkIcon, {
                                  class: "h-6 w-6",
                                  "aria-hidden": "true"
                                })
                              ], 8, ["onClick"])
                            ])
                          ]),
                          _: 1
                        }),
                        createVNode("div", { class: "h-full bg-white p-8 overflow-y-auto" }, [
                          createVNode("div", { class: "pb-16 space-y-6" }, [
                            createVNode("div", null, [
                              createVNode("div", { class: "mt-0 flex items-start justify-between" }, [
                                createVNode("div", { class: "grid grid-cols-1 pt-1 text-left pt-1" }, [
                                  createVNode("div", { class: "text-xs italic text-gray-600 pr-5" }, [
                                    createVNode("span", { class: "text-gray-400" }, "Last updated on"),
                                    createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                      $props.project.updated_at
                                    )), 1)
                                  ]),
                                  createVNode("div", { class: "text-xs italic mt-1 text-gray-600 pr-5" }, [
                                    createVNode("span", { class: "text-gray-400" }, "Created on"),
                                    createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                      $props.project.created_at
                                    )), 1)
                                  ])
                                ])
                              ])
                            ]),
                            createVNode("div", null, [
                              createVNode("div", null, [
                                createVNode("h2", {
                                  id: "timeline-title",
                                  class: "text-lg font-medium text-gray-900"
                                }, " Activity "),
                                createVNode("div", { class: "mt-6 flow-root" }, [
                                  createVNode("ul", {
                                    role: "list",
                                    class: "-mb-8"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($data.audit, (log) => {
                                      return openBlock(), createBlock("li", {
                                        key: log.auditable_id
                                      }, [
                                        createVNode("div", { class: "relative pb-8" }, [
                                          createVNode("span", {
                                            class: "absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200",
                                            "aria-hidden": "true"
                                          }),
                                          createVNode("div", { class: "relative flex space-x-3" }, [
                                            createVNode("div", null, [
                                              createVNode("img", {
                                                src: log.user.profile_photo_url,
                                                alt: log.user.first_name + " " + log.user.last_name,
                                                class: "h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"
                                              }, null, 8, ["src", "alt"])
                                            ]),
                                            createVNode("div", { class: "min-w-0 flex-1 pt-1.5 flex justify-between space-x-4" }, [
                                              createVNode("div", null, [
                                                createVNode("a", { class: "font-medium text-gray-900" }, toDisplayString(log.user.first_name) + " " + toDisplayString(log.user.last_name), 1),
                                                createVNode("p", { class: "text-sm text-gray-500" }, [
                                                  createTextVNode(toDisplayString(log.event) + "  ", 1),
                                                  createVNode("a", { class: "font-medium text-gray-900" }, [
                                                    log.event == "created" || log.event == "deleted" ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(log.auditable_type.replace(
                                                      "App\\Models\\",
                                                      ""
                                                    )), 1)) : createCommentVNode("", true),
                                                    log.event == "updated" ? (openBlock(), createBlock("span", { key: 1 }, [
                                                      (openBlock(true), createBlock(Fragment, null, renderList(Object.keys(
                                                        log.new_values
                                                      ), (property, $index) => {
                                                        return openBlock(), createBlock("span", {
                                                          key: property
                                                        }, [
                                                          $index > 0 ? (openBlock(), createBlock("span", { key: 0 }, ",")) : createCommentVNode("", true),
                                                          createTextVNode(" " + toDisplayString(property), 1)
                                                        ]);
                                                      }), 128))
                                                    ])) : createCommentVNode("", true)
                                                  ])
                                                ]),
                                                createVNode("small", null, [
                                                  createVNode("time", null, toDisplayString(_ctx.formatDateTime(
                                                    log.created_at
                                                  )), 1)
                                                ])
                                              ])
                                            ])
                                          ])
                                        ])
                                      ]);
                                    }), 128))
                                  ])
                                ])
                              ])
                            ])
                          ]),
                          $data.audit.length ? (openBlock(), createBlock("small", {
                            key: 0,
                            class: "text-gray-500"
                          }, [
                            createVNode("i", null, [
                              createTextVNode("No logs before "),
                              createVNode("br"),
                              createTextVNode(toDisplayString(_ctx.formatDateTime(
                                $data.audit[$data.audit.length - 1]["created_at"]
                              )), 1)
                            ])
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`</div></div>`);
            } else {
              return [
                createVNode("div", { class: "absolute inset-0 overflow-hidden" }, [
                  createVNode(_component_TransitionChild, {
                    as: "template",
                    enter: "ease-in-out duration-500",
                    "enter-from": "opacity-0",
                    "enter-to": "opacity-100",
                    leave: "ease-in-out duration-500",
                    "leave-from": "opacity-100",
                    "leave-to": "opacity-0"
                  }, {
                    default: withCtx(() => [
                      createVNode(_component_DialogOverlay, { class: "absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" })
                    ]),
                    _: 1
                  }),
                  createVNode("div", { class: "fixed inset-y-0 right-0 pl-10 max-w-full flex" }, [
                    createVNode(_component_TransitionChild, {
                      as: "template",
                      enter: "transform transition ease-in-out duration-500 sm:duration-700",
                      "enter-from": "translate-x-full",
                      "enter-to": "translate-x-0",
                      leave: "transform transition ease-in-out duration-500 sm:duration-700",
                      "leave-from": "translate-x-0",
                      "leave-to": "translate-x-full"
                    }, {
                      default: withCtx(() => [
                        createVNode("div", { class: "relative w-96" }, [
                          createVNode(_component_TransitionChild, {
                            as: "template",
                            enter: "ease-in-out duration-500",
                            "enter-from": "opacity-0",
                            "enter-to": "opacity-100",
                            leave: "ease-in-out duration-500",
                            "leave-from": "opacity-100",
                            "leave-to": "opacity-0"
                          }, {
                            default: withCtx(() => [
                              createVNode("div", { class: "absolute top-0 left-0 -ml-8 pt-4 pr-2 flex sm:-ml-10 sm:pr-4" }, [
                                createVNode("button", {
                                  type: "button",
                                  class: "rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white",
                                  onClick: ($event) => $data.open = false
                                }, [
                                  createVNode("span", { class: "sr-only" }, "Close panel"),
                                  createVNode(_component_XMarkIcon, {
                                    class: "h-6 w-6",
                                    "aria-hidden": "true"
                                  })
                                ], 8, ["onClick"])
                              ])
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "h-full bg-white p-8 overflow-y-auto" }, [
                            createVNode("div", { class: "pb-16 space-y-6" }, [
                              createVNode("div", null, [
                                createVNode("div", { class: "mt-0 flex items-start justify-between" }, [
                                  createVNode("div", { class: "grid grid-cols-1 pt-1 text-left pt-1" }, [
                                    createVNode("div", { class: "text-xs italic text-gray-600 pr-5" }, [
                                      createVNode("span", { class: "text-gray-400" }, "Last updated on"),
                                      createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                        $props.project.updated_at
                                      )), 1)
                                    ]),
                                    createVNode("div", { class: "text-xs italic mt-1 text-gray-600 pr-5" }, [
                                      createVNode("span", { class: "text-gray-400" }, "Created on"),
                                      createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                        $props.project.created_at
                                      )), 1)
                                    ])
                                  ])
                                ])
                              ]),
                              createVNode("div", null, [
                                createVNode("div", null, [
                                  createVNode("h2", {
                                    id: "timeline-title",
                                    class: "text-lg font-medium text-gray-900"
                                  }, " Activity "),
                                  createVNode("div", { class: "mt-6 flow-root" }, [
                                    createVNode("ul", {
                                      role: "list",
                                      class: "-mb-8"
                                    }, [
                                      (openBlock(true), createBlock(Fragment, null, renderList($data.audit, (log) => {
                                        return openBlock(), createBlock("li", {
                                          key: log.auditable_id
                                        }, [
                                          createVNode("div", { class: "relative pb-8" }, [
                                            createVNode("span", {
                                              class: "absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200",
                                              "aria-hidden": "true"
                                            }),
                                            createVNode("div", { class: "relative flex space-x-3" }, [
                                              createVNode("div", null, [
                                                createVNode("img", {
                                                  src: log.user.profile_photo_url,
                                                  alt: log.user.first_name + " " + log.user.last_name,
                                                  class: "h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"
                                                }, null, 8, ["src", "alt"])
                                              ]),
                                              createVNode("div", { class: "min-w-0 flex-1 pt-1.5 flex justify-between space-x-4" }, [
                                                createVNode("div", null, [
                                                  createVNode("a", { class: "font-medium text-gray-900" }, toDisplayString(log.user.first_name) + " " + toDisplayString(log.user.last_name), 1),
                                                  createVNode("p", { class: "text-sm text-gray-500" }, [
                                                    createTextVNode(toDisplayString(log.event) + "  ", 1),
                                                    createVNode("a", { class: "font-medium text-gray-900" }, [
                                                      log.event == "created" || log.event == "deleted" ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(log.auditable_type.replace(
                                                        "App\\Models\\",
                                                        ""
                                                      )), 1)) : createCommentVNode("", true),
                                                      log.event == "updated" ? (openBlock(), createBlock("span", { key: 1 }, [
                                                        (openBlock(true), createBlock(Fragment, null, renderList(Object.keys(
                                                          log.new_values
                                                        ), (property, $index) => {
                                                          return openBlock(), createBlock("span", {
                                                            key: property
                                                          }, [
                                                            $index > 0 ? (openBlock(), createBlock("span", { key: 0 }, ",")) : createCommentVNode("", true),
                                                            createTextVNode(" " + toDisplayString(property), 1)
                                                          ]);
                                                        }), 128))
                                                      ])) : createCommentVNode("", true)
                                                    ])
                                                  ]),
                                                  createVNode("small", null, [
                                                    createVNode("time", null, toDisplayString(_ctx.formatDateTime(
                                                      log.created_at
                                                    )), 1)
                                                  ])
                                                ])
                                              ])
                                            ])
                                          ])
                                        ]);
                                      }), 128))
                                    ])
                                  ])
                                ])
                              ])
                            ]),
                            $data.audit.length ? (openBlock(), createBlock("small", {
                              key: 0,
                              class: "text-gray-500"
                            }, [
                              createVNode("i", null, [
                                createTextVNode("No logs before "),
                                createVNode("br"),
                                createTextVNode(toDisplayString(_ctx.formatDateTime(
                                  $data.audit[$data.audit.length - 1]["created_at"]
                                )), 1)
                              ])
                            ])) : createCommentVNode("", true)
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
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_Dialog, {
            as: "div",
            class: "fixed inset-0 overflow-hidden z-50",
            onClose: ($event) => $data.open = false
          }, {
            default: withCtx(() => [
              createVNode("div", { class: "absolute inset-0 overflow-hidden" }, [
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "ease-in-out duration-500",
                  "enter-from": "opacity-0",
                  "enter-to": "opacity-100",
                  leave: "ease-in-out duration-500",
                  "leave-from": "opacity-100",
                  "leave-to": "opacity-0"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_DialogOverlay, { class: "absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" })
                  ]),
                  _: 1
                }),
                createVNode("div", { class: "fixed inset-y-0 right-0 pl-10 max-w-full flex" }, [
                  createVNode(_component_TransitionChild, {
                    as: "template",
                    enter: "transform transition ease-in-out duration-500 sm:duration-700",
                    "enter-from": "translate-x-full",
                    "enter-to": "translate-x-0",
                    leave: "transform transition ease-in-out duration-500 sm:duration-700",
                    "leave-from": "translate-x-0",
                    "leave-to": "translate-x-full"
                  }, {
                    default: withCtx(() => [
                      createVNode("div", { class: "relative w-96" }, [
                        createVNode(_component_TransitionChild, {
                          as: "template",
                          enter: "ease-in-out duration-500",
                          "enter-from": "opacity-0",
                          "enter-to": "opacity-100",
                          leave: "ease-in-out duration-500",
                          "leave-from": "opacity-100",
                          "leave-to": "opacity-0"
                        }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "absolute top-0 left-0 -ml-8 pt-4 pr-2 flex sm:-ml-10 sm:pr-4" }, [
                              createVNode("button", {
                                type: "button",
                                class: "rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white",
                                onClick: ($event) => $data.open = false
                              }, [
                                createVNode("span", { class: "sr-only" }, "Close panel"),
                                createVNode(_component_XMarkIcon, {
                                  class: "h-6 w-6",
                                  "aria-hidden": "true"
                                })
                              ], 8, ["onClick"])
                            ])
                          ]),
                          _: 1
                        }),
                        createVNode("div", { class: "h-full bg-white p-8 overflow-y-auto" }, [
                          createVNode("div", { class: "pb-16 space-y-6" }, [
                            createVNode("div", null, [
                              createVNode("div", { class: "mt-0 flex items-start justify-between" }, [
                                createVNode("div", { class: "grid grid-cols-1 pt-1 text-left pt-1" }, [
                                  createVNode("div", { class: "text-xs italic text-gray-600 pr-5" }, [
                                    createVNode("span", { class: "text-gray-400" }, "Last updated on"),
                                    createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                      $props.project.updated_at
                                    )), 1)
                                  ]),
                                  createVNode("div", { class: "text-xs italic mt-1 text-gray-600 pr-5" }, [
                                    createVNode("span", { class: "text-gray-400" }, "Created on"),
                                    createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                      $props.project.created_at
                                    )), 1)
                                  ])
                                ])
                              ])
                            ]),
                            createVNode("div", null, [
                              createVNode("div", null, [
                                createVNode("h2", {
                                  id: "timeline-title",
                                  class: "text-lg font-medium text-gray-900"
                                }, " Activity "),
                                createVNode("div", { class: "mt-6 flow-root" }, [
                                  createVNode("ul", {
                                    role: "list",
                                    class: "-mb-8"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($data.audit, (log) => {
                                      return openBlock(), createBlock("li", {
                                        key: log.auditable_id
                                      }, [
                                        createVNode("div", { class: "relative pb-8" }, [
                                          createVNode("span", {
                                            class: "absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200",
                                            "aria-hidden": "true"
                                          }),
                                          createVNode("div", { class: "relative flex space-x-3" }, [
                                            createVNode("div", null, [
                                              createVNode("img", {
                                                src: log.user.profile_photo_url,
                                                alt: log.user.first_name + " " + log.user.last_name,
                                                class: "h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"
                                              }, null, 8, ["src", "alt"])
                                            ]),
                                            createVNode("div", { class: "min-w-0 flex-1 pt-1.5 flex justify-between space-x-4" }, [
                                              createVNode("div", null, [
                                                createVNode("a", { class: "font-medium text-gray-900" }, toDisplayString(log.user.first_name) + " " + toDisplayString(log.user.last_name), 1),
                                                createVNode("p", { class: "text-sm text-gray-500" }, [
                                                  createTextVNode(toDisplayString(log.event) + "  ", 1),
                                                  createVNode("a", { class: "font-medium text-gray-900" }, [
                                                    log.event == "created" || log.event == "deleted" ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(log.auditable_type.replace(
                                                      "App\\Models\\",
                                                      ""
                                                    )), 1)) : createCommentVNode("", true),
                                                    log.event == "updated" ? (openBlock(), createBlock("span", { key: 1 }, [
                                                      (openBlock(true), createBlock(Fragment, null, renderList(Object.keys(
                                                        log.new_values
                                                      ), (property, $index) => {
                                                        return openBlock(), createBlock("span", {
                                                          key: property
                                                        }, [
                                                          $index > 0 ? (openBlock(), createBlock("span", { key: 0 }, ",")) : createCommentVNode("", true),
                                                          createTextVNode(" " + toDisplayString(property), 1)
                                                        ]);
                                                      }), 128))
                                                    ])) : createCommentVNode("", true)
                                                  ])
                                                ]),
                                                createVNode("small", null, [
                                                  createVNode("time", null, toDisplayString(_ctx.formatDateTime(
                                                    log.created_at
                                                  )), 1)
                                                ])
                                              ])
                                            ])
                                          ])
                                        ])
                                      ]);
                                    }), 128))
                                  ])
                                ])
                              ])
                            ])
                          ]),
                          $data.audit.length ? (openBlock(), createBlock("small", {
                            key: 0,
                            class: "text-gray-500"
                          }, [
                            createVNode("i", null, [
                              createTextVNode("No logs before "),
                              createVNode("br"),
                              createTextVNode(toDisplayString(_ctx.formatDateTime(
                                $data.audit[$data.audit.length - 1]["created_at"]
                              )), 1)
                            ])
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ]),
                    _: 1
                  })
                ])
              ])
            ]),
            _: 1
          }, 8, ["onClose"])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Project/Partials/Activity.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const ProjectActivity = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  ProjectActivity as default
};
