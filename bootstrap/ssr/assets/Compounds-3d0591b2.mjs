import { A as AppLayout, J as JetDropdownLink } from "./AppLayout-b93d1c11.mjs";
import { D as Depictor2D } from "./Depictor2D-b60253ae.mjs";
import { Link } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createVNode, openBlock, createBlock, Fragment, renderList, toDisplayString, useSSRContext, ref, withDirectives, vModelRadio, createTextVNode, mergeProps, vModelText, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrInterpolate, ssrRenderStyle, ssrIncludeBooleanAttr, ssrLooseEqual, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import OCL from "openchemlib/full.js";
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, Dialog, DialogPanel, TransitionChild, TransitionRoot, Menu, MenuItems, MenuButton } from "@headlessui/vue";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
import "@vueuse/core";
import "@heroicons/vue/24/outline";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "./Button-8d6976fd.mjs";
import "@sipec/vue3-tags-input";
import "vue3-slider";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./InputError-6ae0b65c.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
const _sfc_main$2 = {
  components: {
    Depictor2D,
    Link
  },
  props: ["molecule"],
  computed: {},
  data() {
    return {
      results: []
    };
  },
  mounted() {
  },
  methods: {}
};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  const _component_Depictor2D = resolveComponent("Depictor2D");
  _push(`<a${ssrRenderAttrs(_attrs)}><div>`);
  _push(ssrRenderComponent(_component_Link, {
    href: "/spectra?compound=" + $props.molecule.identifier
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="rounded overflow-hidden"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Depictor2D, {
          class: "p-4 border-b",
          molecule: $props.molecule.canonical_smiles
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="py-4 px-4"${_scopeId}><div class="flex items-center"${_scopeId}><div class="mb-1 flex items-center"${_scopeId}><!--[-->`);
        ssrRenderList($props.molecule.annotation_level, (index) => {
          _push2(`<svg class="inline text-yellow-400 h-4 w-4 flex-shrink-0" x-state:on="Active" x-state:off="Inactive" x-state-description="Active: &quot;text-yellow-400&quot;, Inactive: &quot;text-gray-200&quot;" x-description="Heroicon name: mini/star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"${_scopeId}></path></svg>`);
        });
        _push2(`<!--]--><!--[-->`);
        ssrRenderList(5 - $props.molecule.annotation_level, (index) => {
          _push2(`<svg class="inline text-gray-200 h-4 w-4 flex-shrink-0" x-state-description="undefined: &quot;text-yellow-400&quot;, undefined: &quot;text-gray-200&quot;" x-description="Heroicon name: mini/star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"${_scopeId}></path></svg>`);
        });
        _push2(`<!--]--></div></div><div class="font-bold text-md mb-2"${_scopeId}><a class="font-semibold text-gray-600 hover:underline"${_scopeId}>#NMRXIV:${ssrInterpolate($props.molecule.identifier)}</a></div>`);
        if ($props.molecule.iupac_name && $props.molecule.iupac_name != "") {
          _push2(`<div class="text-gray-700 text-base break-all text-sm capitalize"${_scopeId}>${ssrInterpolate($props.molecule.iupac_name)}</div>`);
        } else {
          _push2(`<div class="text-gray-700 text-base break-all text-sm"${_scopeId}>${ssrInterpolate($props.molecule.canonical_smiles)}</div>`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "rounded overflow-hidden" }, [
            createVNode(_component_Depictor2D, {
              class: "p-4 border-b",
              molecule: $props.molecule.canonical_smiles
            }, null, 8, ["molecule"])
          ]),
          createVNode("div", { class: "py-4 px-4" }, [
            createVNode("div", { class: "flex items-center" }, [
              createVNode("div", { class: "mb-1 flex items-center" }, [
                (openBlock(true), createBlock(Fragment, null, renderList($props.molecule.annotation_level, (index) => {
                  return openBlock(), createBlock("svg", {
                    key: index,
                    class: "inline text-yellow-400 h-4 w-4 flex-shrink-0",
                    "x-state:on": "Active",
                    "x-state:off": "Inactive",
                    "x-state-description": 'Active: "text-yellow-400", Inactive: "text-gray-200"',
                    "x-description": "Heroicon name: mini/star",
                    xmlns: "http://www.w3.org/2000/svg",
                    viewBox: "0 0 20 20",
                    fill: "currentColor",
                    "aria-hidden": "true"
                  }, [
                    createVNode("path", {
                      "fill-rule": "evenodd",
                      d: "M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z",
                      "clip-rule": "evenodd"
                    })
                  ]);
                }), 128)),
                (openBlock(true), createBlock(Fragment, null, renderList(5 - $props.molecule.annotation_level, (index) => {
                  return openBlock(), createBlock("svg", {
                    key: index,
                    class: "inline text-gray-200 h-4 w-4 flex-shrink-0",
                    "x-state-description": 'undefined: "text-yellow-400", undefined: "text-gray-200"',
                    "x-description": "Heroicon name: mini/star",
                    xmlns: "http://www.w3.org/2000/svg",
                    viewBox: "0 0 20 20",
                    fill: "currentColor",
                    "aria-hidden": "true"
                  }, [
                    createVNode("path", {
                      "fill-rule": "evenodd",
                      d: "M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z",
                      "clip-rule": "evenodd"
                    })
                  ]);
                }), 128))
              ])
            ]),
            createVNode("div", { class: "font-bold text-md mb-2" }, [
              createVNode("a", { class: "font-semibold text-gray-600 hover:underline" }, "#NMRXIV:" + toDisplayString($props.molecule.identifier), 1)
            ]),
            $props.molecule.iupac_name && $props.molecule.iupac_name != "" ? (openBlock(), createBlock("div", {
              key: 0,
              class: "text-gray-700 text-base break-all text-sm capitalize"
            }, toDisplayString($props.molecule.iupac_name), 1)) : (openBlock(), createBlock("div", {
              key: 1,
              class: "text-gray-700 text-base break-all text-sm"
            }, toDisplayString($props.molecule.canonical_smiles), 1))
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></a>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/App/MoleculeCard.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const MoleculeCard = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$1 = {
  components: {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot
  },
  props: [],
  computed: {},
  data() {
    return {
      editor: "",
      open: ref(false),
      query: ref(""),
      smiles: null,
      type: "exact"
    };
  },
  mounted() {
  },
  methods: {
    openDialog(value) {
      this.open = value;
      if (value) {
        this.$nextTick(() => {
          this.editor = OCL.StructureEditor.createSVGEditor(
            "structureSearchEditor",
            1
          );
        });
      }
    },
    search() {
      this.$page.props.query = this.editor.getSmiles();
      this.$page.props.queryType = this.type;
      window.location = "/compounds/?query=" + encodeURI(this.editor.getSmiles()) + "&type=" + this.type;
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_TransitionRoot = resolveComponent("TransitionRoot");
  const _component_Dialog = resolveComponent("Dialog");
  const _component_TransitionChild = resolveComponent("TransitionChild");
  const _component_DialogPanel = resolveComponent("DialogPanel");
  _push(`<div${ssrRenderAttrs(_attrs)}><button type="button" class="px-4 shadow py-3 rounded-full bg-white p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-3 ml-2 h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"></path></svg></button>`);
  _push(ssrRenderComponent(_component_TransitionRoot, {
    show: $data.open,
    as: "template",
    onAfterLeave: ($event) => $data.query = "",
    appear: ""
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_Dialog, {
          as: "div",
          class: "relative z-10",
          onClose: ($event) => $data.open = false
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(`<div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"${_scopeId3}></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0 scale-95",
                "enter-to": "opacity-100 scale-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100 scale-100",
                "leave-to": "opacity-0 scale-95"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(ssrRenderComponent(_component_DialogPanel, { class: "mx-auto max-w-3xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6"${_scopeId4}><h3 class="text-lg font-medium leading-6 text-gray-900"${_scopeId4}> Structure Search </h3></div><div class="p-4"${_scopeId4}><div id="structureSearchEditor" class="w-full border rounded-md" style="${ssrRenderStyle({ "height": "400px" })}"${_scopeId4}></div><div class="mt-6"${_scopeId4}><fieldset class="mt-6"${_scopeId4}><legend class="contents text-base font-medium text-gray-900"${_scopeId4}> Select search type </legend><div class="mt-4 space-y-4"${_scopeId4}><div class="flex items-center"${_scopeId4}><label for="search-type-exact" class="block cursor-pointer text-sm font-medium text-gray-700"${_scopeId4}><input id="search-type-exact" name="search-type"${ssrIncludeBooleanAttr(ssrLooseEqual($data.type, "exact")) ? " checked" : ""} value="exact" type="radio" class="mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"${_scopeId4}> Exact match</label></div><div class="flex items-center"${_scopeId4}><label for="search-type-sub" class="block cursor-pointer text-sm font-medium text-gray-700"${_scopeId4}><input id="search-type-sub" name="search-type"${ssrIncludeBooleanAttr(ssrLooseEqual($data.type, "substructure")) ? " checked" : ""} type="radio" value="substructure" class="mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"${_scopeId4}>Substructure Search</label></div><div class="flex items-center"${_scopeId4}><label for="search-type-similar" class="block cursor-pointer text-sm font-medium text-gray-700"${_scopeId4}><input id="search-type-similar"${ssrIncludeBooleanAttr(ssrLooseEqual($data.type, "similarity")) ? " checked" : ""} name="search-type" value="similarity" type="radio" class="mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"${_scopeId4}>Similarity Search (tanimoto_threshold=0.5)</label></div></div></fieldset></div></div><div class="p-4"${_scopeId4}><div class="flex justify-end"${_scopeId4}><button type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"${_scopeId4}> Cancel </button><button class="ml-3 inline-flex justify-center rounded-md bg-gray-700 text-white py-2 px-4 text-sm font-medium shadow-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"${_scopeId4}> Search </button></div></div>`);
                        } else {
                          return [
                            createVNode("div", { class: "border-b border-gray-200 bg-white px-4 py-5 sm:px-6" }, [
                              createVNode("h3", { class: "text-lg font-medium leading-6 text-gray-900" }, " Structure Search ")
                            ]),
                            createVNode("div", { class: "p-4" }, [
                              createVNode("div", {
                                id: "structureSearchEditor",
                                class: "w-full border rounded-md",
                                style: { "height": "400px" }
                              }),
                              createVNode("div", { class: "mt-6" }, [
                                createVNode("fieldset", { class: "mt-6" }, [
                                  createVNode("legend", { class: "contents text-base font-medium text-gray-900" }, " Select search type "),
                                  createVNode("div", { class: "mt-4 space-y-4" }, [
                                    createVNode("div", { class: "flex items-center" }, [
                                      createVNode("label", {
                                        for: "search-type-exact",
                                        class: "block cursor-pointer text-sm font-medium text-gray-700"
                                      }, [
                                        withDirectives(createVNode("input", {
                                          id: "search-type-exact",
                                          name: "search-type",
                                          "onUpdate:modelValue": ($event) => $data.type = $event,
                                          value: "exact",
                                          type: "radio",
                                          class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                        }, null, 8, ["onUpdate:modelValue"]), [
                                          [vModelRadio, $data.type]
                                        ]),
                                        createTextVNode(" Exact match")
                                      ])
                                    ]),
                                    createVNode("div", { class: "flex items-center" }, [
                                      createVNode("label", {
                                        for: "search-type-sub",
                                        class: "block cursor-pointer text-sm font-medium text-gray-700"
                                      }, [
                                        withDirectives(createVNode("input", {
                                          id: "search-type-sub",
                                          name: "search-type",
                                          "onUpdate:modelValue": ($event) => $data.type = $event,
                                          type: "radio",
                                          value: "substructure",
                                          class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                        }, null, 8, ["onUpdate:modelValue"]), [
                                          [vModelRadio, $data.type]
                                        ]),
                                        createTextVNode("Substructure Search")
                                      ])
                                    ]),
                                    createVNode("div", { class: "flex items-center" }, [
                                      createVNode("label", {
                                        for: "search-type-similar",
                                        class: "block cursor-pointer text-sm font-medium text-gray-700"
                                      }, [
                                        withDirectives(createVNode("input", {
                                          id: "search-type-similar",
                                          "onUpdate:modelValue": ($event) => $data.type = $event,
                                          name: "search-type",
                                          value: "similarity",
                                          type: "radio",
                                          class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                        }, null, 8, ["onUpdate:modelValue"]), [
                                          [vModelRadio, $data.type]
                                        ]),
                                        createTextVNode("Similarity Search (tanimoto_threshold=0.5)")
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ]),
                            createVNode("div", { class: "p-4" }, [
                              createVNode("div", { class: "flex justify-end" }, [
                                createVNode("button", {
                                  onClick: ($event) => $options.openDialog(false),
                                  type: "button",
                                  class: "rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"
                                }, " Cancel ", 8, ["onClick"]),
                                createVNode("button", {
                                  class: "ml-3 inline-flex justify-center rounded-md bg-gray-700 text-white py-2 px-4 text-sm font-medium shadow-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2",
                                  onClick: $options.search
                                }, " Search ", 8, ["onClick"])
                              ])
                            ])
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_DialogPanel, { class: "mx-auto max-w-3xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "border-b border-gray-200 bg-white px-4 py-5 sm:px-6" }, [
                            createVNode("h3", { class: "text-lg font-medium leading-6 text-gray-900" }, " Structure Search ")
                          ]),
                          createVNode("div", { class: "p-4" }, [
                            createVNode("div", {
                              id: "structureSearchEditor",
                              class: "w-full border rounded-md",
                              style: { "height": "400px" }
                            }),
                            createVNode("div", { class: "mt-6" }, [
                              createVNode("fieldset", { class: "mt-6" }, [
                                createVNode("legend", { class: "contents text-base font-medium text-gray-900" }, " Select search type "),
                                createVNode("div", { class: "mt-4 space-y-4" }, [
                                  createVNode("div", { class: "flex items-center" }, [
                                    createVNode("label", {
                                      for: "search-type-exact",
                                      class: "block cursor-pointer text-sm font-medium text-gray-700"
                                    }, [
                                      withDirectives(createVNode("input", {
                                        id: "search-type-exact",
                                        name: "search-type",
                                        "onUpdate:modelValue": ($event) => $data.type = $event,
                                        value: "exact",
                                        type: "radio",
                                        class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                      }, null, 8, ["onUpdate:modelValue"]), [
                                        [vModelRadio, $data.type]
                                      ]),
                                      createTextVNode(" Exact match")
                                    ])
                                  ]),
                                  createVNode("div", { class: "flex items-center" }, [
                                    createVNode("label", {
                                      for: "search-type-sub",
                                      class: "block cursor-pointer text-sm font-medium text-gray-700"
                                    }, [
                                      withDirectives(createVNode("input", {
                                        id: "search-type-sub",
                                        name: "search-type",
                                        "onUpdate:modelValue": ($event) => $data.type = $event,
                                        type: "radio",
                                        value: "substructure",
                                        class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                      }, null, 8, ["onUpdate:modelValue"]), [
                                        [vModelRadio, $data.type]
                                      ]),
                                      createTextVNode("Substructure Search")
                                    ])
                                  ]),
                                  createVNode("div", { class: "flex items-center" }, [
                                    createVNode("label", {
                                      for: "search-type-similar",
                                      class: "block cursor-pointer text-sm font-medium text-gray-700"
                                    }, [
                                      withDirectives(createVNode("input", {
                                        id: "search-type-similar",
                                        "onUpdate:modelValue": ($event) => $data.type = $event,
                                        name: "search-type",
                                        value: "similarity",
                                        type: "radio",
                                        class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                      }, null, 8, ["onUpdate:modelValue"]), [
                                        [vModelRadio, $data.type]
                                      ]),
                                      createTextVNode("Similarity Search (tanimoto_threshold=0.5)")
                                    ])
                                  ])
                                ])
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "p-4" }, [
                            createVNode("div", { class: "flex justify-end" }, [
                              createVNode("button", {
                                onClick: ($event) => $options.openDialog(false),
                                type: "button",
                                class: "rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"
                              }, " Cancel ", 8, ["onClick"]),
                              createVNode("button", {
                                class: "ml-3 inline-flex justify-center rounded-md bg-gray-700 text-white py-2 px-4 text-sm font-medium shadow-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2",
                                onClick: $options.search
                              }, " Search ", 8, ["onClick"])
                            ])
                          ])
                        ]),
                        _: 1
                      })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`</div>`);
            } else {
              return [
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "ease-out duration-300",
                  "enter-from": "opacity-0",
                  "enter-to": "opacity-100",
                  leave: "ease-in duration-200",
                  "leave-from": "opacity-100",
                  "leave-to": "opacity-0"
                }, {
                  default: withCtx(() => [
                    createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                  ]),
                  _: 1
                }),
                createVNode("div", { class: "fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20" }, [
                  createVNode(_component_TransitionChild, {
                    as: "template",
                    enter: "ease-out duration-300",
                    "enter-from": "opacity-0 scale-95",
                    "enter-to": "opacity-100 scale-100",
                    leave: "ease-in duration-200",
                    "leave-from": "opacity-100 scale-100",
                    "leave-to": "opacity-0 scale-95"
                  }, {
                    default: withCtx(() => [
                      createVNode(_component_DialogPanel, { class: "mx-auto max-w-3xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "border-b border-gray-200 bg-white px-4 py-5 sm:px-6" }, [
                            createVNode("h3", { class: "text-lg font-medium leading-6 text-gray-900" }, " Structure Search ")
                          ]),
                          createVNode("div", { class: "p-4" }, [
                            createVNode("div", {
                              id: "structureSearchEditor",
                              class: "w-full border rounded-md",
                              style: { "height": "400px" }
                            }),
                            createVNode("div", { class: "mt-6" }, [
                              createVNode("fieldset", { class: "mt-6" }, [
                                createVNode("legend", { class: "contents text-base font-medium text-gray-900" }, " Select search type "),
                                createVNode("div", { class: "mt-4 space-y-4" }, [
                                  createVNode("div", { class: "flex items-center" }, [
                                    createVNode("label", {
                                      for: "search-type-exact",
                                      class: "block cursor-pointer text-sm font-medium text-gray-700"
                                    }, [
                                      withDirectives(createVNode("input", {
                                        id: "search-type-exact",
                                        name: "search-type",
                                        "onUpdate:modelValue": ($event) => $data.type = $event,
                                        value: "exact",
                                        type: "radio",
                                        class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                      }, null, 8, ["onUpdate:modelValue"]), [
                                        [vModelRadio, $data.type]
                                      ]),
                                      createTextVNode(" Exact match")
                                    ])
                                  ]),
                                  createVNode("div", { class: "flex items-center" }, [
                                    createVNode("label", {
                                      for: "search-type-sub",
                                      class: "block cursor-pointer text-sm font-medium text-gray-700"
                                    }, [
                                      withDirectives(createVNode("input", {
                                        id: "search-type-sub",
                                        name: "search-type",
                                        "onUpdate:modelValue": ($event) => $data.type = $event,
                                        type: "radio",
                                        value: "substructure",
                                        class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                      }, null, 8, ["onUpdate:modelValue"]), [
                                        [vModelRadio, $data.type]
                                      ]),
                                      createTextVNode("Substructure Search")
                                    ])
                                  ]),
                                  createVNode("div", { class: "flex items-center" }, [
                                    createVNode("label", {
                                      for: "search-type-similar",
                                      class: "block cursor-pointer text-sm font-medium text-gray-700"
                                    }, [
                                      withDirectives(createVNode("input", {
                                        id: "search-type-similar",
                                        "onUpdate:modelValue": ($event) => $data.type = $event,
                                        name: "search-type",
                                        value: "similarity",
                                        type: "radio",
                                        class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                      }, null, 8, ["onUpdate:modelValue"]), [
                                        [vModelRadio, $data.type]
                                      ]),
                                      createTextVNode("Similarity Search (tanimoto_threshold=0.5)")
                                    ])
                                  ])
                                ])
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "p-4" }, [
                            createVNode("div", { class: "flex justify-end" }, [
                              createVNode("button", {
                                onClick: ($event) => $options.openDialog(false),
                                type: "button",
                                class: "rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"
                              }, " Cancel ", 8, ["onClick"]),
                              createVNode("button", {
                                class: "ml-3 inline-flex justify-center rounded-md bg-gray-700 text-white py-2 px-4 text-sm font-medium shadow-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2",
                                onClick: $options.search
                              }, " Search ", 8, ["onClick"])
                            ])
                          ])
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
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_Dialog, {
            as: "div",
            class: "relative z-10",
            onClose: ($event) => $data.open = false
          }, {
            default: withCtx(() => [
              createVNode(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx(() => [
                  createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                ]),
                _: 1
              }),
              createVNode("div", { class: "fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20" }, [
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "ease-out duration-300",
                  "enter-from": "opacity-0 scale-95",
                  "enter-to": "opacity-100 scale-100",
                  leave: "ease-in duration-200",
                  "leave-from": "opacity-100 scale-100",
                  "leave-to": "opacity-0 scale-95"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_DialogPanel, { class: "mx-auto max-w-3xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                      default: withCtx(() => [
                        createVNode("div", { class: "border-b border-gray-200 bg-white px-4 py-5 sm:px-6" }, [
                          createVNode("h3", { class: "text-lg font-medium leading-6 text-gray-900" }, " Structure Search ")
                        ]),
                        createVNode("div", { class: "p-4" }, [
                          createVNode("div", {
                            id: "structureSearchEditor",
                            class: "w-full border rounded-md",
                            style: { "height": "400px" }
                          }),
                          createVNode("div", { class: "mt-6" }, [
                            createVNode("fieldset", { class: "mt-6" }, [
                              createVNode("legend", { class: "contents text-base font-medium text-gray-900" }, " Select search type "),
                              createVNode("div", { class: "mt-4 space-y-4" }, [
                                createVNode("div", { class: "flex items-center" }, [
                                  createVNode("label", {
                                    for: "search-type-exact",
                                    class: "block cursor-pointer text-sm font-medium text-gray-700"
                                  }, [
                                    withDirectives(createVNode("input", {
                                      id: "search-type-exact",
                                      name: "search-type",
                                      "onUpdate:modelValue": ($event) => $data.type = $event,
                                      value: "exact",
                                      type: "radio",
                                      class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                    }, null, 8, ["onUpdate:modelValue"]), [
                                      [vModelRadio, $data.type]
                                    ]),
                                    createTextVNode(" Exact match")
                                  ])
                                ]),
                                createVNode("div", { class: "flex items-center" }, [
                                  createVNode("label", {
                                    for: "search-type-sub",
                                    class: "block cursor-pointer text-sm font-medium text-gray-700"
                                  }, [
                                    withDirectives(createVNode("input", {
                                      id: "search-type-sub",
                                      name: "search-type",
                                      "onUpdate:modelValue": ($event) => $data.type = $event,
                                      type: "radio",
                                      value: "substructure",
                                      class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                    }, null, 8, ["onUpdate:modelValue"]), [
                                      [vModelRadio, $data.type]
                                    ]),
                                    createTextVNode("Substructure Search")
                                  ])
                                ]),
                                createVNode("div", { class: "flex items-center" }, [
                                  createVNode("label", {
                                    for: "search-type-similar",
                                    class: "block cursor-pointer text-sm font-medium text-gray-700"
                                  }, [
                                    withDirectives(createVNode("input", {
                                      id: "search-type-similar",
                                      "onUpdate:modelValue": ($event) => $data.type = $event,
                                      name: "search-type",
                                      value: "similarity",
                                      type: "radio",
                                      class: "mr-3 h-4 w-4 border-gray-300 text-secondary-dark focus:ring-secondary-dark"
                                    }, null, 8, ["onUpdate:modelValue"]), [
                                      [vModelRadio, $data.type]
                                    ]),
                                    createTextVNode("Similarity Search (tanimoto_threshold=0.5)")
                                  ])
                                ])
                              ])
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "p-4" }, [
                          createVNode("div", { class: "flex justify-end" }, [
                            createVNode("button", {
                              onClick: ($event) => $options.openDialog(false),
                              type: "button",
                              class: "rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2"
                            }, " Cancel ", 8, ["onClick"]),
                            createVNode("button", {
                              class: "ml-3 inline-flex justify-center rounded-md bg-gray-700 text-white py-2 px-4 text-sm font-medium shadow-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-secondary-dark focus:ring-offset-2",
                              onClick: $options.search
                            }, " Search ", 8, ["onClick"])
                          ])
                        ])
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                })
              ])
            ]),
            _: 1
          }, 8, ["onClose"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/App/StructureSearch.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const StructureSearch = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    AppLayout,
    MoleculeCard,
    StructureSearch,
    JetDropdownLink,
    Menu,
    MenuItems,
    MenuButton
  },
  props: ["page", "query", "limit", "tagType"],
  data() {
    return {
      results: null,
      loading: false,
      searchTerm: "",
      recentQueries: [],
      type: null,
      error: null
    };
  },
  mounted() {
    this.searchTerm = "";
    if (this.query) {
      this.searchTerm = this.query;
      this.fetchCompounds();
    } else {
      this.searchTerm = "";
      this.fetchCompounds();
    }
    if (typeof window !== "undefined") {
      let recentQueries = localStorage.getItem("recentQueries");
      if (recentQueries) {
        this.recentQueries = JSON.parse(recentQueries);
      }
    }
    if (window.location.hash) {
      if (window.location.hash == "#structure") {
        this.$refs["structure-search"].openDialog(true);
      } else if (window.location.hash == "#advanced") {
        this.$refs["advanced-search"].openDialog(true);
      }
    }
  },
  methods: {
    fetchCompounds(queryT) {
      this.error = null;
      this.results = null;
      this.loading = true;
      let queryTerm = null;
      if (queryT) {
        queryTerm = queryT;
      } else {
        queryTerm = this.searchTerm ? this.searchTerm : "";
      }
      if (queryTerm && queryTerm != "") {
        let queryType = this.getParameterByName("type");
        this.type = queryType && queryType != "" ? this.getParameterByName("type") : this.tagQuery(queryTerm);
      }
      let sort = this.getParameterByName("sort");
      if (typeof window !== "undefined") {
        let recentQueries = localStorage.getItem("recentQueries");
        recentQueries = recentQueries ? JSON.parse(recentQueries) : [];
        recentQueries.push(queryTerm);
        recentQueries = [...new Set(recentQueries)].filter((n) => n);
        localStorage.setItem(
          "recentQueries",
          JSON.stringify(recentQueries)
        );
      }
      let url = new URL(window.location);
      url.searchParams.set("query", queryTerm);
      url.searchParams.set("page", 1);
      url = this.sanitiseURL(url);
      window.history.pushState(
        null,
        "",
        queryTerm == "" ? window.location.href.split("?")[0] : url.toString()
      );
      let page = this.page ? this.page : 1;
      let urlEndpoint = "/api/v1/search/?limit=" + this.limit + "&page=" + page;
      if (sort) {
        urlEndpoint = urlEndpoint + "&sort=" + sort;
      }
      if (queryTerm == "") {
        this.type = "";
      }
      axios.post(urlEndpoint, {
        query: queryTerm,
        type: this.type,
        tagType: this.tagType ? this.tagType : null
      }).then((response) => {
        this.results = response.data;
        this.loading = false;
      }).catch((err) => {
        this.loading = false;
        this.results = {};
        this.results["data"] = [];
        this.error = err.response;
      });
    },
    sanitiseURL(url) {
      for (const [key, value] of url.searchParams.entries()) {
        if (value == "null" || value == null || value == "" || value == "undefined") {
          url.searchParams.delete(key);
        }
      }
      return url;
    },
    search(query) {
      if (query) {
        this.searchTerm = query;
        this.type = "text";
      }
      this.fetchCompounds(query);
    },
    removeSearchQuery(query) {
      if (typeof window !== "undefined") {
        let recentQueries = localStorage.getItem("recentQueries");
        recentQueries = recentQueries ? JSON.parse(recentQueries) : [];
        recentQueries = recentQueries.filter(function(item) {
          return item !== query;
        });
        recentQueries = [...new Set(recentQueries)].filter((n) => n);
        this.recentQueries = recentQueries;
        localStorage.setItem(
          "recentQueries",
          JSON.stringify(recentQueries)
        );
      }
    },
    navigateTo(link) {
      let queryType = this.getParameterByName("type");
      let searchTerm = this.searchTerm;
      let tagType = this.tagType;
      let location = "/compounds" + link.url;
      let query = queryType ? "&type=" + queryType : "";
      query += searchTerm ? "&query=" + searchTerm : "";
      tagType = tagType ? "&tagType=" + tagType : "";
      window.location = location + query + tagType;
    },
    getParameterByName(name, url = window.location.href) {
      name = name.replace(/[\[\]]/g, "\\$&");
      var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"), results = regex.exec(url);
      if (!results)
        return null;
      if (!results[2])
        return "";
      return decodeURIComponent(results[2].replace(/\+/g, " "));
    },
    downloadSmilesData(downloadType) {
      console.log("Download Data");
      console.log(this.$page.props.queryType);
      axios.post("/downloadSmiles", {
        query: this.$page.props.query,
        type: this.$page.props.queryType,
        downloadType
      }).then((response) => {
        const { url, filename, mimeType } = response.data;
        const link = document.createElement("a");
        link.href = url;
        link.download = filename;
        link.type = mimeType;
        link.click();
      }).catch((error) => {
        console.error(error);
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_StructureSearch = resolveComponent("StructureSearch");
  const _component_MoleculeCard = resolveComponent("MoleculeCard");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Compounds" }, _attrs), {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}><div class="mx-auto"${_scopeId}><div class="space-y-6 sm:px-6 lg:col-span-9 lg:px-0"${_scopeId}><section class="mb-20 bg-white shadow sm:overflow-hidden"${_scopeId}><div${_scopeId}><div class="relative border-b border-zinc-900/5"${_scopeId}><div class="relative pt-10 dark:border-white/5 mx-8 max-w-7xl py-12 sm:py-12"${_scopeId}><div class="absolute inset-0 bg-gradient-to-r from-[#36b49f] to-[#DBFF75] opacity-40 [mask-image:radial-gradient(farthest-side_at_top,white,transparent)] dark:from-[#36b49f]/30 dark:to-[#DBFF75]/30 dark:opacity-100"${_scopeId}><svg aria-hidden="true" class="absolute inset-x-0 inset-y-[-50%] h-[200%] w-full skew-y-[-18deg] fill-black/40 stroke-black/50 mix-blend-overlay dark:fill-white/2.5 dark:stroke-white/5"${_scopeId}><defs${_scopeId}><pattern id=":r99:" width="72" height="56" patternUnits="userSpaceOnUse" x="-12" y="4"${_scopeId}><path d="M.5 56V.5H72" fill="none"${_scopeId}></path></pattern></defs><rect width="100%" height="100%" stroke-width="0" fill="url(#:r99:)"${_scopeId}></rect><svg x="-12" y="4" class="overflow-visible"${_scopeId}><rect stroke-width="0" width="73" height="57" x="288" y="168"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="144" y="56"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="504" y="168"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="720" y="336"${_scopeId}></rect></svg></svg></div><div class="text-4xl mb-3 font-bold tracking-tight text-gray-900"${_scopeId}> Browse Compounds </div><p${_scopeId}> Search spectra by compound names, structure, sub-structures and smiliarity. </p></div></div><div${_scopeId}><div class="bg-white"${_scopeId}><section aria-labelledby="filter-heading"${_scopeId}><div class="bg-gray-100 border-t border-b"${_scopeId}><div class="mx-auto py-3 px-4 sm:px-6 lg:px-8"${_scopeId}><div class="flex items-center"${_scopeId}><div class="flex w-full bg-white shadow rounded-full"${_scopeId}><form class="flex w-full md:ml-0" action="#" method="GET"${_scopeId}><label for="mobile-search-field" class="sr-only"${_scopeId}>Search</label><label for="desktop-search-field" class="sr-only"${_scopeId}>Search</label><div class="relative w-full text-gray-400 focus-within:text-gray-600"${_scopeId}><div class="pointer-events-none absolute inset-y-0 left-0 flex items-center"${_scopeId}><svg class="h-5 w-5 flex-shrink-0" x-description="Heroicon name: mini/magnifying-glass" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"${_scopeId}></path></svg></div><input name="mobile-search-field" id="mobile-search-field" class="h-full w-full border-transparent py-2 pl-8 pr-3 text-base text-gray-900 placeholder-gray-500 focus:border-transparent focus:placeholder-gray-400 focus:outline-none focus:ring-0 sm:hidden" placeholder="Search" type="search" autofocus${_scopeId}><input name="desktop-search-field" id="desktop-search-field" class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline hidden h-full w-full border-transparent text-gray-900 placeholder-gray-500 focus:border-transparent focus:placeholder-gray-400 focus:outline-none focus:ring-0 sm:block" placeholder="Search by Compound name, SMILES, InChI, InChI Key" type="search"${ssrRenderAttr(
          "value",
          $data.searchTerm
        )} autofocus${_scopeId}></div></form></div><div class="flex items-center md:ml-6"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_StructureSearch, { ref: "structure-search" }, null, _parent2, _scopeId));
        _push2(`</div></div></div></div></section></div></div></div>`);
        if ($data.recentQueries && $data.recentQueries.length > 0) {
          _push2(`<div${_scopeId}><div class="bg-gray-100"${_scopeId}><div class="mx-auto border-y max-w-7xl py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-16"${_scopeId}><h3 class="text-sm font-medium text-gray-500"${_scopeId}> Filters </h3><div aria-hidden="true" class="hidden h-5 w-px bg-gray-300 sm:ml-4 sm:block"${_scopeId}></div><div class="mt-2 sm:mt-0 sm:ml-4"${_scopeId}><div class="-m-1 flex flex-wrap items-center"${_scopeId}>`);
          if ($data.type) {
            _push2(`<span class="m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"${_scopeId}><a class="mr-1"${_scopeId}>${ssrInterpolate($data.type)}</a></span>`);
          } else {
            _push2(`<!---->`);
          }
          if ($props.limit) {
            _push2(`<span class="m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"${_scopeId}><a class="cursor-pointer"${_scopeId}>LIMIT: ${ssrInterpolate($props.limit)}</a></span>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($data.results) {
          _push2(`<div${_scopeId}><div class="py-6 px-4 lg:px-12 sm:p-6 border-b border-gray-200"${_scopeId}>`);
          if ($data.results.data.length > 0) {
            _push2(`<div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"${_scopeId}><div${_scopeId}><p class="text-sm text-gray-700"${_scopeId}> Showing <span class="font-medium"${_scopeId}>${ssrInterpolate($data.results.from)}</span> to <span class="font-medium"${_scopeId}>${ssrInterpolate($data.results.to)}</span> of <span class="font-medium"${_scopeId}>${ssrInterpolate($data.results.total)}</span> results </p></div><div${_scopeId}><nav class="isolate inline-flex -space-x-px shadow-sm" aria-label="Pagination"${_scopeId}><!--[-->`);
            ssrRenderList($data.results.links, (link) => {
              _push2(`<a class="${ssrRenderClass([
                link.active ? "bg-gray-200" : "",
                "first:rounded-l-lg last:rounded-r-lg relative cursor-pointer inline-flex items-center border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20"
              ])}"${_scopeId}>${link.label}</a>`);
            });
            _push2(`<!--]--></nav></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          if ($data.results.data.length > 0) {
            _push2(`<div class="mx-auto grid mt-6 gap-5 lg:max-w-none md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6"${_scopeId}><!--[-->`);
            ssrRenderList($data.results.data, (result) => {
              _push2(`<span class="rounded-lg hover:shadow-lg shadow border"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_MoleculeCard, { molecule: result }, null, _parent2, _scopeId));
              _push2(`</span>`);
            });
            _push2(`<!--]--></div>`);
          } else {
            _push2(`<div${_scopeId}>`);
            if ($data.error) {
              _push2(`<div${_scopeId}><div class="bg-white"${_scopeId}><div class="mx-auto w-full max-w-7xl px-6 pb-16 sm:pb-24 lg:px-8"${_scopeId}><div class="mx-auto mt-20 max-w-2xl text-center sm:mt-24"${_scopeId}><p class="text-base font-semibold leading-8 text-indigo-600"${_scopeId}>${ssrInterpolate($data.error.status)}</p><h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl"${_scopeId}>${ssrInterpolate($data.error.statusText)}</h1><p class="mt-4 text-base leading-7 text-gray-600 sm:mt-6 sm:text-lg sm:leading-8"${_scopeId}>${ssrInterpolate($data.error.data.message)}</p></div><div class="mx-auto mt-16 flow-root max-w-lg sm:mt-20"${_scopeId}><div class="mt-10 flex justify-center"${_scopeId}><a href="/" class="text-sm font-semibold leading-6 text-indigo-600"${_scopeId}><span aria-hidden="true"${_scopeId}>←</span> Back to home </a></div></div></div></div></div>`);
            } else {
              _push2(`<div class="bg-white px-4 py-12"${_scopeId}><div class="text-center"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"${_scopeId}><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"${_scopeId}></path></svg><h3 class="mt-2 text-sm font-semibold text-gray-900"${_scopeId}> No records were found </h3><p class="mt-1 text-sm text-gray-500"${_scopeId}> Try to refine your search query. If the problem persist please contact us. </p></div></div>`);
            }
            _push2(`</div>`);
          }
          _push2(`</div>`);
          if ($data.results.data.length > 0) {
            _push2(`<div class="flex items-center justify-between bg-white px-12 py-3 rounded-md"${_scopeId}><div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"${_scopeId}><div${_scopeId}><p class="text-sm text-gray-700"${_scopeId}> Showing <span class="font-medium"${_scopeId}>${ssrInterpolate($data.results.from)}</span> to <span class="font-medium"${_scopeId}>${ssrInterpolate($data.results.to)}</span> of <span class="font-medium"${_scopeId}>${ssrInterpolate($data.results.total)}</span> results </p></div><div${_scopeId}><nav class="isolate inline-flex -space-x-px shadow-sm" aria-label="Pagination"${_scopeId}><!--[-->`);
            ssrRenderList($data.results.links, (link) => {
              _push2(`<a class="${ssrRenderClass([
                link.active ? "bg-gray-200" : "",
                "first:rounded-l-lg last:rounded-r-lg relative cursor-pointer inline-flex items-center border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20"
              ])}"${_scopeId}>${link.label}</a>`);
            });
            _push2(`<!--]--></nav></div></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          if ($data.recentQueries.length > 0) {
            _push2(`<div class="bg-gray-100 border-t"${_scopeId}><div class="mx-auto max-w-7xl py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-16"${_scopeId}><h3 class="text-sm font-medium text-gray-500"${_scopeId}> Recent Searches </h3><div aria-hidden="true" class="hidden h-5 w-px bg-gray-300 sm:ml-4 sm:block"${_scopeId}></div><div class="mt-2 sm:mt-0 sm:ml-4"${_scopeId}><div class="-m-1 flex flex-wrap items-center"${_scopeId}><!--[-->`);
            ssrRenderList($data.recentQueries, (query) => {
              _push2(`<span class="m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"${_scopeId}><a class="cursor-pointer"${_scopeId}>${ssrInterpolate(query)}</a><button type="button" class="ml-1 inline-flex h-4 w-4 flex-shrink-0 rounded-full p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-500"${_scopeId}><span class="sr-only"${_scopeId}>Remove filter for Objects</span><svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8"${_scopeId}><path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"${_scopeId}></path></svg></button></span>`);
            });
            _push2(`<!--]--></div></div></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($data.loading) {
          _push2(`<div${_scopeId}><div class="text-center py-20"${_scopeId}><svg class="animate-spin -ml-1 mr-3 h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId}></path></svg> Loading... </div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</section></div></div></div>`);
      } else {
        return [
          createVNode("div", null, [
            createVNode("div", { class: "mx-auto" }, [
              createVNode("div", { class: "space-y-6 sm:px-6 lg:col-span-9 lg:px-0" }, [
                createVNode("section", { class: "mb-20 bg-white shadow sm:overflow-hidden" }, [
                  createVNode("div", null, [
                    createVNode("div", { class: "relative border-b border-zinc-900/5" }, [
                      createVNode("div", { class: "relative pt-10 dark:border-white/5 mx-8 max-w-7xl py-12 sm:py-12" }, [
                        createVNode("div", { class: "absolute inset-0 bg-gradient-to-r from-[#36b49f] to-[#DBFF75] opacity-40 [mask-image:radial-gradient(farthest-side_at_top,white,transparent)] dark:from-[#36b49f]/30 dark:to-[#DBFF75]/30 dark:opacity-100" }, [
                          (openBlock(), createBlock("svg", {
                            "aria-hidden": "true",
                            class: "absolute inset-x-0 inset-y-[-50%] h-[200%] w-full skew-y-[-18deg] fill-black/40 stroke-black/50 mix-blend-overlay dark:fill-white/2.5 dark:stroke-white/5"
                          }, [
                            createVNode("defs", null, [
                              createVNode("pattern", {
                                id: ":r99:",
                                width: "72",
                                height: "56",
                                patternUnits: "userSpaceOnUse",
                                x: "-12",
                                y: "4"
                              }, [
                                createVNode("path", {
                                  d: "M.5 56V.5H72",
                                  fill: "none"
                                })
                              ])
                            ]),
                            createVNode("rect", {
                              width: "100%",
                              height: "100%",
                              "stroke-width": "0",
                              fill: "url(#:r99:)"
                            }),
                            (openBlock(), createBlock("svg", {
                              x: "-12",
                              y: "4",
                              class: "overflow-visible"
                            }, [
                              createVNode("rect", {
                                "stroke-width": "0",
                                width: "73",
                                height: "57",
                                x: "288",
                                y: "168"
                              }),
                              createVNode("rect", {
                                "stroke-width": "0",
                                width: "73",
                                height: "57",
                                x: "144",
                                y: "56"
                              }),
                              createVNode("rect", {
                                "stroke-width": "0",
                                width: "73",
                                height: "57",
                                x: "504",
                                y: "168"
                              }),
                              createVNode("rect", {
                                "stroke-width": "0",
                                width: "73",
                                height: "57",
                                x: "720",
                                y: "336"
                              })
                            ]))
                          ]))
                        ]),
                        createVNode("div", { class: "text-4xl mb-3 font-bold tracking-tight text-gray-900" }, " Browse Compounds "),
                        createVNode("p", null, " Search spectra by compound names, structure, sub-structures and smiliarity. ")
                      ])
                    ]),
                    createVNode("div", null, [
                      createVNode("div", { class: "bg-white" }, [
                        createVNode("section", { "aria-labelledby": "filter-heading" }, [
                          createVNode("div", { class: "bg-gray-100 border-t border-b" }, [
                            createVNode("div", { class: "mx-auto py-3 px-4 sm:px-6 lg:px-8" }, [
                              createVNode("div", { class: "flex items-center" }, [
                                createVNode("div", { class: "flex w-full bg-white shadow rounded-full" }, [
                                  createVNode("form", {
                                    class: "flex w-full md:ml-0",
                                    action: "#",
                                    method: "GET"
                                  }, [
                                    createVNode("label", {
                                      for: "mobile-search-field",
                                      class: "sr-only"
                                    }, "Search"),
                                    createVNode("label", {
                                      for: "desktop-search-field",
                                      class: "sr-only"
                                    }, "Search"),
                                    createVNode("div", { class: "relative w-full text-gray-400 focus-within:text-gray-600" }, [
                                      createVNode("div", { class: "pointer-events-none absolute inset-y-0 left-0 flex items-center" }, [
                                        (openBlock(), createBlock("svg", {
                                          class: "h-5 w-5 flex-shrink-0",
                                          "x-description": "Heroicon name: mini/magnifying-glass",
                                          xmlns: "http://www.w3.org/2000/svg",
                                          viewBox: "0 0 20 20",
                                          fill: "currentColor",
                                          "aria-hidden": "true"
                                        }, [
                                          createVNode("path", {
                                            "fill-rule": "evenodd",
                                            d: "M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z",
                                            "clip-rule": "evenodd"
                                          })
                                        ]))
                                      ]),
                                      createVNode("input", {
                                        name: "mobile-search-field",
                                        id: "mobile-search-field",
                                        class: "h-full w-full border-transparent py-2 pl-8 pr-3 text-base text-gray-900 placeholder-gray-500 focus:border-transparent focus:placeholder-gray-400 focus:outline-none focus:ring-0 sm:hidden",
                                        placeholder: "Search",
                                        type: "search",
                                        autofocus: ""
                                      }),
                                      withDirectives(createVNode("input", {
                                        name: "desktop-search-field",
                                        id: "desktop-search-field",
                                        class: "relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline hidden h-full w-full border-transparent text-gray-900 placeholder-gray-500 focus:border-transparent focus:placeholder-gray-400 focus:outline-none focus:ring-0 sm:block",
                                        placeholder: "Search by Compound name, SMILES, InChI, InChI Key",
                                        type: "search",
                                        "onUpdate:modelValue": ($event) => $data.searchTerm = $event,
                                        autofocus: "",
                                        onChange: ($event) => $options.search(),
                                        onSearch: ($event) => $options.search(
                                          ""
                                        )
                                      }, null, 40, ["onUpdate:modelValue", "onChange", "onSearch"]), [
                                        [
                                          vModelText,
                                          $data.searchTerm
                                        ]
                                      ])
                                    ])
                                  ])
                                ]),
                                createVNode("div", { class: "flex items-center md:ml-6" }, [
                                  createVNode(_component_StructureSearch, { ref: "structure-search" }, null, 512)
                                ])
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ]),
                  $data.recentQueries && $data.recentQueries.length > 0 ? (openBlock(), createBlock("div", { key: 0 }, [
                    createVNode("div", { class: "bg-gray-100" }, [
                      createVNode("div", { class: "mx-auto border-y max-w-7xl py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-16" }, [
                        createVNode("h3", { class: "text-sm font-medium text-gray-500" }, " Filters "),
                        createVNode("div", {
                          "aria-hidden": "true",
                          class: "hidden h-5 w-px bg-gray-300 sm:ml-4 sm:block"
                        }),
                        createVNode("div", { class: "mt-2 sm:mt-0 sm:ml-4" }, [
                          createVNode("div", { class: "-m-1 flex flex-wrap items-center" }, [
                            $data.type ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"
                            }, [
                              createVNode("a", { class: "mr-1" }, toDisplayString($data.type), 1)
                            ])) : createCommentVNode("", true),
                            $props.limit ? (openBlock(), createBlock("span", {
                              key: 1,
                              class: "m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"
                            }, [
                              createVNode("a", { class: "cursor-pointer" }, "LIMIT: " + toDisplayString($props.limit), 1)
                            ])) : createCommentVNode("", true)
                          ])
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  $data.results ? (openBlock(), createBlock("div", { key: 1 }, [
                    createVNode("div", { class: "py-6 px-4 lg:px-12 sm:p-6 border-b border-gray-200" }, [
                      $data.results.data.length > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
                      }, [
                        createVNode("div", null, [
                          createVNode("p", { class: "text-sm text-gray-700" }, [
                            createTextVNode(" Showing "),
                            createVNode("span", { class: "font-medium" }, toDisplayString($data.results.from), 1),
                            createTextVNode(" to "),
                            createVNode("span", { class: "font-medium" }, toDisplayString($data.results.to), 1),
                            createTextVNode(" of "),
                            createVNode("span", { class: "font-medium" }, toDisplayString($data.results.total), 1),
                            createTextVNode(" results ")
                          ])
                        ]),
                        createVNode("div", null, [
                          createVNode("nav", {
                            class: "isolate inline-flex -space-x-px shadow-sm",
                            "aria-label": "Pagination"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList($data.results.links, (link) => {
                              return openBlock(), createBlock("a", {
                                onClick: ($event) => $options.navigateTo(link),
                                key: link.label,
                                class: [
                                  link.active ? "bg-gray-200" : "",
                                  "first:rounded-l-lg last:rounded-r-lg relative cursor-pointer inline-flex items-center border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20"
                                ],
                                innerHTML: link.label
                              }, null, 10, ["onClick", "innerHTML"]);
                            }), 128))
                          ])
                        ])
                      ])) : createCommentVNode("", true),
                      $data.results.data.length > 0 ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mx-auto grid mt-6 gap-5 lg:max-w-none md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList($data.results.data, (result) => {
                          return openBlock(), createBlock("span", {
                            class: "rounded-lg hover:shadow-lg shadow border",
                            key: result.id
                          }, [
                            createVNode(_component_MoleculeCard, { molecule: result }, null, 8, ["molecule"])
                          ]);
                        }), 128))
                      ])) : (openBlock(), createBlock("div", { key: 2 }, [
                        $data.error ? (openBlock(), createBlock("div", { key: 0 }, [
                          createVNode("div", { class: "bg-white" }, [
                            createVNode("div", { class: "mx-auto w-full max-w-7xl px-6 pb-16 sm:pb-24 lg:px-8" }, [
                              createVNode("div", { class: "mx-auto mt-20 max-w-2xl text-center sm:mt-24" }, [
                                createVNode("p", { class: "text-base font-semibold leading-8 text-indigo-600" }, toDisplayString($data.error.status), 1),
                                createVNode("h1", { class: "mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl" }, toDisplayString($data.error.statusText), 1),
                                createVNode("p", { class: "mt-4 text-base leading-7 text-gray-600 sm:mt-6 sm:text-lg sm:leading-8" }, toDisplayString($data.error.data.message), 1)
                              ]),
                              createVNode("div", { class: "mx-auto mt-16 flow-root max-w-lg sm:mt-20" }, [
                                createVNode("div", { class: "mt-10 flex justify-center" }, [
                                  createVNode("a", {
                                    href: "/",
                                    class: "text-sm font-semibold leading-6 text-indigo-600"
                                  }, [
                                    createVNode("span", { "aria-hidden": "true" }, "←"),
                                    createTextVNode(" Back to home ")
                                  ])
                                ])
                              ])
                            ])
                          ])
                        ])) : (openBlock(), createBlock("div", {
                          key: 1,
                          class: "bg-white px-4 py-12"
                        }, [
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
                            createVNode("h3", { class: "mt-2 text-sm font-semibold text-gray-900" }, " No records were found "),
                            createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Try to refine your search query. If the problem persist please contact us. ")
                          ])
                        ]))
                      ]))
                    ]),
                    $data.results.data.length > 0 ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex items-center justify-between bg-white px-12 py-3 rounded-md"
                    }, [
                      createVNode("div", { class: "hidden sm:flex sm:flex-1 sm:items-center sm:justify-between" }, [
                        createVNode("div", null, [
                          createVNode("p", { class: "text-sm text-gray-700" }, [
                            createTextVNode(" Showing "),
                            createVNode("span", { class: "font-medium" }, toDisplayString($data.results.from), 1),
                            createTextVNode(" to "),
                            createVNode("span", { class: "font-medium" }, toDisplayString($data.results.to), 1),
                            createTextVNode(" of "),
                            createVNode("span", { class: "font-medium" }, toDisplayString($data.results.total), 1),
                            createTextVNode(" results ")
                          ])
                        ]),
                        createVNode("div", null, [
                          createVNode("nav", {
                            class: "isolate inline-flex -space-x-px shadow-sm",
                            "aria-label": "Pagination"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList($data.results.links, (link) => {
                              return openBlock(), createBlock("a", {
                                onClick: ($event) => $options.navigateTo(link),
                                key: link.label,
                                class: [
                                  link.active ? "bg-gray-200" : "",
                                  "first:rounded-l-lg last:rounded-r-lg relative cursor-pointer inline-flex items-center border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20"
                                ],
                                innerHTML: link.label
                              }, null, 10, ["onClick", "innerHTML"]);
                            }), 128))
                          ])
                        ])
                      ])
                    ])) : createCommentVNode("", true),
                    $data.recentQueries.length > 0 ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "bg-gray-100 border-t"
                    }, [
                      createVNode("div", { class: "mx-auto max-w-7xl py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-16" }, [
                        createVNode("h3", { class: "text-sm font-medium text-gray-500" }, " Recent Searches "),
                        createVNode("div", {
                          "aria-hidden": "true",
                          class: "hidden h-5 w-px bg-gray-300 sm:ml-4 sm:block"
                        }),
                        createVNode("div", { class: "mt-2 sm:mt-0 sm:ml-4" }, [
                          createVNode("div", { class: "-m-1 flex flex-wrap items-center" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList($data.recentQueries, (query) => {
                              return openBlock(), createBlock("span", {
                                key: query,
                                class: "m-1 inline-flex items-center rounded-full border border-gray-200 bg-white py-1.5 pl-3 pr-2 text-sm font-medium text-gray-900"
                              }, [
                                createVNode("a", {
                                  class: "cursor-pointer",
                                  onClick: ($event) => $options.search(query)
                                }, toDisplayString(query), 9, ["onClick"]),
                                createVNode("button", {
                                  type: "button",
                                  onClick: ($event) => $options.removeSearchQuery(query),
                                  class: "ml-1 inline-flex h-4 w-4 flex-shrink-0 rounded-full p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-500"
                                }, [
                                  createVNode("span", { class: "sr-only" }, "Remove filter for Objects"),
                                  (openBlock(), createBlock("svg", {
                                    class: "h-2 w-2",
                                    stroke: "currentColor",
                                    fill: "none",
                                    viewBox: "0 0 8 8"
                                  }, [
                                    createVNode("path", {
                                      "stroke-linecap": "round",
                                      "stroke-width": "1.5",
                                      d: "M1 1l6 6m0-6L1 7"
                                    })
                                  ]))
                                ], 8, ["onClick"])
                              ]);
                            }), 128))
                          ])
                        ])
                      ])
                    ])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true),
                  $data.loading ? (openBlock(), createBlock("div", { key: 2 }, [
                    createVNode("div", { class: "text-center py-20" }, [
                      (openBlock(), createBlock("svg", {
                        class: "animate-spin -ml-1 mr-3 h-5 w-5 inline",
                        xmlns: "http://www.w3.org/2000/svg",
                        fill: "none",
                        viewBox: "0 0 24 24"
                      }, [
                        createVNode("circle", {
                          class: "opacity-25",
                          cx: "12",
                          cy: "12",
                          r: "10",
                          stroke: "currentColor",
                          "stroke-width": "4"
                        }),
                        createVNode("path", {
                          class: "opacity-75",
                          fill: "currentColor",
                          d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        })
                      ])),
                      createTextVNode(" Loading... ")
                    ])
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
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Compounds.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Compounds = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Compounds as default
};
