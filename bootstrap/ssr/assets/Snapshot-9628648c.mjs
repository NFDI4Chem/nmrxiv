import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { S as SearchFilter } from "./SearchFilter-01feb0b0.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { B as BreadCrumbs } from "./BreadCrumbs-a9158bdf.mjs";
import { Link } from "@inertiajs/vue3";
import { mergeProps, useSSRContext, resolveComponent, withCtx, createVNode, createTextVNode, openBlock, createBlock, toDisplayString, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderAttr, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
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
const _sfc_main$1 = {
  components: {},
  props: {
    id: Number
  },
  emits: ["loading"],
  setup() {
  },
  data() {
    return {
      spectraError: null
    };
  },
  computed: {
    url() {
      return String(this.$page.props.url) ? String(this.$page.props.url) : "https://nmrxiv.org";
    },
    nmriumURL() {
      return this.$page.props.nmriumURL ? String(this.$page.props.nmriumURL + "&id=" + Math.random()) : "https://nmriumdev.nmrxiv.org?defaultEmptyMessage=''&workspace=embedded&id=" + Math.random();
    }
  },
  mounted() {
    this.registerEvents();
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
            this.saveDSPreview(data.data);
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
              console.log(actionType);
              if (actionType == "INITIATE") {
                this.exportPreview();
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
    exportPreview() {
      setTimeout(() => {
        const iframe = window.frames.snapshotNMRiumIframe;
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
      }, 1e3);
    },
    loadSpectra() {
      const iframe = window.frames.snapshotNMRiumIframe;
      this.spectraError = null;
      if (iframe) {
        axios.get("/dashboard/datasets/" + this.id + "/nmriumInfo").then((response) => {
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
          } else {
            this.loadFromURLs(null);
          }
        });
      }
    },
    saveDSPreview(data) {
      if (this.id) {
        const reader = new FileReader();
        reader.addEventListener("loadend", () => {
          let svg = reader.result;
          axios.post("/dashboard/datasets/" + this.id + "/preview", {
            img: svg
          }).then((response) => {
            this.updateLoadingStatus(false, "dataset loaded");
          });
        });
        reader.readAsText(data.blob);
      }
    },
    updateLoadingStatus(status, message) {
      this.$emit("loading", {
        status,
        message
      });
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "p-6" }, _attrs))}><iframe name="snapshotNMRiumIframe" frameborder="0" allowfullscreen class="rounded-md border" style="${ssrRenderStyle({ "width": "100%", "height": "75vh", "max-height": "600px" })}"${ssrRenderAttr("src", $options.nmriumURL)}></iframe></div>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SpectraSnapshot.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const SpectraSnapshot = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    AppLayout,
    SearchFilter,
    JetDialogModal,
    JetSecondaryButton,
    JetButton,
    Link,
    BreadCrumbs,
    SpectraSnapshot
  },
  props: {
    users: Array,
    datasets: Array
  },
  data() {
    return {
      currentId: null,
      pages: [
        { name: "Console", route: "console", current: false },
        { name: "Curation", route: "console.spectra", current: true },
        {
          name: "Spectra Snapshots",
          route: "console.spectra.snapshots",
          current: true
        }
      ],
      index: 0,
      autoImport: false
    };
  },
  watch: {},
  mounted() {
    if (this.datasets && this.autoImport) {
      this.currentId = this.datasets[this.index];
    }
  },
  methods: {
    status(e) {
      if (!e.status && e.message == "dataset loaded") {
        if (this.autoImport) {
          this.index = this.index + 1;
          this.loadSpectra();
        }
      }
    },
    toggleAutoImport() {
      this.autoImport = !this.autoImport;
      if (this.autoImport) {
        this.loadSpectra();
      }
    },
    loadSpectra() {
      this.currentId = this.datasets[this.index];
      this.$refs.spectraEditorREF.loadSpectra();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_bread_crumbs = resolveComponent("bread-crumbs");
  const _component_SpectraSnapshot = resolveComponent("SpectraSnapshot");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({ title: "Spectra Snapshots" }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_bread_crumbs, { pages: $data.pages }, null, _parent2, _scopeId));
        _push2(`<div class="mt-2 md:flex md:items-center md:justify-between"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><div class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3"${_scopeId}> Spectra SnapShots `);
        if ($data.currentId) {
          _push2(`<span${_scopeId}>(Spectra ID: ${ssrInterpolate($data.currentId)})</span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div></div>`);
        if ($props.datasets.length > 0 && this.datasets.length >= this.index + 1) {
          _push2(`<div class="mx-auto flex max-w-2xl items-center justify-between gap-x-8 lg:mx-0 lg:max-w-none"${_scopeId}><div class="flex items-center gap-x-4 sm:gap-x-6"${_scopeId}><a class="text-sm font-semibold leading-6 text-gray-900 sm:block"${_scopeId}>${ssrInterpolate($data.index + 1)} / ${ssrInterpolate($props.datasets.length)}</a>`);
          if (!$data.autoImport) {
            _push2(`<a class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"${_scopeId}> Import </a>`);
          } else {
            _push2(`<a class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"${_scopeId}> Pause </a>`);
          }
          _push2(`</div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", null, [
                  createVNode(_component_bread_crumbs, { pages: $data.pages }, null, 8, ["pages"]),
                  createVNode("div", { class: "mt-2 md:flex md:items-center md:justify-between" }, [
                    createVNode("div", { class: "flex-1 min-w-0" }, [
                      createVNode("div", { class: "flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest mt-3" }, [
                        createTextVNode(" Spectra SnapShots "),
                        $data.currentId ? (openBlock(), createBlock("span", { key: 0 }, "(Spectra ID: " + toDisplayString($data.currentId) + ")", 1)) : createCommentVNode("", true)
                      ])
                    ])
                  ])
                ]),
                $props.datasets.length > 0 && this.datasets.length >= this.index + 1 ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "mx-auto flex max-w-2xl items-center justify-between gap-x-8 lg:mx-0 lg:max-w-none"
                }, [
                  createVNode("div", { class: "flex items-center gap-x-4 sm:gap-x-6" }, [
                    createVNode("a", { class: "text-sm font-semibold leading-6 text-gray-900 sm:block" }, toDisplayString($data.index + 1) + " / " + toDisplayString($props.datasets.length), 1),
                    !$data.autoImport ? (openBlock(), createBlock("a", {
                      key: 0,
                      onClick: $options.toggleAutoImport,
                      class: "rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    }, " Import ", 8, ["onClick"])) : (openBlock(), createBlock("a", {
                      key: 1,
                      onClick: $options.toggleAutoImport,
                      class: "rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    }, " Pause ", 8, ["onClick"]))
                  ])
                ])) : createCommentVNode("", true)
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($props.datasets.length > 0) {
          _push2(`<div${_scopeId}>`);
          if (this.datasets.length == this.index) {
            _push2(`<div class="p-10"${_scopeId}><button type="button" class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"${_scopeId}><svg class="mx-auto h-12 w-12 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId}></path></svg><span class="mt-2 block text-sm font-semibold text-gray-900"${_scopeId}>Import complete</span></button></div>`);
          } else {
            _push2(`<div${_scopeId}>`);
            if ($data.currentId) {
              _push2(`<div${_scopeId}>`);
              _push2(ssrRenderComponent(_component_SpectraSnapshot, {
                ref: "spectraEditorREF",
                id: $data.currentId,
                onLoading: $options.status
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (!$data.autoImport) {
              _push2(`<div class="p-10"${_scopeId}><button type="button" class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-12 w-12 text-blue-400"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"${_scopeId}></path></svg><div class="mt-6"${_scopeId}><button type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"${_scopeId}><svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"${_scopeId}></path></svg> Import </button></div></button></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<div class="p-10"${_scopeId}><button type="button" class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"${_scopeId}><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"${_scopeId}></path></svg><span class="mt-2 block text-sm font-semibold text-gray-900"${_scopeId}>Nothing to import</span></button></div>`);
        }
      } else {
        return [
          $props.datasets.length > 0 ? (openBlock(), createBlock("div", { key: 0 }, [
            this.datasets.length == this.index ? (openBlock(), createBlock("div", {
              key: 0,
              class: "p-10"
            }, [
              createVNode("button", {
                type: "button",
                class: "relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
              }, [
                (openBlock(), createBlock("svg", {
                  class: "mx-auto h-12 w-12 text-green-400",
                  xmlns: "http://www.w3.org/2000/svg",
                  fill: "none",
                  viewBox: "0 0 24 24",
                  "stroke-width": "1.5",
                  stroke: "currentColor"
                }, [
                  createVNode("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    d: "M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  })
                ])),
                createVNode("span", { class: "mt-2 block text-sm font-semibold text-gray-900" }, "Import complete")
              ])
            ])) : (openBlock(), createBlock("div", { key: 1 }, [
              $data.currentId ? (openBlock(), createBlock("div", { key: 0 }, [
                createVNode(_component_SpectraSnapshot, {
                  ref: "spectraEditorREF",
                  id: $data.currentId,
                  onLoading: $options.status
                }, null, 8, ["id", "onLoading"])
              ])) : createCommentVNode("", true),
              !$data.autoImport ? (openBlock(), createBlock("div", {
                key: 1,
                class: "p-10"
              }, [
                createVNode("button", {
                  type: "button",
                  class: "relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                }, [
                  (openBlock(), createBlock("svg", {
                    xmlns: "http://www.w3.org/2000/svg",
                    fill: "none",
                    viewBox: "0 0 24 24",
                    "stroke-width": "1.5",
                    stroke: "currentColor",
                    class: "mx-auto h-12 w-12 text-blue-400"
                  }, [
                    createVNode("path", {
                      "stroke-linecap": "round",
                      "stroke-linejoin": "round",
                      d: "M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"
                    })
                  ])),
                  createVNode("div", { class: "mt-6" }, [
                    createVNode("button", {
                      onClick: $options.toggleAutoImport,
                      type: "button",
                      class: "inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "-ml-0.5 mr-1.5 h-5 w-5",
                        viewBox: "0 0 20 20",
                        fill: "currentColor",
                        "aria-hidden": "true"
                      }, [
                        createVNode("path", { d: "M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" })
                      ])),
                      createTextVNode(" Import ")
                    ], 8, ["onClick"])
                  ])
                ])
              ])) : createCommentVNode("", true)
            ]))
          ])) : (openBlock(), createBlock("div", {
            key: 1,
            class: "p-10"
          }, [
            createVNode("button", {
              type: "button",
              class: "relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            }, [
              (openBlock(), createBlock("svg", {
                class: "mx-auto h-12 w-12 text-gray-400",
                fill: "none",
                viewBox: "0 0 24 24",
                "stroke-width": "1.5",
                stroke: "currentColor",
                "aria-hidden": "true"
              }, [
                createVNode("path", {
                  "stroke-linecap": "round",
                  "stroke-linejoin": "round",
                  d: "M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                })
              ])),
              createVNode("span", { class: "mt-2 block text-sm font-semibold text-gray-900" }, "Nothing to import")
            ])
          ]))
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Console/Spectra/Snapshot.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Snapshot = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Snapshot as default
};
