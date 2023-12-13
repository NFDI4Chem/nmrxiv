import { EllipsisVerticalIcon, ArrowDownTrayIcon, ScaleIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { Link, router } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createVNode, openBlock, createBlock, toDisplayString, createCommentVNode, Fragment, renderList, createTextVNode, Transition, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrRenderClass, ssrRenderStyle, ssrRenderList } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    Link,
    EllipsisVerticalIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    ArrowDownTrayIcon,
    ScaleIcon
  },
  props: ["project", "mode"],
  methods: {
    toggleUpVote() {
      if (this.$page.props.auth.user.username && this.$page.props.auth.user.username != "") {
        const url = "/projects/" + this.project.id + "/toggleUpVote";
        axios.get(url).catch((err) => {
          if (err.response.status !== 200 || err.response.status !== 201) {
            throw new Error(
              `API call failed with status code: ${err.response.status} after multiple attempts`
            );
          }
        }).then(function() {
          router.reload({ only: ["projects"] });
        });
      } else {
        this.$inertia.visit(route("login"));
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  const _component_Menu = resolveComponent("Menu");
  const _component_MenuButton = resolveComponent("MenuButton");
  const _component_EllipsisVerticalIcon = resolveComponent("EllipsisVerticalIcon");
  const _component_MenuItems = resolveComponent("MenuItems");
  const _component_MenuItem = resolveComponent("MenuItem");
  const _component_ArrowDownTrayIcon = resolveComponent("ArrowDownTrayIcon");
  const _component_ScaleIcon = resolveComponent("ScaleIcon");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  if ($props.project) {
    _push(`<div class="hover:shadow-lg">`);
    if ($props.mode == "mini" || $props.mode == "grid") {
      _push(`<div><div class="flex flex-col rounded-lg shadow-lg">`);
      _push(ssrRenderComponent(_component_Link, {
        href: $props.project.public_url,
        class: "block cursor-pointer"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="relative rounded-t-lg lg:h-36 xl:h-36"${_scopeId}>`);
            if ($props.project.photo_url && $props.project.photo_url != "") {
              _push2(`<img${ssrRenderAttr("src", $props.project.photo_url)} alt="" class="w-full h-full object-center rounded-t-lg object-cover"${_scopeId}>`);
            } else {
              _push2(`<div class="flex-shrink-0 lg:h-36 xl:h-36 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"${_scopeId}></div>`);
            }
            _push2(`<div class="absolute top-0 right-0"${_scopeId}><div class="p-2 flex items-center"${_scopeId}><div class="flex-shrink-0"${_scopeId}>`);
            if ($props.project.stats) {
              _push2(`<span class="relative z-0 inline-flex shadow-sm rounded-md"${_scopeId}><button type="button" class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"${_scopeId}></path></svg></button><a class="-ml-px relative inline-flex items-center px-2 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"${_scopeId}>${ssrInterpolate($props.project.stats.likes)}</a></span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "relative rounded-t-lg lg:h-36 xl:h-36" }, [
                $props.project.photo_url && $props.project.photo_url != "" ? (openBlock(), createBlock("img", {
                  key: 0,
                  src: $props.project.photo_url,
                  alt: "",
                  class: "w-full h-full object-center rounded-t-lg object-cover"
                }, null, 8, ["src"])) : (openBlock(), createBlock("div", {
                  key: 1,
                  class: "flex-shrink-0 lg:h-36 xl:h-36 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"
                })),
                createVNode("div", { class: "absolute top-0 right-0" }, [
                  createVNode("div", { class: "p-2 flex items-center" }, [
                    createVNode("div", { class: "flex-shrink-0" }, [
                      $props.project.stats ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "relative z-0 inline-flex shadow-sm rounded-md"
                      }, [
                        createVNode("button", {
                          type: "button",
                          class: "relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50",
                          onClick: ($event) => $options.toggleUpVote()
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
                        createVNode("a", { class: "-ml-px relative inline-flex items-center px-2 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" }, toDisplayString($props.project.stats.likes), 1)
                      ])) : createCommentVNode("", true)
                    ])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="${ssrRenderClass([
        $props.mode != "mini" ? "" : "rounded-b-lg",
        "flex-1 bg-white flex flex-col justify-between"
      ])}"><div style="${ssrRenderStyle({ "min-height": "168px" })}" class="flex-1 p-3">`);
      if ($props.project.identifier) {
        _push(`<small class="text-gray-500">#${ssrInterpolate($props.project.identifier)}</small>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_component_Link, {
        href: $props.project.public_url,
        class: "block cursor-pointer"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<p class="text-lg h-14 font-black text-gray-900 line-clamp-2"${_scopeId}>${ssrInterpolate($props.project.name)}</p><p class="text-xs text-gray-500 line-clamp-3"${_scopeId}>${ssrInterpolate($props.project.description)}</p><div class="mt-1 h-14 overflow-hidden"${_scopeId}><!--[-->`);
            ssrRenderList($props.project.tags, (tag) => {
              _push2(`<span class="mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"${_scopeId}>${ssrInterpolate(tag.name["en"])}</span>`);
            });
            _push2(`<!--]--></div>`);
          } else {
            return [
              createVNode("p", { class: "text-lg h-14 font-black text-gray-900 line-clamp-2" }, toDisplayString($props.project.name), 1),
              createVNode("p", { class: "text-xs text-gray-500 line-clamp-3" }, toDisplayString($props.project.description), 1),
              createVNode("div", { class: "mt-1 h-14 overflow-hidden" }, [
                (openBlock(true), createBlock(Fragment, null, renderList($props.project.tags, (tag) => {
                  return openBlock(), createBlock("span", {
                    key: tag.id,
                    class: "mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                  }, toDisplayString(tag.name["en"]), 1);
                }), 128))
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div>`);
      if ($props.mode != "mini") {
        _push(`<div class="p-3 rounded-b-lg bg-white border-t flex"><div class="flex-0.5 self-center align-middle"><img class="h-7 w-7 rounded-full"${ssrRenderAttr("src", $props.project.owner.profile_photo_url)}></div><div class="flex-auto pl-4 text-xs font-xs font-semibold text-black"><p class="text-ellipsis overflow-hidden ...">${ssrInterpolate($props.project.owner.first_name)} ${ssrInterpolate($props.project.owner.last_name)}</p><div class="flex-1 space-x-1 text-xs font-xs text-gray-500"><time datetime="2020-03-16">${ssrInterpolate(_ctx.formatDate($props.project.created_at))}</time></div></div><div class="flex-0.5 self-center">`);
        _push(ssrRenderComponent(_component_Menu, {
          as: "div",
          class: "relative text-left"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div${_scopeId}>`);
              _push2(ssrRenderComponent(_component_MenuButton, { class: "bg-white rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span class="sr-only"${_scopeId2}>Open options</span>`);
                    _push3(ssrRenderComponent(_component_EllipsisVerticalIcon, {
                      class: "h-5 w-5",
                      "aria-hidden": "true"
                    }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode("span", { class: "sr-only" }, "Open options"),
                      createVNode(_component_EllipsisVerticalIcon, {
                        class: "h-5 w-5",
                        "aria-hidden": "true"
                      })
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div>`);
              _push2(ssrRenderComponent(_component_MenuItems, { class: "absolute -right-2 mt-2 z-50 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div${_scopeId2}>`);
                    if ($props.project.download_url) {
                      _push3(ssrRenderComponent(_component_MenuItem, { class: "border-b" }, {
                        default: withCtx(({ active }, _push4, _parent4, _scopeId3) => {
                          if (_push4) {
                            _push4(`<a${ssrRenderAttr("href", $props.project.download_url)} class="${ssrRenderClass([
                              active ? "bg-gray-100 text-gray-600" : "text-gray-700",
                              "block px-4 py-4 text-sm cursor-pointer hover:text-gray-900"
                            ])}"${_scopeId3}>`);
                            _push4(ssrRenderComponent(_component_ArrowDownTrayIcon, {
                              class: "h-5 w-5 inline",
                              "aria-hidden": "true"
                            }, null, _parent4, _scopeId3));
                            _push4(` Download</a>`);
                          } else {
                            return [
                              createVNode("a", {
                                href: $props.project.download_url,
                                class: [
                                  active ? "bg-gray-100 text-gray-600" : "text-gray-700",
                                  "block px-4 py-4 text-sm cursor-pointer hover:text-gray-900"
                                ]
                              }, [
                                createVNode(_component_ArrowDownTrayIcon, {
                                  class: "h-5 w-5 inline",
                                  "aria-hidden": "true"
                                }),
                                createTextVNode(" Download")
                              ], 10, ["href"])
                            ];
                          }
                        }),
                        _: 1
                      }, _parent3, _scopeId2));
                    } else {
                      _push3(`<!---->`);
                    }
                    _push3(ssrRenderComponent(_component_MenuItem, null, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(`<div class="px-4 py-2"${_scopeId3}><p class="pb-2"${_scopeId3}><small class="text-gray-500"${_scopeId3}>License</small><br${_scopeId3}><span class="mt-2 float text-xs whitespace-nowrap border-gray-200 items-center py-1.5 bg-white text-gray-900 ellipsis ..."${_scopeId3}>${ssrInterpolate($props.project.license.title)}</span></p></div>`);
                        } else {
                          return [
                            createVNode("div", { class: "px-4 py-2" }, [
                              createVNode("p", { class: "pb-2" }, [
                                createVNode("small", { class: "text-gray-500" }, "License"),
                                createVNode("br"),
                                createVNode("span", { class: "mt-2 float text-xs whitespace-nowrap border-gray-200 items-center py-1.5 bg-white text-gray-900 ellipsis ..." }, toDisplayString($props.project.license.title), 1)
                              ])
                            ])
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(`</div>`);
                  } else {
                    return [
                      createVNode("div", null, [
                        $props.project.download_url ? (openBlock(), createBlock(_component_MenuItem, {
                          key: 0,
                          class: "border-b"
                        }, {
                          default: withCtx(({ active }) => [
                            createVNode("a", {
                              href: $props.project.download_url,
                              class: [
                                active ? "bg-gray-100 text-gray-600" : "text-gray-700",
                                "block px-4 py-4 text-sm cursor-pointer hover:text-gray-900"
                              ]
                            }, [
                              createVNode(_component_ArrowDownTrayIcon, {
                                class: "h-5 w-5 inline",
                                "aria-hidden": "true"
                              }),
                              createTextVNode(" Download")
                            ], 10, ["href"])
                          ]),
                          _: 1
                        })) : createCommentVNode("", true),
                        createVNode(_component_MenuItem, null, {
                          default: withCtx(() => [
                            createVNode("div", { class: "px-4 py-2" }, [
                              createVNode("p", { class: "pb-2" }, [
                                createVNode("small", { class: "text-gray-500" }, "License"),
                                createVNode("br"),
                                createVNode("span", { class: "mt-2 float text-xs whitespace-nowrap border-gray-200 items-center py-1.5 bg-white text-gray-900 ellipsis ..." }, toDisplayString($props.project.license.title), 1)
                              ])
                            ])
                          ]),
                          _: 1
                        })
                      ])
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              return [
                createVNode("div", null, [
                  createVNode(_component_MenuButton, { class: "bg-white rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" }, {
                    default: withCtx(() => [
                      createVNode("span", { class: "sr-only" }, "Open options"),
                      createVNode(_component_EllipsisVerticalIcon, {
                        class: "h-5 w-5",
                        "aria-hidden": "true"
                      })
                    ]),
                    _: 1
                  })
                ]),
                createVNode(Transition, {
                  "enter-active-class": "transition ease-out duration-100",
                  "enter-from-class": "transform opacity-0 scale-95",
                  "enter-to-class": "transform opacity-100 scale-100",
                  "leave-active-class": "transition ease-in duration-75",
                  "leave-from-class": "transform opacity-100 scale-100",
                  "leave-to-class": "transform opacity-0 scale-95"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_MenuItems, { class: "absolute -right-2 mt-2 z-50 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" }, {
                      default: withCtx(() => [
                        createVNode("div", null, [
                          $props.project.download_url ? (openBlock(), createBlock(_component_MenuItem, {
                            key: 0,
                            class: "border-b"
                          }, {
                            default: withCtx(({ active }) => [
                              createVNode("a", {
                                href: $props.project.download_url,
                                class: [
                                  active ? "bg-gray-100 text-gray-600" : "text-gray-700",
                                  "block px-4 py-4 text-sm cursor-pointer hover:text-gray-900"
                                ]
                              }, [
                                createVNode(_component_ArrowDownTrayIcon, {
                                  class: "h-5 w-5 inline",
                                  "aria-hidden": "true"
                                }),
                                createTextVNode(" Download")
                              ], 10, ["href"])
                            ]),
                            _: 1
                          })) : createCommentVNode("", true),
                          createVNode(_component_MenuItem, null, {
                            default: withCtx(() => [
                              createVNode("div", { class: "px-4 py-2" }, [
                                createVNode("p", { class: "pb-2" }, [
                                  createVNode("small", { class: "text-gray-500" }, "License"),
                                  createVNode("br"),
                                  createVNode("span", { class: "mt-2 float text-xs whitespace-nowrap border-gray-200 items-center py-1.5 bg-white text-gray-900 ellipsis ..." }, toDisplayString($props.project.license.title), 1)
                                ])
                              ])
                            ]),
                            _: 1
                          })
                        ])
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                })
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div>`);
    } else {
      _push(`<!---->`);
    }
    if ($props.mode == "list") {
      _push(`<div><li class="flex border-b bprder-gray-100"><div class="flex-shrink-0">`);
      if ($props.project.photo_url && $props.project.photo_url != "") {
        _push(`<img${ssrRenderAttr("src", $props.project.photo_url)} alt="" class="border w-36 lg:h-36 xl:h-36 m-2 mr-0 h-full object-center object-cover">`);
      } else {
        _push(`<div class="w-36 m-2 flex-shrink-0 border mr-0 lg:h-36 xl:h-36 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"></div>`);
      }
      _push(`</div><div class="flex-1 flex flex-col px-4 py-2 sm:px-6 justify-between"><div class="flex items-center justify-between"><div>`);
      if ($props.project.identifier) {
        _push(`<small class="text-gray-500">#${ssrInterpolate($props.project.identifier)}</small>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<p class="text-lg font-black text-gray-900 line-clamp-2 font-black">`);
      _push(ssrRenderComponent(_component_Link, {
        href: $props.project.public_url,
        class: "block cursor-pointer"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate($props.project.name)}`);
          } else {
            return [
              createTextVNode(toDisplayString($props.project.name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</p><p class="my-2 text-sm text-gray-500 line-clamp-2 pr-8">${ssrInterpolate($props.project.description)}</p></div><div class="ml-2 flex-shrink-0 flex"><div><div class="p-2 flex items-center"><div class="flex-shrink-0">`);
      if ($props.project.stats) {
        _push(`<span class="relative z-0 inline-flex shadow-sm rounded-md"><button type="button" class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-900 hover:bg-gray-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg></button><a class="-ml-px relative inline-flex items-center px-2 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">${ssrInterpolate($props.project.stats.likes)}</a></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></div></div><div class="mt-2 sm:flex sm:justify-between"><div class="sm:flex"><p class="flex items-center text-sm text-gray-500"><img class="h-7 w-5 mr-2 rounded-full"${ssrRenderAttr("src", $props.project.owner.profile_photo_url)}> ${ssrInterpolate($props.project.owner.first_name)} ${ssrInterpolate($props.project.owner.last_name)}</p><p class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-3">`);
      _push(ssrRenderComponent(_component_ScaleIcon, { class: "text-gray-400 h-5 w-5 mr-1.5" }, null, _parent));
      _push(` ${ssrInterpolate($props.project.license.title)}</p>`);
      if ($props.project.download_url) {
        _push(`<p class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-2"><a${ssrRenderAttr("href", $props.project.download_url)} class="block px-4 text-sm cursor-pointer hover:text-gray-900&#39;, ]">`);
        _push(ssrRenderComponent(_component_ArrowDownTrayIcon, {
          class: "h-5 w-5 inline",
          "aria-hidden": "true"
        }, null, _parent));
        _push(`</a></p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"><svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg><p><time datetime="2020-03-16">${ssrInterpolate(_ctx.formatDate($props.project.created_at))}</time></p></div></div></div></li></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/ProjectCard.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const ProjectCard = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  ProjectCard as P
};
