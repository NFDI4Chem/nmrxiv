import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { AtSymbolIcon, CodeBracketIcon, LinkIcon, CheckCircleIcon, ChevronRightIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import { Tab, TabGroup, TabList, TabPanel, TabPanels, Switch, SwitchDescription, SwitchGroup, SwitchLabel } from "@headlessui/vue";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { ref, resolveComponent, mergeProps, withCtx, createTextVNode, toDisplayString, createVNode, withDirectives, vModelText, openBlock, createBlock, useSSRContext } from "vue";
import { S as SelectRich } from "./SelectRich-b58dcc3f.mjs";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass, ssrRenderStyle } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./ToolTip-cf9882a3.mjs";
const _sfc_main = {
  components: {
    Tab,
    TabGroup,
    TabList,
    TabPanel,
    TabPanels,
    Switch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
    AtSymbolIcon,
    CodeBracketIcon,
    LinkIcon,
    JetDialogModal,
    JetSecondaryButton,
    JetButton,
    Link,
    CheckCircleIcon,
    ChevronRightIcon,
    JetInputError,
    SelectRich
  },
  props: ["project"],
  data() {
    return {
      createStudyForm: this.$inertia.form({
        _method: "POST",
        name: "",
        description: "",
        error_message: null,
        team_id: null,
        owner_id: null,
        color: null,
        starred: null,
        project_id: this.project ? this.project.id : null,
        is_public: ref(false),
        license: null
      }),
      createStudyDialog: false,
      licenses: []
    };
  },
  mounted() {
    this.emitter.on("openStudyCreateDialog", () => {
      this.createStudyDialog = true;
    });
  },
  methods: {
    createStudy() {
      this.createStudyForm.owner_id = this.$page.props.auth.user.id;
      this.createStudyForm.team_id = this.$page.props.auth.user.current_team.id;
      this.createStudyForm.project_id = this.project.id;
      this.createStudyForm.post(route("dashboard.study.create"), {
        preserveScroll: true,
        onSuccess: () => {
          this.createStudyDialog = false;
          this.createStudyForm.reset();
        },
        onError: (err) => console.error(err)
      });
    },
    toggleCreateStudyDialog() {
      this.createStudyDialog = !this.createStudyDialog;
      this.createStudyForm.clearErrors();
      if (this.$page.props.licenses) {
        this.licenses = this.$page.props.licenses;
        this.createStudyForm.license = this.licenses.find(
          (l) => l.id == this.project.license_id
        );
      } else {
        axios.get(route("licenses")).then((res) => {
          this.licenses = res.data;
          this.$page.props.licenses = res.data;
          this.createStudyForm.license = this.licenses.find(
            (l) => l.id == this.project.license_id
          );
        });
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_TabGroup = resolveComponent("TabGroup");
  const _component_TabList = resolveComponent("TabList");
  const _component_Tab = resolveComponent("Tab");
  const _component_TabPanels = resolveComponent("TabPanels");
  const _component_TabPanel = resolveComponent("TabPanel");
  const _component_SwitchGroup = resolveComponent("SwitchGroup");
  const _component_Switch = resolveComponent("Switch");
  const _component_SwitchLabel = resolveComponent("SwitchLabel");
  const _component_select_rich = resolveComponent("select-rich");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_button = resolveComponent("jet-button");
  _push(ssrRenderComponent(_component_jet_dialog_modal, mergeProps({
    show: $data.createStudyDialog,
    onClose: ($event) => $data.createStudyDialog = false
  }, _attrs), {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`${ssrInterpolate($props.project.name)} <span class="text-gray-400"${_scopeId}>&gt;</span> New Study `);
      } else {
        return [
          createTextVNode(toDisplayString($props.project.name) + " ", 1),
          createVNode("span", { class: "text-gray-400" }, ">"),
          createTextVNode(" New Study ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="relative z-0 mt-1 rounded-lg cursor-pointer"${_scopeId}><div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6"${_scopeId}><div class="sm:col-span-6"${_scopeId}><label for="name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Name </label><div class="mt-1 flex rounded-md shadow-sm"${_scopeId}><input id="name"${ssrRenderAttr("value", $data.createStudyForm.name)} type="text" name="name" autocomplete="off" class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"${_scopeId}></div>`);
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.createStudyForm.errors.name,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="sm:col-span-6"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_TabGroup, null, {
          default: withCtx(({ $selectedIndex }, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_TabList, { class: "flex items-center" }, {
                default: withCtx((_2, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(ssrRenderComponent(_component_Tab, {
                      as: "template",
                      class: "after:content-['*'] after:ml-0.5 after:text-red-500"
                    }, {
                      default: withCtx(({ selected }, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<button class="${ssrRenderClass([
                            selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                            "px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                          ])}"${_scopeId4}> Write </button>`);
                        } else {
                          return [
                            createVNode("button", {
                              class: [
                                selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                                "px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                              ]
                            }, " Write ", 2)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent4, _scopeId3));
                    _push4(ssrRenderComponent(_component_Tab, { as: "template" }, {
                      default: withCtx(({ selected }, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<button class="${ssrRenderClass([
                            selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                            "ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                          ])}"${_scopeId4}> Preview </button>`);
                        } else {
                          return [
                            createVNode("button", {
                              class: [
                                selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                                "ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                              ]
                            }, " Preview ", 2)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_Tab, {
                        as: "template",
                        class: "after:content-['*'] after:ml-0.5 after:text-red-500"
                      }, {
                        default: withCtx(({ selected }) => [
                          createVNode("button", {
                            class: [
                              selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                              "px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                            ]
                          }, " Write ", 2)
                        ]),
                        _: 1
                      }),
                      createVNode(_component_Tab, { as: "template" }, {
                        default: withCtx(({ selected }) => [
                          createVNode("button", {
                            class: [
                              selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                              "ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                            ]
                          }, " Preview ", 2)
                        ]),
                        _: 1
                      })
                    ];
                  }
                }),
                _: 2
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_TabPanels, { class: "mt-2" }, {
                default: withCtx((_2, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(ssrRenderComponent(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                      default: withCtx((_3, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<label for="comment" class="sr-only"${_scopeId4}>Comment</label><div${_scopeId4}><textarea id="description" name="description" placeholder="Describe this study in atleast 20 characters." rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"${_scopeId4}>${ssrInterpolate(
                            $data.createStudyForm.description
                          )}</textarea></div>`);
                        } else {
                          return [
                            createVNode("label", {
                              for: "comment",
                              class: "sr-only"
                            }, "Comment"),
                            createVNode("div", null, [
                              withDirectives(createVNode("textarea", {
                                id: "description",
                                "onUpdate:modelValue": ($event) => $data.createStudyForm.description = $event,
                                name: "description",
                                placeholder: "Describe this study in atleast 20 characters.",
                                rows: "3",
                                class: "shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                              }, null, 8, ["onUpdate:modelValue"]), [
                                [
                                  vModelText,
                                  $data.createStudyForm.description
                                ]
                              ])
                            ])
                          ];
                        }
                      }),
                      _: 2
                    }, _parent4, _scopeId3));
                    _push4(ssrRenderComponent(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                      default: withCtx((_3, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<div class="border-b"${_scopeId4}><div class="mx-px mt-px px-3 pt-2 pb-12"${_scopeId4}>`);
                          if ($data.createStudyForm.description == "") {
                            _push5(`<span class="text-gray-400 text-sm font-medium"${_scopeId4}> Nothing to preview </span>`);
                          } else {
                            _push5(`<span${_scopeId4}><div class="prose"${_scopeId4}>${_ctx.md(
                              $data.createStudyForm.description
                            )}</div></span>`);
                          }
                          _push5(`</div></div>`);
                        } else {
                          return [
                            createVNode("div", { class: "border-b" }, [
                              createVNode("div", { class: "mx-px mt-px px-3 pt-2 pb-12" }, [
                                $data.createStudyForm.description == "" ? (openBlock(), createBlock("span", {
                                  key: 0,
                                  class: "text-gray-400 text-sm font-medium"
                                }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                  createVNode("div", {
                                    class: "prose",
                                    innerHTML: _ctx.md(
                                      $data.createStudyForm.description
                                    )
                                  }, null, 8, ["innerHTML"])
                                ]))
                              ])
                            ])
                          ];
                        }
                      }),
                      _: 2
                    }, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                        default: withCtx(() => [
                          createVNode("label", {
                            for: "comment",
                            class: "sr-only"
                          }, "Comment"),
                          createVNode("div", null, [
                            withDirectives(createVNode("textarea", {
                              id: "description",
                              "onUpdate:modelValue": ($event) => $data.createStudyForm.description = $event,
                              name: "description",
                              placeholder: "Describe this study in atleast 20 characters.",
                              rows: "3",
                              class: "shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [
                                vModelText,
                                $data.createStudyForm.description
                              ]
                            ])
                          ])
                        ]),
                        _: 1
                      }),
                      createVNode(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "border-b" }, [
                            createVNode("div", { class: "mx-px mt-px px-3 pt-2 pb-12" }, [
                              $data.createStudyForm.description == "" ? (openBlock(), createBlock("span", {
                                key: 0,
                                class: "text-gray-400 text-sm font-medium"
                              }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                createVNode("div", {
                                  class: "prose",
                                  innerHTML: _ctx.md(
                                    $data.createStudyForm.description
                                  )
                                }, null, 8, ["innerHTML"])
                              ]))
                            ])
                          ])
                        ]),
                        _: 1
                      })
                    ];
                  }
                }),
                _: 2
              }, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_TabList, { class: "flex items-center" }, {
                  default: withCtx(() => [
                    createVNode(_component_Tab, {
                      as: "template",
                      class: "after:content-['*'] after:ml-0.5 after:text-red-500"
                    }, {
                      default: withCtx(({ selected }) => [
                        createVNode("button", {
                          class: [
                            selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                            "px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                          ]
                        }, " Write ", 2)
                      ]),
                      _: 1
                    }),
                    createVNode(_component_Tab, { as: "template" }, {
                      default: withCtx(({ selected }) => [
                        createVNode("button", {
                          class: [
                            selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                            "ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                          ]
                        }, " Preview ", 2)
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                }),
                createVNode(_component_TabPanels, { class: "mt-2" }, {
                  default: withCtx(() => [
                    createVNode(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                      default: withCtx(() => [
                        createVNode("label", {
                          for: "comment",
                          class: "sr-only"
                        }, "Comment"),
                        createVNode("div", null, [
                          withDirectives(createVNode("textarea", {
                            id: "description",
                            "onUpdate:modelValue": ($event) => $data.createStudyForm.description = $event,
                            name: "description",
                            placeholder: "Describe this study in atleast 20 characters.",
                            rows: "3",
                            class: "shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                          }, null, 8, ["onUpdate:modelValue"]), [
                            [
                              vModelText,
                              $data.createStudyForm.description
                            ]
                          ])
                        ])
                      ]),
                      _: 1
                    }),
                    createVNode(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                      default: withCtx(() => [
                        createVNode("div", { class: "border-b" }, [
                          createVNode("div", { class: "mx-px mt-px px-3 pt-2 pb-12" }, [
                            $data.createStudyForm.description == "" ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "text-gray-400 text-sm font-medium"
                            }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                              createVNode("div", {
                                class: "prose",
                                innerHTML: _ctx.md(
                                  $data.createStudyForm.description
                                )
                              }, null, 8, ["innerHTML"])
                            ]))
                          ])
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
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.createStudyForm.errors.description,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`<label class="block text-sm font-medium text-gray-700"${_scopeId}><small${_scopeId}><svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-markdown v-align-bottom inline"${_scopeId}><path fill-rule="evenodd" d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.69C0 12.48.52 13 1.15 13h13.69c.64 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"${_scopeId}></path></svg> Styling with Markdown is <span${_scopeId}>supported</span></small></label></div><div class="sm:col-span-6"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_SwitchGroup, {
          as: "div",
          class: "flex items-center"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_Switch, {
                modelValue: $data.createStudyForm.is_public,
                "onUpdate:modelValue": ($event) => $data.createStudyForm.is_public = $event,
                class: [
                  $data.createStudyForm.is_public ? "bg-green-600" : "bg-gray-200",
                  "relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                ]
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(`<span class="${ssrRenderClass([
                      $data.createStudyForm.is_public ? "translate-x-5" : "translate-x-0",
                      "pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                    ])}"${_scopeId3}><span class="${ssrRenderClass([
                      $data.createStudyForm.is_public ? "opacity-0 ease-out duration-100" : "opacity-100 ease-in duration-200",
                      "absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                    ])}" aria-hidden="true"${_scopeId3}><svg id="Capa_1" class="h-3 w-3 text-gray-400 inline" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="${ssrRenderStyle({ "enable-background": "new 0 0 512\n                                                    512" })}" xml:space="preserve"${_scopeId3}><g${_scopeId3}><g${_scopeId3}><path d="M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32
                                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333
                                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292
                                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51
                                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667
                                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64
                                                s85.333,38.281,85.333,85.333V192z"${_scopeId3}></path></g></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g><g${_scopeId3}></g></svg></span><span class="${ssrRenderClass([
                      $data.createStudyForm.is_public ? "opacity-100 ease-in duration-200" : "opacity-0 ease-out duration-100",
                      "absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                    ])}" aria-hidden="true"${_scopeId3}><svg class="h-3 w-3 text-green-400 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="512" height="512"${_scopeId3}><g id="globe"${_scopeId3}><path d="M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z"${_scopeId3}></path><path d="M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z"${_scopeId3}></path><path d="M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z"${_scopeId3}></path></g></svg></span></span>`);
                  } else {
                    return [
                      createVNode("span", {
                        class: [
                          $data.createStudyForm.is_public ? "translate-x-5" : "translate-x-0",
                          "pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                        ]
                      }, [
                        createVNode("span", {
                          class: [
                            $data.createStudyForm.is_public ? "opacity-0 ease-out duration-100" : "opacity-100 ease-in duration-200",
                            "absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                          ],
                          "aria-hidden": "true"
                        }, [
                          (openBlock(), createBlock("svg", {
                            id: "Capa_1",
                            class: "h-3 w-3 text-gray-400 inline",
                            version: "1.1",
                            xmlns: "http://www.w3.org/2000/svg",
                            "xmlns:xlink": "http://www.w3.org/1999/xlink",
                            x: "0px",
                            y: "0px",
                            viewBox: "0 0 512 512",
                            style: { "enable-background": "new 0 0 512\n                                                    512" },
                            "xml:space": "preserve"
                          }, [
                            createVNode("g", null, [
                              createVNode("g", null, [
                                createVNode("path", { d: "M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32\n                                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333\n                                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292\n                                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51\n                                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667\n                                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64\n                                                s85.333,38.281,85.333,85.333V192z" })
                              ])
                            ]),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g"),
                            createVNode("g")
                          ]))
                        ], 2),
                        createVNode("span", {
                          class: [
                            $data.createStudyForm.is_public ? "opacity-100 ease-in duration-200" : "opacity-0 ease-out duration-100",
                            "absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                          ],
                          "aria-hidden": "true"
                        }, [
                          (openBlock(), createBlock("svg", {
                            class: "h-3 w-3 text-green-400 inline",
                            xmlns: "http://www.w3.org/2000/svg",
                            viewBox: "0 0 64 64",
                            width: "512",
                            height: "512"
                          }, [
                            createVNode("g", { id: "globe" }, [
                              createVNode("path", { d: "M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z" }),
                              createVNode("path", { d: "M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z" }),
                              createVNode("path", { d: "M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z" })
                            ])
                          ]))
                        ], 2)
                      ], 2)
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<span class="flex-grow flex ml-4 flex-col"${_scopeId2}>`);
              if (!$data.createStudyForm.is_public) {
                _push3(`<span${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_SwitchLabel, {
                  as: "span",
                  class: "text-sm font-medium text-gray-900",
                  passive: ""
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(` Private`);
                    } else {
                      return [
                        createTextVNode(" Private")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
                _push3(`</span>`);
              } else {
                _push3(`<span${_scopeId2}>`);
                _push3(ssrRenderComponent(_component_SwitchLabel, {
                  as: "span",
                  class: "text-sm font-medium text-gray-900",
                  passive: ""
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(`Public`);
                    } else {
                      return [
                        createTextVNode("Public")
                      ];
                    }
                  }),
                  _: 1
                }, _parent3, _scopeId2));
                _push3(`</span>`);
              }
              _push3(`</span>`);
            } else {
              return [
                createVNode(_component_Switch, {
                  modelValue: $data.createStudyForm.is_public,
                  "onUpdate:modelValue": ($event) => $data.createStudyForm.is_public = $event,
                  class: [
                    $data.createStudyForm.is_public ? "bg-green-600" : "bg-gray-200",
                    "relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  ]
                }, {
                  default: withCtx(() => [
                    createVNode("span", {
                      class: [
                        $data.createStudyForm.is_public ? "translate-x-5" : "translate-x-0",
                        "pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                      ]
                    }, [
                      createVNode("span", {
                        class: [
                          $data.createStudyForm.is_public ? "opacity-0 ease-out duration-100" : "opacity-100 ease-in duration-200",
                          "absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                        ],
                        "aria-hidden": "true"
                      }, [
                        (openBlock(), createBlock("svg", {
                          id: "Capa_1",
                          class: "h-3 w-3 text-gray-400 inline",
                          version: "1.1",
                          xmlns: "http://www.w3.org/2000/svg",
                          "xmlns:xlink": "http://www.w3.org/1999/xlink",
                          x: "0px",
                          y: "0px",
                          viewBox: "0 0 512 512",
                          style: { "enable-background": "new 0 0 512\n                                                    512" },
                          "xml:space": "preserve"
                        }, [
                          createVNode("g", null, [
                            createVNode("g", null, [
                              createVNode("path", { d: "M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32\n                                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333\n                                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292\n                                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51\n                                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667\n                                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64\n                                                s85.333,38.281,85.333,85.333V192z" })
                            ])
                          ]),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g"),
                          createVNode("g")
                        ]))
                      ], 2),
                      createVNode("span", {
                        class: [
                          $data.createStudyForm.is_public ? "opacity-100 ease-in duration-200" : "opacity-0 ease-out duration-100",
                          "absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                        ],
                        "aria-hidden": "true"
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-3 w-3 text-green-400 inline",
                          xmlns: "http://www.w3.org/2000/svg",
                          viewBox: "0 0 64 64",
                          width: "512",
                          height: "512"
                        }, [
                          createVNode("g", { id: "globe" }, [
                            createVNode("path", { d: "M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z" }),
                            createVNode("path", { d: "M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z" }),
                            createVNode("path", { d: "M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z" })
                          ])
                        ]))
                      ], 2)
                    ], 2)
                  ]),
                  _: 1
                }, 8, ["modelValue", "onUpdate:modelValue", "class"]),
                createVNode("span", { class: "flex-grow flex ml-4 flex-col" }, [
                  !$data.createStudyForm.is_public ? (openBlock(), createBlock("span", { key: 0 }, [
                    createVNode(_component_SwitchLabel, {
                      as: "span",
                      class: "text-sm font-medium text-gray-900",
                      passive: ""
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" Private")
                      ]),
                      _: 1
                    })
                  ])) : (openBlock(), createBlock("span", { key: 1 }, [
                    createVNode(_component_SwitchLabel, {
                      as: "span",
                      class: "text-sm font-medium text-gray-900",
                      passive: ""
                    }, {
                      default: withCtx(() => [
                        createTextVNode("Public")
                      ]),
                      _: 1
                    })
                  ]))
                ])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></div><div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-1"${_scopeId}><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_select_rich, {
          selected: $data.createStudyForm.license,
          "onUpdate:selected": ($event) => $data.createStudyForm.license = $event,
          label: "License",
          items: $data.licenses
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.createStudyForm.errors.license,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "relative z-0 mt-1 rounded-lg cursor-pointer" }, [
            createVNode("div", { class: "mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6" }, [
              createVNode("div", { class: "sm:col-span-6" }, [
                createVNode("label", {
                  for: "name",
                  class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                }, " Name "),
                createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                  withDirectives(createVNode("input", {
                    id: "name",
                    "onUpdate:modelValue": ($event) => $data.createStudyForm.name = $event,
                    type: "text",
                    name: "name",
                    autocomplete: "off",
                    class: "flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                  }, null, 8, ["onUpdate:modelValue"]), [
                    [vModelText, $data.createStudyForm.name]
                  ])
                ]),
                createVNode(_component_jet_input_error, {
                  message: $data.createStudyForm.errors.name,
                  class: "mt-2"
                }, null, 8, ["message"])
              ]),
              createVNode("div", { class: "sm:col-span-6" }, [
                createVNode(_component_TabGroup, null, {
                  default: withCtx(({ $selectedIndex }) => [
                    createVNode(_component_TabList, { class: "flex items-center" }, {
                      default: withCtx(() => [
                        createVNode(_component_Tab, {
                          as: "template",
                          class: "after:content-['*'] after:ml-0.5 after:text-red-500"
                        }, {
                          default: withCtx(({ selected }) => [
                            createVNode("button", {
                              class: [
                                selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                                "px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                              ]
                            }, " Write ", 2)
                          ]),
                          _: 1
                        }),
                        createVNode(_component_Tab, { as: "template" }, {
                          default: withCtx(({ selected }) => [
                            createVNode("button", {
                              class: [
                                selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                                "ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                              ]
                            }, " Preview ", 2)
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    }),
                    createVNode(_component_TabPanels, { class: "mt-2" }, {
                      default: withCtx(() => [
                        createVNode(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                          default: withCtx(() => [
                            createVNode("label", {
                              for: "comment",
                              class: "sr-only"
                            }, "Comment"),
                            createVNode("div", null, [
                              withDirectives(createVNode("textarea", {
                                id: "description",
                                "onUpdate:modelValue": ($event) => $data.createStudyForm.description = $event,
                                name: "description",
                                placeholder: "Describe this study in atleast 20 characters.",
                                rows: "3",
                                class: "shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                              }, null, 8, ["onUpdate:modelValue"]), [
                                [
                                  vModelText,
                                  $data.createStudyForm.description
                                ]
                              ])
                            ])
                          ]),
                          _: 1
                        }),
                        createVNode(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "border-b" }, [
                              createVNode("div", { class: "mx-px mt-px px-3 pt-2 pb-12" }, [
                                $data.createStudyForm.description == "" ? (openBlock(), createBlock("span", {
                                  key: 0,
                                  class: "text-gray-400 text-sm font-medium"
                                }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                  createVNode("div", {
                                    class: "prose",
                                    innerHTML: _ctx.md(
                                      $data.createStudyForm.description
                                    )
                                  }, null, 8, ["innerHTML"])
                                ]))
                              ])
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
                createVNode(_component_jet_input_error, {
                  message: $data.createStudyForm.errors.description,
                  class: "mt-2"
                }, null, 8, ["message"]),
                createVNode("label", { class: "block text-sm font-medium text-gray-700" }, [
                  createVNode("small", null, [
                    (openBlock(), createBlock("svg", {
                      "aria-hidden": "true",
                      height: "16",
                      viewBox: "0 0 16 16",
                      version: "1.1",
                      width: "16",
                      "data-view-component": "true",
                      class: "octicon octicon-markdown v-align-bottom inline"
                    }, [
                      createVNode("path", {
                        "fill-rule": "evenodd",
                        d: "M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.69C0 12.48.52 13 1.15 13h13.69c.64 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"
                      })
                    ])),
                    createTextVNode(" Styling with Markdown is "),
                    createVNode("span", {
                      onClick: ($event) => $data.createStudyForm.description = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
                    }, "supported", 8, ["onClick"])
                  ])
                ])
              ]),
              createVNode("div", { class: "sm:col-span-6" }, [
                createVNode(_component_SwitchGroup, {
                  as: "div",
                  class: "flex items-center"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_Switch, {
                      modelValue: $data.createStudyForm.is_public,
                      "onUpdate:modelValue": ($event) => $data.createStudyForm.is_public = $event,
                      class: [
                        $data.createStudyForm.is_public ? "bg-green-600" : "bg-gray-200",
                        "relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                      ]
                    }, {
                      default: withCtx(() => [
                        createVNode("span", {
                          class: [
                            $data.createStudyForm.is_public ? "translate-x-5" : "translate-x-0",
                            "pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                          ]
                        }, [
                          createVNode("span", {
                            class: [
                              $data.createStudyForm.is_public ? "opacity-0 ease-out duration-100" : "opacity-100 ease-in duration-200",
                              "absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                            ],
                            "aria-hidden": "true"
                          }, [
                            (openBlock(), createBlock("svg", {
                              id: "Capa_1",
                              class: "h-3 w-3 text-gray-400 inline",
                              version: "1.1",
                              xmlns: "http://www.w3.org/2000/svg",
                              "xmlns:xlink": "http://www.w3.org/1999/xlink",
                              x: "0px",
                              y: "0px",
                              viewBox: "0 0 512 512",
                              style: { "enable-background": "new 0 0 512\n                                                    512" },
                              "xml:space": "preserve"
                            }, [
                              createVNode("g", null, [
                                createVNode("g", null, [
                                  createVNode("path", { d: "M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32\n                                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333\n                                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292\n                                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51\n                                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667\n                                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64\n                                                s85.333,38.281,85.333,85.333V192z" })
                                ])
                              ]),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g"),
                              createVNode("g")
                            ]))
                          ], 2),
                          createVNode("span", {
                            class: [
                              $data.createStudyForm.is_public ? "opacity-100 ease-in duration-200" : "opacity-0 ease-out duration-100",
                              "absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
                            ],
                            "aria-hidden": "true"
                          }, [
                            (openBlock(), createBlock("svg", {
                              class: "h-3 w-3 text-green-400 inline",
                              xmlns: "http://www.w3.org/2000/svg",
                              viewBox: "0 0 64 64",
                              width: "512",
                              height: "512"
                            }, [
                              createVNode("g", { id: "globe" }, [
                                createVNode("path", { d: "M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z" }),
                                createVNode("path", { d: "M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z" }),
                                createVNode("path", { d: "M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z" })
                              ])
                            ]))
                          ], 2)
                        ], 2)
                      ]),
                      _: 1
                    }, 8, ["modelValue", "onUpdate:modelValue", "class"]),
                    createVNode("span", { class: "flex-grow flex ml-4 flex-col" }, [
                      !$data.createStudyForm.is_public ? (openBlock(), createBlock("span", { key: 0 }, [
                        createVNode(_component_SwitchLabel, {
                          as: "span",
                          class: "text-sm font-medium text-gray-900",
                          passive: ""
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Private")
                          ]),
                          _: 1
                        })
                      ])) : (openBlock(), createBlock("span", { key: 1 }, [
                        createVNode(_component_SwitchLabel, {
                          as: "span",
                          class: "text-sm font-medium text-gray-900",
                          passive: ""
                        }, {
                          default: withCtx(() => [
                            createTextVNode("Public")
                          ]),
                          _: 1
                        })
                      ]))
                    ])
                  ]),
                  _: 1
                })
              ])
            ]),
            createVNode("div", { class: "mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-1" }, [
              createVNode("div", null, [
                createVNode(_component_select_rich, {
                  selected: $data.createStudyForm.license,
                  "onUpdate:selected": ($event) => $data.createStudyForm.license = $event,
                  label: "License",
                  items: $data.licenses
                }, null, 8, ["selected", "onUpdate:selected", "items"]),
                createVNode(_component_jet_input_error, {
                  message: $data.createStudyForm.errors.license,
                  class: "mt-2"
                }, null, 8, ["message"])
              ])
            ])
          ])
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, { onClick: $options.toggleCreateStudyDialog }, {
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
          class: ["ml-2", { "opacity-25": $data.createStudyForm.processing }],
          disabled: $data.createStudyForm.processing,
          onClick: $options.createStudy
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Save `);
            } else {
              return [
                createTextVNode(" Save ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, { onClick: $options.toggleCreateStudyDialog }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_button, {
            class: ["ml-2", { "opacity-25": $data.createStudyForm.processing }],
            disabled: $data.createStudyForm.processing,
            onClick: $options.createStudy
          }, {
            default: withCtx(() => [
              createTextVNode(" Save ")
            ]),
            _: 1
          }, 8, ["class", "disabled", "onClick"])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/Partials/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const StudyCreate = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  StudyCreate as default
};
