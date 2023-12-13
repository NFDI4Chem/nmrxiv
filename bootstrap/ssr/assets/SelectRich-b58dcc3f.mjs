import { resolveComponent, mergeProps, withCtx, createTextVNode, toDisplayString, openBlock, createBlock, createVNode, createCommentVNode, Fragment, renderList, Transition, useSSRContext } from "vue";
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/24/solid";
import { T as ToolTip } from "./ToolTip-cf9882a3.mjs";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
    CheckIcon,
    ChevronUpDownIcon,
    ToolTip
  },
  props: ["items", "selected", "label"],
  computed: {
    proxySelected: {
      get() {
        return this.selected;
      },
      set(val) {
        this.$emit("update:selected", val);
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Listbox = resolveComponent("Listbox");
  const _component_ListboxLabel = resolveComponent("ListboxLabel");
  const _component_ListboxButton = resolveComponent("ListboxButton");
  const _component_ChevronUpDownIcon = resolveComponent("ChevronUpDownIcon");
  const _component_ListboxOptions = resolveComponent("ListboxOptions");
  const _component_ListboxOption = resolveComponent("ListboxOption");
  const _component_CheckIcon = resolveComponent("CheckIcon");
  _push(ssrRenderComponent(_component_Listbox, mergeProps({
    modelValue: $options.proxySelected,
    "onUpdate:modelValue": ($event) => $options.proxySelected = $event,
    as: "div"
  }, _attrs), {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($props.label) {
          _push2(ssrRenderComponent(_component_ListboxLabel, { class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500" }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`${ssrInterpolate($props.label)}`);
              } else {
                return [
                  createTextVNode(toDisplayString($props.label), 1)
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="mt-1 relative"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_ListboxButton, { class: "relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 sm:text-sm" }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              if ($props.selected) {
                _push3(`<span class="block truncate"${_scopeId2}>${ssrInterpolate($props.selected.title)}</span>`);
              } else {
                _push3(`<span class="block truncate"${_scopeId2}>--Select--</span>`);
              }
              _push3(`<span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_ChevronUpDownIcon, {
                class: "h-5 w-5 text-gray-400",
                "aria-hidden": "true"
              }, null, _parent3, _scopeId2));
              _push3(`</span>`);
            } else {
              return [
                $props.selected ? (openBlock(), createBlock("span", {
                  key: 0,
                  class: "block truncate"
                }, toDisplayString($props.selected.title), 1)) : (openBlock(), createBlock("span", {
                  key: 1,
                  class: "block truncate"
                }, "--Select--")),
                createVNode("span", { class: "absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none" }, [
                  createVNode(_component_ChevronUpDownIcon, {
                    class: "h-5 w-5 text-gray-400",
                    "aria-hidden": "true"
                  })
                ])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_ListboxOptions, { class: "absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<!--[-->`);
              ssrRenderList($props.items, (item) => {
                _push3(ssrRenderComponent(_component_ListboxOption, {
                  key: item.id,
                  as: "template",
                  value: item
                }, {
                  default: withCtx(({ active, selected }, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(`<li class="${ssrRenderClass([
                        active ? "text-white bg-teal-600" : "text-gray-900",
                        "cursor-default border-b select-none relative py-2 pl-8 pr-4"
                      ])}"${_scopeId3}><span class="${ssrRenderClass([
                        selected ? "font-semibold" : "font-normal",
                        "block truncate"
                      ])}"${_scopeId3}><b${_scopeId3}>${ssrInterpolate(item.title)}</b> <br${_scopeId3}>`);
                      if (item.description) {
                        _push4(`<small${_scopeId3}>${item.description}</small>`);
                      } else {
                        _push4(`<!---->`);
                      }
                      _push4(`</span>`);
                      if (selected) {
                        _push4(`<span class="${ssrRenderClass([
                          active ? "text-white" : "text-teal-600",
                          "absolute inset-y-0 left-0 flex items-center pl-1.5"
                        ])}"${_scopeId3}>`);
                        _push4(ssrRenderComponent(_component_CheckIcon, {
                          class: "h-5 w-5",
                          "aria-hidden": "true"
                        }, null, _parent4, _scopeId3));
                        _push4(`</span>`);
                      } else {
                        _push4(`<!---->`);
                      }
                      _push4(`</li>`);
                    } else {
                      return [
                        createVNode("li", {
                          class: [
                            active ? "text-white bg-teal-600" : "text-gray-900",
                            "cursor-default border-b select-none relative py-2 pl-8 pr-4"
                          ]
                        }, [
                          createVNode("span", {
                            class: [
                              selected ? "font-semibold" : "font-normal",
                              "block truncate"
                            ]
                          }, [
                            createVNode("b", null, toDisplayString(item.title), 1),
                            createTextVNode(),
                            createVNode("br"),
                            item.description ? (openBlock(), createBlock("small", {
                              key: 0,
                              innerHTML: item.description
                            }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
                          ], 2),
                          selected ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: [
                              active ? "text-white" : "text-teal-600",
                              "absolute inset-y-0 left-0 flex items-center pl-1.5"
                            ]
                          }, [
                            createVNode(_component_CheckIcon, {
                              class: "h-5 w-5",
                              "aria-hidden": "true"
                            })
                          ], 2)) : createCommentVNode("", true)
                        ], 2)
                      ];
                    }
                  }),
                  _: 2
                }, _parent3, _scopeId2));
              });
              _push3(`<!--]-->`);
            } else {
              return [
                (openBlock(true), createBlock(Fragment, null, renderList($props.items, (item) => {
                  return openBlock(), createBlock(_component_ListboxOption, {
                    key: item.id,
                    as: "template",
                    value: item
                  }, {
                    default: withCtx(({ active, selected }) => [
                      createVNode("li", {
                        class: [
                          active ? "text-white bg-teal-600" : "text-gray-900",
                          "cursor-default border-b select-none relative py-2 pl-8 pr-4"
                        ]
                      }, [
                        createVNode("span", {
                          class: [
                            selected ? "font-semibold" : "font-normal",
                            "block truncate"
                          ]
                        }, [
                          createVNode("b", null, toDisplayString(item.title), 1),
                          createTextVNode(),
                          createVNode("br"),
                          item.description ? (openBlock(), createBlock("small", {
                            key: 0,
                            innerHTML: item.description
                          }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
                        ], 2),
                        selected ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: [
                            active ? "text-white" : "text-teal-600",
                            "absolute inset-y-0 left-0 flex items-center pl-1.5"
                          ]
                        }, [
                          createVNode(_component_CheckIcon, {
                            class: "h-5 w-5",
                            "aria-hidden": "true"
                          })
                        ], 2)) : createCommentVNode("", true)
                      ], 2)
                    ]),
                    _: 2
                  }, 1032, ["value"]);
                }), 128))
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          $props.label ? (openBlock(), createBlock(_component_ListboxLabel, {
            key: 0,
            class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
          }, {
            default: withCtx(() => [
              createTextVNode(toDisplayString($props.label), 1)
            ]),
            _: 1
          })) : createCommentVNode("", true),
          createVNode("div", { class: "mt-1 relative" }, [
            createVNode(_component_ListboxButton, { class: "relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 sm:text-sm" }, {
              default: withCtx(() => [
                $props.selected ? (openBlock(), createBlock("span", {
                  key: 0,
                  class: "block truncate"
                }, toDisplayString($props.selected.title), 1)) : (openBlock(), createBlock("span", {
                  key: 1,
                  class: "block truncate"
                }, "--Select--")),
                createVNode("span", { class: "absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none" }, [
                  createVNode(_component_ChevronUpDownIcon, {
                    class: "h-5 w-5 text-gray-400",
                    "aria-hidden": "true"
                  })
                ])
              ]),
              _: 1
            }),
            createVNode(Transition, {
              "leave-active-class": "transition ease-in duration-100",
              "leave-from-class": "opacity-100",
              "leave-to-class": "opacity-0"
            }, {
              default: withCtx(() => [
                createVNode(_component_ListboxOptions, { class: "absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" }, {
                  default: withCtx(() => [
                    (openBlock(true), createBlock(Fragment, null, renderList($props.items, (item) => {
                      return openBlock(), createBlock(_component_ListboxOption, {
                        key: item.id,
                        as: "template",
                        value: item
                      }, {
                        default: withCtx(({ active, selected }) => [
                          createVNode("li", {
                            class: [
                              active ? "text-white bg-teal-600" : "text-gray-900",
                              "cursor-default border-b select-none relative py-2 pl-8 pr-4"
                            ]
                          }, [
                            createVNode("span", {
                              class: [
                                selected ? "font-semibold" : "font-normal",
                                "block truncate"
                              ]
                            }, [
                              createVNode("b", null, toDisplayString(item.title), 1),
                              createTextVNode(),
                              createVNode("br"),
                              item.description ? (openBlock(), createBlock("small", {
                                key: 0,
                                innerHTML: item.description
                              }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
                            ], 2),
                            selected ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: [
                                active ? "text-white" : "text-teal-600",
                                "absolute inset-y-0 left-0 flex items-center pl-1.5"
                              ]
                            }, [
                              createVNode(_component_CheckIcon, {
                                class: "h-5 w-5",
                                "aria-hidden": "true"
                              })
                            ], 2)) : createCommentVNode("", true)
                          ], 2)
                        ]),
                        _: 2
                      }, 1032, ["value"]);
                    }), 128))
                  ]),
                  _: 1
                })
              ]),
              _: 1
            })
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SelectRich.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SelectRich = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  SelectRich as S
};
