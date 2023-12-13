import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { Head, Link, router } from "@inertiajs/vue3";
import { D as Dropdown } from "./AnnouncementBanner-3ebe3621.mjs";
import { mergeProps, useSSRContext, resolveComponent, withCtx, createTextVNode, toDisplayString, openBlock, createBlock, createCommentVNode, createVNode, ref, Fragment, renderList, Transition } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderClass, ssrRenderStyle, ssrRenderComponent, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { LockClosedIcon, LockOpenIcon, ArrowDownTrayIcon, EnvelopeIcon, PencilIcon, ScaleIcon, EllipsisVerticalIcon, ChevronDownIcon, QueueListIcon, Squares2X2Icon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems, Dialog, DialogPanel, Disclosure, DisclosureButton, DisclosurePanel, Popover, PopoverButton, PopoverGroup, PopoverPanel, TransitionChild, TransitionRoot } from "@headlessui/vue";
import throttle from "lodash/throttle.js";
import pickBy from "lodash/pickBy.js";
import { P as Pagination } from "./Pagination-5085a5ef.mjs";
import { XMarkIcon } from "@heroicons/vue/24/outline";
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
const _sfc_main$2 = {
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
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex items-center" }, _attrs))}><div class="flex w-full bg-white shadow rounded-full"><input class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Search"${ssrRenderAttr("value", $props.modelValue)}></div><button class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500" type="button"> Reset </button></div>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/DatasetSearch.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const DatasetSearch = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$1 = {
  components: {
    Head,
    Link,
    LockClosedIcon,
    LockOpenIcon,
    ArrowDownTrayIcon,
    EnvelopeIcon,
    PencilIcon,
    ScaleIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    EllipsisVerticalIcon
  },
  props: ["dataset", "mode"],
  setup() {
  },
  data() {
    return {};
  },
  computed: {
    url() {
      return String(this.$page.props.url);
    }
  },
  methods: {
    toggleUpVote() {
      if (this.$page.props.auth.user.username && this.$page.props.auth.user.username != "") {
        const url = "/projects/" + this.dataset.id + "/toggleUpVote";
        axios.get(url).catch((err) => {
          if (err.response.status !== 200 || err.response.status !== 201) {
            throw new Error(
              `API call failed with status code: ${err.response.status} after multiple attempts`
            );
          }
        }).then(function(response) {
          router.reload({ only: ["datasets"] });
        });
      } else {
        this.$inertia.visit(route("login"));
      }
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  const _component_ScaleIcon = resolveComponent("ScaleIcon");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  if ($props.dataset) {
    _push(`<div class="hover:shadow-lg">`);
    if ($props.mode == "mini" || $props.mode == "grid") {
      _push(`<div><div class="flex flex-col rounded-lg shadow-lg"><div class="relative rounded-t lg:h-32 xl:h-32">`);
      if ($props.dataset.dataset_photo_url) {
        _push(`<div class="flex-shrink-0 h-32 p-3 border rounded-t border-gray-100"><img${ssrRenderAttr("src", $props.dataset.dataset_photo_url)} alt="" class="w-full h-full object-center object-cover"></div>`);
      } else {
        _push(`<div class="flex-shrink-0 h-32 pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"></div>`);
      }
      _push(`</div><div class="${ssrRenderClass([
        $props.mode != "mini" ? "" : "rounded-b-lg",
        "flex-1 bg-white flex flex-col justify-between"
      ])}"><div style="${ssrRenderStyle({ "min-height": "100px", "max-height": "168px" })}" class="flex-1 p-3">`);
      _push(ssrRenderComponent(_component_Link, {
        href: $props.dataset.public_url,
        class: "block"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            if ($props.dataset.identifier) {
              _push2(`<small class="text-gray-500"${_scopeId}>#${ssrInterpolate($props.dataset.identifier)}</small>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<p class="text-lg font-black text-gray-900 line-clamp-2"${_scopeId}>${ssrInterpolate($props.dataset.name)} `);
            if ($props.dataset.type) {
              _push2(`<span${_scopeId}>(${ssrInterpolate($props.dataset.type.replace(/,\s*$/, ""))})</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</p><p class="mt-0 text-sm text-gray-800 line-clamp-4"${_scopeId}><small class="text-gray-400"${_scopeId}>Project:</small><br${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Link, {
              href: $props.dataset.project.public_url,
              class: "hover:underline hover:text-teal-900"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate($props.dataset.project.name)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString($props.dataset.project.name), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(` / `);
            _push2(ssrRenderComponent(_component_Link, {
              class: "hover:underline hover:text-teal-900",
              href: $props.dataset.study.public_url
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate($props.dataset.study.name)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString($props.dataset.study.name), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</p>`);
          } else {
            return [
              $props.dataset.identifier ? (openBlock(), createBlock("small", {
                key: 0,
                class: "text-gray-500"
              }, "#" + toDisplayString($props.dataset.identifier), 1)) : createCommentVNode("", true),
              createVNode("p", { class: "text-lg font-black text-gray-900 line-clamp-2" }, [
                createTextVNode(toDisplayString($props.dataset.name) + " ", 1),
                $props.dataset.type ? (openBlock(), createBlock("span", { key: 0 }, "(" + toDisplayString($props.dataset.type.replace(/,\s*$/, "")) + ")", 1)) : createCommentVNode("", true)
              ]),
              createVNode("p", { class: "mt-0 text-sm text-gray-800 line-clamp-4" }, [
                createVNode("small", { class: "text-gray-400" }, "Project:"),
                createVNode("br"),
                createVNode(_component_Link, {
                  href: $props.dataset.project.public_url,
                  class: "hover:underline hover:text-teal-900"
                }, {
                  default: withCtx(() => [
                    createTextVNode(toDisplayString($props.dataset.project.name), 1)
                  ]),
                  _: 1
                }, 8, ["href"]),
                createTextVNode(" / "),
                createVNode(_component_Link, {
                  class: "hover:underline hover:text-teal-900",
                  href: $props.dataset.study.public_url
                }, {
                  default: withCtx(() => [
                    createTextVNode(toDisplayString($props.dataset.study.name), 1)
                  ]),
                  _: 1
                }, 8, ["href"])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div>`);
      if ($props.mode != "mini") {
        _push(`<div class="p-3 rounded-b-lg bg-white border-t flex"><div class="flex-0.5 self-center align-middle"><img class="h-7 w-7 rounded-full"${ssrRenderAttr("src", $props.dataset.owner.profile_photo_url)}></div><div class="flex-auto pl-4"><p class="text-xs font-xs font-semibold text-black">`);
        _push(ssrRenderComponent(_component_Link, null, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate($props.dataset.owner.first_name)} ${ssrInterpolate($props.dataset.owner.last_name)}`);
            } else {
              return [
                createTextVNode(toDisplayString($props.dataset.owner.first_name) + " " + toDisplayString($props.dataset.owner.last_name), 1)
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</p><div class="flex-1 space-x-1 text-xs font-xs text-gray-500"><time datetime="2020-03-16">${ssrInterpolate(_ctx.formatDate($props.dataset.created_at))}</time></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div>`);
    } else {
      _push(`<!---->`);
    }
    if ($props.mode == "list") {
      _push(`<div><li class="flex border-b bprder-gray-100"><div class="flex-shrink-0">`);
      if ($props.dataset.dataset_photo_url) {
        _push(`<div class="flex-shrink-0 w-64 border-r h-36 p-3"><img${ssrRenderAttr("src", $props.dataset.dataset_photo_url)} alt="" class="w-full h-full object-center object-cover"></div>`);
      } else {
        _push(`<div class="flex-shrink-0 h-36 w-64 border-r pattern-diagonal-lines pattern-gray-400 pattern-bg-white pattern-size-2 pattern-opacity-20 border-b border-gray-400"></div>`);
      }
      _push(`</div><div class="flex-1 flex flex-col px-4 py-2 sm:px-6 justify-between"><div class="flex items-center justify-between"><div>`);
      if ($props.dataset.identifier) {
        _push(`<small class="text-gray-500">#${ssrInterpolate($props.dataset.identifier)}</small>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<p class="text-lg font-black text-gray-900 line-clamp-2 font-black">`);
      _push(ssrRenderComponent(_component_Link, {
        href: $props.dataset.public_url,
        class: "block"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate($props.dataset.name)}`);
          } else {
            return [
              createTextVNode(toDisplayString($props.dataset.name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</p><p class="mt-0 text-sm text-gray-800 line-clamp-4"><small class="text-gray-400">Project:</small><br>`);
      _push(ssrRenderComponent(_component_Link, {
        href: $props.dataset.project.public_url,
        class: "hover:underline hover:text-teal-900"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate($props.dataset.project.name)}`);
          } else {
            return [
              createTextVNode(toDisplayString($props.dataset.project.name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(` / `);
      _push(ssrRenderComponent(_component_Link, {
        class: "hover:underline hover:text-teal-900",
        href: $props.dataset.study.public_url
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate($props.dataset.study.name)}`);
          } else {
            return [
              createTextVNode(toDisplayString($props.dataset.study.name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</p></div></div><div class="mt-2 sm:flex sm:justify-between"><div class="sm:flex"><p class="flex items-center text-sm text-gray-500"><img class="h-7 w-5 mr-2 rounded-full"${ssrRenderAttr("src", $props.dataset.owner.profile_photo_url)}> ${ssrInterpolate($props.dataset.owner.first_name)} ${ssrInterpolate($props.dataset.owner.last_name)}</p>`);
      if ($props.dataset.license) {
        _push(`<p class="flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-3">`);
        _push(ssrRenderComponent(_component_ScaleIcon, { class: "text-gray-400 h-5 w-5 mr-1.5" }, null, _parent));
        _push(` ${ssrInterpolate($props.dataset.license.title)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"><svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg><p><time datetime="2020-03-16">${ssrInterpolate(_ctx.formatDate($props.dataset.created_at))}</time></p></div></div></div></li></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/DatasetCard.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const DatasetCard = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    AppLayout,
    Link,
    DatasetSearch,
    Pagination,
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
    Squares2X2Icon,
    DatasetCard
  },
  props: {
    datasets: {
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
        this.$inertia.get("/spectra", pickBy(this.form), {
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
  const _component_dataset_search = resolveComponent("dataset-search");
  const _component_Menu = resolveComponent("Menu");
  const _component_MenuButton = resolveComponent("MenuButton");
  const _component_ChevronDownIcon = resolveComponent("ChevronDownIcon");
  const _component_MenuItems = resolveComponent("MenuItems");
  const _component_MenuItem = resolveComponent("MenuItem");
  const _component_QueueListIcon = resolveComponent("QueueListIcon");
  const _component_Squares2X2Icon = resolveComponent("Squares2X2Icon");
  const _component_dataset_card = resolveComponent("dataset-card");
  const _component_Pagination = resolveComponent("Pagination");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Spectra" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white mb-0"${_scopeId}><div class="px-8"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}><div class="text-4xl mb-3 font-bold tracking-tight text-gray-900"${_scopeId}> Spectra </div><p${_scopeId}> Explore, analyze, and share raw spectra and assignments. Learn more about <a class="text-teal-900" href="https://docs.nmrxiv.org/docs/introduction/intro" target="_blank"${_scopeId}>spectra</a>. </p></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white mb-0" }, [
            createVNode("div", { class: "px-8" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode("div", { class: "text-4xl mb-3 font-bold tracking-tight text-gray-900" }, " Spectra "),
                  createVNode("p", null, [
                    createTextVNode(" Explore, analyze, and share raw spectra and assignments. Learn more about "),
                    createVNode("a", {
                      class: "text-teal-900",
                      href: "https://docs.nmrxiv.org/docs/introduction/intro",
                      target: "_blank"
                    }, "spectra"),
                    createTextVNode(". ")
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
        _push2(`<div${_scopeId}><div class="bg-white"${_scopeId}><section aria-labelledby="filter-heading"${_scopeId}><div class="bg-gray-100 border-t border-b"${_scopeId}><div class="mx-auto py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-8"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_dataset_search, {
          modelValue: $data.form.search,
          "onUpdate:modelValue": ($event) => $data.form.search = $event,
          class: "mr-4 w-full",
          onReset: $options.reset
        }, null, _parent2, _scopeId));
        _push2(`</div></div></section></div></div><div class="min-h-[calc(100vh-500px)] px-12 mb-24 mx-auto"${_scopeId}><div class="relative border-gray-200 pt-4"${_scopeId}><div class="mx-auto flex items-center justify-between"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Menu, {
          as: "div",
          class: "relative inline-block text-left z-10"
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
          "relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
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
        if ($props.datasets.meta.total > 0) {
          _push2(`<div${_scopeId}><div class="border-t mt-3 border-gray-100"${_scopeId}><h2 class="text-gray-600 text-md mt-4 font-bold"${_scopeId}> Results (${ssrInterpolate($props.datasets.meta.total)}) </h2></div>`);
          if ($props.filters.mode == "grid") {
            _push2(`<div${_scopeId}><div class="mt-4 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl"${_scopeId}><!--[-->`);
            ssrRenderList($props.datasets.data, (dataset) => {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_dataset_card, {
                mode: $props.filters.mode,
                dataset
              }, null, _parent2, _scopeId));
              _push2(`</span>`);
            });
            _push2(`<!--]--></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          if ($props.filters.mode == "list") {
            _push2(`<div${_scopeId}><div class="mt-4 bg-white shadow overflow-hidden sm:rounded-md"${_scopeId}><ul role="list" class="divide-y border rounded-md divide-gray-200"${_scopeId}><!--[-->`);
            ssrRenderList($props.datasets.data, (dataset) => {
              _push2(`<span${_scopeId}>`);
              _push2(ssrRenderComponent(_component_dataset_card, {
                mode: $props.filters.mode ? $props.filters.mode : "grid",
                dataset
              }, null, _parent2, _scopeId));
              _push2(`</span>`);
            });
            _push2(`<!--]--></ul></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          if ($props.datasets.meta.total > $props.datasets.meta.per_page) {
            _push2(`<div class="py-10"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Pagination, {
              links: $props.datasets.meta.links
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<div${_scopeId}><div class="text-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-5"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"${_scopeId}><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"${_scopeId}></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No datasets </h3></div></div>`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", null, [
            createVNode("div", { class: "bg-white" }, [
              createVNode("section", { "aria-labelledby": "filter-heading" }, [
                createVNode("div", { class: "bg-gray-100 border-t border-b" }, [
                  createVNode("div", { class: "mx-auto py-3 px-4 sm:flex sm:items-center sm:px-6 lg:px-8" }, [
                    createVNode(_component_dataset_search, {
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
                  class: "relative inline-block text-left z-10"
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
                        "relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
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
            $props.datasets.meta.total > 0 ? (openBlock(), createBlock("div", { key: 0 }, [
              createVNode("div", { class: "border-t mt-3 border-gray-100" }, [
                createVNode("h2", { class: "text-gray-600 text-md mt-4 font-bold" }, " Results (" + toDisplayString($props.datasets.meta.total) + ") ", 1)
              ]),
              $props.filters.mode == "grid" ? (openBlock(), createBlock("div", { key: 0 }, [
                createVNode("div", { class: "mt-4 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl" }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($props.datasets.data, (dataset) => {
                    return openBlock(), createBlock("span", {
                      key: dataset.id
                    }, [
                      createVNode(_component_dataset_card, {
                        mode: $props.filters.mode,
                        dataset
                      }, null, 8, ["mode", "dataset"])
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
                    (openBlock(true), createBlock(Fragment, null, renderList($props.datasets.data, (dataset) => {
                      return openBlock(), createBlock("span", {
                        key: dataset.id
                      }, [
                        createVNode(_component_dataset_card, {
                          mode: $props.filters.mode ? $props.filters.mode : "grid",
                          dataset
                        }, null, 8, ["mode", "dataset"])
                      ]);
                    }), 128))
                  ])
                ])
              ])) : createCommentVNode("", true),
              $props.datasets.meta.total > $props.datasets.meta.per_page ? (openBlock(), createBlock("div", {
                key: 2,
                class: "py-10"
              }, [
                createVNode(_component_Pagination, {
                  links: $props.datasets.meta.links
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
                createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No datasets ")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Datasets.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Datasets = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Datasets as default
};
