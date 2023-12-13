import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { AtSymbolIcon, CodeBracketIcon, LinkIcon, CheckCircleIcon, ChevronRightIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { T as ToggleButton } from "./ToggleButton-fec39d54.mjs";
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from "@headlessui/vue";
import Datepicker from "@vuepic/vue-datepicker";
/* empty css                */import { resolveComponent, mergeProps, withCtx, createTextVNode, createVNode, withDirectives, vModelText, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
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
    Datepicker,
    ToggleButton
  },
  props: [],
  data() {
    return {
      selectedAnnouncement: null,
      editAnnouncementForm: this.$inertia.form({
        _method: "POST",
        id: "",
        title: "",
        message: "",
        enabled: null,
        error_message: null,
        creator_id: null,
        start_time: null,
        end_time: null
      }),
      editAnnouncementDialog: false
    };
  },
  methods: {
    editAnnouncement() {
      this.editAnnouncementForm.put(
        route(
          "console.announcements.edit",
          this.editAnnouncementForm.id
        ),
        {
          preserveScroll: true,
          onSuccess: () => {
            this.editAnnouncementDialog = false;
            this.editAnnouncementForm.reset();
          },
          onError: (err) => console.error(err)
        }
      );
    },
    toggleEditAnnouncementDialog(announcement) {
      this.selectedAnnouncement = announcement;
      this.editAnnouncementForm.id = announcement.id;
      this.editAnnouncementForm.title = announcement.title;
      this.editAnnouncementForm.message = announcement.message;
      if (announcement.status == "active" || announcement.status == "Active")
        this.editAnnouncementForm.enabled = true;
      else
        this.editAnnouncementForm.enabled = false;
      this.editAnnouncementForm.start_time = announcement.start_time;
      this.editAnnouncementForm.end_time = announcement.end_time;
      this.editAnnouncementDialog = !this.editAnnouncementDialog;
    },
    onCancel() {
      this.editAnnouncementDialog = false;
      this.editAnnouncementForm.clearErrors();
      this.$page.props.errors = new Object();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_validation_errors = resolveComponent("jet-validation-errors");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_toggle_button = resolveComponent("toggle-button");
  const _component_Datepicker = resolveComponent("Datepicker");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_button = resolveComponent("jet-button");
  _push(ssrRenderComponent(_component_jet_dialog_modal, mergeProps({
    show: $data.editAnnouncementDialog,
    onClose: ($event) => $data.editAnnouncementDialog = false
  }, _attrs), {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Edit Announcement `);
      } else {
        return [
          createTextVNode(" Edit Announcement ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_validation_errors, { class: "mb-4" }, null, _parent2, _scopeId));
        _push2(`<div class="relative z-0 mt-1 rounded-lg cursor-pointer"${_scopeId}><div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6"${_scopeId}><div class="sm:col-span-6"${_scopeId}><label for="name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Title </label><div class="mt-1 flex rounded-md shadow-sm"${_scopeId}><input id="name"${ssrRenderAttr("value", $data.editAnnouncementForm.title)} type="text" name="name" autocomplete="off" class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"${_scopeId}></div>`);
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.editAnnouncementForm.errors.title,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="sm:col-span-6"${_scopeId}><label for="name" class="block text-sm font-medium text-gray-700 after:content-[&#39;*&#39;] after:ml-0.5 after:text-red-500"${_scopeId}> Message </label><div mt-1 flex rounded-md shadow-sm${_scopeId}><textarea id="description" name="description" placeholder="Message to the users.." rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"${_scopeId}>${ssrInterpolate($data.editAnnouncementForm.message)}</textarea></div>`);
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.editAnnouncementForm.errors.message,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`<div class="py-2"${_scopeId}><label class="block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700"${_scopeId}> Status </label>`);
        _push2(ssrRenderComponent(_component_toggle_button, {
          enabled: $data.editAnnouncementForm.enabled,
          "onUpdate:enabled": ($event) => $data.editAnnouncementForm.enabled = $event
        }, null, _parent2, _scopeId));
        _push2(`</div></div></div><div class="sm grid grid-cols-2 gap-4 pt-4"${_scopeId}><div${_scopeId}><label class="block text-sm font-medium text-gray-700"${_scopeId}> Start Time </label>`);
        _push2(ssrRenderComponent(_component_Datepicker, {
          modelValue: $data.editAnnouncementForm.start_time,
          "onUpdate:modelValue": ($event) => $data.editAnnouncementForm.start_time = $event
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.editAnnouncementForm.errors.start_time,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div><div${_scopeId}><label class="block text-sm font-medium text-gray-700"${_scopeId}> End Time </label>`);
        _push2(ssrRenderComponent(_component_Datepicker, {
          modelValue: $data.editAnnouncementForm.end_time,
          "onUpdate:modelValue": ($event) => $data.editAnnouncementForm.end_time = $event
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_jet_input_error, {
          message: $data.editAnnouncementForm.errors.end_time,
          class: "mt-2"
        }, null, _parent2, _scopeId));
        _push2(`</div></div></div>`);
      } else {
        return [
          createVNode(_component_jet_validation_errors, { class: "mb-4" }),
          createVNode("div", { class: "relative z-0 mt-1 rounded-lg cursor-pointer" }, [
            createVNode("div", { class: "mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6" }, [
              createVNode("div", { class: "sm:col-span-6" }, [
                createVNode("label", {
                  for: "name",
                  class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                }, " Title "),
                createVNode("div", { class: "mt-1 flex rounded-md shadow-sm" }, [
                  withDirectives(createVNode("input", {
                    id: "name",
                    "onUpdate:modelValue": ($event) => $data.editAnnouncementForm.title = $event,
                    type: "text",
                    name: "name",
                    autocomplete: "off",
                    class: "flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded sm:text-sm border-gray-300"
                  }, null, 8, ["onUpdate:modelValue"]), [
                    [vModelText, $data.editAnnouncementForm.title]
                  ])
                ]),
                createVNode(_component_jet_input_error, {
                  message: $data.editAnnouncementForm.errors.title,
                  class: "mt-2"
                }, null, 8, ["message"])
              ]),
              createVNode("div", { class: "sm:col-span-6" }, [
                createVNode("label", {
                  for: "name",
                  class: "block text-sm font-medium text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500"
                }, " Message "),
                createVNode("div", {
                  "mt-1": "",
                  flex: "",
                  "rounded-md": "",
                  "shadow-sm": ""
                }, [
                  withDirectives(createVNode("textarea", {
                    id: "description",
                    "onUpdate:modelValue": ($event) => $data.editAnnouncementForm.message = $event,
                    name: "description",
                    placeholder: "Message to the users..",
                    rows: "3",
                    class: "shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  }, null, 8, ["onUpdate:modelValue"]), [
                    [vModelText, $data.editAnnouncementForm.message]
                  ])
                ]),
                createVNode(_component_jet_input_error, {
                  message: $data.editAnnouncementForm.errors.message,
                  class: "mt-2"
                }, null, 8, ["message"]),
                createVNode("div", { class: "py-2" }, [
                  createVNode("label", { class: "block text-sm font-medium text-gray-700, block text-sm font-medium text-gray-700" }, " Status "),
                  createVNode(_component_toggle_button, {
                    enabled: $data.editAnnouncementForm.enabled,
                    "onUpdate:enabled": ($event) => $data.editAnnouncementForm.enabled = $event
                  }, null, 8, ["enabled", "onUpdate:enabled"])
                ])
              ])
            ]),
            createVNode("div", { class: "sm grid grid-cols-2 gap-4 pt-4" }, [
              createVNode("div", null, [
                createVNode("label", { class: "block text-sm font-medium text-gray-700" }, " Start Time "),
                createVNode(_component_Datepicker, {
                  modelValue: $data.editAnnouncementForm.start_time,
                  "onUpdate:modelValue": ($event) => $data.editAnnouncementForm.start_time = $event
                }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                createVNode(_component_jet_input_error, {
                  message: $data.editAnnouncementForm.errors.start_time,
                  class: "mt-2"
                }, null, 8, ["message"])
              ]),
              createVNode("div", null, [
                createVNode("label", { class: "block text-sm font-medium text-gray-700" }, " End Time "),
                createVNode(_component_Datepicker, {
                  modelValue: $data.editAnnouncementForm.end_time,
                  "onUpdate:modelValue": ($event) => $data.editAnnouncementForm.end_time = $event
                }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                createVNode(_component_jet_input_error, {
                  message: $data.editAnnouncementForm.errors.end_time,
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
        _push2(ssrRenderComponent(_component_jet_secondary_button, { onClick: $options.onCancel }, {
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
          class: ["ml-2", { "opacity-25": $data.editAnnouncementForm.processing }],
          disabled: $data.editAnnouncementForm.processing,
          onClick: $options.editAnnouncement
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Update `);
            } else {
              return [
                createTextVNode(" Update ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, { onClick: $options.onCancel }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_button, {
            class: ["ml-2", { "opacity-25": $data.editAnnouncementForm.processing }],
            disabled: $data.editAnnouncementForm.processing,
            onClick: $options.editAnnouncement
          }, {
            default: withCtx(() => [
              createTextVNode(" Update ")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Announcement/Partials/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const AnnouncementEdit = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  AnnouncementEdit as default
};
