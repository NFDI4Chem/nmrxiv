import { mergeProps, useSSRContext, ref, resolveComponent, withCtx, createVNode, openBlock, createBlock, createTextVNode, toDisplayString, Fragment, renderList, Transition, createCommentVNode } from "vue";
import { Dialog, DialogPanel, Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems, Popover, PopoverButton, PopoverGroup, PopoverPanel, TransitionChild, TransitionRoot } from "@headlessui/vue";
import throttle from "lodash/throttle.js";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import { ChevronDownIcon, QueueListIcon, Squares2X2Icon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { P as ProjectCard } from "./ProjectCard-c2ec56ff.mjs";
import { D as Dropdown } from "./AnnouncementBanner-3ebe3621.mjs";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { P as Pagination } from "./Pagination-5085a5ef.mjs";
import pickBy from "lodash/pickBy.js";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@vueuse/core";
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
import "popper.js";
const _sfc_main$1 = {
  components: {
    Dropdown
  },
  props: {
    modelValue: String,
    maxWidth: {
      type: Number,
      default: 300
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex items-center" }, _attrs))}><div class="flex w-full bg-white shadow rounded-full"><input class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Search"${ssrRenderAttr("value", $props.modelValue)}></div><button class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500" type="button"> Reset </button></div>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/ProjectSearch.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const ProjectSearch = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    AppLayout,
    ProjectCard,
    Dialog,
    DialogPanel,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
    TransitionChild,
    TransitionRoot,
    XMarkIcon,
    ChevronDownIcon,
    QueueListIcon,
    Link,
    ProjectSearch,
    Pagination,
    Squares2X2Icon
  },
  props: {
    projects: {
      default: [],
      type: Object
    },
    filters: {
      default: {
        search: "",
        sort: "newest",
        mode: "grid"
      },
      type: Object
    }
  },
  data() {
    return {
      sortOptions: [
        { name: "Creation", value: "creation", current: true },
        { name: "Newest", value: "newest", current: false }
      ],
      open: ref(false),
      form: {
        search: this.filters.search,
        sort: "newest",
        mode: "grid"
      }
    };
  },
  mounted() {
    if (this.filters) {
      if (this.filters.search == null) {
        this.filters.search = "";
      }
      if (this.filters.mode == null) {
        this.filters.mode = "grid";
      }
      if (this.filters.sort == null) {
        this.filters.sort = "newest";
      }
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get("/projects", pickBy(this.form), {
          preserveState: true
        });
      }, 150)
    }
  },
  methods: {
    reset() {
      this.form = {
        search: "",
        sort: "newest",
        mode: "grid"
      };
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_project_search = resolveComponent("project-search");
  const _component_Menu = resolveComponent("Menu");
  const _component_MenuButton = resolveComponent("MenuButton");
  const _component_ChevronDownIcon = resolveComponent("ChevronDownIcon");
  const _component_MenuItems = resolveComponent("MenuItems");
  const _component_MenuItem = resolveComponent("MenuItem");
  const _component_QueueListIcon = resolveComponent("QueueListIcon");
  const _component_Squares2X2Icon = resolveComponent("Squares2X2Icon");
  const _component_project_card = resolveComponent("project-card");
  const _component_Pagination = resolveComponent("Pagination");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Projects" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="relative border-b border-zinc-900/5"${_scopeId}><div class="relative pt-10 dark:border-white/5 mx-8 py-12 sm:py-12"${_scopeId}><div class="absolute inset-0 bg-gradient-to-r from-[#36b49f] to-[#DBFF75] opacity-40 [mask-image:radial-gradient(farthest-side_at_top,white,transparent)] dark:from-[#36b49f]/30 dark:to-[#DBFF75]/30 dark:opacity-100"${_scopeId}><svg aria-hidden="true" class="absolute inset-x-0 inset-y-[-50%] h-[200%] w-full skew-y-[-18deg] fill-black/40 stroke-black/50 mix-blend-overlay dark:fill-white/2.5 dark:stroke-white/5"${_scopeId}><defs${_scopeId}><pattern id=":r99:" width="72" height="56" patternUnits="userSpaceOnUse" x="-12" y="4"${_scopeId}><path d="M.5 56V.5H72" fill="none"${_scopeId}></path></pattern></defs><rect width="100%" height="100%" stroke-width="0" fill="url(#:r99:)"${_scopeId}></rect><svg x="-12" y="4" class="overflow-visible"${_scopeId}><rect stroke-width="0" width="73" height="57" x="288" y="168"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="144" y="56"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="504" y="168"${_scopeId}></rect><rect stroke-width="0" width="73" height="57" x="720" y="336"${_scopeId}></rect></svg></svg></div><div class="text-4xl mb-3 font-bold tracking-tight text-gray-900"${_scopeId}> Browse Projects </div><p${_scopeId}> Explore, analyse, and share raw spectral data and assignments. Learn more about <a class="text-teal-900" href="https://docs.nmrxiv.org/docs/introduction/intro" target="_blank"${_scopeId}>projects</a>. </p></div></div>`);
      } else {
        return [
          createVNode("div", { class: "relative border-b border-zinc-900/5" }, [
            createVNode("div", { class: "relative pt-10 dark:border-white/5 mx-8 py-12 sm:py-12" }, [
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
              createVNode("div", { class: "text-4xl mb-3 font-bold tracking-tight text-gray-900" }, " Browse Projects "),
              createVNode("p", null, [
                createTextVNode(" Explore, analyse, and share raw spectral data and assignments. Learn more about "),
                createVNode("a", {
                  class: "text-teal-900",
                  href: "https://docs.nmrxiv.org/docs/introduction/intro",
                  target: "_blank"
                }, "projects"),
                createTextVNode(". ")
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div${_scopeId}><div class="bg-white"${_scopeId}><section aria-labelledby="filter-heading"${_scopeId}><div class="bg-gray-100 border-t border-b"${_scopeId}><div class="mx-auto py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-8"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_project_search, {
          modelValue: $data.form.search,
          "onUpdate:modelValue": ($event) => $data.form.search = $event,
          class: "mr-4 w-full",
          onReset: $options.reset
        }, null, _parent2, _scopeId));
        _push2(`</div></div></section></div></div><div class="min-h-[calc(100vh-500px)] px-12 mb-24 mx-auto"${_scopeId}><div class="relative border-gray-200 pt-4"${_scopeId}><div class="mx-auto flex items-center justify-between"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Menu, {
          as: "div",
          class: "relative inline-block text-left"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<div${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_MenuButton, { class: "group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900" }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Sort by: <span class="capitalize font-black text-gray-900"${_scopeId3}>${ssrInterpolate($data.form.sort)}</span>`);
                    _push4(ssrRenderComponent(_component_ChevronDownIcon, {
                      class: "flex-shrink-0 -mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500",
                      "aria-hidden": "true"
                    }, null, _parent4, _scopeId3));
                  } else {
                    return [
                      createTextVNode(" Sort by: "),
                      createVNode("span", { class: "capitalize font-black text-gray-900" }, toDisplayString($data.form.sort), 1),
                      createVNode(_component_ChevronDownIcon, {
                        class: "flex-shrink-0 -mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500",
                        "aria-hidden": "true"
                      })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`</div>`);
              _push3(ssrRenderComponent(_component_MenuItems, { class: "origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(`<div class="py-1"${_scopeId3}><!--[-->`);
                    ssrRenderList($data.sortOptions, (option) => {
                      _push4(ssrRenderComponent(_component_MenuItem, {
                        key: option.name
                      }, {
                        default: withCtx(({ active }, _push5, _parent5, _scopeId4) => {
                          if (_push5) {
                            _push5(`<a class="${ssrRenderClass([
                              option.current ? "font-medium text-gray-900" : "text-gray-500",
                              active ? "bg-gray-100" : "",
                              "block px-4 py-2 text-sm cursor-pointer"
                            ])}"${_scopeId4}>${ssrInterpolate(option.name)}</a>`);
                          } else {
                            return [
                              createVNode("a", {
                                class: [
                                  option.current ? "font-medium text-gray-900" : "text-gray-500",
                                  active ? "bg-gray-100" : "",
                                  "block px-4 py-2 text-sm cursor-pointer"
                                ],
                                onClick: ($event) => $data.form.sort = option.value
                              }, toDisplayString(option.name), 11, ["onClick"])
                            ];
                          }
                        }),
                        _: 2
                      }, _parent4, _scopeId3));
                    });
                    _push4(`<!--]--></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "py-1" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList($data.sortOptions, (option) => {
                          return openBlock(), createBlock(_component_MenuItem, {
                            key: option.name
                          }, {
                            default: withCtx(({ active }) => [
                              createVNode("a", {
                                class: [
                                  option.current ? "font-medium text-gray-900" : "text-gray-500",
                                  active ? "bg-gray-100" : "",
                                  "block px-4 py-2 text-sm cursor-pointer"
                                ],
                                onClick: ($event) => $data.form.sort = option.value
                              }, toDisplayString(option.name), 11, ["onClick"])
                            ]),
                            _: 2
                          }, 1024);
                        }), 128))
                      ])
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode("div", null, [
                  createVNode(_component_MenuButton, { class: "group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900" }, {
                    default: withCtx(() => [
                      createTextVNode(" Sort by: "),
                      createVNode("span", { class: "capitalize font-black text-gray-900" }, toDisplayString($data.form.sort), 1),
                      createVNode(_component_ChevronDownIcon, {
                        class: "flex-shrink-0 -mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500",
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
                    createVNode(_component_MenuItems, { class: "origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" }, {
                      default: withCtx(() => [
                        createVNode("div", { class: "py-1" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList($data.sortOptions, (option) => {
                            return openBlock(), createBlock(_component_MenuItem, {
                              key: option.name
                            }, {
                              default: withCtx(({ active }) => [
                                createVNode("a", {
                                  class: [
                                    option.current ? "font-medium text-gray-900" : "text-gray-500",
                                    active ? "bg-gray-100" : "",
                                    "block px-4 py-2 text-sm cursor-pointer"
                                  ],
                                  onClick: ($event) => $data.form.sort = option.value
                                }, toDisplayString(option.name), 11, ["onClick"])
                              ]),
                              _: 2
                            }, 1024);
                          }), 128))
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
        }, _parent2, _scopeId));
        _push2(`<div class="float-right"${_scopeId}><span class="relative z-0 inline-flex shadow-sm rounded-md"${_scopeId}><button type="button" class="${ssrRenderClass([
          $props.filters.mode == "list" ? "bg-gray-300 text-gray-900" : "",
          "relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10"
        ])}"${_scopeId}><span class="sr-only"${_scopeId}>List</span>`);
        _push2(ssrRenderComponent(_component_QueueListIcon, {
          class: "h-5 w-5",
          "aria-hidden": "true"
        }, null, _parent2, _scopeId));
        _push2(`</button><button type="button" class="${ssrRenderClass([
          $props.filters.mode == "grid" ? "bg-gray-300 text-gray-900" : "",
          "-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        ])}"${_scopeId}><span class="sr-only"${_scopeId}>Grid</span>`);
        _push2(ssrRenderComponent(_component_Squares2X2Icon, {
          class: "h-5 w-5",
          "aria-hidden": "true"
        }, null, _parent2, _scopeId));
        _push2(`</button></span></div></div></div>`);
        if ($props.projects.meta.total > 0) {
          _push2(`<div${_scopeId}><div class="border-t mt-3 border-gray-100"${_scopeId}><h2 class="text-gray-600 text-md mt-4 font-bold"${_scopeId}> Results (${ssrInterpolate($props.projects.meta.total)}) </h2></div>`);
          if ($props.filters.mode == "grid") {
            _push2(`<div${_scopeId}><div class="mt-4 mx-auto grid gap-8 lg:grid-cols-4 2xl:grid-cols-6 w-full"${_scopeId}><!--[-->`);
            ssrRenderList($props.projects.data.filter(
              (p) => p.owner
            ), (project) => {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_project_card, {
                mode: $props.filters.mode,
                project
              }, null, _parent2, _scopeId));
              _push2(`</span>`);
            });
            _push2(`<!--]--></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          if ($props.filters.mode == "list") {
            _push2(`<div${_scopeId}><div class="mt-4 bg-white shadow overflow-hidden sm:rounded-md"${_scopeId}><ul role="list" class="divide-y border rounded-md divide-gray-200"${_scopeId}><!--[-->`);
            ssrRenderList($props.projects.data.filter(
              (p) => p.owner
            ), (project) => {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_project_card, {
                mode: $props.filters.mode ? $props.filters.mode : "grid",
                project
              }, null, _parent2, _scopeId));
              _push2(`</span>`);
            });
            _push2(`<!--]--></ul></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          if ($props.projects.meta.total > $props.projects.meta.per_page) {
            _push2(`<div class="py-10"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Pagination, {
              links: $props.projects.meta.links
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<div${_scopeId}><div class="text-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-5"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"${_scopeId}><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"${_scopeId}></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No projects </h3></div></div>`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", null, [
            createVNode("div", { class: "bg-white" }, [
              createVNode("section", { "aria-labelledby": "filter-heading" }, [
                createVNode("div", { class: "bg-gray-100 border-t border-b" }, [
                  createVNode("div", { class: "mx-auto py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-8" }, [
                    createVNode(_component_project_search, {
                      modelValue: $data.form.search,
                      "onUpdate:modelValue": ($event) => $data.form.search = $event,
                      class: "mr-4 w-full",
                      onReset: $options.reset
                    }, null, 8, ["modelValue", "onUpdate:modelValue", "onReset"])
                  ])
                ])
              ])
            ])
          ]),
          createVNode("div", { class: "min-h-[calc(100vh-500px)] px-12 mb-24 mx-auto" }, [
            createVNode("div", { class: "relative border-gray-200 pt-4" }, [
              createVNode("div", { class: "mx-auto flex items-center justify-between" }, [
                createVNode(_component_Menu, {
                  as: "div",
                  class: "relative inline-block text-left"
                }, {
                  default: withCtx(() => [
                    createVNode("div", null, [
                      createVNode(_component_MenuButton, { class: "group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900" }, {
                        default: withCtx(() => [
                          createTextVNode(" Sort by: "),
                          createVNode("span", { class: "capitalize font-black text-gray-900" }, toDisplayString($data.form.sort), 1),
                          createVNode(_component_ChevronDownIcon, {
                            class: "flex-shrink-0 -mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500",
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
                        createVNode(_component_MenuItems, { class: "origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "py-1" }, [
                              (openBlock(true), createBlock(Fragment, null, renderList($data.sortOptions, (option) => {
                                return openBlock(), createBlock(_component_MenuItem, {
                                  key: option.name
                                }, {
                                  default: withCtx(({ active }) => [
                                    createVNode("a", {
                                      class: [
                                        option.current ? "font-medium text-gray-900" : "text-gray-500",
                                        active ? "bg-gray-100" : "",
                                        "block px-4 py-2 text-sm cursor-pointer"
                                      ],
                                      onClick: ($event) => $data.form.sort = option.value
                                    }, toDisplayString(option.name), 11, ["onClick"])
                                  ]),
                                  _: 2
                                }, 1024);
                              }), 128))
                            ])
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                }),
                createVNode("div", { class: "float-right" }, [
                  createVNode("span", { class: "relative z-0 inline-flex shadow-sm rounded-md" }, [
                    createVNode("button", {
                      type: "button",
                      class: [
                        $props.filters.mode == "list" ? "bg-gray-300 text-gray-900" : "",
                        "relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10"
                      ],
                      onClick: ($event) => $data.form.mode = "list"
                    }, [
                      createVNode("span", { class: "sr-only" }, "List"),
                      createVNode(_component_QueueListIcon, {
                        class: "h-5 w-5",
                        "aria-hidden": "true"
                      })
                    ], 10, ["onClick"]),
                    createVNode("button", {
                      type: "button",
                      class: [
                        $props.filters.mode == "grid" ? "bg-gray-300 text-gray-900" : "",
                        "-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                      ],
                      onClick: ($event) => $data.form.mode = "grid"
                    }, [
                      createVNode("span", { class: "sr-only" }, "Grid"),
                      createVNode(_component_Squares2X2Icon, {
                        class: "h-5 w-5",
                        "aria-hidden": "true"
                      })
                    ], 10, ["onClick"])
                  ])
                ])
              ])
            ]),
            $props.projects.meta.total > 0 ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("div", { class: "border-t mt-3 border-gray-100" }, [
                createVNode("h2", { class: "text-gray-600 text-md mt-4 font-bold" }, " Results (" + toDisplayString($props.projects.meta.total) + ") ", 1)
              ]),
              $props.filters.mode == "grid" ? (openBlock(), createBlock("div", { key: 0 }, [
                createVNode("div", { class: "mt-4 mx-auto grid gap-8 lg:grid-cols-4 2xl:grid-cols-6 w-full" }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($props.projects.data.filter(
                    (p) => p.owner
                  ), (project) => {
                    return openBlock(), createBlock("span", {
                      key: project.id
                    }, [
                      createVNode(_component_project_card, {
                        mode: $props.filters.mode,
                        project
                      }, null, 8, ["mode", "project"])
                    ]);
                  }), 128))
                ])
              ])) : createCommentVNode("", true),
              $props.filters.mode == "list" ? (openBlock(), createBlock("div", { key: 1 }, [
                createVNode("div", { class: "mt-4 bg-white shadow overflow-hidden sm:rounded-md" }, [
                  createVNode("ul", {
                    role: "list",
                    class: "divide-y border rounded-md divide-gray-200"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList($props.projects.data.filter(
                      (p) => p.owner
                    ), (project) => {
                      return openBlock(), createBlock("span", {
                        key: project.id
                      }, [
                        createVNode(_component_project_card, {
                          mode: $props.filters.mode ? $props.filters.mode : "grid",
                          project
                        }, null, 8, ["mode", "project"])
                      ]);
                    }), 128))
                  ])
                ])
              ])) : createCommentVNode("", true),
              $props.projects.meta.total > $props.projects.meta.per_page ? (openBlock(), createBlock("div", {
                key: 2,
                class: "py-10"
              }, [
                createVNode(_component_Pagination, {
                  links: $props.projects.meta.links
                }, null, 8, ["links"])
              ])) : createCommentVNode("", true)
            ])) : (openBlock(), createBlock("div", { key: 1 }, [
              createVNode("div", { class: "text-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-5" }, [
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
                createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No projects ")
              ])
            ]))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Projects.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Projects = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Projects as default
};
