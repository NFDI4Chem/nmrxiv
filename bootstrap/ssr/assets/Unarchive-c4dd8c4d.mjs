import { J as JetActionSection } from "./ActionSection-c1b53bcc.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { L as LoadingButton } from "./LoadingButton-8ae902b0.mjs";
import { Link } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createTextVNode, createSlots, createVNode, openBlock, createBlock, Fragment, withKeys, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
    JetActionSection,
    JetDangerButton,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetSecondaryButton,
    LoadingButton,
    Link
  },
  props: ["project"],
  data() {
    return {
      loading: false,
      confirmingProjectUnarchival: false,
      confirmingProjectUnarchivalWithoutPassword: false,
      form: this.$inertia.form({
        password: ""
      }),
      hasPassword: false
    };
  },
  methods: {
    confirmProjectDeletion() {
      this.loading = true;
      this.confirmingProjectUnarchival = true;
      axios.get(route("auth.checkPassword", this.project.id)).then((res) => {
        this.hasPassword = res.data.hasPassword;
      }).finally(() => {
        this.loading = false;
      });
    },
    unarchiveProject() {
      this.form.put(
        route("dashboard.project.toggle-archive", this.project.id),
        {
          preserveScroll: true,
          onSuccess: () => this.closeModal(),
          onError: () => this.$refs.password.focus(),
          onFinish: () => this.form.reset()
        }
      );
    },
    closeModal() {
      this.confirmingProjectUnarchival = false;
      this.confirmingProjectUnarchivalWithoutPassword = false;
      this.form.reset();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_action_section = resolveComponent("jet-action-section");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_loading_button = resolveComponent("loading-button");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  _push(ssrRenderComponent(_component_jet_action_section, _attrs, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Unarchive Project `);
      } else {
        return [
          createTextVNode(" Unarchive Project ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Permanently unarchive your project. `);
      } else {
        return [
          createTextVNode(" Permanently unarchive your project. ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="max-w-xl text-sm text-gray-600"${_scopeId}> Once your project is unarchived, all of its resources and data will be permanently unarchived. Before deleting your project, please download any data or information that you wish to retain. </div><div class="mt-5"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_danger_button, { onClick: $options.confirmProjectDeletion }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Unarchive Project `);
            } else {
              return [
                createTextVNode(" Unarchive Project ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
        _push2(ssrRenderComponent(_component_jet_dialog_modal, {
          show: $data.confirmingProjectUnarchival,
          onClose: $options.closeModal
        }, createSlots({
          title: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Unarchive Project `);
            } else {
              return [
                createTextVNode(" Unarchive Project ")
              ];
            }
          }),
          content: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_loading_button, { loading: $data.loading }, null, _parent3, _scopeId2));
              if (!$data.loading) {
                _push3(`<!--[-->`);
                if ($data.hasPassword) {
                  _push3(`<span${_scopeId2}> Are you sure you want to unarchive your project? Once your project is unarchived, all of its resources and data will be permanently unarchived. Please enter your password to confirm you would like to permanently unarchive your project. <div class="mt-4"${_scopeId2}>`);
                  _push3(ssrRenderComponent(_component_jet_input, {
                    ref: "password",
                    modelValue: $data.form.password,
                    "onUpdate:modelValue": ($event) => $data.form.password = $event,
                    type: "password",
                    class: "mt-1 block w-3/4",
                    placeholder: "Password",
                    onKeyup: $options.unarchiveProject
                  }, null, _parent3, _scopeId2));
                  _push3(ssrRenderComponent(_component_jet_input_error, {
                    message: $data.form.errors.password,
                    class: "mt-2"
                  }, null, _parent3, _scopeId2));
                  _push3(`</div></span>`);
                } else {
                  _push3(`<span${_scopeId2}> You need to set your password first before deleting the project. </span>`);
                }
                _push3(`<!--]-->`);
              } else {
                _push3(`<!---->`);
              }
            } else {
              return [
                createVNode(_component_loading_button, { loading: $data.loading }, null, 8, ["loading"]),
                !$data.loading ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                  $data.hasPassword ? (openBlock(), createBlock("span", { key: 0 }, [
                    createTextVNode(" Are you sure you want to unarchive your project? Once your project is unarchived, all of its resources and data will be permanently unarchived. Please enter your password to confirm you would like to permanently unarchive your project. "),
                    createVNode("div", { class: "mt-4" }, [
                      createVNode(_component_jet_input, {
                        ref: "password",
                        modelValue: $data.form.password,
                        "onUpdate:modelValue": ($event) => $data.form.password = $event,
                        type: "password",
                        class: "mt-1 block w-3/4",
                        placeholder: "Password",
                        onKeyup: withKeys($options.unarchiveProject, ["enter"])
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "onKeyup"]),
                      createVNode(_component_jet_input_error, {
                        message: $data.form.errors.password,
                        class: "mt-2"
                      }, null, 8, ["message"])
                    ])
                  ])) : (openBlock(), createBlock("span", { key: 1 }, " You need to set your password first before deleting the project. "))
                ], 64)) : createCommentVNode("", true)
              ];
            }
          }),
          _: 2
        }, [
          !$data.loading ? {
            name: "footer",
            fn: withCtx((_2, _push3, _parent3, _scopeId2) => {
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
                if ($data.hasPassword) {
                  _push3(ssrRenderComponent(_component_jet_danger_button, {
                    class: ["ml-2", { "opacity-25": $data.form.processing }],
                    disabled: $data.form.processing,
                    onClick: $options.unarchiveProject
                  }, {
                    default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(` Unarchive Project `);
                      } else {
                        return [
                          createTextVNode(" Unarchive Project ")
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                } else {
                  _push3(`<!---->`);
                }
              } else {
                return [
                  createVNode(_component_jet_secondary_button, { onClick: $options.closeModal }, {
                    default: withCtx(() => [
                      createTextVNode(" Cancel ")
                    ]),
                    _: 1
                  }, 8, ["onClick"]),
                  $data.hasPassword ? (openBlock(), createBlock(_component_jet_danger_button, {
                    key: 0,
                    class: ["ml-2", { "opacity-25": $data.form.processing }],
                    disabled: $data.form.processing,
                    onClick: $options.unarchiveProject
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Unarchive Project ")
                    ]),
                    _: 1
                  }, 8, ["class", "disabled", "onClick"])) : createCommentVNode("", true)
                ];
              }
            }),
            key: "0"
          } : void 0
        ]), _parent2, _scopeId));
      } else {
        return [
          createVNode("div", { class: "max-w-xl text-sm text-gray-600" }, " Once your project is unarchived, all of its resources and data will be permanently unarchived. Before deleting your project, please download any data or information that you wish to retain. "),
          createVNode("div", { class: "mt-5" }, [
            createVNode(_component_jet_danger_button, { onClick: $options.confirmProjectDeletion }, {
              default: withCtx(() => [
                createTextVNode(" Unarchive Project ")
              ]),
              _: 1
            }, 8, ["onClick"])
          ]),
          createVNode(_component_jet_dialog_modal, {
            show: $data.confirmingProjectUnarchival,
            onClose: $options.closeModal
          }, createSlots({
            title: withCtx(() => [
              createTextVNode(" Unarchive Project ")
            ]),
            content: withCtx(() => [
              createVNode(_component_loading_button, { loading: $data.loading }, null, 8, ["loading"]),
              !$data.loading ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                $data.hasPassword ? (openBlock(), createBlock("span", { key: 0 }, [
                  createTextVNode(" Are you sure you want to unarchive your project? Once your project is unarchived, all of its resources and data will be permanently unarchived. Please enter your password to confirm you would like to permanently unarchive your project. "),
                  createVNode("div", { class: "mt-4" }, [
                    createVNode(_component_jet_input, {
                      ref: "password",
                      modelValue: $data.form.password,
                      "onUpdate:modelValue": ($event) => $data.form.password = $event,
                      type: "password",
                      class: "mt-1 block w-3/4",
                      placeholder: "Password",
                      onKeyup: withKeys($options.unarchiveProject, ["enter"])
                    }, null, 8, ["modelValue", "onUpdate:modelValue", "onKeyup"]),
                    createVNode(_component_jet_input_error, {
                      message: $data.form.errors.password,
                      class: "mt-2"
                    }, null, 8, ["message"])
                  ])
                ])) : (openBlock(), createBlock("span", { key: 1 }, " You need to set your password first before deleting the project. "))
              ], 64)) : createCommentVNode("", true)
            ]),
            _: 2
          }, [
            !$data.loading ? {
              name: "footer",
              fn: withCtx(() => [
                createVNode(_component_jet_secondary_button, { onClick: $options.closeModal }, {
                  default: withCtx(() => [
                    createTextVNode(" Cancel ")
                  ]),
                  _: 1
                }, 8, ["onClick"]),
                $data.hasPassword ? (openBlock(), createBlock(_component_jet_danger_button, {
                  key: 0,
                  class: ["ml-2", { "opacity-25": $data.form.processing }],
                  disabled: $data.form.processing,
                  onClick: $options.unarchiveProject
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Unarchive Project ")
                  ]),
                  _: 1
                }, 8, ["class", "disabled", "onClick"])) : createCommentVNode("", true)
              ]),
              key: "0"
            } : void 0
          ]), 1032, ["show", "onClose"])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Project/Partials/Unarchive.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const ProjectUnarchive = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  ProjectUnarchive as default
};
