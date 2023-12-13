import ProjectLayout from "./Layout-3465446f.mjs";
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { S as SpectraViewer } from "./SpectraViewer-3065e728.mjs";
import { D as DOIBadge } from "./DOIBadge-164e4f67.mjs";
import { D as Depictor2D } from "./Depictor2D-b60253ae.mjs";
import { Head } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createVNode, toDisplayString, createTextVNode, Transition, openBlock, createBlock, createCommentVNode, Fragment, renderList, resolveDynamicComponent, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderClass, ssrRenderAttr, ssrRenderList, ssrRenderVNode } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./AppLayout-b93d1c11.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
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
import "openchemlib/full.js";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./InputError-6ae0b65c.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./DangerButton-9c23157c.mjs";
import "@heroicons/vue/20/solid";
const _sfc_main = {
  components: {
    ProjectLayout,
    ShareIcon,
    ClipboardDocumentIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    SpectraViewer,
    DOIBadge,
    Depictor2D,
    Head
  },
  props: ["project", "tab", "study", "dataset"],
  data() {
    return {
      selectedSpectraData: null,
      schema: {}
    };
  },
  computed: {
    shareURL() {
      return this.dataset.data.public_url;
    },
    url() {
      return String(this.$page.props.url);
    }
  },
  mounted() {
    axios.get(route("bioschemas.id", this.dataset.data.identifier)).then((response) => {
      this.schema = response.data;
    });
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_project_layout = resolveComponent("project-layout");
  const _component_DOIBadge = resolveComponent("DOIBadge");
  const _component_Menu = resolveComponent("Menu");
  const _component_MenuButton = resolveComponent("MenuButton");
  const _component_ShareIcon = resolveComponent("ShareIcon");
  const _component_MenuItems = resolveComponent("MenuItems");
  const _component_MenuItem = resolveComponent("MenuItem");
  const _component_ClipboardDocumentIcon = resolveComponent("ClipboardDocumentIcon");
  const _component_SpectraViewer = resolveComponent("SpectraViewer");
  const _component_Depictor2D = resolveComponent("Depictor2D");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<title${_scopeId}>${ssrInterpolate($props.dataset.data.name + " - " + $props.study.data.name)}</title><meta name="description" content="Your nmrxiv description"${_scopeId}><meta property="og:title" content="nmrXiv-msXiv-VibSpecDB synergies weekly meeting agenda"${_scopeId}><meta property="og:type" content="article"${_scopeId}><meta property="og:site_name" content="Google Docs"${_scopeId}><meta property="og:url" content="https://docs.google.com/document/d/1kq53WvSkvlY5QdgU6-AP2szXuPAfUMxq4x7uAKP9aX4/edit?usp=embed_facebook"${_scopeId}><meta property="og:image" content="https://lh7-us.googleusercontent.com/docs/AHkbwyJM4oyOCYTE0UI2J6YeyMCjJtFVCYB3hNPlxoQeMvL5ULxddInOt7igI6zjTN0yo4wmjVvG-IpE1d3ZHuLyL2feyg2dzdw_FEaq4hUd6MY4jfM=w1200-h630-p"${_scopeId}><meta property="og:image:width" content="1200"${_scopeId}><meta property="og:image:height" content="630"${_scopeId}><meta name="google" content="notranslate"${_scopeId}><meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"${_scopeId}><meta http-equiv="X-UA-Compatible" content="IE=edge;"${_scopeId}><meta name="fragment" content="!"${_scopeId}><meta name="referrer" content="strict-origin-when-cross-origin"${_scopeId}><link rel="shortcut icon" href="https://ssl.gstatic.com/docs/documents/images/kix-favicon-2023q4.ico"${_scopeId}><link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/ghbmnnjooekpmoecnnnilnnbdlolhkhi"${_scopeId}><link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/apdfllckaahabafndbhieahigkjlhalf"${_scopeId}><link rel="manifest" href="/document/manifest.json" crossorigin="use-credentials"${_scopeId}>`);
      } else {
        return [
          createVNode("title", null, toDisplayString($props.dataset.data.name + " - " + $props.study.data.name), 1),
          createVNode("meta", {
            name: "description",
            content: "Your nmrxiv description"
          }),
          createVNode("meta", {
            property: "og:title",
            content: "nmrXiv-msXiv-VibSpecDB synergies weekly meeting agenda"
          }),
          createVNode("meta", {
            property: "og:type",
            content: "article"
          }),
          createVNode("meta", {
            property: "og:site_name",
            content: "Google Docs"
          }),
          createVNode("meta", {
            property: "og:url",
            content: "https://docs.google.com/document/d/1kq53WvSkvlY5QdgU6-AP2szXuPAfUMxq4x7uAKP9aX4/edit?usp=embed_facebook"
          }),
          createVNode("meta", {
            property: "og:image",
            content: "https://lh7-us.googleusercontent.com/docs/AHkbwyJM4oyOCYTE0UI2J6YeyMCjJtFVCYB3hNPlxoQeMvL5ULxddInOt7igI6zjTN0yo4wmjVvG-IpE1d3ZHuLyL2feyg2dzdw_FEaq4hUd6MY4jfM=w1200-h630-p"
          }),
          createVNode("meta", {
            property: "og:image:width",
            content: "1200"
          }),
          createVNode("meta", {
            property: "og:image:height",
            content: "630"
          }),
          createVNode("meta", {
            name: "google",
            content: "notranslate"
          }),
          createVNode("meta", {
            name: "viewport",
            content: "width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"
          }),
          createVNode("meta", {
            "http-equiv": "X-UA-Compatible",
            content: "IE=edge;"
          }),
          createVNode("meta", {
            name: "fragment",
            content: "!"
          }),
          createVNode("meta", {
            name: "referrer",
            content: "strict-origin-when-cross-origin"
          }),
          createVNode("link", {
            rel: "shortcut icon",
            href: "https://ssl.gstatic.com/docs/documents/images/kix-favicon-2023q4.ico"
          }),
          createVNode("link", {
            rel: "chrome-webstore-item",
            href: "https://chrome.google.com/webstore/detail/ghbmnnjooekpmoecnnnilnnbdlolhkhi"
          }),
          createVNode("link", {
            rel: "chrome-webstore-item",
            href: "https://chrome.google.com/webstore/detail/apdfllckaahabafndbhieahigkjlhalf"
          }),
          createVNode("link", {
            rel: "manifest",
            href: "/document/manifest.json",
            crossorigin: "use-credentials"
          })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_project_layout, {
    project: $props.project,
    "selected-tab": $props.tab
  }, {
    "project-content": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"${_scopeId}><h1 class="mt-2 text-2xl font-bold break-words text-gray-900"${_scopeId}><span${_scopeId}>${ssrInterpolate($props.study.data.name)} &gt; <span class="text-blue-500"${_scopeId}>${ssrInterpolate($props.dataset.data.name)} `);
        if ($props.dataset.data.type) {
          _push2(`<span${_scopeId}>- (${ssrInterpolate($props.dataset.data.type.replace(/,\s*$/, ""))})</span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</span></span><div class="text-sm"${_scopeId}><span class="text-gray-400 pt-2"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_DOIBadge, {
          doi: $props.dataset.data.doi,
          color: "bg-green-100"
        }, null, _parent2, _scopeId));
        _push2(`</span></div><div class="float-right"${_scopeId}><span class="flex-0.5 self-center"${_scopeId}>`);
        if ($props.dataset && $props.study.data.is_public) {
          _push2(ssrRenderComponent(_component_Menu, {
            as: "div",
            class: "relative text-left"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`<div${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_MenuButton, { class: "bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600" }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(ssrRenderComponent(_component_ShareIcon, {
                        class: "h-4 w-4 text-gray-800 flex-shrink-0 mr-2",
                        "aria-hidden": "true"
                      }, null, _parent4, _scopeId3));
                      _push4(`Share `);
                    } else {
                      return [
                        createVNode(_component_ShareIcon, {
                          class: "h-4 w-4 text-gray-800 flex-shrink-0 mr-2",
                          "aria-hidden": "true"
                        }),
                        createTextVNode("Share ")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
                _push3(`</div>`);
                _push3(ssrRenderComponent(_component_MenuItems, { class: "origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(`<div class="py-1"${_scopeId3}>`);
                      _push4(ssrRenderComponent(_component_MenuItem, null, {
                        default: withCtx(({ active }, _push5, _parent5, _scopeId4) => {
                          if (_push5) {
                            _push5(`<div class="${ssrRenderClass([
                              active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                              "block px-4 py-2 text-sm flex"
                            ])}"${_scopeId4}><div class="flex-grow"${_scopeId4}><input id="datasetPublicURLCopy" readonly type="text"${ssrRenderAttr("value", $options.shareURL)} class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"${_scopeId4}></div><button type="button" class="-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"${_scopeId4}><span${_scopeId4}>`);
                            _push5(ssrRenderComponent(_component_ClipboardDocumentIcon, {
                              class: "h-5 w-5",
                              "aria-hidden": "true"
                            }, null, _parent5, _scopeId4));
                            _push5(`</span></button></div>`);
                          } else {
                            return [
                              createVNode("div", {
                                class: [
                                  active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                  "block px-4 py-2 text-sm flex"
                                ]
                              }, [
                                createVNode("div", { class: "flex-grow" }, [
                                  createVNode("input", {
                                    id: "datasetPublicURLCopy",
                                    readonly: "",
                                    type: "text",
                                    value: $options.shareURL,
                                    class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                    onFocus: ($event) => $event.target.select()
                                  }, null, 40, ["value", "onFocus"])
                                ]),
                                createVNode("button", {
                                  type: "button",
                                  class: "-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                  onClick: ($event) => _ctx.copyToClipboard(
                                    $options.shareURL,
                                    "datasetPublicURLCopy"
                                  )
                                }, [
                                  createVNode("span", null, [
                                    createVNode(_component_ClipboardDocumentIcon, {
                                      class: "h-5 w-5",
                                      "aria-hidden": "true"
                                    })
                                  ])
                                ], 8, ["onClick"])
                              ], 2)
                            ];
                          }
                        }),
                        _: 1
                      }, _parent4, _scopeId3));
                      _push4(`</div>`);
                    } else {
                      return [
                        createVNode("div", { class: "py-1" }, [
                          createVNode(_component_MenuItem, null, {
                            default: withCtx(({ active }) => [
                              createVNode("div", {
                                class: [
                                  active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                  "block px-4 py-2 text-sm flex"
                                ]
                              }, [
                                createVNode("div", { class: "flex-grow" }, [
                                  createVNode("input", {
                                    id: "datasetPublicURLCopy",
                                    readonly: "",
                                    type: "text",
                                    value: $options.shareURL,
                                    class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                    onFocus: ($event) => $event.target.select()
                                  }, null, 40, ["value", "onFocus"])
                                ]),
                                createVNode("button", {
                                  type: "button",
                                  class: "-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                  onClick: ($event) => _ctx.copyToClipboard(
                                    $options.shareURL,
                                    "datasetPublicURLCopy"
                                  )
                                }, [
                                  createVNode("span", null, [
                                    createVNode(_component_ClipboardDocumentIcon, {
                                      class: "h-5 w-5",
                                      "aria-hidden": "true"
                                    })
                                  ])
                                ], 8, ["onClick"])
                              ], 2)
                            ]),
                            _: 1
                          })
                        ])
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
              } else {
                return [
                  createVNode("div", null, [
                    createVNode(_component_MenuButton, { class: "bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600" }, {
                      default: withCtx(() => [
                        createVNode(_component_ShareIcon, {
                          class: "h-4 w-4 text-gray-800 flex-shrink-0 mr-2",
                          "aria-hidden": "true"
                        }),
                        createTextVNode("Share ")
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
                      createVNode(_component_MenuItems, { class: "origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "py-1" }, [
                            createVNode(_component_MenuItem, null, {
                              default: withCtx(({ active }) => [
                                createVNode("div", {
                                  class: [
                                    active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                    "block px-4 py-2 text-sm flex"
                                  ]
                                }, [
                                  createVNode("div", { class: "flex-grow" }, [
                                    createVNode("input", {
                                      id: "datasetPublicURLCopy",
                                      readonly: "",
                                      type: "text",
                                      value: $options.shareURL,
                                      class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                      onFocus: ($event) => $event.target.select()
                                    }, null, 40, ["value", "onFocus"])
                                  ]),
                                  createVNode("button", {
                                    type: "button",
                                    class: "-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                    onClick: ($event) => _ctx.copyToClipboard(
                                      $options.shareURL,
                                      "datasetPublicURLCopy"
                                    )
                                  }, [
                                    createVNode("span", null, [
                                      createVNode(_component_ClipboardDocumentIcon, {
                                        class: "h-5 w-5",
                                        "aria-hidden": "true"
                                      })
                                    ])
                                  ], 8, ["onClick"])
                                ], 2)
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
          }, _parent2, _scopeId));
        } else {
          _push2(`<!---->`);
        }
        _push2(`</span></div></h1><div class="mt-4"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-100"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="pr-3 text-md bg-white font-medium text-gray-400"${_scopeId}> Spectra </span></div></div><h1 class="mt-2 text-2xl font-bold break-words text-gray-900"${_scopeId}></h1><div class="my-7"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_SpectraViewer, {
          dataset: $props.dataset.data,
          project: $props.project.data,
          study: $props.study.data
        }, null, _parent2, _scopeId));
        _push2(`</div></div>`);
        if ($props.study.data.tags.length > 0) {
          _push2(`<div class="mt-4"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-100"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="pr-3 text-md bg-white font-medium text-gray-400"${_scopeId}> Keywords </span></div></div><div class="mt-3"${_scopeId}><dd class="mt-1 text-md text-gray-900 space-y-5"${_scopeId}><p${_scopeId}><!--[-->`);
          ssrRenderList($props.study.data.tags, (tag) => {
            _push2(`<span class="mr-2"${_scopeId}><span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"${_scopeId}><svg class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400" fill="currentColor" viewBox="0 0 8 8"${_scopeId}><circle cx="4" cy="4" r="3"${_scopeId}></circle></svg> ${ssrInterpolate(tag.name["en"])}</span></span>`);
          });
          _push2(`<!--]--></p></dd></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.study.data.sample.molecules.length > 0 || $props.study.data.sample.description == "") {
          _push2(`<div class="mt-4"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-100"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="pr-3 text-md bg-white font-medium text-gray-400"${_scopeId}> Sample </span></div></div>`);
          if ($props.study.data.sample.molecules.length > 0) {
            _push2(`<div class="mt-3"${_scopeId}><label${_scopeId}>Molecular Composition</label><div class="grid md:grid-cols-2 gap-2 mt-2"${_scopeId}><div class="pr-2"${_scopeId}>`);
            if ($props.study.data.sample.molecules.length > 0) {
              _push2(`<div class="flow-root"${_scopeId}><ul role="list" class="-mb-8"${_scopeId}><!--[-->`);
              ssrRenderList($props.study.data.sample.molecules, (molecule) => {
                _push2(`<li${_scopeId}><div class="relative pb-8"${_scopeId}><span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"${_scopeId}></span><div class="relative flex items-start space-x-3"${_scopeId}>`);
                if (molecule && molecule.pivot) {
                  _push2(`<div class="relative"${_scopeId}><div class="rounded-full border p-2 z-10 bg-gray-100 text-sm"${_scopeId}>${ssrInterpolate(molecule.pivot.percentage_composition)}% </div></div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<div class="min-w-0 flex-1"${_scopeId}><div${_scopeId}><div class="text-sm"${_scopeId}><a class="font-medium text-gray-900"${_scopeId}>${ssrInterpolate(molecule.standard_inchi)}</a></div></div><div class="mt-2 text-sm text-gray-700"${_scopeId}><div class="rounded-md border my-3 flex justify-center items-center"${_scopeId}>`);
                _push2(ssrRenderComponent(_component_Depictor2D, {
                  class: "py-4 -px-4",
                  molecule: molecule.canonical_smiles
                }, null, _parent2, _scopeId));
                _push2(`</div></div></div></div></div></li>`);
              });
              _push2(`<!--]--></ul><div class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"${_scopeId}> Sample chemical composition </div></div>`);
            } else {
              _push2(`<div${_scopeId}><div class="text-center my-10 py-10"${_scopeId}><h3 class="mt-2 text-sm font-medium text-gray-900"${_scopeId}> No structures associated with the sample yet! </h3><p class="mt-1 text-sm text-gray-500"${_scopeId}> Get started by adding a new molecule. </p></div></div>`);
            }
            _push2(`</div></div></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" }, [
            createVNode("h1", { class: "mt-2 text-2xl font-bold break-words text-gray-900" }, [
              createVNode("span", null, [
                createTextVNode(toDisplayString($props.study.data.name) + " > ", 1),
                createVNode("span", { class: "text-blue-500" }, [
                  createTextVNode(toDisplayString($props.dataset.data.name) + " ", 1),
                  $props.dataset.data.type ? (openBlock(), createBlock("span", { key: 0 }, "- (" + toDisplayString($props.dataset.data.type.replace(/,\s*$/, "")) + ")", 1)) : createCommentVNode("", true)
                ])
              ]),
              createVNode("div", { class: "text-sm" }, [
                createVNode("span", { class: "text-gray-400 pt-2" }, [
                  createVNode(_component_DOIBadge, {
                    doi: $props.dataset.data.doi,
                    color: "bg-green-100"
                  }, null, 8, ["doi"])
                ])
              ]),
              createVNode("div", { class: "float-right" }, [
                createVNode("span", { class: "flex-0.5 self-center" }, [
                  $props.dataset && $props.study.data.is_public ? (openBlock(), createBlock(_component_Menu, {
                    key: 0,
                    as: "div",
                    class: "relative text-left"
                  }, {
                    default: withCtx(() => [
                      createVNode("div", null, [
                        createVNode(_component_MenuButton, { class: "bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600" }, {
                          default: withCtx(() => [
                            createVNode(_component_ShareIcon, {
                              class: "h-4 w-4 text-gray-800 flex-shrink-0 mr-2",
                              "aria-hidden": "true"
                            }),
                            createTextVNode("Share ")
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
                          createVNode(_component_MenuItems, { class: "origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" }, {
                            default: withCtx(() => [
                              createVNode("div", { class: "py-1" }, [
                                createVNode(_component_MenuItem, null, {
                                  default: withCtx(({ active }) => [
                                    createVNode("div", {
                                      class: [
                                        active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                        "block px-4 py-2 text-sm flex"
                                      ]
                                    }, [
                                      createVNode("div", { class: "flex-grow" }, [
                                        createVNode("input", {
                                          id: "datasetPublicURLCopy",
                                          readonly: "",
                                          type: "text",
                                          value: $options.shareURL,
                                          class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                          onFocus: ($event) => $event.target.select()
                                        }, null, 40, ["value", "onFocus"])
                                      ]),
                                      createVNode("button", {
                                        type: "button",
                                        class: "-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                        onClick: ($event) => _ctx.copyToClipboard(
                                          $options.shareURL,
                                          "datasetPublicURLCopy"
                                        )
                                      }, [
                                        createVNode("span", null, [
                                          createVNode(_component_ClipboardDocumentIcon, {
                                            class: "h-5 w-5",
                                            "aria-hidden": "true"
                                          })
                                        ])
                                      ], 8, ["onClick"])
                                    ], 2)
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
                    ]),
                    _: 1
                  })) : createCommentVNode("", true)
                ])
              ])
            ]),
            createVNode("div", { class: "mt-4" }, [
              createVNode("div", { class: "relative" }, [
                createVNode("div", {
                  class: "absolute inset-0 flex items-center",
                  "aria-hidden": "true"
                }, [
                  createVNode("div", { class: "w-full border-t border-gray-100" })
                ]),
                createVNode("div", { class: "relative flex items-center justify-between" }, [
                  createVNode("span", { class: "pr-3 text-md bg-white font-medium text-gray-400" }, " Spectra ")
                ])
              ]),
              createVNode("h1", { class: "mt-2 text-2xl font-bold break-words text-gray-900" }),
              createVNode("div", { class: "my-7" }, [
                createVNode(_component_SpectraViewer, {
                  dataset: $props.dataset.data,
                  project: $props.project.data,
                  study: $props.study.data
                }, null, 8, ["dataset", "project", "study"])
              ])
            ]),
            $props.study.data.tags.length > 0 ? (openBlock(), createBlock("div", {
              key: 0,
              class: "mt-4"
            }, [
              createVNode("div", { class: "relative" }, [
                createVNode("div", {
                  class: "absolute inset-0 flex items-center",
                  "aria-hidden": "true"
                }, [
                  createVNode("div", { class: "w-full border-t border-gray-100" })
                ]),
                createVNode("div", { class: "relative flex items-center justify-between" }, [
                  createVNode("span", { class: "pr-3 text-md bg-white font-medium text-gray-400" }, " Keywords ")
                ])
              ]),
              createVNode("div", { class: "mt-3" }, [
                createVNode("dd", { class: "mt-1 text-md text-gray-900 space-y-5" }, [
                  createVNode("p", null, [
                    (openBlock(true), createBlock(Fragment, null, renderList($props.study.data.tags, (tag) => {
                      return openBlock(), createBlock("span", {
                        key: tag.id,
                        class: "mr-2"
                      }, [
                        createVNode("span", { class: "inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800" }, [
                          (openBlock(), createBlock("svg", {
                            class: "-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400",
                            fill: "currentColor",
                            viewBox: "0 0 8 8"
                          }, [
                            createVNode("circle", {
                              cx: "4",
                              cy: "4",
                              r: "3"
                            })
                          ])),
                          createTextVNode(" " + toDisplayString(tag.name["en"]), 1)
                        ])
                      ]);
                    }), 128))
                  ])
                ])
              ])
            ])) : createCommentVNode("", true),
            $props.study.data.sample.molecules.length > 0 || $props.study.data.sample.description == "" ? (openBlock(), createBlock("div", {
              key: 1,
              class: "mt-4"
            }, [
              createVNode("div", { class: "relative" }, [
                createVNode("div", {
                  class: "absolute inset-0 flex items-center",
                  "aria-hidden": "true"
                }, [
                  createVNode("div", { class: "w-full border-t border-gray-100" })
                ]),
                createVNode("div", { class: "relative flex items-center justify-between" }, [
                  createVNode("span", { class: "pr-3 text-md bg-white font-medium text-gray-400" }, " Sample ")
                ])
              ]),
              $props.study.data.sample.molecules.length > 0 ? (openBlock(), createBlock("div", {
                key: 0,
                class: "mt-3"
              }, [
                createVNode("label", null, "Molecular Composition"),
                createVNode("div", { class: "grid md:grid-cols-2 gap-2 mt-2" }, [
                  createVNode("div", { class: "pr-2" }, [
                    $props.study.data.sample.molecules.length > 0 ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flow-root"
                    }, [
                      createVNode("ul", {
                        role: "list",
                        class: "-mb-8"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList($props.study.data.sample.molecules, (molecule) => {
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
                                      createVNode("a", { class: "font-medium text-gray-900" }, toDisplayString(molecule.standard_inchi), 1)
                                    ])
                                  ]),
                                  createVNode("div", { class: "mt-2 text-sm text-gray-700" }, [
                                    createVNode("div", { class: "rounded-md border my-3 flex justify-center items-center" }, [
                                      createVNode(_component_Depictor2D, {
                                        class: "py-4 -px-4",
                                        molecule: molecule.canonical_smiles
                                      }, null, 8, ["molecule"])
                                    ])
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
                        createVNode("h3", { class: "mt-2 text-sm font-medium text-gray-900" }, " No structures associated with the sample yet! "),
                        createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " Get started by adding a new molecule. ")
                      ])
                    ]))
                  ])
                ])
              ])) : createCommentVNode("", true)
            ])) : createCommentVNode("", true)
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  ssrRenderVNode(_push, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`${ssrInterpolate($data.schema)}`);
      } else {
        return [
          createTextVNode(toDisplayString($data.schema), 1)
        ];
      }
    }),
    _: 1
  }), _parent);
  _push(`<!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Project/Dataset.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Dataset = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Dataset as default
};
