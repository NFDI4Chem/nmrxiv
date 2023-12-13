import { J as JetApplicationLogo } from "./ApplicationLogo-88e5413e.mjs";
import { instantMeiliSearch } from "@meilisearch/instant-meilisearch";
import { ref, useSSRContext, watchEffect, resolveComponent, withCtx, createVNode, toDisplayString, openBlock, createBlock, createCommentVNode, resolveDynamicComponent, Fragment, renderList, createTextVNode, mergeProps, onMounted, onUnmounted, renderSlot, withDirectives, vModelSelect, vModelText, withModifiers, Transition } from "vue";
import { MagnifyingGlassIcon, ArchiveBoxIcon, PencilSquareIcon, TableCellsIcon, PlusIcon, PencilIcon, PlusSmallIcon, ChevronUpIcon, PaperClipIcon, FolderIcon as FolderIcon$1, DocumentTextIcon, ArrowDownTrayIcon, ChevronRightIcon, InformationCircleIcon, HomeIcon, EllipsisVerticalIcon, ExclamationCircleIcon, ArrowUpTrayIcon, CheckIcon, TrashIcon, XCircleIcon, ClipboardDocumentIcon, QuestionMarkCircleIcon, ExclamationTriangleIcon, ArrowDownOnSquareStackIcon, PlayIcon, PauseIcon } from "@heroicons/vue/24/solid";
import { Link, Head } from "@inertiajs/vue3";
import { useMagicKeys } from "@vueuse/core";
import { FolderIcon, TagIcon, DocumentPlusIcon, XMarkIcon, HeartIcon, ArrowPathIcon, BellIcon, Bars3Icon, ClockIcon, UsersIcon, StarIcon, TrashIcon as TrashIcon$1, Squares2X2Icon, SwatchIcon, HomeIcon as HomeIcon$1 } from "@heroicons/vue/24/outline";
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, DialogPanel, Dialog, DialogOverlay, TransitionChild, TransitionRoot, Menu, MenuItem, MenuItems, Disclosure, DisclosureButton, DisclosurePanel, DialogTitle, MenuButton } from "@headlessui/vue";
import { ssrRenderComponent, ssrRenderList, ssrRenderClass, ssrInterpolate, ssrRenderVNode, ssrRenderAttrs, ssrRenderSlot, ssrRenderStyle, ssrRenderAttr, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { D as Dropdown, A as AnnouncementBanner } from "./AnnouncementBanner-3ebe3621.mjs";
import { F as FlashMessages } from "./FlashMessages-f0051c19.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import VueTagsInput from "@sipec/vue3-tags-input";
import slider from "vue3-slider";
import OCL$1 from "openchemlib/full.js";
import { S as SelectRich } from "./SelectRich-b58dcc3f.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import Dropzone from "dropzone";
import axiosRetry from "axios-retry";
import { T as ToolTip } from "./ToolTip-cf9882a3.mjs";
import { J as JetConfirmationModal } from "./ConfirmationModal-d0b47856.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { EllipsisVerticalIcon as EllipsisVerticalIcon$1 } from "@heroicons/vue/20/solid";
const { meta, k } = useMagicKeys();
const open = ref(false);
const _sfc_main$g = {
  components: {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    DialogPanel,
    Dialog,
    DialogOverlay,
    FolderIcon,
    MagnifyingGlassIcon,
    TransitionChild,
    TransitionRoot,
    Link
  },
  props: ["akey", "host"],
  setup() {
    watchEffect(() => {
      if (meta.value && k.value) {
        open.value = true;
      }
    });
    return {
      open,
      onSelect(item) {
        if (item.url && item.url !== void 0) {
          window.location = item.url;
        }
      }
    };
  },
  data: function() {
    return {
      projects: [],
      recent: [],
      quickActions: [
        {
          name: "Learn Spectral analysis...",
          icon: TagIcon,
          shortcut: "F",
          url: "https://docs.nmrxiv.org"
        },
        //   { name: "Add hashtag...", icon: HashtagIcon, shortcut: "H", url: "#" },
        //   { name: "Add label...", icon: TagIcon, shortcut: "L", url: "#" },
        {
          name: "For queries/feedback write to us at info.nmrxiv@uni-jena.de",
          icon: DocumentPlusIcon,
          shortcut: "N",
          url: "https://docs.nmrxiv.org/FAQs.html#how-to-reach-to-you"
        }
      ],
      selected: null,
      department: "all",
      query: null,
      filteredProjects: [],
      searchClient: instantMeiliSearch(this.host, this.akey)
    };
  },
  computed: {
    index() {
      return this.$page.props.SCOUT_PREFIX + "projects";
    }
  },
  methods: {
    submit() {
      if (this.query && this.query != "") {
        window.location.href = "/search?query=" + this.query;
      } else {
        window.location.href = "/search";
      }
    },
    searchFunction(helper) {
      const page = helper.getPage();
      helper.setQuery(this.query).setPage(page).search();
    }
  }
};
function _sfc_ssrRender$g(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_ais_instant_search = resolveComponent("ais-instant-search");
  const _component_ais_autocomplete = resolveComponent("ais-autocomplete");
  const _component_TransitionRoot = resolveComponent("TransitionRoot");
  const _component_Dialog = resolveComponent("Dialog");
  const _component_TransitionChild = resolveComponent("TransitionChild");
  const _component_DialogPanel = resolveComponent("DialogPanel");
  const _component_Combobox = resolveComponent("Combobox");
  const _component_MagnifyingGlassIcon = resolveComponent("MagnifyingGlassIcon");
  const _component_ComboboxInput = resolveComponent("ComboboxInput");
  const _component_ComboboxOptions = resolveComponent("ComboboxOptions");
  const _component_ComboboxOption = resolveComponent("ComboboxOption");
  const _component_Link = resolveComponent("Link");
  const _component_FolderIcon = resolveComponent("FolderIcon");
  _push(`<!--[--><button type="button" class="hidden -ml-4 sm:flex items-center w-96 text-left space-x-3 px-4 h-12 bg-white ring-1 ring-slate-900/10 hover:ring-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-sm rounded-lg text-slate-400"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-none text-slate-300 dark:text-slate-400" aria-hidden="true"><path d="m19 19-3.5-3.5"></path><circle cx="11" cy="11" r="6"></circle></svg><span class="flex-auto">Search</span><kbd class="font-sans text-gray-300 font-semibold dark:text-slate-500"><abbr title="Command" class="no-underline text-slate-300 dark:text-slate-500">⌘</abbr> K</kbd></button>`);
  _push(ssrRenderComponent(_component_ais_instant_search, {
    "index-name": $options.index,
    "search-client": _ctx.searchClient
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_ais_autocomplete, null, {
          default: withCtx(({ currentRefinement, indices, refine }, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_TransitionRoot, {
                show: $setup.open,
                as: "template",
                appear: "",
                onAfterLeave: ($event) => _ctx.query = ""
              }, {
                default: withCtx((_2, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(ssrRenderComponent(_component_Dialog, {
                      as: "div",
                      class: "relative z-10",
                      onClose: ($event) => $setup.open = false
                    }, {
                      default: withCtx((_3, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(ssrRenderComponent(_component_TransitionChild, {
                            as: "template",
                            enter: "ease-out duration-300",
                            "enter-from": "opacity-0",
                            "enter-to": "opacity-100",
                            leave: "ease-in duration-200",
                            "leave-from": "opacity-100",
                            "leave-to": "opacity-0"
                          }, {
                            default: withCtx((_4, _push6, _parent6, _scopeId5) => {
                              if (_push6) {
                                _push6(`<div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"${_scopeId5}></div>`);
                              } else {
                                return [
                                  createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                                ];
                              }
                            }),
                            _: 2
                          }, _parent5, _scopeId4));
                          _push5(`<div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20"${_scopeId4}>`);
                          _push5(ssrRenderComponent(_component_TransitionChild, {
                            as: "template",
                            enter: "ease-out duration-300",
                            "enter-from": "opacity-0 scale-95",
                            "enter-to": "opacity-100 scale-100",
                            leave: "ease-in duration-200",
                            "leave-from": "opacity-100 scale-100",
                            "leave-to": "opacity-0 scale-95"
                          }, {
                            default: withCtx((_4, _push6, _parent6, _scopeId5) => {
                              if (_push6) {
                                _push6(ssrRenderComponent(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                                  default: withCtx((_5, _push7, _parent7, _scopeId6) => {
                                    if (_push7) {
                                      _push7(ssrRenderComponent(_component_Combobox, {
                                        as: "div",
                                        class: "mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all",
                                        "onUpdate:modelValue": $setup.onSelect
                                      }, {
                                        default: withCtx((_6, _push8, _parent8, _scopeId7) => {
                                          if (_push8) {
                                            _push8(`<div class="relative"${_scopeId7}>`);
                                            _push8(ssrRenderComponent(_component_MagnifyingGlassIcon, {
                                              class: "pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40",
                                              "aria-hidden": "true"
                                            }, null, _parent8, _scopeId7));
                                            _push8(ssrRenderComponent(_component_ComboboxInput, {
                                              id: "searchAC",
                                              class: "h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm",
                                              placeholder: "Search",
                                              onInput: ($event) => refine(
                                                $event.currentTarget.value
                                              )
                                            }, null, _parent8, _scopeId7));
                                            _push8(`</div>`);
                                            _push8(ssrRenderComponent(_component_ComboboxOptions, {
                                              static: "",
                                              class: "max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                                            }, {
                                              default: withCtx((_7, _push9, _parent9, _scopeId8) => {
                                                if (_push9) {
                                                  _push9(`<!--[-->`);
                                                  ssrRenderList(indices, (index) => {
                                                    _push9(`<div aria-labelledby="searchAC" class="dropdown-menu p-0 w-100"${_scopeId8}>`);
                                                    if (currentRefinement === "") {
                                                      _push9(`<li class="p-2"${_scopeId8}><div class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700"${_scopeId8}> Type to search projects or datasets or click below for help. </div></li>`);
                                                    } else {
                                                      _push9(`<!---->`);
                                                    }
                                                    if (currentRefinement != "") {
                                                      _push9(`<li class="p-2"${_scopeId8}>`);
                                                      if (currentRefinement === "" && _ctx.recent.length > 0) {
                                                        _push9(`<h2 class="mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"${_scopeId8}> Recent searches </h2>`);
                                                      } else {
                                                        _push9(`<!---->`);
                                                      }
                                                      if (currentRefinement) {
                                                        _push9(`<div class="border rounded-md"${_scopeId8}>`);
                                                        if (index.hits.length > 0) {
                                                          _push9(`<span${_scopeId8}><ul class="text-sm text-gray-700"${_scopeId8}><!--[-->`);
                                                          ssrRenderList(index.hits, (hit) => {
                                                            _push9(ssrRenderComponent(_component_ComboboxOption, {
                                                              key: hit.objectID,
                                                              value: hit,
                                                              as: "template"
                                                            }, {
                                                              default: withCtx(({
                                                                active
                                                              }, _push10, _parent10, _scopeId9) => {
                                                                if (_push10) {
                                                                  _push10(`<li class="${ssrRenderClass([
                                                                    "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                    active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                  ])}"${_scopeId9}>`);
                                                                  _push10(ssrRenderComponent(_component_Link, {
                                                                    class: "w-full",
                                                                    href: hit.public_url,
                                                                    onClick: ($event) => $setup.open = false
                                                                  }, {
                                                                    default: withCtx((_8, _push11, _parent11, _scopeId10) => {
                                                                      if (_push11) {
                                                                        _push11(ssrRenderComponent(_component_FolderIcon, {
                                                                          class: [
                                                                            "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                            active && "text-opacity-100"
                                                                          ],
                                                                          "aria-hidden": "true"
                                                                        }, null, _parent11, _scopeId10));
                                                                        _push11(`<span class="ml-3 flex-auto truncate"${_scopeId10}>${ssrInterpolate(hit.name)}</span>`);
                                                                        if (active) {
                                                                          _push11(`<span class="ml-3 flex-none text-gray-500"${_scopeId10}>Jump to...</span>`);
                                                                        } else {
                                                                          _push11(`<!---->`);
                                                                        }
                                                                      } else {
                                                                        return [
                                                                          createVNode(_component_FolderIcon, {
                                                                            class: [
                                                                              "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                              active && "text-opacity-100"
                                                                            ],
                                                                            "aria-hidden": "true"
                                                                          }, null, 8, ["class"]),
                                                                          createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                          active ? (openBlock(), createBlock("span", {
                                                                            key: 0,
                                                                            class: "ml-3 flex-none text-gray-500"
                                                                          }, "Jump to...")) : createCommentVNode("", true)
                                                                        ];
                                                                      }
                                                                    }),
                                                                    _: 2
                                                                  }, _parent10, _scopeId9));
                                                                  _push10(`</li>`);
                                                                } else {
                                                                  return [
                                                                    createVNode("li", {
                                                                      class: [
                                                                        "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                        active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                      ]
                                                                    }, [
                                                                      createVNode(_component_Link, {
                                                                        class: "w-full",
                                                                        href: hit.public_url,
                                                                        onClick: ($event) => $setup.open = false
                                                                      }, {
                                                                        default: withCtx(() => [
                                                                          createVNode(_component_FolderIcon, {
                                                                            class: [
                                                                              "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                              active && "text-opacity-100"
                                                                            ],
                                                                            "aria-hidden": "true"
                                                                          }, null, 8, ["class"]),
                                                                          createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                          active ? (openBlock(), createBlock("span", {
                                                                            key: 0,
                                                                            class: "ml-3 flex-none text-gray-500"
                                                                          }, "Jump to...")) : createCommentVNode("", true)
                                                                        ]),
                                                                        _: 2
                                                                      }, 1032, ["href", "onClick"])
                                                                    ], 2)
                                                                  ];
                                                                }
                                                              }),
                                                              _: 2
                                                            }, _parent9, _scopeId8));
                                                          });
                                                          _push9(`<!--]--></ul></span>`);
                                                        } else {
                                                          _push9(`<span${_scopeId8}>`);
                                                          if (currentRefinement !== "" && index.hits.length === 0) {
                                                            _push9(`<div class="py-14 px-6 text-center sm:px-14"${_scopeId8}><p class="mt-4 text-sm text-gray-900"${_scopeId8}> We couldn&#39;t find any results with that term. Please try again. </p></div>`);
                                                          } else {
                                                            _push9(`<!---->`);
                                                          }
                                                          _push9(`</span>`);
                                                        }
                                                        _push9(`</div>`);
                                                      } else {
                                                        _push9(`<!---->`);
                                                      }
                                                      _push9(`</li>`);
                                                    } else {
                                                      _push9(`<!---->`);
                                                    }
                                                    _push9(`</div>`);
                                                  });
                                                  _push9(`<!--]-->`);
                                                  if (currentRefinement === "") {
                                                    _push9(`<li class="p-2"${_scopeId8}><h2 class="sr-only"${_scopeId8}> Quick actions </h2><ul class="text-sm text-gray-700"${_scopeId8}><!--[-->`);
                                                    ssrRenderList(_ctx.quickActions, (action) => {
                                                      _push9(ssrRenderComponent(_component_ComboboxOption, {
                                                        key: action.shortcut,
                                                        value: action,
                                                        as: "template"
                                                      }, {
                                                        default: withCtx(({ active }, _push10, _parent10, _scopeId9) => {
                                                          if (_push10) {
                                                            _push10(`<li class="${ssrRenderClass([
                                                              "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                              active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                            ])}"${_scopeId9}>`);
                                                            ssrRenderVNode(_push10, createVNode(resolveDynamicComponent(
                                                              action.icon
                                                            ), {
                                                              class: [
                                                                "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                                active && "text-opacity-100"
                                                              ],
                                                              "aria-hidden": "true"
                                                            }, null), _parent10, _scopeId9);
                                                            _push10(`<span class="ml-3 flex-auto truncate"${_scopeId9}>${ssrInterpolate(action.name)}</span></li>`);
                                                          } else {
                                                            return [
                                                              createVNode("li", {
                                                                class: [
                                                                  "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                                  active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                ]
                                                              }, [
                                                                (openBlock(), createBlock(resolveDynamicComponent(
                                                                  action.icon
                                                                ), {
                                                                  class: [
                                                                    "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                                    active && "text-opacity-100"
                                                                  ],
                                                                  "aria-hidden": "true"
                                                                }, null, 8, ["class"])),
                                                                createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                              ], 2)
                                                            ];
                                                          }
                                                        }),
                                                        _: 2
                                                      }, _parent9, _scopeId8));
                                                    });
                                                    _push9(`<!--]--></ul></li>`);
                                                  } else {
                                                    _push9(`<!---->`);
                                                  }
                                                } else {
                                                  return [
                                                    (openBlock(true), createBlock(Fragment, null, renderList(indices, (index) => {
                                                      return openBlock(), createBlock("div", {
                                                        key: index.indexId,
                                                        "aria-labelledby": "searchAC",
                                                        class: "dropdown-menu p-0 w-100"
                                                      }, [
                                                        currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                          key: 0,
                                                          class: "p-2"
                                                        }, [
                                                          createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, " Type to search projects or datasets or click below for help. ")
                                                        ])) : createCommentVNode("", true),
                                                        currentRefinement != "" ? (openBlock(), createBlock("li", {
                                                          key: 1,
                                                          class: "p-2"
                                                        }, [
                                                          currentRefinement === "" && _ctx.recent.length > 0 ? (openBlock(), createBlock("h2", {
                                                            key: 0,
                                                            class: "mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                                                          }, " Recent searches ")) : createCommentVNode("", true),
                                                          currentRefinement ? (openBlock(), createBlock("div", {
                                                            key: 1,
                                                            class: "border rounded-md"
                                                          }, [
                                                            index.hits.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                                                              createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                                (openBlock(true), createBlock(Fragment, null, renderList(index.hits, (hit) => {
                                                                  return openBlock(), createBlock(_component_ComboboxOption, {
                                                                    key: hit.objectID,
                                                                    value: hit,
                                                                    as: "template"
                                                                  }, {
                                                                    default: withCtx(({
                                                                      active
                                                                    }) => [
                                                                      createVNode("li", {
                                                                        class: [
                                                                          "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                          active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                        ]
                                                                      }, [
                                                                        createVNode(_component_Link, {
                                                                          class: "w-full",
                                                                          href: hit.public_url,
                                                                          onClick: ($event) => $setup.open = false
                                                                        }, {
                                                                          default: withCtx(() => [
                                                                            createVNode(_component_FolderIcon, {
                                                                              class: [
                                                                                "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                                active && "text-opacity-100"
                                                                              ],
                                                                              "aria-hidden": "true"
                                                                            }, null, 8, ["class"]),
                                                                            createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                            active ? (openBlock(), createBlock("span", {
                                                                              key: 0,
                                                                              class: "ml-3 flex-none text-gray-500"
                                                                            }, "Jump to...")) : createCommentVNode("", true)
                                                                          ]),
                                                                          _: 2
                                                                        }, 1032, ["href", "onClick"])
                                                                      ], 2)
                                                                    ]),
                                                                    _: 2
                                                                  }, 1032, ["value"]);
                                                                }), 128))
                                                              ])
                                                            ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                                              currentRefinement !== "" && index.hits.length === 0 ? (openBlock(), createBlock("div", {
                                                                key: 0,
                                                                class: "py-14 px-6 text-center sm:px-14"
                                                              }, [
                                                                createVNode("p", { class: "mt-4 text-sm text-gray-900" }, " We couldn't find any results with that term. Please try again. ")
                                                              ])) : createCommentVNode("", true)
                                                            ]))
                                                          ])) : createCommentVNode("", true)
                                                        ])) : createCommentVNode("", true)
                                                      ]);
                                                    }), 128)),
                                                    currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                      key: 0,
                                                      class: "p-2"
                                                    }, [
                                                      createVNode("h2", { class: "sr-only" }, " Quick actions "),
                                                      createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                        (openBlock(true), createBlock(Fragment, null, renderList(_ctx.quickActions, (action) => {
                                                          return openBlock(), createBlock(_component_ComboboxOption, {
                                                            key: action.shortcut,
                                                            value: action,
                                                            as: "template"
                                                          }, {
                                                            default: withCtx(({ active }) => [
                                                              createVNode("li", {
                                                                class: [
                                                                  "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                                  active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                ]
                                                              }, [
                                                                (openBlock(), createBlock(resolveDynamicComponent(
                                                                  action.icon
                                                                ), {
                                                                  class: [
                                                                    "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                                    active && "text-opacity-100"
                                                                  ],
                                                                  "aria-hidden": "true"
                                                                }, null, 8, ["class"])),
                                                                createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                              ], 2)
                                                            ]),
                                                            _: 2
                                                          }, 1032, ["value"]);
                                                        }), 128))
                                                      ])
                                                    ])) : createCommentVNode("", true)
                                                  ];
                                                }
                                              }),
                                              _: 2
                                            }, _parent8, _scopeId7));
                                          } else {
                                            return [
                                              createVNode("div", { class: "relative" }, [
                                                createVNode(_component_MagnifyingGlassIcon, {
                                                  class: "pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40",
                                                  "aria-hidden": "true"
                                                }),
                                                createVNode(_component_ComboboxInput, {
                                                  id: "searchAC",
                                                  class: "h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm",
                                                  placeholder: "Search",
                                                  onInput: ($event) => refine(
                                                    $event.currentTarget.value
                                                  )
                                                }, null, 8, ["onInput"])
                                              ]),
                                              createVNode(_component_ComboboxOptions, {
                                                static: "",
                                                class: "max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                                              }, {
                                                default: withCtx(() => [
                                                  (openBlock(true), createBlock(Fragment, null, renderList(indices, (index) => {
                                                    return openBlock(), createBlock("div", {
                                                      key: index.indexId,
                                                      "aria-labelledby": "searchAC",
                                                      class: "dropdown-menu p-0 w-100"
                                                    }, [
                                                      currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                        key: 0,
                                                        class: "p-2"
                                                      }, [
                                                        createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, " Type to search projects or datasets or click below for help. ")
                                                      ])) : createCommentVNode("", true),
                                                      currentRefinement != "" ? (openBlock(), createBlock("li", {
                                                        key: 1,
                                                        class: "p-2"
                                                      }, [
                                                        currentRefinement === "" && _ctx.recent.length > 0 ? (openBlock(), createBlock("h2", {
                                                          key: 0,
                                                          class: "mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                                                        }, " Recent searches ")) : createCommentVNode("", true),
                                                        currentRefinement ? (openBlock(), createBlock("div", {
                                                          key: 1,
                                                          class: "border rounded-md"
                                                        }, [
                                                          index.hits.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                                                            createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                              (openBlock(true), createBlock(Fragment, null, renderList(index.hits, (hit) => {
                                                                return openBlock(), createBlock(_component_ComboboxOption, {
                                                                  key: hit.objectID,
                                                                  value: hit,
                                                                  as: "template"
                                                                }, {
                                                                  default: withCtx(({
                                                                    active
                                                                  }) => [
                                                                    createVNode("li", {
                                                                      class: [
                                                                        "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                        active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                      ]
                                                                    }, [
                                                                      createVNode(_component_Link, {
                                                                        class: "w-full",
                                                                        href: hit.public_url,
                                                                        onClick: ($event) => $setup.open = false
                                                                      }, {
                                                                        default: withCtx(() => [
                                                                          createVNode(_component_FolderIcon, {
                                                                            class: [
                                                                              "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                              active && "text-opacity-100"
                                                                            ],
                                                                            "aria-hidden": "true"
                                                                          }, null, 8, ["class"]),
                                                                          createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                          active ? (openBlock(), createBlock("span", {
                                                                            key: 0,
                                                                            class: "ml-3 flex-none text-gray-500"
                                                                          }, "Jump to...")) : createCommentVNode("", true)
                                                                        ]),
                                                                        _: 2
                                                                      }, 1032, ["href", "onClick"])
                                                                    ], 2)
                                                                  ]),
                                                                  _: 2
                                                                }, 1032, ["value"]);
                                                              }), 128))
                                                            ])
                                                          ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                                            currentRefinement !== "" && index.hits.length === 0 ? (openBlock(), createBlock("div", {
                                                              key: 0,
                                                              class: "py-14 px-6 text-center sm:px-14"
                                                            }, [
                                                              createVNode("p", { class: "mt-4 text-sm text-gray-900" }, " We couldn't find any results with that term. Please try again. ")
                                                            ])) : createCommentVNode("", true)
                                                          ]))
                                                        ])) : createCommentVNode("", true)
                                                      ])) : createCommentVNode("", true)
                                                    ]);
                                                  }), 128)),
                                                  currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                    key: 0,
                                                    class: "p-2"
                                                  }, [
                                                    createVNode("h2", { class: "sr-only" }, " Quick actions "),
                                                    createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                      (openBlock(true), createBlock(Fragment, null, renderList(_ctx.quickActions, (action) => {
                                                        return openBlock(), createBlock(_component_ComboboxOption, {
                                                          key: action.shortcut,
                                                          value: action,
                                                          as: "template"
                                                        }, {
                                                          default: withCtx(({ active }) => [
                                                            createVNode("li", {
                                                              class: [
                                                                "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                                active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                              ]
                                                            }, [
                                                              (openBlock(), createBlock(resolveDynamicComponent(
                                                                action.icon
                                                              ), {
                                                                class: [
                                                                  "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                                  active && "text-opacity-100"
                                                                ],
                                                                "aria-hidden": "true"
                                                              }, null, 8, ["class"])),
                                                              createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                            ], 2)
                                                          ]),
                                                          _: 2
                                                        }, 1032, ["value"]);
                                                      }), 128))
                                                    ])
                                                  ])) : createCommentVNode("", true)
                                                ]),
                                                _: 2
                                              }, 1024)
                                            ];
                                          }
                                        }),
                                        _: 2
                                      }, _parent7, _scopeId6));
                                    } else {
                                      return [
                                        createVNode(_component_Combobox, {
                                          as: "div",
                                          class: "mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all",
                                          "onUpdate:modelValue": $setup.onSelect
                                        }, {
                                          default: withCtx(() => [
                                            createVNode("div", { class: "relative" }, [
                                              createVNode(_component_MagnifyingGlassIcon, {
                                                class: "pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40",
                                                "aria-hidden": "true"
                                              }),
                                              createVNode(_component_ComboboxInput, {
                                                id: "searchAC",
                                                class: "h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm",
                                                placeholder: "Search",
                                                onInput: ($event) => refine(
                                                  $event.currentTarget.value
                                                )
                                              }, null, 8, ["onInput"])
                                            ]),
                                            createVNode(_component_ComboboxOptions, {
                                              static: "",
                                              class: "max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                                            }, {
                                              default: withCtx(() => [
                                                (openBlock(true), createBlock(Fragment, null, renderList(indices, (index) => {
                                                  return openBlock(), createBlock("div", {
                                                    key: index.indexId,
                                                    "aria-labelledby": "searchAC",
                                                    class: "dropdown-menu p-0 w-100"
                                                  }, [
                                                    currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                      key: 0,
                                                      class: "p-2"
                                                    }, [
                                                      createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, " Type to search projects or datasets or click below for help. ")
                                                    ])) : createCommentVNode("", true),
                                                    currentRefinement != "" ? (openBlock(), createBlock("li", {
                                                      key: 1,
                                                      class: "p-2"
                                                    }, [
                                                      currentRefinement === "" && _ctx.recent.length > 0 ? (openBlock(), createBlock("h2", {
                                                        key: 0,
                                                        class: "mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                                                      }, " Recent searches ")) : createCommentVNode("", true),
                                                      currentRefinement ? (openBlock(), createBlock("div", {
                                                        key: 1,
                                                        class: "border rounded-md"
                                                      }, [
                                                        index.hits.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                                                          createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                            (openBlock(true), createBlock(Fragment, null, renderList(index.hits, (hit) => {
                                                              return openBlock(), createBlock(_component_ComboboxOption, {
                                                                key: hit.objectID,
                                                                value: hit,
                                                                as: "template"
                                                              }, {
                                                                default: withCtx(({
                                                                  active
                                                                }) => [
                                                                  createVNode("li", {
                                                                    class: [
                                                                      "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                      active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                    ]
                                                                  }, [
                                                                    createVNode(_component_Link, {
                                                                      class: "w-full",
                                                                      href: hit.public_url,
                                                                      onClick: ($event) => $setup.open = false
                                                                    }, {
                                                                      default: withCtx(() => [
                                                                        createVNode(_component_FolderIcon, {
                                                                          class: [
                                                                            "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                            active && "text-opacity-100"
                                                                          ],
                                                                          "aria-hidden": "true"
                                                                        }, null, 8, ["class"]),
                                                                        createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                        active ? (openBlock(), createBlock("span", {
                                                                          key: 0,
                                                                          class: "ml-3 flex-none text-gray-500"
                                                                        }, "Jump to...")) : createCommentVNode("", true)
                                                                      ]),
                                                                      _: 2
                                                                    }, 1032, ["href", "onClick"])
                                                                  ], 2)
                                                                ]),
                                                                _: 2
                                                              }, 1032, ["value"]);
                                                            }), 128))
                                                          ])
                                                        ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                                          currentRefinement !== "" && index.hits.length === 0 ? (openBlock(), createBlock("div", {
                                                            key: 0,
                                                            class: "py-14 px-6 text-center sm:px-14"
                                                          }, [
                                                            createVNode("p", { class: "mt-4 text-sm text-gray-900" }, " We couldn't find any results with that term. Please try again. ")
                                                          ])) : createCommentVNode("", true)
                                                        ]))
                                                      ])) : createCommentVNode("", true)
                                                    ])) : createCommentVNode("", true)
                                                  ]);
                                                }), 128)),
                                                currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                  key: 0,
                                                  class: "p-2"
                                                }, [
                                                  createVNode("h2", { class: "sr-only" }, " Quick actions "),
                                                  createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                    (openBlock(true), createBlock(Fragment, null, renderList(_ctx.quickActions, (action) => {
                                                      return openBlock(), createBlock(_component_ComboboxOption, {
                                                        key: action.shortcut,
                                                        value: action,
                                                        as: "template"
                                                      }, {
                                                        default: withCtx(({ active }) => [
                                                          createVNode("li", {
                                                            class: [
                                                              "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                              active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                            ]
                                                          }, [
                                                            (openBlock(), createBlock(resolveDynamicComponent(
                                                              action.icon
                                                            ), {
                                                              class: [
                                                                "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                                active && "text-opacity-100"
                                                              ],
                                                              "aria-hidden": "true"
                                                            }, null, 8, ["class"])),
                                                            createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                          ], 2)
                                                        ]),
                                                        _: 2
                                                      }, 1032, ["value"]);
                                                    }), 128))
                                                  ])
                                                ])) : createCommentVNode("", true)
                                              ]),
                                              _: 2
                                            }, 1024)
                                          ]),
                                          _: 2
                                        }, 1032, ["onUpdate:modelValue"])
                                      ];
                                    }
                                  }),
                                  _: 2
                                }, _parent6, _scopeId5));
                              } else {
                                return [
                                  createVNode(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                                    default: withCtx(() => [
                                      createVNode(_component_Combobox, {
                                        as: "div",
                                        class: "mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all",
                                        "onUpdate:modelValue": $setup.onSelect
                                      }, {
                                        default: withCtx(() => [
                                          createVNode("div", { class: "relative" }, [
                                            createVNode(_component_MagnifyingGlassIcon, {
                                              class: "pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40",
                                              "aria-hidden": "true"
                                            }),
                                            createVNode(_component_ComboboxInput, {
                                              id: "searchAC",
                                              class: "h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm",
                                              placeholder: "Search",
                                              onInput: ($event) => refine(
                                                $event.currentTarget.value
                                              )
                                            }, null, 8, ["onInput"])
                                          ]),
                                          createVNode(_component_ComboboxOptions, {
                                            static: "",
                                            class: "max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                                          }, {
                                            default: withCtx(() => [
                                              (openBlock(true), createBlock(Fragment, null, renderList(indices, (index) => {
                                                return openBlock(), createBlock("div", {
                                                  key: index.indexId,
                                                  "aria-labelledby": "searchAC",
                                                  class: "dropdown-menu p-0 w-100"
                                                }, [
                                                  currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                    key: 0,
                                                    class: "p-2"
                                                  }, [
                                                    createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, " Type to search projects or datasets or click below for help. ")
                                                  ])) : createCommentVNode("", true),
                                                  currentRefinement != "" ? (openBlock(), createBlock("li", {
                                                    key: 1,
                                                    class: "p-2"
                                                  }, [
                                                    currentRefinement === "" && _ctx.recent.length > 0 ? (openBlock(), createBlock("h2", {
                                                      key: 0,
                                                      class: "mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                                                    }, " Recent searches ")) : createCommentVNode("", true),
                                                    currentRefinement ? (openBlock(), createBlock("div", {
                                                      key: 1,
                                                      class: "border rounded-md"
                                                    }, [
                                                      index.hits.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                                                        createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                          (openBlock(true), createBlock(Fragment, null, renderList(index.hits, (hit) => {
                                                            return openBlock(), createBlock(_component_ComboboxOption, {
                                                              key: hit.objectID,
                                                              value: hit,
                                                              as: "template"
                                                            }, {
                                                              default: withCtx(({
                                                                active
                                                              }) => [
                                                                createVNode("li", {
                                                                  class: [
                                                                    "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                    active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                  ]
                                                                }, [
                                                                  createVNode(_component_Link, {
                                                                    class: "w-full",
                                                                    href: hit.public_url,
                                                                    onClick: ($event) => $setup.open = false
                                                                  }, {
                                                                    default: withCtx(() => [
                                                                      createVNode(_component_FolderIcon, {
                                                                        class: [
                                                                          "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                          active && "text-opacity-100"
                                                                        ],
                                                                        "aria-hidden": "true"
                                                                      }, null, 8, ["class"]),
                                                                      createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                      active ? (openBlock(), createBlock("span", {
                                                                        key: 0,
                                                                        class: "ml-3 flex-none text-gray-500"
                                                                      }, "Jump to...")) : createCommentVNode("", true)
                                                                    ]),
                                                                    _: 2
                                                                  }, 1032, ["href", "onClick"])
                                                                ], 2)
                                                              ]),
                                                              _: 2
                                                            }, 1032, ["value"]);
                                                          }), 128))
                                                        ])
                                                      ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                                        currentRefinement !== "" && index.hits.length === 0 ? (openBlock(), createBlock("div", {
                                                          key: 0,
                                                          class: "py-14 px-6 text-center sm:px-14"
                                                        }, [
                                                          createVNode("p", { class: "mt-4 text-sm text-gray-900" }, " We couldn't find any results with that term. Please try again. ")
                                                        ])) : createCommentVNode("", true)
                                                      ]))
                                                    ])) : createCommentVNode("", true)
                                                  ])) : createCommentVNode("", true)
                                                ]);
                                              }), 128)),
                                              currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                key: 0,
                                                class: "p-2"
                                              }, [
                                                createVNode("h2", { class: "sr-only" }, " Quick actions "),
                                                createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                  (openBlock(true), createBlock(Fragment, null, renderList(_ctx.quickActions, (action) => {
                                                    return openBlock(), createBlock(_component_ComboboxOption, {
                                                      key: action.shortcut,
                                                      value: action,
                                                      as: "template"
                                                    }, {
                                                      default: withCtx(({ active }) => [
                                                        createVNode("li", {
                                                          class: [
                                                            "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                            active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                          ]
                                                        }, [
                                                          (openBlock(), createBlock(resolveDynamicComponent(
                                                            action.icon
                                                          ), {
                                                            class: [
                                                              "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                              active && "text-opacity-100"
                                                            ],
                                                            "aria-hidden": "true"
                                                          }, null, 8, ["class"])),
                                                          createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                        ], 2)
                                                      ]),
                                                      _: 2
                                                    }, 1032, ["value"]);
                                                  }), 128))
                                                ])
                                              ])) : createCommentVNode("", true)
                                            ]),
                                            _: 2
                                          }, 1024)
                                        ]),
                                        _: 2
                                      }, 1032, ["onUpdate:modelValue"])
                                    ]),
                                    _: 2
                                  }, 1024)
                                ];
                              }
                            }),
                            _: 2
                          }, _parent5, _scopeId4));
                          _push5(`</div>`);
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
                                  createVNode(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                                    default: withCtx(() => [
                                      createVNode(_component_Combobox, {
                                        as: "div",
                                        class: "mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all",
                                        "onUpdate:modelValue": $setup.onSelect
                                      }, {
                                        default: withCtx(() => [
                                          createVNode("div", { class: "relative" }, [
                                            createVNode(_component_MagnifyingGlassIcon, {
                                              class: "pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40",
                                              "aria-hidden": "true"
                                            }),
                                            createVNode(_component_ComboboxInput, {
                                              id: "searchAC",
                                              class: "h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm",
                                              placeholder: "Search",
                                              onInput: ($event) => refine(
                                                $event.currentTarget.value
                                              )
                                            }, null, 8, ["onInput"])
                                          ]),
                                          createVNode(_component_ComboboxOptions, {
                                            static: "",
                                            class: "max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                                          }, {
                                            default: withCtx(() => [
                                              (openBlock(true), createBlock(Fragment, null, renderList(indices, (index) => {
                                                return openBlock(), createBlock("div", {
                                                  key: index.indexId,
                                                  "aria-labelledby": "searchAC",
                                                  class: "dropdown-menu p-0 w-100"
                                                }, [
                                                  currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                    key: 0,
                                                    class: "p-2"
                                                  }, [
                                                    createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, " Type to search projects or datasets or click below for help. ")
                                                  ])) : createCommentVNode("", true),
                                                  currentRefinement != "" ? (openBlock(), createBlock("li", {
                                                    key: 1,
                                                    class: "p-2"
                                                  }, [
                                                    currentRefinement === "" && _ctx.recent.length > 0 ? (openBlock(), createBlock("h2", {
                                                      key: 0,
                                                      class: "mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                                                    }, " Recent searches ")) : createCommentVNode("", true),
                                                    currentRefinement ? (openBlock(), createBlock("div", {
                                                      key: 1,
                                                      class: "border rounded-md"
                                                    }, [
                                                      index.hits.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                                                        createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                          (openBlock(true), createBlock(Fragment, null, renderList(index.hits, (hit) => {
                                                            return openBlock(), createBlock(_component_ComboboxOption, {
                                                              key: hit.objectID,
                                                              value: hit,
                                                              as: "template"
                                                            }, {
                                                              default: withCtx(({
                                                                active
                                                              }) => [
                                                                createVNode("li", {
                                                                  class: [
                                                                    "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                    active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                  ]
                                                                }, [
                                                                  createVNode(_component_Link, {
                                                                    class: "w-full",
                                                                    href: hit.public_url,
                                                                    onClick: ($event) => $setup.open = false
                                                                  }, {
                                                                    default: withCtx(() => [
                                                                      createVNode(_component_FolderIcon, {
                                                                        class: [
                                                                          "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                          active && "text-opacity-100"
                                                                        ],
                                                                        "aria-hidden": "true"
                                                                      }, null, 8, ["class"]),
                                                                      createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                      active ? (openBlock(), createBlock("span", {
                                                                        key: 0,
                                                                        class: "ml-3 flex-none text-gray-500"
                                                                      }, "Jump to...")) : createCommentVNode("", true)
                                                                    ]),
                                                                    _: 2
                                                                  }, 1032, ["href", "onClick"])
                                                                ], 2)
                                                              ]),
                                                              _: 2
                                                            }, 1032, ["value"]);
                                                          }), 128))
                                                        ])
                                                      ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                                        currentRefinement !== "" && index.hits.length === 0 ? (openBlock(), createBlock("div", {
                                                          key: 0,
                                                          class: "py-14 px-6 text-center sm:px-14"
                                                        }, [
                                                          createVNode("p", { class: "mt-4 text-sm text-gray-900" }, " We couldn't find any results with that term. Please try again. ")
                                                        ])) : createCommentVNode("", true)
                                                      ]))
                                                    ])) : createCommentVNode("", true)
                                                  ])) : createCommentVNode("", true)
                                                ]);
                                              }), 128)),
                                              currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                key: 0,
                                                class: "p-2"
                                              }, [
                                                createVNode("h2", { class: "sr-only" }, " Quick actions "),
                                                createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                  (openBlock(true), createBlock(Fragment, null, renderList(_ctx.quickActions, (action) => {
                                                    return openBlock(), createBlock(_component_ComboboxOption, {
                                                      key: action.shortcut,
                                                      value: action,
                                                      as: "template"
                                                    }, {
                                                      default: withCtx(({ active }) => [
                                                        createVNode("li", {
                                                          class: [
                                                            "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                            active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                          ]
                                                        }, [
                                                          (openBlock(), createBlock(resolveDynamicComponent(
                                                            action.icon
                                                          ), {
                                                            class: [
                                                              "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                              active && "text-opacity-100"
                                                            ],
                                                            "aria-hidden": "true"
                                                          }, null, 8, ["class"])),
                                                          createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                        ], 2)
                                                      ]),
                                                      _: 2
                                                    }, 1032, ["value"]);
                                                  }), 128))
                                                ])
                                              ])) : createCommentVNode("", true)
                                            ]),
                                            _: 2
                                          }, 1024)
                                        ]),
                                        _: 2
                                      }, 1032, ["onUpdate:modelValue"])
                                    ]),
                                    _: 2
                                  }, 1024)
                                ]),
                                _: 2
                              }, 1024)
                            ])
                          ];
                        }
                      }),
                      _: 2
                    }, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_Dialog, {
                        as: "div",
                        class: "relative z-10",
                        onClose: ($event) => $setup.open = false
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
                                createVNode(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                                  default: withCtx(() => [
                                    createVNode(_component_Combobox, {
                                      as: "div",
                                      class: "mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all",
                                      "onUpdate:modelValue": $setup.onSelect
                                    }, {
                                      default: withCtx(() => [
                                        createVNode("div", { class: "relative" }, [
                                          createVNode(_component_MagnifyingGlassIcon, {
                                            class: "pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40",
                                            "aria-hidden": "true"
                                          }),
                                          createVNode(_component_ComboboxInput, {
                                            id: "searchAC",
                                            class: "h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm",
                                            placeholder: "Search",
                                            onInput: ($event) => refine(
                                              $event.currentTarget.value
                                            )
                                          }, null, 8, ["onInput"])
                                        ]),
                                        createVNode(_component_ComboboxOptions, {
                                          static: "",
                                          class: "max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                                        }, {
                                          default: withCtx(() => [
                                            (openBlock(true), createBlock(Fragment, null, renderList(indices, (index) => {
                                              return openBlock(), createBlock("div", {
                                                key: index.indexId,
                                                "aria-labelledby": "searchAC",
                                                class: "dropdown-menu p-0 w-100"
                                              }, [
                                                currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                  key: 0,
                                                  class: "p-2"
                                                }, [
                                                  createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, " Type to search projects or datasets or click below for help. ")
                                                ])) : createCommentVNode("", true),
                                                currentRefinement != "" ? (openBlock(), createBlock("li", {
                                                  key: 1,
                                                  class: "p-2"
                                                }, [
                                                  currentRefinement === "" && _ctx.recent.length > 0 ? (openBlock(), createBlock("h2", {
                                                    key: 0,
                                                    class: "mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                                                  }, " Recent searches ")) : createCommentVNode("", true),
                                                  currentRefinement ? (openBlock(), createBlock("div", {
                                                    key: 1,
                                                    class: "border rounded-md"
                                                  }, [
                                                    index.hits.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                                                      createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                        (openBlock(true), createBlock(Fragment, null, renderList(index.hits, (hit) => {
                                                          return openBlock(), createBlock(_component_ComboboxOption, {
                                                            key: hit.objectID,
                                                            value: hit,
                                                            as: "template"
                                                          }, {
                                                            default: withCtx(({
                                                              active
                                                            }) => [
                                                              createVNode("li", {
                                                                class: [
                                                                  "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                  active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                                ]
                                                              }, [
                                                                createVNode(_component_Link, {
                                                                  class: "w-full",
                                                                  href: hit.public_url,
                                                                  onClick: ($event) => $setup.open = false
                                                                }, {
                                                                  default: withCtx(() => [
                                                                    createVNode(_component_FolderIcon, {
                                                                      class: [
                                                                        "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                        active && "text-opacity-100"
                                                                      ],
                                                                      "aria-hidden": "true"
                                                                    }, null, 8, ["class"]),
                                                                    createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                    active ? (openBlock(), createBlock("span", {
                                                                      key: 0,
                                                                      class: "ml-3 flex-none text-gray-500"
                                                                    }, "Jump to...")) : createCommentVNode("", true)
                                                                  ]),
                                                                  _: 2
                                                                }, 1032, ["href", "onClick"])
                                                              ], 2)
                                                            ]),
                                                            _: 2
                                                          }, 1032, ["value"]);
                                                        }), 128))
                                                      ])
                                                    ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                                      currentRefinement !== "" && index.hits.length === 0 ? (openBlock(), createBlock("div", {
                                                        key: 0,
                                                        class: "py-14 px-6 text-center sm:px-14"
                                                      }, [
                                                        createVNode("p", { class: "mt-4 text-sm text-gray-900" }, " We couldn't find any results with that term. Please try again. ")
                                                      ])) : createCommentVNode("", true)
                                                    ]))
                                                  ])) : createCommentVNode("", true)
                                                ])) : createCommentVNode("", true)
                                              ]);
                                            }), 128)),
                                            currentRefinement === "" ? (openBlock(), createBlock("li", {
                                              key: 0,
                                              class: "p-2"
                                            }, [
                                              createVNode("h2", { class: "sr-only" }, " Quick actions "),
                                              createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                (openBlock(true), createBlock(Fragment, null, renderList(_ctx.quickActions, (action) => {
                                                  return openBlock(), createBlock(_component_ComboboxOption, {
                                                    key: action.shortcut,
                                                    value: action,
                                                    as: "template"
                                                  }, {
                                                    default: withCtx(({ active }) => [
                                                      createVNode("li", {
                                                        class: [
                                                          "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                          active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                        ]
                                                      }, [
                                                        (openBlock(), createBlock(resolveDynamicComponent(
                                                          action.icon
                                                        ), {
                                                          class: [
                                                            "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                            active && "text-opacity-100"
                                                          ],
                                                          "aria-hidden": "true"
                                                        }, null, 8, ["class"])),
                                                        createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                      ], 2)
                                                    ]),
                                                    _: 2
                                                  }, 1032, ["value"]);
                                                }), 128))
                                              ])
                                            ])) : createCommentVNode("", true)
                                          ]),
                                          _: 2
                                        }, 1024)
                                      ]),
                                      _: 2
                                    }, 1032, ["onUpdate:modelValue"])
                                  ]),
                                  _: 2
                                }, 1024)
                              ]),
                              _: 2
                            }, 1024)
                          ])
                        ]),
                        _: 2
                      }, 1032, ["onClose"])
                    ];
                  }
                }),
                _: 2
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_TransitionRoot, {
                  show: $setup.open,
                  as: "template",
                  appear: "",
                  onAfterLeave: ($event) => _ctx.query = ""
                }, {
                  default: withCtx(() => [
                    createVNode(_component_Dialog, {
                      as: "div",
                      class: "relative z-10",
                      onClose: ($event) => $setup.open = false
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
                              createVNode(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                                default: withCtx(() => [
                                  createVNode(_component_Combobox, {
                                    as: "div",
                                    class: "mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all",
                                    "onUpdate:modelValue": $setup.onSelect
                                  }, {
                                    default: withCtx(() => [
                                      createVNode("div", { class: "relative" }, [
                                        createVNode(_component_MagnifyingGlassIcon, {
                                          class: "pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40",
                                          "aria-hidden": "true"
                                        }),
                                        createVNode(_component_ComboboxInput, {
                                          id: "searchAC",
                                          class: "h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm",
                                          placeholder: "Search",
                                          onInput: ($event) => refine(
                                            $event.currentTarget.value
                                          )
                                        }, null, 8, ["onInput"])
                                      ]),
                                      createVNode(_component_ComboboxOptions, {
                                        static: "",
                                        class: "max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                                      }, {
                                        default: withCtx(() => [
                                          (openBlock(true), createBlock(Fragment, null, renderList(indices, (index) => {
                                            return openBlock(), createBlock("div", {
                                              key: index.indexId,
                                              "aria-labelledby": "searchAC",
                                              class: "dropdown-menu p-0 w-100"
                                            }, [
                                              currentRefinement === "" ? (openBlock(), createBlock("li", {
                                                key: 0,
                                                class: "p-2"
                                              }, [
                                                createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, " Type to search projects or datasets or click below for help. ")
                                              ])) : createCommentVNode("", true),
                                              currentRefinement != "" ? (openBlock(), createBlock("li", {
                                                key: 1,
                                                class: "p-2"
                                              }, [
                                                currentRefinement === "" && _ctx.recent.length > 0 ? (openBlock(), createBlock("h2", {
                                                  key: 0,
                                                  class: "mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                                                }, " Recent searches ")) : createCommentVNode("", true),
                                                currentRefinement ? (openBlock(), createBlock("div", {
                                                  key: 1,
                                                  class: "border rounded-md"
                                                }, [
                                                  index.hits.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                                                    createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                      (openBlock(true), createBlock(Fragment, null, renderList(index.hits, (hit) => {
                                                        return openBlock(), createBlock(_component_ComboboxOption, {
                                                          key: hit.objectID,
                                                          value: hit,
                                                          as: "template"
                                                        }, {
                                                          default: withCtx(({
                                                            active
                                                          }) => [
                                                            createVNode("li", {
                                                              class: [
                                                                "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                                active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                              ]
                                                            }, [
                                                              createVNode(_component_Link, {
                                                                class: "w-full",
                                                                href: hit.public_url,
                                                                onClick: ($event) => $setup.open = false
                                                              }, {
                                                                default: withCtx(() => [
                                                                  createVNode(_component_FolderIcon, {
                                                                    class: [
                                                                      "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                      active && "text-opacity-100"
                                                                    ],
                                                                    "aria-hidden": "true"
                                                                  }, null, 8, ["class"]),
                                                                  createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                  active ? (openBlock(), createBlock("span", {
                                                                    key: 0,
                                                                    class: "ml-3 flex-none text-gray-500"
                                                                  }, "Jump to...")) : createCommentVNode("", true)
                                                                ]),
                                                                _: 2
                                                              }, 1032, ["href", "onClick"])
                                                            ], 2)
                                                          ]),
                                                          _: 2
                                                        }, 1032, ["value"]);
                                                      }), 128))
                                                    ])
                                                  ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                                    currentRefinement !== "" && index.hits.length === 0 ? (openBlock(), createBlock("div", {
                                                      key: 0,
                                                      class: "py-14 px-6 text-center sm:px-14"
                                                    }, [
                                                      createVNode("p", { class: "mt-4 text-sm text-gray-900" }, " We couldn't find any results with that term. Please try again. ")
                                                    ])) : createCommentVNode("", true)
                                                  ]))
                                                ])) : createCommentVNode("", true)
                                              ])) : createCommentVNode("", true)
                                            ]);
                                          }), 128)),
                                          currentRefinement === "" ? (openBlock(), createBlock("li", {
                                            key: 0,
                                            class: "p-2"
                                          }, [
                                            createVNode("h2", { class: "sr-only" }, " Quick actions "),
                                            createVNode("ul", { class: "text-sm text-gray-700" }, [
                                              (openBlock(true), createBlock(Fragment, null, renderList(_ctx.quickActions, (action) => {
                                                return openBlock(), createBlock(_component_ComboboxOption, {
                                                  key: action.shortcut,
                                                  value: action,
                                                  as: "template"
                                                }, {
                                                  default: withCtx(({ active }) => [
                                                    createVNode("li", {
                                                      class: [
                                                        "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                        active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                      ]
                                                    }, [
                                                      (openBlock(), createBlock(resolveDynamicComponent(
                                                        action.icon
                                                      ), {
                                                        class: [
                                                          "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                          active && "text-opacity-100"
                                                        ],
                                                        "aria-hidden": "true"
                                                      }, null, 8, ["class"])),
                                                      createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                    ], 2)
                                                  ]),
                                                  _: 2
                                                }, 1032, ["value"]);
                                              }), 128))
                                            ])
                                          ])) : createCommentVNode("", true)
                                        ]),
                                        _: 2
                                      }, 1024)
                                    ]),
                                    _: 2
                                  }, 1032, ["onUpdate:modelValue"])
                                ]),
                                _: 2
                              }, 1024)
                            ]),
                            _: 2
                          }, 1024)
                        ])
                      ]),
                      _: 2
                    }, 1032, ["onClose"])
                  ]),
                  _: 2
                }, 1032, ["show", "onAfterLeave"])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_ais_autocomplete, null, {
            default: withCtx(({ currentRefinement, indices, refine }) => [
              createVNode(_component_TransitionRoot, {
                show: $setup.open,
                as: "template",
                appear: "",
                onAfterLeave: ($event) => _ctx.query = ""
              }, {
                default: withCtx(() => [
                  createVNode(_component_Dialog, {
                    as: "div",
                    class: "relative z-10",
                    onClose: ($event) => $setup.open = false
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
                            createVNode(_component_DialogPanel, { class: "mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all" }, {
                              default: withCtx(() => [
                                createVNode(_component_Combobox, {
                                  as: "div",
                                  class: "mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all",
                                  "onUpdate:modelValue": $setup.onSelect
                                }, {
                                  default: withCtx(() => [
                                    createVNode("div", { class: "relative" }, [
                                      createVNode(_component_MagnifyingGlassIcon, {
                                        class: "pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40",
                                        "aria-hidden": "true"
                                      }),
                                      createVNode(_component_ComboboxInput, {
                                        id: "searchAC",
                                        class: "h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm",
                                        placeholder: "Search",
                                        onInput: ($event) => refine(
                                          $event.currentTarget.value
                                        )
                                      }, null, 8, ["onInput"])
                                    ]),
                                    createVNode(_component_ComboboxOptions, {
                                      static: "",
                                      class: "max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
                                    }, {
                                      default: withCtx(() => [
                                        (openBlock(true), createBlock(Fragment, null, renderList(indices, (index) => {
                                          return openBlock(), createBlock("div", {
                                            key: index.indexId,
                                            "aria-labelledby": "searchAC",
                                            class: "dropdown-menu p-0 w-100"
                                          }, [
                                            currentRefinement === "" ? (openBlock(), createBlock("li", {
                                              key: 0,
                                              class: "p-2"
                                            }, [
                                              createVNode("div", { class: "flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700" }, " Type to search projects or datasets or click below for help. ")
                                            ])) : createCommentVNode("", true),
                                            currentRefinement != "" ? (openBlock(), createBlock("li", {
                                              key: 1,
                                              class: "p-2"
                                            }, [
                                              currentRefinement === "" && _ctx.recent.length > 0 ? (openBlock(), createBlock("h2", {
                                                key: 0,
                                                class: "mt-4 mb-2 px-3 text-xs font-semibold text-gray-900"
                                              }, " Recent searches ")) : createCommentVNode("", true),
                                              currentRefinement ? (openBlock(), createBlock("div", {
                                                key: 1,
                                                class: "border rounded-md"
                                              }, [
                                                index.hits.length > 0 ? (openBlock(), createBlock("span", { key: 0 }, [
                                                  createVNode("ul", { class: "text-sm text-gray-700" }, [
                                                    (openBlock(true), createBlock(Fragment, null, renderList(index.hits, (hit) => {
                                                      return openBlock(), createBlock(_component_ComboboxOption, {
                                                        key: hit.objectID,
                                                        value: hit,
                                                        as: "template"
                                                      }, {
                                                        default: withCtx(({
                                                          active
                                                        }) => [
                                                          createVNode("li", {
                                                            class: [
                                                              "flex cursor-default select-none items-center rounded-md px-3 py-2",
                                                              active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                            ]
                                                          }, [
                                                            createVNode(_component_Link, {
                                                              class: "w-full",
                                                              href: hit.public_url,
                                                              onClick: ($event) => $setup.open = false
                                                            }, {
                                                              default: withCtx(() => [
                                                                createVNode(_component_FolderIcon, {
                                                                  class: [
                                                                    "h-6 w-6 inline flex-none text-gray-900 text-opacity-40",
                                                                    active && "text-opacity-100"
                                                                  ],
                                                                  "aria-hidden": "true"
                                                                }, null, 8, ["class"]),
                                                                createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(hit.name), 1),
                                                                active ? (openBlock(), createBlock("span", {
                                                                  key: 0,
                                                                  class: "ml-3 flex-none text-gray-500"
                                                                }, "Jump to...")) : createCommentVNode("", true)
                                                              ]),
                                                              _: 2
                                                            }, 1032, ["href", "onClick"])
                                                          ], 2)
                                                        ]),
                                                        _: 2
                                                      }, 1032, ["value"]);
                                                    }), 128))
                                                  ])
                                                ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                                  currentRefinement !== "" && index.hits.length === 0 ? (openBlock(), createBlock("div", {
                                                    key: 0,
                                                    class: "py-14 px-6 text-center sm:px-14"
                                                  }, [
                                                    createVNode("p", { class: "mt-4 text-sm text-gray-900" }, " We couldn't find any results with that term. Please try again. ")
                                                  ])) : createCommentVNode("", true)
                                                ]))
                                              ])) : createCommentVNode("", true)
                                            ])) : createCommentVNode("", true)
                                          ]);
                                        }), 128)),
                                        currentRefinement === "" ? (openBlock(), createBlock("li", {
                                          key: 0,
                                          class: "p-2"
                                        }, [
                                          createVNode("h2", { class: "sr-only" }, " Quick actions "),
                                          createVNode("ul", { class: "text-sm text-gray-700" }, [
                                            (openBlock(true), createBlock(Fragment, null, renderList(_ctx.quickActions, (action) => {
                                              return openBlock(), createBlock(_component_ComboboxOption, {
                                                key: action.shortcut,
                                                value: action,
                                                as: "template"
                                              }, {
                                                default: withCtx(({ active }) => [
                                                  createVNode("li", {
                                                    class: [
                                                      "flex cursor-pointer select-none items-center rounded-md px-3 py-2",
                                                      active && "bg-gray-900 bg-opacity-5 text-gray-900"
                                                    ]
                                                  }, [
                                                    (openBlock(), createBlock(resolveDynamicComponent(
                                                      action.icon
                                                    ), {
                                                      class: [
                                                        "h-6 w-6 flex-none text-gray-900 text-opacity-40",
                                                        active && "text-opacity-100"
                                                      ],
                                                      "aria-hidden": "true"
                                                    }, null, 8, ["class"])),
                                                    createVNode("span", { class: "ml-3 flex-auto truncate" }, toDisplayString(action.name), 1)
                                                  ], 2)
                                                ]),
                                                _: 2
                                              }, 1032, ["value"]);
                                            }), 128))
                                          ])
                                        ])) : createCommentVNode("", true)
                                      ]),
                                      _: 2
                                    }, 1024)
                                  ]),
                                  _: 2
                                }, 1032, ["onUpdate:modelValue"])
                              ]),
                              _: 2
                            }, 1024)
                          ]),
                          _: 2
                        }, 1024)
                      ])
                    ]),
                    _: 2
                  }, 1032, ["onClose"])
                ]),
                _: 2
              }, 1032, ["show", "onAfterLeave"])
            ]),
            _: 1
          })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup$g = _sfc_main$g.setup;
_sfc_main$g.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Search.vue");
  return _sfc_setup$g ? _sfc_setup$g(props, ctx) : void 0;
};
const Search = /* @__PURE__ */ _export_sfc(_sfc_main$g, [["ssrRender", _sfc_ssrRender$g]]);
const _sfc_main$f = {
  components: {
    Link,
    Menu,
    MenuItem,
    MenuItems,
    ArchiveBoxIcon,
    PencilSquareIcon,
    TableCellsIcon,
    PlusIcon
  },
  props: {
    mode: String
  },
  setup() {
  },
  computed: {},
  methods: {}
};
function _sfc_ssrRender$f(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  if ($props.mode == "button") {
    _push(`<span>`);
    if (!_ctx.$page.props.auth.user.email) {
      _push(`<span>`);
      _push(ssrRenderComponent(_component_Link, {
        href: "/login",
        class: "p-3 cursor-pointer inline-flex items-center text-center border border-transparent text-base rounded-full shadow-sm text-white inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg xmlns="http://www.w3.org/2000/svg" class="mr-3 ml-2 h-6 w-6 text-dark" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg> Upload   `);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                class: "mr-3 ml-2 h-6 w-6 text-dark",
                viewBox: "0 0 20 20",
                fill: "currentColor"
              }, [
                createVNode("path", {
                  "fill-rule": "evenodd",
                  d: "M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z",
                  "clip-rule": "evenodd"
                })
              ])),
              createTextVNode(" Upload   ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</span>`);
    } else {
      _push(`<span>`);
      _push(ssrRenderComponent(_component_Link, {
        id: "tour-step-upload",
        href: _ctx.route("upload"),
        class: "p-3 inline-flex items-center text-center border border-transparent text-base rounded-full shadow-sm text-white inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg xmlns="http://www.w3.org/2000/svg" class="mr-3 ml-2 h-6 w-6 text-dark" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg> Upload   `);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                class: "mr-3 ml-2 h-6 w-6 text-dark",
                viewBox: "0 0 20 20",
                fill: "currentColor"
              }, [
                createVNode("path", {
                  "fill-rule": "evenodd",
                  d: "M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z",
                  "clip-rule": "evenodd"
                })
              ])),
              createTextVNode(" Upload   ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</span>`);
    }
    _push(`</span>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.mode == "icon") {
    _push(`<span>`);
    if (!_ctx.$page.props.auth.user.email) {
      _push(`<span>`);
      _push(ssrRenderComponent(_component_Link, {
        href: "/login",
        class: "cursor-pointer inline-flex items-center text-center p-3 border border-transparent text-base font-medium rounded-full shadow-sm inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-dark" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                class: "h-6 w-6 text-dark",
                viewBox: "0 0 20 20",
                fill: "currentColor"
              }, [
                createVNode("path", {
                  "fill-rule": "evenodd",
                  d: "M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z",
                  "clip-rule": "evenodd"
                })
              ]))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</span>`);
    } else {
      _push(`<span>`);
      _push(ssrRenderComponent(_component_Link, {
        id: "tour-step-upload",
        href: _ctx.route("upload"),
        class: "inline-flex items-center text-center p-3 border border-transparent text-base font-medium rounded-full shadow-sm inline-flex items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-dark" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"${_scopeId}></path></svg>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                class: "h-6 w-6 text-dark",
                viewBox: "0 0 20 20",
                fill: "currentColor"
              }, [
                createVNode("path", {
                  "fill-rule": "evenodd",
                  d: "M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z",
                  "clip-rule": "evenodd"
                })
              ]))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</span>`);
    }
    _push(`</span>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup$f = _sfc_main$f.setup;
_sfc_main$f.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/CreateButton.vue");
  return _sfc_setup$f ? _sfc_setup$f(props, ctx) : void 0;
};
const Create = /* @__PURE__ */ _export_sfc(_sfc_main$f, [["ssrRender", _sfc_ssrRender$f]]);
const _sfc_main$e = {};
function _sfc_ssrRender$e(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "48",
    height: "42",
    viewBox: "0 0 48 42",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M6.35242 20.7125C6.35242 30.4591 14.2535 38.3601 24 38.3601H24.7065C25.0925 38.3601 25.4118 38.6791 25.4118 39.0726V40.4712C25.4118 40.8708 25.0977 41.1837 24.7101 41.1837H24C12.6941 41.1837 3.52877 32.0184 3.52877 20.7125V19.8855L2.20216 21.2121C1.92597 21.4883 1.47792 21.4852 1.19966 21.207L0.210663 20.2181C-0.0718141 19.9355 -0.0681577 19.4892 0.209351 19.2117L3.94231 15.4787C4.49367 14.9274 5.38751 14.9274 5.93887 15.4787L9.67183 19.2117C9.94662 19.4865 9.94878 19.9398 9.67052 20.2181L8.68152 21.207C8.39904 21.4895 7.95437 21.4875 7.67902 21.2121L6.35242 19.8855V20.7125ZM41.6476 20.7125C41.6476 10.9659 33.7465 3.06492 24 3.06492H23.2973C22.9045 3.06492 22.5883 2.74588 22.5883 2.3524V0.953792C22.5883 0.554217 22.904 0.24127 23.2935 0.24127H24.0001C35.306 0.24127 44.4713 9.40656 44.4713 20.7125V21.5395L45.7979 20.2129C46.0741 19.9367 46.5222 19.9398 46.8004 20.2181L47.7894 21.207C48.0719 21.4895 48.0682 21.9358 47.7907 22.2133L44.0578 25.9463C43.5064 26.4976 42.6126 26.4976 42.0612 25.9463L38.3282 22.2133C38.0534 21.9385 38.0512 21.4852 38.3295 21.207L39.3185 20.2181C39.601 19.9355 40.0455 19.9375 40.321 20.2129L41.6476 21.5395V20.7125Z" fill="black"></path><path d="M16.2907 20.4709C16.2907 15.7923 20.0827 12 24.7616 12C29.4402 12 33.2325 15.7919 33.2325 20.4709C33.2325 25.1494 29.4405 28.9417 24.7616 28.9417C20.083 28.9417 16.2907 25.1498 16.2907 20.4709Z" fill="#019DBB"></path></svg>`);
}
const _sfc_setup$e = _sfc_main$e.setup;
_sfc_main$e.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/ApplicationMark.vue");
  return _sfc_setup$e ? _sfc_setup$e(props, ctx) : void 0;
};
const JetApplicationMark = /* @__PURE__ */ _export_sfc(_sfc_main$e, [["ssrRender", _sfc_ssrRender$e]]);
const _sfc_main$d = {
  components: {
    XMarkIcon,
    Button: Dropdown
  },
  data() {
    return {
      show: true
    };
  },
  computed: {
    style() {
      var _a;
      return ((_a = this.$page.props.jetstream.flash) == null ? void 0 : _a.bannerStyle) || "success";
    },
    message() {
      var _a;
      return ((_a = this.$page.props.jetstream.flash) == null ? void 0 : _a.banner) || "";
    }
  }
};
function _sfc_ssrRender$d(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_XMarkIcon = resolveComponent("XMarkIcon");
  if ($data.show && $options.message) {
    _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed z-50 bg-yellow-400 border-b w-screen border-black-800" }, _attrs))}><div class="max-w-7xl mx-auto py-1 px-6"><p class="text-sm text-center text-dark font-semibold animate-pulse"><span>${ssrInterpolate($options.message)}</span></p><div class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pt-1 sm:pr-2 sm:items-start"><button type="button" class="flex p-1 rounded-md focus:outline-none hover:bg-yellow-300 focus:ring-2 focus:ring-white"><span class="sr-only">Dismiss</span>`);
    _push(ssrRenderComponent(_component_XMarkIcon, {
      class: "h-3 w-3 text-dark",
      "aria-hidden": "true"
    }, null, _parent));
    _push(`</button></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
}
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/Banner.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const JetBanner = /* @__PURE__ */ _export_sfc(_sfc_main$d, [["ssrRender", _sfc_ssrRender$d]]);
const _sfc_main$c = {
  props: {
    align: {
      default: "right"
    },
    width: {
      default: "48"
    },
    contentClasses: {
      default: () => ["py-1", "bg-white"]
    }
  },
  setup() {
    let open2 = ref(false);
    const closeOnEscape = (e) => {
      if (open2.value && e.keyCode === 27) {
        open2.value = false;
      }
    };
    onMounted(() => document.addEventListener("keydown", closeOnEscape));
    onUnmounted(
      () => document.removeEventListener("keydown", closeOnEscape)
    );
    return {
      open: open2
    };
  },
  computed: {
    widthClass() {
      return {
        48: "w-48"
      }[this.width.toString()];
    },
    alignmentClasses() {
      if (this.align === "left") {
        return "origin-top-left left-0";
      } else if (this.align === "right") {
        return "origin-top-right right-0";
      } else {
        return "origin-top";
      }
    }
  }
};
function _sfc_ssrRender$c(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div>`);
  ssrRenderSlot(_ctx.$slots, "trigger", {}, null, _push, _parent);
  _push(`</div><div style="${ssrRenderStyle($setup.open ? null : { display: "none" })}" class="fixed inset-0 z-40"></div><div style="${ssrRenderStyle([
    $setup.open ? null : { display: "none" },
    { "display": "none" }
  ])}" class="${ssrRenderClass([[$options.widthClass, $options.alignmentClasses], "absolute z-50 mt-2 rounded-md shadow-lg"])}"><div class="${ssrRenderClass([$props.contentClasses, "rounded-md ring-1 ring-black ring-opacity-5"])}">`);
  ssrRenderSlot(_ctx.$slots, "content", {}, null, _push, _parent);
  _push(`</div></div></div>`);
}
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/Dropdown.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const JetDropdown = /* @__PURE__ */ _export_sfc(_sfc_main$c, [["ssrRender", _sfc_ssrRender$c]]);
const _sfc_main$b = {
  components: {
    Link
  },
  props: ["href", "as"]
};
function _sfc_ssrRender$b(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  if ($props.as == "button") {
    _push(`<button type="submit" class="block w-full px-4 py-2 text-sm leading-5 text-gray-700 text-left hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition">`);
    ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
    _push(`</button>`);
  } else if ($props.as == "a") {
    _push(ssrRenderComponent(_component_Link, {
      href: $props.href,
      class: "block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          ssrRenderSlot(_ctx.$slots, "default", {}, null, _push2, _parent2, _scopeId);
        } else {
          return [
            renderSlot(_ctx.$slots, "default")
          ];
        }
      }),
      _: 3
    }, _parent));
  } else {
    _push(ssrRenderComponent(_component_Link, {
      href: $props.href,
      class: "block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          ssrRenderSlot(_ctx.$slots, "default", {}, null, _push2, _parent2, _scopeId);
        } else {
          return [
            renderSlot(_ctx.$slots, "default")
          ];
        }
      }),
      _: 3
    }, _parent));
  }
  _push(`</div>`);
}
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/DropdownLink.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const JetDropdownLink = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["ssrRender", _sfc_ssrRender$b]]);
const _sfc_main$a = {
  name: "AppTour",
  data() {
    return {
      steps: [
        {
          target: "#tour-step-upload",
          header: {
            title: "Get Started"
          },
          content: `Click to start uploading <br/> your datasets to nmrXiv. <br/> <small>Check out our guides  <br/>to prepare datasets <br/>for uploading <a class="text-green-400 hover:text-decoration-line" href="https://docs.nmrxiv.org" target="_blank">here</a>.</small>`
        },
        {
          target: "#tour-step-dashboard",
          header: {
            title: "Your space: Dashboard"
          },
          content: `<small>You can find all your work<br/> organised under this section.<br/> All your active projects <br/>and quicklinks are accessible <br/> from your dashboard. <br/>Please remember you will have <br/>dedicated dashboards for you <br/>and your teams.</small>`
        },
        {
          target: "#tour-step-shared-with-me",
          header: {
            title: "Your space: Shared with me"
          },
          content: `<small>Projects and studies shared<br/> with you can be accessed from here.</small>`
        },
        {
          target: "#tour-step-recent",
          header: {
            title: "Your space: Recent"
          },
          content: `<small>Quick access to recently<br/> edited projects and studies.</small>`
        },
        {
          target: "#tour-step-starred",
          header: {
            title: "Your space: Starred"
          },
          content: `<small>Projects and studies that you star<br/> can be accessed quickly from this view.</small>`
        },
        {
          target: "#tour-step-trash",
          header: {
            title: "Your space: Trash"
          },
          content: `<small>Restore your deleted projects and studies <br/> (within 30 days) from here.</small>`
        },
        {
          target: "#tour-step-submission-guide",
          header: {
            title: "Guides on Submission"
          },
          content: `<small>Check out our submission guides any time for more information. Here is a quick link for you right from your dashboard.</small>`
        },
        {
          target: "#tour-step-api",
          header: {
            title: "API"
          },
          content: `<small>Developing a tool? Check out our API to access our data.</small>`
        },
        {
          target: "#tour-step-spectra-challenge",
          header: {
            title: "Spectra Challenges"
          },
          content: `<small>Are you proficient in structure ellucidation? Stay tuned for new challenges.</small>`
        },
        {
          target: "#tour-step-get-in-touch",
          header: {
            title: "Need help! Email us"
          },
          content: `<small>Any queries? Write to us via email.<br/>We are happy to help!</small>`
        },
        {
          target: "#tour-step-support-bubble",
          header: {
            title: "Help! Chat window"
          },
          content: `<small>Reach out to us with any questions, suggestions or bugs. We will get back to you as soon as we can.</small>`
        },
        {
          target: "#tour-step-documentation",
          header: {
            title: "Documentation site"
          },
          content: `<small>Check out our documentation site for the installation, submission guides, design principles and architectural overview.</small>`
        },
        {
          target: "#tour-step-account-management",
          header: {
            title: "Account management"
          },
          content: `<small>Manage your teams, profile and other settings from the drop down here.</small>`
        },
        {
          target: "#tour-step-upload",
          header: {
            title: "Let's upload a demo dataset"
          },
          content: `<small>Click here to start uploading.</small>`,
          before: (type) => new Promise((resolve, reject) => {
            this.emitter.emit("openDatasetCreateDialog", {});
            resolve("upload dialog open");
          })
        },
        {
          target: "#tour-step-submission-header",
          header: {
            title: "Files upload"
          },
          content: `<small>Lets provide some basic details about your submission and then upload files.</small>`
        },
        {
          target: "#tour-step-project-name",
          header: {
            title: "Project Name"
          },
          content: `<small>Enter a suitable name for your project.</small>`
        },
        {
          target: "#tour-step-project-desc",
          header: {
            title: "Project Description"
          },
          content: `<small>Briefly describe your project in not less than 20 words.</small>`
        },
        {
          target: "#tour-step-project-keywords",
          header: {
            title: "Project Keywords"
          },
          content: `<small>Classify your project </br>by entering keywords or tags.</small>`
        },
        {
          target: "#tour-step-upload-spectra",
          header: {
            title: "Upload Spectra files"
          },
          content: `<small>Lets start uploading your spectra data. <a class="text-green-400 hover:text-decoration-line" href="https://drive.google.com/drive/folders/1PS9jYkjCl_2iwwpod7rkxsmiH32QSgCM" target="_blank">Here</a> is an example dataset. Download and then drag and drop the files here.</small>`
        },
        {
          target: "#tour-step-proceed-from-step-1",
          header: {
            title: "Proceed"
          },
          content: `<small>Once the upload is finish. Press on the Proceed button to continue.</small>`
        },
        {
          target: "#tour-step-submission-header",
          header: {
            title: "Assignment and Meta data"
          },
          content: `<small>In this step you can use NMRium to edit your spectra and assign the peaks to the corresponding chemical groups, additionally, you can provide the metadata of the studies and samples.</small>`
        },
        {
          target: "#tour-step-side-panel-studies",
          header: {
            title: "Studies"
          },
          content: `<small>In this panel, you have a view of all the studies in the project with the datasets they include.</small> `
        },
        {
          target: "#tour-step-select-exp",
          header: {
            title: "Select Experiments"
          },
          content: `<small>You can navigate through different datasets (experiments) within the study through this dropdown.</small>`
        },
        {
          target: "#tour-step-nmrium",
          header: {
            title: "NMRium"
          },
          content: `<small>Visualize and analyse your spectra data using NMRium.<br/> You can visualize both 1D and 2D spectra, do smart peak picking and much more. <br/>Click <a class="text-green-400 hover:text-decoration-line" href="https://docs.nmrxiv.org/docs/introduction/nmrium" target="_blank">here</a> to learn more about NMRium.</small> `
        },
        {
          target: "#tour-step-spectra-info",
          header: {
            title: "Spectra Info"
          },
          content: `<small>In the Info table, you get all the information extracted about the NMR experiment.</small>`
        },
        // {
        //     target: "#sample-info",
        //     header: {
        //         title: "Sample Info.",
        //     },
        //     content: `<small></small>`,
        // },
        // {
        //     target: "#meta-data",
        //     header: {
        //         title: "Metadata.",
        //     },
        //     content: `<small></small>`,
        // },
        {
          target: "#tour-step-proceed-from-step-2",
          header: {
            title: "Proceed"
          },
          content: `<small>Click on proceed to submit your data and allow us for further processing.</small>`
        },
        {
          target: "#tour-step-queue",
          header: {
            title: "Queued"
          },
          content: `<small>Give us some moment to process your submission. <br/>You will automatically be routed to next step and also recieve an email once the process is complete.</small>`
        },
        {
          target: "#tour-step-submission-header",
          header: {
            title: "Complete"
          },
          content: `<small>GREAT!! <br/>Your data is now submitted to us and you are almost done with the submission process. </br>Here you find the summary of the project and we ask you to update few of the essential info to make your project complete. </small>`
        },
        {
          target: "#tour-step-summary",
          header: {
            title: "Summary"
          },
          content: `<small>Checkout to find the quick summary of your Study(s) and the list of Dataset(s) within.</small>`
        },
        {
          target: "#tour-step-privacy",
          header: {
            title: "Privacy"
          },
          content: `<small>From here you can choose to keep your project as private or make it public for open access.</small>`
        },
        {
          target: "#tour-step-release-date",
          header: {
            title: "Release Date"
          },
          content: `<small>Choose a release date to auto publish your project on selected date and time.</small>`
        },
        {
          target: "#tour-step-license",
          header: {
            title: "License"
          },
          content: `<small>Add a license to your project from the drop down. </br> This step is required before making your project public.</small>`
        },
        {
          target: "#tour-step-save",
          header: {
            title: "Save"
          },
          content: `<small>Dont forget to click on Save after making any changes.</small>`
        },
        {
          target: "#tour-step-download",
          header: {
            title: "Download"
          },
          content: `<small>Here is the quick link to download your project.</small>`
        },
        {
          target: "#tour-step-finish",
          header: {
            title: "Finish"
          },
          content: `<small>Thanks for completing the tour!! Press on Finish once your final changes are done and you will be routed to the dashboard where you can find your project listed. </small>`
        }
        /* {
            target: "#tour-step-projects",
            header: {
                title: "Projects.",
            },
            content: `<small>Now that the project is created succesfully, you can find the list of your projects in this section.<br/> You can always visit your project by clicking on it and make changes if required.</small>`,
        },
        {
            target: "#tour-step-tour",
            header: {
                title: "Tour",
            },
            content: `<small>Thanks for completing the tour!! <br/> You can always comeback and start this tour by clicking on this tour button.</small>`,
        },*/
      ]
    };
  },
  mounted: function() {
  },
  methods: {}
};
function _sfc_ssrRender$a(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_v_tour = resolveComponent("v-tour");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_v_tour, {
    name: "appTour",
    steps: $data.steps
  }, null, _parent));
  _push(`</div>`);
}
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/App/Tour.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const AppTour = /* @__PURE__ */ _export_sfc(_sfc_main$a, [["ssrRender", _sfc_ssrRender$a]]);
const _sfc_main$9 = {
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
  props: ["dataset"],
  data() {
    return {
      open: false,
      versions: []
    };
  },
  updated() {
    this.fetchVersions();
  },
  methods: {
    toggleVersions() {
      this.open = !this.open;
    },
    fetchVersions() {
      axios.get(
        "/dashboard/datasets/" + this.dataset.id + "/nmriumVersions"
      ).then((response) => {
        this.versions = response.data;
      });
    }
  }
};
function _sfc_ssrRender$9(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
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
                    _push4(`<div class="h-full bg-white p-8 overflow-y-auto"${_scopeId3}><div class="pb-16 space-y-6"${_scopeId3}><div${_scopeId3}><div class="mt-0 flex items-start justify-between"${_scopeId3}><div${_scopeId3}><div class="mt-0 flex items-start justify-between"${_scopeId3}><div class="grid grid-cols-1 pt-1 text-left pt-0"${_scopeId3}><div class="text-xs italic text-gray-600 pr-5"${_scopeId3}><span class="text-gray-400"${_scopeId3}>Last updated on</span><br${_scopeId3}> ${ssrInterpolate(_ctx.formatDateTime(
                      $props.dataset.updated_at
                    ))}</div></div></div></div></div></div><div${_scopeId3}><div${_scopeId3}><h2 id="timeline-title" class="text-lg font-medium text-gray-900"${_scopeId3}> Edited by </h2><div class="mt-6 flow-root"${_scopeId3}><ul role="list" class="-mb-8"${_scopeId3}><!--[-->`);
                    ssrRenderList($data.versions, (version) => {
                      _push4(`<li${_scopeId3}><div class="relative pb-8"${_scopeId3}><span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"${_scopeId3}></span><div class="relative flex space-x-3"${_scopeId3}><div${_scopeId3}><img${ssrRenderAttr(
                        "src",
                        version.user.profile_photo_url
                      )}${ssrRenderAttr(
                        "alt",
                        version.user.name
                      )} class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"${_scopeId3}></div><div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4"${_scopeId3}><div${_scopeId3}><a class="font-medium text-gray-900"${_scopeId3}>${ssrInterpolate(version.user.name)}</a><br${_scopeId3}><small${_scopeId3}><time${_scopeId3}>${ssrInterpolate(_ctx.formatDateTime(
                        version.updated_at
                      ))}</time></small></div></div></div></div></li>`);
                    });
                    _push4(`<!--]--></ul></div></div></div></div>`);
                    if ($data.versions.length) {
                      _push4(`<small class="text-gray-500"${_scopeId3}><i${_scopeId3}>No logs before <br${_scopeId3}>${ssrInterpolate(_ctx.formatDateTime(
                        $data.versions[$data.versions.length - 1]["updated_at"]
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
                                createVNode("div", null, [
                                  createVNode("div", { class: "mt-0 flex items-start justify-between" }, [
                                    createVNode("div", { class: "grid grid-cols-1 pt-1 text-left pt-0" }, [
                                      createVNode("div", { class: "text-xs italic text-gray-600 pr-5" }, [
                                        createVNode("span", { class: "text-gray-400" }, "Last updated on"),
                                        createVNode("br"),
                                        createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                          $props.dataset.updated_at
                                        )), 1)
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ]),
                            createVNode("div", null, [
                              createVNode("div", null, [
                                createVNode("h2", {
                                  id: "timeline-title",
                                  class: "text-lg font-medium text-gray-900"
                                }, " Edited by "),
                                createVNode("div", { class: "mt-6 flow-root" }, [
                                  createVNode("ul", {
                                    role: "list",
                                    class: "-mb-8"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($data.versions, (version) => {
                                      return openBlock(), createBlock("li", {
                                        key: version.id
                                      }, [
                                        createVNode("div", { class: "relative pb-8" }, [
                                          createVNode("span", {
                                            class: "absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200",
                                            "aria-hidden": "true"
                                          }),
                                          createVNode("div", { class: "relative flex space-x-3" }, [
                                            createVNode("div", null, [
                                              createVNode("img", {
                                                src: version.user.profile_photo_url,
                                                alt: version.user.name,
                                                class: "h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"
                                              }, null, 8, ["src", "alt"])
                                            ]),
                                            createVNode("div", { class: "min-w-0 flex-1 pt-1.5 flex justify-between space-x-4" }, [
                                              createVNode("div", null, [
                                                createVNode("a", { class: "font-medium text-gray-900" }, toDisplayString(version.user.name), 1),
                                                createVNode("br"),
                                                createVNode("small", null, [
                                                  createVNode("time", null, toDisplayString(_ctx.formatDateTime(
                                                    version.updated_at
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
                          $data.versions.length ? (openBlock(), createBlock("small", {
                            key: 0,
                            class: "text-gray-500"
                          }, [
                            createVNode("i", null, [
                              createTextVNode("No logs before "),
                              createVNode("br"),
                              createTextVNode(toDisplayString(_ctx.formatDateTime(
                                $data.versions[$data.versions.length - 1]["updated_at"]
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
                                  createVNode("div", null, [
                                    createVNode("div", { class: "mt-0 flex items-start justify-between" }, [
                                      createVNode("div", { class: "grid grid-cols-1 pt-1 text-left pt-0" }, [
                                        createVNode("div", { class: "text-xs italic text-gray-600 pr-5" }, [
                                          createVNode("span", { class: "text-gray-400" }, "Last updated on"),
                                          createVNode("br"),
                                          createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                            $props.dataset.updated_at
                                          )), 1)
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ]),
                              createVNode("div", null, [
                                createVNode("div", null, [
                                  createVNode("h2", {
                                    id: "timeline-title",
                                    class: "text-lg font-medium text-gray-900"
                                  }, " Edited by "),
                                  createVNode("div", { class: "mt-6 flow-root" }, [
                                    createVNode("ul", {
                                      role: "list",
                                      class: "-mb-8"
                                    }, [
                                      (openBlock(true), createBlock(Fragment, null, renderList($data.versions, (version) => {
                                        return openBlock(), createBlock("li", {
                                          key: version.id
                                        }, [
                                          createVNode("div", { class: "relative pb-8" }, [
                                            createVNode("span", {
                                              class: "absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200",
                                              "aria-hidden": "true"
                                            }),
                                            createVNode("div", { class: "relative flex space-x-3" }, [
                                              createVNode("div", null, [
                                                createVNode("img", {
                                                  src: version.user.profile_photo_url,
                                                  alt: version.user.name,
                                                  class: "h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"
                                                }, null, 8, ["src", "alt"])
                                              ]),
                                              createVNode("div", { class: "min-w-0 flex-1 pt-1.5 flex justify-between space-x-4" }, [
                                                createVNode("div", null, [
                                                  createVNode("a", { class: "font-medium text-gray-900" }, toDisplayString(version.user.name), 1),
                                                  createVNode("br"),
                                                  createVNode("small", null, [
                                                    createVNode("time", null, toDisplayString(_ctx.formatDateTime(
                                                      version.updated_at
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
                            $data.versions.length ? (openBlock(), createBlock("small", {
                              key: 0,
                              class: "text-gray-500"
                            }, [
                              createVNode("i", null, [
                                createTextVNode("No logs before "),
                                createVNode("br"),
                                createTextVNode(toDisplayString(_ctx.formatDateTime(
                                  $data.versions[$data.versions.length - 1]["updated_at"]
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
                                createVNode("div", null, [
                                  createVNode("div", { class: "mt-0 flex items-start justify-between" }, [
                                    createVNode("div", { class: "grid grid-cols-1 pt-1 text-left pt-0" }, [
                                      createVNode("div", { class: "text-xs italic text-gray-600 pr-5" }, [
                                        createVNode("span", { class: "text-gray-400" }, "Last updated on"),
                                        createVNode("br"),
                                        createTextVNode(" " + toDisplayString(_ctx.formatDateTime(
                                          $props.dataset.updated_at
                                        )), 1)
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ]),
                            createVNode("div", null, [
                              createVNode("div", null, [
                                createVNode("h2", {
                                  id: "timeline-title",
                                  class: "text-lg font-medium text-gray-900"
                                }, " Edited by "),
                                createVNode("div", { class: "mt-6 flow-root" }, [
                                  createVNode("ul", {
                                    role: "list",
                                    class: "-mb-8"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($data.versions, (version) => {
                                      return openBlock(), createBlock("li", {
                                        key: version.id
                                      }, [
                                        createVNode("div", { class: "relative pb-8" }, [
                                          createVNode("span", {
                                            class: "absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200",
                                            "aria-hidden": "true"
                                          }),
                                          createVNode("div", { class: "relative flex space-x-3" }, [
                                            createVNode("div", null, [
                                              createVNode("img", {
                                                src: version.user.profile_photo_url,
                                                alt: version.user.name,
                                                class: "h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white object-cover"
                                              }, null, 8, ["src", "alt"])
                                            ]),
                                            createVNode("div", { class: "min-w-0 flex-1 pt-1.5 flex justify-between space-x-4" }, [
                                              createVNode("div", null, [
                                                createVNode("a", { class: "font-medium text-gray-900" }, toDisplayString(version.user.name), 1),
                                                createVNode("br"),
                                                createVNode("small", null, [
                                                  createVNode("time", null, toDisplayString(_ctx.formatDateTime(
                                                    version.updated_at
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
                          $data.versions.length ? (openBlock(), createBlock("small", {
                            key: 0,
                            class: "text-gray-500"
                          }, [
                            createVNode("i", null, [
                              createTextVNode("No logs before "),
                              createVNode("br"),
                              createTextVNode(toDisplayString(_ctx.formatDateTime(
                                $data.versions[$data.versions.length - 1]["updated_at"]
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
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Versions.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const Versions = /* @__PURE__ */ _export_sfc(_sfc_main$9, [["ssrRender", _sfc_ssrRender$9]]);
const _sfc_main$8 = {
  components: {
    Versions,
    ArrowPathIcon
  },
  props: {
    dataset: Object,
    project: Object,
    study: Object
  },
  emits: ["loading"],
  setup() {
    const versionsElement = ref(null);
    return {
      versionsElement
    };
  },
  data() {
    return {
      spectraError: null,
      selectedSpectraData: null,
      autoSaving: false,
      currentMolecules: [],
      reset: false,
      resetInProgress: false,
      info: null,
      version: null
    };
  },
  computed: {
    url() {
      return String(this.$page.props.url) ? String(this.$page.props.url) : "https://dev.nmrxiv.org";
    },
    nmriumURL() {
      return this.$page.props.nmriumURL ? String(this.$page.props.nmriumURL + "&id=" + Math.random()) : "https://nmriumdev.nmrxiv.org?defaultEmptyMessage=''&workspace=embedded&id=" + Math.random();
    },
    mailFromAddress() {
      return String(this.$page.props.mailFromAddress);
    }
  },
  watch: {
    dataset: {
      immediate: true,
      handler() {
        this.loadSpectra();
      }
    },
    study: {
      immediate: true,
      handler() {
        this.loadSpectra();
      }
    }
  },
  methods: {
    registerEvents() {
      const saveNMRiumUpdates = (e) => {
        const { data, type } = e.data;
        if (type == "nmr-wrapper:action-response") {
          if (e.origin != "https://nmriumdev.nmrxiv.org" && e.origin != "https://nmrium.nmrxiv.org") {
            return;
          }
          let actionType = data.type;
          if (actionType == "exportSpectraViewerAsBlob") {
            this.saveStudyPreview(data.data);
          }
        }
        if (data && data.source == "data") {
          if (e.origin != "https://nmriumdev.nmrxiv.org" && e.origin != "https://nmrium.nmrxiv.org") {
            return;
          }
          if (type == "nmr-wrapper:error") {
            this.spectraError = e.data;
            this.updateLoadingStatus(false);
            return;
          }
          let state = data.state;
          this.version = state.version;
          let actionType = state.data.actionType;
          if (type == "nmr-wrapper:data-change") {
            if (state.data.spectra.length > 0) {
              this.selectedSpectraData = state.data;
              if (actionType == "INITIATE") {
                if (!this.study.has_nmrium || this.resetInProgress) {
                  delete this.selectedSpectraData["actionType"];
                  this.selectedSpectraData.spectra.forEach(
                    (spec) => {
                      delete spec["data"];
                      delete spec["meta"];
                      delete spec["originalData"];
                      delete spec["originalInfo"];
                    }
                  );
                  if (this.study.study_photo_url == "") {
                    setTimeout(
                      (function() {
                        this.exportPreview();
                      }).bind(this),
                      500
                    );
                  }
                  this.resetInProgress = false;
                  this.updateStudyNMRiumInfo();
                  return;
                } else {
                  this.infoLog(
                    "Spectra loaded successfully...",
                    true
                  );
                  this.updateLoadingStatus(false);
                  return;
                }
              } else {
                this.updateLoadingStatus(
                  true,
                  "Saving updates: " + this.study.name
                );
                delete this.selectedSpectraData["actionType"];
                this.selectedSpectraData.spectra.forEach(
                  (spec) => {
                    delete spec["data"];
                    delete spec["meta"];
                    delete spec["originalData"];
                    delete spec["originalInfo"];
                  }
                );
                this.updateStudyNMRiumInfo();
                return;
              }
            }
          }
        }
      };
      if (!this.$props.eventRegistered) {
        window.addEventListener("message", saveNMRiumUpdates);
        this.$props.eventRegistered = true;
      } else {
        window.removeEventListener("message", saveNMRiumUpdates);
        this.$props.eventRegistered = false;
      }
    },
    loadSpectra() {
      this.infoLog("Loading Spectra..");
      const iframe = window.frames.submissionNMRiumIframe;
      this.spectraError = null;
      this.currentMolecules = [];
      let username = this.$page.props.team.owner ? this.$page.props.team.owner.username : this.project.owner.username;
      if (iframe) {
        this.updateLoadingStatus(
          true,
          "Loading spectra: " + this.study.name
        );
        if (this.study && this.study.has_nmrium) {
          this.infoLog("Loading Spectra from NMRium JSON..");
          axios.get(
            "/dashboard/studies/" + this.study.id + "/nmriumInfo"
          ).then((response) => {
            let nmrium_info = response.data;
            if (nmrium_info) {
              let data = {
                data: nmrium_info,
                type: "nmrium"
              };
              iframe.postMessage(
                { type: `nmr-wrapper:load`, data },
                "*"
              );
              this.updateMolecularData(
                nmrium_info.data.molecules
              );
            } else {
              this.loadFromURLs(null);
            }
          });
        } else {
          if (this.study.datasets.length > 0) {
            let urls = [];
            let url = null;
            if (this.study.download_url) {
              url = this.study.download_url;
            } else {
              url = this.url + "/" + username + "/datasets/" + this.project.slug + "/" + this.study.slug;
            }
            urls.push(url);
            this.loadFromURLs(urls);
          }
        }
      }
    },
    loadFromURLs(urls) {
      this.infoLog("Loading Spectra from URL..");
      const iframe = window.frames.submissionNMRiumIframe;
      if (!urls) {
        urls = [];
        let url = null;
        if (this.study.download_url) {
          url = this.study.download_url;
        } else {
          let username = this.$page.props.team.owner ? this.$page.props.team.owner.username : this.project.owner.username;
          url = this.url + "/" + username + "/datasets/" + this.project.slug + "/" + this.study.slug;
          urls.push(url);
        }
        urls.push(url);
        this.loadFromURLs(urls);
      }
      let data = {
        data: urls,
        type: "url"
      };
      iframe.postMessage({ type: `nmr-wrapper:load`, data }, "*");
    },
    updateStudyNMRiumInfo() {
      if (this.study != null && this.selectedSpectraData) {
        let _version = this.version ? this.version : 4;
        axios.post(
          "/dashboard/studies/" + this.study.id + "/nmriumInfo",
          {
            version: _version,
            data: this.selectedSpectraData
          }
        ).then((response) => {
          this.infoLog("Spectra saved successfully", true);
          this.updateLoadingStatus(false);
          this.autoSaving = false;
          this.study.has_nmrium = response.data.has_nmrium;
          this.updateMolecularData(
            this.selectedSpectraData.molecules
          );
        }).catch(() => {
          this.updateLoadingStatus(false);
          this.infoLog("Error saving spectra info");
          console.error(
            "Error saving the nmrium info. Please contact us at " + this.mailFromAddress + " if the error persist."
          );
          this.autoSaving = false;
        });
      }
    },
    updateMolecularData(molecules) {
      if (molecules.length > 0) {
        molecules.forEach((mol) => {
          axios.post(
            "https://api.naturalproducts.net/latest/chem/standardize",
            mol.molfile
          ).then((res) => {
            let _mol = res.data;
            axios.post(
              "/dashboard/studies/" + this.study.id + "/molecule",
              {
                InChI: _mol.inchi,
                InChIKey: _mol.inchikey,
                percentage: 0,
                mol: _mol.standardized_mol,
                canonical_smiles: _mol.canonical_smiles
              }
            ).then((res2) => {
              this.study.sample.molecules = res2.data;
            });
          });
        });
      }
    },
    updateDataSet() {
      if (this.dataset != null && this.selectedSpectraData.length > 0) {
        axios.post(
          "/dashboard/datasets/" + this.dataset.id + "/nmriumInfo",
          {
            spectra: this.selectedSpectraData,
            molecules: this.currentMolecules,
            version: this.version || 4
          }
        ).then((response) => {
          this.infoLog("Spectra saved successfully", true);
          this.autoSaving = false;
          this.dataset.has_nmrium = response.data.has_nmrium;
        }).catch((err) => {
          this.infoLog("Error saving spectra info");
          console.error(
            "Error saving the nmrium info. Please contact us at {{mailFromAddress}} if the error persist."
          );
          this.autoSaving = false;
        });
      }
    },
    loadMol(molFile) {
      let svgString = null;
      let mol = OCL.Molecule.fromMolfile(molFile);
      if (mol.toIsomericSmiles() != "") {
        svgString = mol.toSVG(300, 300);
      }
      return svgString;
    },
    exportPreview() {
      this.infoLog("Updating Preview");
      const iframe = window.frames.submissionNMRiumIframe;
      if (iframe) {
        let data = {
          type: "exportSpectraViewerAsBlob"
        };
        iframe.postMessage(
          {
            type: `nmr-wrapper:action-request`,
            data
          },
          "*"
        );
      }
    },
    saveStudyPreview(data) {
      if (this.study) {
        const reader = new FileReader();
        reader.addEventListener("loadend", () => {
          let svg = reader.result;
          axios.post(
            "/dashboard/studies/" + this.study.id + "/preview",
            {
              img: svg
            }
          ).then((response) => {
            this.infoLog("Saved preview successfully", true);
          });
        });
        reader.readAsText(data.blob);
      }
    },
    getSVGString(molecule) {
      if (molecule.MOL) {
        let mol = OCL.Molecule.fromMolfile(
          "\n  " + molecule.MOL.replaceAll('"', "")
        );
        return mol.toSVG(200, 200);
      }
    },
    updateLoadingStatus(status, message) {
      this.$emit("loading", {
        status,
        message
      });
    },
    infoLog(message, reset) {
      this.info = message;
      if (reset) {
        setTimeout(() => {
          this.info = "";
        }, 5e3);
      }
    },
    resetStudy() {
      this.resetInProgress = true;
      this.loadFromURLs(null);
    },
    showVersions() {
      this.versionsElement.toggleVersions();
    }
  }
};
function _sfc_ssrRender$8(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_ArrowPathIcon = resolveComponent("ArrowPathIcon");
  const _component_Versions = resolveComponent("Versions");
  _push(`<div${ssrRenderAttrs(_attrs)}><div><div class="mb-2 float-right"><small><a class="text-xs cursor-pointer hover:text-blue-700 mr-2" href="https://docs.nmrxiv.org/advanced-guides/nmrium/nmrium.html" target="_blank">Learn more </a><a class="cursor-pointer mr-3 border px-2 py-1 rounded-md">Reset</a><a class="cursor-pointer border px-2 py-1 rounded-md">`);
  _push(ssrRenderComponent(_component_ArrowPathIcon, { class: "w-4 h-4 inline" }, null, _parent));
  _push(` Preview</a></small></div><small class="text-gray-400">`);
  if ($data.info) {
    _push(`<span>${ssrInterpolate($data.info)}</span>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</small></div>`);
  if ($data.spectraError) {
    _push(`<div><div class="rounded-md bg-red-50 p-4"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"></path></svg></div><div class="ml-3"><h3 class="text-sm font-medium text-red-800"> There were errors loading spectra </h3><div class="mt-2 text-sm text-red-700"><ul role="list" class="list-disc space-y-1 pl-5"><li>${ssrInterpolate($data.spectraError)}</li></ul></div></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<iframe name="submissionNMRiumIframe" frameborder="0" allowfullscreen class="rounded-md border" style="${ssrRenderStyle({ "width": "100%", "height": "75vh", "max-height": "600px" })}"${ssrRenderAttr("src", $options.nmriumURL)}></iframe>`);
  _push(ssrRenderComponent(_component_Versions, {
    ref: "versionsElement",
    dataset: $props.dataset
  }, null, _parent));
  _push(`</div>`);
}
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SpectraEditor.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const SpectraEditor = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["ssrRender", _sfc_ssrRender$8]]);
const _sfc_main$7 = {
  props: ["status"],
  computed: {
    isRequired() {
      if (typeof this.status != "boolean") {
        return this.status.indexOf("|required") > -1;
      }
      return true;
    },
    valueExists() {
      if (typeof this.status == "boolean") {
        return this.status;
      }
      return this.status ? this.status.split("|")[0] == "true" : "false";
    }
  }
};
function _sfc_ssrRender$7(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  if ($options.valueExists) {
    _push(`<div class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-green-800"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>`);
  } else {
    _push(`<!---->`);
  }
  if (!$options.valueExists && !$options.isRequired) {
    _push(`<div class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-yellow-400"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path></svg></div>`);
  } else {
    _push(`<!---->`);
  }
  if (!$options.valueExists && $options.isRequired) {
    _push(`<div class="inline-flex py-2 mr-2 text-xs font-semibold leading-5 text-red-800"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/ValidationStatus.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const ValidationStatus = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["ssrRender", _sfc_ssrRender$7]]);
const _sfc_main$6 = {
  components: {
    ValidationStatus,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    ChevronUpIcon
  },
  props: {
    project: Object,
    validation: Object,
    mode: String,
    draft: Number
  },
  methods: {
    getStatus(value) {
      if (typeof value == "boolean") {
        return value;
      }
      return value ? value.split("|")[0] == "true" : "false";
    },
    update(model, property) {
      if (this.mode == "study") {
        this.$emit("update", {
          property,
          model
        });
      } else {
        window.location.replace(
          "/upload?draft_id=" + this.draft + "&step=2&sample=" + model.id
        );
      }
    }
  }
};
function _sfc_ssrRender$6(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_ValidationStatus = resolveComponent("ValidationStatus");
  const _component_Disclosure = resolveComponent("Disclosure");
  const _component_DisclosureButton = resolveComponent("DisclosureButton");
  const _component_ChevronUpIcon = resolveComponent("ChevronUpIcon");
  const _component_DisclosurePanel = resolveComponent("DisclosurePanel");
  _push(`<div${ssrRenderAttrs(_attrs)}><h1 class="text-md font-extrabold text-gray-400"> Here’s how your `);
  if ($props.mode != "study") {
    _push(`<span>project</span>`);
  } else {
    _push(`<span>samples meta-data</span>`);
  }
  _push(` compares to <a target="_blank" class="text-blue-800" href="https://www.nfdi4chem.de/index.php/reports-from-minimum-information-standards-workshops">recommended community standards</a>. </h1><div class="items-center text-sm mt-6 mb-3 text-gray-700 uppercase font-bold tracking-widest"> Check list <div class="float-right"><small><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline text-red-700 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Missing <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline text-yellow-700 mr-1 ml-3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path></svg>Optional <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline text-green-700 mr-1 ml-3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Complete</small></div></div><div class="border overflow-hidden bg-white shadow sm:rounded-md mb-12">`);
  if ($props.validation) {
    _push(`<ul role="list" class="divide-y border-t divide-gray-200">`);
    if ($props.mode != "study") {
      _push(`<li><a target="_blank" class="block"><div class="px-4 py-4 sm:px-6"><div class="flex items-center border-b">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.project.status
      }, null, _parent));
      _push(`<p class="truncate text-md font-bold text-indigo-600"> Project </p></div><div class="flex items-center mt-2 hover:bg-gray-100 rounded-md px-2">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.project.title
      }, null, _parent));
      _push(`<p class="truncate text-md font-bold text-dark-600"> Name </p>`);
      if (!$options.getStatus($props.validation.project.title)) {
        _push(`<a target="_blank"${ssrRenderAttr(
          "href",
          _ctx.route("publish", $props.project.draft_id) + "?edit=title#project-details"
        )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"> Edit </a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.project.description
      }, null, _parent));
      _push(`<p class="truncate text-md font-bold text-dark-600"> Description </p>`);
      if (!$options.getStatus(
        $props.validation.project.description
      )) {
        _push(`<a target="_blank"${ssrRenderAttr(
          "href",
          _ctx.route("publish", $props.project.draft_id) + "?edit=description#project-details"
        )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"> Edit </a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.project.keywords
      }, null, _parent));
      _push(`<p class="truncate text-md font-bold text-dark-600"> Keywords </p>`);
      if (!$options.getStatus($props.validation.project.keywords)) {
        _push(`<a target="_blank"${ssrRenderAttr(
          "href",
          _ctx.route("publish", $props.project.draft_id) + "?edit=keywords#project-details"
        )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"> Edit </a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.project.citations
      }, null, _parent));
      _push(`<p class="truncate text-md font-bold text-dark-600"> Citations </p>`);
      if (!$options.getStatus($props.validation.project.citations)) {
        _push(`<a target="_blank"${ssrRenderAttr(
          "href",
          _ctx.route("publish", $props.project.draft_id) + "?edit=citation#project-details"
        )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"> Edit </a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.project.authors
      }, null, _parent));
      _push(`<p class="truncate text-md font-bold text-dark-600"> Authors </p>`);
      if (!$options.getStatus($props.validation.project.authors)) {
        _push(`<a target="_blank"${ssrRenderAttr(
          "href",
          _ctx.route("publish", $props.project.draft_id) + "?edit=authors#project-details"
        )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"> Edit </a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.project.license
      }, null, _parent));
      _push(`<p class="truncate text-md font-bold text-dark-600"> License </p>`);
      if (!$options.getStatus($props.validation.project.license)) {
        _push(`<a target="_blank"${ssrRenderAttr(
          "href",
          _ctx.route("publish", $props.project.draft_id) + "?edit=license#publish-details"
        )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"> Edit </a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.project.image
      }, null, _parent));
      _push(`<p class="truncate text-md font-bold text-dark-600"> Project profile image </p>`);
      if (!$options.getStatus($props.validation.project.image)) {
        _push(`<a target="_blank"${ssrRenderAttr(
          "href",
          _ctx.route("publish", $props.project.draft_id) + "?edit=profile_image#publish-details"
        )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"> Edit </a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></a></li>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<li><a target="_blank" class="block"><div class="px-4 py-4 sm:px-6"><div class="flex items-center hover:bg-gray-100 rounded-md px-2"><p class="truncate text-md font-bold text-indigo-600"> Samples </p></div><!--[-->`);
    ssrRenderList($props.validation.project.studies, (study) => {
      _push(`<div><div><div class="mx-auto w-full py-2">`);
      _push(ssrRenderComponent(_component_Disclosure, null, {
        default: withCtx(({ open: open2 }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_DisclosureButton, {
              class: [
                study.status ? "bg-gray-100 hover:bg-gray-200" : "bg-red-100 hover:bg-red-200",
                "flex w-full rounded-md px-4 py-2 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75"
              ]
            }, {
              default: withCtx((_, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(_component_ValidationStatus, {
                    class: "-my-2",
                    status: study.status
                  }, null, _parent3, _scopeId2));
                  _push3(`<span${_scopeId2}>${ssrInterpolate(study.name)}</span>`);
                  _push3(ssrRenderComponent(_component_ChevronUpIcon, {
                    class: [
                      !open2 ? "rotate-180 transform" : "",
                      "h-5 w-5 text-gray-500"
                    ]
                  }, null, _parent3, _scopeId2));
                } else {
                  return [
                    createVNode(_component_ValidationStatus, {
                      class: "-my-2",
                      status: study.status
                    }, null, 8, ["status"]),
                    createVNode("span", null, toDisplayString(study.name), 1),
                    createVNode(_component_ChevronUpIcon, {
                      class: [
                        !open2 ? "rotate-180 transform" : "",
                        "h-5 w-5 text-gray-500"
                      ]
                    }, null, 8, ["class"])
                  ];
                }
              }),
              _: 2
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_DisclosurePanel, { class: "px-4 pt-4 pb-2 text-sm text-gray-500" }, {
              default: withCtx((_, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId2}>`);
                  _push3(ssrRenderComponent(_component_ValidationStatus, {
                    status: study.title
                  }, null, _parent3, _scopeId2));
                  _push3(`<p class="truncate text-md text-dark-600"${_scopeId2}> Sample title </p></div><div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId2}>`);
                  _push3(ssrRenderComponent(_component_ValidationStatus, {
                    status: study.description
                  }, null, _parent3, _scopeId2));
                  _push3(`<p class="truncate text-md text-dark-600"${_scopeId2}> Sample description </p></div><div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId2}>`);
                  _push3(ssrRenderComponent(_component_ValidationStatus, {
                    status: study.keywords
                  }, null, _parent3, _scopeId2));
                  _push3(`<p class="truncate text-md text-dark-600"${_scopeId2}> Sample keywords </p>`);
                  if (!$options.getStatus(
                    study.keywords
                  )) {
                    _push3(`<a class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"${_scopeId2}> Edit </a>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId2}>`);
                  _push3(ssrRenderComponent(_component_ValidationStatus, {
                    status: study.sample
                  }, null, _parent3, _scopeId2));
                  _push3(`<p class="truncate text-md text-dark-600"${_scopeId2}> Sample meta data </p>`);
                  if (!$options.getStatus(
                    study.sample
                  )) {
                    _push3(`<span class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"${_scopeId2}> Edit </span>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId2}>`);
                  _push3(ssrRenderComponent(_component_ValidationStatus, {
                    status: study.nmrium_info
                  }, null, _parent3, _scopeId2));
                  _push3(`<p class="truncate text-md text-dark-600"${_scopeId2}> Spectra `);
                  if (study.nmrium_info.split(
                    "|"
                  )[0] !== "true") {
                    _push3(`<small${_scopeId2}><br${_scopeId2}> Please load and confirm the spectra uploaded. </small>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`</p>`);
                  if (!$options.getStatus(
                    study.nmrium_info
                  )) {
                    _push3(`<a target="_blank"${ssrRenderAttr(
                      "href",
                      _ctx.route(
                        "dashboard.study.datasets",
                        study.id
                      )
                    )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"${_scopeId2}> Edit </a>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId2}>`);
                  _push3(ssrRenderComponent(_component_ValidationStatus, {
                    status: study.molecules
                  }, null, _parent3, _scopeId2));
                  _push3(`<p class="truncate text-md text-dark-600"${_scopeId2}> Compound Information </p>`);
                  if (!$options.getStatus(
                    study.molecules
                  )) {
                    _push3(`<a class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"${_scopeId2}> Edit </a>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`</div><div class="flex items-center mt-2"${_scopeId2}><p class="truncate text-md text-dark-600 mb-3"${_scopeId2}> Spectral Datasets </p></div><!--[-->`);
                  ssrRenderList(study.datasets, (dataset) => {
                    _push3(`<div class="mb-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_component_Disclosure, null, {
                      default: withCtx(({ open: open3 }, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(ssrRenderComponent(_component_DisclosureButton, {
                            class: [
                              dataset.status ? "bg-gray-100 hover:bg-gray-200" : "bg-red-100 hover:bg-red-200",
                              "flex w-full rounded-md px-4 py-2 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75"
                            ]
                          }, {
                            default: withCtx((_2, _push5, _parent5, _scopeId4) => {
                              if (_push5) {
                                _push5(ssrRenderComponent(_component_ValidationStatus, {
                                  class: "-my-2",
                                  status: dataset.status
                                }, null, _parent5, _scopeId4));
                                _push5(`<span${_scopeId4}>${ssrInterpolate(dataset.name)}</span>`);
                                _push5(ssrRenderComponent(_component_ChevronUpIcon, {
                                  class: [
                                    !open3 ? "rotate-180 transform" : "",
                                    "h-5 w-5 text-gray-500"
                                  ]
                                }, null, _parent5, _scopeId4));
                              } else {
                                return [
                                  createVNode(_component_ValidationStatus, {
                                    class: "-my-2",
                                    status: dataset.status
                                  }, null, 8, ["status"]),
                                  createVNode("span", null, toDisplayString(dataset.name), 1),
                                  createVNode(_component_ChevronUpIcon, {
                                    class: [
                                      !open3 ? "rotate-180 transform" : "",
                                      "h-5 w-5 text-gray-500"
                                    ]
                                  }, null, 8, ["class"])
                                ];
                              }
                            }),
                            _: 2
                          }, _parent4, _scopeId3));
                          _push4(ssrRenderComponent(_component_DisclosurePanel, { class: "px-4 pt-4 pb-2 text-sm text-gray-500" }, {
                            default: withCtx((_2, _push5, _parent5, _scopeId4) => {
                              if (_push5) {
                                _push5(`<div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId4}>`);
                                _push5(ssrRenderComponent(_component_ValidationStatus, {
                                  status: dataset.files
                                }, null, _parent5, _scopeId4));
                                _push5(`<p class="truncate text-md text-dark-600"${_scopeId4}> Files </p>`);
                                if (!$options.getStatus(
                                  dataset.files
                                )) {
                                  _push5(`<span class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"${_scopeId4}> Edit </span>`);
                                } else {
                                  _push5(`<!---->`);
                                }
                                _push5(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId4}>`);
                                _push5(ssrRenderComponent(_component_ValidationStatus, {
                                  status: dataset.nmrium_info
                                }, null, _parent5, _scopeId4));
                                _push5(`<p class="truncate text-md text-dark-600"${_scopeId4}> Spectra `);
                                if (dataset.nmrium_info.split(
                                  "|"
                                )[0] !== "true") {
                                  _push5(`<small${_scopeId4}><br${_scopeId4}> Please load and confirm the spectra uploaded. </small>`);
                                } else {
                                  _push5(`<!---->`);
                                }
                                _push5(`</p>`);
                                if (!$options.getStatus(
                                  dataset.nmrium_info
                                )) {
                                  _push5(`<a target="_blank"${ssrRenderAttr(
                                    "href",
                                    _ctx.route(
                                      "dashboard.study.datasets",
                                      study.id
                                    ) + "?dsid=" + dataset.id
                                  )} class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"${_scopeId4}> Edit </a>`);
                                } else {
                                  _push5(`<!---->`);
                                }
                                _push5(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId4}>`);
                                _push5(ssrRenderComponent(_component_ValidationStatus, {
                                  status: dataset.assay
                                }, null, _parent5, _scopeId4));
                                _push5(`<p class="truncate text-md text-dark-600"${_scopeId4}> Assay (Meta data) </p>`);
                                if (!$options.getStatus(
                                  dataset.assay
                                )) {
                                  _push5(`<span class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"${_scopeId4}> Edit </span>`);
                                } else {
                                  _push5(`<!---->`);
                                }
                                _push5(`</div><div class="flex items-center hover:bg-gray-100 rounded-md px-2"${_scopeId4}>`);
                                _push5(ssrRenderComponent(_component_ValidationStatus, {
                                  status: dataset.assignments
                                }, null, _parent5, _scopeId4));
                                _push5(`<p class="truncate text-md text-dark-600"${_scopeId4}> Assignments </p>`);
                                if (!$options.getStatus(
                                  dataset.assignments
                                )) {
                                  _push5(`<a class="cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"${_scopeId4}> Edit </a>`);
                                } else {
                                  _push5(`<!---->`);
                                }
                                _push5(`</div>`);
                              } else {
                                return [
                                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                    createVNode(_component_ValidationStatus, {
                                      status: dataset.files
                                    }, null, 8, ["status"]),
                                    createVNode("p", { class: "truncate text-md text-dark-600" }, " Files "),
                                    !$options.getStatus(
                                      dataset.files
                                    ) ? (openBlock(), createBlock("span", {
                                      key: 0,
                                      class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                    }, " Edit ")) : createCommentVNode("", true)
                                  ]),
                                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                    createVNode(_component_ValidationStatus, {
                                      status: dataset.nmrium_info
                                    }, null, 8, ["status"]),
                                    createVNode("p", { class: "truncate text-md text-dark-600" }, [
                                      createTextVNode(" Spectra "),
                                      dataset.nmrium_info.split(
                                        "|"
                                      )[0] !== "true" ? (openBlock(), createBlock("small", { key: 0 }, [
                                        createVNode("br"),
                                        createTextVNode(" Please load and confirm the spectra uploaded. ")
                                      ])) : createCommentVNode("", true)
                                    ]),
                                    !$options.getStatus(
                                      dataset.nmrium_info
                                    ) ? (openBlock(), createBlock("a", {
                                      key: 0,
                                      target: "_blank",
                                      href: _ctx.route(
                                        "dashboard.study.datasets",
                                        study.id
                                      ) + "?dsid=" + dataset.id,
                                      class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                    }, " Edit ", 8, ["href"])) : createCommentVNode("", true)
                                  ]),
                                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                    createVNode(_component_ValidationStatus, {
                                      status: dataset.assay
                                    }, null, 8, ["status"]),
                                    createVNode("p", { class: "truncate text-md text-dark-600" }, " Assay (Meta data) "),
                                    !$options.getStatus(
                                      dataset.assay
                                    ) ? (openBlock(), createBlock("span", {
                                      key: 0,
                                      class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                    }, " Edit ")) : createCommentVNode("", true)
                                  ]),
                                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                    createVNode(_component_ValidationStatus, {
                                      status: dataset.assignments
                                    }, null, 8, ["status"]),
                                    createVNode("p", { class: "truncate text-md text-dark-600" }, " Assignments "),
                                    !$options.getStatus(
                                      dataset.assignments
                                    ) ? (openBlock(), createBlock("a", {
                                      key: 0,
                                      class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full",
                                      onClick: ($event) => $options.update(
                                        study,
                                        "structure"
                                      )
                                    }, " Edit ", 8, ["onClick"])) : createCommentVNode("", true)
                                  ])
                                ];
                              }
                            }),
                            _: 2
                          }, _parent4, _scopeId3));
                        } else {
                          return [
                            createVNode(_component_DisclosureButton, {
                              class: [
                                dataset.status ? "bg-gray-100 hover:bg-gray-200" : "bg-red-100 hover:bg-red-200",
                                "flex w-full rounded-md px-4 py-2 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75"
                              ]
                            }, {
                              default: withCtx(() => [
                                createVNode(_component_ValidationStatus, {
                                  class: "-my-2",
                                  status: dataset.status
                                }, null, 8, ["status"]),
                                createVNode("span", null, toDisplayString(dataset.name), 1),
                                createVNode(_component_ChevronUpIcon, {
                                  class: [
                                    !open3 ? "rotate-180 transform" : "",
                                    "h-5 w-5 text-gray-500"
                                  ]
                                }, null, 8, ["class"])
                              ]),
                              _: 2
                            }, 1032, ["class"]),
                            createVNode(_component_DisclosurePanel, { class: "px-4 pt-4 pb-2 text-sm text-gray-500" }, {
                              default: withCtx(() => [
                                createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                  createVNode(_component_ValidationStatus, {
                                    status: dataset.files
                                  }, null, 8, ["status"]),
                                  createVNode("p", { class: "truncate text-md text-dark-600" }, " Files "),
                                  !$options.getStatus(
                                    dataset.files
                                  ) ? (openBlock(), createBlock("span", {
                                    key: 0,
                                    class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                  }, " Edit ")) : createCommentVNode("", true)
                                ]),
                                createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                  createVNode(_component_ValidationStatus, {
                                    status: dataset.nmrium_info
                                  }, null, 8, ["status"]),
                                  createVNode("p", { class: "truncate text-md text-dark-600" }, [
                                    createTextVNode(" Spectra "),
                                    dataset.nmrium_info.split(
                                      "|"
                                    )[0] !== "true" ? (openBlock(), createBlock("small", { key: 0 }, [
                                      createVNode("br"),
                                      createTextVNode(" Please load and confirm the spectra uploaded. ")
                                    ])) : createCommentVNode("", true)
                                  ]),
                                  !$options.getStatus(
                                    dataset.nmrium_info
                                  ) ? (openBlock(), createBlock("a", {
                                    key: 0,
                                    target: "_blank",
                                    href: _ctx.route(
                                      "dashboard.study.datasets",
                                      study.id
                                    ) + "?dsid=" + dataset.id,
                                    class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                  }, " Edit ", 8, ["href"])) : createCommentVNode("", true)
                                ]),
                                createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                  createVNode(_component_ValidationStatus, {
                                    status: dataset.assay
                                  }, null, 8, ["status"]),
                                  createVNode("p", { class: "truncate text-md text-dark-600" }, " Assay (Meta data) "),
                                  !$options.getStatus(
                                    dataset.assay
                                  ) ? (openBlock(), createBlock("span", {
                                    key: 0,
                                    class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                  }, " Edit ")) : createCommentVNode("", true)
                                ]),
                                createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                  createVNode(_component_ValidationStatus, {
                                    status: dataset.assignments
                                  }, null, 8, ["status"]),
                                  createVNode("p", { class: "truncate text-md text-dark-600" }, " Assignments "),
                                  !$options.getStatus(
                                    dataset.assignments
                                  ) ? (openBlock(), createBlock("a", {
                                    key: 0,
                                    class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full",
                                    onClick: ($event) => $options.update(
                                      study,
                                      "structure"
                                    )
                                  }, " Edit ", 8, ["onClick"])) : createCommentVNode("", true)
                                ])
                              ]),
                              _: 2
                            }, 1024)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent3, _scopeId2));
                    _push3(`</div>`);
                  });
                  _push3(`<!--]-->`);
                } else {
                  return [
                    createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                      createVNode(_component_ValidationStatus, {
                        status: study.title
                      }, null, 8, ["status"]),
                      createVNode("p", { class: "truncate text-md text-dark-600" }, " Sample title ")
                    ]),
                    createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                      createVNode(_component_ValidationStatus, {
                        status: study.description
                      }, null, 8, ["status"]),
                      createVNode("p", { class: "truncate text-md text-dark-600" }, " Sample description ")
                    ]),
                    createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                      createVNode(_component_ValidationStatus, {
                        status: study.keywords
                      }, null, 8, ["status"]),
                      createVNode("p", { class: "truncate text-md text-dark-600" }, " Sample keywords "),
                      !$options.getStatus(
                        study.keywords
                      ) ? (openBlock(), createBlock("a", {
                        key: 0,
                        class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full",
                        onClick: ($event) => $options.update(
                          study,
                          "keywords"
                        )
                      }, " Edit ", 8, ["onClick"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                      createVNode(_component_ValidationStatus, {
                        status: study.sample
                      }, null, 8, ["status"]),
                      createVNode("p", { class: "truncate text-md text-dark-600" }, " Sample meta data "),
                      !$options.getStatus(
                        study.sample
                      ) ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                      }, " Edit ")) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                      createVNode(_component_ValidationStatus, {
                        status: study.nmrium_info
                      }, null, 8, ["status"]),
                      createVNode("p", { class: "truncate text-md text-dark-600" }, [
                        createTextVNode(" Spectra "),
                        study.nmrium_info.split(
                          "|"
                        )[0] !== "true" ? (openBlock(), createBlock("small", { key: 0 }, [
                          createVNode("br"),
                          createTextVNode(" Please load and confirm the spectra uploaded. ")
                        ])) : createCommentVNode("", true)
                      ]),
                      !$options.getStatus(
                        study.nmrium_info
                      ) ? (openBlock(), createBlock("a", {
                        key: 0,
                        target: "_blank",
                        href: _ctx.route(
                          "dashboard.study.datasets",
                          study.id
                        ),
                        class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                      }, " Edit ", 8, ["href"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                      createVNode(_component_ValidationStatus, {
                        status: study.molecules
                      }, null, 8, ["status"]),
                      createVNode("p", { class: "truncate text-md text-dark-600" }, " Compound Information "),
                      !$options.getStatus(
                        study.molecules
                      ) ? (openBlock(), createBlock("a", {
                        key: 0,
                        class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full",
                        onClick: ($event) => $options.update(
                          study,
                          "structure"
                        )
                      }, " Edit ", 8, ["onClick"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex items-center mt-2" }, [
                      createVNode("p", { class: "truncate text-md text-dark-600 mb-3" }, " Spectral Datasets ")
                    ]),
                    (openBlock(true), createBlock(Fragment, null, renderList(study.datasets, (dataset) => {
                      return openBlock(), createBlock("div", {
                        key: dataset.id,
                        class: "mb-2"
                      }, [
                        createVNode(_component_Disclosure, null, {
                          default: withCtx(({ open: open3 }) => [
                            createVNode(_component_DisclosureButton, {
                              class: [
                                dataset.status ? "bg-gray-100 hover:bg-gray-200" : "bg-red-100 hover:bg-red-200",
                                "flex w-full rounded-md px-4 py-2 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75"
                              ]
                            }, {
                              default: withCtx(() => [
                                createVNode(_component_ValidationStatus, {
                                  class: "-my-2",
                                  status: dataset.status
                                }, null, 8, ["status"]),
                                createVNode("span", null, toDisplayString(dataset.name), 1),
                                createVNode(_component_ChevronUpIcon, {
                                  class: [
                                    !open3 ? "rotate-180 transform" : "",
                                    "h-5 w-5 text-gray-500"
                                  ]
                                }, null, 8, ["class"])
                              ]),
                              _: 2
                            }, 1032, ["class"]),
                            createVNode(_component_DisclosurePanel, { class: "px-4 pt-4 pb-2 text-sm text-gray-500" }, {
                              default: withCtx(() => [
                                createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                  createVNode(_component_ValidationStatus, {
                                    status: dataset.files
                                  }, null, 8, ["status"]),
                                  createVNode("p", { class: "truncate text-md text-dark-600" }, " Files "),
                                  !$options.getStatus(
                                    dataset.files
                                  ) ? (openBlock(), createBlock("span", {
                                    key: 0,
                                    class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                  }, " Edit ")) : createCommentVNode("", true)
                                ]),
                                createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                  createVNode(_component_ValidationStatus, {
                                    status: dataset.nmrium_info
                                  }, null, 8, ["status"]),
                                  createVNode("p", { class: "truncate text-md text-dark-600" }, [
                                    createTextVNode(" Spectra "),
                                    dataset.nmrium_info.split(
                                      "|"
                                    )[0] !== "true" ? (openBlock(), createBlock("small", { key: 0 }, [
                                      createVNode("br"),
                                      createTextVNode(" Please load and confirm the spectra uploaded. ")
                                    ])) : createCommentVNode("", true)
                                  ]),
                                  !$options.getStatus(
                                    dataset.nmrium_info
                                  ) ? (openBlock(), createBlock("a", {
                                    key: 0,
                                    target: "_blank",
                                    href: _ctx.route(
                                      "dashboard.study.datasets",
                                      study.id
                                    ) + "?dsid=" + dataset.id,
                                    class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                  }, " Edit ", 8, ["href"])) : createCommentVNode("", true)
                                ]),
                                createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                  createVNode(_component_ValidationStatus, {
                                    status: dataset.assay
                                  }, null, 8, ["status"]),
                                  createVNode("p", { class: "truncate text-md text-dark-600" }, " Assay (Meta data) "),
                                  !$options.getStatus(
                                    dataset.assay
                                  ) ? (openBlock(), createBlock("span", {
                                    key: 0,
                                    class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                  }, " Edit ")) : createCommentVNode("", true)
                                ]),
                                createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                  createVNode(_component_ValidationStatus, {
                                    status: dataset.assignments
                                  }, null, 8, ["status"]),
                                  createVNode("p", { class: "truncate text-md text-dark-600" }, " Assignments "),
                                  !$options.getStatus(
                                    dataset.assignments
                                  ) ? (openBlock(), createBlock("a", {
                                    key: 0,
                                    class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full",
                                    onClick: ($event) => $options.update(
                                      study,
                                      "structure"
                                    )
                                  }, " Edit ", 8, ["onClick"])) : createCommentVNode("", true)
                                ])
                              ]),
                              _: 2
                            }, 1024)
                          ]),
                          _: 2
                        }, 1024)
                      ]);
                    }), 128))
                  ];
                }
              }),
              _: 2
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_DisclosureButton, {
                class: [
                  study.status ? "bg-gray-100 hover:bg-gray-200" : "bg-red-100 hover:bg-red-200",
                  "flex w-full rounded-md px-4 py-2 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75"
                ]
              }, {
                default: withCtx(() => [
                  createVNode(_component_ValidationStatus, {
                    class: "-my-2",
                    status: study.status
                  }, null, 8, ["status"]),
                  createVNode("span", null, toDisplayString(study.name), 1),
                  createVNode(_component_ChevronUpIcon, {
                    class: [
                      !open2 ? "rotate-180 transform" : "",
                      "h-5 w-5 text-gray-500"
                    ]
                  }, null, 8, ["class"])
                ]),
                _: 2
              }, 1032, ["class"]),
              createVNode(_component_DisclosurePanel, { class: "px-4 pt-4 pb-2 text-sm text-gray-500" }, {
                default: withCtx(() => [
                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                    createVNode(_component_ValidationStatus, {
                      status: study.title
                    }, null, 8, ["status"]),
                    createVNode("p", { class: "truncate text-md text-dark-600" }, " Sample title ")
                  ]),
                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                    createVNode(_component_ValidationStatus, {
                      status: study.description
                    }, null, 8, ["status"]),
                    createVNode("p", { class: "truncate text-md text-dark-600" }, " Sample description ")
                  ]),
                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                    createVNode(_component_ValidationStatus, {
                      status: study.keywords
                    }, null, 8, ["status"]),
                    createVNode("p", { class: "truncate text-md text-dark-600" }, " Sample keywords "),
                    !$options.getStatus(
                      study.keywords
                    ) ? (openBlock(), createBlock("a", {
                      key: 0,
                      class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full",
                      onClick: ($event) => $options.update(
                        study,
                        "keywords"
                      )
                    }, " Edit ", 8, ["onClick"])) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                    createVNode(_component_ValidationStatus, {
                      status: study.sample
                    }, null, 8, ["status"]),
                    createVNode("p", { class: "truncate text-md text-dark-600" }, " Sample meta data "),
                    !$options.getStatus(
                      study.sample
                    ) ? (openBlock(), createBlock("span", {
                      key: 0,
                      class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                    }, " Edit ")) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                    createVNode(_component_ValidationStatus, {
                      status: study.nmrium_info
                    }, null, 8, ["status"]),
                    createVNode("p", { class: "truncate text-md text-dark-600" }, [
                      createTextVNode(" Spectra "),
                      study.nmrium_info.split(
                        "|"
                      )[0] !== "true" ? (openBlock(), createBlock("small", { key: 0 }, [
                        createVNode("br"),
                        createTextVNode(" Please load and confirm the spectra uploaded. ")
                      ])) : createCommentVNode("", true)
                    ]),
                    !$options.getStatus(
                      study.nmrium_info
                    ) ? (openBlock(), createBlock("a", {
                      key: 0,
                      target: "_blank",
                      href: _ctx.route(
                        "dashboard.study.datasets",
                        study.id
                      ),
                      class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                    }, " Edit ", 8, ["href"])) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                    createVNode(_component_ValidationStatus, {
                      status: study.molecules
                    }, null, 8, ["status"]),
                    createVNode("p", { class: "truncate text-md text-dark-600" }, " Compound Information "),
                    !$options.getStatus(
                      study.molecules
                    ) ? (openBlock(), createBlock("a", {
                      key: 0,
                      class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full",
                      onClick: ($event) => $options.update(
                        study,
                        "structure"
                      )
                    }, " Edit ", 8, ["onClick"])) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "flex items-center mt-2" }, [
                    createVNode("p", { class: "truncate text-md text-dark-600 mb-3" }, " Spectral Datasets ")
                  ]),
                  (openBlock(true), createBlock(Fragment, null, renderList(study.datasets, (dataset) => {
                    return openBlock(), createBlock("div", {
                      key: dataset.id,
                      class: "mb-2"
                    }, [
                      createVNode(_component_Disclosure, null, {
                        default: withCtx(({ open: open3 }) => [
                          createVNode(_component_DisclosureButton, {
                            class: [
                              dataset.status ? "bg-gray-100 hover:bg-gray-200" : "bg-red-100 hover:bg-red-200",
                              "flex w-full rounded-md px-4 py-2 text-left text-sm font-medium text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75"
                            ]
                          }, {
                            default: withCtx(() => [
                              createVNode(_component_ValidationStatus, {
                                class: "-my-2",
                                status: dataset.status
                              }, null, 8, ["status"]),
                              createVNode("span", null, toDisplayString(dataset.name), 1),
                              createVNode(_component_ChevronUpIcon, {
                                class: [
                                  !open3 ? "rotate-180 transform" : "",
                                  "h-5 w-5 text-gray-500"
                                ]
                              }, null, 8, ["class"])
                            ]),
                            _: 2
                          }, 1032, ["class"]),
                          createVNode(_component_DisclosurePanel, { class: "px-4 pt-4 pb-2 text-sm text-gray-500" }, {
                            default: withCtx(() => [
                              createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                createVNode(_component_ValidationStatus, {
                                  status: dataset.files
                                }, null, 8, ["status"]),
                                createVNode("p", { class: "truncate text-md text-dark-600" }, " Files "),
                                !$options.getStatus(
                                  dataset.files
                                ) ? (openBlock(), createBlock("span", {
                                  key: 0,
                                  class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                }, " Edit ")) : createCommentVNode("", true)
                              ]),
                              createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                createVNode(_component_ValidationStatus, {
                                  status: dataset.nmrium_info
                                }, null, 8, ["status"]),
                                createVNode("p", { class: "truncate text-md text-dark-600" }, [
                                  createTextVNode(" Spectra "),
                                  dataset.nmrium_info.split(
                                    "|"
                                  )[0] !== "true" ? (openBlock(), createBlock("small", { key: 0 }, [
                                    createVNode("br"),
                                    createTextVNode(" Please load and confirm the spectra uploaded. ")
                                  ])) : createCommentVNode("", true)
                                ]),
                                !$options.getStatus(
                                  dataset.nmrium_info
                                ) ? (openBlock(), createBlock("a", {
                                  key: 0,
                                  target: "_blank",
                                  href: _ctx.route(
                                    "dashboard.study.datasets",
                                    study.id
                                  ) + "?dsid=" + dataset.id,
                                  class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                }, " Edit ", 8, ["href"])) : createCommentVNode("", true)
                              ]),
                              createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                createVNode(_component_ValidationStatus, {
                                  status: dataset.assay
                                }, null, 8, ["status"]),
                                createVNode("p", { class: "truncate text-md text-dark-600" }, " Assay (Meta data) "),
                                !$options.getStatus(
                                  dataset.assay
                                ) ? (openBlock(), createBlock("span", {
                                  key: 0,
                                  class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full"
                                }, " Edit ")) : createCommentVNode("", true)
                              ]),
                              createVNode("div", { class: "flex items-center hover:bg-gray-100 rounded-md px-2" }, [
                                createVNode(_component_ValidationStatus, {
                                  status: dataset.assignments
                                }, null, 8, ["status"]),
                                createVNode("p", { class: "truncate text-md text-dark-600" }, " Assignments "),
                                !$options.getStatus(
                                  dataset.assignments
                                ) ? (openBlock(), createBlock("a", {
                                  key: 0,
                                  class: "cursor-pointer bg-red-800 text-white ml-auto inline-block py-0.5 px-3 text-xs rounded-full",
                                  onClick: ($event) => $options.update(
                                    study,
                                    "structure"
                                  )
                                }, " Edit ", 8, ["onClick"])) : createCommentVNode("", true)
                              ])
                            ]),
                            _: 2
                          }, 1024)
                        ]),
                        _: 2
                      }, 1024)
                    ]);
                  }), 128))
                ]),
                _: 2
              }, 1024)
            ];
          }
        }),
        _: 2
      }, _parent));
      _push(`</div></div></div>`);
    });
    _push(`<!--]--></div></a></li>`);
    if ($props.validation.errors && $props.validation.errors.length > 0) {
      _push(`<li><a target="_blank" class="block hover:bg-gray-50"><div class="px-4 py-4 sm:px-6"><div class="flex items-center hover:bg-gray-100 rounded-md px-2"><p class="truncate text-md font-bold text-red-600"> Errors </p><div class="ml-2 flex flex-shrink-0">`);
      _push(ssrRenderComponent(_component_ValidationStatus, {
        status: $props.validation.errors.length == 0
      }, null, _parent));
      _push(`</div></div></div></a></li>`);
    } else {
      _push(`<!---->`);
    }
    if ($props.validation.missing && $props.validation.missing.length > 0) {
      _push(`<li><a target="_blank" class="block hover:bg-gray-50"><div class="px-4 py-4 sm:px-6"><div class="flex items-center hover:bg-gray-100 rounded-md px-2"><p class="truncate text-md font-bold text-yellow-600"> Warnings </p></div></div></a></li>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</ul>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div>`);
}
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Validation.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const Validation = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["ssrRender", _sfc_ssrRender$6]]);
const _sfc_main$5 = {
  components: {
    PaperClipIcon
  },
  props: ["file", "project", "study"],
  setup() {
    return {};
  },
  data() {
    return {};
  },
  computed: {
    downloadURL() {
      if (this.study || this.project) {
        let project = null;
        if (this.study && !this.project) {
          project = this.study.project;
        } else {
          project = this.project;
        }
        if (project) {
          return this.url + "/" + project.owner.username + "/download/" + project.slug + "?key=" + this.file.key + "&uuid=" + this.file.uuid;
        }
      }
    },
    url() {
      return String(this.$page.props.url);
    },
    fileInfo() {
      return JSON.parse(this.file.info);
    }
  }
};
function _sfc_ssrRender$5(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white shadow overflow-hidden sm:rounded-lg" }, _attrs))}><div class="px-4 py-5 sm:px-6"><h3 class="text-lg leading-6 text-xl font-bold text-gray-900">`);
  if ($props.file.status == "missing") {
    _push(`<span class="float-left inline pr-4 pt-1 text-sm font-medium pointer-events-none"><span class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path></svg></span></span>`);
  } else {
    _push(`<!---->`);
  }
  _push(`${ssrInterpolate($props.file.name)}</h3><p class="mt-1 max-w-2xl text-sm text-gray-500">File information</p></div><div class="border-t border-gray-200 px-4 py-5 sm:p-0"><dl class="sm:divide-y sm:divide-gray-200"><div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"><dt class="text-sm font-medium text-gray-500"> Uploaded at </dt><dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">${ssrInterpolate(_ctx.formatDateTime($props.file.created_at))}</dd><dt class="text-sm font-medium text-gray-500">File size</dt><dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">${ssrInterpolate(_ctx.bytesToSize($options.fileInfo.size))}</dd><dt class="text-sm font-medium text-gray-500">ETag</dt><dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">`);
  if ($options.fileInfo.ETag) {
    _push(`<div>${ssrInterpolate($options.fileInfo.ETag.replace(/"/g, ""))}</div>`);
  } else {
    _push(`<div>-</div>`);
  }
  _push(`</dd></div><div></div><div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"><dt class="text-sm font-medium text-gray-500">Content</dt><dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200"><li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm"><div class="w-0 flex-1 flex items-center"><svg class="flex-shrink-0 h-5 w-5 text-gray-400" x-description="Heroicon name: solid/paper-clip" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path></svg><span class="ml-2 flex-1 w-0 truncate">${ssrInterpolate($props.file.name)}</span></div>`);
  if ($options.downloadURL) {
    _push(`<div class="ml-4 flex-shrink-0"><a${ssrRenderAttr("href", $options.downloadURL)} class="font-medium text-indigo-600 hover:text-indigo-500"> Download </a></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</li></ul></dd></div></dl></div></div>`);
}
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/FileDetails.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const FileDetails = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["ssrRender", _sfc_ssrRender$5]]);
const _sfc_main$4 = {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default(test, ing) {
        return `select-input-${Math.random() * 1e3}`;
      }
    },
    modelValue: [String, Number, Boolean],
    label: String,
    error: String
  },
  data() {
    return {
      selected: this.modelValue
    };
  },
  watch: {
    selected(selected) {
      this.$emit("update:modelValue", selected);
    }
  },
  methods: {
    focus() {
      this.$refs.input.focus();
    },
    select() {
      this.$refs.input.select();
    }
  }
};
function _sfc_ssrRender$4(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({
    class: _ctx.$attrs.class
  }, _attrs))}>`);
  if ($props.label) {
    _push(`<label class="form-label"${ssrRenderAttr("for", $props.id)}>${ssrInterpolate($props.label)}:</label>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<select${ssrRenderAttrs(mergeProps({
    id: $props.id,
    ref: "input"
  }, { ..._ctx.$attrs, class: null }, {
    class: ["form-select", { error: $props.error }]
  }))}>`);
  ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
  _push(`</select>`);
  if ($props.error) {
    _push(`<div class="form-error">${ssrInterpolate($props.error)}</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SelectInput.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const SelectInput = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender$4]]);
const _sfc_main$3 = {
  components: {
    FolderIcon: FolderIcon$1,
    DocumentTextIcon,
    ArrowDownTrayIcon,
    ChevronRightIcon,
    InformationCircleIcon,
    HomeIcon,
    FileDetails,
    JetDialogModal,
    JetSecondaryButton,
    EllipsisVerticalIcon,
    ExclamationCircleIcon,
    ArrowUpTrayIcon,
    CheckIcon,
    SelectInput,
    TrashIcon,
    ToolTip,
    JetConfirmationModal,
    JetDangerButton
  },
  props: ["draft", "readonly", "height"],
  emits: ["loading"],
  data() {
    return {
      status: "",
      dropzone: null,
      fullScreen: false,
      precentageUpload: 0,
      busy: false,
      loading: false,
      file: null,
      url: null,
      logs: {},
      logFilter: "Error",
      logFilters: ["Error", "Success", "Queued", "Inprogress"],
      uploadBatchErrors: [],
      showErrorBatchLogs: false,
      showLogsDialog: false,
      currentLog: null,
      fsoBeingDeleted: null,
      showMissingFilesDetails: null,
      missing_files: 0,
      missing_files_list: []
    };
  },
  computed: {
    baseURL() {
      return String(this.$page.props.url);
    },
    filteredLogs() {
      let logsClone = JSON.parse(JSON.stringify(this.logs));
      Object.keys(logsClone).forEach((key) => {
        if (logsClone[key].status != this.logFilter)
          delete logsClone[key];
      });
      return logsClone;
    }
  },
  mounted() {
    if (this.draft) {
      this.url = this.baseURL + "/dashboard/drafts/" + this.draft.id + "/files";
      this.loadDropZone();
    }
  },
  methods: {
    confirmFSODeletion() {
      this.fsoBeingDeleted = this.$page.props.selectedFileSystemObject.id;
    },
    deleteFSO() {
      if (this.$page.props.selectedFileSystemObject.id) {
        this.fsoBeingDeleted = null;
        this.updateBusyStatus(true);
        this.$emit("loading", true);
        axios.delete(
          "/dashboard/drafts/" + this.draft.id + "/files/" + this.$page.props.selectedFileSystemObject.id
        ).then((response) => {
          this.annotate();
          this.$emit("loading", true);
        });
      }
    },
    showMissingFilesDetailsModal() {
      this.fetchMissingFiles();
      this.showMissingFilesDetails = true;
    },
    fetchMissingFiles() {
      axios.get("/dashboard/drafts/" + this.draft.id + "/missing-files").then((response) => {
        console.log(response);
        this.missing_files_list = response.data.missing_files;
      });
    },
    toggleFullScreen() {
      this.fullScreen = !this.fullScreen;
    },
    toggleShowLogsDialog() {
      this.showLogsDialog = !this.showLogsDialog;
    },
    updateBusyStatus(status) {
      this.busy = status;
    },
    loadFiles() {
      this.updateBusyStatus(true);
      if (this.draft) {
        axios.get(this.url).then((response) => {
          this.file = response.data.file;
          this.file.has_children = true;
          this.$page.props.selectedFileSystemObject = this.file;
          this.$page.props.selectedFolder = "/";
          this.updateBusyStatus(true);
          this.$emit("loading", false);
          this.loading = false;
          this.missing_files = response.data.missing_files;
        });
      } else {
        this.file = this.$page.props.selectedFileSystemObject;
      }
    },
    annotate() {
      this.updateBusyStatus(true);
      this.$emit("loading", true);
      this.loading = true;
      this.status = "PROCESSING UPLOADED FILES";
      axios.get("/dashboard/drafts/" + this.draft.id + "/annotate").then(() => {
        this.updateBusyStatus(false);
        this.status = null;
        this.loadFiles();
      });
    },
    processFilesDZL(vm, filesBatch) {
      vm.batchesCount += 1;
      const url = "/dashboard/storage/signed-draft-storage-url";
      const client = axios.create({ baseURL: window.location.origin });
      vm.currentLog = "Fetching temporary storage url";
      axiosRetry(client, {
        retries: 3,
        retryCondition: (error) => {
          return error.response.status === 500 || error.response.status === 502;
        }
      });
      client.post(url, {
        draft_files: filesBatch,
        destination: vm.$page.props.selectedFolder,
        draft_id: vm.draft.id
      }).catch((err) => {
        this.processedBatches += 1;
        if (this.processedBatches == this.batches) {
          if (this.dropzone.files.length > 0) {
            this.status = "ERROR UPLOADING FILES";
            this.updateBusyStatus(false);
            this.dropzone.files.forEach((file) => {
              let message = "Upload failed";
              if (file.fullPath) {
                vm.logs[file.fullPath].status = "Error";
                vm.logs[file.fullPath].messages.push(
                  message + " (API call failed with status code:" + err.response.status + ") "
                );
              } else {
                vm.logs[file.name].status = "Error";
                vm.logs[file.name].messages.push(
                  message + "(API call failed with status code:" + err.response.status + ")"
                );
              }
            });
          }
        }
        this.uploadBatchErrors.push(err.response.data);
      }).then((response) => {
        if (response) {
          vm.currentLog = "Uploading files to temporary storage url";
          let data = response.data;
          this.processedBatches += 1;
          data.forEach((u) => {
            let cFile = vm.dropzone.files.find((f) => {
              if (f.fullPath) {
                return f.fullPath.trim() == u.fullPath;
              } else {
                return "/" + f.name.trim() == u.fullPath || vm.$page.props.selectedFolder + "/" + f.name.trim() == u.fullPath;
              }
            });
            if (cFile) {
              let message = "Presigned Upload URL receieved.  Starting Upload.";
              if (cFile.fullPath) {
                vm.logs[cFile.fullPath].status = "Inprogress";
                vm.logs[cFile.fullPath].messages.push(
                  message
                );
              } else {
                vm.logs[cFile.name].status = "Inprogress";
                vm.logs[cFile.name].messages.push(message);
              }
              let headers = u.headers;
              if ("Host" in headers) {
                delete headers.Host;
              }
              cFile.uploadURL = u.url;
              setTimeout(
                () => vm.dropzone.processFile(cFile)
              );
            }
          });
        }
      });
    },
    loadDropZone() {
      this.$nextTick(() => {
        const vm = this;
        vm.totalFilesCount = 0;
        vm.uploadedFilesCount = 0;
        vm.batchCount = 100;
        vm.count = 0;
        vm.batches = 0;
        vm.processedBatches = 0;
        vm.$page.props.selectedFileSystemObject = vm.file;
        vm.$page.props.selectedFolder = "/";
        vm.selectedFSO = [];
        let options = {
          url: "/",
          method: "put",
          sending(file, xhr) {
            let _send = xhr.send;
            xhr.send = () => {
              _send.call(xhr, file);
            };
          },
          autoProcessQueue: false,
          uploadMultiple: true,
          disablePreviews: true,
          parallelUploads: 100,
          autoQueue: false,
          maxFiles: 2e4,
          dictDefaultMessage: document.querySelector(
            "#fs-dropzone-message"
          ).innerHTML,
          totaluploadprogress: function(progress) {
            vm.progress = Math.ceil(progress);
          }
        };
        vm.dropzone = new Dropzone("#fs-dropzone", options);
        vm.dropzone.on("processing", (file) => {
          vm.dropzone.options.url = file.uploadURL;
          vm.status = "UPLOAD IN PROGRESS";
        });
        vm.dropzone.on("success", (file) => {
          let message = "Upload complete";
          if (file.fullPath) {
            vm.logs[file.fullPath].status = "Success";
            vm.logs[file.fullPath].messages.push(message);
          } else {
            vm.logs[file.name].status = "Success";
            vm.logs[file.name].messages.push(message);
          }
          vm.uploadedFilesCount += 1;
          vm.precentageUpload = vm.uploadedFilesCount / vm.totalFilesCount * 100;
          vm.currentLog = file.fullPath;
        });
        vm.dropzone.on("error", (file) => {
          let message = "Upload failed";
          if (file.fullPath) {
            vm.logs[file.fullPath].status = "Error";
            vm.logs[file.fullPath].messages.push(message);
          } else {
            vm.logs[file.name].status = "Error";
            vm.logs[file.name].messages.push(message);
          }
        });
        vm.dropzone.on("addedfile", (file) => {
          vm.selectedFSO.push(file);
          if (file.fullPath) {
            vm.logs[file.fullPath] = {
              status: "Queued",
              messages: []
            };
          } else {
            vm.logs[file.name] = {
              status: "Queued",
              messages: []
            };
          }
        });
        vm.dropzone.on("addedfiles", (files) => {
          if (files.length > 0) {
            this.updateBusyStatus(true);
            setTimeout(() => {
              var timer = setInterval(function() {
                if (vm.totalFilesCount === vm.selectedFSO.length) {
                  clearInterval(timer);
                  vm.status = "BATCH UPLOAD STARTED";
                  for (let i = 0; i < vm.totalFilesCount; i += vm.batchCount) {
                    let filesBatch = vm.dropzone.files.slice(
                      i,
                      i + vm.batchCount
                    );
                    vm.batches += 1;
                    vm.processFilesDZL(vm, filesBatch);
                  }
                } else {
                  vm.totalFilesCount = vm.selectedFSO.length;
                  vm.status = "PROCESSING FILES";
                }
              }, 500);
            });
          }
        });
        vm.dropzone.on("queuecomplete", () => {
          vm.status = "UPLOAD COMPLETE";
          vm.annotate();
          vm.currentLog = null;
          vm.dropzone.removeAllFiles();
          vm.precentageUpload = 0;
          vm.totalFilesCount = 0;
          vm.uploadedFilesCount = 0;
          this.updateBusyStatus(false);
          setTimeout(() => {
            vm.status = null;
          }, 5e3);
        });
      });
    },
    displaySelected(file) {
      this.$page.props.selectedFileSystemObject = file;
      let sFolder = "/";
      if (this.$page.props.selectedFileSystemObject.name == "/") {
        sFolder = "/";
      } else {
        if (this.$page.props.selectedFileSystemObject.type != "file") {
          sFolder = this.$page.props.selectedFileSystemObject.relative_url;
        } else {
          if (this.$page.props.selectedFileSystemObject.parent_id == null) {
            sFolder = "/";
          } else {
            sFolder = this.$page.props.selectedFileSystemObject.relative_url.replace(
              "/" + this.$page.props.selectedFileSystemObject.name,
              ""
            );
          }
        }
      }
      this.$page.props.selectedFolder = sFolder;
      if (file.has_children && file.level > 0 && !file.children) {
        file.loading = true;
        axios.get("/api/v1/files/children/" + file.id).then((response) => {
          file.children = response.data.files[0].children;
          file.loading = false;
        });
      }
    }
  }
};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_ToolTip = resolveComponent("ToolTip");
  const _component_children = resolveComponent("children");
  const _component_InformationCircleIcon = resolveComponent("InformationCircleIcon");
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_CheckIcon = resolveComponent("CheckIcon");
  const _component_ArrowUpTrayIcon = resolveComponent("ArrowUpTrayIcon");
  const _component_EllipsisVerticalIcon = resolveComponent("EllipsisVerticalIcon");
  const _component_ExclamationCircleIcon = resolveComponent("ExclamationCircleIcon");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_TrashIcon = resolveComponent("TrashIcon");
  const _component_FolderIcon = resolveComponent("FolderIcon");
  const _component_DocumentTextIcon = resolveComponent("DocumentTextIcon");
  const _component_File_details = resolveComponent("File-details");
  const _component_jet_confirmation_modal = resolveComponent("jet-confirmation-modal");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  _push(`<!--[--><div id="fs-dropzone" class="${ssrRenderClass([
    $data.fullScreen ? "fixed w-screen h-screen -ml-4 -mt-6 sm:ml-0 md:-ml-0 md:w-auto inset-0" : "",
    "mt-3 bg-white"
  ])}"><div><div class="${ssrRenderClass([$data.fullScreen ? "px-6 py-4" : "", "flex"])}"><div class="w-full px-5">`);
  if (!$props.readonly) {
    _push(`<div class="text-sm cursor-pointer hover:text-blue-700 mt-2 mr-10"><i><a href="https://docs.nmrxiv.org/submission-guides/folder-structure.html" target="_blank" class="mb-4">`);
    _push(ssrRenderComponent(_component_ToolTip, {
      class: "w-3.5 h-3.5",
      text: "To submit data you will need an account with nmrXiv, so you will be redirected to our register page and once registered you can then go ahead and submit data. For more information please checkout our <a target='_blank' href='//docs.nmrxiv.org' class='text-gray-400' target='_blank'>documentation</a>."
    }, null, _parent));
    _push(`<span class="ml-4"> Learn more about folder structuring </span></a></i>`);
    if ($data.missing_files > 0) {
      _push(`<a class="text-red-900 text-strong float-right"><svg class="h-5 w-5 text-red-400 inline" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg> ${ssrInterpolate($data.missing_files)} files missing </a>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div>`);
  if (!$props.readonly) {
    _push(`<div class="${ssrRenderClass([$data.fullScreen ? "px-6 py-4" : "px-5", ""])}"><form class="py-2 mb-3"><div id="fs-dropzone-message" class="text-center"><div type="button" class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-4 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"><svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"></path></svg><span class="mt-2 block text-lg font-bold text-blue-600"> Drop Files or Folders to upload `);
    if (_ctx.$page.props.selectedFolder && _ctx.$page.props.selectedFolder != "/") {
      _push(`<span> to &quot;${ssrInterpolate(_ctx.$page.props.selectedFolder)}&quot; folder </span>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div class="text-sm text-gray-400"> Need help? Check out our <a class="text-blue-800 hover:underline" href="https://docs.nmrxiv.org/submission-guides/submission-process.html#step-1-files-upload" target="_blank">submission guides </a></div></span>`);
    if ($data.dropzone) {
      _push(`<div class="relative mt-5"><div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200"><div style="${ssrRenderStyle(
        "width: " + $data.precentageUpload + "%"
      )}" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div></div>`);
      if ($data.status) {
        _push(`<span class="mt-2 block text-sm font-medium text-gray-900">${ssrInterpolate($data.status)} `);
        if ($data.uploadBatchErrors.length > 0) {
          _push(`<div> - <a class="text-red-700 cursor-pointer">View logs</a></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</span>`);
      } else {
        _push(`<!---->`);
      }
      if ($data.showErrorBatchLogs) {
        _push(`<div class="mt-2 block text-sm font-medium text-gray-900"><!--[-->`);
        ssrRenderList($data.uploadBatchErrors, (error, $index) => {
          _push(`<div class="rounded-md">${error}</div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div></div></form></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($data.loading) {
    _push(`<div><div class="h-[calc(100vh-260px)] text-center py-12"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading Files... </div></div>`);
  } else {
    _push(`<div class="${ssrRenderClass([
      $data.fullScreen ? "overflow-scroll h-full relative px-6 py-4" : "px-1",
      "min-w-0 flex-1 bg-white border-t border-gray-200 lg:flex"
    ])}"><aside class="${ssrRenderClass([
      $props.height ? $props.height : "",
      "py-2 lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col overflow-y-scroll overflow-x-scroll border-r"
    ])}">`);
    _push(ssrRenderComponent(_component_children, { file: $data.file }, null, _parent));
    if (Object.keys($data.logs).length > 0 && !$props.readonly) {
      _push(`<div class="mt-4 text-sm cursor-pointer text-gray-400">`);
      _push(ssrRenderComponent(_component_InformationCircleIcon, {
        class: "h-5 w-5 inline text-gray-400 flex-shrink-0 mx-auto",
        "aria-hidden": "true"
      }, null, _parent));
      _push(` View logs </div>`);
    } else {
      _push(`<!---->`);
    }
    _push(ssrRenderComponent(_component_jet_dialog_modal, {
      show: $data.showLogsDialog,
      onClose: ($event) => $data.showLogsDialog = false
    }, {
      title: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="block"${_scopeId}> File logs <div class="inline float-right"${_scopeId}><select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"${_scopeId}><!--[-->`);
          ssrRenderList($data.logFilters, (filter) => {
            _push2(`<option${ssrRenderAttr("value", filter)}${_scopeId}>${ssrInterpolate(filter)}</option>`);
          });
          _push2(`<!--]--></select></div></div>`);
        } else {
          return [
            createVNode("div", { class: "block" }, [
              createTextVNode(" File logs "),
              createVNode("div", { class: "inline float-right" }, [
                withDirectives(createVNode("select", {
                  "onUpdate:modelValue": ($event) => $data.logFilter = $event,
                  class: "mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($data.logFilters, (filter) => {
                    return openBlock(), createBlock("option", {
                      key: filter,
                      value: filter
                    }, toDisplayString(filter), 9, ["value"]);
                  }), 128))
                ], 8, ["onUpdate:modelValue"]), [
                  [vModelSelect, $data.logFilter]
                ])
              ])
            ])
          ];
        }
      }),
      content: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="relative h-[74vh] overflow-x-scroll z-0 mt-1 rounded-lg cursor-pointer"${_scopeId}>`);
          if (Object.keys($options.filteredLogs).length > 0) {
            _push2(`<ul role="list" class="divide-y divide-gray-200"${_scopeId}><!--[-->`);
            ssrRenderList(Object.keys(
              $options.filteredLogs
            ), (file) => {
              _push2(`<li class="py-4 flex"${_scopeId}>`);
              if ($data.logs[file].status == "Success") {
                _push2(ssrRenderComponent(_component_CheckIcon, {
                  class: "h-5 w-5 inline text-green-400",
                  "aria-hidden": "true"
                }, null, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              if ($data.logs[file].status == "Inprogress") {
                _push2(ssrRenderComponent(_component_ArrowUpTrayIcon, {
                  class: "h-5 w-5 inline text-yellow-400",
                  "aria-hidden": "true"
                }, null, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              if ($data.logs[file].status == "Inprogress") {
                _push2(ssrRenderComponent(_component_EllipsisVerticalIcon, {
                  class: "h-5 w-5 inline text-gray-400",
                  "aria-hidden": "true"
                }, null, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              if ($data.logs[file].status == "Error") {
                _push2(ssrRenderComponent(_component_ExclamationCircleIcon, {
                  class: "h-5 w-5 inline text-red-400",
                  "aria-hidden": "true"
                }, null, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="ml-3"${_scopeId}><p class="text-sm font-medium text-gray-900"${_scopeId}>${ssrInterpolate(file)}</p><!--[-->`);
              ssrRenderList($data.logs[file].messages, (message) => {
                _push2(`<p class="text-sm text-gray-400"${_scopeId}>${ssrInterpolate(message)}</p>`);
              });
              _push2(`<!--]--></div></li>`);
            });
            _push2(`<!--]--></ul>`);
          } else {
            _push2(`<div class="mt-10"${_scopeId}><i class="text-gray-400"${_scopeId}>No logs with the status ${ssrInterpolate($data.logFilter)}</i></div>`);
          }
          _push2(`</div>`);
        } else {
          return [
            createVNode("div", { class: "relative h-[74vh] overflow-x-scroll z-0 mt-1 rounded-lg cursor-pointer" }, [
              Object.keys($options.filteredLogs).length > 0 ? (openBlock(), createBlock("ul", {
                key: 0,
                role: "list",
                class: "divide-y divide-gray-200"
              }, [
                (openBlock(true), createBlock(Fragment, null, renderList(Object.keys(
                  $options.filteredLogs
                ), (file) => {
                  return openBlock(), createBlock("li", {
                    key: file,
                    class: "py-4 flex"
                  }, [
                    $data.logs[file].status == "Success" ? (openBlock(), createBlock(_component_CheckIcon, {
                      key: 0,
                      class: "h-5 w-5 inline text-green-400",
                      "aria-hidden": "true"
                    })) : createCommentVNode("", true),
                    $data.logs[file].status == "Inprogress" ? (openBlock(), createBlock(_component_ArrowUpTrayIcon, {
                      key: 1,
                      class: "h-5 w-5 inline text-yellow-400",
                      "aria-hidden": "true"
                    })) : createCommentVNode("", true),
                    $data.logs[file].status == "Inprogress" ? (openBlock(), createBlock(_component_EllipsisVerticalIcon, {
                      key: 2,
                      class: "h-5 w-5 inline text-gray-400",
                      "aria-hidden": "true"
                    })) : createCommentVNode("", true),
                    $data.logs[file].status == "Error" ? (openBlock(), createBlock(_component_ExclamationCircleIcon, {
                      key: 3,
                      class: "h-5 w-5 inline text-red-400",
                      "aria-hidden": "true"
                    })) : createCommentVNode("", true),
                    createVNode("div", { class: "ml-3" }, [
                      createVNode("p", { class: "text-sm font-medium text-gray-900" }, toDisplayString(file), 1),
                      (openBlock(true), createBlock(Fragment, null, renderList($data.logs[file].messages, (message) => {
                        return openBlock(), createBlock("p", {
                          key: message,
                          class: "text-sm text-gray-400"
                        }, toDisplayString(message), 1);
                      }), 128))
                    ])
                  ]);
                }), 128))
              ])) : (openBlock(), createBlock("div", {
                key: 1,
                class: "mt-10"
              }, [
                createVNode("i", { class: "text-gray-400" }, "No logs with the status " + toDisplayString($data.logFilter), 1)
              ]))
            ])
          ];
        }
      }),
      footer: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_jet_secondary_button, {
            class: "cursor-pointer",
            onClick: $options.toggleShowLogsDialog
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Close `);
              } else {
                return [
                  createTextVNode(" Close ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
        } else {
          return [
            createVNode(_component_jet_secondary_button, {
              class: "cursor-pointer",
              onClick: $options.toggleShowLogsDialog
            }, {
              default: withCtx(() => [
                createTextVNode(" Close ")
              ]),
              _: 1
            }, 8, ["onClick"])
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</aside><section class="${ssrRenderClass([
      $props.height ? $props.height : "",
      "p-6 flex-1 flex flex-col lg:order-last overflow-y-scroll"
    ])}">`);
    if (_ctx.$page.props.selectedFileSystemObject && _ctx.$page.props.selectedFileSystemObject.has_children) {
      _push(`<div class="mb-3"><div class="py-2 mb-2 block border-b pb-4"><p class="font-bold text-xl">${ssrInterpolate(_ctx.$page.props.selectedFileSystemObject.relative_url)} `);
      if (_ctx.$page.props.selectedFileSystemObject.id && !$props.readonly) {
        _push(`<a class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right">`);
        _push(ssrRenderComponent(_component_TrashIcon, {
          class: "cursor-pointer h-4 w-4 text-gray-900 mr-2",
          "aria-hidden": "true"
        }, null, _parent));
        _push(` Delete </a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</p></div><ul role="list" class="mb-3 mt-6 grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4"><!--[-->`);
      ssrRenderList(_ctx.$page.props.selectedFileSystemObject.children, (file) => {
        _push(`<li class="relative shadow rounded-lg"><div style="${ssrRenderStyle({ "user-select": "none" })}" class="hover:cursor-pointer"><div class="group block w-full aspect-w-10 aspect-h-7 py-4 bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden">`);
        if (file.type == "directory") {
          _push(`<span>`);
          _push(ssrRenderComponent(_component_FolderIcon, {
            class: "cursor-pointer h-28 w-28 text-gray-400 flex-shrink-0 mx-auto",
            "aria-hidden": "true"
          }, null, _parent));
          _push(`</span>`);
        } else {
          _push(`<span>`);
          _push(ssrRenderComponent(_component_DocumentTextIcon, {
            class: "h-28 w-28 text-gray-400 flex-shrink-0 mx-auto",
            "aria-hidden": "true"
          }, null, _parent));
          _push(`</span>`);
        }
        if (file.status == "missing") {
          _push(`<span class="absolute right-0 top-0 pr-4 pt-4 text-sm font-medium text-gray-500 pointer-events-none"><div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path></svg></div></span>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><p class="mt-2 px-2 py-1 block text-sm font-medium truncate text-gray-900 pointer-events-none"><span class="float-left">${ssrInterpolate(file.name)}</span></p></div></li>`);
      });
      _push(`<!--]--></ul></div>`);
    } else {
      _push(`<div>`);
      if (_ctx.$page.props.selectedFileSystemObject) {
        _push(`<div class="py-2 mb-2 block border-b pb-4"><p class="font-bold text-xl">${ssrInterpolate(_ctx.$page.props.selectedFileSystemObject.relative_url)} `);
        if (!$props.readonly) {
          _push(`<a class="ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 float-right">`);
          _push(ssrRenderComponent(_component_TrashIcon, {
            class: "cursor-pointer h-4 w-4 text-gray-900 mr-2",
            "aria-hidden": "true"
          }, null, _parent));
          _push(` Delete </a>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</p></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="pt-5">`);
      if (_ctx.$page.props.selectedFileSystemObject && _ctx.$page.props.selectedFileSystemObject.type == "file") {
        _push(`<span>`);
        _push(ssrRenderComponent(_component_File_details, {
          file: _ctx.$page.props.selectedFileSystemObject
        }, null, _parent));
        _push(`</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div>`);
    }
    _push(`</section></div>`);
  }
  _push(`</div></div>`);
  if ($data.status != "PROCESSING UPLOADED FILES" && $data.status != "" && $data.status != null || $data.precentageUpload > 0) {
    _push(`<div class="w-full h-screen mx-84 px-10 fixed block top-0 left-0 bg-white opacity-90 z-50"><div role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2"><svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path></svg><div class="mt-4 w-64 h-84"><div class="h-2 mb-2 text-xs flex rounded-md bg-gray-200"><div style="${ssrRenderStyle("width: " + $data.precentageUpload + "%")}" class="shadow-none flex rounded-md flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div></div> ${ssrInterpolate($data.status)} (${ssrInterpolate(_ctx.uploadedFilesCount)}/${ssrInterpolate(_ctx.totalFilesCount)}) </div><div class="w-64 text-gray-700 truncate"><small><i>${ssrInterpolate($data.currentLog)}</i></small></div>`);
    if (Object.keys($data.logs).length > 0) {
      _push(`<button class="mt-4 text-sm cursor-pointer bg-white-900">`);
      _push(ssrRenderComponent(_component_InformationCircleIcon, {
        class: "h-5 w-5 inline flex-shrink-0 mx-auto",
        "aria-hidden": "true"
      }, null, _parent));
      _push(` View logs </button>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(ssrRenderComponent(_component_jet_confirmation_modal, {
    show: $data.fsoBeingDeleted,
    onClose: ($event) => $data.fsoBeingDeleted = null
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Delete `);
      } else {
        return [
          createTextVNode(" Delete ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Are you sure you would like to delete ${ssrInterpolate(_ctx.$page.props.selectedFileSystemObject.name)}? `);
      } else {
        return [
          createTextVNode(" Are you sure you would like to delete " + toDisplayString(_ctx.$page.props.selectedFileSystemObject.name) + "? ", 1)
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.fsoBeingDeleted = null
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Cancel `);
            } else {
              return [
                createTextVNode(" Cancel ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_danger_button, {
          class: "ml-2",
          onClick: $options.deleteFSO
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Delete `);
            } else {
              return [
                createTextVNode(" Delete ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, {
            onClick: ($event) => $data.fsoBeingDeleted = null
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_danger_button, {
            class: "ml-2",
            onClick: $options.deleteFSO
          }, {
            default: withCtx(() => [
              createTextVNode(" Delete ")
            ]),
            _: 1
          }, 8, ["onClick"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_jet_confirmation_modal, {
    show: $data.missing_files > 0 && $data.showMissingFilesDetails,
    onClose: ($event) => $data.showMissingFilesDetails = null
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Missing Files `);
      } else {
        return [
          createTextVNode(" Missing Files ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Following files are mising <br${_scopeId}><!--[-->`);
        ssrRenderList($data.missing_files_list, (file) => {
          _push2(`<span${_scopeId}>${ssrInterpolate(file.relative_url)} <br${_scopeId}></span>`);
        });
        _push2(`<!--]-->`);
      } else {
        return [
          createTextVNode(" Following files are mising "),
          createVNode("br"),
          (openBlock(true), createBlock(Fragment, null, renderList($data.missing_files_list, (file) => {
            return openBlock(), createBlock("span", null, [
              createTextVNode(toDisplayString(file.relative_url) + " ", 1),
              createVNode("br")
            ]);
          }), 256))
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.showMissingFilesDetails = null
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Cancel `);
            } else {
              return [
                createTextVNode(" Cancel ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, {
            onClick: ($event) => $data.showMissingFilesDetails = null
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/FileSystemBrowser.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const FileSystemBrowser = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender$3]]);
const _sfc_main$2 = {
  components: {
    Link,
    JetDialogModal,
    JetSecondaryButton,
    JetButton,
    VueTagsInput,
    slider,
    SelectRich,
    XCircleIcon,
    PencilIcon,
    JetInputError,
    FileSystemBrowser,
    ClipboardDocumentIcon,
    QuestionMarkCircleIcon,
    ExclamationTriangleIcon,
    ArrowDownOnSquareStackIcon,
    TrashIcon,
    PlayIcon,
    PauseIcon,
    SpectraEditor,
    Validation
  },
  props: [],
  data() {
    return {
      openCreateDatasetDialog: false,
      loading: false,
      loadingStep: false,
      currentDraft: null,
      errorMessage: null,
      datasetsToImport: null,
      errorMessage: "",
      draftForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        tags: [],
        tag: "",
        tags_array: [],
        owner_id: null
      }),
      steps: [
        {
          id: "01",
          step: "tour-step-submission-header",
          name: "Files Upload",
          description: "Vitae sed mi luctus laoreet.",
          href: "#",
          status: "upcoming"
        },
        {
          id: "02",
          step: "v-step-20",
          name: "Assignments & Metadata",
          description: "Cursus semper viverra.",
          href: "#",
          status: "upcoming"
        },
        {
          id: "03",
          name: "Complete ~ Community Standards",
          description: "Penatibus eu quis ante.",
          href: "#",
          status: "upcoming"
        }
      ],
      tabs: [
        { name: "Experiments (Spectra)", current: true },
        { name: "Sample Info", current: false },
        { name: "Metadata", current: false }
      ],
      createDatasetForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        team_id: null,
        owner_id: null,
        color: null,
        starred: null,
        project_id: this.project ? this.project.id : null,
        is_public: ref(false)
      }),
      file: null,
      status: null,
      selectedStudy: null,
      studyName: "",
      studyDescription: "",
      studyTags: [],
      studyTag: "",
      selectedDataset: null,
      currentMolecules: [],
      showPrimer: false,
      smiles: "",
      percentage: 1,
      editor: null,
      updateProjectForm: this.$inertia.form({
        _method: "PUT",
        name: null,
        description: null,
        error_message: null,
        is_public: false,
        license: null,
        owner_id: null,
        team_id: null,
        license_id: null,
        releaseDate: this.setReleaseDate()
      }),
      studyForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        tags: [],
        tag: "",
        tags_array: []
      }),
      licenses: [],
      project: null,
      initialised: false,
      autoimportList: [],
      confirmPublicAccess: false,
      studiesExist: false,
      returnUrl: "/dashboard"
    };
  },
  computed: {
    primed() {
      return this.$page.props.auth.user.primed;
    },
    currentStep() {
      return this.steps.filter((s) => s.status == "current")[0];
    },
    currentTab() {
      return this.tabs.find((t) => t.current);
    },
    downloadURL() {
      return this.url + "/" + this.project.owner.username + "/datasets/" + this.project.slug;
    },
    url() {
      return String(this.$page.props.url) ? String(this.$page.props.url) : "https://dev.nmrxiv.org";
    },
    getMax() {
      if (this.selectedStudy) {
        let totalCount = 0;
        this.selectedStudy.sample.molecules.forEach((mol) => {
          totalCount += parseInt(mol.pivot.percentage_composition);
        });
        return 100 - totalCount;
      } else {
        return 100;
      }
    }
  },
  mounted() {
    const initialise = (data) => {
      this.fetchDrafts().then((response) => {
        this.returnUrl = this.url + this.returnUrl + (data.return_url ? data.return_url : "");
        this.drafts = response.data.drafts;
        this.sharedDrafts = response.data.sharedDrafts;
        if (data.draft_id) {
          let selectedDraft = this.drafts.find(
            (d) => d.id == data.draft_id
          );
          if (!selectedDraft) {
            selectedDraft = this.sharedDrafts.find(
              (d) => d.id == data.draft_id
            );
          }
          this.selectDraft(selectedDraft);
          this.loading = false;
          this.toggleOpenCreateDatasetDialog();
        } else {
          this.defaultDraft = response.data.default;
          this.toggleOpenCreateDatasetDialog();
          if (this.drafts.length == 0) {
            this.currentDraft = this.defaultDraft;
            this.selectStep(1);
            this.loading = false;
          } else {
            this.loading = false;
          }
          this.showPrimer = !this.primed;
        }
      });
    };
    this.emitter.all.set("openDatasetCreateDialog", [initialise]);
  },
  unmounted() {
  },
  methods: {
    hidePrimer() {
      axios.post("/primer/skip").then(() => {
        router.reload({
          only: ["user", "user.permissions", "user.roles"]
        });
      });
    },
    skipPrimer() {
      this.showPrimer = false;
    },
    getSVGString(molecule) {
      if (molecule.MOL) {
        let mol = OCL$1.Molecule.fromMolfile(
          "\n  " + molecule.MOL.replaceAll('"', "")
        );
        return mol.toSVG(200, 200);
      }
    },
    autoGenerateDescription() {
      let desc = "This dataset contains NMR spectra obtained for the sample -" + this.selectedStudy.name + "</br>";
      let sampleNames = [];
      let sampleTags = [];
      this.selectedStudy.datasets.forEach((ds) => {
        if (ds.nmrium_info) {
          let nmriuminfo = JSON.parse(ds.nmrium_info);
          if (nmriuminfo) {
            let spectra = nmriuminfo.spectra;
            spectra.forEach((sp) => {
              desc = desc + "Experiment: " + sp.info.experiment + "</br>";
              desc = desc + "Nucleus: " + sp.info.nucleus + "</br>";
              desc = desc + "Type: " + sp.info.type + "</br>";
              desc = desc + "Temperature: " + sp.info.temperature + "</br>";
              desc = desc + "Solvent: " + sp.info.solvent + "</br>";
              desc = desc + "Origin Frequency: " + sp.info.originFrequency + "</br>";
              desc = desc + "Probe Name: " + sp.info.probeName + "</br>";
              sampleTags.push(
                this.isString(sp.info.nucleus) ? sp.info.nucleus : sp.info.nucleus.join(" - ")
              );
              if (sp.info.sampleName) {
                sampleNames.push(sp.info.sampleName);
                desc = desc + "Sample Name: " + sp.info.sampleName + "</br>";
              }
            });
            desc = desc + "</br></br>";
          }
        }
      });
      this.studyForm.description = desc;
      if (sampleNames.length > 0) {
        sampleNames = [...new Set(sampleNames)];
        let sampleName = sampleNames.join(", ");
        if (sampleName != "") {
          this.studyForm.name = sampleNames.join(", ");
        }
      }
      if (sampleTags.length > 0) {
        sampleTags = [...new Set(sampleTags)];
        let tags = [];
        sampleTags.forEach((t) => {
          tags.push({
            text: t
          });
        });
        this.studyForm.tags = tags;
      }
    },
    updateLoadingStatus(status) {
      this.loadingStep = status;
    },
    deleteMolecule(mol) {
      axios.delete(
        "/dashboard/studies/" + this.selectedStudy.id + "/molecule/" + mol.id
      ).then((res) => {
        this.selectedStudy.sample.molecules = res.data;
        this.smiles = "";
        this.percentage = 0;
        this.editor.setSmiles("");
      });
    },
    updateProject() {
      this.updateProjectForm.clearErrors();
      this.loadingStep = true;
      this.updateProjectForm.name = this.project.name;
      this.updateProjectForm.owner_id = this.project.owner_id;
      this.updateProjectForm.license_id = this.updateProjectForm.license ? this.updateProjectForm.license.id : null;
      this.updateProjectForm.team_id = this.project.team_id;
      this.updateProjectForm.description = this.project.description;
      this.updateProjectForm.post(
        route("dashboard.project.update", this.project.id),
        {
          preserveScroll: true,
          onSuccess: () => {
            this.loadingStep = false;
          },
          onError: (err) => {
            this.loadingStep = false;
          }
        }
      );
    },
    loadLicenses() {
      if (this.$page.props.licenses) {
        this.licenses = this.$page.props.licenses;
      } else {
        axios.get(route("licenses")).then((res) => {
          this.licenses = res.data;
          this.$page.props.licenses = res.data;
        });
      }
    },
    setReleaseDate() {
      var current_date = /* @__PURE__ */ new Date();
      var relase_date = /* @__PURE__ */ new Date();
      relase_date.setDate(current_date.getDate() + 7);
      return relase_date;
    },
    saveStudyDetails() {
      this.loadingStep = true;
      this.studyForm.tags_array = this.studyForm.tags.map((a) => a.text);
      axios.put(
        "/dashboard/studies/" + this.selectedStudy.id + "/update",
        this.studyForm
      ).catch((error) => {
        this.loadingStep = false;
        Object.keys(error.response.data.errors).forEach((key) => {
          error.response.data.errors[key] = error.response.data.errors[key].join(", ");
        });
        this.studyForm.errors = error.response.data.errors;
        this.studyForm.error_message = error.response.data.message;
        this.studyForm.hasErrors = true;
      }).then((response) => {
        if (response) {
          this.loadingStep = false;
          this.selectStudy(
            response.data,
            this.selectedStudyIndex
          );
          this.studyForm.hasErrors = false;
        }
      });
    },
    editMolecule(mol) {
      this.editor.setMolFile("\n  " + mol.MOL.replaceAll('"', ""));
      this.percentage = parseInt(mol.pivot.percentage_composition);
      axios.delete(
        "/dashboard/studies/" + this.selectedStudy.id + "/molecule/" + mol.id
      ).then((res) => {
        this.selectedStudy.sample.molecules = res.data;
      });
    },
    saveMolecule() {
      let mol = this.editor.getMolFile();
      axios.post(
        "https://www.cheminfo.org/webservices/inchi",
        "molfile=" + mol
      ).then((response) => {
        let InChI = response.data.output.InChI;
        let InChIKey = response.data.output.InChIKey;
        let MF = this.editor.getMolecule().getMolecularFormula().formula;
        axios.post(
          "/dashboard/studies/" + this.selectedStudy.id + "/molecule",
          {
            InChI,
            InChIKey,
            percentage: this.percentage,
            formula: MF,
            mol
          }
        ).then((res) => {
          this.selectedStudy.sample.molecules = res.data;
          this.smiles = "";
          this.percentage = 0;
          this.editor.setSmiles("");
        });
      });
    },
    loadSmiles() {
      this.errorMessage = "";
      if (this.smiles && this.smiles != "") {
        try {
          let mol = OCL$1.Molecule.fromSmiles(this.smiles);
          this.editor.setSmiles(this.smiles);
        } catch (e) {
          this.errorMessage = "The entered SMILES is not valid.";
        }
      }
    },
    selectTab(tab) {
      this.tabs.forEach((t) => {
        if (t.name == tab.name) {
          t.current = true;
          this.$nextTick(() => {
            this.editor = OCL$1.StructureEditor.createSVGEditor(
              "structureSearchEditor",
              1
            );
          });
        } else {
          t.current = false;
        }
      });
    },
    closeDraft() {
      this.loadingStep = true;
      axios.post(
        "/dashboard/drafts/" + this.currentDraft.id + "/complete",
        {}
      ).catch((error) => {
        this.loadingStep = false;
      }).then((response) => {
        this.project = response.data.project;
        this.validation = this.parseJSON(
          response.data.validation.report
        );
        if (this.project) {
          this.loadingStep = false;
          this.loadLicenses();
          this.selectStep(3);
        }
      });
    },
    trackProject() {
      axios.get("/projects/status/" + this.project.id + "/queue").then((response) => {
        this.project.status = response.data.status;
        if (this.project.status != "complete") {
          setTimeout(() => this.trackProject(), 1e4);
        }
      });
    },
    openSelectDraftsView() {
      this.steps.forEach((s) => {
        s.status = "upcoming";
      });
      this.currentDraft = null;
    },
    selectDataset(dataset) {
      this.selectedDataset = dataset;
    },
    selectStudy(study, index, datasetIndex = null) {
      this.selectedStudyIndex = index;
      this.selectedStudy = study;
      this.studyForm.name = this.selectedStudy.name;
      this.studyForm.description = this.selectedStudy.description;
      let tags = [];
      this.selectedStudy.tags.forEach((t) => {
        tags.push({
          text: t.name["en"]
        });
      });
      this.studyForm.tags = tags;
      if (!datasetIndex) {
        this.selectedDSIndex = 0;
        this.selectDataset(this.selectedStudy.datasets[0]);
      } else {
        this.selectedDSIndex = datasetIndex;
        this.selectDataset(this.selectedStudy.datasets[datasetIndex]);
      }
    },
    createNewDraft() {
      if (!this.primed) {
        this.showPrimer = true;
        this.newDraft();
      } else {
        this.newDraft();
      }
    },
    newDraft() {
      if (this.defaultDraft) {
        this.defaultDraft.name = "Untitled Project - " + (/* @__PURE__ */ new Date()).toLocaleString();
        this.selectDraft(this.defaultDraft);
      } else {
        this.fetchDrafts().then((response) => {
          this.defaultDraft = response.data.default;
          this.loading = false;
          this.defaultDraft.name = "Untitled Project - " + (/* @__PURE__ */ new Date()).toLocaleString();
          this.selectDraft(this.defaultDraft);
        });
      }
    },
    hasStudies(file) {
      if (file.model_type == "study") {
        this.studiesExist = true;
      }
      if (file.has_children && file.children) {
        file.children.forEach((fso) => {
          this.hasStudies(fso);
        });
      }
    },
    process() {
      this.errorMessage = null;
      let foldersExist = false;
      this.$refs.fsbRef.file.children.forEach((fso) => {
        if (fso.has_children) {
          foldersExist = true;
        }
      });
      this.hasStudies(this.$refs.fsbRef.file);
      if (this.$refs.fsbRef.file && this.$refs.fsbRef.file.children.length > 0 && foldersExist && this.studiesExist) {
        this.loadingStep = true;
        this.draftForm.owner_id = this.$page.props.auth.user.id;
        this.draftForm.tags_array = this.draftForm.tags.map(
          (a) => a.text
        );
        axios.post(
          "/dashboard/drafts/" + this.currentDraft.id + "/process",
          this.draftForm
        ).then(
          (response) => {
            this.project = response.data.project;
            this.studies = response.data.studies;
            if (this.project && this.studies && this.studies.length > 0) {
              this.selectStudy(this.studies[0], 0);
              this.selectedDataset = this.selectedStudy.datasets[0];
              this.loadingStep = false;
              this.selectStep(2);
            } else {
              if (this.studies.length == 0) {
                this.loadingStep = false;
              }
            }
          },
          (error) => {
            this.loadingStep = false;
            Object.keys(error.response.data.errors).forEach(
              (key) => {
                error.response.data.errors[key] = error.response.data.errors[key].join(
                  ", "
                );
              }
            );
            this.draftForm.errors = error.response.data.errors;
            this.draftForm.error_message = error.response.data.message;
            this.draftForm.hasErrors = true;
            Object.keys(this.draftForm.errors).forEach(
              (key) => {
                if (!this.errorMessage) {
                  this.errorMessage = "<b class='capitalize'>" + key + "</b>: " + this.draftForm.errors[key] + "</br>";
                } else {
                  this.errorMessage += "<b class='capitalize'>" + key + "</b>: " + this.draftForm.errors[key] + "</br>";
                }
              }
            );
          }
        );
      } else {
        if (this.$refs.fsbRef.file.children.length > 0 && !foldersExist) {
          this.errorMessage = "Spectra files needs to be organised into folders. Please create a folder corresponding to each sample and add all your NMR spectroscopic experiment output files are added to the corresponding folders";
        } else if (this.$refs.fsbRef.file.children.length <= 0) {
          this.errorMessage = "Please upload spectral data to proceed.";
        } else if (!this.studiesExist) {
          this.errorMessage = "Please upload spectral data to proceed.";
        } else {
          this.errorMessage = "Please make sure you fill in all the required data before you proceed";
        }
      }
    },
    fetchProjectData() {
      axios.get("/dashboard/drafts/" + this.currentDraft.id + "/info").then((response) => {
        this.project = response.data.project;
        this.studies = response.data.studies;
        if (this.project && this.studies && this.studies.length > 0) {
          this.selectStudy(this.studies[0], 0);
          this.selectedDataset = this.selectedStudy.datasets[0];
          this.loadingStep = false;
          this.selectStep(2);
        } else {
          if (this.studies.length == 0) {
            this.loadingStep = false;
          }
        }
      });
    },
    selectStep(id) {
      this.steps.forEach((step) => {
        if (parseInt(step.id) < id) {
          step.status = "complete";
        } else if (parseInt(step.id) == id) {
          step.status = "current";
        } else {
          step.status = "upcoming";
        }
      });
      if (id == 1) {
        this.$nextTick(function() {
          if (this.$refs.fsbRef) {
            this.$refs.fsbRef.annotate();
          }
        });
      } else if (id == 2) {
        this.$nextTick(function() {
          if (this.$refs.spectraEditorREF)
            ;
        });
      }
    },
    toggleOpenCreateDatasetDialog() {
      this.openCreateDatasetDialog = !this.openCreateDatasetDialog;
    },
    autoImport() {
      this.loadingStep = true;
      this.datasetsToImport = [];
      this.studies.forEach((study) => {
        study.datasets.forEach((dataset) => {
          if (!dataset.has_nmrium) {
            this.datasetsToImport.push({
              projectSlug: this.project.slug,
              studySlug: study.slug,
              datasetSlug: dataset.slug,
              datasetId: dataset.id,
              status: false
            });
          }
        });
      });
      this.fetchNMRium();
    },
    fetchNMRium() {
      let datasetDetails = this.datasetsToImport.filter(
        (f) => f.status == false
      )[0];
      if (datasetDetails) {
        this.loadingStep = true;
        let ownerUserName = this.$page.props.team ? this.$page.props.team.owner.username : this.project.owner.username;
        axios.post("https://nodejsdev.nmrxiv.org/spectra-parser", {
          urls: [
            this.url + "/" + ownerUserName + "/datasets/" + datasetDetails.projectSlug + "/" + datasetDetails.studySlug + "/" + datasetDetails.datasetSlug
          ],
          snapshot: false
        }).then((response) => {
          axios.post(
            "/dashboard/datasets/" + datasetDetails.datasetId + "/nmriumInfo",
            response.data.data
          ).then((res) => {
            this.loadingStep = false;
            this.datasetsToImport.filter(
              (f) => f.datasetId == datasetDetails.datasetId
            )[0].status = true;
            this.fetchNMRium();
          }).catch((err) => {
            this.loadingStep = false;
            this.datasetsToImport.filter(
              (f) => f.datasetId == datasetDetails.datasetId
            )[0].status = true;
            this.fetchNMRium();
          });
        }).catch((error) => {
          this.loadingStep = false;
          this.datasetsToImport.filter(
            (f) => f.datasetId == datasetDetails.datasetId
          )[0].status = true;
          this.fetchNMRium();
        });
      } else {
        if (this.datasetsToImport.length > 0) {
          this.datasetsToImport = null;
          this.fetchProjectData();
        }
      }
    },
    fetchDrafts() {
      this.loading = true;
      return axios.get("/dashboard/drafts");
    },
    selectDraft(draft) {
      this.currentDraft = draft;
      this.draftForm.name = this.currentDraft.name;
      this.draftForm.description = this.currentDraft.description;
      let tags = [];
      this.file = null;
      this.draftForm.tags = [];
      if (this.currentDraft.tags) {
        this.currentDraft.tags.forEach((t) => {
          tags.push({
            text: t.name["en"]
          });
        });
        this.draftForm.tags = tags;
      }
      this.selectStep(1);
    },
    UpdateAndClose() {
      this.updateProject();
      this.toggleOpenCreateDatasetDialog();
    }
  }
};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_Link = resolveComponent("Link");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_vue_tags_input = resolveComponent("vue-tags-input");
  const _component_file_system_browser = resolveComponent("file-system-browser");
  const _component_ArrowDownOnSquareStackIcon = resolveComponent("ArrowDownOnSquareStackIcon");
  const _component_SpectraEditor = resolveComponent("SpectraEditor");
  const _component_TrashIcon = resolveComponent("TrashIcon");
  const _component_PencilIcon = resolveComponent("PencilIcon");
  const _component_slider = resolveComponent("slider");
  const _component_Validation = resolveComponent("Validation");
  const _component_ExclamationTriangleIcon = resolveComponent("ExclamationTriangleIcon");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  _push(ssrRenderComponent(_component_jet_dialog_modal, mergeProps({
    "max-width": "7xl",
    show: $data.openCreateDatasetDialog,
    onClose: ($event) => $data.openCreateDatasetDialog = false
  }, _attrs), {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if (!$data.showPrimer) {
          _push2(`<div${_scopeId}>`);
          if (!$data.loading && $data.currentDraft) {
            _push2(`<div class="lg:border-b lg:border-gray-200 pb-3 pr-5 -mx-6"${_scopeId}><div id="tour-step-submission-header" class="flex items-left justify-start pl-5 float-left"${_scopeId}><b${_scopeId}>${ssrInterpolate($data.steps.find((step) => step.status === "current").name)}</b></div><nav class="flex items-right justify-end" aria-label="Progress"${_scopeId}><p class="text-sm font-medium"${_scopeId}> Step ${ssrInterpolate($data.steps.findIndex(
              (step) => step.status === "current"
            ) + 1)} of ${ssrInterpolate($data.steps.length)}</p><ol role="list" class="ml-8 flex items-center space-x-5"${_scopeId}><!--[-->`);
            ssrRenderList($data.steps, (step) => {
              _push2(`<li${_scopeId}>`);
              if (step.status === "complete") {
                _push2(`<a class="block w-2.5 h-2.5 bg-teal-600 rounded-full hover:bg-teal-900"${_scopeId}></a>`);
              } else if (step.status === "current") {
                _push2(`<a class="relative flex items-center justify-center" aria-current="step"${_scopeId}><span class="absolute w-5 h-5 p-px flex" aria-hidden="true"${_scopeId}><span class="w-full h-full rounded-full bg-blue-100"${_scopeId}></span></span><span class="relative block w-2.5 h-2.5 bg-teal-600 rounded-full" aria-hidden="true"${_scopeId}></span></a>`);
              } else {
                _push2(`<a class="block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"${_scopeId}></a>`);
              }
              _push2(`</li>`);
            });
            _push2(`<!--]--></ol></nav></div>`);
          } else {
            _push2(`<div class="lg:border-b lg:border-gray-200 pb-3 pr-5 -mx-6"${_scopeId}>`);
            if (!$data.currentDraft) {
              _push2(`<div class="flex text-md font-bold items-left justify-start pl-5"${_scopeId}> Drafts </div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          !$data.showPrimer ? (openBlock(), createBlock("div", { key: 0 }, [
            !$data.loading && $data.currentDraft ? (openBlock(), createBlock("div", {
              key: 0,
              class: "lg:border-b lg:border-gray-200 pb-3 pr-5 -mx-6"
            }, [
              createVNode("div", {
                id: "tour-step-submission-header",
                class: "flex items-left justify-start pl-5 float-left"
              }, [
                createVNode("b", null, toDisplayString($data.steps.find((step) => step.status === "current").name), 1)
              ]),
              createVNode("nav", {
                class: "flex items-right justify-end",
                "aria-label": "Progress"
              }, [
                createVNode("p", { class: "text-sm font-medium" }, " Step " + toDisplayString($data.steps.findIndex(
                  (step) => step.status === "current"
                ) + 1) + " of " + toDisplayString($data.steps.length), 1),
                createVNode("ol", {
                  role: "list",
                  class: "ml-8 flex items-center space-x-5"
                }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($data.steps, (step) => {
                    return openBlock(), createBlock("li", {
                      key: step.name
                    }, [
                      step.status === "complete" ? (openBlock(), createBlock("a", {
                        key: 0,
                        class: "block w-2.5 h-2.5 bg-teal-600 rounded-full hover:bg-teal-900"
                      })) : step.status === "current" ? (openBlock(), createBlock("a", {
                        key: 1,
                        class: "relative flex items-center justify-center",
                        "aria-current": "step"
                      }, [
                        createVNode("span", {
                          class: "absolute w-5 h-5 p-px flex",
                          "aria-hidden": "true"
                        }, [
                          createVNode("span", { class: "w-full h-full rounded-full bg-blue-100" })
                        ]),
                        createVNode("span", {
                          class: "relative block w-2.5 h-2.5 bg-teal-600 rounded-full",
                          "aria-hidden": "true"
                        })
                      ])) : (openBlock(), createBlock("a", {
                        key: 2,
                        class: "block w-2.5 h-2.5 bg-gray-200 rounded-full hover:bg-gray-400"
                      }))
                    ]);
                  }), 128))
                ])
              ])
            ])) : (openBlock(), createBlock("div", {
              key: 1,
              class: "lg:border-b lg:border-gray-200 pb-3 pr-5 -mx-6"
            }, [
              !$data.currentDraft ? (openBlock(), createBlock("div", {
                key: 0,
                class: "flex text-md font-bold items-left justify-start pl-5"
              }, " Drafts ")) : createCommentVNode("", true)
            ]))
          ])) : createCommentVNode("", true)
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($data.loadingStep) {
          _push2(`<div class="absolute inset-0 flex justify-center items-center z-10 bg-white rounded bg-opacity-70"${_scopeId}><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId}></path></svg>`);
          if ($data.datasetsToImport && $data.datasetsToImport.length > 0) {
            _push2(`<div${_scopeId}><br${_scopeId}> Processing ${ssrInterpolate($data.datasetsToImport.filter((f) => f.status == true).length + 1)} of ${ssrInterpolate($data.datasetsToImport.length)} spectra </div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if (!$data.loading) {
          _push2(`<div${_scopeId}>`);
          if (_ctx.drafts.length > 0 && !$data.currentDraft) {
            _push2(`<div${_scopeId}><div${_scopeId}><ul role="list" class="-my-5 divide-y divide-gray-200"${_scopeId}><!--[-->`);
            ssrRenderList(_ctx.drafts, (draft) => {
              _push2(`<li class="py-4"${_scopeId}><div class="flex items-center space-x-4"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><p class="text-lg font-large text-black truncate"${_scopeId}><b${_scopeId}>${ssrInterpolate(draft.name)}</b></p><p class="text-sm font-medium text-gray-600 truncate pr-10"${_scopeId}>${ssrInterpolate(draft.description)}</p><p class="text-sm font-medium text-gray-500 truncate"${_scopeId}> ID: ${ssrInterpolate(draft.key)}</p><p class="text-sm text-gray-500 truncate"${_scopeId}> Created at: ${ssrInterpolate(_ctx.formatDateTime(draft.created_at))}</p></div><div${_scopeId}>`);
              _push2(ssrRenderComponent(_component_Link, {
                href: _ctx.route("dashboard", {
                  action: "submission",
                  draft_id: draft.id
                }),
                class: "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-2"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Select `);
                  } else {
                    return [
                      createTextVNode(" Select ")
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</div></div></li>`);
            });
            _push2(`<!--]--></ul></div><div class="relative mt-6"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex justify-center"${_scopeId}><span class="px-2 bg-white text-sm text-gray-500"${_scopeId}> Or </span></div></div><div class="my-6 justify-center items-center flex"${_scopeId}><button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"${_scopeId}> Create New </button></div></div>`);
          } else {
            _push2(`<div${_scopeId}>`);
            if ($data.showPrimer) {
              _push2(`<div class="-mx-6 -my-4"${_scopeId}><div${_scopeId}><section class="h-1/2 overflow-hidden bg-white overflow-hidden py-12"${_scopeId}><div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"${_scopeId}><svg class="absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2" width="404" height="404" fill="none" viewBox="0 0 404 404" role="img" aria-labelledby="svg-nmrxiv"${_scopeId}><title id="svg-nmrxiv"${_scopeId}>nmrxiv</title><defs${_scopeId}><pattern id="ad119f34-7694-4c31-947f-5c9d249b21f3" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"${_scopeId}><rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"${_scopeId}></rect></pattern></defs><rect width="404" height="404" fill="url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"${_scopeId}></rect></svg><div class="relative"${_scopeId}><blockquote${_scopeId}><div class="max-w-3xl mx-auto text-left text-xl leading-9 font-medium text-gray-900"${_scopeId}><p class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"${_scopeId}> Basic Concepts </p><p class="mt-3 text-lg"${_scopeId}> In nmrXiv, data is organised as <b class="text-teal-700"${_scopeId}>projects</b>. Consider a project is equivalent to your publication with the corresponding NMR data to be uploaded to nmrXiv. A project can have multiple studies. <b class="text-teal-700"${_scopeId}>A study corresponds to a group of NMR experiments of one sample</b>, e.g. 1H, 13C, APT, COSY HSQC, HMBC, NOESY in Bruker Format or (another study) in another format such as JCAMP-DX / Varian. <b class="text-teal-700"${_scopeId}>Each NMR measurement in a study is referred to as a dataset</b>, e.g. 1H NMR or COSY (are each a dataset). </p><img src="/img/primer.png" alt=""${_scopeId}><p class="text-md text-center leading-5 mb-3"${_scopeId}><small${_scopeId}>nmrXiv allows you to upload NMR raw data from any NMR instrument. We can currently auto-detect Bruker/Varian/JOEL formats &amp; JCAMP files and will support more raw &amp; processed file formats soon. Once you upload your raw or processed NMR data, nmrXiv will auto-generate the studies and datasets for you based on the uploaded folder structure. <i${_scopeId}><a class="text-xs" target="_blank" href="https://docs.nmrxiv.org/submission-guides/submission/folder-structure"${_scopeId}>More info here</a></i></small></p></div></blockquote></div></div></section></div></div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if ($options.currentStep) {
                _push2(`<div${_scopeId}>`);
                if ($options.currentStep.id == "01") {
                  _push2(`<div id="submission-dropzone"${_scopeId}>`);
                  if ($data.currentDraft) {
                    _push2(`<div${_scopeId}><div id="tour-step-project-name" class="mb-3"${_scopeId}><label for="project-name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Project Name </label><div class="mt-1"${_scopeId}><input${ssrRenderAttr("value", $data.draftForm.name)} type="text" name="project-name" class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"${_scopeId}></div>`);
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.draftForm.errors.name,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div><div id="tour-step-project-desc" class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700"${_scopeId}><span${_scopeId}>Project Description (Optional)</span></label><div class="mt-1"${_scopeId}><textarea id="description" name="description" placeholder="Describe this project" rows="3" class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"${_scopeId}>${ssrInterpolate($data.draftForm.description)}</textarea></div>`);
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.draftForm.errors.description,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div><div id="tour-step-project-keywords" class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700"${_scopeId}> Keywords (Optional) </label><div${_scopeId}>`);
                    _push2(ssrRenderComponent(_component_vue_tags_input, {
                      modelValue: $data.draftForm.tag,
                      "onUpdate:modelValue": ($event) => $data.draftForm.tag = $event,
                      placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                      separators: [";", ","],
                      "max-width": "100%",
                      tags: $data.draftForm.tags,
                      onTagsChanged: (newTags) => $data.draftForm.tags = newTags
                    }, null, _parent2, _scopeId));
                    _push2(`</div>`);
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.draftForm.errors.tags,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div><div${_scopeId}><div${_scopeId}><small class="cursor-pointer"${_scopeId}>Draft ID: ${ssrInterpolate($data.currentDraft.key)}</small><div id="tour-step-upload-spectra"${_scopeId}>`);
                    _push2(ssrRenderComponent(_component_file_system_browser, {
                      ref: "fsbRef",
                      readonly: false,
                      draft: $data.currentDraft,
                      onLoading: $options.updateLoadingStatus
                    }, null, _parent2, _scopeId));
                    _push2(`</div>`);
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.draftForm.errors.studies,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div></div></div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`</div>`);
                } else {
                  _push2(`<!---->`);
                }
                if ($options.currentStep.id == "02") {
                  _push2(`<div class="-mx-6 -my-4"${_scopeId}><div style="${ssrRenderStyle({ "height": "80vh" })}"${_scopeId}><div class="flex-1 flex md:overflow-hidden"${_scopeId}><nav aria-label="Sections" class="hidden flex-shrink-0 w-80 bg-white border-r border-blue-gray-200 md:flex md:flex-col"${_scopeId}><div class="border-gray-200 px-4 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider flex-shrink-0 border-b border-blue-gray-200"${_scopeId}>${ssrInterpolate(_ctx.pluralize(
                    "STUDY",
                    _ctx.studies.length
                  ))} (${ssrInterpolate(_ctx.studies.length)}) <div class="float-right cursor-pointer tooltip"${_scopeId}>`);
                  _push2(ssrRenderComponent(_component_ArrowDownOnSquareStackIcon, { class: "w-5 h-5 mr-1 text-gray-600 hover:text-gray-500" }, null, _parent2, _scopeId));
                  _push2(`<span class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"${_scopeId}>Click to auto import spectra</span></div></div><div id="tour-step-side-panel-studies" style="${ssrRenderStyle({ "height": "74vh", "overflow": "scroll !important" })}"${_scopeId}><!--[-->`);
                  ssrRenderList(_ctx.studies, (study, $index) => {
                    _push2(`<a class="${ssrRenderClass([
                      $data.selectedStudy.id == study.id ? "bg-gray-800 text-white" : "hover:bg-gray-200 hover:bg-opacity-50",
                      "cursor-pointer flex p-4 pr-5 border-b border-blue-gray-200"
                    ])}"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 -mt-0.5 h-6 w-6 text-blue-gray-400" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"${_scopeId}></path></svg><div class="ml-3 text-sm w-full"${_scopeId}><p class="font-medium text-blue-gray-900 pr-4"${_scopeId}><a class="hover:font-bold"${_scopeId}>${ssrInterpolate(study.name)}</a>`);
                    if (study.sample.molecules.length > 0) {
                      _push2(`<span class="float-right"${_scopeId}><img class="flex-shrink-0 -mt-0.5 mr-2 h-6 w-6 text-blue-gray-400" src="https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png" alt=""${_scopeId}></span>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(`</p><div class="mt-1 text-blue-gray-500"${_scopeId}><!--[-->`);
                    ssrRenderList(study.datasets, (ds, $dsIndex) => {
                      _push2(`<span${_scopeId}><div class="${ssrRenderClass([
                        study.has_nmrium || ds.has_nmrium ? "bg-green-100 text-gray-800" : "bg-gray-100 text-gray-800",
                        "w-64 inline-flex truncate break-words items-center px-3 py-0.5 rounded-full text-xs font-medium mr-1 hover:bg-teal-700"
                      ])}"${_scopeId}><a${_scopeId}>${ssrInterpolate(ds.name)} `);
                      if (ds.type) {
                        _push2(`<span${_scopeId}>(${ssrInterpolate(ds.type)})</span>`);
                      } else {
                        _push2(`<!---->`);
                      }
                      _push2(`</a></div></span>`);
                    });
                    _push2(`<!--]--></div></div></a>`);
                  });
                  _push2(`<!--]--></div></nav><div style="${ssrRenderStyle({ "height": "80vh", "overflow": "scroll !important" })}" class="flex-1 xl:overflow-y-auto"${_scopeId}><div class="mx-auto flex flex-col md:px-0 xl:px-0"${_scopeId}><main class="flex-1"${_scopeId}><div class="relative mx-auto md:px-0 xl:px-0"${_scopeId}><div class="pt-5 pb-16"${_scopeId}><div class="px-3 sm:px-3"${_scopeId}><h1 class="text-3xl font-extrabold text-gray-900"${_scopeId}>${ssrInterpolate($data.selectedStudy.name)}</h1></div><div class="lg:hidden px-4 sm:px-6"${_scopeId}><label for="selected-tab" class="sr-only"${_scopeId}>Select a tab</label><select id="selected-tab" name="selected-tab" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"${_scopeId}><!--[-->`);
                  ssrRenderList($data.tabs, (tab) => {
                    _push2(`<option${ssrIncludeBooleanAttr(
                      tab.current
                    ) ? " selected" : ""}${_scopeId}>${ssrInterpolate(tab.name)}</option>`);
                  });
                  _push2(`<!--]--></select></div><div class="hidden lg:block px-3 sm:px-3"${_scopeId}><div class="border-b border-gray-200"${_scopeId}><nav class="-mb-px flex space-x-8"${_scopeId}><!--[-->`);
                  ssrRenderList($data.tabs, (tab) => {
                    _push2(`<a class="${ssrRenderClass([
                      tab.current ? "border-teal-500 text-teal-600" : "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700",
                      "cursor-pointer whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    ])}"${_scopeId}>${ssrInterpolate(tab.name)}</a>`);
                  });
                  _push2(`<!--]--></nav></div></div>`);
                  if ($options.currentTab.name == "Metadata") {
                    _push2(`<div class="px-4 sm:px-6"${_scopeId}><div class="pt-4 xl:col-span-2"${_scopeId}><div class="mb-3"${_scopeId}><label for="study-name" class="block text-sm font-medium text-gray-700"${_scopeId}> Study Name <span class="float-right border rounded-full px-2 py-1 mb-2"${_scopeId}> Auto generate </span></label><div class="mt-1"${_scopeId}><input${ssrRenderAttr(
                      "value",
                      $data.studyForm.name
                    )} type="text" name="study-name" class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"${_scopeId}>`);
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.studyForm.errors.name,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div></div><div class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700"${_scopeId}> Study Description </label><div class="mt-1"${_scopeId}><textarea id="study-description" name="study-description" rows="3" class="block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"${_scopeId}>${ssrInterpolate(
                      $data.studyForm.description
                    )}</textarea>`);
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.studyForm.errors.description,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div></div><div class="mb-3"${_scopeId}><label for="description" class="block text-sm font-medium text-gray-700"${_scopeId}> Keywords </label><div${_scopeId}>`);
                    _push2(ssrRenderComponent(_component_vue_tags_input, {
                      modelValue: $data.studyForm.tag,
                      "onUpdate:modelValue": ($event) => $data.studyForm.tag = $event,
                      placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                      separators: [
                        ";",
                        ","
                      ],
                      "max-width": "100%",
                      tags: $data.studyForm.tags,
                      onTagsChanged: (newTags) => $data.studyForm.tags = newTags
                    }, null, _parent2, _scopeId));
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.studyForm.errors.tags,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div></div><div class="mb-6"${_scopeId}><button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}> SAVE </button></div></div></div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  if ($options.currentTab.name == "Experiments (Spectra)") {
                    _push2(`<div id="tour-step-nmrium" class="px-4 sm:px-6 md:px-0"${_scopeId}><div class="p-3"${_scopeId}><div class="my-3"${_scopeId}>`);
                    _push2(ssrRenderComponent(_component_SpectraEditor, {
                      ref: "spectraEditorREF",
                      dataset: $data.selectedDataset,
                      project: $data.project,
                      study: $data.selectedStudy,
                      onLoading: $options.updateLoadingStatus
                    }, null, _parent2, _scopeId));
                    _push2(`</div></div></div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  if ($options.currentTab.name == "Sample Info") {
                    _push2(`<div class="px-4 sm:px-6 pt-4"${_scopeId}><h1 class="text-xl font-bold text-gray-900"${_scopeId}> Sample Details </h1><div class="py-4 max-w-7xl mx-auto"${_scopeId}><div class="sm:flex sm:items-center sm:justify-between"${_scopeId}><h3 class="text-xl font-bold text-gray-900"${_scopeId}> Chemical composition (optional) </h3></div><p class="mt-2 pb-3 border-b border-gray-200 text-sm text-gray-500"${_scopeId}> Residual Complexity (RC) refers to the subtle but significant convolution of major and minor (&quot;hidden&quot;) chemical species in materials that originate from biochemical or synthetic reaction mixtures. Certain levels of these chemical species (molecules) usually remain present even after a number of purification steps, and this RC is to some degree conserved even in highly purified materials. In principle, RC affects all &quot;pure&quot; materials, including synthetic compound, whenever chromatographic or other purification steps are required prior to their biological evaluation. Please report the chemical composition (if known) of your sample. If unknown please report the molecules that you expect to be present in your sample with-out the composition. </p></div><div class="grid grid-cols-2 gap-2"${_scopeId}><div class="pr-2"${_scopeId}>`);
                    if ($data.selectedStudy.sample.molecules.length > 0) {
                      _push2(`<div class="flow-root"${_scopeId}><ul role="list" class="-mb-8"${_scopeId}><!--[-->`);
                      ssrRenderList($data.selectedStudy.sample.molecules, (molecule) => {
                        _push2(`<li${_scopeId}><div class="relative pb-8"${_scopeId}><span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"${_scopeId}></span><div class="relative flex items-start space-x-3"${_scopeId}>`);
                        if (molecule && molecule.pivot) {
                          _push2(`<div class="relative"${_scopeId}><div class="rounded-full border p-2 z-10 bg-gray-100 text-sm"${_scopeId}>${ssrInterpolate(molecule.pivot.percentage_composition)}% </div></div>`);
                        } else {
                          _push2(`<!---->`);
                        }
                        _push2(`<div class="min-w-0 flex-1"${_scopeId}><div${_scopeId}><div class="text-sm"${_scopeId}><a href="#" class="font-medium text-gray-900"${_scopeId}>${ssrInterpolate(molecule.standard_inchi)}</a></div></div><div class="mt-2 text-sm text-gray-700"${_scopeId}><div class="rounded-md border my-3 flex justify-center items-center"${_scopeId}><span${_scopeId}>${$options.getSVGString(
                          molecule
                        )}</span></div><button class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"${_scopeId}>`);
                        _push2(ssrRenderComponent(_component_TrashIcon, { class: "w-4 h-4 inline mr-1" }, null, _parent2, _scopeId));
                        _push2(`Delete </button><button class="inline-flex ml-2 items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"${_scopeId}>`);
                        _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 inline mr-1" }, null, _parent2, _scopeId));
                        _push2(`Edit </button></div></div></div></div></li>`);
                      });
                      _push2(`<!--]--></ul><div class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"${_scopeId}> Sample chemical composition </div></div>`);
                    } else {
                      _push2(`<div${_scopeId}><div class="text-center my-10 py-10"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"${_scopeId}><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"${_scopeId}></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No structures associated with the sample yet! </h3><p class="mt-1 text-sm text-gray-500"${_scopeId}> Get started by adding a new molecule. </p></div></div>`);
                    }
                    _push2(`</div><div class="pl-2"${_scopeId}><div class="sm:col-span-4"${_scopeId}><label for="email" class="block text-sm font-medium text-gray-700"${_scopeId}> SMILES </label><div class="mt-1 mb-2"${_scopeId}><input id="smiles"${ssrRenderAttr(
                      "value",
                      $data.smiles
                    )} name="smiles" type="text" class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md"${_scopeId}></div>`);
                    if ($data.smiles && $data.smiles != "") {
                      _push2(`<button class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2"${_scopeId}> Load Structure </button>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.errorMessage,
                      class: "mt-2"
                    }, null, _parent2, _scopeId));
                    _push2(`</div><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex justify-center"${_scopeId}><span class="px-2 bg-white text-sm text-gray-500"${_scopeId}> Or </span></div></div><span class="float-right text-xs cursor-pointer hover:text-blue-700 m-2"${_scopeId}><a href="https://docs.nmrxiv.org/docs/submission-guides/submission/editor" target="_blank"${_scopeId}>Need help? </a></span><div id="structureSearchEditor" class="w-full border my-4 rounded-md" style="${ssrRenderStyle({ "height": "400px" })}"${_scopeId}></div><div class="mt-1 mb-6"${_scopeId}><label for="email" class="block text-sm font-medium text-gray-700"${_scopeId}> Percentage composition (${ssrInterpolate($data.percentage)}%) </label>`);
                    _push2(ssrRenderComponent(_component_slider, {
                      modelValue: $data.percentage,
                      "onUpdate:modelValue": ($event) => $data.percentage = $event,
                      min: 0,
                      max: $options.getMax,
                      height: 10,
                      color: "#000",
                      "track-color": "#999"
                    }, null, _parent2, _scopeId));
                    _push2(`</div><button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}> ADD </button></div></div>`);
                    if ($data.selectedStudy) {
                      _push2(`<div${_scopeId}>`);
                      if ($data.selectedStudy.molecules && $data.selectedStudy.molecules.length == 0) {
                        _push2(`<div${_scopeId}><template${_scopeId}><button type="button" class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"${_scopeId}></path></svg><span class="mt-2 block text-sm font-medium text-gray-900"${_scopeId}> Create a new database </span></button></template></div>`);
                      } else {
                        _push2(`<!---->`);
                      }
                      if ($data.selectedStudy.molecules && $data.selectedStudy.molecules.length > 0) {
                        _push2(`<div${_scopeId}> Mols </div>`);
                      } else {
                        _push2(`<!---->`);
                      }
                      _push2(`</div>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(`</div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`</div></div></main></div></div></div></div></div>`);
                } else {
                  _push2(`<!---->`);
                }
                if ($options.currentStep.id == "03") {
                  _push2(`<div${_scopeId}>`);
                  _push2(ssrRenderComponent(_component_Validation, {
                    project: $data.project,
                    validation: _ctx.validation
                  }, null, _parent2, _scopeId));
                  _push2(`</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            }
            _push2(`</div>`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<div${_scopeId}><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId}></path></svg> Loading... </div>`);
        }
        if ($data.errorMessage != null && $data.errorMessage != "") {
          _push2(`<div class="rounded-md bg-red-50 p-4 mb-2"${_scopeId}><div class="flex"${_scopeId}><div class="flex-shrink-0"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_ExclamationTriangleIcon, {
            class: "h-5 w-5 text-red-400",
            "aria-hidden": "true"
          }, null, _parent2, _scopeId));
          _push2(`</div><div class="ml-3"${_scopeId}><h3 class="text-sm font-medium text-red-400"${_scopeId}> There were errors with your submission </h3><div class="mt-2 text-sm text-red-900"${_scopeId}>${$data.errorMessage}</div></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          $data.loadingStep ? (openBlock(), createBlock("div", {
            key: 0,
            class: "absolute inset-0 flex justify-center items-center z-10 bg-white rounded bg-opacity-70"
          }, [
            (openBlock(), createBlock("svg", {
              class: "animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline",
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
            $data.datasetsToImport && $data.datasetsToImport.length > 0 ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("br"),
              createTextVNode(" Processing " + toDisplayString($data.datasetsToImport.filter((f) => f.status == true).length + 1) + " of " + toDisplayString($data.datasetsToImport.length) + " spectra ", 1)
            ])) : createCommentVNode("", true)
          ])) : createCommentVNode("", true),
          !$data.loading ? (openBlock(), createBlock("div", { key: 1 }, [
            _ctx.drafts.length > 0 && !$data.currentDraft ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("div", null, [
                createVNode("ul", {
                  role: "list",
                  class: "-my-5 divide-y divide-gray-200"
                }, [
                  (openBlock(true), createBlock(Fragment, null, renderList(_ctx.drafts, (draft) => {
                    return openBlock(), createBlock("li", {
                      key: draft.id,
                      class: "py-4"
                    }, [
                      createVNode("div", { class: "flex items-center space-x-4" }, [
                        createVNode("div", { class: "flex-1 min-w-0" }, [
                          createVNode("p", { class: "text-lg font-large text-black truncate" }, [
                            createVNode("b", null, toDisplayString(draft.name), 1)
                          ]),
                          createVNode("p", { class: "text-sm font-medium text-gray-600 truncate pr-10" }, toDisplayString(draft.description), 1),
                          createVNode("p", { class: "text-sm font-medium text-gray-500 truncate" }, " ID: " + toDisplayString(draft.key), 1),
                          createVNode("p", { class: "text-sm text-gray-500 truncate" }, " Created at: " + toDisplayString(_ctx.formatDateTime(draft.created_at)), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode(_component_Link, {
                            href: _ctx.route("dashboard", {
                              action: "submission",
                              draft_id: draft.id
                            }),
                            class: "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-2"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Select ")
                            ]),
                            _: 2
                          }, 1032, ["href"])
                        ])
                      ])
                    ]);
                  }), 128))
                ])
              ]),
              createVNode("div", { class: "relative mt-6" }, [
                createVNode("div", {
                  class: "absolute inset-0 flex items-center",
                  "aria-hidden": "true"
                }, [
                  createVNode("div", { class: "w-full border-t border-gray-300" })
                ]),
                createVNode("div", { class: "relative flex justify-center" }, [
                  createVNode("span", { class: "px-2 bg-white text-sm text-gray-500" }, " Or ")
                ])
              ]),
              createVNode("div", { class: "my-6 justify-center items-center flex" }, [
                createVNode("button", {
                  class: "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition",
                  onClick: ($event) => $options.createNewDraft()
                }, " Create New ", 8, ["onClick"])
              ])
            ])) : (openBlock(), createBlock("div", { key: 1 }, [
              $data.showPrimer ? (openBlock(), createBlock("div", {
                key: 0,
                class: "-mx-6 -my-4"
              }, [
                createVNode("div", null, [
                  createVNode("section", { class: "h-1/2 overflow-hidden bg-white overflow-hidden py-12" }, [
                    createVNode("div", { class: "relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" }, [
                      (openBlock(), createBlock("svg", {
                        class: "absolute top-full right-full transform translate-x-1/3 -translate-y-1/4 lg:translate-x-1/2 xl:-translate-y-1/2",
                        width: "404",
                        height: "404",
                        fill: "none",
                        viewBox: "0 0 404 404",
                        role: "img",
                        "aria-labelledby": "svg-nmrxiv"
                      }, [
                        createVNode("title", { id: "svg-nmrxiv" }, "nmrxiv"),
                        createVNode("defs", null, [
                          createVNode("pattern", {
                            id: "ad119f34-7694-4c31-947f-5c9d249b21f3",
                            x: "0",
                            y: "0",
                            width: "20",
                            height: "20",
                            patternUnits: "userSpaceOnUse"
                          }, [
                            createVNode("rect", {
                              x: "0",
                              y: "0",
                              width: "4",
                              height: "4",
                              class: "text-gray-200",
                              fill: "currentColor"
                            })
                          ])
                        ]),
                        createVNode("rect", {
                          width: "404",
                          height: "404",
                          fill: "url(#ad119f34-7694-4c31-947f-5c9d249b21f3)"
                        })
                      ])),
                      createVNode("div", { class: "relative" }, [
                        createVNode("blockquote", null, [
                          createVNode("div", { class: "max-w-3xl mx-auto text-left text-xl leading-9 font-medium text-gray-900" }, [
                            createVNode("p", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest" }, " Basic Concepts "),
                            createVNode("p", { class: "mt-3 text-lg" }, [
                              createTextVNode(" In nmrXiv, data is organised as "),
                              createVNode("b", { class: "text-teal-700" }, "projects"),
                              createTextVNode(". Consider a project is equivalent to your publication with the corresponding NMR data to be uploaded to nmrXiv. A project can have multiple studies. "),
                              createVNode("b", { class: "text-teal-700" }, "A study corresponds to a group of NMR experiments of one sample"),
                              createTextVNode(", e.g. 1H, 13C, APT, COSY HSQC, HMBC, NOESY in Bruker Format or (another study) in another format such as JCAMP-DX / Varian. "),
                              createVNode("b", { class: "text-teal-700" }, "Each NMR measurement in a study is referred to as a dataset"),
                              createTextVNode(", e.g. 1H NMR or COSY (are each a dataset). ")
                            ]),
                            createVNode("img", {
                              src: "/img/primer.png",
                              alt: ""
                            }),
                            createVNode("p", { class: "text-md text-center leading-5 mb-3" }, [
                              createVNode("small", null, [
                                createTextVNode("nmrXiv allows you to upload NMR raw data from any NMR instrument. We can currently auto-detect Bruker/Varian/JOEL formats & JCAMP files and will support more raw & processed file formats soon. Once you upload your raw or processed NMR data, nmrXiv will auto-generate the studies and datasets for you based on the uploaded folder structure. "),
                                createVNode("i", null, [
                                  createVNode("a", {
                                    class: "text-xs",
                                    target: "_blank",
                                    href: "https://docs.nmrxiv.org/submission-guides/submission/folder-structure"
                                  }, "More info here")
                                ])
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ])) : (openBlock(), createBlock("div", { key: 1 }, [
                $options.currentStep ? (openBlock(), createBlock("div", { key: 0 }, [
                  $options.currentStep.id == "01" ? (openBlock(), createBlock("div", {
                    key: 0,
                    id: "submission-dropzone"
                  }, [
                    $data.currentDraft ? (openBlock(), createBlock("div", { key: 0 }, [
                      createVNode("div", {
                        id: "tour-step-project-name",
                        class: "mb-3"
                      }, [
                        createVNode("label", {
                          for: "project-name",
                          class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, " Project Name "),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("input", {
                            "onUpdate:modelValue": ($event) => $data.draftForm.name = $event,
                            type: "text",
                            name: "project-name",
                            class: "block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelText, $data.draftForm.name]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.draftForm.errors.name,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", {
                        id: "tour-step-project-desc",
                        class: "mb-3"
                      }, [
                        createVNode("label", {
                          for: "description",
                          class: "block text-sm font-medium text-gray-700"
                        }, [
                          createVNode("span", {
                            onClick: ($event) => $data.draftForm.description = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore"
                          }, "Project Description (Optional)", 8, ["onClick"])
                        ]),
                        createVNode("div", { class: "mt-1" }, [
                          withDirectives(createVNode("textarea", {
                            id: "description",
                            "onUpdate:modelValue": ($event) => $data.draftForm.description = $event,
                            name: "description",
                            placeholder: "Describe this project",
                            rows: "3",
                            class: "block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [vModelText, $data.draftForm.description]
                          ])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.draftForm.errors.description,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", {
                        id: "tour-step-project-keywords",
                        class: "mb-3"
                      }, [
                        createVNode("label", {
                          for: "description",
                          class: "block text-sm font-medium text-gray-700"
                        }, " Keywords (Optional) "),
                        createVNode("div", null, [
                          createVNode(_component_vue_tags_input, {
                            modelValue: $data.draftForm.tag,
                            "onUpdate:modelValue": ($event) => $data.draftForm.tag = $event,
                            placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                            separators: [";", ","],
                            "max-width": "100%",
                            tags: $data.draftForm.tags,
                            onTagsChanged: (newTags) => $data.draftForm.tags = newTags
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "tags", "onTagsChanged"])
                        ]),
                        createVNode(_component_jet_input_error, {
                          message: $data.draftForm.errors.tags,
                          class: "mt-2"
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode("div", null, [
                          createVNode("small", { class: "cursor-pointer" }, "Draft ID: " + toDisplayString($data.currentDraft.key), 1),
                          createVNode("div", { id: "tour-step-upload-spectra" }, [
                            createVNode(_component_file_system_browser, {
                              ref: "fsbRef",
                              readonly: false,
                              draft: $data.currentDraft,
                              onLoading: $options.updateLoadingStatus
                            }, null, 8, ["draft", "onLoading"])
                          ]),
                          createVNode(_component_jet_input_error, {
                            message: $data.draftForm.errors.studies,
                            class: "mt-2"
                          }, null, 8, ["message"])
                        ])
                      ])
                    ])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true),
                  $options.currentStep.id == "02" ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "-mx-6 -my-4"
                  }, [
                    createVNode("div", { style: { "height": "80vh" } }, [
                      createVNode("div", { class: "flex-1 flex md:overflow-hidden" }, [
                        createVNode("nav", {
                          "aria-label": "Sections",
                          class: "hidden flex-shrink-0 w-80 bg-white border-r border-blue-gray-200 md:flex md:flex-col"
                        }, [
                          createVNode("div", { class: "border-gray-200 px-4 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 tracking-wider flex-shrink-0 border-b border-blue-gray-200" }, [
                            createTextVNode(toDisplayString(_ctx.pluralize(
                              "STUDY",
                              _ctx.studies.length
                            )) + " (" + toDisplayString(_ctx.studies.length) + ") ", 1),
                            createVNode("div", {
                              class: "float-right cursor-pointer tooltip",
                              onClick: ($event) => $options.autoImport()
                            }, [
                              createVNode(_component_ArrowDownOnSquareStackIcon, { class: "w-5 h-5 mr-1 text-gray-600 hover:text-gray-500" }),
                              createVNode("span", { class: "bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom" }, "Click to auto import spectra")
                            ], 8, ["onClick"])
                          ]),
                          createVNode("div", {
                            id: "tour-step-side-panel-studies",
                            style: { "height": "74vh", "overflow": "scroll !important" }
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(_ctx.studies, (study, $index) => {
                              return openBlock(), createBlock("a", {
                                key: study.slug,
                                class: [
                                  $data.selectedStudy.id == study.id ? "bg-gray-800 text-white" : "hover:bg-gray-200 hover:bg-opacity-50",
                                  "cursor-pointer flex p-4 pr-5 border-b border-blue-gray-200"
                                ]
                              }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  class: "flex-shrink-0 -mt-0.5 h-6 w-6 text-blue-gray-400",
                                  viewBox: "0 0 20 20",
                                  fill: "currentColor"
                                }, [
                                  createVNode("path", { d: "M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" })
                                ])),
                                createVNode("div", { class: "ml-3 text-sm w-full" }, [
                                  createVNode("p", { class: "font-medium text-blue-gray-900 pr-4" }, [
                                    createVNode("a", {
                                      class: "hover:font-bold",
                                      onClick: ($event) => $options.selectStudy(
                                        study,
                                        $index
                                      )
                                    }, toDisplayString(study.name), 9, ["onClick"]),
                                    study.sample.molecules.length > 0 ? (openBlock(), createBlock("span", {
                                      key: 0,
                                      class: "float-right"
                                    }, [
                                      createVNode("img", {
                                        class: "flex-shrink-0 -mt-0.5 mr-2 h-6 w-6 text-blue-gray-400",
                                        src: "https://upload.wikimedia.org/wikipedia/sco/3/35/ChEBI_logo.png",
                                        alt: ""
                                      })
                                    ])) : createCommentVNode("", true)
                                  ]),
                                  createVNode("div", { class: "mt-1 text-blue-gray-500" }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList(study.datasets, (ds, $dsIndex) => {
                                      return openBlock(), createBlock("span", {
                                        key: ds.id
                                      }, [
                                        createVNode("div", {
                                          class: [
                                            study.has_nmrium || ds.has_nmrium ? "bg-green-100 text-gray-800" : "bg-gray-100 text-gray-800",
                                            "w-64 inline-flex truncate break-words items-center px-3 py-0.5 rounded-full text-xs font-medium mr-1 hover:bg-teal-700"
                                          ],
                                          onClick: ($event) => $options.selectStudy(
                                            study,
                                            $index,
                                            $dsIndex
                                          )
                                        }, [
                                          createVNode("a", null, [
                                            createTextVNode(toDisplayString(ds.name) + " ", 1),
                                            ds.type ? (openBlock(), createBlock("span", { key: 0 }, "(" + toDisplayString(ds.type) + ")", 1)) : createCommentVNode("", true)
                                          ])
                                        ], 10, ["onClick"])
                                      ]);
                                    }), 128))
                                  ])
                                ])
                              ], 2);
                            }), 128))
                          ])
                        ]),
                        createVNode("div", {
                          style: { "height": "80vh", "overflow": "scroll !important" },
                          class: "flex-1 xl:overflow-y-auto"
                        }, [
                          createVNode("div", { class: "mx-auto flex flex-col md:px-0 xl:px-0" }, [
                            createVNode("main", { class: "flex-1" }, [
                              createVNode("div", { class: "relative mx-auto md:px-0 xl:px-0" }, [
                                createVNode("div", { class: "pt-5 pb-16" }, [
                                  createVNode("div", { class: "px-3 sm:px-3" }, [
                                    createVNode("h1", { class: "text-3xl font-extrabold text-gray-900" }, toDisplayString($data.selectedStudy.name), 1)
                                  ]),
                                  createVNode("div", { class: "lg:hidden px-4 sm:px-6" }, [
                                    createVNode("label", {
                                      for: "selected-tab",
                                      class: "sr-only"
                                    }, "Select a tab"),
                                    createVNode("select", {
                                      id: "selected-tab",
                                      name: "selected-tab",
                                      class: "mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md"
                                    }, [
                                      (openBlock(true), createBlock(Fragment, null, renderList($data.tabs, (tab) => {
                                        return openBlock(), createBlock("option", {
                                          key: tab.name,
                                          selected: tab.current
                                        }, toDisplayString(tab.name), 9, ["selected"]);
                                      }), 128))
                                    ])
                                  ]),
                                  createVNode("div", { class: "hidden lg:block px-3 sm:px-3" }, [
                                    createVNode("div", { class: "border-b border-gray-200" }, [
                                      createVNode("nav", { class: "-mb-px flex space-x-8" }, [
                                        (openBlock(true), createBlock(Fragment, null, renderList($data.tabs, (tab) => {
                                          return openBlock(), createBlock("a", {
                                            key: tab.name,
                                            class: [
                                              tab.current ? "border-teal-500 text-teal-600" : "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700",
                                              "cursor-pointer whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                            ],
                                            onClick: ($event) => $options.selectTab(
                                              tab
                                            )
                                          }, toDisplayString(tab.name), 11, ["onClick"]);
                                        }), 128))
                                      ])
                                    ])
                                  ]),
                                  $options.currentTab.name == "Metadata" ? (openBlock(), createBlock("div", {
                                    key: 0,
                                    class: "px-4 sm:px-6"
                                  }, [
                                    createVNode("div", { class: "pt-4 xl:col-span-2" }, [
                                      createVNode("div", { class: "mb-3" }, [
                                        createVNode("label", {
                                          for: "study-name",
                                          class: "block text-sm font-medium text-gray-700"
                                        }, [
                                          createTextVNode(" Study Name "),
                                          createVNode("span", {
                                            class: "float-right border rounded-full px-2 py-1 mb-2",
                                            onClick: ($event) => $options.autoGenerateDescription()
                                          }, " Auto generate ", 8, ["onClick"])
                                        ]),
                                        createVNode("div", { class: "mt-1" }, [
                                          withDirectives(createVNode("input", {
                                            "onUpdate:modelValue": ($event) => $data.studyForm.name = $event,
                                            type: "text",
                                            name: "study-name",
                                            class: "block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border-gray-300 rounded-md"
                                          }, null, 8, ["onUpdate:modelValue"]), [
                                            [
                                              vModelText,
                                              $data.studyForm.name
                                            ]
                                          ]),
                                          createVNode(_component_jet_input_error, {
                                            message: $data.studyForm.errors.name,
                                            class: "mt-2"
                                          }, null, 8, ["message"])
                                        ])
                                      ]),
                                      createVNode("div", { class: "mb-3" }, [
                                        createVNode("label", {
                                          for: "description",
                                          class: "block text-sm font-medium text-gray-700"
                                        }, " Study Description "),
                                        createVNode("div", { class: "mt-1" }, [
                                          withDirectives(createVNode("textarea", {
                                            id: "study-description",
                                            "onUpdate:modelValue": ($event) => $data.studyForm.description = $event,
                                            name: "study-description",
                                            rows: "3",
                                            class: "block w-full shadow-sm focus:ring-teal-500 focus:border-teal-500 sm:text-sm border border-gray-300 rounded-md"
                                          }, null, 8, ["onUpdate:modelValue"]), [
                                            [
                                              vModelText,
                                              $data.studyForm.description
                                            ]
                                          ]),
                                          createVNode(_component_jet_input_error, {
                                            message: $data.studyForm.errors.description,
                                            class: "mt-2"
                                          }, null, 8, ["message"])
                                        ])
                                      ]),
                                      createVNode("div", { class: "mb-3" }, [
                                        createVNode("label", {
                                          for: "description",
                                          class: "block text-sm font-medium text-gray-700"
                                        }, " Keywords "),
                                        createVNode("div", null, [
                                          createVNode(_component_vue_tags_input, {
                                            modelValue: $data.studyForm.tag,
                                            "onUpdate:modelValue": ($event) => $data.studyForm.tag = $event,
                                            placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                                            separators: [
                                              ";",
                                              ","
                                            ],
                                            "max-width": "100%",
                                            tags: $data.studyForm.tags,
                                            onTagsChanged: (newTags) => $data.studyForm.tags = newTags
                                          }, null, 8, ["modelValue", "onUpdate:modelValue", "tags", "onTagsChanged"]),
                                          createVNode(_component_jet_input_error, {
                                            message: $data.studyForm.errors.tags,
                                            class: "mt-2"
                                          }, null, 8, ["message"])
                                        ])
                                      ]),
                                      createVNode("div", { class: "mb-6" }, [
                                        createVNode("button", {
                                          type: "button",
                                          class: "inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                                          onClick: $options.saveStudyDetails
                                        }, " SAVE ", 8, ["onClick"])
                                      ])
                                    ])
                                  ])) : createCommentVNode("", true),
                                  $options.currentTab.name == "Experiments (Spectra)" ? (openBlock(), createBlock("div", {
                                    key: 1,
                                    id: "tour-step-nmrium",
                                    class: "px-4 sm:px-6 md:px-0"
                                  }, [
                                    createVNode("div", { class: "p-3" }, [
                                      createVNode("div", { class: "my-3" }, [
                                        createVNode(_component_SpectraEditor, {
                                          ref: "spectraEditorREF",
                                          dataset: $data.selectedDataset,
                                          project: $data.project,
                                          study: $data.selectedStudy,
                                          onLoading: $options.updateLoadingStatus
                                        }, null, 8, ["dataset", "project", "study", "onLoading"])
                                      ])
                                    ])
                                  ])) : createCommentVNode("", true),
                                  $options.currentTab.name == "Sample Info" ? (openBlock(), createBlock("div", {
                                    key: 2,
                                    class: "px-4 sm:px-6 pt-4"
                                  }, [
                                    createVNode("h1", { class: "text-xl font-bold text-gray-900" }, " Sample Details "),
                                    createVNode("div", { class: "py-4 max-w-7xl mx-auto" }, [
                                      createVNode("div", { class: "sm:flex sm:items-center sm:justify-between" }, [
                                        createVNode("h3", { class: "text-xl font-bold text-gray-900" }, " Chemical composition (optional) ")
                                      ]),
                                      createVNode("p", { class: "mt-2 pb-3 border-b border-gray-200 text-sm text-gray-500" }, ' Residual Complexity (RC) refers to the subtle but significant convolution of major and minor ("hidden") chemical species in materials that originate from biochemical or synthetic reaction mixtures. Certain levels of these chemical species (molecules) usually remain present even after a number of purification steps, and this RC is to some degree conserved even in highly purified materials. In principle, RC affects all "pure" materials, including synthetic compound, whenever chromatographic or other purification steps are required prior to their biological evaluation. Please report the chemical composition (if known) of your sample. If unknown please report the molecules that you expect to be present in your sample with-out the composition. ')
                                    ]),
                                    createVNode("div", { class: "grid grid-cols-2 gap-2" }, [
                                      createVNode("div", { class: "pr-2" }, [
                                        $data.selectedStudy.sample.molecules.length > 0 ? (openBlock(), createBlock("div", {
                                          key: 0,
                                          class: "flow-root"
                                        }, [
                                          createVNode("ul", {
                                            role: "list",
                                            class: "-mb-8"
                                          }, [
                                            (openBlock(true), createBlock(Fragment, null, renderList($data.selectedStudy.sample.molecules, (molecule) => {
                                              return openBlock(), createBlock("li", {
                                                key: molecule.standard_inchi
                                              }, [
                                                createVNode("div", { class: "relative pb-8" }, [
                                                  createVNode("span", {
                                                    class: "absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200",
                                                    "aria-hidden": "true"
                                                  }),
                                                  createVNode("div", { class: "relative flex items-start space-x-3" }, [
                                                    molecule && molecule.pivot ? (openBlock(), createBlock("div", {
                                                      key: 0,
                                                      class: "relative"
                                                    }, [
                                                      createVNode("div", { class: "rounded-full border p-2 z-10 bg-gray-100 text-sm" }, toDisplayString(molecule.pivot.percentage_composition) + "% ", 1)
                                                    ])) : createCommentVNode("", true),
                                                    createVNode("div", { class: "min-w-0 flex-1" }, [
                                                      createVNode("div", null, [
                                                        createVNode("div", { class: "text-sm" }, [
                                                          createVNode("a", {
                                                            href: "#",
                                                            class: "font-medium text-gray-900"
                                                          }, toDisplayString(molecule.standard_inchi), 1)
                                                        ])
                                                      ]),
                                                      createVNode("div", { class: "mt-2 text-sm text-gray-700" }, [
                                                        createVNode("div", { class: "rounded-md border my-3 flex justify-center items-center" }, [
                                                          createVNode("span", {
                                                            innerHTML: $options.getSVGString(
                                                              molecule
                                                            )
                                                          }, null, 8, ["innerHTML"])
                                                        ]),
                                                        createVNode("button", {
                                                          class: "inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",
                                                          onClick: ($event) => $options.deleteMolecule(
                                                            molecule
                                                          )
                                                        }, [
                                                          createVNode(_component_TrashIcon, { class: "w-4 h-4 inline mr-1" }),
                                                          createTextVNode("Delete ")
                                                        ], 8, ["onClick"]),
                                                        createVNode("button", {
                                                          class: "inline-flex ml-2 items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",
                                                          onClick: ($event) => $options.editMolecule(
                                                            molecule
                                                          )
                                                        }, [
                                                          createVNode(_component_PencilIcon, { class: "w-4 h-4 inline mr-1" }),
                                                          createTextVNode("Edit ")
                                                        ], 8, ["onClick"])
                                                      ])
                                                    ])
                                                  ])
                                                ])
                                              ]);
                                            }), 128))
                                          ]),
                                          createVNode("div", { class: "rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center" }, " Sample chemical composition ")
                                        ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                          createVNode("div", { class: "text-center my-10 py-10" }, [
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
                                            createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No structures associated with the sample yet! "),
                                            createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Get started by adding a new molecule. ")
                                          ])
                                        ]))
                                      ]),
                                      createVNode("div", { class: "pl-2" }, [
                                        createVNode("div", { class: "sm:col-span-4" }, [
                                          createVNode("label", {
                                            for: "email",
                                            class: "block text-sm font-medium text-gray-700"
                                          }, " SMILES "),
                                          createVNode("div", { class: "mt-1 mb-2" }, [
                                            withDirectives(createVNode("input", {
                                              id: "smiles",
                                              "onUpdate:modelValue": ($event) => $data.smiles = $event,
                                              name: "smiles",
                                              type: "text",
                                              class: "shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-md",
                                              onBlur: $options.loadSmiles
                                            }, null, 40, ["onUpdate:modelValue", "onBlur"]), [
                                              [
                                                vModelText,
                                                $data.smiles
                                              ]
                                            ])
                                          ]),
                                          $data.smiles && $data.smiles != "" ? (openBlock(), createBlock("button", {
                                            key: 0,
                                            class: "inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2",
                                            onClick: $options.loadSmiles
                                          }, " Load Structure ", 8, ["onClick"])) : createCommentVNode("", true),
                                          createVNode(_component_jet_input_error, {
                                            message: $data.errorMessage,
                                            class: "mt-2"
                                          }, null, 8, ["message"])
                                        ]),
                                        createVNode("div", { class: "relative" }, [
                                          createVNode("div", {
                                            class: "absolute inset-0 flex items-center",
                                            "aria-hidden": "true"
                                          }, [
                                            createVNode("div", { class: "w-full border-t border-gray-300" })
                                          ]),
                                          createVNode("div", { class: "relative flex justify-center" }, [
                                            createVNode("span", { class: "px-2 bg-white text-sm text-gray-500" }, " Or ")
                                          ])
                                        ]),
                                        createVNode("span", { class: "float-right text-xs cursor-pointer hover:text-blue-700 m-2" }, [
                                          createVNode("a", {
                                            href: "https://docs.nmrxiv.org/docs/submission-guides/submission/editor",
                                            target: "_blank"
                                          }, "Need help? ")
                                        ]),
                                        createVNode("div", {
                                          id: "structureSearchEditor",
                                          class: "w-full border my-4 rounded-md",
                                          style: { "height": "400px" }
                                        }),
                                        createVNode("div", { class: "mt-1 mb-6" }, [
                                          createVNode("label", {
                                            for: "email",
                                            class: "block text-sm font-medium text-gray-700"
                                          }, " Percentage composition (" + toDisplayString($data.percentage) + "%) ", 1),
                                          createVNode(_component_slider, {
                                            modelValue: $data.percentage,
                                            "onUpdate:modelValue": ($event) => $data.percentage = $event,
                                            min: 0,
                                            max: $options.getMax,
                                            height: 10,
                                            color: "#000",
                                            "track-color": "#999"
                                          }, null, 8, ["modelValue", "onUpdate:modelValue", "max"])
                                        ]),
                                        createVNode("button", {
                                          type: "button",
                                          class: "inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                                          onClick: $options.saveMolecule
                                        }, " ADD ", 8, ["onClick"])
                                      ])
                                    ]),
                                    $data.selectedStudy ? (openBlock(), createBlock("div", { key: 0 }, [
                                      $data.selectedStudy.molecules && $data.selectedStudy.molecules.length == 0 ? (openBlock(), createBlock("div", { key: 0 }, [
                                        createVNode("template", null, [
                                          createVNode("button", {
                                            type: "button",
                                            class: "relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                          }, [
                                            (openBlock(), createBlock("svg", {
                                              class: "mx-auto h-12 w-12 text-gray-400",
                                              xmlns: "http://www.w3.org/2000/svg",
                                              stroke: "currentColor",
                                              fill: "none",
                                              viewBox: "0 0 48 48",
                                              "aria-hidden": "true"
                                            }, [
                                              createVNode("path", {
                                                "stroke-linecap": "round",
                                                "stroke-linejoin": "round",
                                                "stroke-width": "2",
                                                d: "M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"
                                              })
                                            ])),
                                            createVNode("span", { class: "mt-2 block text-sm font-medium text-gray-900" }, " Create a new database ")
                                          ])
                                        ])
                                      ])) : createCommentVNode("", true),
                                      $data.selectedStudy.molecules && $data.selectedStudy.molecules.length > 0 ? (openBlock(), createBlock("div", { key: 1 }, " Mols ")) : createCommentVNode("", true)
                                    ])) : createCommentVNode("", true)
                                  ])) : createCommentVNode("", true)
                                ])
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  $options.currentStep.id == "03" ? (openBlock(), createBlock("div", { key: 2 }, [
                    createVNode(_component_Validation, {
                      project: $data.project,
                      validation: _ctx.validation
                    }, null, 8, ["project", "validation"])
                  ])) : createCommentVNode("", true)
                ])) : createCommentVNode("", true)
              ]))
            ]))
          ])) : (openBlock(), createBlock("div", { key: 2 }, [
            (openBlock(), createBlock("svg", {
              class: "animate-spin -ml-1 mr-3 h-5 w-5 text-dark flex-inline inline",
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
          ])),
          $data.errorMessage != null && $data.errorMessage != "" ? (openBlock(), createBlock("div", {
            key: 3,
            class: "rounded-md bg-red-50 p-4 mb-2"
          }, [
            createVNode("div", { class: "flex" }, [
              createVNode("div", { class: "flex-shrink-0" }, [
                createVNode(_component_ExclamationTriangleIcon, {
                  class: "h-5 w-5 text-red-400",
                  "aria-hidden": "true"
                })
              ]),
              createVNode("div", { class: "ml-3" }, [
                createVNode("h3", { class: "text-sm font-medium text-red-400" }, " There were errors with your submission "),
                createVNode("div", {
                  class: "mt-2 text-sm text-red-900",
                  innerHTML: $data.errorMessage
                }, null, 8, ["innerHTML"])
              ])
            ])
          ])) : createCommentVNode("", true)
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="flex"${_scopeId}>`);
        if ($data.showPrimer && $data.currentDraft) {
          _push2(`<div class="w-full"${_scopeId}><div class="float-left"${_scopeId}><div class="relative mt-2 flex items-start"${_scopeId}><div class="flex h-5 items-center"${_scopeId}><input id="comments" aria-describedby="comments-description" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"${_scopeId}></div><div class="ml-3 text-sm"${_scopeId}><label for="comments" class="font-medium text-gray-700"${_scopeId}>Don&#39;t show this again</label></div></div></div>`);
          _push2(ssrRenderComponent(_component_jet_button, {
            class: ["ml-2 float-right", {
              "opacity-25": $data.createDatasetForm.processing
            }],
            onClick: ($event) => $options.skipPrimer()
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Proceed `);
              } else {
                return [
                  createTextVNode(" Proceed ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<div class="w-full"${_scopeId}>`);
          if (!$options.currentStep) {
            _push2(`<span${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Link, {
              class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
              href: $data.returnUrl
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Cancel `);
                } else {
                  return [
                    createTextVNode(" Cancel ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span>`);
          } else {
            _push2(`<span${_scopeId}>`);
            if ($options.currentStep.id == "01") {
              _push2(`<span${_scopeId}>`);
              if (_ctx.drafts.length > 0) {
                _push2(ssrRenderComponent(_component_jet_secondary_button, {
                  class: ["ml-2 float-left", {
                    "opacity-25": $data.createDatasetForm.processing
                  }],
                  disabled: $data.createDatasetForm.processing,
                  onClick: $options.openSelectDraftsView
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(` Drafts `);
                    } else {
                      return [
                        createTextVNode(" Drafts ")
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(_component_Link, {
                class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                href: $data.returnUrl
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Cancel `);
                  } else {
                    return [
                      createTextVNode(" Cancel ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_component_jet_button, {
                id: "tour-step-proceed-from-step-1",
                class: ["ml-2", {
                  "opacity-25": $data.createDatasetForm.processing
                }],
                disabled: $data.createDatasetForm.processing || $data.loading || $data.loadingStep,
                onClick: $options.process
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    if ($data.loadingStep) {
                      _push3(`<span${_scopeId2}><svg class="animate-spin -ml-1 mr-3 h-2 w-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId2}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId2}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId2}></path></svg></span>`);
                    } else {
                      _push3(`<!---->`);
                    }
                    _push3(` Proceed `);
                  } else {
                    return [
                      $data.loadingStep ? (openBlock(), createBlock("span", { key: 0 }, [
                        (openBlock(), createBlock("svg", {
                          class: "animate-spin -ml-1 mr-3 h-2 w-2 text-white",
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
                        ]))
                      ])) : createCommentVNode("", true),
                      createTextVNode(" Proceed ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</span>`);
            } else if ($options.currentStep.id == "02") {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_jet_button, {
                class: ["ml-2 float-left", {
                  "opacity-25": $data.createDatasetForm.processing
                }],
                disabled: $data.createDatasetForm.processing,
                onClick: ($event) => $options.selectStep(1)
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Back `);
                  } else {
                    return [
                      createTextVNode(" Back ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_component_Link, {
                href: $data.returnUrl,
                class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Cancel `);
                  } else {
                    return [
                      createTextVNode(" Cancel ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_component_jet_button, {
                id: "tour-step-proceed-from-step-2",
                class: ["ml-2", {
                  "opacity-25": $data.createDatasetForm.processing
                }],
                disabled: $data.createDatasetForm.processing || $data.loading || $data.loadingStep,
                onClick: $options.closeDraft
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    if ($data.loadingStep) {
                      _push3(`<span${_scopeId2}><svg class="animate-spin -ml-1 mr-3 h-2 w-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId2}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId2}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId2}></path></svg></span>`);
                    } else {
                      _push3(`<!---->`);
                    }
                    _push3(` Proceed `);
                  } else {
                    return [
                      $data.loadingStep ? (openBlock(), createBlock("span", { key: 0 }, [
                        (openBlock(), createBlock("svg", {
                          class: "animate-spin -ml-1 mr-3 h-2 w-2 text-white",
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
                        ]))
                      ])) : createCommentVNode("", true),
                      createTextVNode(" Proceed ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</span>`);
            } else if ($options.currentStep.id == "03") {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_Link, {
                id: "tour-step-finish",
                class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                href: _ctx.route("dashboard")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Finish `);
                  } else {
                    return [
                      createTextVNode(" Finish ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</span>`);
          }
          _push2(`</div>`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "flex" }, [
            $data.showPrimer && $data.currentDraft ? (openBlock(), createBlock("div", {
              key: 0,
              class: "w-full"
            }, [
              createVNode("div", { class: "float-left" }, [
                createVNode("div", {
                  class: "relative mt-2 flex items-start",
                  onChange: ($event) => $options.hidePrimer()
                }, [
                  createVNode("div", { class: "flex h-5 items-center" }, [
                    createVNode("input", {
                      id: "comments",
                      "aria-describedby": "comments-description",
                      name: "comments",
                      type: "checkbox",
                      class: "h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    })
                  ]),
                  createVNode("div", { class: "ml-3 text-sm" }, [
                    createVNode("label", {
                      for: "comments",
                      class: "font-medium text-gray-700"
                    }, "Don't show this again")
                  ])
                ], 40, ["onChange"])
              ]),
              createVNode(_component_jet_button, {
                class: ["ml-2 float-right", {
                  "opacity-25": $data.createDatasetForm.processing
                }],
                onClick: ($event) => $options.skipPrimer()
              }, {
                default: withCtx(() => [
                  createTextVNode(" Proceed ")
                ]),
                _: 1
              }, 8, ["class", "onClick"])
            ])) : (openBlock(), createBlock("div", {
              key: 1,
              class: "w-full"
            }, [
              !$options.currentStep ? (openBlock(), createBlock("span", { key: 0 }, [
                createVNode(_component_Link, {
                  class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                  href: $data.returnUrl
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["href"])
              ])) : (openBlock(), createBlock("span", { key: 1 }, [
                $options.currentStep.id == "01" ? (openBlock(), createBlock("span", { key: 0 }, [
                  _ctx.drafts.length > 0 ? (openBlock(), createBlock(_component_jet_secondary_button, {
                    key: 0,
                    class: ["ml-2 float-left", {
                      "opacity-25": $data.createDatasetForm.processing
                    }],
                    disabled: $data.createDatasetForm.processing,
                    onClick: $options.openSelectDraftsView
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Drafts ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled", "onClick"])) : createCommentVNode("", true),
                  createVNode(_component_Link, {
                    class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                    href: $data.returnUrl
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Cancel ")
                    ]),
                    _: 1
                  }, 8, ["href"]),
                  createVNode(_component_jet_button, {
                    id: "tour-step-proceed-from-step-1",
                    class: ["ml-2", {
                      "opacity-25": $data.createDatasetForm.processing
                    }],
                    disabled: $data.createDatasetForm.processing || $data.loading || $data.loadingStep,
                    onClick: $options.process
                  }, {
                    default: withCtx(() => [
                      $data.loadingStep ? (openBlock(), createBlock("span", { key: 0 }, [
                        (openBlock(), createBlock("svg", {
                          class: "animate-spin -ml-1 mr-3 h-2 w-2 text-white",
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
                        ]))
                      ])) : createCommentVNode("", true),
                      createTextVNode(" Proceed ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled", "onClick"])
                ])) : $options.currentStep.id == "02" ? (openBlock(), createBlock("span", { key: 1 }, [
                  createVNode(_component_jet_button, {
                    class: ["ml-2 float-left", {
                      "opacity-25": $data.createDatasetForm.processing
                    }],
                    disabled: $data.createDatasetForm.processing,
                    onClick: ($event) => $options.selectStep(1)
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Back ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled", "onClick"]),
                  createVNode(_component_Link, {
                    href: $data.returnUrl,
                    class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Cancel ")
                    ]),
                    _: 1
                  }, 8, ["href"]),
                  createVNode(_component_jet_button, {
                    id: "tour-step-proceed-from-step-2",
                    class: ["ml-2", {
                      "opacity-25": $data.createDatasetForm.processing
                    }],
                    disabled: $data.createDatasetForm.processing || $data.loading || $data.loadingStep,
                    onClick: $options.closeDraft
                  }, {
                    default: withCtx(() => [
                      $data.loadingStep ? (openBlock(), createBlock("span", { key: 0 }, [
                        (openBlock(), createBlock("svg", {
                          class: "animate-spin -ml-1 mr-3 h-2 w-2 text-white",
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
                        ]))
                      ])) : createCommentVNode("", true),
                      createTextVNode(" Proceed ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled", "onClick"])
                ])) : $options.currentStep.id == "03" ? (openBlock(), createBlock("span", { key: 2 }, [
                  createVNode(_component_Link, {
                    id: "tour-step-finish",
                    class: "inline-flex items-center px-2.5 py-1 border border-gray-300 shadow-sm text-md font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                    href: _ctx.route("dashboard")
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Finish ")
                    ]),
                    _: 1
                  }, 8, ["href"])
                ])) : createCommentVNode("", true)
              ]))
            ]))
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Submission.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const Submission = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$1 = {
  components: {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    XMarkIcon,
    JetButton,
    JetSecondaryButton,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    EllipsisVerticalIcon: EllipsisVerticalIcon$1
  },
  props: [],
  data() {
    return {
      open: false,
      info: {},
      notifications: this.$page.props.auth.user.notifications,
      notificationForm: this.$inertia.form({
        _method: "POST",
        id: "",
        title: "",
        message: "",
        user_id: ""
      }),
      markAllAsReadForm: this.$inertia.form({
        _method: "POST"
      })
    };
  },
  methods: {
    toggleShowNotificationDialog(notifications) {
      this.info.title = "";
      this.info.body = "";
      this.open = !this.open;
    },
    markNotificationAsRead(notification) {
      this.notificationForm.user_id = notification.notifiable_id;
      this.notificationForm.id = notification.id;
      this.notificationForm.title = notification.data["title"];
      this.notificationForm.message = notification.data["message"];
      this.notificationForm.post(
        route(
          "users.markNotificationAsRead",
          notification.notifiable_id
        ),
        {
          preserveScroll: true,
          onSuccess: () => {
            this.notificationForm.reset();
            this.notifications = this.$page.props.auth.user.notifications;
          },
          onError: (err) => console.error(err)
        }
      );
    },
    markAllNotificationAsRead() {
      this.markAllAsReadForm.post(
        route("users.markAllNotificationAsRead"),
        {
          preserveScroll: true,
          onSuccess: () => {
            this.markAllAsReadForm.reset();
            this.notifications = this.$page.props.auth.user.notifications;
          },
          onError: (err) => console.error(err)
        }
      );
    },
    calculateDaysDifference(createdDate) {
      var createdDateObj = new Date(createdDate);
      var currentDate = /* @__PURE__ */ new Date();
      var differenceInMs = currentDate - createdDateObj;
      var differenceInDays = Math.floor(
        differenceInMs / (1e3 * 60 * 60 * 24)
      );
      if (differenceInDays === 0) {
        return "today";
      } else if (differenceInDays === 1) {
        return "yesterday";
      } else {
        return `${differenceInDays} days ago`;
      }
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_TransitionRoot = resolveComponent("TransitionRoot");
  const _component_Dialog = resolveComponent("Dialog");
  const _component_TransitionChild = resolveComponent("TransitionChild");
  const _component_DialogPanel = resolveComponent("DialogPanel");
  const _component_XMarkIcon = resolveComponent("XMarkIcon");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_button = resolveComponent("jet-button");
  _push(ssrRenderComponent(_component_TransitionRoot, mergeProps({
    as: "template",
    show: $data.open
  }, _attrs), {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_Dialog, {
          as: "div",
          class: "fixed inset-0 z-50",
          onClose: ($event) => $data.open = false
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
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
                    _push4(`<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"${_scopeId3}></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<div class="fixed inset-0 overflow-hidden"${_scopeId2}><div class="absolute inset-0 overflow-hidden"${_scopeId2}><div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10"${_scopeId2}>`);
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
                    _push4(ssrRenderComponent(_component_DialogPanel, { class: "pointer-events-auto relative w-screen max-w-md" }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(ssrRenderComponent(_component_TransitionChild, {
                            as: "template",
                            enter: "ease-in-out duration-500",
                            "enter-from": "opacity-0",
                            "enter-to": "opacity-100",
                            leave: "ease-in-out duration-500",
                            "leave-from": "opacity-100",
                            "leave-to": "opacity-0"
                          }, {
                            default: withCtx((_5, _push6, _parent6, _scopeId5) => {
                              if (_push6) {
                                _push6(`<div class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4"${_scopeId5}><button type="button" class="rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"${_scopeId5}><span class="sr-only"${_scopeId5}>Close panel</span>`);
                                _push6(ssrRenderComponent(_component_XMarkIcon, {
                                  class: "h-6 w-6",
                                  "aria-hidden": "true"
                                }, null, _parent6, _scopeId5));
                                _push6(`</button></div>`);
                              } else {
                                return [
                                  createVNode("div", { class: "absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4" }, [
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
                          }, _parent5, _scopeId4));
                          _push5(`<div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl"${_scopeId4}><h2 class="text-2xl font-bold dark:text-white text-center"${_scopeId4}> Notifications </h2><div class="inline-flex items-center justify-center w-full"${_scopeId4}><hr class="w-64 h-1 my-8 bg-gray-200 border-0 rounded dark:bg-gray-700"${_scopeId4}><div class="absolute px-4 -translate-x-1/2 bg-white left-1/2 dark:bg-gray-900"${_scopeId4}><svg class="w-4 h-4 text-primary-dark dark:text-primary-dark" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 14"${_scopeId4}><path d="M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z"${_scopeId4}></path></svg></div></div><div class="flex-1 overflow-y-auto m-2"${_scopeId4}>`);
                          if ($data.notifications.length == 0) {
                            _push5(`<div class="text-m font-semi-bold text-center text-gray-500 italic"${_scopeId4}> No unread notifications. </div>`);
                          } else {
                            _push5(`<!---->`);
                          }
                          _push5(`<!--[-->`);
                          ssrRenderList($data.notifications, (notification) => {
                            _push5(`<div${_scopeId4}><div class="relative rounded-lg border border-gray-300 bg-gray-50 px-6 py-5 shadow-sm items-top m-1 shadow-md transition-all"${_scopeId4}><div class="text-m font-semi-bold text-gray-900"${_scopeId4}>${notification.data.title}</div><div class="text-sm text-gray-500"${_scopeId4}>${notification.data.message}</div><div class="flex justify-between mt-2"${_scopeId4}><button type="button" class="rounded-md text-sm font-medium text-teal-600 hover:text-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId4}> Mark As Read </button><div class="text-sm text-gray-400 justify-end italic"${_scopeId4}>${ssrInterpolate($options.calculateDaysDifference(
                              notification.created_at
                            ))}</div></div></div></div>`);
                          });
                          _push5(`<!--]--></div><hr class="h-px my-1 bg-gray-200 border-0 dark:bg-gray-700"${_scopeId4}><div class="flex justify-end m-2"${_scopeId4}>`);
                          _push5(ssrRenderComponent(_component_jet_secondary_button, {
                            type: "button",
                            onClick: ($event) => $data.open = false
                          }, {
                            default: withCtx((_5, _push6, _parent6, _scopeId5) => {
                              if (_push6) {
                                _push6(` Close `);
                              } else {
                                return [
                                  createTextVNode(" Close ")
                                ];
                              }
                            }),
                            _: 1
                          }, _parent5, _scopeId4));
                          _push5(ssrRenderComponent(_component_jet_button, {
                            type: "button",
                            class: "ml-4",
                            disabled: $data.notifications.length == 0,
                            onClick: ($event) => $options.markAllNotificationAsRead()
                          }, {
                            default: withCtx((_5, _push6, _parent6, _scopeId5) => {
                              if (_push6) {
                                _push6(` Mark All As Read `);
                              } else {
                                return [
                                  createTextVNode(" Mark All As Read ")
                                ];
                              }
                            }),
                            _: 1
                          }, _parent5, _scopeId4));
                          _push5(`</div></div>`);
                        } else {
                          return [
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
                                createVNode("div", { class: "absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4" }, [
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
                            createVNode("div", { class: "flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl" }, [
                              createVNode("h2", { class: "text-2xl font-bold dark:text-white text-center" }, " Notifications "),
                              createVNode("div", { class: "inline-flex items-center justify-center w-full" }, [
                                createVNode("hr", { class: "w-64 h-1 my-8 bg-gray-200 border-0 rounded dark:bg-gray-700" }),
                                createVNode("div", { class: "absolute px-4 -translate-x-1/2 bg-white left-1/2 dark:bg-gray-900" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "w-4 h-4 text-primary-dark dark:text-primary-dark",
                                    "aria-hidden": "true",
                                    xmlns: "http://www.w3.org/2000/svg",
                                    fill: "currentColor",
                                    viewBox: "0 0 18 14"
                                  }, [
                                    createVNode("path", { d: "M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z" })
                                  ]))
                                ])
                              ]),
                              createVNode("div", { class: "flex-1 overflow-y-auto m-2" }, [
                                $data.notifications.length == 0 ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-m font-semi-bold text-center text-gray-500 italic"
                                }, " No unread notifications. ")) : createCommentVNode("", true),
                                (openBlock(true), createBlock(Fragment, null, renderList($data.notifications, (notification) => {
                                  return openBlock(), createBlock("div", {
                                    key: notification.title
                                  }, [
                                    createVNode("div", { class: "relative rounded-lg border border-gray-300 bg-gray-50 px-6 py-5 shadow-sm items-top m-1 shadow-md transition-all" }, [
                                      createVNode("div", {
                                        class: "text-m font-semi-bold text-gray-900",
                                        innerHTML: notification.data.title
                                      }, null, 8, ["innerHTML"]),
                                      createVNode("div", {
                                        class: "text-sm text-gray-500",
                                        innerHTML: notification.data.message
                                      }, null, 8, ["innerHTML"]),
                                      createVNode("div", { class: "flex justify-between mt-2" }, [
                                        createVNode("button", {
                                          type: "button",
                                          class: "rounded-md text-sm font-medium text-teal-600 hover:text-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                                          onClick: ($event) => $options.markNotificationAsRead(
                                            notification
                                          )
                                        }, " Mark As Read ", 8, ["onClick"]),
                                        createVNode("div", { class: "text-sm text-gray-400 justify-end italic" }, toDisplayString($options.calculateDaysDifference(
                                          notification.created_at
                                        )), 1)
                                      ])
                                    ])
                                  ]);
                                }), 128))
                              ]),
                              createVNode("hr", { class: "h-px my-1 bg-gray-200 border-0 dark:bg-gray-700" }),
                              createVNode("div", { class: "flex justify-end m-2" }, [
                                createVNode(_component_jet_secondary_button, {
                                  type: "button",
                                  onClick: ($event) => $data.open = false
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" Close ")
                                  ]),
                                  _: 1
                                }, 8, ["onClick"]),
                                createVNode(_component_jet_button, {
                                  type: "button",
                                  class: "ml-4",
                                  disabled: $data.notifications.length == 0,
                                  onClick: ($event) => $options.markAllNotificationAsRead()
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" Mark All As Read ")
                                  ]),
                                  _: 1
                                }, 8, ["disabled", "onClick"])
                              ])
                            ])
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_DialogPanel, { class: "pointer-events-auto relative w-screen max-w-md" }, {
                        default: withCtx(() => [
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
                              createVNode("div", { class: "absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4" }, [
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
                          createVNode("div", { class: "flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl" }, [
                            createVNode("h2", { class: "text-2xl font-bold dark:text-white text-center" }, " Notifications "),
                            createVNode("div", { class: "inline-flex items-center justify-center w-full" }, [
                              createVNode("hr", { class: "w-64 h-1 my-8 bg-gray-200 border-0 rounded dark:bg-gray-700" }),
                              createVNode("div", { class: "absolute px-4 -translate-x-1/2 bg-white left-1/2 dark:bg-gray-900" }, [
                                (openBlock(), createBlock("svg", {
                                  class: "w-4 h-4 text-primary-dark dark:text-primary-dark",
                                  "aria-hidden": "true",
                                  xmlns: "http://www.w3.org/2000/svg",
                                  fill: "currentColor",
                                  viewBox: "0 0 18 14"
                                }, [
                                  createVNode("path", { d: "M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z" })
                                ]))
                              ])
                            ]),
                            createVNode("div", { class: "flex-1 overflow-y-auto m-2" }, [
                              $data.notifications.length == 0 ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "text-m font-semi-bold text-center text-gray-500 italic"
                              }, " No unread notifications. ")) : createCommentVNode("", true),
                              (openBlock(true), createBlock(Fragment, null, renderList($data.notifications, (notification) => {
                                return openBlock(), createBlock("div", {
                                  key: notification.title
                                }, [
                                  createVNode("div", { class: "relative rounded-lg border border-gray-300 bg-gray-50 px-6 py-5 shadow-sm items-top m-1 shadow-md transition-all" }, [
                                    createVNode("div", {
                                      class: "text-m font-semi-bold text-gray-900",
                                      innerHTML: notification.data.title
                                    }, null, 8, ["innerHTML"]),
                                    createVNode("div", {
                                      class: "text-sm text-gray-500",
                                      innerHTML: notification.data.message
                                    }, null, 8, ["innerHTML"]),
                                    createVNode("div", { class: "flex justify-between mt-2" }, [
                                      createVNode("button", {
                                        type: "button",
                                        class: "rounded-md text-sm font-medium text-teal-600 hover:text-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                                        onClick: ($event) => $options.markNotificationAsRead(
                                          notification
                                        )
                                      }, " Mark As Read ", 8, ["onClick"]),
                                      createVNode("div", { class: "text-sm text-gray-400 justify-end italic" }, toDisplayString($options.calculateDaysDifference(
                                        notification.created_at
                                      )), 1)
                                    ])
                                  ])
                                ]);
                              }), 128))
                            ]),
                            createVNode("hr", { class: "h-px my-1 bg-gray-200 border-0 dark:bg-gray-700" }),
                            createVNode("div", { class: "flex justify-end m-2" }, [
                              createVNode(_component_jet_secondary_button, {
                                type: "button",
                                onClick: ($event) => $data.open = false
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(" Close ")
                                ]),
                                _: 1
                              }, 8, ["onClick"]),
                              createVNode(_component_jet_button, {
                                type: "button",
                                class: "ml-4",
                                disabled: $data.notifications.length == 0,
                                onClick: ($event) => $options.markAllNotificationAsRead()
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(" Mark All As Read ")
                                ]),
                                _: 1
                              }, 8, ["disabled", "onClick"])
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
              _push3(`</div></div></div>`);
            } else {
              return [
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
                    createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" })
                  ]),
                  _: 1
                }),
                createVNode("div", { class: "fixed inset-0 overflow-hidden" }, [
                  createVNode("div", { class: "absolute inset-0 overflow-hidden" }, [
                    createVNode("div", { class: "pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10" }, [
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
                          createVNode(_component_DialogPanel, { class: "pointer-events-auto relative w-screen max-w-md" }, {
                            default: withCtx(() => [
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
                                  createVNode("div", { class: "absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4" }, [
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
                              createVNode("div", { class: "flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl" }, [
                                createVNode("h2", { class: "text-2xl font-bold dark:text-white text-center" }, " Notifications "),
                                createVNode("div", { class: "inline-flex items-center justify-center w-full" }, [
                                  createVNode("hr", { class: "w-64 h-1 my-8 bg-gray-200 border-0 rounded dark:bg-gray-700" }),
                                  createVNode("div", { class: "absolute px-4 -translate-x-1/2 bg-white left-1/2 dark:bg-gray-900" }, [
                                    (openBlock(), createBlock("svg", {
                                      class: "w-4 h-4 text-primary-dark dark:text-primary-dark",
                                      "aria-hidden": "true",
                                      xmlns: "http://www.w3.org/2000/svg",
                                      fill: "currentColor",
                                      viewBox: "0 0 18 14"
                                    }, [
                                      createVNode("path", { d: "M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z" })
                                    ]))
                                  ])
                                ]),
                                createVNode("div", { class: "flex-1 overflow-y-auto m-2" }, [
                                  $data.notifications.length == 0 ? (openBlock(), createBlock("div", {
                                    key: 0,
                                    class: "text-m font-semi-bold text-center text-gray-500 italic"
                                  }, " No unread notifications. ")) : createCommentVNode("", true),
                                  (openBlock(true), createBlock(Fragment, null, renderList($data.notifications, (notification) => {
                                    return openBlock(), createBlock("div", {
                                      key: notification.title
                                    }, [
                                      createVNode("div", { class: "relative rounded-lg border border-gray-300 bg-gray-50 px-6 py-5 shadow-sm items-top m-1 shadow-md transition-all" }, [
                                        createVNode("div", {
                                          class: "text-m font-semi-bold text-gray-900",
                                          innerHTML: notification.data.title
                                        }, null, 8, ["innerHTML"]),
                                        createVNode("div", {
                                          class: "text-sm text-gray-500",
                                          innerHTML: notification.data.message
                                        }, null, 8, ["innerHTML"]),
                                        createVNode("div", { class: "flex justify-between mt-2" }, [
                                          createVNode("button", {
                                            type: "button",
                                            class: "rounded-md text-sm font-medium text-teal-600 hover:text-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                                            onClick: ($event) => $options.markNotificationAsRead(
                                              notification
                                            )
                                          }, " Mark As Read ", 8, ["onClick"]),
                                          createVNode("div", { class: "text-sm text-gray-400 justify-end italic" }, toDisplayString($options.calculateDaysDifference(
                                            notification.created_at
                                          )), 1)
                                        ])
                                      ])
                                    ]);
                                  }), 128))
                                ]),
                                createVNode("hr", { class: "h-px my-1 bg-gray-200 border-0 dark:bg-gray-700" }),
                                createVNode("div", { class: "flex justify-end m-2" }, [
                                  createVNode(_component_jet_secondary_button, {
                                    type: "button",
                                    onClick: ($event) => $data.open = false
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(" Close ")
                                    ]),
                                    _: 1
                                  }, 8, ["onClick"]),
                                  createVNode(_component_jet_button, {
                                    type: "button",
                                    class: "ml-4",
                                    disabled: $data.notifications.length == 0,
                                    onClick: ($event) => $options.markAllNotificationAsRead()
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(" Mark All As Read ")
                                    ]),
                                    _: 1
                                  }, 8, ["disabled", "onClick"])
                                ])
                              ])
                            ]),
                            _: 1
                          })
                        ]),
                        _: 1
                      })
                    ])
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
            class: "fixed inset-0 z-50",
            onClose: ($event) => $data.open = false
          }, {
            default: withCtx(() => [
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
                  createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" })
                ]),
                _: 1
              }),
              createVNode("div", { class: "fixed inset-0 overflow-hidden" }, [
                createVNode("div", { class: "absolute inset-0 overflow-hidden" }, [
                  createVNode("div", { class: "pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10" }, [
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
                        createVNode(_component_DialogPanel, { class: "pointer-events-auto relative w-screen max-w-md" }, {
                          default: withCtx(() => [
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
                                createVNode("div", { class: "absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4" }, [
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
                            createVNode("div", { class: "flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl" }, [
                              createVNode("h2", { class: "text-2xl font-bold dark:text-white text-center" }, " Notifications "),
                              createVNode("div", { class: "inline-flex items-center justify-center w-full" }, [
                                createVNode("hr", { class: "w-64 h-1 my-8 bg-gray-200 border-0 rounded dark:bg-gray-700" }),
                                createVNode("div", { class: "absolute px-4 -translate-x-1/2 bg-white left-1/2 dark:bg-gray-900" }, [
                                  (openBlock(), createBlock("svg", {
                                    class: "w-4 h-4 text-primary-dark dark:text-primary-dark",
                                    "aria-hidden": "true",
                                    xmlns: "http://www.w3.org/2000/svg",
                                    fill: "currentColor",
                                    viewBox: "0 0 18 14"
                                  }, [
                                    createVNode("path", { d: "M6 0H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3H2a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Zm10 0h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v1a3 3 0 0 1-3 3h-1a1 1 0 0 0 0 2h1a5.006 5.006 0 0 0 5-5V2a2 2 0 0 0-2-2Z" })
                                  ]))
                                ])
                              ]),
                              createVNode("div", { class: "flex-1 overflow-y-auto m-2" }, [
                                $data.notifications.length == 0 ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-m font-semi-bold text-center text-gray-500 italic"
                                }, " No unread notifications. ")) : createCommentVNode("", true),
                                (openBlock(true), createBlock(Fragment, null, renderList($data.notifications, (notification) => {
                                  return openBlock(), createBlock("div", {
                                    key: notification.title
                                  }, [
                                    createVNode("div", { class: "relative rounded-lg border border-gray-300 bg-gray-50 px-6 py-5 shadow-sm items-top m-1 shadow-md transition-all" }, [
                                      createVNode("div", {
                                        class: "text-m font-semi-bold text-gray-900",
                                        innerHTML: notification.data.title
                                      }, null, 8, ["innerHTML"]),
                                      createVNode("div", {
                                        class: "text-sm text-gray-500",
                                        innerHTML: notification.data.message
                                      }, null, 8, ["innerHTML"]),
                                      createVNode("div", { class: "flex justify-between mt-2" }, [
                                        createVNode("button", {
                                          type: "button",
                                          class: "rounded-md text-sm font-medium text-teal-600 hover:text-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                                          onClick: ($event) => $options.markNotificationAsRead(
                                            notification
                                          )
                                        }, " Mark As Read ", 8, ["onClick"]),
                                        createVNode("div", { class: "text-sm text-gray-400 justify-end italic" }, toDisplayString($options.calculateDaysDifference(
                                          notification.created_at
                                        )), 1)
                                      ])
                                    ])
                                  ]);
                                }), 128))
                              ]),
                              createVNode("hr", { class: "h-px my-1 bg-gray-200 border-0 dark:bg-gray-700" }),
                              createVNode("div", { class: "flex justify-end m-2" }, [
                                createVNode(_component_jet_secondary_button, {
                                  type: "button",
                                  onClick: ($event) => $data.open = false
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" Close ")
                                  ]),
                                  _: 1
                                }, 8, ["onClick"]),
                                createVNode(_component_jet_button, {
                                  type: "button",
                                  class: "ml-4",
                                  disabled: $data.notifications.length == 0,
                                  onClick: ($event) => $options.markAllNotificationAsRead()
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" Mark All As Read ")
                                  ]),
                                  _: 1
                                }, 8, ["disabled", "onClick"])
                              ])
                            ])
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    })
                  ])
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
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Notification.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Notification = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const userNavigation = [];
const secondaryNavigation = [
  { name: "Starred", href: "#" },
  { name: "Shared with me", href: "#" }
];
const navigation = [
  {
    auth: false,
    name: "Projects",
    href: "/projects",
    icon: FolderIcon,
    bg: "bg-white"
  },
  {
    auth: false,
    name: "Spectra",
    href: "/spectra",
    icon: Squares2X2Icon,
    bg: "bg-white"
  },
  {
    auth: false,
    name: "Compounds",
    href: "/compounds",
    icon: SwatchIcon,
    bg: "bg-white"
  },
  {
    auth: true,
    name: "Dashboard",
    href: "/dashboard",
    prefix: "Your Space",
    id: "tour-step-dashboard",
    icon: HomeIcon$1,
    bg: "bg-gray-50",
    children: [
      {
        auth: true,
        name: "Shared with me",
        href: "/dashboard/shared-with-me",
        id: "tour-step-shared-with-me",
        icon: UsersIcon,
        bg: "bg-white"
      },
      {
        auth: false,
        name: "Recent",
        href: "/dashboard/recent",
        id: "tour-step-recent",
        icon: ClockIcon,
        bg: "bg-white"
      },
      {
        auth: false,
        name: "Starred",
        href: "/dashboard/starred",
        id: "tour-step-starred",
        icon: StarIcon,
        bg: "bg-white"
      },
      {
        auth: false,
        name: "Trash",
        href: "/dashboard/trashed",
        id: "tour-step-trash",
        icon: TrashIcon$1,
        bg: "bg-white"
      }
    ]
  }
];
const _sfc_main = {
  components: {
    // ProjectCreate,
    AppTour,
    JetBanner,
    JetApplicationLogo,
    JetApplicationMark,
    JetDropdownLink,
    JetDropdown,
    Link,
    Head,
    Dialog,
    DialogOverlay,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
    BellIcon,
    Bars3Icon,
    MagnifyingGlassIcon,
    XMarkIcon,
    PlusIcon,
    FlashMessages,
    DialogPanel,
    AnnouncementBanner,
    Search,
    Create,
    ClockIcon,
    UsersIcon,
    StarIcon,
    TrashIcon: TrashIcon$1,
    FolderIcon,
    Squares2X2Icon,
    // StudyCreate,
    Submission,
    Notification,
    SwatchIcon
  },
  props: {
    title: String
  },
  setup() {
    var collapseSidebarStatus = JSON.parse(
      localStorage.getItem("collapseSidebarStatus")
    );
    if (!collapseSidebarStatus) {
      collapseSidebarStatus = false;
    }
    const sidebarOpen = ref(false);
    const collapseSidebar = ref(collapseSidebarStatus);
    const notificationElement = ref(null);
    return {
      userNavigation,
      secondaryNavigation,
      sidebarOpen,
      collapseSidebar,
      notificationElement,
      navigation
    };
  },
  computed: {
    editableTeamRole() {
      if (this.$page.props.teamRole && this.$page.props.teamRole.name) {
        if (this.$page.props.teamRole.key == "owner" || this.$page.props.teamRole.key == "collaborator") {
          return true;
        } else {
          return false;
        }
      } else {
        return true;
      }
    },
    filteredNavigation() {
      if (this.$page.props.auth.user.first_name) {
        return this.navigation;
      } else {
        return this.navigation.filter((i) => !i.auth);
      }
    },
    personalTeam() {
      return this.$page.props.auth.user.all_teams.filter(
        (t) => t.personal_team
      )[0];
    },
    MEILISEARCH_HOST() {
      return this.$page.props.MEILISEARCH_HOST;
    },
    MEILISEARCH_PUBLICKEY() {
      return this.$page.props.MEILISEARCH_PUBLICKEY;
    }
  },
  mounted() {
  },
  methods: {
    switchToTeam(team) {
      this.$inertia.put(
        route("current-team.update"),
        {
          team_id: team.id
        },
        {
          preserveState: false
        }
      );
    },
    toggleCollapseSidebar() {
      this.collapseSidebar = !this.collapseSidebar;
      localStorage.setItem("collapseSidebarStatus", this.collapseSidebar);
    },
    logout() {
      this.$inertia.post(route("logout"));
    },
    startTour() {
      this.$tours["appTour"].start();
    },
    openShowNotificationDialog(notification) {
      this.notificationElement.toggleShowNotificationDialog(notification);
    },
    hasUnreadNotification() {
      return this.$page.props.auth.user.notifications ? this.$page.props.auth.user.notifications.length > 0 : false;
    },
    countNotification() {
      return this.$page.props.auth.user.notifications ? this.$page.props.auth.user.notifications.length : 0;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_AppTour = resolveComponent("AppTour");
  const _component_jet_banner = resolveComponent("jet-banner");
  const _component_announcement_banner = resolveComponent("announcement-banner");
  const _component_TransitionRoot = resolveComponent("TransitionRoot");
  const _component_Dialog = resolveComponent("Dialog");
  const _component_TransitionChild = resolveComponent("TransitionChild");
  const _component_DialogOverlay = resolveComponent("DialogOverlay");
  const _component_XMarkIcon = resolveComponent("XMarkIcon");
  const _component_Link = resolveComponent("Link");
  const _component_jet_application_logo = resolveComponent("jet-application-logo");
  const _component_create = resolveComponent("create");
  const _component_jet_application_mark = resolveComponent("jet-application-mark");
  const _component_Bars3Icon = resolveComponent("Bars3Icon");
  const _component_search = resolveComponent("search");
  const _component_flash_messages = resolveComponent("flash-messages");
  const _component_BellIcon = resolveComponent("BellIcon");
  const _component_notification = resolveComponent("notification");
  const _component_Menu = resolveComponent("Menu");
  const _component_MenuButton = resolveComponent("MenuButton");
  const _component_MenuItems = resolveComponent("MenuItems");
  const _component_jet_dropdown_link = resolveComponent("jet-dropdown-link");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, { title: $props.title }, null, _parent));
  _push(ssrRenderComponent(_component_AppTour, null, null, _parent));
  _push(ssrRenderComponent(_component_jet_banner, null, null, _parent));
  _push(ssrRenderComponent(_component_announcement_banner, null, null, _parent));
  _push(`<div class="h-full flex min-h-screen">`);
  _push(ssrRenderComponent(_component_TransitionRoot, {
    as: "template",
    show: $setup.sidebarOpen
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_Dialog, {
          as: "div",
          class: "fixed inset-0 flex z-40 md:hidden",
          onClose: ($event) => $setup.sidebarOpen = false
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "transition-opacity ease-linear duration-300",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "transition-opacity ease-linear duration-300",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(ssrRenderComponent(_component_DialogOverlay, { class: "fixed inset-0 bg-gray-600 bg-opacity-75" }, null, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_DialogOverlay, { class: "fixed inset-0 bg-gray-600 bg-opacity-75" })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "transition ease-in-out duration-300 transform",
                "enter-from": "-translate-x-full",
                "enter-to": "translate-x-0",
                leave: "transition ease-in-out duration-300 transform",
                "leave-from": "translate-x-0",
                "leave-to": "-translate-x-full"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(`<div class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-white"${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_TransitionChild, {
                      as: "template",
                      enter: "ease-in-out duration-300",
                      "enter-from": "opacity-0",
                      "enter-to": "opacity-100",
                      leave: "ease-in-out duration-300",
                      "leave-from": "opacity-100",
                      "leave-to": "opacity-0"
                    }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<div class="absolute top-0 right-0 -mr-12 pt-2"${_scopeId4}><button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"${_scopeId4}><span class="sr-only"${_scopeId4}>Close sidebar</span>`);
                          _push5(ssrRenderComponent(_component_XMarkIcon, {
                            class: "h-6 w-6 text-white",
                            "aria-hidden": "true"
                          }, null, _parent5, _scopeId4));
                          _push5(`</button></div>`);
                        } else {
                          return [
                            createVNode("div", { class: "absolute top-0 right-0 -mr-12 pt-2" }, [
                              createVNode("button", {
                                type: "button",
                                class: "ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white",
                                onClick: ($event) => $setup.sidebarOpen = false
                              }, [
                                createVNode("span", { class: "sr-only" }, "Close sidebar"),
                                createVNode(_component_XMarkIcon, {
                                  class: "h-6 w-6 text-white",
                                  "aria-hidden": "true"
                                })
                              ], 8, ["onClick"])
                            ])
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                    _push4(`<div class="flex-shrink-0 flex items-center px-4"${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_Link, {
                      href: _ctx.route("welcome")
                    }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(ssrRenderComponent(_component_jet_application_logo, { class: "block h-8 w-auto" }, null, _parent5, _scopeId4));
                        } else {
                          return [
                            createVNode(_component_jet_application_logo, { class: "block h-8 w-auto" })
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                    _push4(`</div>`);
                    if ($options.editableTeamRole) {
                      _push4(`<div class="mt-1 flex-1 h-0 overflow-y-auto"${_scopeId3}><div class="my-4 mx-4"${_scopeId3}>`);
                      _push4(ssrRenderComponent(_component_create, { mode: "button" }, null, _parent4, _scopeId3));
                      _push4(`<span class="float-center text-xs cursor-pointer hover:text-blue-700 mt-2"${_scopeId3}><a href="https://docs.nmrxiv.org/submission-guides/submission-process.html" target="_blank"${_scopeId3}>Need help? </a></span></div><!--[-->`);
                      ssrRenderList($options.filteredNavigation, (item) => {
                        _push4(`<nav class="flex-1 px-2 bg-white space-y-1"${_scopeId3}>`);
                        _push4(ssrRenderComponent(_component_Link, {
                          href: item.href,
                          class: "my-6 text-gray-900 group flex items-center px-2 text-sm font-medium rounded-md"
                        }, {
                          default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                            if (_push5) {
                              _push5(`<div class="pl-2 pr-4"${_scopeId4}>`);
                              ssrRenderVNode(_push5, createVNode(resolveDynamicComponent(item.icon), {
                                class: "h-6 w-6",
                                "aria-hidden": "true"
                              }, null), _parent5, _scopeId4);
                              _push5(`</div> ${ssrInterpolate(item.name)}`);
                            } else {
                              return [
                                createVNode("div", { class: "pl-2 pr-4" }, [
                                  (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                                    class: "h-6 w-6",
                                    "aria-hidden": "true"
                                  }))
                                ]),
                                createTextVNode(" " + toDisplayString(item.name), 1)
                              ];
                            }
                          }),
                          _: 2
                        }, _parent4, _scopeId3));
                        _push4(`</nav>`);
                      });
                      _push4(`<!--]--></div>`);
                    } else {
                      _push4(`<!---->`);
                    }
                    _push4(`</div>`);
                  } else {
                    return [
                      createVNode("div", { class: "relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-white" }, [
                        createVNode(_component_TransitionChild, {
                          as: "template",
                          enter: "ease-in-out duration-300",
                          "enter-from": "opacity-0",
                          "enter-to": "opacity-100",
                          leave: "ease-in-out duration-300",
                          "leave-from": "opacity-100",
                          "leave-to": "opacity-0"
                        }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "absolute top-0 right-0 -mr-12 pt-2" }, [
                              createVNode("button", {
                                type: "button",
                                class: "ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white",
                                onClick: ($event) => $setup.sidebarOpen = false
                              }, [
                                createVNode("span", { class: "sr-only" }, "Close sidebar"),
                                createVNode(_component_XMarkIcon, {
                                  class: "h-6 w-6 text-white",
                                  "aria-hidden": "true"
                                })
                              ], 8, ["onClick"])
                            ])
                          ]),
                          _: 1
                        }),
                        createVNode("div", { class: "flex-shrink-0 flex items-center px-4" }, [
                          createVNode(_component_Link, {
                            href: _ctx.route("welcome")
                          }, {
                            default: withCtx(() => [
                              createVNode(_component_jet_application_logo, { class: "block h-8 w-auto" })
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        $options.editableTeamRole ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "mt-1 flex-1 h-0 overflow-y-auto"
                        }, [
                          createVNode("div", { class: "my-4 mx-4" }, [
                            createVNode(_component_create, { mode: "button" }),
                            createVNode("span", { class: "float-center text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                              createVNode("a", {
                                href: "https://docs.nmrxiv.org/submission-guides/submission-process.html",
                                target: "_blank"
                              }, "Need help? ")
                            ])
                          ]),
                          (openBlock(true), createBlock(Fragment, null, renderList($options.filteredNavigation, (item) => {
                            return openBlock(), createBlock("nav", {
                              key: item.name,
                              class: "flex-1 px-2 bg-white space-y-1"
                            }, [
                              createVNode(_component_Link, {
                                href: item.href,
                                class: "my-6 text-gray-900 group flex items-center px-2 text-sm font-medium rounded-md"
                              }, {
                                default: withCtx(() => [
                                  createVNode("div", { class: "pl-2 pr-4" }, [
                                    (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                                      class: "h-6 w-6",
                                      "aria-hidden": "true"
                                    }))
                                  ]),
                                  createTextVNode(" " + toDisplayString(item.name), 1)
                                ]),
                                _: 2
                              }, 1032, ["href"])
                            ]);
                          }), 128))
                        ])) : createCommentVNode("", true)
                      ])
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<div class="flex-shrink-0 w-14" aria-hidden="true"${_scopeId2}></div>`);
            } else {
              return [
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "transition-opacity ease-linear duration-300",
                  "enter-from": "opacity-0",
                  "enter-to": "opacity-100",
                  leave: "transition-opacity ease-linear duration-300",
                  "leave-from": "opacity-100",
                  "leave-to": "opacity-0"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_DialogOverlay, { class: "fixed inset-0 bg-gray-600 bg-opacity-75" })
                  ]),
                  _: 1
                }),
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "transition ease-in-out duration-300 transform",
                  "enter-from": "-translate-x-full",
                  "enter-to": "translate-x-0",
                  leave: "transition ease-in-out duration-300 transform",
                  "leave-from": "translate-x-0",
                  "leave-to": "-translate-x-full"
                }, {
                  default: withCtx(() => [
                    createVNode("div", { class: "relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-white" }, [
                      createVNode(_component_TransitionChild, {
                        as: "template",
                        enter: "ease-in-out duration-300",
                        "enter-from": "opacity-0",
                        "enter-to": "opacity-100",
                        leave: "ease-in-out duration-300",
                        "leave-from": "opacity-100",
                        "leave-to": "opacity-0"
                      }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "absolute top-0 right-0 -mr-12 pt-2" }, [
                            createVNode("button", {
                              type: "button",
                              class: "ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white",
                              onClick: ($event) => $setup.sidebarOpen = false
                            }, [
                              createVNode("span", { class: "sr-only" }, "Close sidebar"),
                              createVNode(_component_XMarkIcon, {
                                class: "h-6 w-6 text-white",
                                "aria-hidden": "true"
                              })
                            ], 8, ["onClick"])
                          ])
                        ]),
                        _: 1
                      }),
                      createVNode("div", { class: "flex-shrink-0 flex items-center px-4" }, [
                        createVNode(_component_Link, {
                          href: _ctx.route("welcome")
                        }, {
                          default: withCtx(() => [
                            createVNode(_component_jet_application_logo, { class: "block h-8 w-auto" })
                          ]),
                          _: 1
                        }, 8, ["href"])
                      ]),
                      $options.editableTeamRole ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-1 flex-1 h-0 overflow-y-auto"
                      }, [
                        createVNode("div", { class: "my-4 mx-4" }, [
                          createVNode(_component_create, { mode: "button" }),
                          createVNode("span", { class: "float-center text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                            createVNode("a", {
                              href: "https://docs.nmrxiv.org/submission-guides/submission-process.html",
                              target: "_blank"
                            }, "Need help? ")
                          ])
                        ]),
                        (openBlock(true), createBlock(Fragment, null, renderList($options.filteredNavigation, (item) => {
                          return openBlock(), createBlock("nav", {
                            key: item.name,
                            class: "flex-1 px-2 bg-white space-y-1"
                          }, [
                            createVNode(_component_Link, {
                              href: item.href,
                              class: "my-6 text-gray-900 group flex items-center px-2 text-sm font-medium rounded-md"
                            }, {
                              default: withCtx(() => [
                                createVNode("div", { class: "pl-2 pr-4" }, [
                                  (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                                    class: "h-6 w-6",
                                    "aria-hidden": "true"
                                  }))
                                ]),
                                createTextVNode(" " + toDisplayString(item.name), 1)
                              ]),
                              _: 2
                            }, 1032, ["href"])
                          ]);
                        }), 128))
                      ])) : createCommentVNode("", true)
                    ])
                  ]),
                  _: 1
                }),
                createVNode("div", {
                  class: "flex-shrink-0 w-14",
                  "aria-hidden": "true"
                })
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_Dialog, {
            as: "div",
            class: "fixed inset-0 flex z-40 md:hidden",
            onClose: ($event) => $setup.sidebarOpen = false
          }, {
            default: withCtx(() => [
              createVNode(_component_TransitionChild, {
                as: "template",
                enter: "transition-opacity ease-linear duration-300",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "transition-opacity ease-linear duration-300",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx(() => [
                  createVNode(_component_DialogOverlay, { class: "fixed inset-0 bg-gray-600 bg-opacity-75" })
                ]),
                _: 1
              }),
              createVNode(_component_TransitionChild, {
                as: "template",
                enter: "transition ease-in-out duration-300 transform",
                "enter-from": "-translate-x-full",
                "enter-to": "translate-x-0",
                leave: "transition ease-in-out duration-300 transform",
                "leave-from": "translate-x-0",
                "leave-to": "-translate-x-full"
              }, {
                default: withCtx(() => [
                  createVNode("div", { class: "relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-white" }, [
                    createVNode(_component_TransitionChild, {
                      as: "template",
                      enter: "ease-in-out duration-300",
                      "enter-from": "opacity-0",
                      "enter-to": "opacity-100",
                      leave: "ease-in-out duration-300",
                      "leave-from": "opacity-100",
                      "leave-to": "opacity-0"
                    }, {
                      default: withCtx(() => [
                        createVNode("div", { class: "absolute top-0 right-0 -mr-12 pt-2" }, [
                          createVNode("button", {
                            type: "button",
                            class: "ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white",
                            onClick: ($event) => $setup.sidebarOpen = false
                          }, [
                            createVNode("span", { class: "sr-only" }, "Close sidebar"),
                            createVNode(_component_XMarkIcon, {
                              class: "h-6 w-6 text-white",
                              "aria-hidden": "true"
                            })
                          ], 8, ["onClick"])
                        ])
                      ]),
                      _: 1
                    }),
                    createVNode("div", { class: "flex-shrink-0 flex items-center px-4" }, [
                      createVNode(_component_Link, {
                        href: _ctx.route("welcome")
                      }, {
                        default: withCtx(() => [
                          createVNode(_component_jet_application_logo, { class: "block h-8 w-auto" })
                        ]),
                        _: 1
                      }, 8, ["href"])
                    ]),
                    $options.editableTeamRole ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mt-1 flex-1 h-0 overflow-y-auto"
                    }, [
                      createVNode("div", { class: "my-4 mx-4" }, [
                        createVNode(_component_create, { mode: "button" }),
                        createVNode("span", { class: "float-center text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                          createVNode("a", {
                            href: "https://docs.nmrxiv.org/submission-guides/submission-process.html",
                            target: "_blank"
                          }, "Need help? ")
                        ])
                      ]),
                      (openBlock(true), createBlock(Fragment, null, renderList($options.filteredNavigation, (item) => {
                        return openBlock(), createBlock("nav", {
                          key: item.name,
                          class: "flex-1 px-2 bg-white space-y-1"
                        }, [
                          createVNode(_component_Link, {
                            href: item.href,
                            class: "my-6 text-gray-900 group flex items-center px-2 text-sm font-medium rounded-md"
                          }, {
                            default: withCtx(() => [
                              createVNode("div", { class: "pl-2 pr-4" }, [
                                (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                                  class: "h-6 w-6",
                                  "aria-hidden": "true"
                                }))
                              ]),
                              createTextVNode(" " + toDisplayString(item.name), 1)
                            ]),
                            _: 2
                          }, 1032, ["href"])
                        ]);
                      }), 128))
                    ])) : createCommentVNode("", true)
                  ])
                ]),
                _: 1
              }),
              createVNode("div", {
                class: "flex-shrink-0 w-14",
                "aria-hidden": "true"
              })
            ]),
            _: 1
          }, 8, ["onClose"])
        ];
      }
    }),
    _: 1
  }, _parent));
  if ($setup.collapseSidebar) {
    _push(`<div class="hidden fixed bg-white border-r md:flex md:flex-shrink-0 md:inset-y-0 z-10"><div class="flex flex-col w-20"><div class="flex-1 flex flex-col min-h-0 overflow-y-auto"><div class="flex-1"><div class="bg-white border-b pb-4 pt-4 flex flex-shrink-0 px-4">`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("welcome")
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_jet_application_mark, { class: "block h-8 p-0.5 ml-1.5 w-auto" }, null, _parent2, _scopeId));
        } else {
          return [
            createVNode(_component_jet_application_mark, { class: "block h-8 p-0.5 ml-1.5 w-auto" })
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div><div class="px-4 flex flex-col mt-3 mb-1">`);
    _push(ssrRenderComponent(_component_create, { mode: "icon" }, null, _parent));
    _push(`</div><nav aria-label="Sidebar" class="flex flex-col items-center py-4 px-0"><!--[-->`);
    ssrRenderList($options.filteredNavigation, (item) => {
      _push(ssrRenderComponent(_component_Link, {
        key: item.name,
        href: item.href,
        class: [
          _ctx.$page.url === item.href ? " border-r-4 bg-gray-200 border-r-black" : "",
          "w-full px-7 hover:bg-gray-700 hover:text-white group flex items-center py-3 text-sm font-medium " + item.bg
        ]
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent(item.icon), {
              class: "h-6 w-6",
              "aria-hidden": "true"
            }, null), _parent2, _scopeId);
            _push2(`<span class="sr-only"${_scopeId}>${ssrInterpolate(item.name)}</span>`);
          } else {
            return [
              (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                class: "h-6 w-6",
                "aria-hidden": "true"
              })),
              createVNode("span", { class: "sr-only" }, toDisplayString(item.name), 1)
            ];
          }
        }),
        _: 2
      }, _parent));
    });
    _push(`<!--]--></nav></div><div class="flex-shrink-0 flex pb-5"></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  if (!$setup.collapseSidebar) {
    _push(`<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-10"><div class="flex flex-col flex-grow border-r border-gray-200 bg-white overflow-y-auto"><div class="flex py-3 pb-4 items-center flex-shrink-0 px-4">`);
    _push(ssrRenderComponent(_component_Link, {
      class: "ml-2",
      href: _ctx.route("welcome")
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_jet_application_logo, { class: "block h-10 w-auto" }, null, _parent2, _scopeId));
        } else {
          return [
            createVNode(_component_jet_application_logo, { class: "block h-10 w-auto" })
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div><div class="flex-grow flex flex-col -mt-1.5">`);
    if ($options.editableTeamRole) {
      _push(`<div class="px-4 flex flex-col mt-3 mb-1 text-center">`);
      _push(ssrRenderComponent(_component_create, { mode: "button" }, null, _parent));
      _push(`<span class="text-xs cursor-pointer hover:text-blue-700 mt-2"><a href="https://docs.nmrxiv.org/submission-guides/submission-process.html" target="_blank">Need help? </a></span></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<nav class="py-4 px-0 bg-white"><!--[-->`);
    ssrRenderList($options.filteredNavigation, (item) => {
      _push(`<div class="${ssrRenderClass([item.auth ? "border-t" : ""])}">`);
      if (item.prefix) {
        _push(`<div class="p-2 bg-gray-100 text-gray-500 text-sm border-b">${ssrInterpolate(item.prefix)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_component_Link, {
        id: item.id,
        href: item.href,
        class: [
          _ctx.$page.url === item.href ? " border-r-4 bg-gray-200 border-r-black" : "",
          "w-full px-7 hover:bg-gray-700 hover:text-white group flex items-center py-3 text-sm font-medium " + item.bg
        ]
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            ssrRenderVNode(_push2, createVNode(resolveDynamicComponent(item.icon), {
              class: "mr-3 ml-2 h-6 w-6",
              "aria-hidden": "true"
            }, null), _parent2, _scopeId);
            _push2(`<span class="flex-1"${_scopeId}>${ssrInterpolate(item.name)}</span>`);
          } else {
            return [
              (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                class: "mr-3 ml-2 h-6 w-6",
                "aria-hidden": "true"
              })),
              createVNode("span", { class: "flex-1" }, toDisplayString(item.name), 1)
            ];
          }
        }),
        _: 2
      }, _parent));
      _push(`<!--[-->`);
      ssrRenderList(item.children, (child) => {
        _push(ssrRenderComponent(_component_Link, {
          id: child.id,
          key: child.name,
          href: child.href,
          class: [
            _ctx.$page.url === child.href ? " border-r-4 bg-gray-200 border-r-black" : "",
            "w-full px-10 hover:bg-gray-700 hover:text-white group flex items-center py-1.5 text-sm font-small " + child.bg
          ]
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              ssrRenderVNode(_push2, createVNode(resolveDynamicComponent(child.icon), {
                class: "mr-3 ml-2 h-4 w-4",
                "aria-hidden": "true"
              }, null), _parent2, _scopeId);
              _push2(`<span class="flex-1"${_scopeId}>${ssrInterpolate(child.name)}</span>`);
            } else {
              return [
                (openBlock(), createBlock(resolveDynamicComponent(child.icon), {
                  class: "mr-3 ml-2 h-4 w-4",
                  "aria-hidden": "true"
                })),
                createVNode("span", { class: "flex-1" }, toDisplayString(child.name), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
      });
      _push(`<!--]--></div>`);
    });
    _push(`<!--]--></nav></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="${ssrRenderClass([
    $setup.collapseSidebar ? "md:pl-40 md:-ml-20" : "md:pl-64",
    "flex flex-col flex-1 z-0"
  ])}"><div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow"><button type="button" class="hidden md:inline-flex p-4 rounded mx-1 mr-3 border-red-200 text-gray-500">`);
  _push(ssrRenderComponent(_component_Bars3Icon, {
    class: "h-6 w-6",
    "aria-hidden": "true"
  }, null, _parent));
  _push(`</button><button type="button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500 md:hidden"><span class="sr-only">Open sidebar</span>`);
  _push(ssrRenderComponent(_component_Bars3Icon, {
    class: "h-6 w-6",
    "aria-hidden": "true"
  }, null, _parent));
  _push(`</button><div class="flex-1 px-4 py-2 flex justify-between"><div class="flex-1 flex">`);
  _push(ssrRenderComponent(_component_search, {
    host: $options.MEILISEARCH_HOST,
    akey: $options.MEILISEARCH_PUBLICKEY
  }, null, _parent));
  _push(`</div>`);
  _push(ssrRenderComponent(_component_flash_messages, null, null, _parent));
  _push(`<div class="ml-4 flex items-center md:ml-6">`);
  if (_ctx.$page.props.auth.user.first_name != null) {
    _push(`<span><div class="ml-5 mt-2 tooltip"><a class="cursor-pointer">`);
    _push(ssrRenderComponent(_component_BellIcon, { class: "w-6 h-6 fill-current text-gray-600" }, null, _parent));
    _push(`</a><span class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom">View Notifications</span></div>`);
    if ($options.hasUnreadNotification()) {
      _push(`<span class="inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">${ssrInterpolate($options.countNotification())}</span>`);
    } else {
      _push(`<!---->`);
    }
    _push(ssrRenderComponent(_component_notification, {
      ref: "notificationElement",
      notification: _ctx.$page.props.auth.user.notifications
    }, null, _parent));
    _push(`</span>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="ml-5 tooltip"><a id="tour-step-documentation" href="https://docs.nmrxiv.org/submission-guides/submission-process.html" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6"><path d="M12 21a2 2 0 0 1-1.41-.59l-.83-.82A2 2 0 0 0 8.34 19H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4a5 5 0 0 1 4 2v16z" class="fill-current text-gray-400"></path><path d="M12 21V5a5 5 0 0 1 4-2h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4.34a2 2 0 0 0-1.42.59l-.83.82A2 2 0 0 1 12 21z" class="fill-current text-gray-600"></path></svg></a><span class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom">Submission guides</span></div>`);
  if (_ctx.$page.props.auth.user) {
    _push(ssrRenderComponent(_component_Menu, {
      as: "div",
      class: "ml-3 relative"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          if (_ctx.$page.props.auth.user.current_team && _ctx.$page.props.auth.user.current_team.personal_team) {
            _push2(`<div id="tour-step-account-management"${_scopeId}>`);
            if (!_ctx.$page.props.jetstream.managesProfilePhotos) {
              _push2(ssrRenderComponent(_component_MenuButton, { class: "flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition" }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<img class="h-8 w-8 rounded-full object-cover"${ssrRenderAttr(
                      "src",
                      _ctx.$page.props.auth.user.profile_photo_url
                    )}${ssrRenderAttr("alt", _ctx.$page.props.auth.user.first_name)}${_scopeId2}>`);
                  } else {
                    return [
                      createVNode("img", {
                        class: "h-8 w-8 rounded-full object-cover",
                        src: _ctx.$page.props.auth.user.profile_photo_url,
                        alt: _ctx.$page.props.auth.user.first_name
                      }, null, 8, ["src", "alt"])
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<span class="inline-flex rounded-md"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_MenuButton, {
                type: "button",
                class: "inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<img class="h-8 w-8 rounded-full object-cover mr-2"${ssrRenderAttr(
                      "src",
                      _ctx.$page.props.auth.user.profile_photo_url
                    )}${ssrRenderAttr("alt", _ctx.$page.props.auth.user.first_name)}${_scopeId2}><span class="flex md:block hidden"${_scopeId2}>${ssrInterpolate(_ctx.$page.props.auth.user.first_name)}</span><svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"${_scopeId2}><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"${_scopeId2}></path></svg>`);
                  } else {
                    return [
                      createVNode("img", {
                        class: "h-8 w-8 rounded-full object-cover mr-2",
                        src: _ctx.$page.props.auth.user.profile_photo_url,
                        alt: _ctx.$page.props.auth.user.first_name
                      }, null, 8, ["src", "alt"]),
                      createVNode("span", { class: "flex md:block hidden" }, toDisplayString(_ctx.$page.props.auth.user.first_name), 1),
                      (openBlock(), createBlock("svg", {
                        class: "ml-2 -mr-0.5 h-4 w-4",
                        xmlns: "http://www.w3.org/2000/svg",
                        viewBox: "0 0 20 20",
                        fill: "currentColor"
                      }, [
                        createVNode("path", {
                          "fill-rule": "evenodd",
                          d: "M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z",
                          "clip-rule": "evenodd"
                        })
                      ]))
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</span>`);
            }
            _push2(`</div>`);
          } else {
            _push2(`<div${_scopeId}>`);
            if (_ctx.$page.props.auth.user && _ctx.$page.props.auth.user.current_team) {
              _push2(ssrRenderComponent(_component_MenuButton, {
                type: "button",
                class: "inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<img class="h-8 w-8 rounded-full object-cover mr-2"${ssrRenderAttr(
                      "src",
                      _ctx.$page.props.auth.user.current_team.profile_photo_url
                    )}${ssrRenderAttr(
                      "alt",
                      _ctx.$page.props.auth.user.current_team.name
                    )}${_scopeId2}><span class="flex md:block hidden"${_scopeId2}>${ssrInterpolate(_ctx.$page.props.auth.user.current_team.name)}</span><svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"${_scopeId2}><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"${_scopeId2}></path></svg>`);
                  } else {
                    return [
                      createVNode("img", {
                        class: "h-8 w-8 rounded-full object-cover mr-2",
                        src: _ctx.$page.props.auth.user.current_team.profile_photo_url,
                        alt: _ctx.$page.props.auth.user.current_team.name
                      }, null, 8, ["src", "alt"]),
                      createVNode("span", { class: "flex md:block hidden" }, toDisplayString(_ctx.$page.props.auth.user.current_team.name), 1),
                      (openBlock(), createBlock("svg", {
                        class: "ml-2 -mr-0.5 h-4 w-4",
                        xmlns: "http://www.w3.org/2000/svg",
                        viewBox: "0 0 20 20",
                        fill: "currentColor"
                      }, [
                        createVNode("path", {
                          "fill-rule": "evenodd",
                          d: "M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z",
                          "clip-rule": "evenodd"
                        })
                      ]))
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
          }
          _push2(``);
          _push2(ssrRenderComponent(_component_MenuItems, { class: "origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                if (_ctx.hasAnyPermission([
                  "manage platform"
                ])) {
                  _push3(`<span${_scopeId2}><div class="block px-4 pt-2 text-xs text-gray-400"${_scopeId2}> Management </div>`);
                  _push3(ssrRenderComponent(_component_jet_dropdown_link, {
                    href: _ctx.route("console")
                  }, {
                    default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(` Admin Console `);
                      } else {
                        return [
                          createTextVNode(" Admin Console ")
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                  _push3(`</span>`);
                } else {
                  _push3(`<!---->`);
                }
                _push3(`<div class="border-t border-gray-100"${_scopeId2}></div>`);
                if (_ctx.$page.props.jetstream.hasTeamFeatures) {
                  _push3(`<span${_scopeId2}>`);
                  if (!_ctx.$page.props.auth.user.current_team.personal_team) {
                    _push3(`<div class="block px-4 pt-2 text-xs text-gray-400"${_scopeId2}> Personal Account </div>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  if (!_ctx.$page.props.auth.user.current_team.personal_team) {
                    _push3(`<form${_scopeId2}>`);
                    _push3(ssrRenderComponent(_component_jet_dropdown_link, { as: "button" }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(`<div class="flex items-center"${_scopeId3}><svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"${_scopeId3}><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId3}></path></svg><div${_scopeId3}>${ssrInterpolate(_ctx.$page.props.auth.user.first_name)} ${ssrInterpolate(_ctx.$page.props.auth.user.last_name)}</div></div>`);
                        } else {
                          return [
                            createVNode("div", { class: "flex items-center" }, [
                              (openBlock(), createBlock("svg", {
                                class: "mr-2 h-5 w-5 text-gray-400",
                                fill: "none",
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                "stroke-width": "2",
                                stroke: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                              ])),
                              createVNode("div", null, toDisplayString(_ctx.$page.props.auth.user.first_name) + " " + toDisplayString(_ctx.$page.props.auth.user.last_name), 1)
                            ])
                          ];
                        }
                      }),
                      _: 1
                    }, _parent3, _scopeId2));
                    _push3(`</form>`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`<div class="block px-4 pt-2 text-xs text-gray-400"${_scopeId2}> Your Teams </div>`);
                  if (_ctx.$page.props.jetstream.hasTeamFeatures) {
                    _push3(`<!--[--><!--[-->`);
                    ssrRenderList(_ctx.$page.props.auth.user.all_teams, (team) => {
                      _push3(`<form${_scopeId2}>`);
                      if (!team.personal_team) {
                        _push3(ssrRenderComponent(_component_jet_dropdown_link, { as: "button" }, {
                          default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                            if (_push4) {
                              _push4(`<div class="flex items-center"${_scopeId3}>`);
                              if (team.id == _ctx.$page.props.user.current_team_id) {
                                _push4(`<svg class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"${_scopeId3}><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId3}></path></svg>`);
                              } else {
                                _push4(`<svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"${_scopeId3}><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId3}></path></svg>`);
                              }
                              _push4(`<div${_scopeId3}>${ssrInterpolate(team.name)}</div></div>`);
                            } else {
                              return [
                                createVNode("div", { class: "flex items-center" }, [
                                  team.id == _ctx.$page.props.user.current_team_id ? (openBlock(), createBlock("svg", {
                                    key: 0,
                                    class: "mr-2 h-5 w-5 text-green-400",
                                    fill: "none",
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                  }, [
                                    createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                  ])) : (openBlock(), createBlock("svg", {
                                    key: 1,
                                    class: "mr-2 h-5 w-5 text-gray-400",
                                    fill: "none",
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                  }, [
                                    createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                  ])),
                                  createVNode("div", null, toDisplayString(team.name), 1)
                                ])
                              ];
                            }
                          }),
                          _: 2
                        }, _parent3, _scopeId2));
                      } else {
                        _push3(`<!---->`);
                      }
                      _push3(`</form>`);
                    });
                    _push3(`<!--]-->`);
                    if (_ctx.$page.props.jetstream.canCreateTeams) {
                      _push3(ssrRenderComponent(_component_jet_dropdown_link, {
                        href: _ctx.route("teams.create")
                      }, {
                        default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                          if (_push4) {
                            _push4(` Create New Team `);
                          } else {
                            return [
                              createTextVNode(" Create New Team ")
                            ];
                          }
                        }),
                        _: 1
                      }, _parent3, _scopeId2));
                    } else {
                      _push3(`<!---->`);
                    }
                    _push3(`<!--]-->`);
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(`</span>`);
                } else {
                  _push3(`<!---->`);
                }
                _push3(`<div class="border-t border-gray-100"${_scopeId2}></div><div class="block px-4 pt-2 text-xs text-gray-400"${_scopeId2}> Manage Account </div>`);
                _push3(ssrRenderComponent(_component_jet_dropdown_link, {
                  href: _ctx.route("profile.show")
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(` Account `);
                    } else {
                      return [
                        createTextVNode(" Account ")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
                if (_ctx.$page.props.jetstream.hasApiFeatures) {
                  _push3(ssrRenderComponent(_component_jet_dropdown_link, {
                    href: _ctx.route("api-tokens.index")
                  }, {
                    default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(` API Tokens `);
                      } else {
                        return [
                          createTextVNode(" API Tokens ")
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                } else {
                  _push3(`<!---->`);
                }
                _push3(`<div class="border-t border-gray-100"${_scopeId2}></div><form${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_jet_dropdown_link, { as: "button" }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(` Log Out `);
                    } else {
                      return [
                        createTextVNode(" Log Out ")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
                _push3(`</form>`);
              } else {
                return [
                  _ctx.hasAnyPermission([
                    "manage platform"
                  ]) ? (openBlock(), createBlock("span", { key: 0 }, [
                    createVNode("div", { class: "block px-4 pt-2 text-xs text-gray-400" }, " Management "),
                    createVNode(_component_jet_dropdown_link, {
                      href: _ctx.route("console")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" Admin Console ")
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ])) : createCommentVNode("", true),
                  createVNode("div", { class: "border-t border-gray-100" }),
                  _ctx.$page.props.jetstream.hasTeamFeatures ? (openBlock(), createBlock("span", { key: 1 }, [
                    !_ctx.$page.props.auth.user.current_team.personal_team ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "block px-4 pt-2 text-xs text-gray-400"
                    }, " Personal Account ")) : createCommentVNode("", true),
                    !_ctx.$page.props.auth.user.current_team.personal_team ? (openBlock(), createBlock("form", {
                      key: 1,
                      onSubmit: withModifiers(($event) => $options.switchToTeam($options.personalTeam), ["prevent"])
                    }, [
                      createVNode(_component_jet_dropdown_link, { as: "button" }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "flex items-center" }, [
                            (openBlock(), createBlock("svg", {
                              class: "mr-2 h-5 w-5 text-gray-400",
                              fill: "none",
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              "stroke-width": "2",
                              stroke: "currentColor",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                            ])),
                            createVNode("div", null, toDisplayString(_ctx.$page.props.auth.user.first_name) + " " + toDisplayString(_ctx.$page.props.auth.user.last_name), 1)
                          ])
                        ]),
                        _: 1
                      })
                    ], 40, ["onSubmit"])) : createCommentVNode("", true),
                    createVNode("div", { class: "block px-4 pt-2 text-xs text-gray-400" }, " Your Teams "),
                    _ctx.$page.props.jetstream.hasTeamFeatures ? (openBlock(), createBlock(Fragment, { key: 2 }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(_ctx.$page.props.auth.user.all_teams, (team) => {
                        return openBlock(), createBlock("form", {
                          key: team.id,
                          onSubmit: withModifiers(($event) => $options.switchToTeam(team), ["prevent"])
                        }, [
                          !team.personal_team ? (openBlock(), createBlock(_component_jet_dropdown_link, {
                            key: 0,
                            as: "button"
                          }, {
                            default: withCtx(() => [
                              createVNode("div", { class: "flex items-center" }, [
                                team.id == _ctx.$page.props.user.current_team_id ? (openBlock(), createBlock("svg", {
                                  key: 0,
                                  class: "mr-2 h-5 w-5 text-green-400",
                                  fill: "none",
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  "stroke-width": "2",
                                  stroke: "currentColor",
                                  viewBox: "0 0 24 24"
                                }, [
                                  createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                ])) : (openBlock(), createBlock("svg", {
                                  key: 1,
                                  class: "mr-2 h-5 w-5 text-gray-400",
                                  fill: "none",
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  "stroke-width": "2",
                                  stroke: "currentColor",
                                  viewBox: "0 0 24 24"
                                }, [
                                  createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                ])),
                                createVNode("div", null, toDisplayString(team.name), 1)
                              ])
                            ]),
                            _: 2
                          }, 1024)) : createCommentVNode("", true)
                        ], 40, ["onSubmit"]);
                      }), 128)),
                      _ctx.$page.props.jetstream.canCreateTeams ? (openBlock(), createBlock(_component_jet_dropdown_link, {
                        key: 0,
                        href: _ctx.route("teams.create")
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Create New Team ")
                        ]),
                        _: 1
                      }, 8, ["href"])) : createCommentVNode("", true)
                    ], 64)) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true),
                  createVNode("div", { class: "border-t border-gray-100" }),
                  createVNode("div", { class: "block px-4 pt-2 text-xs text-gray-400" }, " Manage Account "),
                  createVNode(_component_jet_dropdown_link, {
                    href: _ctx.route("profile.show")
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Account ")
                    ]),
                    _: 1
                  }, 8, ["href"]),
                  _ctx.$page.props.jetstream.hasApiFeatures ? (openBlock(), createBlock(_component_jet_dropdown_link, {
                    key: 2,
                    href: _ctx.route("api-tokens.index")
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" API Tokens ")
                    ]),
                    _: 1
                  }, 8, ["href"])) : createCommentVNode("", true),
                  createVNode("div", { class: "border-t border-gray-100" }),
                  createVNode("form", {
                    onSubmit: withModifiers($options.logout, ["prevent"])
                  }, [
                    createVNode(_component_jet_dropdown_link, { as: "button" }, {
                      default: withCtx(() => [
                        createTextVNode(" Log Out ")
                      ]),
                      _: 1
                    })
                  ], 40, ["onSubmit"])
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
        } else {
          return [
            _ctx.$page.props.auth.user.current_team && _ctx.$page.props.auth.user.current_team.personal_team ? (openBlock(), createBlock("div", {
              key: 0,
              id: "tour-step-account-management"
            }, [
              !_ctx.$page.props.jetstream.managesProfilePhotos ? (openBlock(), createBlock(_component_MenuButton, {
                key: 0,
                class: "flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
              }, {
                default: withCtx(() => [
                  createVNode("img", {
                    class: "h-8 w-8 rounded-full object-cover",
                    src: _ctx.$page.props.auth.user.profile_photo_url,
                    alt: _ctx.$page.props.auth.user.first_name
                  }, null, 8, ["src", "alt"])
                ]),
                _: 1
              })) : (openBlock(), createBlock("span", {
                key: 1,
                class: "inline-flex rounded-md"
              }, [
                createVNode(_component_MenuButton, {
                  type: "button",
                  class: "inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                }, {
                  default: withCtx(() => [
                    createVNode("img", {
                      class: "h-8 w-8 rounded-full object-cover mr-2",
                      src: _ctx.$page.props.auth.user.profile_photo_url,
                      alt: _ctx.$page.props.auth.user.first_name
                    }, null, 8, ["src", "alt"]),
                    createVNode("span", { class: "flex md:block hidden" }, toDisplayString(_ctx.$page.props.auth.user.first_name), 1),
                    (openBlock(), createBlock("svg", {
                      class: "ml-2 -mr-0.5 h-4 w-4",
                      xmlns: "http://www.w3.org/2000/svg",
                      viewBox: "0 0 20 20",
                      fill: "currentColor"
                    }, [
                      createVNode("path", {
                        "fill-rule": "evenodd",
                        d: "M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z",
                        "clip-rule": "evenodd"
                      })
                    ]))
                  ]),
                  _: 1
                })
              ]))
            ])) : (openBlock(), createBlock("div", { key: 1 }, [
              _ctx.$page.props.auth.user && _ctx.$page.props.auth.user.current_team ? (openBlock(), createBlock(_component_MenuButton, {
                key: 0,
                type: "button",
                class: "inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
              }, {
                default: withCtx(() => [
                  createVNode("img", {
                    class: "h-8 w-8 rounded-full object-cover mr-2",
                    src: _ctx.$page.props.auth.user.current_team.profile_photo_url,
                    alt: _ctx.$page.props.auth.user.current_team.name
                  }, null, 8, ["src", "alt"]),
                  createVNode("span", { class: "flex md:block hidden" }, toDisplayString(_ctx.$page.props.auth.user.current_team.name), 1),
                  (openBlock(), createBlock("svg", {
                    class: "ml-2 -mr-0.5 h-4 w-4",
                    xmlns: "http://www.w3.org/2000/svg",
                    viewBox: "0 0 20 20",
                    fill: "currentColor"
                  }, [
                    createVNode("path", {
                      "fill-rule": "evenodd",
                      d: "M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z",
                      "clip-rule": "evenodd"
                    })
                  ]))
                ]),
                _: 1
              })) : createCommentVNode("", true)
            ])),
            createVNode(Transition, {
              "enter-active-class": "transition ease-out duration-100",
              "enter-from-class": "transform opacity-0 scale-95",
              "enter-to-class": "transform opacity-100 scale-100",
              "leave-active-class": "transition ease-in duration-75",
              "leave-from-class": "transform opacity-100 scale-100",
              "leave-to-class": "transform opacity-0 scale-95"
            }, {
              default: withCtx(() => [
                createVNode(_component_MenuItems, { class: "origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" }, {
                  default: withCtx(() => [
                    _ctx.hasAnyPermission([
                      "manage platform"
                    ]) ? (openBlock(), createBlock("span", { key: 0 }, [
                      createVNode("div", { class: "block px-4 pt-2 text-xs text-gray-400" }, " Management "),
                      createVNode(_component_jet_dropdown_link, {
                        href: _ctx.route("console")
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Admin Console ")
                        ]),
                        _: 1
                      }, 8, ["href"])
                    ])) : createCommentVNode("", true),
                    createVNode("div", { class: "border-t border-gray-100" }),
                    _ctx.$page.props.jetstream.hasTeamFeatures ? (openBlock(), createBlock("span", { key: 1 }, [
                      !_ctx.$page.props.auth.user.current_team.personal_team ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "block px-4 pt-2 text-xs text-gray-400"
                      }, " Personal Account ")) : createCommentVNode("", true),
                      !_ctx.$page.props.auth.user.current_team.personal_team ? (openBlock(), createBlock("form", {
                        key: 1,
                        onSubmit: withModifiers(($event) => $options.switchToTeam($options.personalTeam), ["prevent"])
                      }, [
                        createVNode(_component_jet_dropdown_link, { as: "button" }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "flex items-center" }, [
                              (openBlock(), createBlock("svg", {
                                class: "mr-2 h-5 w-5 text-gray-400",
                                fill: "none",
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                "stroke-width": "2",
                                stroke: "currentColor",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                              ])),
                              createVNode("div", null, toDisplayString(_ctx.$page.props.auth.user.first_name) + " " + toDisplayString(_ctx.$page.props.auth.user.last_name), 1)
                            ])
                          ]),
                          _: 1
                        })
                      ], 40, ["onSubmit"])) : createCommentVNode("", true),
                      createVNode("div", { class: "block px-4 pt-2 text-xs text-gray-400" }, " Your Teams "),
                      _ctx.$page.props.jetstream.hasTeamFeatures ? (openBlock(), createBlock(Fragment, { key: 2 }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(_ctx.$page.props.auth.user.all_teams, (team) => {
                          return openBlock(), createBlock("form", {
                            key: team.id,
                            onSubmit: withModifiers(($event) => $options.switchToTeam(team), ["prevent"])
                          }, [
                            !team.personal_team ? (openBlock(), createBlock(_component_jet_dropdown_link, {
                              key: 0,
                              as: "button"
                            }, {
                              default: withCtx(() => [
                                createVNode("div", { class: "flex items-center" }, [
                                  team.id == _ctx.$page.props.user.current_team_id ? (openBlock(), createBlock("svg", {
                                    key: 0,
                                    class: "mr-2 h-5 w-5 text-green-400",
                                    fill: "none",
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                  }, [
                                    createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                  ])) : (openBlock(), createBlock("svg", {
                                    key: 1,
                                    class: "mr-2 h-5 w-5 text-gray-400",
                                    fill: "none",
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    "stroke-width": "2",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24"
                                  }, [
                                    createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                  ])),
                                  createVNode("div", null, toDisplayString(team.name), 1)
                                ])
                              ]),
                              _: 2
                            }, 1024)) : createCommentVNode("", true)
                          ], 40, ["onSubmit"]);
                        }), 128)),
                        _ctx.$page.props.jetstream.canCreateTeams ? (openBlock(), createBlock(_component_jet_dropdown_link, {
                          key: 0,
                          href: _ctx.route("teams.create")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Create New Team ")
                          ]),
                          _: 1
                        }, 8, ["href"])) : createCommentVNode("", true)
                      ], 64)) : createCommentVNode("", true)
                    ])) : createCommentVNode("", true),
                    createVNode("div", { class: "border-t border-gray-100" }),
                    createVNode("div", { class: "block px-4 pt-2 text-xs text-gray-400" }, " Manage Account "),
                    createVNode(_component_jet_dropdown_link, {
                      href: _ctx.route("profile.show")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" Account ")
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    _ctx.$page.props.jetstream.hasApiFeatures ? (openBlock(), createBlock(_component_jet_dropdown_link, {
                      key: 2,
                      href: _ctx.route("api-tokens.index")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" API Tokens ")
                      ]),
                      _: 1
                    }, 8, ["href"])) : createCommentVNode("", true),
                    createVNode("div", { class: "border-t border-gray-100" }),
                    createVNode("form", {
                      onSubmit: withModifiers($options.logout, ["prevent"])
                    }, [
                      createVNode(_component_jet_dropdown_link, { as: "button" }, {
                        default: withCtx(() => [
                          createTextVNode(" Log Out ")
                        ]),
                        _: 1
                      })
                    ], 40, ["onSubmit"])
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
  } else {
    _push(`<!---->`);
  }
  if (!_ctx.$page.props.auth.user.first_name) {
    _push(ssrRenderComponent(_component_Menu, {
      as: "div",
      class: "ml-3 relative"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_MenuButton, { class: "inline-flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition" }, null, _parent2, _scopeId));
          _push2(`<div class="inline-flex"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Link, {
            href: "/login",
            class: "px-3 py-2 whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Login `);
              } else {
                return [
                  createTextVNode(" Login ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_Link, {
            href: "/register",
            type: "button",
            class: "inline-flex ml-3 items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Register `);
              } else {
                return [
                  createTextVNode(" Register ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          return [
            createVNode(_component_MenuButton, { class: "inline-flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition" }),
            createVNode("div", { class: "inline-flex" }, [
              createVNode(_component_Link, {
                href: "/login",
                class: "px-3 py-2 whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900"
              }, {
                default: withCtx(() => [
                  createTextVNode(" Login ")
                ]),
                _: 1
              }),
              createVNode(_component_Link, {
                href: "/register",
                type: "button",
                class: "inline-flex ml-3 items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
              }, {
                default: withCtx(() => [
                  createTextVNode(" Register ")
                ]),
                _: 1
              })
            ])
          ];
        }
      }),
      _: 1
    }, _parent));
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div></div><main class="flex-1 relative overflow-y-hidden bg-white focus:outline-none">`);
  ssrRenderSlot(_ctx.$slots, "header", {}, null, _push, _parent);
  ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
  _push(`</main></div></div><!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/AppLayout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const AppLayout = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  AppLayout as A,
  Create as C,
  FileSystemBrowser as F,
  JetDropdownLink as J,
  SpectraEditor as S,
  Validation as V,
  FileDetails as a
};
