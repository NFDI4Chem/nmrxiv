import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetFormSection } from "./FormSection-d2d52d64.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { J as JetActionMessage } from "./ActionMessage-3207b224.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { S as SelectOrcidId } from "./SelectOrcidId-6ef7a184.mjs";
import { resolveComponent, mergeProps, withCtx, createTextVNode, openBlock, createBlock, createVNode, withDirectives, vShow, createCommentVNode, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderStyle, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./DialogModal-f54eea4c.mjs";
import "./Modal-b2fb4cf2.mjs";
import "./LoadingButton-8ae902b0.mjs";
const _sfc_main = {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
    SelectOrcidId
  },
  props: ["user"],
  data() {
    return {
      form: this.$inertia.form({
        _method: this.user ? "PUT" : "POST",
        first_name: this.user ? this.user.first_name : "",
        last_name: this.user ? this.user.last_name : "",
        email: this.user ? this.user.email : "",
        username: this.user ? this.user.username : "",
        orcid_id: this.user ? this.user.orcid_id : "",
        password: null,
        password_confirmation: null,
        terms: true,
        photo: null
      }),
      error: {},
      photoPreview: null
    };
  },
  methods: {
    findOrcidID() {
      this.error.orcid = "";
      if (this.form.first_name && this.form.last_name) {
        this.selectOrcidIdElement.findOrcidID(
          this.form.first_name,
          this.form.last_name
        );
      } else {
        this.error.orcid = "Please enter first name and last name";
      }
    },
    updateProfileInformation() {
      if (this.$refs.photo) {
        this.form.photo = this.$refs.photo.files[0];
      }
      if (this.user) {
        this.form.post(route("console.users.update", this.user.id), {
          errorBag: "updateProfileInformation",
          preserveScroll: true,
          onSuccess: () => this.clearPhotoFileInput()
        });
      } else {
        this.form.post(route("console.users.store"), {
          errorBag: "updateProfileInformation",
          preserveScroll: true,
          onSuccess: () => this.clearPhotoFileInput()
        });
      }
    },
    selectNewPhoto() {
      this.$refs.photo.click();
    },
    updatePhotoPreview() {
      const photo = this.$refs.photo.files[0];
      if (!photo)
        return;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.photoPreview = e.target.result;
      };
      reader.readAsDataURL(photo);
    },
    deletePhoto() {
      this.$inertia.delete(route("users.destroy-photo", this.user.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.photoPreview = null;
          this.clearPhotoFileInput();
        }
      });
    },
    clearPhotoFileInput() {
      var _a;
      if ((_a = this.$refs.photo) == null ? void 0 : _a.value) {
        this.$refs.photo.value = null;
      }
    },
    back() {
      window.history.back();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_form_section = resolveComponent("jet-form-section");
  const _component_jet_label = resolveComponent("jet-label");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_action_message = resolveComponent("jet-action-message");
  const _component_jet_button = resolveComponent("jet-button");
  _push(ssrRenderComponent(_component_jet_form_section, mergeProps({ onSubmitted: $options.updateProfileInformation }, _attrs), {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Profile Information `);
      } else {
        return [
          createTextVNode(" Profile Information ")
        ];
      }
    }),
    description: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Update user&#39;s profile information and email address. `);
      } else {
        return [
          createTextVNode(" Update user's profile information and email address. ")
        ];
      }
    }),
    form: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($props.user && _ctx.$page.props.jetstream.managesProfilePhotos) {
          _push2(`<div class="col-span-6 sm:col-span-4"${_scopeId}><input type="file" class="hidden"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_label, {
            for: "photo",
            value: "Photo"
          }, null, _parent2, _scopeId));
          if ($props.user) {
            _push2(`<div style="${ssrRenderStyle(!$data.photoPreview ? null : { display: "none" })}" class="mt-2"${_scopeId}><img${ssrRenderAttr("src", $props.user.profile_photo_url)}${ssrRenderAttr("alt", $props.user.first_name)} class="rounded-full h-20 w-20 object-cover"${_scopeId}></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`<div style="${ssrRenderStyle($data.photoPreview ? null : { display: "none" })}" class="mt-2"${_scopeId}><span class="block rounded-full w-20 h-20" style="${ssrRenderStyle(
            "background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url('" + $data.photoPreview + "');"
          )}"${_scopeId}></span></div>`);
          _push2(ssrRenderComponent(_component_jet_secondary_button, {
            class: "mt-2 mr-2",
            type: "button",
            onClick: $options.selectNewPhoto
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Select A New Photo `);
              } else {
                return [
                  createTextVNode(" Select A New Photo ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          if ($props.user && $props.user.profile_photo_url) {
            _push2(ssrRenderComponent(_component_jet_secondary_button, {
              type: "button",
              class: "mt-2",
              onClick: $options.deletePhoto
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` Remove Photo `);
                } else {
                  return [
                    createTextVNode(" Remove Photo ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            _push2(`<!---->`);
          }
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.form.errors.photo,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="col-span-6 sm:col-span-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "first_name",
          value: "First Name"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "first_name",
          modelValue: $data.form.first_name,
          "onUpdate:modelValue": ($event) => $data.form.first_name = $event,
          type: "text",
          class: "mt-1 block w-full",
          autocomplete: "first_name"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.form.errors.first_name,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="col-span-6 sm:col-span-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "last_name",
          value: "Last Name"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "last_name",
          modelValue: $data.form.last_name,
          "onUpdate:modelValue": ($event) => $data.form.last_name = $event,
          type: "text",
          class: "mt-1 block w-full",
          autocomplete: "last_name"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.form.errors.last_name,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="col-span-6 sm:col-span-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "email",
          value: "Email"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "email",
          modelValue: $data.form.email,
          "onUpdate:modelValue": ($event) => $data.form.email = $event,
          type: "email",
          class: "mt-1 block w-full"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.form.errors.email,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="col-span-6 sm:col-span-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "username",
          value: "Username"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "username",
          modelValue: $data.form.username,
          "onUpdate:modelValue": ($event) => $data.form.username = $event,
          type: "text",
          class: "mt-1 block w-full",
          autocomplete: "username"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.form.errors.username,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="col-span-6 sm:col-span-4"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_label, {
          for: "orcid",
          value: "ORCID iD"
        }, null, _parent2, _scopeId));
        _push2(`<div class="mt-1 flex rounded-md shadow-sm"${_scopeId}><div class="relative flex items-stretch flex-grow focus-within:z-10"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_jet_input, {
          id: "orcid",
          modelValue: $data.form.orcid_id,
          "onUpdate:modelValue": ($event) => $data.form.orcid_id = $event,
          type: "text",
          class: "rounded-l-md focus:ring-indigo-200 focus:border-indigo-200 block w-full rounded-none sm:text-medium border-gray-300"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="tooltip -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 cursor-pointer"${_scopeId}><button type="button" class=""${_scopeId}><img alt="ORCID logo" src="https://orcid.org/assets/vectors/orcid.logo.icon.svg" width="20" height="20"${_scopeId}></button><span class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"${_scopeId}>Click to find ORCID iD</span></div></div>`);
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.error.orcid,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div>`);
        if (!$props.user) {
          _push2(`<div class="col-span-6 sm:col-span-4"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_label, {
            for: "password",
            value: "Password"
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_input, {
            id: "password",
            ref: "password",
            modelValue: $data.form.password,
            "onUpdate:modelValue": ($event) => $data.form.password = $event,
            type: "password",
            class: "mt-1 block w-full",
            autocomplete: "new-password"
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.form.errors.password,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if (!$props.user) {
          _push2(`<div class="col-span-6 sm:col-span-4"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_label, {
            for: "password_confirmation",
            value: "Confirm Password"
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_input, {
            id: "password_confirmation",
            modelValue: $data.form.password_confirmation,
            "onUpdate:modelValue": ($event) => $data.form.password_confirmation = $event,
            type: "password",
            class: "mt-1 block w-full",
            autocomplete: "new-password"
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.form.errors.password_confirmation,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          $props.user && _ctx.$page.props.jetstream.managesProfilePhotos ? (openBlock(), createBlock("div", {
            key: 0,
            class: "col-span-6 sm:col-span-4"
          }, [
            createVNode("input", {
              ref: "photo",
              type: "file",
              class: "hidden",
              onChange: $options.updatePhotoPreview
            }, null, 40, ["onChange"]),
            createVNode(_component_jet_label, {
              for: "photo",
              value: "Photo"
            }),
            $props.user ? withDirectives((openBlock(), createBlock("div", {
              key: 0,
              class: "mt-2"
            }, [
              createVNode("img", {
                src: $props.user.profile_photo_url,
                alt: $props.user.first_name,
                class: "rounded-full h-20 w-20 object-cover"
              }, null, 8, ["src", "alt"])
            ], 512)), [
              [vShow, !$data.photoPreview]
            ]) : createCommentVNode("", true),
            withDirectives(createVNode("div", { class: "mt-2" }, [
              createVNode("span", {
                class: "block rounded-full w-20 h-20",
                style: "background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url('" + $data.photoPreview + "');"
              }, null, 4)
            ], 512), [
              [vShow, $data.photoPreview]
            ]),
            createVNode(_component_jet_secondary_button, {
              class: "mt-2 mr-2",
              type: "button",
              onClick: withModifiers($options.selectNewPhoto, ["prevent"])
            }, {
              default: withCtx(() => [
                createTextVNode(" Select A New Photo ")
              ]),
              _: 1
            }, 8, ["onClick"]),
            $props.user && $props.user.profile_photo_url ? (openBlock(), createBlock(_component_jet_secondary_button, {
              key: 1,
              type: "button",
              class: "mt-2",
              onClick: withModifiers($options.deletePhoto, ["prevent"])
            }, {
              default: withCtx(() => [
                createTextVNode(" Remove Photo ")
              ]),
              _: 1
            }, 8, ["onClick"])) : createCommentVNode("", true),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.photo,
              class: "mt-2"
            }, null, 8, ["message"])
          ])) : createCommentVNode("", true),
          createVNode("div", { class: "col-span-6 sm:col-span-4" }, [
            createVNode(_component_jet_label, {
              for: "first_name",
              value: "First Name"
            }),
            createVNode(_component_jet_input, {
              id: "first_name",
              modelValue: $data.form.first_name,
              "onUpdate:modelValue": ($event) => $data.form.first_name = $event,
              type: "text",
              class: "mt-1 block w-full",
              autocomplete: "first_name"
            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.first_name,
              class: "mt-2"
            }, null, 8, ["message"])
          ]),
          createVNode("div", { class: "col-span-6 sm:col-span-4" }, [
            createVNode(_component_jet_label, {
              for: "last_name",
              value: "Last Name"
            }),
            createVNode(_component_jet_input, {
              id: "last_name",
              modelValue: $data.form.last_name,
              "onUpdate:modelValue": ($event) => $data.form.last_name = $event,
              type: "text",
              class: "mt-1 block w-full",
              autocomplete: "last_name"
            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.last_name,
              class: "mt-2"
            }, null, 8, ["message"])
          ]),
          createVNode("div", { class: "col-span-6 sm:col-span-4" }, [
            createVNode(_component_jet_label, {
              for: "email",
              value: "Email"
            }),
            createVNode(_component_jet_input, {
              id: "email",
              modelValue: $data.form.email,
              "onUpdate:modelValue": ($event) => $data.form.email = $event,
              type: "email",
              class: "mt-1 block w-full"
            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.email,
              class: "mt-2"
            }, null, 8, ["message"])
          ]),
          createVNode("div", { class: "col-span-6 sm:col-span-4" }, [
            createVNode(_component_jet_label, {
              for: "username",
              value: "Username"
            }),
            createVNode(_component_jet_input, {
              id: "username",
              modelValue: $data.form.username,
              "onUpdate:modelValue": ($event) => $data.form.username = $event,
              type: "text",
              class: "mt-1 block w-full",
              autocomplete: "username"
            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.username,
              class: "mt-2"
            }, null, 8, ["message"])
          ]),
          createVNode("div", { class: "col-span-6 sm:col-span-4" }, [
            createVNode(_component_jet_label, {
              for: "orcid",
              value: "ORCID iD"
            }),
            createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
              createVNode("div", { class: "relative flex items-stretch flex-grow focus-within:z-10" }, [
                createVNode(_component_jet_input, {
                  id: "orcid",
                  modelValue: $data.form.orcid_id,
                  "onUpdate:modelValue": ($event) => $data.form.orcid_id = $event,
                  type: "text",
                  class: "rounded-l-md focus:ring-indigo-200 focus:border-indigo-200 block w-full rounded-none sm:text-medium border-gray-300"
                }, null, 8, ["modelValue", "onUpdate:modelValue"])
              ]),
              createVNode("div", {
                class: "tooltip -ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 cursor-pointer",
                onClick: ($event) => $options.findOrcidID()
              }, [
                createVNode("button", {
                  type: "button",
                  class: ""
                }, [
                  createVNode("img", {
                    alt: "ORCID logo",
                    src: "https://orcid.org/assets/vectors/orcid.logo.icon.svg",
                    width: "20",
                    height: "20"
                  })
                ]),
                createVNode("span", { class: "bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom" }, "Click to find ORCID iD")
              ], 8, ["onClick"])
            ]),
            createVNode(_component_jet_input_error, {
              message: $data.error.orcid,
              class: "mt-2"
            }, null, 8, ["message"])
          ]),
          !$props.user ? (openBlock(), createBlock("div", {
            key: 1,
            class: "col-span-6 sm:col-span-4"
          }, [
            createVNode(_component_jet_label, {
              for: "password",
              value: "Password"
            }),
            createVNode(_component_jet_input, {
              id: "password",
              ref: "password",
              modelValue: $data.form.password,
              "onUpdate:modelValue": ($event) => $data.form.password = $event,
              type: "password",
              class: "mt-1 block w-full",
              autocomplete: "new-password"
            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.password,
              class: "mt-2"
            }, null, 8, ["message"])
          ])) : createCommentVNode("", true),
          !$props.user ? (openBlock(), createBlock("div", {
            key: 2,
            class: "col-span-6 sm:col-span-4"
          }, [
            createVNode(_component_jet_label, {
              for: "password_confirmation",
              value: "Confirm Password"
            }),
            createVNode(_component_jet_input, {
              id: "password_confirmation",
              modelValue: $data.form.password_confirmation,
              "onUpdate:modelValue": ($event) => $data.form.password_confirmation = $event,
              type: "password",
              class: "mt-1 block w-full",
              autocomplete: "new-password"
            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
            createVNode(_component_jet_input_error, {
              message: $data.form.errors.password_confirmation,
              class: "mt-2"
            }, null, 8, ["message"])
          ])) : createCommentVNode("", true)
        ];
      }
    }),
    actions: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          class: { "opacity-25": $data.form.processing },
          disabled: $data.form.processing,
          onClick: $options.back
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
        _push2(ssrRenderComponent(_component_jet_action_message, {
          on: $data.form.recentlySuccessful,
          class: "mr-3"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Saved. `);
            } else {
              return [
                createTextVNode(" Saved. ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_button, {
          class: { "opacity-25": $data.form.processing },
          disabled: $data.form.processing
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
          createVNode(_component_jet_secondary_button, {
            class: { "opacity-25": $data.form.processing },
            disabled: $data.form.processing,
            onClick: $options.back
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["class", "disabled", "onClick"]),
          createVNode(_component_jet_action_message, {
            on: $data.form.recentlySuccessful,
            class: "mr-3"
          }, {
            default: withCtx(() => [
              createTextVNode(" Saved. ")
            ]),
            _: 1
          }, 8, ["on"]),
          createVNode(_component_jet_button, {
            class: { "opacity-25": $data.form.processing },
            disabled: $data.form.processing
          }, {
            default: withCtx(() => [
              createTextVNode(" Save ")
            ]),
            _: 1
          }, 8, ["class", "disabled"])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Console/Users/Partials/UserProfile.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const UserProfile = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  UserProfile as default
};
