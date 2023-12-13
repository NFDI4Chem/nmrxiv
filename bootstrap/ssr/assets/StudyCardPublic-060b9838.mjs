import { LockClosedIcon, LockOpenIcon, EnvelopeIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import { D as Depictor2D } from "./Depictor2D-b60253ae.mjs";
import { resolveComponent, mergeProps, withCtx, createVNode, openBlock, createBlock, Fragment, renderList, createCommentVNode, withModifiers, toDisplayString, createTextVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrRenderList, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main = {
  components: {
    LockClosedIcon,
    LockOpenIcon,
    EnvelopeIcon,
    PencilIcon,
    Link,
    Depictor2D
  },
  props: ["study", "project"],
  setup() {
  },
  data() {
    return {
      selectedPreviewIndex: 0
    };
  },
  methods: {}
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  const _component_LockOpenIcon = resolveComponent("LockOpenIcon");
  const _component_LockClosedIcon = resolveComponent("LockClosedIcon");
  if ($props.study) {
    _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col border rounded-lg shadow-lg transition ease-in-out delay-150 duration-300 overflow-hidden" }, _attrs))}>`);
    _push(ssrRenderComponent(_component_Link, {
      href: $props.study.public_url
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div${_scopeId}><div${_scopeId}>`);
          if ($props.study.study_preview_urls && $props.study.study_preview_urls.length > 0) {
            _push2(`<span${_scopeId}>`);
            if ($props.study.study_preview_urls.length == 1) {
              _push2(`<span${_scopeId}><img class="h-48 w-full rounded-t-md"${ssrRenderAttr("src", $props.study.study_preview_urls[0])} alt=""${_scopeId}></span>`);
            } else {
              _push2(`<span${_scopeId}><div class="relative"${_scopeId}><!--[-->`);
              ssrRenderList($props.study.study_preview_urls, (url, index) => {
                _push2(`<span${_scopeId}>`);
                if (index == $data.selectedPreviewIndex) {
                  _push2(`<span${_scopeId}><img class="h-48 w-full rounded-t-md"${ssrRenderAttr("src", url)} alt=""${_scopeId}></span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</span>`);
              });
              _push2(`<!--]--><div class="absolute w-full py-2.5 bottom-0 inset-x-0 pl-4 text-white text-xs text-center leading-4"${_scopeId}><div${_scopeId}><ol role="list" class="flex items-center space-x-2"${_scopeId}><!--[-->`);
              ssrRenderList($props.study.study_preview_urls, (url, index) => {
                _push2(`<li${_scopeId}>`);
                if (index === $data.selectedPreviewIndex) {
                  _push2(`<a class="block w-2.5 h-2.5 bg-teal-200 rounded-full hover:bg-teal-400"${_scopeId}></a>`);
                } else {
                  _push2(`<a class="cursor-pointer block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"${_scopeId}></a>`);
                }
                _push2(`</li>`);
              });
              _push2(`<!--]--></ol></div></div></div><span class="bg-gradient-to-r from-cyan-500 to-blue-500"${_scopeId}></span></span>`);
            }
            _push2(`</span>`);
          } else {
            _push2(`<span${_scopeId}><img class="h-48 w-full rounded-t-md" src="https://via.placeholder.com/340x180/FFF/f1f1f4?text=No preview" alt=""${_scopeId}></span>`);
          }
          _push2(`</div></div><div class="flex-1 bg-white p-3 border-t mt-1 flex flex-col justify-between"${_scopeId}><div${_scopeId}>`);
          if ($props.study.identifier) {
            _push2(`<small class="float-left text-gray-500"${_scopeId}>#${ssrInterpolate($props.study.identifier)}</small>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`<div class="float-right"${_scopeId}>`);
          if ($props.study.is_public) {
            _push2(`<div class="flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_LockOpenIcon, { class: "w-3 h-3 mr-1 text-green-600" }, null, _parent2, _scopeId));
            _push2(`<p class="text-xs text-gray-600"${_scopeId}>Public</p></div>`);
          } else {
            _push2(`<div class="flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_LockClosedIcon, { class: "w-3 h-3 mr-1 text-teal-600" }, null, _parent2, _scopeId));
            _push2(`<p class="text-xs text-gray-600"${_scopeId}>Private</p></div>`);
          }
          _push2(`</div></div><div class="flex-1"${_scopeId}><p class="text-lg mt-2 font-semibold text-gray-900 line-clamp-1"${_scopeId}>${ssrInterpolate($props.study.name)}</p>`);
          if ($props.study.experiment_types && $props.study.experiment_types.length > 0) {
            _push2(`<div class="mt-1 h-14 overflow-hidden"${_scopeId}><!--[-->`);
            ssrRenderList($props.study.experiment_types, (type) => {
              _push2(`<div${_scopeId}>`);
              if (type && type != null) {
                _push2(`<span class="mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"${_scopeId}>${ssrInterpolate(type)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            });
            _push2(`<!--]--></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div><div class="pt-1"${_scopeId}><div class="text-xs text-gray-600"${_scopeId}><span class="text-gray-400"${_scopeId}>Last updated on</span> ${ssrInterpolate(_ctx.formatDate($props.study.updated_at))}</div></div></div>`);
        } else {
          return [
            createVNode("div", null, [
              createVNode("div", null, [
                $props.study.study_preview_urls && $props.study.study_preview_urls.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                  $props.study.study_preview_urls.length == 1 ? (openBlock(), createBlock("span", { key: 0 }, [
                    createVNode("img", {
                      class: "h-48 w-full rounded-t-md",
                      src: $props.study.study_preview_urls[0],
                      alt: ""
                    }, null, 8, ["src"])
                  ])) : (openBlock(), createBlock("span", { key: 1 }, [
                    createVNode("div", { class: "relative" }, [
                      (openBlock(true), createBlock(Fragment, null, renderList($props.study.study_preview_urls, (url, index) => {
                        return openBlock(), createBlock("span", { key: url }, [
                          index == $data.selectedPreviewIndex ? (openBlock(), createBlock("span", { key: 0 }, [
                            createVNode("img", {
                              class: "h-48 w-full rounded-t-md",
                              src: url,
                              alt: ""
                            }, null, 8, ["src"])
                          ])) : createCommentVNode("", true)
                        ]);
                      }), 128)),
                      createVNode("div", { class: "absolute w-full py-2.5 bottom-0 inset-x-0 pl-4 text-white text-xs text-center leading-4" }, [
                        createVNode("div", null, [
                          createVNode("ol", {
                            role: "list",
                            class: "flex items-center space-x-2"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList($props.study.study_preview_urls, (url, index) => {
                              return openBlock(), createBlock("li", { key: index }, [
                                index === $data.selectedPreviewIndex ? (openBlock(), createBlock("a", {
                                  key: 0,
                                  class: "block w-2.5 h-2.5 bg-teal-200 rounded-full hover:bg-teal-400",
                                  onClick: withModifiers(($event) => $data.selectedPreviewIndex = index, ["prevent"])
                                }, null, 8, ["onClick"])) : (openBlock(), createBlock("a", {
                                  key: 1,
                                  class: "cursor-pointer block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400",
                                  onClick: withModifiers(($event) => $data.selectedPreviewIndex = index, ["prevent"])
                                }, null, 8, ["onClick"]))
                              ]);
                            }), 128))
                          ])
                        ])
                      ])
                    ]),
                    createVNode("span", { class: "bg-gradient-to-r from-cyan-500 to-blue-500" })
                  ]))
                ])) : (openBlock(), createBlock("span", { key: 1 }, [
                  createVNode("img", {
                    class: "h-48 w-full rounded-t-md",
                    src: "https://via.placeholder.com/340x180/FFF/f1f1f4?text=No preview",
                    alt: ""
                  })
                ]))
              ])
            ]),
            createVNode("div", { class: "flex-1 bg-white p-3 border-t mt-1 flex flex-col justify-between" }, [
              createVNode("div", null, [
                $props.study.identifier ? (openBlock(), createBlock("small", {
                  key: 0,
                  class: "float-left text-gray-500"
                }, "#" + toDisplayString($props.study.identifier), 1)) : createCommentVNode("", true),
                createVNode("div", { class: "float-right" }, [
                  $props.study.is_public ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "flex items-center"
                  }, [
                    createVNode(_component_LockOpenIcon, { class: "w-3 h-3 mr-1 text-green-600" }),
                    createVNode("p", { class: "text-xs text-gray-600" }, "Public")
                  ])) : (openBlock(), createBlock("div", {
                    key: 1,
                    class: "flex items-center"
                  }, [
                    createVNode(_component_LockClosedIcon, { class: "w-3 h-3 mr-1 text-teal-600" }),
                    createVNode("p", { class: "text-xs text-gray-600" }, "Private")
                  ]))
                ])
              ]),
              createVNode("div", { class: "flex-1" }, [
                createVNode("p", { class: "text-lg mt-2 font-semibold text-gray-900 line-clamp-1" }, toDisplayString($props.study.name), 1),
                $props.study.experiment_types && $props.study.experiment_types.length > 0 ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "mt-1 h-14 overflow-hidden"
                }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($props.study.experiment_types, (type) => {
                    return openBlock(), createBlock("div", { key: type }, [
                      type && type != null ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                      }, toDisplayString(type), 1)) : createCommentVNode("", true)
                    ]);
                  }), 128))
                ])) : createCommentVNode("", true)
              ]),
              createVNode("div", { class: "pt-1" }, [
                createVNode("div", { class: "text-xs text-gray-600" }, [
                  createVNode("span", { class: "text-gray-400" }, "Last updated on"),
                  createTextVNode(" " + toDisplayString(_ctx.formatDate($props.study.updated_at)), 1)
                ])
              ])
            ])
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/StudyCardPublic.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const StudyPublicCard = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  StudyPublicCard as S
};
