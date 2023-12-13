import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { B as BreadCrumbs } from "./BreadCrumbs-a9158bdf.mjs";
import { I as Icon } from "./Icon-01d9d65b.mjs";
import AnnouncementCreate from "./Create-28b1bf5a.mjs";
import AnnouncementEdit from "./Edit-d353fd09.mjs";
import { J as JetConfirmationModal } from "./ConfirmationModal-d0b47856.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { ref, watch, resolveComponent, mergeProps, withCtx, createVNode, createTextVNode, openBlock, createBlock, withDirectives, vModelText, Fragment, renderList, toDisplayString, createCommentVNode, useSSRContext } from "vue";
import { router } from "@inertiajs/vue3";
import { T as ToggleButton } from "./ToggleButton-fec39d54.mjs";
import { ssrRenderComponent, ssrRenderAttr, ssrRenderList, ssrInterpolate } from "vue/server-renderer";
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
import "./Button-8d6976fd.mjs";
import "@sipec/vue3-tags-input";
import "vue3-slider";
import "openchemlib/full.js";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./InputError-6ae0b65c.mjs";
import "dropzone";
import "axios-retry";
import "@heroicons/vue/20/solid";
import "@vuepic/vue-datepicker";
/* empty css                */import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
    AppLayout,
    BreadCrumbs,
    Icon,
    AnnouncementCreate,
    AnnouncementEdit,
    JetConfirmationModal,
    JetDangerButton,
    JetSecondaryButton,
    JetDialogModal,
    ToggleButton
  },
  props: {
    announcements: Array,
    filters: Object
  },
  setup() {
    const announcementCreateElement = ref(null);
    const announcementEditElement = ref(null);
    const search = ref("");
    watch(search, (value) => {
      router.get(
        "announcements",
        { search: value },
        {
          preserveState: true,
          replace: true
        }
      );
    });
    return {
      announcementCreateElement,
      announcementEditElement,
      search
    };
  },
  data() {
    return {
      form: this.$inertia.form({}),
      confirmingAnnouncementDeletion: false,
      announcementId: null,
      pages: [
        { name: "Console", route: "console", current: false },
        {
          name: "Announcements",
          route: "console.announcements",
          current: true
        }
      ]
    };
  },
  methods: {
    reset() {
      this.search = null;
    },
    openAnnouncementCreateDialog() {
      this.announcementCreateElement.toggleCreateAnnouncementDialog();
    },
    openAnnouncementEditDialog(announcement) {
      this.announcementEditElement.toggleEditAnnouncementDialog(
        announcement
      );
    },
    deleteAnnouncement(id) {
      this.form.delete(route("console.announcements.destroy", id), {
        onSuccess: () => {
          this.closeModal();
          this.announcementId = null;
        },
        onError: (err) => console.error(err)
      });
    },
    confirmAnnouncementDeletion(id) {
      this.announcementId = id;
      this.confirmingAnnouncementDeletion = true;
    },
    closeModal() {
      this.confirmingAnnouncementDeletion = false;
      this.announcementId = null;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_bread_crumbs = resolveComponent("bread-crumbs");
  const _component_Button = resolveComponent("Button");
  const _component_icon = resolveComponent("icon");
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  const _component_announcement_create = resolveComponent("announcement-create");
  const _component_announcement_edit = resolveComponent("announcement-edit");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Announcements and Notifications" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_bread_crumbs, { pages: $data.pages }, null, _parent2, _scopeId));
        _push2(`<div class="mt-2 md:flex md:items-center md:justify-between"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"${_scopeId}> Announcements </div></div></div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode(_component_bread_crumbs, { pages: $data.pages }, null, 8, ["pages"]),
                  createVNode("div", { class: "mt-2 md:flex md:items-center md:justify-between" }, [
                    createVNode("div", { class: "flex-1 min-w-0" }, [
                      createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3" }, " Announcements ")
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
        _push2(`<div class="py-12 px-10"${_scopeId}>`);
        if ($props.announcements.length > 0 || $setup.search != "") {
          _push2(`<span${_scopeId}><div class="mb-6 flex justify-between items-center"${_scopeId}><div class="flex items-center"${_scopeId}><div class="flex w-full bg-white shadow rounded"${_scopeId}><input${ssrRenderAttr("value", $setup.search)} class="relative w-full border-0 px-6 py-3 rounded-r focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Search…"${_scopeId}></div><button class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500" type="button"${_scopeId}> Reset </button></div>`);
          _push2(ssrRenderComponent(_component_Button, {
            class: "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition",
            type: "button",
            onClick: ($event) => $options.openAnnouncementCreateDialog()
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`<span${_scopeId2}>Create</span>  <span class="hidden md:inline"${_scopeId2}>Announcement</span>`);
              } else {
                return [
                  createVNode("span", null, "Create"),
                  createTextVNode("  "),
                  createVNode("span", { class: "hidden md:inline" }, "Announcement")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div><div${_scopeId}><div class="bg-white rounded-md shadow overflow-x-auto"${_scopeId}><table class="divide-y divide-gray-200 w-full table-fixed"${_scopeId}><thead class="bg-gray-50"${_scopeId}><tr${_scopeId}><th colspan="3" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"${_scopeId}> Title </th><th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"${_scopeId}> Status </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"${_scopeId}> Actions </th></tr></thead><tbody class="bg-white divide-y divide-gray-200"${_scopeId}><!--[-->`);
          ssrRenderList($props.announcements, (announcement) => {
            _push2(`<tr${_scopeId}><td colspan="3" class="py-4"${_scopeId}><div class="ml-4"${_scopeId}><h3 class="text-lg leading-6 font-medium text-gray-900"${_scopeId}>${ssrInterpolate(announcement.title)}</h3><p class="mt-1 mb-3 text-sm text-blue-gray-900 break-normal"${_scopeId}>${ssrInterpolate(announcement.message)}</p><label class="block text-sm mt-2 font-medium text-gray-500"${_scopeId}> Created by </label><p class="mt-1 text-md text-blue-gray-900"${_scopeId}>${ssrInterpolate(announcement.created_by)}</p><label class="block text-sm mt-2 font-medium text-gray-500"${_scopeId}> From </label><p class="mt-1 text-sm text-blue-gray-900"${_scopeId}>${ssrInterpolate(_ctx.formatDateTime(
              announcement.start_time
            ))}</p><label class="block text-sm mt-2 font-medium text-gray-500"${_scopeId}> To </label><p class="mt-1 text-sm text-blue-gray-900"${_scopeId}>${ssrInterpolate(_ctx.formatDateTime(
              announcement.end_time
            ))}</p></div></td><td class="px-5 py-4 text-center whitespace-nowrap"${_scopeId}>`);
            if (announcement.status == "active" || announcement.status == "Active") {
              _push2(`<span class="px-4 py-2 inline-flex text-md leading-5 font-semibold rounded-full bg-green-100 text-green-800"${_scopeId}> Active </span>`);
            } else {
              _push2(`<span class="px-4 py-2 inline-flex text-md leading-5 font-semibold rounded-full bg-red-100 text-green-800"${_scopeId}> Inactive </span>`);
            }
            _push2(`</td><td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"${_scopeId}><button type="button" class="inline-flex items-center p-1 border border-transparent rounded-md mr-2 shadow-sm text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_icon, { name: "edit" }, null, _parent2, _scopeId));
            _push2(`</button><button type="button" class="inline-flex items-center p-1 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_icon, { name: "delete" }, null, _parent2, _scopeId));
            _push2(`</button></td></tr>`);
          });
          _push2(`<!--]--></tbody></table>`);
          _push2(ssrRenderComponent(_component_jet_dialog_modal, {
            show: $data.confirmingAnnouncementDeletion,
            onClose: $options.closeModal
          }, {
            title: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Delete Announcement `);
              } else {
                return [
                  createTextVNode(" Delete Announcement ")
                ];
              }
            }),
            content: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Are you sure you want to delete this announcement? <div class="mt-4"${_scopeId2}></div>`);
              } else {
                return [
                  createTextVNode(" Are you sure you want to delete this announcement? "),
                  createVNode("div", { class: "mt-4" })
                ];
              }
            }),
            footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(ssrRenderComponent(_component_jet_secondary_button, { onClick: $options.closeModal }, {
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
                _push3(ssrRenderComponent(_component_jet_danger_button, {
                  class: ["ml-2", { "opacity-25": $data.form.processing }],
                  disabled: $data.form.processing,
                  onClick: ($event) => $options.deleteAnnouncement($data.announcementId)
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(` Delete Announcement `);
                    } else {
                      return [
                        createTextVNode(" Delete Announcement ")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
              } else {
                return [
                  createVNode(_component_jet_secondary_button, { onClick: $options.closeModal }, {
                    default: withCtx(() => [
                      createTextVNode(" Cancel ")
                    ]),
                    _: 1
                  }, 8, ["onClick"]),
                  createVNode(_component_jet_danger_button, {
                    class: ["ml-2", { "opacity-25": $data.form.processing }],
                    disabled: $data.form.processing,
                    onClick: ($event) => $options.deleteAnnouncement($data.announcementId)
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Delete Announcement ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled", "onClick"])
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div></div></span>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.announcements.length == 0 && $setup.search == "") {
          _push2(`<span${_scopeId}><button type="button" class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"${_scopeId}></path></svg><span class="mt-2 block text-sm font-medium text-gray-900"${_scopeId}> Create a new announcement </span></button></span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(ssrRenderComponent(_component_announcement_create, { ref: "announcementCreateElement" }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_announcement_edit, { ref: "announcementEditElement" }, null, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "py-12 px-10" }, [
            $props.announcements.length > 0 || $setup.search != "" ? (openBlock(), createBlock("span", { key: 0 }, [
              createVNode("div", { class: "mb-6 flex justify-between items-center" }, [
                createVNode("div", { class: "flex items-center" }, [
                  createVNode("div", { class: "flex w-full bg-white shadow rounded" }, [
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => $setup.search = $event,
                      class: "relative w-full border-0 px-6 py-3 rounded-r focus:shadow-outline",
                      autocomplete: "off",
                      type: "text",
                      name: "search",
                      placeholder: "Search…"
                    }, null, 8, ["onUpdate:modelValue"]), [
                      [vModelText, $setup.search]
                    ])
                  ]),
                  createVNode("button", {
                    class: "ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500",
                    type: "button",
                    onClick: ($event) => $options.reset()
                  }, " Reset ", 8, ["onClick"])
                ]),
                createVNode(_component_Button, {
                  class: "inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition",
                  type: "button",
                  onClick: ($event) => $options.openAnnouncementCreateDialog()
                }, {
                  default: withCtx(() => [
                    createVNode("span", null, "Create"),
                    createTextVNode("  "),
                    createVNode("span", { class: "hidden md:inline" }, "Announcement")
                  ]),
                  _: 1
                }, 8, ["onClick"])
              ]),
              createVNode("div", null, [
                createVNode("div", { class: "bg-white rounded-md shadow overflow-x-auto" }, [
                  createVNode("table", { class: "divide-y divide-gray-200 w-full table-fixed" }, [
                    createVNode("thead", { class: "bg-gray-50" }, [
                      createVNode("tr", null, [
                        createVNode("th", {
                          colspan: "3",
                          scope: "col",
                          class: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        }, " Title "),
                        createVNode("th", {
                          scope: "col",
                          class: "px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                        }, " Status "),
                        createVNode("th", {
                          scope: "col",
                          class: "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        }, " Actions ")
                      ])
                    ]),
                    createVNode("tbody", { class: "bg-white divide-y divide-gray-200" }, [
                      (openBlock(true), createBlock(Fragment, null, renderList($props.announcements, (announcement) => {
                        return openBlock(), createBlock("tr", {
                          key: announcement.id
                        }, [
                          createVNode("td", {
                            colspan: "3",
                            class: "py-4"
                          }, [
                            createVNode("div", { class: "ml-4" }, [
                              createVNode("h3", { class: "text-lg leading-6 font-medium text-gray-900" }, toDisplayString(announcement.title), 1),
                              createVNode("p", { class: "mt-1 mb-3 text-sm text-blue-gray-900 break-normal" }, toDisplayString(announcement.message), 1),
                              createVNode("label", { class: "block text-sm mt-2 font-medium text-gray-500" }, " Created by "),
                              createVNode("p", { class: "mt-1 text-md text-blue-gray-900" }, toDisplayString(announcement.created_by), 1),
                              createVNode("label", { class: "block text-sm mt-2 font-medium text-gray-500" }, " From "),
                              createVNode("p", { class: "mt-1 text-sm text-blue-gray-900" }, toDisplayString(_ctx.formatDateTime(
                                announcement.start_time
                              )), 1),
                              createVNode("label", { class: "block text-sm mt-2 font-medium text-gray-500" }, " To "),
                              createVNode("p", { class: "mt-1 text-sm text-blue-gray-900" }, toDisplayString(_ctx.formatDateTime(
                                announcement.end_time
                              )), 1)
                            ])
                          ]),
                          createVNode("td", { class: "px-5 py-4 text-center whitespace-nowrap" }, [
                            announcement.status == "active" || announcement.status == "Active" ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "px-4 py-2 inline-flex text-md leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                            }, " Active ")) : (openBlock(), createBlock("span", {
                              key: 1,
                              class: "px-4 py-2 inline-flex text-md leading-5 font-semibold rounded-full bg-red-100 text-green-800"
                            }, " Inactive "))
                          ]),
                          createVNode("td", { class: "px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200" }, [
                            createVNode("button", {
                              type: "button",
                              class: "inline-flex items-center p-1 border border-transparent rounded-md mr-2 shadow-sm text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300",
                              onClick: ($event) => $options.openAnnouncementEditDialog(
                                announcement
                              )
                            }, [
                              createVNode(_component_icon, { name: "edit" })
                            ], 8, ["onClick"]),
                            createVNode("button", {
                              type: "button",
                              class: "inline-flex items-center p-1 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500",
                              onClick: ($event) => $options.confirmAnnouncementDeletion(
                                announcement.id
                              )
                            }, [
                              createVNode(_component_icon, { name: "delete" })
                            ], 8, ["onClick"])
                          ])
                        ]);
                      }), 128))
                    ])
                  ]),
                  createVNode(_component_jet_dialog_modal, {
                    show: $data.confirmingAnnouncementDeletion,
                    onClose: $options.closeModal
                  }, {
                    title: withCtx(() => [
                      createTextVNode(" Delete Announcement ")
                    ]),
                    content: withCtx(() => [
                      createTextVNode(" Are you sure you want to delete this announcement? "),
                      createVNode("div", { class: "mt-4" })
                    ]),
                    footer: withCtx(() => [
                      createVNode(_component_jet_secondary_button, { onClick: $options.closeModal }, {
                        default: withCtx(() => [
                          createTextVNode(" Cancel ")
                        ]),
                        _: 1
                      }, 8, ["onClick"]),
                      createVNode(_component_jet_danger_button, {
                        class: ["ml-2", { "opacity-25": $data.form.processing }],
                        disabled: $data.form.processing,
                        onClick: ($event) => $options.deleteAnnouncement($data.announcementId)
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Delete Announcement ")
                        ]),
                        _: 1
                      }, 8, ["class", "disabled", "onClick"])
                    ]),
                    _: 1
                  }, 8, ["show", "onClose"])
                ])
              ])
            ])) : createCommentVNode("", true),
            $props.announcements.length == 0 && $setup.search == "" ? (openBlock(), createBlock("span", { key: 1 }, [
              createVNode("button", {
                type: "button",
                class: "relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500",
                onClick: ($event) => $options.openAnnouncementCreateDialog()
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
                createVNode("span", { class: "mt-2 block text-sm font-medium text-gray-900" }, " Create a new announcement ")
              ], 8, ["onClick"])
            ])) : createCommentVNode("", true),
            createVNode(_component_announcement_create, { ref: "announcementCreateElement" }, null, 512),
            createVNode(_component_announcement_edit, { ref: "announcementEditElement" }, null, 512)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Announcement/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
