import { ref, resolveComponent, mergeProps, withCtx, createTextVNode, toDisplayString, createVNode, withDirectives, vModelText, openBlock, createBlock, createCommentVNode, vModelRadio, useSSRContext } from "vue";
import { J as JetActionMessage } from "./ActionMessage-3207b224.mjs";
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot, Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions, Tab, TabGroup, TabList, TabPanel, TabPanels, Switch, SwitchGroup, SwitchLabel } from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import { LinkIcon, PlusSmallIcon, QuestionMarkCircleIcon, ExclamationCircleIcon, ClipboardDocumentIcon, CheckIcon, ChevronDownIcon } from "@heroicons/vue/24/solid";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import StudyActivity from "./Activity-90a526a4.mjs";
import { ColorPicker } from "vue3-colorpicker";
/* empty css                 */import { J as JetButton } from "./Button-8d6976fd.mjs";
import { S as SelectRich } from "./SelectRich-b58dcc3f.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import VueTagsInput from "@sipec/vue3-tags-input";
import { ssrRenderComponent, ssrRenderStyle, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderClass, ssrLooseEqual } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./ToolTip-cf9882a3.mjs";
const publishingOptions = [
  {
    value: "viewer",
    title: "Viewer",
    description: "Anyone on the internet with this link can view",
    current: true
  },
  {
    value: "commentor",
    title: "Commentor",
    description: "Anyone on the internet with this link can comment",
    current: false
  },
  {
    value: "editor",
    title: "Editor",
    description: "Anyone on the internet with this link can edit (sign in required)",
    current: false
  }
];
const _sfc_main = {
  components: {
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    JetInputError,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
    StudyActivity,
    Tab,
    JetActionMessage,
    JetButton,
    JetSecondaryButton,
    TabGroup,
    TabList,
    TabPanel,
    TabPanels,
    LinkIcon,
    PlusSmallIcon,
    QuestionMarkCircleIcon,
    ExclamationCircleIcon,
    XMarkIcon,
    ColorPicker,
    Switch,
    SwitchGroup,
    SwitchLabel,
    ClipboardDocumentIcon,
    CheckIcon,
    ChevronDownIcon,
    SelectRich,
    VueTagsInput
  },
  props: ["study", "role", "studyPermissions"],
  setup() {
    const activityDetailsElement = ref(null);
    return {
      activityDetailsElement
    };
  },
  data() {
    return {
      form: this.$inertia.form({
        _method: "PUT",
        name: this.study.name,
        description: this.study.description,
        error_message: null,
        team_id: this.study.team_id,
        owner_id: this.study.owner_id,
        color: this.study.color,
        starred: this.study.starred,
        is_public: this.study.is_public,
        access: this.study.access,
        access_type: this.study.access_type,
        doi: this.study.doi,
        license: null,
        license_id: null,
        tag: "",
        tags_array: [],
        tags: []
      }),
      open: false,
      licenses: [],
      selectedAccessType: publishingOptions.filter(
        (option) => option.value == this.study.access_type
      )[0],
      linkAccess: this.study.access == "link",
      tag: "",
      tags: this.getTags(),
      open: false,
      selectedAccessType: publishingOptions.filter(
        (option) => option.value == this.study.access_type
      )[0],
      linkAccess: this.study.access == "link"
    };
  },
  computed: {
    canUpdateStudy() {
      return this.studyPermissions ? this.studyPermissions.canUpdateStudy : false;
    }
  },
  mounted() {
    const initialise = () => {
      this.loadLicenses();
      this.open = true;
    };
    this.emitter.all.set("openStudyDetails", [initialise]);
  },
  methods: {
    toggleDetails() {
      this.loadLicenses();
      this.open = !this.open;
      this.getTags();
    },
    loadLicenses() {
      if (this.$page.props.licenses) {
        this.licenses = this.$page.props.licenses;
        this.form.license = this.licenses.find(
          (l) => l.id == this.study.license_id
        );
      } else {
        axios.get(route("licenses")).then((res) => {
          this.licenses = res.data;
          this.$page.props.licenses = res.data;
          this.form.license = this.licenses.find(
            (l) => l.id == this.study.license_id
          );
        });
      }
    },
    getTags() {
      if (this.study && this.study.tags) {
        let tags = [];
        this.study.tags.forEach((t) => {
          tags.push({
            text: t.name["en"]
          });
        });
        return tags;
      }
      return [];
    },
    updateStudy() {
      this.form.owner_id = this.study.owner_id;
      this.form.team_id = this.study.team_id;
      this.form.project_id = this.study.project_id;
      if (this.form.license) {
        this.form.license_id = this.form.license.id;
      }
      if (this.linkAccess) {
        this.form.access = "link";
        this.form.access_type = this.selectedAccessType.value;
      } else {
        this.form.access = "restricted";
      }
      this.form.tags_array = this.form.tags.map((t) => t.text);
      if (this.form.tag != "") {
        let tags = this.form.tag.split(",");
        if (tags.length > 0) {
          this.form.tags_array = this.form.tags_array.concat(tags);
        }
      }
      this.form.tags_array = [...new Set(this.form.tags_array)];
      this.form.post(route("dashboard.study.update", this.study.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.open = false;
          this.form.tag = "";
        },
        onError: (err) => {
        }
      });
    },
    toggleActivityDetails() {
      this.activityDetailsElement.toggleDetails();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_TransitionRoot = resolveComponent("TransitionRoot");
  const _component_Dialog = resolveComponent("Dialog");
  const _component_DialogOverlay = resolveComponent("DialogOverlay");
  const _component_TransitionChild = resolveComponent("TransitionChild");
  const _component_DialogTitle = resolveComponent("DialogTitle");
  const _component_XMarkIcon = resolveComponent("XMarkIcon");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_TabGroup = resolveComponent("TabGroup");
  const _component_TabList = resolveComponent("TabList");
  const _component_Tab = resolveComponent("Tab");
  const _component_TabPanels = resolveComponent("TabPanels");
  const _component_TabPanel = resolveComponent("TabPanel");
  const _component_vue_tags_input = resolveComponent("vue-tags-input");
  const _component_color_picker = resolveComponent("color-picker");
  const _component_select_rich = resolveComponent("select-rich");
  const _component_ClipboardDocumentIcon = resolveComponent("ClipboardDocumentIcon");
  const _component_QuestionMarkCircleIcon = resolveComponent("QuestionMarkCircleIcon");
  const _component_ExclamationCircleIcon = resolveComponent("ExclamationCircleIcon");
  const _component_study_activity = resolveComponent("study-activity");
  const _component_jet_action_message = resolveComponent("jet-action-message");
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
          class: "fixed inset-0 z-50"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<div class="absolute inset-0"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_DialogOverlay, { class: "absolute inset-0" }, null, _parent3, _scopeId2));
              _push3(`<div class="fixed inset-y-0 pl-16 max-w-full right-0 flex"${_scopeId2}>`);
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
                    _push4(`<div class="w-screen max-w-2xl"${_scopeId3}><div class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl"${_scopeId3}><div class="flex-1 h-0 overflow-y-auto"${_scopeId3}><div class="py-6 px-4 sm:px-6" style="${ssrRenderStyle([
                      $props.study.color && $props.study.color != "" ? "background-color:" + $props.study.color : "background-color:rgb(75,75,75)"
                    ])}"${_scopeId3}><div class="flex items-center justify-between"${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_DialogTitle, { class: "text-lg font-medium text-white" }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`${ssrInterpolate($props.study.name)}`);
                        } else {
                          return [
                            createTextVNode(toDisplayString($props.study.name), 1)
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                    _push4(`<div class="ml-3 h-7 flex items-center"${_scopeId3}><button type="button" class="rounded-md hover:text-white"${_scopeId3}><span class="sr-only"${_scopeId3}>Close panel</span>`);
                    _push4(ssrRenderComponent(_component_XMarkIcon, {
                      class: "h-6 w-6",
                      "aria-hidden": "true"
                    }, null, _parent4, _scopeId3));
                    _push4(`</button></div></div></div><div class="flex-1 flex flex-col justify-between"${_scopeId3}><div class="px-4 divide-y divide-gray-200 sm:px-6"${_scopeId3}><div class="space-y-6 pt-6 pb-5"${_scopeId3}><div${_scopeId3}><label for="study-name" class="block text-sm font-medium text-gray-900 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId3}> Study name </label><div class="mt-1"${_scopeId3}><input id="study-name"${ssrRenderAttr("value", $data.form.name)} type="text"${ssrIncludeBooleanAttr(
                      !$options.canUpdateStudy
                    ) ? " readonly" : ""} name="study-name" class="block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"${_scopeId3}></div>`);
                    _push4(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.form.errors.name,
                      class: "mt-2"
                    }, null, _parent4, _scopeId3));
                    _push4(`</div><div${_scopeId3}><label for="description" class="block text-sm font-medium text-gray-900 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId3}> Description </label><div class="mt-1"${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_TabGroup, null, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(ssrRenderComponent(_component_TabList, { class: "flex items-center" }, {
                            default: withCtx((_5, _push6, _parent6, _scopeId5) => {
                              if (_push6) {
                                _push6(ssrRenderComponent(_component_Tab, { as: "template" }, {
                                  default: withCtx(({
                                    selected
                                  }, _push7, _parent7, _scopeId6) => {
                                    if (_push7) {
                                      _push7(`<button class="${ssrRenderClass([
                                        selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                                        "px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                                      ])}"${_scopeId6}> Write </button>`);
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
                                  _: 1
                                }, _parent6, _scopeId5));
                                _push6(ssrRenderComponent(_component_Tab, { as: "template" }, {
                                  default: withCtx(({
                                    selected
                                  }, _push7, _parent7, _scopeId6) => {
                                    if (_push7) {
                                      _push7(`<button class="${ssrRenderClass([
                                        selected ? "text-gray-900 bg-gray-100 hover:bg-gray-200" : "text-gray-500 hover:text-gray-900 bg-white hover:bg-gray-100",
                                        "ml-2 px-3 py-1.5 border border-transparent text-sm font-medium rounded-md"
                                      ])}"${_scopeId6}> Preview </button>`);
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
                                  _: 1
                                }, _parent6, _scopeId5));
                              } else {
                                return [
                                  createVNode(_component_Tab, { as: "template" }, {
                                    default: withCtx(({
                                      selected
                                    }) => [
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
                                    default: withCtx(({
                                      selected
                                    }) => [
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
                            _: 1
                          }, _parent5, _scopeId4));
                          _push5(ssrRenderComponent(_component_TabPanels, { class: "mt-2" }, {
                            default: withCtx((_5, _push6, _parent6, _scopeId5) => {
                              if (_push6) {
                                _push6(ssrRenderComponent(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                                  default: withCtx((_6, _push7, _parent7, _scopeId6) => {
                                    if (_push7) {
                                      _push7(`<label for="comment" class="sr-only"${_scopeId6}>Comment</label><div${_scopeId6}><textarea id="description"${ssrIncludeBooleanAttr(
                                        !$options.canUpdateStudy
                                      ) ? " readonly" : ""} name="description" placeholder="Describe this study" rows="3" class="shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md"${_scopeId6}>${ssrInterpolate(
                                        $data.form.description
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
                                            "onUpdate:modelValue": ($event) => $data.form.description = $event,
                                            readonly: !$options.canUpdateStudy,
                                            name: "description",
                                            placeholder: "Describe this study",
                                            rows: "3",
                                            class: "shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                          }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                            [
                                              vModelText,
                                              $data.form.description
                                            ]
                                          ])
                                        ])
                                      ];
                                    }
                                  }),
                                  _: 1
                                }, _parent6, _scopeId5));
                                _push6(ssrRenderComponent(_component_TabPanel, { class: "p-0.5 -m-0.5 rounded-lg" }, {
                                  default: withCtx((_6, _push7, _parent7, _scopeId6) => {
                                    if (_push7) {
                                      _push7(`<div class="border-b"${_scopeId6}><div class="mx-px mt-px px-3 pt-2 pb-12"${_scopeId6}>`);
                                      if ($data.form.description == "") {
                                        _push7(`<span class="text-gray-400 text-sm font-medium"${_scopeId6}> Nothing to preview </span>`);
                                      } else {
                                        _push7(`<span${_scopeId6}><div class="prose"${_scopeId6}>${_ctx.md(
                                          $data.form.description
                                        )}</div></span>`);
                                      }
                                      _push7(`</div></div>`);
                                    } else {
                                      return [
                                        createVNode("div", { class: "border-b" }, [
                                          createVNode("div", { class: "mx-px mt-px px-3 pt-2 pb-12" }, [
                                            $data.form.description == "" ? (openBlock(), createBlock("span", {
                                              key: 0,
                                              class: "text-gray-400 text-sm font-medium"
                                            }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                              createVNode("div", {
                                                class: "prose",
                                                innerHTML: _ctx.md(
                                                  $data.form.description
                                                )
                                              }, null, 8, ["innerHTML"])
                                            ]))
                                          ])
                                        ])
                                      ];
                                    }
                                  }),
                                  _: 1
                                }, _parent6, _scopeId5));
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
                                          "onUpdate:modelValue": ($event) => $data.form.description = $event,
                                          readonly: !$options.canUpdateStudy,
                                          name: "description",
                                          placeholder: "Describe this study",
                                          rows: "3",
                                          class: "shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                          [
                                            vModelText,
                                            $data.form.description
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
                                          $data.form.description == "" ? (openBlock(), createBlock("span", {
                                            key: 0,
                                            class: "text-gray-400 text-sm font-medium"
                                          }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                            createVNode("div", {
                                              class: "prose",
                                              innerHTML: _ctx.md(
                                                $data.form.description
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
                            _: 1
                          }, _parent5, _scopeId4));
                        } else {
                          return [
                            createVNode(_component_TabList, { class: "flex items-center" }, {
                              default: withCtx(() => [
                                createVNode(_component_Tab, { as: "template" }, {
                                  default: withCtx(({
                                    selected
                                  }) => [
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
                                  default: withCtx(({
                                    selected
                                  }) => [
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
                                        "onUpdate:modelValue": ($event) => $data.form.description = $event,
                                        readonly: !$options.canUpdateStudy,
                                        name: "description",
                                        placeholder: "Describe this study",
                                        rows: "3",
                                        class: "shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                        [
                                          vModelText,
                                          $data.form.description
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
                                        $data.form.description == "" ? (openBlock(), createBlock("span", {
                                          key: 0,
                                          class: "text-gray-400 text-sm font-medium"
                                        }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                          createVNode("div", {
                                            class: "prose",
                                            innerHTML: _ctx.md(
                                              $data.form.description
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
                    }, _parent4, _scopeId3));
                    _push4(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.form.errors.description,
                      class: "mt-2"
                    }, null, _parent4, _scopeId3));
                    _push4(`<label class="block text-sm font-medium text-gray-700"${_scopeId3}><small${_scopeId3}><svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-markdown v-align-bottom inline"${_scopeId3}><path fill-rule="evenodd" d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.69C0 12.48.52 13 1.15 13h13.69c.64 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"${_scopeId3}></path></svg> Styling with Markdown is supported</small></label></div></div><div class="mb-3"${_scopeId3}><label for="description" class="block text-sm font-medium text-gray-700"${_scopeId3}> Keywords </label><div${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_vue_tags_input, {
                      modelValue: $data.form.tag,
                      "onUpdate:modelValue": ($event) => $data.form.tag = $event,
                      placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                      separators: [
                        ";",
                        ","
                      ],
                      disabled: !$options.canUpdateStudy,
                      "max-width": "100%",
                      tags: $data.form.tags,
                      onTagsChanged: (newTags) => $data.form.tags = newTags
                    }, null, _parent4, _scopeId3));
                    _push4(`</div></div>`);
                    if ($options.canUpdateStudy) {
                      _push4(`<div${_scopeId3}><label for="study-name" class="block text-sm font-medium text-gray-900"${_scopeId3}> Color </label>`);
                      _push4(ssrRenderComponent(_component_color_picker, {
                        pureColor: $data.form.color,
                        "onUpdate:pureColor": ($event) => $data.form.color = $event
                      }, null, _parent4, _scopeId3));
                      _push4(`</div>`);
                    } else {
                      _push4(`<!---->`);
                    }
                    _push4(`<fieldset${_scopeId3}><legend class="text-sm font-medium text-gray-900"${_scopeId3}> Privacy </legend><div class="mt-2 space-y-5"${_scopeId3}><div${_scopeId3}><div class="relative flex items-start"${_scopeId3}><div class="absolute flex items-center h-5"${_scopeId3}><input id="privacy-private-to-study"${ssrIncludeBooleanAttr(ssrLooseEqual(
                      $data.form.is_public,
                      "false"
                    )) ? " checked" : ""}${ssrIncludeBooleanAttr(
                      $data.form.is_public === false
                    ) ? " checked" : ""}${ssrIncludeBooleanAttr(
                      !$options.canUpdateStudy
                    ) ? " disabled" : ""} name="privacy" value="false" aria-describedby="privacy-private-to-study-description" type="radio" class="focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300"${_scopeId3}></div><div class="pl-7 text-sm"${_scopeId3}><label for="privacy-private-to-study" class="font-medium text-gray-900"${_scopeId3}> Private to study members </label><p id="privacy-private-to-study-description" class="text-gray-500"${_scopeId3}> Only members of this study would be able to access. </p></div></div></div></div></fieldset></div><div class="pb-6"${_scopeId3}><div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-1"${_scopeId3}><div${_scopeId3}><span class="float-right text-xs cursor-pointer hover:text-blue-700 mt-2"${_scopeId3}><a target="_blank" href="https://docs.nmrxiv.org/submission-guides/licenses.html"${_scopeId3}>How to choose the right license?</a></span>`);
                    _push4(ssrRenderComponent(_component_select_rich, {
                      selected: $data.form.license,
                      "onUpdate:selected": ($event) => $data.form.license = $event,
                      label: "License",
                      disabled: !$options.canUpdateStudy,
                      items: $data.licenses
                    }, null, _parent4, _scopeId3));
                    _push4(`</div></div>`);
                    _push4(ssrRenderComponent(_component_jet_input_error, {
                      message: $data.form.errors.license,
                      class: "mt-2"
                    }, null, _parent4, _scopeId3));
                    _push4(`</div><div class="pt-4 pb-6"${_scopeId3}>`);
                    if ($data.form.is_public == true || $data.form.is_public == "true") {
                      _push4(`<div${_scopeId3}><label for="public_url" class="block text-sm font-medium text-gray-700"${_scopeId3}>Public URL</label><div class="mt-1 flex rounded-md shadow-sm"${_scopeId3}><div class="relative flex items-stretch flex-grow focus-within:z-10"${_scopeId3}><input id="studyPublicURLCopy"${ssrRenderAttr(
                        "value",
                        $props.study.public_url
                      )} type="text" class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"${_scopeId3}></div><button type="button" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"${_scopeId3}><span${_scopeId3}>`);
                      _push4(ssrRenderComponent(_component_ClipboardDocumentIcon, {
                        class: "h-5 w-5",
                        "aria-hidden": "true"
                      }, null, _parent4, _scopeId3));
                      _push4(`</span></button></div>`);
                      if ($data.form.doi) {
                        _push4(`<div class="mt-2"${_scopeId3}><label for="doi" class="block text-sm font-medium text-gray-700"${_scopeId3}>DOI</label><div class="mt-1 flex rounded-md shadow-sm"${_scopeId3}><div class="relative flex items-stretch flex-grow focus-within:z-10"${_scopeId3}><input id="studyPublicDOICopy"${ssrRenderAttr(
                          "value",
                          $props.study.doi
                        )} type="text" class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"${_scopeId3}></div><button type="button" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"${_scopeId3}><span${_scopeId3}>`);
                        _push4(ssrRenderComponent(_component_ClipboardDocumentIcon, {
                          class: "h-5 w-5",
                          "aria-hidden": "true"
                        }, null, _parent4, _scopeId3));
                        _push4(`</span></button></div></div>`);
                      } else {
                        _push4(`<!---->`);
                      }
                      _push4(`</div>`);
                    } else {
                      _push4(`<!---->`);
                    }
                    _push4(`<div class="mt-6 flex text-sm"${_scopeId3}><a target="_blank" href="https://docs.nmrxiv.org/submission-guides/sharing.html" class="group inline-flex items-center text-gray-500 hover:text-gray-900"${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_QuestionMarkCircleIcon, {
                      class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                      "aria-hidden": "true"
                    }, null, _parent4, _scopeId3));
                    _push4(`<span class="ml-2"${_scopeId3}> Learn more about sharing </span></a></div><div class="mt-4 flex text-sm mb-6"${_scopeId3}><a class="cursor-pointer group inline-flex items-center text-gray-500 hover:text-gray-900"${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_ExclamationCircleIcon, {
                      class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                      "aria-hidden": "true"
                    }, null, _parent4, _scopeId3));
                    _push4(`<span class="ml-2"${_scopeId3}> Activity </span></a></div>`);
                    _push4(ssrRenderComponent(_component_study_activity, {
                      ref: "activityDetailsElement",
                      study: $props.study
                    }, null, _parent4, _scopeId3));
                    _push4(`</div></div></div></div><div class="flex-shrink-0 px-4 py-4 flex justify-end"${_scopeId3}>`);
                    _push4(ssrRenderComponent(_component_jet_action_message, {
                      on: $data.form.recentlySuccessful,
                      class: "mr-3 py-2 text-green-200"
                    }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(` Saved. `);
                        } else {
                          return [
                            createTextVNode(" Saved. ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                    _push4(ssrRenderComponent(_component_jet_secondary_button, {
                      type: "button",
                      class: "bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500",
                      onClick: ($event) => $data.open = false
                    }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(` Cancel `);
                        } else {
                          return [
                            createTextVNode(" Cancel ")
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                    if ($options.canUpdateStudy) {
                      _push4(ssrRenderComponent(_component_jet_button, {
                        type: "submit",
                        class: "ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500",
                        onClick: $options.updateStudy
                      }, {
                        default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                          if (_push5) {
                            _push5(` Save `);
                          } else {
                            return [
                              createTextVNode(" Save ")
                            ];
                          }
                        }),
                        _: 1
                      }, _parent4, _scopeId3));
                    } else {
                      _push4(`<!---->`);
                    }
                    _push4(`</div></div></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "w-screen max-w-2xl" }, [
                        createVNode("div", { class: "h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" }, [
                          createVNode("div", { class: "flex-1 h-0 overflow-y-auto" }, [
                            createVNode("div", {
                              class: "py-6 px-4 sm:px-6",
                              style: [
                                $props.study.color && $props.study.color != "" ? "background-color:" + $props.study.color : "background-color:rgb(75,75,75)"
                              ]
                            }, [
                              createVNode("div", { class: "flex items-center justify-between" }, [
                                createVNode(_component_DialogTitle, { class: "text-lg font-medium text-white" }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString($props.study.name), 1)
                                  ]),
                                  _: 1
                                }),
                                createVNode("div", { class: "ml-3 h-7 flex items-center" }, [
                                  createVNode("button", {
                                    type: "button",
                                    class: "rounded-md hover:text-white",
                                    onClick: ($event) => $data.open = false
                                  }, [
                                    createVNode("span", { class: "sr-only" }, "Close panel"),
                                    createVNode(_component_XMarkIcon, {
                                      class: "h-6 w-6",
                                      "aria-hidden": "true"
                                    })
                                  ], 8, ["onClick"])
                                ])
                              ])
                            ], 4),
                            createVNode("div", { class: "flex-1 flex flex-col justify-between" }, [
                              createVNode("div", { class: "px-4 divide-y divide-gray-200 sm:px-6" }, [
                                createVNode("div", { class: "space-y-6 pt-6 pb-5" }, [
                                  createVNode("div", null, [
                                    createVNode("label", {
                                      for: "study-name",
                                      class: "block text-sm font-medium text-gray-900 after:content-['*'] after:ml-0.5 after:text-red-500"
                                    }, " Study name "),
                                    createVNode("div", { class: "mt-1" }, [
                                      withDirectives(createVNode("input", {
                                        id: "study-name",
                                        "onUpdate:modelValue": ($event) => $data.form.name = $event,
                                        type: "text",
                                        readonly: !$options.canUpdateStudy,
                                        name: "study-name",
                                        class: "block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"
                                      }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                        [vModelText, $data.form.name]
                                      ])
                                    ]),
                                    createVNode(_component_jet_input_error, {
                                      message: $data.form.errors.name,
                                      class: "mt-2"
                                    }, null, 8, ["message"])
                                  ]),
                                  createVNode("div", null, [
                                    createVNode("label", {
                                      for: "description",
                                      class: "block text-sm font-medium text-gray-900 after:content-['*'] after:ml-0.5 after:text-red-500"
                                    }, " Description "),
                                    createVNode("div", { class: "mt-1" }, [
                                      createVNode(_component_TabGroup, null, {
                                        default: withCtx(() => [
                                          createVNode(_component_TabList, { class: "flex items-center" }, {
                                            default: withCtx(() => [
                                              createVNode(_component_Tab, { as: "template" }, {
                                                default: withCtx(({
                                                  selected
                                                }) => [
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
                                                default: withCtx(({
                                                  selected
                                                }) => [
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
                                                      "onUpdate:modelValue": ($event) => $data.form.description = $event,
                                                      readonly: !$options.canUpdateStudy,
                                                      name: "description",
                                                      placeholder: "Describe this study",
                                                      rows: "3",
                                                      class: "shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                    }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                                      [
                                                        vModelText,
                                                        $data.form.description
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
                                                      $data.form.description == "" ? (openBlock(), createBlock("span", {
                                                        key: 0,
                                                        class: "text-gray-400 text-sm font-medium"
                                                      }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                                        createVNode("div", {
                                                          class: "prose",
                                                          innerHTML: _ctx.md(
                                                            $data.form.description
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
                                        message: $data.form.errors.description,
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
                                          createTextVNode(" Styling with Markdown is supported")
                                        ])
                                      ])
                                    ])
                                  ]),
                                  createVNode("div", { class: "mb-3" }, [
                                    createVNode("label", {
                                      for: "description",
                                      class: "block text-sm font-medium text-gray-700"
                                    }, " Keywords "),
                                    createVNode("div", null, [
                                      createVNode(_component_vue_tags_input, {
                                        modelValue: $data.form.tag,
                                        "onUpdate:modelValue": ($event) => $data.form.tag = $event,
                                        placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                                        separators: [
                                          ";",
                                          ","
                                        ],
                                        disabled: !$options.canUpdateStudy,
                                        "max-width": "100%",
                                        tags: $data.form.tags,
                                        onTagsChanged: (newTags) => $data.form.tags = newTags
                                      }, null, 8, ["modelValue", "onUpdate:modelValue", "disabled", "tags", "onTagsChanged"])
                                    ])
                                  ]),
                                  $options.canUpdateStudy ? (openBlock(), createBlock("div", { key: 0 }, [
                                    createVNode("label", {
                                      for: "study-name",
                                      class: "block text-sm font-medium text-gray-900"
                                    }, " Color "),
                                    createVNode(_component_color_picker, {
                                      pureColor: $data.form.color,
                                      "onUpdate:pureColor": ($event) => $data.form.color = $event
                                    }, null, 8, ["pureColor", "onUpdate:pureColor"])
                                  ])) : createCommentVNode("", true),
                                  createVNode("fieldset", null, [
                                    createVNode("legend", { class: "text-sm font-medium text-gray-900" }, " Privacy "),
                                    createVNode("div", { class: "mt-2 space-y-5" }, [
                                      createVNode("div", null, [
                                        createVNode("div", { class: "relative flex items-start" }, [
                                          createVNode("div", { class: "absolute flex items-center h-5" }, [
                                            withDirectives(createVNode("input", {
                                              id: "privacy-private-to-study",
                                              "onUpdate:modelValue": ($event) => $data.form.is_public = $event,
                                              checked: $data.form.is_public === false,
                                              disabled: !$options.canUpdateStudy,
                                              name: "privacy",
                                              value: "false",
                                              "aria-describedby": "privacy-private-to-study-description",
                                              type: "radio",
                                              class: "focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300"
                                            }, null, 8, ["onUpdate:modelValue", "checked", "disabled"]), [
                                              [
                                                vModelRadio,
                                                $data.form.is_public
                                              ]
                                            ])
                                          ]),
                                          createVNode("div", { class: "pl-7 text-sm" }, [
                                            createVNode("label", {
                                              for: "privacy-private-to-study",
                                              class: "font-medium text-gray-900"
                                            }, " Private to study members "),
                                            createVNode("p", {
                                              id: "privacy-private-to-study-description",
                                              class: "text-gray-500"
                                            }, " Only members of this study would be able to access. ")
                                          ])
                                        ])
                                      ])
                                    ])
                                  ])
                                ]),
                                createVNode("div", { class: "pb-6" }, [
                                  createVNode("div", { class: "mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-1" }, [
                                    createVNode("div", null, [
                                      createVNode("span", { class: "float-right text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                                        createVNode("a", {
                                          target: "_blank",
                                          href: "https://docs.nmrxiv.org/submission-guides/licenses.html"
                                        }, "How to choose the right license?")
                                      ]),
                                      createVNode(_component_select_rich, {
                                        selected: $data.form.license,
                                        "onUpdate:selected": ($event) => $data.form.license = $event,
                                        label: "License",
                                        disabled: !$options.canUpdateStudy,
                                        items: $data.licenses
                                      }, null, 8, ["selected", "onUpdate:selected", "disabled", "items"])
                                    ])
                                  ]),
                                  createVNode(_component_jet_input_error, {
                                    message: $data.form.errors.license,
                                    class: "mt-2"
                                  }, null, 8, ["message"])
                                ]),
                                createVNode("div", { class: "pt-4 pb-6" }, [
                                  $data.form.is_public == true || $data.form.is_public == "true" ? (openBlock(), createBlock("div", { key: 0 }, [
                                    createVNode("label", {
                                      for: "public_url",
                                      class: "block text-sm font-medium text-gray-700"
                                    }, "Public URL"),
                                    createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                                      createVNode("div", { class: "relative flex items-stretch flex-grow focus-within:z-10" }, [
                                        withDirectives(createVNode("input", {
                                          id: "studyPublicURLCopy",
                                          "onUpdate:modelValue": ($event) => $props.study.public_url = $event,
                                          type: "text",
                                          class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                          onFocus: ($event) => $event.target.select()
                                        }, null, 40, ["onUpdate:modelValue", "onFocus"]), [
                                          [
                                            vModelText,
                                            $props.study.public_url
                                          ]
                                        ])
                                      ]),
                                      createVNode("button", {
                                        type: "button",
                                        class: "-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                        onClick: ($event) => _ctx.copyToClipboard(
                                          $props.study.public_url,
                                          "studyPublicURLCopy"
                                        )
                                      }, [
                                        createVNode("span", null, [
                                          createVNode(_component_ClipboardDocumentIcon, {
                                            class: "h-5 w-5",
                                            "aria-hidden": "true"
                                          })
                                        ])
                                      ], 8, ["onClick"])
                                    ]),
                                    $data.form.doi ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "mt-2"
                                    }, [
                                      createVNode("label", {
                                        for: "doi",
                                        class: "block text-sm font-medium text-gray-700"
                                      }, "DOI"),
                                      createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                                        createVNode("div", { class: "relative flex items-stretch flex-grow focus-within:z-10" }, [
                                          withDirectives(createVNode("input", {
                                            id: "studyPublicDOICopy",
                                            "onUpdate:modelValue": ($event) => $props.study.doi = $event,
                                            type: "text",
                                            class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                            onFocus: ($event) => $event.target.select()
                                          }, null, 40, ["onUpdate:modelValue", "onFocus"]), [
                                            [
                                              vModelText,
                                              $props.study.doi
                                            ]
                                          ])
                                        ]),
                                        createVNode("button", {
                                          type: "button",
                                          class: "-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                          onClick: ($event) => _ctx.copyToClipboard(
                                            $props.study.doi,
                                            "studyPublicDOICopy"
                                          )
                                        }, [
                                          createVNode("span", null, [
                                            createVNode(_component_ClipboardDocumentIcon, {
                                              class: "h-5 w-5",
                                              "aria-hidden": "true"
                                            })
                                          ])
                                        ], 8, ["onClick"])
                                      ])
                                    ])) : createCommentVNode("", true)
                                  ])) : createCommentVNode("", true),
                                  createVNode("div", { class: "mt-6 flex text-sm" }, [
                                    createVNode("a", {
                                      target: "_blank",
                                      href: "https://docs.nmrxiv.org/submission-guides/sharing.html",
                                      class: "group inline-flex items-center text-gray-500 hover:text-gray-900"
                                    }, [
                                      createVNode(_component_QuestionMarkCircleIcon, {
                                        class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                                        "aria-hidden": "true"
                                      }),
                                      createVNode("span", { class: "ml-2" }, " Learn more about sharing ")
                                    ])
                                  ]),
                                  createVNode("div", { class: "mt-4 flex text-sm mb-6" }, [
                                    createVNode("a", {
                                      class: "cursor-pointer group inline-flex items-center text-gray-500 hover:text-gray-900",
                                      onClick: $options.toggleActivityDetails
                                    }, [
                                      createVNode(_component_ExclamationCircleIcon, {
                                        class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                                        "aria-hidden": "true"
                                      }),
                                      createVNode("span", { class: "ml-2" }, " Activity ")
                                    ], 8, ["onClick"])
                                  ]),
                                  createVNode(_component_study_activity, {
                                    ref: "activityDetailsElement",
                                    study: $props.study
                                  }, null, 8, ["study"])
                                ])
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "flex-shrink-0 px-4 py-4 flex justify-end" }, [
                            createVNode(_component_jet_action_message, {
                              on: $data.form.recentlySuccessful,
                              class: "mr-3 py-2 text-green-200"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Saved. ")
                              ]),
                              _: 1
                            }, 8, ["on"]),
                            createVNode(_component_jet_secondary_button, {
                              type: "button",
                              class: "bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500",
                              onClick: ($event) => $data.open = false
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Cancel ")
                              ]),
                              _: 1
                            }, 8, ["onClick"]),
                            $options.canUpdateStudy ? (openBlock(), createBlock(_component_jet_button, {
                              key: 0,
                              type: "submit",
                              class: "ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500",
                              onClick: $options.updateStudy
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Save ")
                              ]),
                              _: 1
                            }, 8, ["onClick"])) : createCommentVNode("", true)
                          ])
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
                createVNode("div", { class: "absolute inset-0" }, [
                  createVNode(_component_DialogOverlay, { class: "absolute inset-0" }),
                  createVNode("div", { class: "fixed inset-y-0 pl-16 max-w-full right-0 flex" }, [
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
                        createVNode("div", { class: "w-screen max-w-2xl" }, [
                          createVNode("div", { class: "h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" }, [
                            createVNode("div", { class: "flex-1 h-0 overflow-y-auto" }, [
                              createVNode("div", {
                                class: "py-6 px-4 sm:px-6",
                                style: [
                                  $props.study.color && $props.study.color != "" ? "background-color:" + $props.study.color : "background-color:rgb(75,75,75)"
                                ]
                              }, [
                                createVNode("div", { class: "flex items-center justify-between" }, [
                                  createVNode(_component_DialogTitle, { class: "text-lg font-medium text-white" }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString($props.study.name), 1)
                                    ]),
                                    _: 1
                                  }),
                                  createVNode("div", { class: "ml-3 h-7 flex items-center" }, [
                                    createVNode("button", {
                                      type: "button",
                                      class: "rounded-md hover:text-white",
                                      onClick: ($event) => $data.open = false
                                    }, [
                                      createVNode("span", { class: "sr-only" }, "Close panel"),
                                      createVNode(_component_XMarkIcon, {
                                        class: "h-6 w-6",
                                        "aria-hidden": "true"
                                      })
                                    ], 8, ["onClick"])
                                  ])
                                ])
                              ], 4),
                              createVNode("div", { class: "flex-1 flex flex-col justify-between" }, [
                                createVNode("div", { class: "px-4 divide-y divide-gray-200 sm:px-6" }, [
                                  createVNode("div", { class: "space-y-6 pt-6 pb-5" }, [
                                    createVNode("div", null, [
                                      createVNode("label", {
                                        for: "study-name",
                                        class: "block text-sm font-medium text-gray-900 after:content-['*'] after:ml-0.5 after:text-red-500"
                                      }, " Study name "),
                                      createVNode("div", { class: "mt-1" }, [
                                        withDirectives(createVNode("input", {
                                          id: "study-name",
                                          "onUpdate:modelValue": ($event) => $data.form.name = $event,
                                          type: "text",
                                          readonly: !$options.canUpdateStudy,
                                          name: "study-name",
                                          class: "block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"
                                        }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                          [vModelText, $data.form.name]
                                        ])
                                      ]),
                                      createVNode(_component_jet_input_error, {
                                        message: $data.form.errors.name,
                                        class: "mt-2"
                                      }, null, 8, ["message"])
                                    ]),
                                    createVNode("div", null, [
                                      createVNode("label", {
                                        for: "description",
                                        class: "block text-sm font-medium text-gray-900 after:content-['*'] after:ml-0.5 after:text-red-500"
                                      }, " Description "),
                                      createVNode("div", { class: "mt-1" }, [
                                        createVNode(_component_TabGroup, null, {
                                          default: withCtx(() => [
                                            createVNode(_component_TabList, { class: "flex items-center" }, {
                                              default: withCtx(() => [
                                                createVNode(_component_Tab, { as: "template" }, {
                                                  default: withCtx(({
                                                    selected
                                                  }) => [
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
                                                  default: withCtx(({
                                                    selected
                                                  }) => [
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
                                                        "onUpdate:modelValue": ($event) => $data.form.description = $event,
                                                        readonly: !$options.canUpdateStudy,
                                                        name: "description",
                                                        placeholder: "Describe this study",
                                                        rows: "3",
                                                        class: "shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                      }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                                        [
                                                          vModelText,
                                                          $data.form.description
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
                                                        $data.form.description == "" ? (openBlock(), createBlock("span", {
                                                          key: 0,
                                                          class: "text-gray-400 text-sm font-medium"
                                                        }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                                          createVNode("div", {
                                                            class: "prose",
                                                            innerHTML: _ctx.md(
                                                              $data.form.description
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
                                          message: $data.form.errors.description,
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
                                            createTextVNode(" Styling with Markdown is supported")
                                          ])
                                        ])
                                      ])
                                    ]),
                                    createVNode("div", { class: "mb-3" }, [
                                      createVNode("label", {
                                        for: "description",
                                        class: "block text-sm font-medium text-gray-700"
                                      }, " Keywords "),
                                      createVNode("div", null, [
                                        createVNode(_component_vue_tags_input, {
                                          modelValue: $data.form.tag,
                                          "onUpdate:modelValue": ($event) => $data.form.tag = $event,
                                          placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                                          separators: [
                                            ";",
                                            ","
                                          ],
                                          disabled: !$options.canUpdateStudy,
                                          "max-width": "100%",
                                          tags: $data.form.tags,
                                          onTagsChanged: (newTags) => $data.form.tags = newTags
                                        }, null, 8, ["modelValue", "onUpdate:modelValue", "disabled", "tags", "onTagsChanged"])
                                      ])
                                    ]),
                                    $options.canUpdateStudy ? (openBlock(), createBlock("div", { key: 0 }, [
                                      createVNode("label", {
                                        for: "study-name",
                                        class: "block text-sm font-medium text-gray-900"
                                      }, " Color "),
                                      createVNode(_component_color_picker, {
                                        pureColor: $data.form.color,
                                        "onUpdate:pureColor": ($event) => $data.form.color = $event
                                      }, null, 8, ["pureColor", "onUpdate:pureColor"])
                                    ])) : createCommentVNode("", true),
                                    createVNode("fieldset", null, [
                                      createVNode("legend", { class: "text-sm font-medium text-gray-900" }, " Privacy "),
                                      createVNode("div", { class: "mt-2 space-y-5" }, [
                                        createVNode("div", null, [
                                          createVNode("div", { class: "relative flex items-start" }, [
                                            createVNode("div", { class: "absolute flex items-center h-5" }, [
                                              withDirectives(createVNode("input", {
                                                id: "privacy-private-to-study",
                                                "onUpdate:modelValue": ($event) => $data.form.is_public = $event,
                                                checked: $data.form.is_public === false,
                                                disabled: !$options.canUpdateStudy,
                                                name: "privacy",
                                                value: "false",
                                                "aria-describedby": "privacy-private-to-study-description",
                                                type: "radio",
                                                class: "focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300"
                                              }, null, 8, ["onUpdate:modelValue", "checked", "disabled"]), [
                                                [
                                                  vModelRadio,
                                                  $data.form.is_public
                                                ]
                                              ])
                                            ]),
                                            createVNode("div", { class: "pl-7 text-sm" }, [
                                              createVNode("label", {
                                                for: "privacy-private-to-study",
                                                class: "font-medium text-gray-900"
                                              }, " Private to study members "),
                                              createVNode("p", {
                                                id: "privacy-private-to-study-description",
                                                class: "text-gray-500"
                                              }, " Only members of this study would be able to access. ")
                                            ])
                                          ])
                                        ])
                                      ])
                                    ])
                                  ]),
                                  createVNode("div", { class: "pb-6" }, [
                                    createVNode("div", { class: "mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-1" }, [
                                      createVNode("div", null, [
                                        createVNode("span", { class: "float-right text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                                          createVNode("a", {
                                            target: "_blank",
                                            href: "https://docs.nmrxiv.org/submission-guides/licenses.html"
                                          }, "How to choose the right license?")
                                        ]),
                                        createVNode(_component_select_rich, {
                                          selected: $data.form.license,
                                          "onUpdate:selected": ($event) => $data.form.license = $event,
                                          label: "License",
                                          disabled: !$options.canUpdateStudy,
                                          items: $data.licenses
                                        }, null, 8, ["selected", "onUpdate:selected", "disabled", "items"])
                                      ])
                                    ]),
                                    createVNode(_component_jet_input_error, {
                                      message: $data.form.errors.license,
                                      class: "mt-2"
                                    }, null, 8, ["message"])
                                  ]),
                                  createVNode("div", { class: "pt-4 pb-6" }, [
                                    $data.form.is_public == true || $data.form.is_public == "true" ? (openBlock(), createBlock("div", { key: 0 }, [
                                      createVNode("label", {
                                        for: "public_url",
                                        class: "block text-sm font-medium text-gray-700"
                                      }, "Public URL"),
                                      createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                                        createVNode("div", { class: "relative flex items-stretch flex-grow focus-within:z-10" }, [
                                          withDirectives(createVNode("input", {
                                            id: "studyPublicURLCopy",
                                            "onUpdate:modelValue": ($event) => $props.study.public_url = $event,
                                            type: "text",
                                            class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                            onFocus: ($event) => $event.target.select()
                                          }, null, 40, ["onUpdate:modelValue", "onFocus"]), [
                                            [
                                              vModelText,
                                              $props.study.public_url
                                            ]
                                          ])
                                        ]),
                                        createVNode("button", {
                                          type: "button",
                                          class: "-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                          onClick: ($event) => _ctx.copyToClipboard(
                                            $props.study.public_url,
                                            "studyPublicURLCopy"
                                          )
                                        }, [
                                          createVNode("span", null, [
                                            createVNode(_component_ClipboardDocumentIcon, {
                                              class: "h-5 w-5",
                                              "aria-hidden": "true"
                                            })
                                          ])
                                        ], 8, ["onClick"])
                                      ]),
                                      $data.form.doi ? (openBlock(), createBlock("div", {
                                        key: 0,
                                        class: "mt-2"
                                      }, [
                                        createVNode("label", {
                                          for: "doi",
                                          class: "block text-sm font-medium text-gray-700"
                                        }, "DOI"),
                                        createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                                          createVNode("div", { class: "relative flex items-stretch flex-grow focus-within:z-10" }, [
                                            withDirectives(createVNode("input", {
                                              id: "studyPublicDOICopy",
                                              "onUpdate:modelValue": ($event) => $props.study.doi = $event,
                                              type: "text",
                                              class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                              onFocus: ($event) => $event.target.select()
                                            }, null, 40, ["onUpdate:modelValue", "onFocus"]), [
                                              [
                                                vModelText,
                                                $props.study.doi
                                              ]
                                            ])
                                          ]),
                                          createVNode("button", {
                                            type: "button",
                                            class: "-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                            onClick: ($event) => _ctx.copyToClipboard(
                                              $props.study.doi,
                                              "studyPublicDOICopy"
                                            )
                                          }, [
                                            createVNode("span", null, [
                                              createVNode(_component_ClipboardDocumentIcon, {
                                                class: "h-5 w-5",
                                                "aria-hidden": "true"
                                              })
                                            ])
                                          ], 8, ["onClick"])
                                        ])
                                      ])) : createCommentVNode("", true)
                                    ])) : createCommentVNode("", true),
                                    createVNode("div", { class: "mt-6 flex text-sm" }, [
                                      createVNode("a", {
                                        target: "_blank",
                                        href: "https://docs.nmrxiv.org/submission-guides/sharing.html",
                                        class: "group inline-flex items-center text-gray-500 hover:text-gray-900"
                                      }, [
                                        createVNode(_component_QuestionMarkCircleIcon, {
                                          class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                                          "aria-hidden": "true"
                                        }),
                                        createVNode("span", { class: "ml-2" }, " Learn more about sharing ")
                                      ])
                                    ]),
                                    createVNode("div", { class: "mt-4 flex text-sm mb-6" }, [
                                      createVNode("a", {
                                        class: "cursor-pointer group inline-flex items-center text-gray-500 hover:text-gray-900",
                                        onClick: $options.toggleActivityDetails
                                      }, [
                                        createVNode(_component_ExclamationCircleIcon, {
                                          class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                                          "aria-hidden": "true"
                                        }),
                                        createVNode("span", { class: "ml-2" }, " Activity ")
                                      ], 8, ["onClick"])
                                    ]),
                                    createVNode(_component_study_activity, {
                                      ref: "activityDetailsElement",
                                      study: $props.study
                                    }, null, 8, ["study"])
                                  ])
                                ])
                              ])
                            ]),
                            createVNode("div", { class: "flex-shrink-0 px-4 py-4 flex justify-end" }, [
                              createVNode(_component_jet_action_message, {
                                on: $data.form.recentlySuccessful,
                                class: "mr-3 py-2 text-green-200"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(" Saved. ")
                                ]),
                                _: 1
                              }, 8, ["on"]),
                              createVNode(_component_jet_secondary_button, {
                                type: "button",
                                class: "bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500",
                                onClick: ($event) => $data.open = false
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(" Cancel ")
                                ]),
                                _: 1
                              }, 8, ["onClick"]),
                              $options.canUpdateStudy ? (openBlock(), createBlock(_component_jet_button, {
                                key: 0,
                                type: "submit",
                                class: "ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500",
                                onClick: $options.updateStudy
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(" Save ")
                                ]),
                                _: 1
                              }, 8, ["onClick"])) : createCommentVNode("", true)
                            ])
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
            class: "fixed inset-0 z-50"
          }, {
            default: withCtx(() => [
              createVNode("div", { class: "absolute inset-0" }, [
                createVNode(_component_DialogOverlay, { class: "absolute inset-0" }),
                createVNode("div", { class: "fixed inset-y-0 pl-16 max-w-full right-0 flex" }, [
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
                      createVNode("div", { class: "w-screen max-w-2xl" }, [
                        createVNode("div", { class: "h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl" }, [
                          createVNode("div", { class: "flex-1 h-0 overflow-y-auto" }, [
                            createVNode("div", {
                              class: "py-6 px-4 sm:px-6",
                              style: [
                                $props.study.color && $props.study.color != "" ? "background-color:" + $props.study.color : "background-color:rgb(75,75,75)"
                              ]
                            }, [
                              createVNode("div", { class: "flex items-center justify-between" }, [
                                createVNode(_component_DialogTitle, { class: "text-lg font-medium text-white" }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString($props.study.name), 1)
                                  ]),
                                  _: 1
                                }),
                                createVNode("div", { class: "ml-3 h-7 flex items-center" }, [
                                  createVNode("button", {
                                    type: "button",
                                    class: "rounded-md hover:text-white",
                                    onClick: ($event) => $data.open = false
                                  }, [
                                    createVNode("span", { class: "sr-only" }, "Close panel"),
                                    createVNode(_component_XMarkIcon, {
                                      class: "h-6 w-6",
                                      "aria-hidden": "true"
                                    })
                                  ], 8, ["onClick"])
                                ])
                              ])
                            ], 4),
                            createVNode("div", { class: "flex-1 flex flex-col justify-between" }, [
                              createVNode("div", { class: "px-4 divide-y divide-gray-200 sm:px-6" }, [
                                createVNode("div", { class: "space-y-6 pt-6 pb-5" }, [
                                  createVNode("div", null, [
                                    createVNode("label", {
                                      for: "study-name",
                                      class: "block text-sm font-medium text-gray-900 after:content-['*'] after:ml-0.5 after:text-red-500"
                                    }, " Study name "),
                                    createVNode("div", { class: "mt-1" }, [
                                      withDirectives(createVNode("input", {
                                        id: "study-name",
                                        "onUpdate:modelValue": ($event) => $data.form.name = $event,
                                        type: "text",
                                        readonly: !$options.canUpdateStudy,
                                        name: "study-name",
                                        class: "block w-full shadow-sm sm:text-sm focus:ring-gray-500 focus:border-gray-500 border-gray-300 rounded-md"
                                      }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                        [vModelText, $data.form.name]
                                      ])
                                    ]),
                                    createVNode(_component_jet_input_error, {
                                      message: $data.form.errors.name,
                                      class: "mt-2"
                                    }, null, 8, ["message"])
                                  ]),
                                  createVNode("div", null, [
                                    createVNode("label", {
                                      for: "description",
                                      class: "block text-sm font-medium text-gray-900 after:content-['*'] after:ml-0.5 after:text-red-500"
                                    }, " Description "),
                                    createVNode("div", { class: "mt-1" }, [
                                      createVNode(_component_TabGroup, null, {
                                        default: withCtx(() => [
                                          createVNode(_component_TabList, { class: "flex items-center" }, {
                                            default: withCtx(() => [
                                              createVNode(_component_Tab, { as: "template" }, {
                                                default: withCtx(({
                                                  selected
                                                }) => [
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
                                                default: withCtx(({
                                                  selected
                                                }) => [
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
                                                      "onUpdate:modelValue": ($event) => $data.form.description = $event,
                                                      readonly: !$options.canUpdateStudy,
                                                      name: "description",
                                                      placeholder: "Describe this study",
                                                      rows: "3",
                                                      class: "shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                    }, null, 8, ["onUpdate:modelValue", "readonly"]), [
                                                      [
                                                        vModelText,
                                                        $data.form.description
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
                                                      $data.form.description == "" ? (openBlock(), createBlock("span", {
                                                        key: 0,
                                                        class: "text-gray-400 text-sm font-medium"
                                                      }, " Nothing to preview ")) : (openBlock(), createBlock("span", { key: 1 }, [
                                                        createVNode("div", {
                                                          class: "prose",
                                                          innerHTML: _ctx.md(
                                                            $data.form.description
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
                                        message: $data.form.errors.description,
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
                                          createTextVNode(" Styling with Markdown is supported")
                                        ])
                                      ])
                                    ])
                                  ]),
                                  createVNode("div", { class: "mb-3" }, [
                                    createVNode("label", {
                                      for: "description",
                                      class: "block text-sm font-medium text-gray-700"
                                    }, " Keywords "),
                                    createVNode("div", null, [
                                      createVNode(_component_vue_tags_input, {
                                        modelValue: $data.form.tag,
                                        "onUpdate:modelValue": ($event) => $data.form.tag = $event,
                                        placeholder: "Type a keyword or keywords separated by comma (,) and press enter",
                                        separators: [
                                          ";",
                                          ","
                                        ],
                                        disabled: !$options.canUpdateStudy,
                                        "max-width": "100%",
                                        tags: $data.form.tags,
                                        onTagsChanged: (newTags) => $data.form.tags = newTags
                                      }, null, 8, ["modelValue", "onUpdate:modelValue", "disabled", "tags", "onTagsChanged"])
                                    ])
                                  ]),
                                  $options.canUpdateStudy ? (openBlock(), createBlock("div", { key: 0 }, [
                                    createVNode("label", {
                                      for: "study-name",
                                      class: "block text-sm font-medium text-gray-900"
                                    }, " Color "),
                                    createVNode(_component_color_picker, {
                                      pureColor: $data.form.color,
                                      "onUpdate:pureColor": ($event) => $data.form.color = $event
                                    }, null, 8, ["pureColor", "onUpdate:pureColor"])
                                  ])) : createCommentVNode("", true),
                                  createVNode("fieldset", null, [
                                    createVNode("legend", { class: "text-sm font-medium text-gray-900" }, " Privacy "),
                                    createVNode("div", { class: "mt-2 space-y-5" }, [
                                      createVNode("div", null, [
                                        createVNode("div", { class: "relative flex items-start" }, [
                                          createVNode("div", { class: "absolute flex items-center h-5" }, [
                                            withDirectives(createVNode("input", {
                                              id: "privacy-private-to-study",
                                              "onUpdate:modelValue": ($event) => $data.form.is_public = $event,
                                              checked: $data.form.is_public === false,
                                              disabled: !$options.canUpdateStudy,
                                              name: "privacy",
                                              value: "false",
                                              "aria-describedby": "privacy-private-to-study-description",
                                              type: "radio",
                                              class: "focus:ring-gray-500 h-4 w-4 text-gray-600 border-gray-300"
                                            }, null, 8, ["onUpdate:modelValue", "checked", "disabled"]), [
                                              [
                                                vModelRadio,
                                                $data.form.is_public
                                              ]
                                            ])
                                          ]),
                                          createVNode("div", { class: "pl-7 text-sm" }, [
                                            createVNode("label", {
                                              for: "privacy-private-to-study",
                                              class: "font-medium text-gray-900"
                                            }, " Private to study members "),
                                            createVNode("p", {
                                              id: "privacy-private-to-study-description",
                                              class: "text-gray-500"
                                            }, " Only members of this study would be able to access. ")
                                          ])
                                        ])
                                      ])
                                    ])
                                  ])
                                ]),
                                createVNode("div", { class: "pb-6" }, [
                                  createVNode("div", { class: "mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-1" }, [
                                    createVNode("div", null, [
                                      createVNode("span", { class: "float-right text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                                        createVNode("a", {
                                          target: "_blank",
                                          href: "https://docs.nmrxiv.org/submission-guides/licenses.html"
                                        }, "How to choose the right license?")
                                      ]),
                                      createVNode(_component_select_rich, {
                                        selected: $data.form.license,
                                        "onUpdate:selected": ($event) => $data.form.license = $event,
                                        label: "License",
                                        disabled: !$options.canUpdateStudy,
                                        items: $data.licenses
                                      }, null, 8, ["selected", "onUpdate:selected", "disabled", "items"])
                                    ])
                                  ]),
                                  createVNode(_component_jet_input_error, {
                                    message: $data.form.errors.license,
                                    class: "mt-2"
                                  }, null, 8, ["message"])
                                ]),
                                createVNode("div", { class: "pt-4 pb-6" }, [
                                  $data.form.is_public == true || $data.form.is_public == "true" ? (openBlock(), createBlock("div", { key: 0 }, [
                                    createVNode("label", {
                                      for: "public_url",
                                      class: "block text-sm font-medium text-gray-700"
                                    }, "Public URL"),
                                    createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                                      createVNode("div", { class: "relative flex items-stretch flex-grow focus-within:z-10" }, [
                                        withDirectives(createVNode("input", {
                                          id: "studyPublicURLCopy",
                                          "onUpdate:modelValue": ($event) => $props.study.public_url = $event,
                                          type: "text",
                                          class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                          onFocus: ($event) => $event.target.select()
                                        }, null, 40, ["onUpdate:modelValue", "onFocus"]), [
                                          [
                                            vModelText,
                                            $props.study.public_url
                                          ]
                                        ])
                                      ]),
                                      createVNode("button", {
                                        type: "button",
                                        class: "-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                        onClick: ($event) => _ctx.copyToClipboard(
                                          $props.study.public_url,
                                          "studyPublicURLCopy"
                                        )
                                      }, [
                                        createVNode("span", null, [
                                          createVNode(_component_ClipboardDocumentIcon, {
                                            class: "h-5 w-5",
                                            "aria-hidden": "true"
                                          })
                                        ])
                                      ], 8, ["onClick"])
                                    ]),
                                    $data.form.doi ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "mt-2"
                                    }, [
                                      createVNode("label", {
                                        for: "doi",
                                        class: "block text-sm font-medium text-gray-700"
                                      }, "DOI"),
                                      createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                                        createVNode("div", { class: "relative flex items-stretch flex-grow focus-within:z-10" }, [
                                          withDirectives(createVNode("input", {
                                            id: "studyPublicDOICopy",
                                            "onUpdate:modelValue": ($event) => $props.study.doi = $event,
                                            type: "text",
                                            class: "rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300",
                                            onFocus: ($event) => $event.target.select()
                                          }, null, 40, ["onUpdate:modelValue", "onFocus"]), [
                                            [
                                              vModelText,
                                              $props.study.doi
                                            ]
                                          ])
                                        ]),
                                        createVNode("button", {
                                          type: "button",
                                          class: "-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500",
                                          onClick: ($event) => _ctx.copyToClipboard(
                                            $props.study.doi,
                                            "studyPublicDOICopy"
                                          )
                                        }, [
                                          createVNode("span", null, [
                                            createVNode(_component_ClipboardDocumentIcon, {
                                              class: "h-5 w-5",
                                              "aria-hidden": "true"
                                            })
                                          ])
                                        ], 8, ["onClick"])
                                      ])
                                    ])) : createCommentVNode("", true)
                                  ])) : createCommentVNode("", true),
                                  createVNode("div", { class: "mt-6 flex text-sm" }, [
                                    createVNode("a", {
                                      target: "_blank",
                                      href: "https://docs.nmrxiv.org/submission-guides/sharing.html",
                                      class: "group inline-flex items-center text-gray-500 hover:text-gray-900"
                                    }, [
                                      createVNode(_component_QuestionMarkCircleIcon, {
                                        class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                                        "aria-hidden": "true"
                                      }),
                                      createVNode("span", { class: "ml-2" }, " Learn more about sharing ")
                                    ])
                                  ]),
                                  createVNode("div", { class: "mt-4 flex text-sm mb-6" }, [
                                    createVNode("a", {
                                      class: "cursor-pointer group inline-flex items-center text-gray-500 hover:text-gray-900",
                                      onClick: $options.toggleActivityDetails
                                    }, [
                                      createVNode(_component_ExclamationCircleIcon, {
                                        class: "h-5 w-5 text-gray-400 group-hover:text-gray-500",
                                        "aria-hidden": "true"
                                      }),
                                      createVNode("span", { class: "ml-2" }, " Activity ")
                                    ], 8, ["onClick"])
                                  ]),
                                  createVNode(_component_study_activity, {
                                    ref: "activityDetailsElement",
                                    study: $props.study
                                  }, null, 8, ["study"])
                                ])
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "flex-shrink-0 px-4 py-4 flex justify-end" }, [
                            createVNode(_component_jet_action_message, {
                              on: $data.form.recentlySuccessful,
                              class: "mr-3 py-2 text-green-200"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Saved. ")
                              ]),
                              _: 1
                            }, 8, ["on"]),
                            createVNode(_component_jet_secondary_button, {
                              type: "button",
                              class: "bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500",
                              onClick: ($event) => $data.open = false
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Cancel ")
                              ]),
                              _: 1
                            }, 8, ["onClick"]),
                            $options.canUpdateStudy ? (openBlock(), createBlock(_component_jet_button, {
                              key: 0,
                              type: "submit",
                              class: "ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500",
                              onClick: $options.updateStudy
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Save ")
                              ]),
                              _: 1
                            }, 8, ["onClick"])) : createCommentVNode("", true)
                          ])
                        ])
                      ])
                    ]),
                    _: 1
                  })
                ])
              ])
            ]),
            _: 1
          })
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/Partials/Details.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const StudyDetails = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  StudyDetails as default
};
