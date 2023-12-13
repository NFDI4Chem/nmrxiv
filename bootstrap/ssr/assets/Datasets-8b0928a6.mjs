import StudyContent from "./Content-5913e7b1.mjs";
import { L as LoadingButton } from "./LoadingButton-8ae902b0.mjs";
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { S as SpectraViewer } from "./SpectraViewer-3065e728.mjs";
import { S as SpectraEditor } from "./AppLayout-b93d1c11.mjs";
import { C as Citation } from "./Citation-80069afd.mjs";
import { resolveComponent, withCtx, createVNode, createTextVNode, Transition, openBlock, createBlock, createCommentVNode, withDirectives, vModelText, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderClass, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./Layout-114f561c.mjs";
import "@inertiajs/vue3";
import "./Details-f3d36d90.mjs";
import "./ActionMessage-3207b224.mjs";
import "@heroicons/vue/24/outline";
import "./InputError-6ae0b65c.mjs";
import "./Activity-90a526a4.mjs";
import "vue3-colorpicker";
/* empty css                 */import "./Button-8d6976fd.mjs";
import "./SelectRich-b58dcc3f.mjs";
import "./ToolTip-cf9882a3.mjs";
import "./SecondaryButton-3cee9e05.mjs";
import "@sipec/vue3-tags-input";
import "./AccessDialogue-1fdd1bde.mjs";
import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./ConfirmationModal-d0b47856.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./DangerButton-9c23157c.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Input-f9a8cf77.mjs";
import "./Label-c5144ba4.mjs";
import "./SectionBorder-7d1f222a.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@vueuse/core";
import "./AnnouncementBanner-3ebe3621.mjs";
import "popper.js";
import "./FlashMessages-f0051c19.mjs";
import "vue3-slider";
import "openchemlib/full.js";
import "dropzone";
import "axios-retry";
import "@heroicons/vue/20/solid";
import "axios";
const _sfc_main = {
  components: {
    StudyContent,
    LoadingButton,
    ShareIcon,
    ClipboardDocumentIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    SpectraEditor,
    SpectraViewer,
    Citation
  },
  props: [
    "study",
    "project",
    "current",
    "team",
    "members",
    "availableRoles",
    "studyPermissions",
    "studyRole",
    "model"
  ],
  data() {
    return {
      autoSaving: false,
      selectedDataset: null,
      currentMolecules: [],
      license: "No license selected",
      loading: false,
      spectraError: null,
      role: this.studyRole
    };
  },
  computed: {
    currentStep() {
      return this.steps.filter((s) => s.status == "current")[0];
    },
    url() {
      return String(this.$page.props.url);
    },
    shareURL() {
      return this.selectedDataset.public_url;
    },
    canUpdateStudy() {
      return this.studyPermissions ? this.studyPermissions.canUpdateStudy : false;
    }
  },
  mounted() {
    const urlSearchParams = new URLSearchParams(window.location.search);
    const params = Object.fromEntries(urlSearchParams.entries());
    let dsId = params["dsid"];
    this.study.datasets.forEach((ds) => {
      if (ds.id == dsId) {
        this.selectedDataset = ds;
      }
    });
    if (!this.selectedDataset) {
      this.selectedDataset = this.study.datasets[0];
    }
    this.$nextTick(function() {
      if (this.$refs.spectraEditorREF) {
        this.$refs.spectraEditorREF.registerEvents();
      }
    });
  },
  beforeMount() {
    if (this.study.license_id) {
      this.loading = true;
      axios.get(route("license", this.study.license_id)).then((res) => {
        this.license = res.data[0].title;
      }).finally(() => {
        this.loading = false;
      });
    }
  },
  methods: {
    openDatasetCreateDialog() {
      this.emitter.emit("openDatasetCreateDialog", {
        draft_id: this.project.draft_id,
        return_url: "/studies/" + this.study.id + "/datasets"
      });
    },
    updateDataSet() {
      if (this.selectedDataset) {
        if (this.selectedSpectraData != null) {
          this.autoSaving = true;
          axios.post(
            "/dashboard/datasets/" + this.selectedDataset.id + "/nmriumInfo",
            {
              spectra: this.selectedSpectraData,
              molecules: this.currentMolecules
            }
          ).catch((error) => {
            console.log(error.data);
          }).then((response) => {
            let i = 0;
            this.study.datasets.forEach((ds) => {
              if (ds.id == response.data.id) {
                this.study.datasets[i] = response.data;
                this.selectedDataset = this.study.datasets[i];
                if (this.selectedDataset.nmrium_info) {
                  let nmrium_info = JSON.parse(
                    this.selectedDataset.nmrium_info
                  );
                  if (nmrium_info) {
                    this.selectedSpectraData = nmrium_info["spectra"];
                  }
                }
              }
              i = i + 1;
            });
            this.autoSaving = false;
          });
        }
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_study_content = resolveComponent("study-content");
  const _component_Menu = resolveComponent("Menu");
  const _component_MenuButton = resolveComponent("MenuButton");
  const _component_ShareIcon = resolveComponent("ShareIcon");
  const _component_MenuItems = resolveComponent("MenuItems");
  const _component_MenuItem = resolveComponent("MenuItem");
  const _component_ClipboardDocumentIcon = resolveComponent("ClipboardDocumentIcon");
  const _component_SpectraEditor = resolveComponent("SpectraEditor");
  const _component_SpectraViewer = resolveComponent("SpectraViewer");
  const _component_loading_button = resolveComponent("loading-button");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_study_content, {
    model: "study",
    project: $props.project,
    study: $props.study,
    team: $props.team,
    members: $props.members,
    "available-roles": $props.availableRoles,
    "study-permissions": $props.studyPermissions,
    "study-role": $props.studyRole,
    current: "Datasets"
  }, {
    "study-section": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="px-4 sm:px-6 md:px-0"${_scopeId}>`);
        if ($data.selectedDataset && $props.study.is_public) {
          _push2(`<div class="p-4"${_scopeId}><div class="float-right"${_scopeId}><div class="flex-0.5 self-center"${_scopeId}>`);
          if ($data.selectedDataset && $props.study.is_public) {
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
                              ])}"${_scopeId4}><div class="flex-grow"${_scopeId4}><input id="datasetPublicURLCopy" readonly type="text"${ssrRenderAttr(
                                "value",
                                $options.shareURL
                              )} class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"${_scopeId4}></div><button type="button" class="-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"${_scopeId4}><span${_scopeId4}>`);
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
          _push2(`</div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="p-6"${_scopeId}>`);
        if ($data.selectedDataset) {
          _push2(`<div class="mb-7"${_scopeId}>`);
          if ($options.canUpdateStudy) {
            _push2(ssrRenderComponent(_component_SpectraEditor, {
              ref: "spectraEditorREF",
              dataset: $data.selectedDataset,
              project: $props.project,
              study: $props.study
            }, null, _parent2, _scopeId));
          } else {
            _push2(ssrRenderComponent(_component_SpectraViewer, {
              ref: "spectraViewerREF",
              dataset: $data.selectedDataset,
              project: $props.project,
              study: $props.study
            }, null, _parent2, _scopeId));
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="pt-3"${_scopeId}><div${_scopeId}><label class="block text-sm font-medium text-gray-700"${_scopeId}> License </label><div class="p-1 pr-2 grid grid-cols-2"${_scopeId}><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_loading_button, { loading: $data.loading }, null, _parent2, _scopeId));
        if (!$data.loading) {
          _push2(`<input id="study-name"${ssrRenderAttr("value", $data.license)} readonly type="text" name="study-name" class="block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div></div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "px-4 sm:px-6 md:px-0" }, [
            $data.selectedDataset && $props.study.is_public ? (openBlock(), createBlock("div", {
              key: 0,
              class: "p-4"
            }, [
              createVNode("div", { class: "float-right" }, [
                createVNode("div", { class: "flex-0.5 self-center" }, [
                  $data.selectedDataset && $props.study.is_public ? (openBlock(), createBlock(_component_Menu, {
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
            ])) : createCommentVNode("", true),
            createVNode("div", { class: "p-6" }, [
              $data.selectedDataset ? (openBlock(), createBlock("div", {
                key: 0,
                class: "mb-7"
              }, [
                $options.canUpdateStudy ? (openBlock(), createBlock(_component_SpectraEditor, {
                  key: 0,
                  ref: "spectraEditorREF",
                  dataset: $data.selectedDataset,
                  project: $props.project,
                  study: $props.study
                }, null, 8, ["dataset", "project", "study"])) : (openBlock(), createBlock(_component_SpectraViewer, {
                  key: 1,
                  ref: "spectraViewerREF",
                  dataset: $data.selectedDataset,
                  project: $props.project,
                  study: $props.study
                }, null, 8, ["dataset", "project", "study"]))
              ])) : createCommentVNode("", true),
              createVNode("div", { class: "pt-3" }, [
                createVNode("div", null, [
                  createVNode("label", { class: "block text-sm font-medium text-gray-700" }, " License "),
                  createVNode("div", { class: "p-1 pr-2 grid grid-cols-2" }, [
                    createVNode("div", null, [
                      createVNode(_component_loading_button, { loading: $data.loading }, null, 8, ["loading"]),
                      !$data.loading ? withDirectives((openBlock(), createBlock("input", {
                        key: 0,
                        id: "study-name",
                        "onUpdate:modelValue": ($event) => $data.license = $event,
                        readonly: "",
                        type: "text",
                        name: "study-name",
                        class: "block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"
                      }, null, 8, ["onUpdate:modelValue"])), [
                        [vModelText, $data.license]
                      ]) : createCommentVNode("", true)
                    ])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/Datasets.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Datasets = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Datasets as default
};
