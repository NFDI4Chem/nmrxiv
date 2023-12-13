import { useSSRContext, mergeProps, ref, resolveComponent, withCtx, createVNode, createTextVNode, toDisplayString, openBlock, createBlock, Fragment, renderList, createCommentVNode, Transition } from "vue";
import { UsersIcon } from "@heroicons/vue/24/outline";
import { J as JetActionMessage } from "./ActionMessage-3207b224.mjs";
import { J as JetActionSection } from "./ActionSection-c1b53bcc.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetConfirmationModal } from "./ConfirmationModal-d0b47856.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { ssrRenderAttrs, ssrRenderClass, ssrRenderSlot, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetSectionBorder } from "./SectionBorder-7d1f222a.mjs";
import { Menu, MenuButton, MenuItem, MenuItems, Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, Dialog, DialogPanel, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/24/solid";
const _sfc_main$2 = {
  components: {},
  emits: ["submitted"],
  computed: {
    hasActions() {
      return !!this.$slots.actions;
    }
  }
};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "mt-5 md:mt-0" }, _attrs))}><form><div class="${ssrRenderClass(
    $options.hasActions ? "sm:rounded-tl-md sm:rounded-tr-md" : "sm:rounded-md"
  )}"><div class="grid grid-cols-6 gap-6">`);
  ssrRenderSlot(_ctx.$slots, "form", {}, null, _push, _parent);
  _push(`</div></div>`);
  if ($options.hasActions) {
    _push(`<div class="mt-3">`);
    ssrRenderSlot(_ctx.$slots, "actions", {}, null, _push, _parent);
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</form></div>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/ModalFormSection.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const JetModalFormSection = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$1 = {
  props: ["modelValue", "rows"],
  emits: ["update:modelValue"],
  methods: {
    focus() {
      this.$refs.textarea.focus();
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  let _temp0;
  _push(`<textarea${ssrRenderAttrs(_temp0 = mergeProps({
    ref: "textarea",
    rows: $props.rows,
    class: "border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm",
    value: $props.modelValue
  }, _attrs), "textarea")}>${ssrInterpolate("value" in _temp0 ? _temp0.value : "")}</textarea>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Jetstream/TextArea.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const JetTextArea = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    ChevronDownIcon,
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    TransitionChild,
    JetModalFormSection,
    TransitionRoot,
    UsersIcon,
    JetActionMessage,
    JetActionSection,
    JetButton,
    JetConfirmationModal,
    JetDangerButton,
    JetDialogModal,
    JetInput,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
    JetSectionBorder,
    JetTextArea
  },
  props: {
    members: Object,
    project: Object,
    study: Object,
    dataset: Object,
    team: Object,
    availableRoles: Object,
    role: String,
    model: String,
    calledFrom: String
  },
  setup() {
    const open = ref(false);
    const addUser = ref(false);
    return {
      open,
      addUser
    };
  },
  data() {
    return {
      addMemberForm: this.$inertia.form({
        email: "",
        role: null,
        message: ""
      }),
      updateRoleForm: this.$inertia.form({
        role: null
      }),
      removeModelMemberForm: this.$inertia.form({})
    };
  },
  computed: {
    modelObject() {
      if (this.model == "study") {
        return this.study;
      } else if (this.model == "project") {
        return this.project;
      }
    },
    modelInvitations() {
      return this.modelObject[this.model + "_invitations"];
    },
    //Check if project is already shared for when called from Study view.
    isProjectAlreadyShared() {
      var count = 0;
      var isShared = false;
      if (this.calledFrom == "studyView") {
        if (this.members) {
          this.members.forEach((member) => {
            if (member.hasOwnProperty("project_membership")) {
              count = count + 1;
            }
          });
          if (count > 1) {
            isShared = true;
          }
        }
      }
      return isShared;
    },
    canChangeRole() {
      if (this.role) {
        return this.role == "creator" || this.role == "owner";
      } else {
        return true;
      }
    }
  },
  methods: {
    personRole(person) {
      if (person[this.model + "_membership"]) {
        return person[this.model + "_membership"].role;
      } else {
        if (person["project_membership"]) {
          return person["project_membership"].role;
        } else if (person["study_membership"]) {
          return person["study_membership"].role;
        } else if (person["dataset_membership"]) {
          return person["dataset_membership"].role;
        }
      }
    },
    addModelMember() {
      this.addMemberForm.post(
        route(this.model + "-members.store", this[this.model]),
        {
          errorBag: "addModelMember",
          preserveScroll: true,
          onSuccess: () => this.addMemberForm.reset()
        }
      );
    },
    cancelModelInvitation(invitation) {
      this.$inertia.delete(
        route(this.model + "-invitations.destroy", invitation),
        {
          preserveScroll: true
        }
      );
    },
    updateRole(role, managingRoleFor) {
      this.updateRoleForm.role = role.key;
      this.updateRoleForm.put(
        route(this.model + "-members.update", [
          this[this.model],
          managingRoleFor
        ]),
        {
          preserveScroll: true,
          onSuccess: () => {
            this.updateRoleForm.role = null;
          }
        }
      );
    },
    removeModelMember(modelMemberBeingRemoved) {
      this.removeModelMemberForm.delete(
        route(this.model + "-members.destroy", [
          this[this.model],
          modelMemberBeingRemoved
        ]),
        {
          errorBag: "removeModelMember",
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
          }
        }
      );
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_TransitionRoot = resolveComponent("TransitionRoot");
  const _component_Dialog = resolveComponent("Dialog");
  const _component_TransitionChild = resolveComponent("TransitionChild");
  const _component_DialogPanel = resolveComponent("DialogPanel");
  const _component_Menu = resolveComponent("Menu");
  const _component_MenuButton = resolveComponent("MenuButton");
  const _component_ChevronDownIcon = resolveComponent("ChevronDownIcon");
  const _component_MenuItems = resolveComponent("MenuItems");
  const _component_MenuItem = resolveComponent("MenuItem");
  const _component_jet_modal_form_section = resolveComponent("jet-modal-form-section");
  const _component_jet_label = resolveComponent("jet-label");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_text_area = resolveComponent("jet-text-area");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_jet_action_message = resolveComponent("jet-action-message");
  _push(`<!--[--><div class="cursor-pointer flex flex-row-reverse justify-end mr-2">`);
  if ($props.members.length > 0) {
    _push(`<div class="tooltip w-8 h-8 -ml-.5 rounded-full border-2 border-white bg-gray-100" alt=""><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 m-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path></svg><span class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom">Click here to edit sharing options.</span></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<!--[-->`);
  ssrRenderList($props.members, (user) => {
    _push(`<img class="w-8 h-8 -mr-2 rounded-full border-2 border-white"${ssrRenderAttr("src", user.profile_photo_url)}${ssrRenderAttr("alt", user.first_name)}>`);
  });
  _push(`<!--]--><!--[-->`);
  ssrRenderList($props.team ? $props.team.users : [], (user) => {
    _push(`<img class="w-8 h-8 -mr-2 rounded-full border-2 border-white"${ssrRenderAttr("src", user.profile_photo_url)}${ssrRenderAttr("alt", user.first_name)}>`);
  });
  _push(`<!--]-->`);
  if ($props.team && !$props.team.personal_team) {
    _push(`<img class="w-8 h-8 -mr-2 rounded-full border-2 border-white"${ssrRenderAttr("src", $props.team.owner.profile_photo_url)}${ssrRenderAttr("alt", $props.team.owner.first_name)}>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
  _push(ssrRenderComponent(_component_TransitionRoot, {
    show: $setup.open,
    as: "template",
    appear: "",
    onAfterLeave: ($event) => _ctx.query = ""
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_Dialog, {
          as: "div",
          class: "relative z-10 p-4",
          onClose: ($event) => $setup.open = false
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(`<div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"${_scopeId3}></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0 scale-95",
                "enter-to": "opacity-100 scale-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100 scale-100",
                "leave-to": "opacity-0 scale-95"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(ssrRenderComponent(_component_DialogPanel, { class: "mx-auto max-w-xl transform rounded-xl bg-white p-2 shadow-2xl ring-1 ring-black ring-opacity-5 transition-all p-4" }, {
                      default: withCtx((_4, _push5, _parent5, _scopeId4) => {
                        if (_push5) {
                          _push5(`<div class="sm:flex sm:items-start sm:justify-between"${_scopeId4}><div${_scopeId4}><h3 class="text-lg leading-6 font-medium text-gray-900"${_scopeId4}> Share with users </h3><span class="float-center text-xs cursor-pointer hover:text-blue-700 mt-2"${_scopeId4}><a href="https://docs.nmrxiv.org/submission-guides/sharing.html" target="_blank"${_scopeId4}>Learn more about sharing </a></span><div class="mt-2 max-w-xl text-sm text-gray-500"${_scopeId4}></div></div><div class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center"${_scopeId4}>`);
                          if (!$setup.addUser && $options.canChangeRole) {
                            _push5(`<button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none sm:text-sm"${_scopeId4}><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId4}><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"${_scopeId4}></path></svg>  SHARE </button>`);
                          } else {
                            _push5(`<!---->`);
                          }
                          if ($setup.addUser && $options.canChangeRole) {
                            _push5(`<button type="button" class="inline-flex items-center px-4 py-2 border shadow-sm font-medium rounded-md text-dark bg-white-600 hover:bg-white-700 focus:outline-none sm:text-sm"${_scopeId4}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId4}><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"${_scopeId4}></path></svg></button>`);
                          } else {
                            _push5(`<!---->`);
                          }
                          _push5(`</div></div>`);
                          if (!$setup.addUser) {
                            _push5(`<div class="flow-root mt-1"${_scopeId4}>`);
                            if ($props.members.length > 0) {
                              _push5(`<ul role="list" class="my-5 shadow divide-y divide-gray-200"${_scopeId4}><!--[-->`);
                              ssrRenderList($props.members, (person) => {
                                _push5(`<li${_scopeId4}><a class="block"${_scopeId4}><div class="flex items-center px-4 py-4 sm:px-6"${_scopeId4}><div class="min-w-0 flex-1 pt-3 flex items-center"${_scopeId4}><div class="flex-shrink-0"${_scopeId4}><img class="h-12 w-12 rounded-full"${ssrRenderAttr(
                                  "src",
                                  person.profile_photo_url
                                )}${ssrRenderAttr("alt", person.first_name)}${_scopeId4}></div><div class="min-w-0 flex-1 px-4"${_scopeId4}><div${_scopeId4}><p class="text-sm font-medium text-gray-600"${_scopeId4}>${ssrInterpolate(person.first_name)} ${ssrInterpolate(person.last_name)} `);
                                if (_ctx.$page.props.user.email == person.email) {
                                  _push5(`<span${_scopeId4}>(you)</span>`);
                                } else {
                                  _push5(`<!---->`);
                                }
                                _push5(`</p><p class="mt-2 flex items-center text-sm text-gray-500"${_scopeId4}><svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId4}><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"${_scopeId4}></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"${_scopeId4}></path></svg><span${_scopeId4}>${ssrInterpolate(person.email)}</span></p></div></div></div><div class="-mt-4"${_scopeId4}>`);
                                if ($options.canChangeRole && !$options.isProjectAlreadyShared) {
                                  _push5(`<div${_scopeId4}>`);
                                  if ($options.personRole(
                                    person
                                  ) && $options.personRole(
                                    person
                                  ) == "creator") {
                                    _push5(`<div${_scopeId4}><span class="ml-6 text-sm text-dark-500"${_scopeId4}> Creator </span></div>`);
                                  } else {
                                    _push5(`<div${_scopeId4}><button class="cursor-pointer mr-3 text-sm text-red-500 hover:text-red-700"${_scopeId4}> Remove </button>`);
                                    _push5(ssrRenderComponent(_component_Menu, {
                                      as: "div",
                                      class: "relative inline-block text-left"
                                    }, {
                                      default: withCtx((_5, _push6, _parent6, _scopeId5) => {
                                        if (_push6) {
                                          _push6(`<div${_scopeId5}>`);
                                          _push6(ssrRenderComponent(_component_MenuButton, { class: "inline-flex justify-center w-full rounded-md capitalize border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" }, {
                                            default: withCtx((_6, _push7, _parent7, _scopeId6) => {
                                              if (_push7) {
                                                _push7(`${ssrInterpolate($options.personRole(
                                                  person
                                                ))} `);
                                                _push7(ssrRenderComponent(_component_ChevronDownIcon, {
                                                  class: "-mr-1 ml-2 h-5 w-5",
                                                  "aria-hidden": "true"
                                                }, null, _parent7, _scopeId6));
                                              } else {
                                                return [
                                                  createTextVNode(toDisplayString($options.personRole(
                                                    person
                                                  )) + " ", 1),
                                                  createVNode(_component_ChevronDownIcon, {
                                                    class: "-mr-1 ml-2 h-5 w-5",
                                                    "aria-hidden": "true"
                                                  })
                                                ];
                                              }
                                            }),
                                            _: 2
                                          }, _parent6, _scopeId5));
                                          _push6(`</div>`);
                                          _push6(ssrRenderComponent(_component_MenuItems, { class: "origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" }, {
                                            default: withCtx((_6, _push7, _parent7, _scopeId6) => {
                                              if (_push7) {
                                                _push7(`<div class="py-1"${_scopeId6}><!--[-->`);
                                                ssrRenderList($props.availableRoles, (role) => {
                                                  _push7(`<span${_scopeId6}>`);
                                                  if (person[$props.model + "_membership"].role != role.key) {
                                                    _push7(ssrRenderComponent(_component_MenuItem, {
                                                      onClick: ($event) => $options.updateRole(
                                                        role,
                                                        person
                                                      )
                                                    }, {
                                                      default: withCtx(({
                                                        active
                                                      }, _push8, _parent8, _scopeId7) => {
                                                        if (_push8) {
                                                          _push8(`<a href="#" class="${ssrRenderClass([
                                                            active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                                            "block px-4 py-2 text-sm"
                                                          ])}"${_scopeId7}>${ssrInterpolate(role.name)}</a>`);
                                                        } else {
                                                          return [
                                                            createVNode("a", {
                                                              href: "#",
                                                              class: [
                                                                active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                                                "block px-4 py-2 text-sm"
                                                              ]
                                                            }, toDisplayString(role.name), 3)
                                                          ];
                                                        }
                                                      }),
                                                      _: 2
                                                    }, _parent7, _scopeId6));
                                                  } else {
                                                    _push7(`<!---->`);
                                                  }
                                                  _push7(`</span>`);
                                                });
                                                _push7(`<!--]--></div>`);
                                              } else {
                                                return [
                                                  createVNode("div", { class: "py-1" }, [
                                                    (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role) => {
                                                      return openBlock(), createBlock("span", {
                                                        key: role.key
                                                      }, [
                                                        person[$props.model + "_membership"].role != role.key ? (openBlock(), createBlock(_component_MenuItem, {
                                                          key: 0,
                                                          onClick: ($event) => $options.updateRole(
                                                            role,
                                                            person
                                                          )
                                                        }, {
                                                          default: withCtx(({
                                                            active
                                                          }) => [
                                                            createVNode("a", {
                                                              href: "#",
                                                              class: [
                                                                active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                                                "block px-4 py-2 text-sm"
                                                              ]
                                                            }, toDisplayString(role.name), 3)
                                                          ]),
                                                          _: 2
                                                        }, 1032, ["onClick"])) : createCommentVNode("", true)
                                                      ]);
                                                    }), 128))
                                                  ])
                                                ];
                                              }
                                            }),
                                            _: 2
                                          }, _parent6, _scopeId5));
                                        } else {
                                          return [
                                            createVNode("div", null, [
                                              createVNode(_component_MenuButton, { class: "inline-flex justify-center w-full rounded-md capitalize border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" }, {
                                                default: withCtx(() => [
                                                  createTextVNode(toDisplayString($options.personRole(
                                                    person
                                                  )) + " ", 1),
                                                  createVNode(_component_ChevronDownIcon, {
                                                    class: "-mr-1 ml-2 h-5 w-5",
                                                    "aria-hidden": "true"
                                                  })
                                                ]),
                                                _: 2
                                              }, 1024)
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
                                                      (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role) => {
                                                        return openBlock(), createBlock("span", {
                                                          key: role.key
                                                        }, [
                                                          person[$props.model + "_membership"].role != role.key ? (openBlock(), createBlock(_component_MenuItem, {
                                                            key: 0,
                                                            onClick: ($event) => $options.updateRole(
                                                              role,
                                                              person
                                                            )
                                                          }, {
                                                            default: withCtx(({
                                                              active
                                                            }) => [
                                                              createVNode("a", {
                                                                href: "#",
                                                                class: [
                                                                  active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                                                  "block px-4 py-2 text-sm"
                                                                ]
                                                              }, toDisplayString(role.name), 3)
                                                            ]),
                                                            _: 2
                                                          }, 1032, ["onClick"])) : createCommentVNode("", true)
                                                        ]);
                                                      }), 128))
                                                    ])
                                                  ]),
                                                  _: 2
                                                }, 1024)
                                              ]),
                                              _: 2
                                            }, 1024)
                                          ];
                                        }
                                      }),
                                      _: 2
                                    }, _parent5, _scopeId4));
                                    _push5(`</div>`);
                                  }
                                  _push5(`</div>`);
                                } else {
                                  _push5(`<div${_scopeId4}>`);
                                  if ($options.personRole(person)) {
                                    _push5(`<div${_scopeId4}><span class="ml-6 text-sm capitalize text-dark-500"${_scopeId4}>${ssrInterpolate($options.personRole(
                                      person
                                    ))}</span></div>`);
                                  } else {
                                    _push5(`<!---->`);
                                  }
                                  _push5(`</div>`);
                                }
                                _push5(`</div></div></a></li>`);
                              });
                              _push5(`<!--]--></ul>`);
                            } else {
                              _push5(`<!---->`);
                            }
                            if ($props.team && !$props.team.personal_team) {
                              _push5(`<span${_scopeId4}><div class="mb-2"${_scopeId4}><h2 class="text-lg uppercase leading-6 font-medium text-gray-900"${_scopeId4}>${ssrInterpolate($props.team.name)}</h2><p class="mt-1 text-sm text-gray-500"${_scopeId4}> All the users (corresponding roles) in the team will be applicable to this ${ssrInterpolate($props.model)}. </p></div><div class="bg-white shadow overflow-hidden sm:rounded-md"${_scopeId4}><ul role="list" class="divide-y divide-gray-200"${_scopeId4}><!--[-->`);
                              ssrRenderList($props.team.users, (person) => {
                                _push5(`<li${_scopeId4}><a class="block hover:bg-gray-50"${_scopeId4}><div class="flex items-center px-4 py-4 sm:px-6"${_scopeId4}><div class="min-w-0 flex-1 flex items-center"${_scopeId4}><div class="flex-shrink-0"${_scopeId4}><img class="h-12 w-12 rounded-full"${ssrRenderAttr(
                                  "src",
                                  person.profile_photo_url
                                )}${ssrRenderAttr(
                                  "alt",
                                  person.first_name
                                )}${_scopeId4}></div><div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4"${_scopeId4}><div${_scopeId4}><p class="text-sm font-medium text-gray-600 truncate"${_scopeId4}>${ssrInterpolate(person.first_name)}</p><p class="mt-2 flex items-center text-sm text-gray-500"${_scopeId4}><svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId4}><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"${_scopeId4}></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"${_scopeId4}></path></svg><span class="truncate"${_scopeId4}>${ssrInterpolate(person.email)}</span></p></div></div></div><div${_scopeId4}><span class="inline-flex capitalize items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"${_scopeId4}>${ssrInterpolate(person.membership.role)}</span></div></div></a></li>`);
                              });
                              _push5(`<!--]--><li${_scopeId4}><a class="block hover:bg-gray-50"${_scopeId4}><div class="flex items-center px-4 py-4 sm:px-6"${_scopeId4}><div class="min-w-0 flex-1 flex items-center"${_scopeId4}><div class="flex-shrink-0"${_scopeId4}><img class="h-12 w-12 rounded-full"${ssrRenderAttr(
                                "src",
                                $props.team.owner.profile_photo_url
                              )}${ssrRenderAttr(
                                "alt",
                                $props.team.owner.first_name
                              )}${_scopeId4}></div><div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4"${_scopeId4}><div${_scopeId4}><p class="text-sm font-medium text-gray-600 truncate"${_scopeId4}>${ssrInterpolate($props.team.owner.first_name)}</p><p class="mt-2 flex items-center text-sm text-gray-500"${_scopeId4}><svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId4}><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"${_scopeId4}></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"${_scopeId4}></path></svg><span class="truncate"${_scopeId4}>${ssrInterpolate($props.team.owner.email)}</span></p></div></div></div><div${_scopeId4}><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"${_scopeId4}>owner</span></div></div></a></li></ul></div></span>`);
                            } else {
                              _push5(`<!---->`);
                            }
                            if ($options.modelInvitations.length > 0) {
                              _push5(`<div class="pb-5"${_scopeId4}>`);
                              _push5(ssrRenderComponent(_component_jet_modal_form_section, { class: "mt-10 sm:mt-0" }, {
                                form: withCtx((_5, _push6, _parent6, _scopeId5) => {
                                  if (_push6) {
                                    _push6(`<div class="mb-2 mt-5 col-span-12"${_scopeId5}><h2 class="text-lg leading-6 font-medium text-gray-900"${_scopeId5}> Pending Project Invitations </h2><p class="mt-1 text-sm text-gray-500"${_scopeId5}> These people have been invited to your ${ssrInterpolate($props.model)} and have been sent an invitation email. They may join the ${ssrInterpolate($props.model)} by accepting the email invitation. </p></div><div class="space-y-3 col-span-12 mb-3"${_scopeId5}><!--[-->`);
                                    ssrRenderList($options.modelInvitations, (invitation) => {
                                      _push6(`<div class="flex px-4 items-center justify-between"${_scopeId5}><div class="text-gray-600"${_scopeId5}>${ssrInterpolate(invitation.email)}</div><div class="flex items-center"${_scopeId5}><button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"${_scopeId5}> Cancel </button></div></div>`);
                                    });
                                    _push6(`<!--]--></div>`);
                                  } else {
                                    return [
                                      createVNode("div", { class: "mb-2 mt-5 col-span-12" }, [
                                        createVNode("h2", { class: "text-lg leading-6 font-medium text-gray-900" }, " Pending Project Invitations "),
                                        createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " These people have been invited to your " + toDisplayString($props.model) + " and have been sent an invitation email. They may join the " + toDisplayString($props.model) + " by accepting the email invitation. ", 1)
                                      ]),
                                      createVNode("div", { class: "space-y-3 col-span-12 mb-3" }, [
                                        (openBlock(true), createBlock(Fragment, null, renderList($options.modelInvitations, (invitation) => {
                                          return openBlock(), createBlock("div", {
                                            key: invitation.id,
                                            class: "flex px-4 items-center justify-between"
                                          }, [
                                            createVNode("div", { class: "text-gray-600" }, toDisplayString(invitation.email), 1),
                                            createVNode("div", { class: "flex items-center" }, [
                                              createVNode("button", {
                                                class: "cursor-pointer ml-6 text-sm text-red-500 focus:outline-none",
                                                onClick: ($event) => $options.cancelModelInvitation(
                                                  invitation
                                                )
                                              }, " Cancel ", 8, ["onClick"])
                                            ])
                                          ]);
                                        }), 128))
                                      ])
                                    ];
                                  }
                                }),
                                _: 1
                              }, _parent5, _scopeId4));
                              _push5(`</div>`);
                            } else {
                              _push5(`<!---->`);
                            }
                            _push5(`</div>`);
                          } else {
                            _push5(`<div${_scopeId4}>`);
                            _push5(ssrRenderComponent(_component_jet_modal_form_section, {
                              class: "space-y-3",
                              onSubmitted: $options.addModelMember
                            }, {
                              form: withCtx((_5, _push6, _parent6, _scopeId5) => {
                                if (_push6) {
                                  _push6(`<div class="col-span-12"${_scopeId5}><div class="max-w-xl mt-2 text-sm text-gray-600"${_scopeId5}> Please provide the email address of the person you would like to add. </div></div><div class="col-span-12"${_scopeId5}>`);
                                  _push6(ssrRenderComponent(_component_jet_label, {
                                    for: "email",
                                    value: "Email"
                                  }, null, _parent6, _scopeId5));
                                  _push6(ssrRenderComponent(_component_jet_input, {
                                    id: "email",
                                    modelValue: $data.addMemberForm.email,
                                    "onUpdate:modelValue": ($event) => $data.addMemberForm.email = $event,
                                    type: "email",
                                    class: "mt-1 block w-full"
                                  }, null, _parent6, _scopeId5));
                                  _push6(ssrRenderComponent(_component_jet_input_error, {
                                    message: $data.addMemberForm.errors.email,
                                    class: "mt-2"
                                  }, null, _parent6, _scopeId5));
                                  _push6(`</div>`);
                                  if ($props.availableRoles.length > 0) {
                                    _push6(`<div class="col-span-12"${_scopeId5}>`);
                                    _push6(ssrRenderComponent(_component_jet_label, {
                                      for: "roles",
                                      value: "Role"
                                    }, null, _parent6, _scopeId5));
                                    _push6(ssrRenderComponent(_component_jet_input_error, {
                                      message: $data.addMemberForm.errors.role,
                                      class: "mt-2"
                                    }, null, _parent6, _scopeId5));
                                    _push6(`<div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"${_scopeId5}><!--[-->`);
                                    ssrRenderList($props.availableRoles, (role, i) => {
                                      _push6(`<button type="button" class="${ssrRenderClass([{
                                        "border-t border-gray-200 rounded-t-none": i > 0,
                                        "rounded-b-none": i != Object.keys(
                                          $props.availableRoles
                                        ).length - 1
                                      }, "relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"])}"${_scopeId5}><div class="${ssrRenderClass({
                                        "opacity-50": $data.addMemberForm.role && $data.addMemberForm.role != role.key
                                      })}"${_scopeId5}><div class="flex items-center"${_scopeId5}><div class="${ssrRenderClass([{
                                        "font-semibold": $data.addMemberForm.role == role.key
                                      }, "text-sm text-gray-600"])}"${_scopeId5}>${ssrInterpolate(role.name)}</div>`);
                                      if ($data.addMemberForm.role == role.key) {
                                        _push6(`<svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"${_scopeId5}><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId5}></path></svg>`);
                                      } else {
                                        _push6(`<!---->`);
                                      }
                                      _push6(`</div><div class="mt-2 text-xs text-gray-600"${_scopeId5}>${ssrInterpolate(role.description)}</div></div></button>`);
                                    });
                                    _push6(`<!--]--></div></div>`);
                                  } else {
                                    _push6(`<!---->`);
                                  }
                                  _push6(`<div class="col-span-12"${_scopeId5}>`);
                                  _push6(ssrRenderComponent(_component_jet_label, {
                                    for: "message",
                                    value: "Message"
                                  }, null, _parent6, _scopeId5));
                                  _push6(ssrRenderComponent(_component_jet_text_area, {
                                    id: "message",
                                    modelValue: $data.addMemberForm.message,
                                    "onUpdate:modelValue": ($event) => $data.addMemberForm.message = $event,
                                    rows: 3,
                                    class: "mt-1 block w-full"
                                  }, null, _parent6, _scopeId5));
                                  _push6(ssrRenderComponent(_component_jet_input_error, {
                                    message: $data.addMemberForm.errors.message,
                                    class: "mt-2"
                                  }, null, _parent6, _scopeId5));
                                  _push6(`</div>`);
                                } else {
                                  return [
                                    createVNode("div", { class: "col-span-12" }, [
                                      createVNode("div", { class: "max-w-xl mt-2 text-sm text-gray-600" }, " Please provide the email address of the person you would like to add. ")
                                    ]),
                                    createVNode("div", { class: "col-span-12" }, [
                                      createVNode(_component_jet_label, {
                                        for: "email",
                                        value: "Email"
                                      }),
                                      createVNode(_component_jet_input, {
                                        id: "email",
                                        modelValue: $data.addMemberForm.email,
                                        "onUpdate:modelValue": ($event) => $data.addMemberForm.email = $event,
                                        type: "email",
                                        class: "mt-1 block w-full"
                                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                      createVNode(_component_jet_input_error, {
                                        message: $data.addMemberForm.errors.email,
                                        class: "mt-2"
                                      }, null, 8, ["message"])
                                    ]),
                                    $props.availableRoles.length > 0 ? (openBlock(), createBlock("div", {
                                      key: 0,
                                      class: "col-span-12"
                                    }, [
                                      createVNode(_component_jet_label, {
                                        for: "roles",
                                        value: "Role"
                                      }),
                                      createVNode(_component_jet_input_error, {
                                        message: $data.addMemberForm.errors.role,
                                        class: "mt-2"
                                      }, null, 8, ["message"]),
                                      createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer" }, [
                                        (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role, i) => {
                                          return openBlock(), createBlock("button", {
                                            key: role.key,
                                            type: "button",
                                            class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                                              "border-t border-gray-200 rounded-t-none": i > 0,
                                              "rounded-b-none": i != Object.keys(
                                                $props.availableRoles
                                              ).length - 1
                                            }],
                                            onClick: ($event) => $data.addMemberForm.role = role.key
                                          }, [
                                            createVNode("div", {
                                              class: {
                                                "opacity-50": $data.addMemberForm.role && $data.addMemberForm.role != role.key
                                              }
                                            }, [
                                              createVNode("div", { class: "flex items-center" }, [
                                                createVNode("div", {
                                                  class: ["text-sm text-gray-600", {
                                                    "font-semibold": $data.addMemberForm.role == role.key
                                                  }]
                                                }, toDisplayString(role.name), 3),
                                                $data.addMemberForm.role == role.key ? (openBlock(), createBlock("svg", {
                                                  key: 0,
                                                  class: "ml-2 h-5 w-5 text-green-400",
                                                  fill: "none",
                                                  "stroke-linecap": "round",
                                                  "stroke-linejoin": "round",
                                                  "stroke-width": "2",
                                                  stroke: "currentColor",
                                                  viewBox: "0 0 24 24"
                                                }, [
                                                  createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                                ])) : createCommentVNode("", true)
                                              ]),
                                              createVNode("div", { class: "mt-2 text-xs text-gray-600" }, toDisplayString(role.description), 1)
                                            ], 2)
                                          ], 10, ["onClick"]);
                                        }), 128))
                                      ])
                                    ])) : createCommentVNode("", true),
                                    createVNode("div", { class: "col-span-12" }, [
                                      createVNode(_component_jet_label, {
                                        for: "message",
                                        value: "Message"
                                      }),
                                      createVNode(_component_jet_text_area, {
                                        id: "message",
                                        modelValue: $data.addMemberForm.message,
                                        "onUpdate:modelValue": ($event) => $data.addMemberForm.message = $event,
                                        rows: 3,
                                        class: "mt-1 block w-full"
                                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                      createVNode(_component_jet_input_error, {
                                        message: $data.addMemberForm.errors.message,
                                        class: "mt-2"
                                      }, null, 8, ["message"])
                                    ])
                                  ];
                                }
                              }),
                              actions: withCtx((_5, _push6, _parent6, _scopeId5) => {
                                if (_push6) {
                                  _push6(ssrRenderComponent(_component_jet_button, {
                                    class: {
                                      "opacity-25": $data.addMemberForm.processing
                                    },
                                    disabled: $data.addMemberForm.processing
                                  }, {
                                    default: withCtx((_6, _push7, _parent7, _scopeId6) => {
                                      if (_push7) {
                                        _push7(` SEND `);
                                      } else {
                                        return [
                                          createTextVNode(" SEND ")
                                        ];
                                      }
                                    }),
                                    _: 1
                                  }, _parent6, _scopeId5));
                                  _push6(ssrRenderComponent(_component_jet_action_message, {
                                    on: $data.addMemberForm.recentlySuccessful,
                                    class: "mx-3 inline-flex"
                                  }, {
                                    default: withCtx((_6, _push7, _parent7, _scopeId6) => {
                                      if (_push7) {
                                        _push7(` Added. `);
                                      } else {
                                        return [
                                          createTextVNode(" Added. ")
                                        ];
                                      }
                                    }),
                                    _: 1
                                  }, _parent6, _scopeId5));
                                } else {
                                  return [
                                    createVNode(_component_jet_button, {
                                      class: {
                                        "opacity-25": $data.addMemberForm.processing
                                      },
                                      disabled: $data.addMemberForm.processing
                                    }, {
                                      default: withCtx(() => [
                                        createTextVNode(" SEND ")
                                      ]),
                                      _: 1
                                    }, 8, ["class", "disabled"]),
                                    createVNode(_component_jet_action_message, {
                                      on: $data.addMemberForm.recentlySuccessful,
                                      class: "mx-3 inline-flex"
                                    }, {
                                      default: withCtx(() => [
                                        createTextVNode(" Added. ")
                                      ]),
                                      _: 1
                                    }, 8, ["on"])
                                  ];
                                }
                              }),
                              _: 1
                            }, _parent5, _scopeId4));
                            _push5(`</div>`);
                          }
                        } else {
                          return [
                            createVNode("div", { class: "sm:flex sm:items-start sm:justify-between" }, [
                              createVNode("div", null, [
                                createVNode("h3", { class: "text-lg leading-6 font-medium text-gray-900" }, " Share with users "),
                                createVNode("span", { class: "float-center text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                                  createVNode("a", {
                                    href: "https://docs.nmrxiv.org/submission-guides/sharing.html",
                                    target: "_blank"
                                  }, "Learn more about sharing ")
                                ]),
                                createVNode("div", { class: "mt-2 max-w-xl text-sm text-gray-500" })
                              ]),
                              createVNode("div", { class: "mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center" }, [
                                !$setup.addUser && $options.canChangeRole ? (openBlock(), createBlock("button", {
                                  key: 0,
                                  type: "button",
                                  class: "inline-flex items-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none sm:text-sm",
                                  onClick: ($event) => $setup.addUser = true
                                }, [
                                  (openBlock(), createBlock("svg", {
                                    xmlns: "http://www.w3.org/2000/svg",
                                    class: "h-4 w-4",
                                    fill: "none",
                                    viewBox: "0 0 24 24",
                                    stroke: "currentColor",
                                    "stroke-width": "2"
                                  }, [
                                    createVNode("path", {
                                      "stroke-linecap": "round",
                                      "stroke-linejoin": "round",
                                      d: "M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                    })
                                  ])),
                                  createTextVNode("  SHARE ")
                                ], 8, ["onClick"])) : createCommentVNode("", true),
                                $setup.addUser && $options.canChangeRole ? (openBlock(), createBlock("button", {
                                  key: 1,
                                  type: "button",
                                  class: "inline-flex items-center px-4 py-2 border shadow-sm font-medium rounded-md text-dark bg-white-600 hover:bg-white-700 focus:outline-none sm:text-sm",
                                  onClick: ($event) => $setup.addUser = false
                                }, [
                                  (openBlock(), createBlock("svg", {
                                    xmlns: "http://www.w3.org/2000/svg",
                                    class: "h-6 w-6",
                                    fill: "none",
                                    viewBox: "0 0 24 24",
                                    stroke: "currentColor",
                                    "stroke-width": "2"
                                  }, [
                                    createVNode("path", {
                                      "stroke-linecap": "round",
                                      "stroke-linejoin": "round",
                                      d: "M11 17l-5-5m0 0l5-5m-5 5h12"
                                    })
                                  ]))
                                ], 8, ["onClick"])) : createCommentVNode("", true)
                              ])
                            ]),
                            !$setup.addUser ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "flow-root mt-1"
                            }, [
                              $props.members.length > 0 ? (openBlock(), createBlock("ul", {
                                key: 0,
                                role: "list",
                                class: "my-5 shadow divide-y divide-gray-200"
                              }, [
                                (openBlock(true), createBlock(Fragment, null, renderList($props.members, (person) => {
                                  return openBlock(), createBlock("li", {
                                    key: person.email
                                  }, [
                                    createVNode("a", { class: "block" }, [
                                      createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                        createVNode("div", { class: "min-w-0 flex-1 pt-3 flex items-center" }, [
                                          createVNode("div", { class: "flex-shrink-0" }, [
                                            (openBlock(), createBlock("img", {
                                              key: person.id,
                                              class: "h-12 w-12 rounded-full",
                                              src: person.profile_photo_url,
                                              alt: person.first_name
                                            }, null, 8, ["src", "alt"]))
                                          ]),
                                          createVNode("div", { class: "min-w-0 flex-1 px-4" }, [
                                            createVNode("div", null, [
                                              createVNode("p", { class: "text-sm font-medium text-gray-600" }, [
                                                createTextVNode(toDisplayString(person.first_name) + " " + toDisplayString(person.last_name) + " ", 1),
                                                _ctx.$page.props.user.email == person.email ? (openBlock(), createBlock("span", { key: 0 }, "(you)")) : createCommentVNode("", true)
                                              ]),
                                              createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                                (openBlock(), createBlock("svg", {
                                                  class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                  xmlns: "http://www.w3.org/2000/svg",
                                                  viewBox: "0 0 20 20",
                                                  fill: "currentColor",
                                                  "aria-hidden": "true"
                                                }, [
                                                  createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                  createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                                ])),
                                                createVNode("span", null, toDisplayString(person.email), 1)
                                              ])
                                            ])
                                          ])
                                        ]),
                                        createVNode("div", { class: "-mt-4" }, [
                                          $options.canChangeRole && !$options.isProjectAlreadyShared ? (openBlock(), createBlock("div", { key: 0 }, [
                                            $options.personRole(
                                              person
                                            ) && $options.personRole(
                                              person
                                            ) == "creator" ? (openBlock(), createBlock("div", { key: 0 }, [
                                              createVNode("span", { class: "ml-6 text-sm text-dark-500" }, " Creator ")
                                            ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                              createVNode("button", {
                                                class: "cursor-pointer mr-3 text-sm text-red-500 hover:text-red-700",
                                                onClick: ($event) => $options.removeModelMember(
                                                  person
                                                )
                                              }, " Remove ", 8, ["onClick"]),
                                              createVNode(_component_Menu, {
                                                as: "div",
                                                class: "relative inline-block text-left"
                                              }, {
                                                default: withCtx(() => [
                                                  createVNode("div", null, [
                                                    createVNode(_component_MenuButton, { class: "inline-flex justify-center w-full rounded-md capitalize border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" }, {
                                                      default: withCtx(() => [
                                                        createTextVNode(toDisplayString($options.personRole(
                                                          person
                                                        )) + " ", 1),
                                                        createVNode(_component_ChevronDownIcon, {
                                                          class: "-mr-1 ml-2 h-5 w-5",
                                                          "aria-hidden": "true"
                                                        })
                                                      ]),
                                                      _: 2
                                                    }, 1024)
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
                                                            (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role) => {
                                                              return openBlock(), createBlock("span", {
                                                                key: role.key
                                                              }, [
                                                                person[$props.model + "_membership"].role != role.key ? (openBlock(), createBlock(_component_MenuItem, {
                                                                  key: 0,
                                                                  onClick: ($event) => $options.updateRole(
                                                                    role,
                                                                    person
                                                                  )
                                                                }, {
                                                                  default: withCtx(({
                                                                    active
                                                                  }) => [
                                                                    createVNode("a", {
                                                                      href: "#",
                                                                      class: [
                                                                        active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                                                        "block px-4 py-2 text-sm"
                                                                      ]
                                                                    }, toDisplayString(role.name), 3)
                                                                  ]),
                                                                  _: 2
                                                                }, 1032, ["onClick"])) : createCommentVNode("", true)
                                                              ]);
                                                            }), 128))
                                                          ])
                                                        ]),
                                                        _: 2
                                                      }, 1024)
                                                    ]),
                                                    _: 2
                                                  }, 1024)
                                                ]),
                                                _: 2
                                              }, 1024)
                                            ]))
                                          ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                            $options.personRole(person) ? (openBlock(), createBlock("div", { key: 0 }, [
                                              createVNode("span", { class: "ml-6 text-sm capitalize text-dark-500" }, toDisplayString($options.personRole(
                                                person
                                              )), 1)
                                            ])) : createCommentVNode("", true)
                                          ]))
                                        ])
                                      ])
                                    ])
                                  ]);
                                }), 128))
                              ])) : createCommentVNode("", true),
                              $props.team && !$props.team.personal_team ? (openBlock(), createBlock("span", { key: 1 }, [
                                createVNode("div", { class: "mb-2" }, [
                                  createVNode("h2", { class: "text-lg uppercase leading-6 font-medium text-gray-900" }, toDisplayString($props.team.name), 1),
                                  createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " All the users (corresponding roles) in the team will be applicable to this " + toDisplayString($props.model) + ". ", 1)
                                ]),
                                createVNode("div", { class: "bg-white shadow overflow-hidden sm:rounded-md" }, [
                                  createVNode("ul", {
                                    role: "list",
                                    class: "divide-y divide-gray-200"
                                  }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($props.team.users, (person) => {
                                      return openBlock(), createBlock("li", {
                                        key: person.email
                                      }, [
                                        createVNode("a", { class: "block hover:bg-gray-50" }, [
                                          createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                            createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                              createVNode("div", { class: "flex-shrink-0" }, [
                                                (openBlock(), createBlock("img", {
                                                  key: person.id,
                                                  class: "h-12 w-12 rounded-full",
                                                  src: person.profile_photo_url,
                                                  alt: person.first_name
                                                }, null, 8, ["src", "alt"]))
                                              ]),
                                              createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                                createVNode("div", null, [
                                                  createVNode("p", { class: "text-sm font-medium text-gray-600 truncate" }, toDisplayString(person.first_name), 1),
                                                  createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                                    (openBlock(), createBlock("svg", {
                                                      class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                      xmlns: "http://www.w3.org/2000/svg",
                                                      viewBox: "0 0 20 20",
                                                      fill: "currentColor",
                                                      "aria-hidden": "true"
                                                    }, [
                                                      createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                      createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                                    ])),
                                                    createVNode("span", { class: "truncate" }, toDisplayString(person.email), 1)
                                                  ])
                                                ])
                                              ])
                                            ]),
                                            createVNode("div", null, [
                                              createVNode("span", { class: "inline-flex capitalize items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, toDisplayString(person.membership.role), 1)
                                            ])
                                          ])
                                        ])
                                      ]);
                                    }), 128)),
                                    createVNode("li", null, [
                                      createVNode("a", { class: "block hover:bg-gray-50" }, [
                                        createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                          createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                            createVNode("div", { class: "flex-shrink-0" }, [
                                              (openBlock(), createBlock("img", {
                                                key: $props.team.owner.id,
                                                class: "h-12 w-12 rounded-full",
                                                src: $props.team.owner.profile_photo_url,
                                                alt: $props.team.owner.first_name
                                              }, null, 8, ["src", "alt"]))
                                            ]),
                                            createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                              createVNode("div", null, [
                                                createVNode("p", { class: "text-sm font-medium text-gray-600 truncate" }, toDisplayString($props.team.owner.first_name), 1),
                                                createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                                  (openBlock(), createBlock("svg", {
                                                    class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                    xmlns: "http://www.w3.org/2000/svg",
                                                    viewBox: "0 0 20 20",
                                                    fill: "currentColor",
                                                    "aria-hidden": "true"
                                                  }, [
                                                    createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                    createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                                  ])),
                                                  createVNode("span", { class: "truncate" }, toDisplayString($props.team.owner.email), 1)
                                                ])
                                              ])
                                            ])
                                          ]),
                                          createVNode("div", null, [
                                            createVNode("span", { class: "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, "owner")
                                          ])
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])) : createCommentVNode("", true),
                              $options.modelInvitations.length > 0 ? (openBlock(), createBlock("div", {
                                key: 2,
                                class: "pb-5"
                              }, [
                                createVNode(_component_jet_modal_form_section, { class: "mt-10 sm:mt-0" }, {
                                  form: withCtx(() => [
                                    createVNode("div", { class: "mb-2 mt-5 col-span-12" }, [
                                      createVNode("h2", { class: "text-lg leading-6 font-medium text-gray-900" }, " Pending Project Invitations "),
                                      createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " These people have been invited to your " + toDisplayString($props.model) + " and have been sent an invitation email. They may join the " + toDisplayString($props.model) + " by accepting the email invitation. ", 1)
                                    ]),
                                    createVNode("div", { class: "space-y-3 col-span-12 mb-3" }, [
                                      (openBlock(true), createBlock(Fragment, null, renderList($options.modelInvitations, (invitation) => {
                                        return openBlock(), createBlock("div", {
                                          key: invitation.id,
                                          class: "flex px-4 items-center justify-between"
                                        }, [
                                          createVNode("div", { class: "text-gray-600" }, toDisplayString(invitation.email), 1),
                                          createVNode("div", { class: "flex items-center" }, [
                                            createVNode("button", {
                                              class: "cursor-pointer ml-6 text-sm text-red-500 focus:outline-none",
                                              onClick: ($event) => $options.cancelModelInvitation(
                                                invitation
                                              )
                                            }, " Cancel ", 8, ["onClick"])
                                          ])
                                        ]);
                                      }), 128))
                                    ])
                                  ]),
                                  _: 1
                                })
                              ])) : createCommentVNode("", true)
                            ])) : (openBlock(), createBlock("div", { key: 1 }, [
                              createVNode(_component_jet_modal_form_section, {
                                class: "space-y-3",
                                onSubmitted: $options.addModelMember
                              }, {
                                form: withCtx(() => [
                                  createVNode("div", { class: "col-span-12" }, [
                                    createVNode("div", { class: "max-w-xl mt-2 text-sm text-gray-600" }, " Please provide the email address of the person you would like to add. ")
                                  ]),
                                  createVNode("div", { class: "col-span-12" }, [
                                    createVNode(_component_jet_label, {
                                      for: "email",
                                      value: "Email"
                                    }),
                                    createVNode(_component_jet_input, {
                                      id: "email",
                                      modelValue: $data.addMemberForm.email,
                                      "onUpdate:modelValue": ($event) => $data.addMemberForm.email = $event,
                                      type: "email",
                                      class: "mt-1 block w-full"
                                    }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                    createVNode(_component_jet_input_error, {
                                      message: $data.addMemberForm.errors.email,
                                      class: "mt-2"
                                    }, null, 8, ["message"])
                                  ]),
                                  $props.availableRoles.length > 0 ? (openBlock(), createBlock("div", {
                                    key: 0,
                                    class: "col-span-12"
                                  }, [
                                    createVNode(_component_jet_label, {
                                      for: "roles",
                                      value: "Role"
                                    }),
                                    createVNode(_component_jet_input_error, {
                                      message: $data.addMemberForm.errors.role,
                                      class: "mt-2"
                                    }, null, 8, ["message"]),
                                    createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer" }, [
                                      (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role, i) => {
                                        return openBlock(), createBlock("button", {
                                          key: role.key,
                                          type: "button",
                                          class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                                            "border-t border-gray-200 rounded-t-none": i > 0,
                                            "rounded-b-none": i != Object.keys(
                                              $props.availableRoles
                                            ).length - 1
                                          }],
                                          onClick: ($event) => $data.addMemberForm.role = role.key
                                        }, [
                                          createVNode("div", {
                                            class: {
                                              "opacity-50": $data.addMemberForm.role && $data.addMemberForm.role != role.key
                                            }
                                          }, [
                                            createVNode("div", { class: "flex items-center" }, [
                                              createVNode("div", {
                                                class: ["text-sm text-gray-600", {
                                                  "font-semibold": $data.addMemberForm.role == role.key
                                                }]
                                              }, toDisplayString(role.name), 3),
                                              $data.addMemberForm.role == role.key ? (openBlock(), createBlock("svg", {
                                                key: 0,
                                                class: "ml-2 h-5 w-5 text-green-400",
                                                fill: "none",
                                                "stroke-linecap": "round",
                                                "stroke-linejoin": "round",
                                                "stroke-width": "2",
                                                stroke: "currentColor",
                                                viewBox: "0 0 24 24"
                                              }, [
                                                createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                              ])) : createCommentVNode("", true)
                                            ]),
                                            createVNode("div", { class: "mt-2 text-xs text-gray-600" }, toDisplayString(role.description), 1)
                                          ], 2)
                                        ], 10, ["onClick"]);
                                      }), 128))
                                    ])
                                  ])) : createCommentVNode("", true),
                                  createVNode("div", { class: "col-span-12" }, [
                                    createVNode(_component_jet_label, {
                                      for: "message",
                                      value: "Message"
                                    }),
                                    createVNode(_component_jet_text_area, {
                                      id: "message",
                                      modelValue: $data.addMemberForm.message,
                                      "onUpdate:modelValue": ($event) => $data.addMemberForm.message = $event,
                                      rows: 3,
                                      class: "mt-1 block w-full"
                                    }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                    createVNode(_component_jet_input_error, {
                                      message: $data.addMemberForm.errors.message,
                                      class: "mt-2"
                                    }, null, 8, ["message"])
                                  ])
                                ]),
                                actions: withCtx(() => [
                                  createVNode(_component_jet_button, {
                                    class: {
                                      "opacity-25": $data.addMemberForm.processing
                                    },
                                    disabled: $data.addMemberForm.processing
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(" SEND ")
                                    ]),
                                    _: 1
                                  }, 8, ["class", "disabled"]),
                                  createVNode(_component_jet_action_message, {
                                    on: $data.addMemberForm.recentlySuccessful,
                                    class: "mx-3 inline-flex"
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(" Added. ")
                                    ]),
                                    _: 1
                                  }, 8, ["on"])
                                ]),
                                _: 1
                              }, 8, ["onSubmitted"])
                            ]))
                          ];
                        }
                      }),
                      _: 1
                    }, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode(_component_DialogPanel, { class: "mx-auto max-w-xl transform rounded-xl bg-white p-2 shadow-2xl ring-1 ring-black ring-opacity-5 transition-all p-4" }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "sm:flex sm:items-start sm:justify-between" }, [
                            createVNode("div", null, [
                              createVNode("h3", { class: "text-lg leading-6 font-medium text-gray-900" }, " Share with users "),
                              createVNode("span", { class: "float-center text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                                createVNode("a", {
                                  href: "https://docs.nmrxiv.org/submission-guides/sharing.html",
                                  target: "_blank"
                                }, "Learn more about sharing ")
                              ]),
                              createVNode("div", { class: "mt-2 max-w-xl text-sm text-gray-500" })
                            ]),
                            createVNode("div", { class: "mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center" }, [
                              !$setup.addUser && $options.canChangeRole ? (openBlock(), createBlock("button", {
                                key: 0,
                                type: "button",
                                class: "inline-flex items-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none sm:text-sm",
                                onClick: ($event) => $setup.addUser = true
                              }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  class: "h-4 w-4",
                                  fill: "none",
                                  viewBox: "0 0 24 24",
                                  stroke: "currentColor",
                                  "stroke-width": "2"
                                }, [
                                  createVNode("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    d: "M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                  })
                                ])),
                                createTextVNode("  SHARE ")
                              ], 8, ["onClick"])) : createCommentVNode("", true),
                              $setup.addUser && $options.canChangeRole ? (openBlock(), createBlock("button", {
                                key: 1,
                                type: "button",
                                class: "inline-flex items-center px-4 py-2 border shadow-sm font-medium rounded-md text-dark bg-white-600 hover:bg-white-700 focus:outline-none sm:text-sm",
                                onClick: ($event) => $setup.addUser = false
                              }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  class: "h-6 w-6",
                                  fill: "none",
                                  viewBox: "0 0 24 24",
                                  stroke: "currentColor",
                                  "stroke-width": "2"
                                }, [
                                  createVNode("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    d: "M11 17l-5-5m0 0l5-5m-5 5h12"
                                  })
                                ]))
                              ], 8, ["onClick"])) : createCommentVNode("", true)
                            ])
                          ]),
                          !$setup.addUser ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "flow-root mt-1"
                          }, [
                            $props.members.length > 0 ? (openBlock(), createBlock("ul", {
                              key: 0,
                              role: "list",
                              class: "my-5 shadow divide-y divide-gray-200"
                            }, [
                              (openBlock(true), createBlock(Fragment, null, renderList($props.members, (person) => {
                                return openBlock(), createBlock("li", {
                                  key: person.email
                                }, [
                                  createVNode("a", { class: "block" }, [
                                    createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                      createVNode("div", { class: "min-w-0 flex-1 pt-3 flex items-center" }, [
                                        createVNode("div", { class: "flex-shrink-0" }, [
                                          (openBlock(), createBlock("img", {
                                            key: person.id,
                                            class: "h-12 w-12 rounded-full",
                                            src: person.profile_photo_url,
                                            alt: person.first_name
                                          }, null, 8, ["src", "alt"]))
                                        ]),
                                        createVNode("div", { class: "min-w-0 flex-1 px-4" }, [
                                          createVNode("div", null, [
                                            createVNode("p", { class: "text-sm font-medium text-gray-600" }, [
                                              createTextVNode(toDisplayString(person.first_name) + " " + toDisplayString(person.last_name) + " ", 1),
                                              _ctx.$page.props.user.email == person.email ? (openBlock(), createBlock("span", { key: 0 }, "(you)")) : createCommentVNode("", true)
                                            ]),
                                            createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                              (openBlock(), createBlock("svg", {
                                                class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                xmlns: "http://www.w3.org/2000/svg",
                                                viewBox: "0 0 20 20",
                                                fill: "currentColor",
                                                "aria-hidden": "true"
                                              }, [
                                                createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                              ])),
                                              createVNode("span", null, toDisplayString(person.email), 1)
                                            ])
                                          ])
                                        ])
                                      ]),
                                      createVNode("div", { class: "-mt-4" }, [
                                        $options.canChangeRole && !$options.isProjectAlreadyShared ? (openBlock(), createBlock("div", { key: 0 }, [
                                          $options.personRole(
                                            person
                                          ) && $options.personRole(
                                            person
                                          ) == "creator" ? (openBlock(), createBlock("div", { key: 0 }, [
                                            createVNode("span", { class: "ml-6 text-sm text-dark-500" }, " Creator ")
                                          ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                            createVNode("button", {
                                              class: "cursor-pointer mr-3 text-sm text-red-500 hover:text-red-700",
                                              onClick: ($event) => $options.removeModelMember(
                                                person
                                              )
                                            }, " Remove ", 8, ["onClick"]),
                                            createVNode(_component_Menu, {
                                              as: "div",
                                              class: "relative inline-block text-left"
                                            }, {
                                              default: withCtx(() => [
                                                createVNode("div", null, [
                                                  createVNode(_component_MenuButton, { class: "inline-flex justify-center w-full rounded-md capitalize border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" }, {
                                                    default: withCtx(() => [
                                                      createTextVNode(toDisplayString($options.personRole(
                                                        person
                                                      )) + " ", 1),
                                                      createVNode(_component_ChevronDownIcon, {
                                                        class: "-mr-1 ml-2 h-5 w-5",
                                                        "aria-hidden": "true"
                                                      })
                                                    ]),
                                                    _: 2
                                                  }, 1024)
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
                                                          (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role) => {
                                                            return openBlock(), createBlock("span", {
                                                              key: role.key
                                                            }, [
                                                              person[$props.model + "_membership"].role != role.key ? (openBlock(), createBlock(_component_MenuItem, {
                                                                key: 0,
                                                                onClick: ($event) => $options.updateRole(
                                                                  role,
                                                                  person
                                                                )
                                                              }, {
                                                                default: withCtx(({
                                                                  active
                                                                }) => [
                                                                  createVNode("a", {
                                                                    href: "#",
                                                                    class: [
                                                                      active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                                                      "block px-4 py-2 text-sm"
                                                                    ]
                                                                  }, toDisplayString(role.name), 3)
                                                                ]),
                                                                _: 2
                                                              }, 1032, ["onClick"])) : createCommentVNode("", true)
                                                            ]);
                                                          }), 128))
                                                        ])
                                                      ]),
                                                      _: 2
                                                    }, 1024)
                                                  ]),
                                                  _: 2
                                                }, 1024)
                                              ]),
                                              _: 2
                                            }, 1024)
                                          ]))
                                        ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                          $options.personRole(person) ? (openBlock(), createBlock("div", { key: 0 }, [
                                            createVNode("span", { class: "ml-6 text-sm capitalize text-dark-500" }, toDisplayString($options.personRole(
                                              person
                                            )), 1)
                                          ])) : createCommentVNode("", true)
                                        ]))
                                      ])
                                    ])
                                  ])
                                ]);
                              }), 128))
                            ])) : createCommentVNode("", true),
                            $props.team && !$props.team.personal_team ? (openBlock(), createBlock("span", { key: 1 }, [
                              createVNode("div", { class: "mb-2" }, [
                                createVNode("h2", { class: "text-lg uppercase leading-6 font-medium text-gray-900" }, toDisplayString($props.team.name), 1),
                                createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " All the users (corresponding roles) in the team will be applicable to this " + toDisplayString($props.model) + ". ", 1)
                              ]),
                              createVNode("div", { class: "bg-white shadow overflow-hidden sm:rounded-md" }, [
                                createVNode("ul", {
                                  role: "list",
                                  class: "divide-y divide-gray-200"
                                }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList($props.team.users, (person) => {
                                    return openBlock(), createBlock("li", {
                                      key: person.email
                                    }, [
                                      createVNode("a", { class: "block hover:bg-gray-50" }, [
                                        createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                          createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                            createVNode("div", { class: "flex-shrink-0" }, [
                                              (openBlock(), createBlock("img", {
                                                key: person.id,
                                                class: "h-12 w-12 rounded-full",
                                                src: person.profile_photo_url,
                                                alt: person.first_name
                                              }, null, 8, ["src", "alt"]))
                                            ]),
                                            createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                              createVNode("div", null, [
                                                createVNode("p", { class: "text-sm font-medium text-gray-600 truncate" }, toDisplayString(person.first_name), 1),
                                                createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                                  (openBlock(), createBlock("svg", {
                                                    class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                    xmlns: "http://www.w3.org/2000/svg",
                                                    viewBox: "0 0 20 20",
                                                    fill: "currentColor",
                                                    "aria-hidden": "true"
                                                  }, [
                                                    createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                    createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                                  ])),
                                                  createVNode("span", { class: "truncate" }, toDisplayString(person.email), 1)
                                                ])
                                              ])
                                            ])
                                          ]),
                                          createVNode("div", null, [
                                            createVNode("span", { class: "inline-flex capitalize items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, toDisplayString(person.membership.role), 1)
                                          ])
                                        ])
                                      ])
                                    ]);
                                  }), 128)),
                                  createVNode("li", null, [
                                    createVNode("a", { class: "block hover:bg-gray-50" }, [
                                      createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                        createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                          createVNode("div", { class: "flex-shrink-0" }, [
                                            (openBlock(), createBlock("img", {
                                              key: $props.team.owner.id,
                                              class: "h-12 w-12 rounded-full",
                                              src: $props.team.owner.profile_photo_url,
                                              alt: $props.team.owner.first_name
                                            }, null, 8, ["src", "alt"]))
                                          ]),
                                          createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                            createVNode("div", null, [
                                              createVNode("p", { class: "text-sm font-medium text-gray-600 truncate" }, toDisplayString($props.team.owner.first_name), 1),
                                              createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                                (openBlock(), createBlock("svg", {
                                                  class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                  xmlns: "http://www.w3.org/2000/svg",
                                                  viewBox: "0 0 20 20",
                                                  fill: "currentColor",
                                                  "aria-hidden": "true"
                                                }, [
                                                  createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                  createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                                ])),
                                                createVNode("span", { class: "truncate" }, toDisplayString($props.team.owner.email), 1)
                                              ])
                                            ])
                                          ])
                                        ]),
                                        createVNode("div", null, [
                                          createVNode("span", { class: "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, "owner")
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            $options.modelInvitations.length > 0 ? (openBlock(), createBlock("div", {
                              key: 2,
                              class: "pb-5"
                            }, [
                              createVNode(_component_jet_modal_form_section, { class: "mt-10 sm:mt-0" }, {
                                form: withCtx(() => [
                                  createVNode("div", { class: "mb-2 mt-5 col-span-12" }, [
                                    createVNode("h2", { class: "text-lg leading-6 font-medium text-gray-900" }, " Pending Project Invitations "),
                                    createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " These people have been invited to your " + toDisplayString($props.model) + " and have been sent an invitation email. They may join the " + toDisplayString($props.model) + " by accepting the email invitation. ", 1)
                                  ]),
                                  createVNode("div", { class: "space-y-3 col-span-12 mb-3" }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($options.modelInvitations, (invitation) => {
                                      return openBlock(), createBlock("div", {
                                        key: invitation.id,
                                        class: "flex px-4 items-center justify-between"
                                      }, [
                                        createVNode("div", { class: "text-gray-600" }, toDisplayString(invitation.email), 1),
                                        createVNode("div", { class: "flex items-center" }, [
                                          createVNode("button", {
                                            class: "cursor-pointer ml-6 text-sm text-red-500 focus:outline-none",
                                            onClick: ($event) => $options.cancelModelInvitation(
                                              invitation
                                            )
                                          }, " Cancel ", 8, ["onClick"])
                                        ])
                                      ]);
                                    }), 128))
                                  ])
                                ]),
                                _: 1
                              })
                            ])) : createCommentVNode("", true)
                          ])) : (openBlock(), createBlock("div", { key: 1 }, [
                            createVNode(_component_jet_modal_form_section, {
                              class: "space-y-3",
                              onSubmitted: $options.addModelMember
                            }, {
                              form: withCtx(() => [
                                createVNode("div", { class: "col-span-12" }, [
                                  createVNode("div", { class: "max-w-xl mt-2 text-sm text-gray-600" }, " Please provide the email address of the person you would like to add. ")
                                ]),
                                createVNode("div", { class: "col-span-12" }, [
                                  createVNode(_component_jet_label, {
                                    for: "email",
                                    value: "Email"
                                  }),
                                  createVNode(_component_jet_input, {
                                    id: "email",
                                    modelValue: $data.addMemberForm.email,
                                    "onUpdate:modelValue": ($event) => $data.addMemberForm.email = $event,
                                    type: "email",
                                    class: "mt-1 block w-full"
                                  }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                  createVNode(_component_jet_input_error, {
                                    message: $data.addMemberForm.errors.email,
                                    class: "mt-2"
                                  }, null, 8, ["message"])
                                ]),
                                $props.availableRoles.length > 0 ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "col-span-12"
                                }, [
                                  createVNode(_component_jet_label, {
                                    for: "roles",
                                    value: "Role"
                                  }),
                                  createVNode(_component_jet_input_error, {
                                    message: $data.addMemberForm.errors.role,
                                    class: "mt-2"
                                  }, null, 8, ["message"]),
                                  createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer" }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role, i) => {
                                      return openBlock(), createBlock("button", {
                                        key: role.key,
                                        type: "button",
                                        class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                                          "border-t border-gray-200 rounded-t-none": i > 0,
                                          "rounded-b-none": i != Object.keys(
                                            $props.availableRoles
                                          ).length - 1
                                        }],
                                        onClick: ($event) => $data.addMemberForm.role = role.key
                                      }, [
                                        createVNode("div", {
                                          class: {
                                            "opacity-50": $data.addMemberForm.role && $data.addMemberForm.role != role.key
                                          }
                                        }, [
                                          createVNode("div", { class: "flex items-center" }, [
                                            createVNode("div", {
                                              class: ["text-sm text-gray-600", {
                                                "font-semibold": $data.addMemberForm.role == role.key
                                              }]
                                            }, toDisplayString(role.name), 3),
                                            $data.addMemberForm.role == role.key ? (openBlock(), createBlock("svg", {
                                              key: 0,
                                              class: "ml-2 h-5 w-5 text-green-400",
                                              fill: "none",
                                              "stroke-linecap": "round",
                                              "stroke-linejoin": "round",
                                              "stroke-width": "2",
                                              stroke: "currentColor",
                                              viewBox: "0 0 24 24"
                                            }, [
                                              createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                            ])) : createCommentVNode("", true)
                                          ]),
                                          createVNode("div", { class: "mt-2 text-xs text-gray-600" }, toDisplayString(role.description), 1)
                                        ], 2)
                                      ], 10, ["onClick"]);
                                    }), 128))
                                  ])
                                ])) : createCommentVNode("", true),
                                createVNode("div", { class: "col-span-12" }, [
                                  createVNode(_component_jet_label, {
                                    for: "message",
                                    value: "Message"
                                  }),
                                  createVNode(_component_jet_text_area, {
                                    id: "message",
                                    modelValue: $data.addMemberForm.message,
                                    "onUpdate:modelValue": ($event) => $data.addMemberForm.message = $event,
                                    rows: 3,
                                    class: "mt-1 block w-full"
                                  }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                  createVNode(_component_jet_input_error, {
                                    message: $data.addMemberForm.errors.message,
                                    class: "mt-2"
                                  }, null, 8, ["message"])
                                ])
                              ]),
                              actions: withCtx(() => [
                                createVNode(_component_jet_button, {
                                  class: {
                                    "opacity-25": $data.addMemberForm.processing
                                  },
                                  disabled: $data.addMemberForm.processing
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" SEND ")
                                  ]),
                                  _: 1
                                }, 8, ["class", "disabled"]),
                                createVNode(_component_jet_action_message, {
                                  on: $data.addMemberForm.recentlySuccessful,
                                  class: "mx-3 inline-flex"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" Added. ")
                                  ]),
                                  _: 1
                                }, 8, ["on"])
                              ]),
                              _: 1
                            }, 8, ["onSubmitted"])
                          ]))
                        ]),
                        _: 1
                      })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`</div>`);
            } else {
              return [
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "ease-out duration-300",
                  "enter-from": "opacity-0",
                  "enter-to": "opacity-100",
                  leave: "ease-in duration-200",
                  "leave-from": "opacity-100",
                  "leave-to": "opacity-0"
                }, {
                  default: withCtx(() => [
                    createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                  ]),
                  _: 1
                }),
                createVNode("div", { class: "fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20" }, [
                  createVNode(_component_TransitionChild, {
                    as: "template",
                    enter: "ease-out duration-300",
                    "enter-from": "opacity-0 scale-95",
                    "enter-to": "opacity-100 scale-100",
                    leave: "ease-in duration-200",
                    "leave-from": "opacity-100 scale-100",
                    "leave-to": "opacity-0 scale-95"
                  }, {
                    default: withCtx(() => [
                      createVNode(_component_DialogPanel, { class: "mx-auto max-w-xl transform rounded-xl bg-white p-2 shadow-2xl ring-1 ring-black ring-opacity-5 transition-all p-4" }, {
                        default: withCtx(() => [
                          createVNode("div", { class: "sm:flex sm:items-start sm:justify-between" }, [
                            createVNode("div", null, [
                              createVNode("h3", { class: "text-lg leading-6 font-medium text-gray-900" }, " Share with users "),
                              createVNode("span", { class: "float-center text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                                createVNode("a", {
                                  href: "https://docs.nmrxiv.org/submission-guides/sharing.html",
                                  target: "_blank"
                                }, "Learn more about sharing ")
                              ]),
                              createVNode("div", { class: "mt-2 max-w-xl text-sm text-gray-500" })
                            ]),
                            createVNode("div", { class: "mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center" }, [
                              !$setup.addUser && $options.canChangeRole ? (openBlock(), createBlock("button", {
                                key: 0,
                                type: "button",
                                class: "inline-flex items-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none sm:text-sm",
                                onClick: ($event) => $setup.addUser = true
                              }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  class: "h-4 w-4",
                                  fill: "none",
                                  viewBox: "0 0 24 24",
                                  stroke: "currentColor",
                                  "stroke-width": "2"
                                }, [
                                  createVNode("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    d: "M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                  })
                                ])),
                                createTextVNode("  SHARE ")
                              ], 8, ["onClick"])) : createCommentVNode("", true),
                              $setup.addUser && $options.canChangeRole ? (openBlock(), createBlock("button", {
                                key: 1,
                                type: "button",
                                class: "inline-flex items-center px-4 py-2 border shadow-sm font-medium rounded-md text-dark bg-white-600 hover:bg-white-700 focus:outline-none sm:text-sm",
                                onClick: ($event) => $setup.addUser = false
                              }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  class: "h-6 w-6",
                                  fill: "none",
                                  viewBox: "0 0 24 24",
                                  stroke: "currentColor",
                                  "stroke-width": "2"
                                }, [
                                  createVNode("path", {
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    d: "M11 17l-5-5m0 0l5-5m-5 5h12"
                                  })
                                ]))
                              ], 8, ["onClick"])) : createCommentVNode("", true)
                            ])
                          ]),
                          !$setup.addUser ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "flow-root mt-1"
                          }, [
                            $props.members.length > 0 ? (openBlock(), createBlock("ul", {
                              key: 0,
                              role: "list",
                              class: "my-5 shadow divide-y divide-gray-200"
                            }, [
                              (openBlock(true), createBlock(Fragment, null, renderList($props.members, (person) => {
                                return openBlock(), createBlock("li", {
                                  key: person.email
                                }, [
                                  createVNode("a", { class: "block" }, [
                                    createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                      createVNode("div", { class: "min-w-0 flex-1 pt-3 flex items-center" }, [
                                        createVNode("div", { class: "flex-shrink-0" }, [
                                          (openBlock(), createBlock("img", {
                                            key: person.id,
                                            class: "h-12 w-12 rounded-full",
                                            src: person.profile_photo_url,
                                            alt: person.first_name
                                          }, null, 8, ["src", "alt"]))
                                        ]),
                                        createVNode("div", { class: "min-w-0 flex-1 px-4" }, [
                                          createVNode("div", null, [
                                            createVNode("p", { class: "text-sm font-medium text-gray-600" }, [
                                              createTextVNode(toDisplayString(person.first_name) + " " + toDisplayString(person.last_name) + " ", 1),
                                              _ctx.$page.props.user.email == person.email ? (openBlock(), createBlock("span", { key: 0 }, "(you)")) : createCommentVNode("", true)
                                            ]),
                                            createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                              (openBlock(), createBlock("svg", {
                                                class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                xmlns: "http://www.w3.org/2000/svg",
                                                viewBox: "0 0 20 20",
                                                fill: "currentColor",
                                                "aria-hidden": "true"
                                              }, [
                                                createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                              ])),
                                              createVNode("span", null, toDisplayString(person.email), 1)
                                            ])
                                          ])
                                        ])
                                      ]),
                                      createVNode("div", { class: "-mt-4" }, [
                                        $options.canChangeRole && !$options.isProjectAlreadyShared ? (openBlock(), createBlock("div", { key: 0 }, [
                                          $options.personRole(
                                            person
                                          ) && $options.personRole(
                                            person
                                          ) == "creator" ? (openBlock(), createBlock("div", { key: 0 }, [
                                            createVNode("span", { class: "ml-6 text-sm text-dark-500" }, " Creator ")
                                          ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                            createVNode("button", {
                                              class: "cursor-pointer mr-3 text-sm text-red-500 hover:text-red-700",
                                              onClick: ($event) => $options.removeModelMember(
                                                person
                                              )
                                            }, " Remove ", 8, ["onClick"]),
                                            createVNode(_component_Menu, {
                                              as: "div",
                                              class: "relative inline-block text-left"
                                            }, {
                                              default: withCtx(() => [
                                                createVNode("div", null, [
                                                  createVNode(_component_MenuButton, { class: "inline-flex justify-center w-full rounded-md capitalize border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" }, {
                                                    default: withCtx(() => [
                                                      createTextVNode(toDisplayString($options.personRole(
                                                        person
                                                      )) + " ", 1),
                                                      createVNode(_component_ChevronDownIcon, {
                                                        class: "-mr-1 ml-2 h-5 w-5",
                                                        "aria-hidden": "true"
                                                      })
                                                    ]),
                                                    _: 2
                                                  }, 1024)
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
                                                          (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role) => {
                                                            return openBlock(), createBlock("span", {
                                                              key: role.key
                                                            }, [
                                                              person[$props.model + "_membership"].role != role.key ? (openBlock(), createBlock(_component_MenuItem, {
                                                                key: 0,
                                                                onClick: ($event) => $options.updateRole(
                                                                  role,
                                                                  person
                                                                )
                                                              }, {
                                                                default: withCtx(({
                                                                  active
                                                                }) => [
                                                                  createVNode("a", {
                                                                    href: "#",
                                                                    class: [
                                                                      active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                                                      "block px-4 py-2 text-sm"
                                                                    ]
                                                                  }, toDisplayString(role.name), 3)
                                                                ]),
                                                                _: 2
                                                              }, 1032, ["onClick"])) : createCommentVNode("", true)
                                                            ]);
                                                          }), 128))
                                                        ])
                                                      ]),
                                                      _: 2
                                                    }, 1024)
                                                  ]),
                                                  _: 2
                                                }, 1024)
                                              ]),
                                              _: 2
                                            }, 1024)
                                          ]))
                                        ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                          $options.personRole(person) ? (openBlock(), createBlock("div", { key: 0 }, [
                                            createVNode("span", { class: "ml-6 text-sm capitalize text-dark-500" }, toDisplayString($options.personRole(
                                              person
                                            )), 1)
                                          ])) : createCommentVNode("", true)
                                        ]))
                                      ])
                                    ])
                                  ])
                                ]);
                              }), 128))
                            ])) : createCommentVNode("", true),
                            $props.team && !$props.team.personal_team ? (openBlock(), createBlock("span", { key: 1 }, [
                              createVNode("div", { class: "mb-2" }, [
                                createVNode("h2", { class: "text-lg uppercase leading-6 font-medium text-gray-900" }, toDisplayString($props.team.name), 1),
                                createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " All the users (corresponding roles) in the team will be applicable to this " + toDisplayString($props.model) + ". ", 1)
                              ]),
                              createVNode("div", { class: "bg-white shadow overflow-hidden sm:rounded-md" }, [
                                createVNode("ul", {
                                  role: "list",
                                  class: "divide-y divide-gray-200"
                                }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList($props.team.users, (person) => {
                                    return openBlock(), createBlock("li", {
                                      key: person.email
                                    }, [
                                      createVNode("a", { class: "block hover:bg-gray-50" }, [
                                        createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                          createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                            createVNode("div", { class: "flex-shrink-0" }, [
                                              (openBlock(), createBlock("img", {
                                                key: person.id,
                                                class: "h-12 w-12 rounded-full",
                                                src: person.profile_photo_url,
                                                alt: person.first_name
                                              }, null, 8, ["src", "alt"]))
                                            ]),
                                            createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                              createVNode("div", null, [
                                                createVNode("p", { class: "text-sm font-medium text-gray-600 truncate" }, toDisplayString(person.first_name), 1),
                                                createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                                  (openBlock(), createBlock("svg", {
                                                    class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                    xmlns: "http://www.w3.org/2000/svg",
                                                    viewBox: "0 0 20 20",
                                                    fill: "currentColor",
                                                    "aria-hidden": "true"
                                                  }, [
                                                    createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                    createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                                  ])),
                                                  createVNode("span", { class: "truncate" }, toDisplayString(person.email), 1)
                                                ])
                                              ])
                                            ])
                                          ]),
                                          createVNode("div", null, [
                                            createVNode("span", { class: "inline-flex capitalize items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, toDisplayString(person.membership.role), 1)
                                          ])
                                        ])
                                      ])
                                    ]);
                                  }), 128)),
                                  createVNode("li", null, [
                                    createVNode("a", { class: "block hover:bg-gray-50" }, [
                                      createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                        createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                          createVNode("div", { class: "flex-shrink-0" }, [
                                            (openBlock(), createBlock("img", {
                                              key: $props.team.owner.id,
                                              class: "h-12 w-12 rounded-full",
                                              src: $props.team.owner.profile_photo_url,
                                              alt: $props.team.owner.first_name
                                            }, null, 8, ["src", "alt"]))
                                          ]),
                                          createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                            createVNode("div", null, [
                                              createVNode("p", { class: "text-sm font-medium text-gray-600 truncate" }, toDisplayString($props.team.owner.first_name), 1),
                                              createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                                (openBlock(), createBlock("svg", {
                                                  class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                  xmlns: "http://www.w3.org/2000/svg",
                                                  viewBox: "0 0 20 20",
                                                  fill: "currentColor",
                                                  "aria-hidden": "true"
                                                }, [
                                                  createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                  createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                                ])),
                                                createVNode("span", { class: "truncate" }, toDisplayString($props.team.owner.email), 1)
                                              ])
                                            ])
                                          ])
                                        ]),
                                        createVNode("div", null, [
                                          createVNode("span", { class: "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, "owner")
                                        ])
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])) : createCommentVNode("", true),
                            $options.modelInvitations.length > 0 ? (openBlock(), createBlock("div", {
                              key: 2,
                              class: "pb-5"
                            }, [
                              createVNode(_component_jet_modal_form_section, { class: "mt-10 sm:mt-0" }, {
                                form: withCtx(() => [
                                  createVNode("div", { class: "mb-2 mt-5 col-span-12" }, [
                                    createVNode("h2", { class: "text-lg leading-6 font-medium text-gray-900" }, " Pending Project Invitations "),
                                    createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " These people have been invited to your " + toDisplayString($props.model) + " and have been sent an invitation email. They may join the " + toDisplayString($props.model) + " by accepting the email invitation. ", 1)
                                  ]),
                                  createVNode("div", { class: "space-y-3 col-span-12 mb-3" }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($options.modelInvitations, (invitation) => {
                                      return openBlock(), createBlock("div", {
                                        key: invitation.id,
                                        class: "flex px-4 items-center justify-between"
                                      }, [
                                        createVNode("div", { class: "text-gray-600" }, toDisplayString(invitation.email), 1),
                                        createVNode("div", { class: "flex items-center" }, [
                                          createVNode("button", {
                                            class: "cursor-pointer ml-6 text-sm text-red-500 focus:outline-none",
                                            onClick: ($event) => $options.cancelModelInvitation(
                                              invitation
                                            )
                                          }, " Cancel ", 8, ["onClick"])
                                        ])
                                      ]);
                                    }), 128))
                                  ])
                                ]),
                                _: 1
                              })
                            ])) : createCommentVNode("", true)
                          ])) : (openBlock(), createBlock("div", { key: 1 }, [
                            createVNode(_component_jet_modal_form_section, {
                              class: "space-y-3",
                              onSubmitted: $options.addModelMember
                            }, {
                              form: withCtx(() => [
                                createVNode("div", { class: "col-span-12" }, [
                                  createVNode("div", { class: "max-w-xl mt-2 text-sm text-gray-600" }, " Please provide the email address of the person you would like to add. ")
                                ]),
                                createVNode("div", { class: "col-span-12" }, [
                                  createVNode(_component_jet_label, {
                                    for: "email",
                                    value: "Email"
                                  }),
                                  createVNode(_component_jet_input, {
                                    id: "email",
                                    modelValue: $data.addMemberForm.email,
                                    "onUpdate:modelValue": ($event) => $data.addMemberForm.email = $event,
                                    type: "email",
                                    class: "mt-1 block w-full"
                                  }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                  createVNode(_component_jet_input_error, {
                                    message: $data.addMemberForm.errors.email,
                                    class: "mt-2"
                                  }, null, 8, ["message"])
                                ]),
                                $props.availableRoles.length > 0 ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "col-span-12"
                                }, [
                                  createVNode(_component_jet_label, {
                                    for: "roles",
                                    value: "Role"
                                  }),
                                  createVNode(_component_jet_input_error, {
                                    message: $data.addMemberForm.errors.role,
                                    class: "mt-2"
                                  }, null, 8, ["message"]),
                                  createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer" }, [
                                    (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role, i) => {
                                      return openBlock(), createBlock("button", {
                                        key: role.key,
                                        type: "button",
                                        class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                                          "border-t border-gray-200 rounded-t-none": i > 0,
                                          "rounded-b-none": i != Object.keys(
                                            $props.availableRoles
                                          ).length - 1
                                        }],
                                        onClick: ($event) => $data.addMemberForm.role = role.key
                                      }, [
                                        createVNode("div", {
                                          class: {
                                            "opacity-50": $data.addMemberForm.role && $data.addMemberForm.role != role.key
                                          }
                                        }, [
                                          createVNode("div", { class: "flex items-center" }, [
                                            createVNode("div", {
                                              class: ["text-sm text-gray-600", {
                                                "font-semibold": $data.addMemberForm.role == role.key
                                              }]
                                            }, toDisplayString(role.name), 3),
                                            $data.addMemberForm.role == role.key ? (openBlock(), createBlock("svg", {
                                              key: 0,
                                              class: "ml-2 h-5 w-5 text-green-400",
                                              fill: "none",
                                              "stroke-linecap": "round",
                                              "stroke-linejoin": "round",
                                              "stroke-width": "2",
                                              stroke: "currentColor",
                                              viewBox: "0 0 24 24"
                                            }, [
                                              createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                            ])) : createCommentVNode("", true)
                                          ]),
                                          createVNode("div", { class: "mt-2 text-xs text-gray-600" }, toDisplayString(role.description), 1)
                                        ], 2)
                                      ], 10, ["onClick"]);
                                    }), 128))
                                  ])
                                ])) : createCommentVNode("", true),
                                createVNode("div", { class: "col-span-12" }, [
                                  createVNode(_component_jet_label, {
                                    for: "message",
                                    value: "Message"
                                  }),
                                  createVNode(_component_jet_text_area, {
                                    id: "message",
                                    modelValue: $data.addMemberForm.message,
                                    "onUpdate:modelValue": ($event) => $data.addMemberForm.message = $event,
                                    rows: 3,
                                    class: "mt-1 block w-full"
                                  }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                  createVNode(_component_jet_input_error, {
                                    message: $data.addMemberForm.errors.message,
                                    class: "mt-2"
                                  }, null, 8, ["message"])
                                ])
                              ]),
                              actions: withCtx(() => [
                                createVNode(_component_jet_button, {
                                  class: {
                                    "opacity-25": $data.addMemberForm.processing
                                  },
                                  disabled: $data.addMemberForm.processing
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" SEND ")
                                  ]),
                                  _: 1
                                }, 8, ["class", "disabled"]),
                                createVNode(_component_jet_action_message, {
                                  on: $data.addMemberForm.recentlySuccessful,
                                  class: "mx-3 inline-flex"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" Added. ")
                                  ]),
                                  _: 1
                                }, 8, ["on"])
                              ]),
                              _: 1
                            }, 8, ["onSubmitted"])
                          ]))
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
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
            class: "relative z-10 p-4",
            onClose: ($event) => $setup.open = false
          }, {
            default: withCtx(() => [
              createVNode(_component_TransitionChild, {
                as: "template",
                enter: "ease-out duration-300",
                "enter-from": "opacity-0",
                "enter-to": "opacity-100",
                leave: "ease-in duration-200",
                "leave-from": "opacity-100",
                "leave-to": "opacity-0"
              }, {
                default: withCtx(() => [
                  createVNode("div", { class: "fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" })
                ]),
                _: 1
              }),
              createVNode("div", { class: "fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20" }, [
                createVNode(_component_TransitionChild, {
                  as: "template",
                  enter: "ease-out duration-300",
                  "enter-from": "opacity-0 scale-95",
                  "enter-to": "opacity-100 scale-100",
                  leave: "ease-in duration-200",
                  "leave-from": "opacity-100 scale-100",
                  "leave-to": "opacity-0 scale-95"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_DialogPanel, { class: "mx-auto max-w-xl transform rounded-xl bg-white p-2 shadow-2xl ring-1 ring-black ring-opacity-5 transition-all p-4" }, {
                      default: withCtx(() => [
                        createVNode("div", { class: "sm:flex sm:items-start sm:justify-between" }, [
                          createVNode("div", null, [
                            createVNode("h3", { class: "text-lg leading-6 font-medium text-gray-900" }, " Share with users "),
                            createVNode("span", { class: "float-center text-xs cursor-pointer hover:text-blue-700 mt-2" }, [
                              createVNode("a", {
                                href: "https://docs.nmrxiv.org/submission-guides/sharing.html",
                                target: "_blank"
                              }, "Learn more about sharing ")
                            ]),
                            createVNode("div", { class: "mt-2 max-w-xl text-sm text-gray-500" })
                          ]),
                          createVNode("div", { class: "mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center" }, [
                            !$setup.addUser && $options.canChangeRole ? (openBlock(), createBlock("button", {
                              key: 0,
                              type: "button",
                              class: "inline-flex items-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none sm:text-sm",
                              onClick: ($event) => $setup.addUser = true
                            }, [
                              (openBlock(), createBlock("svg", {
                                xmlns: "http://www.w3.org/2000/svg",
                                class: "h-4 w-4",
                                fill: "none",
                                viewBox: "0 0 24 24",
                                stroke: "currentColor",
                                "stroke-width": "2"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  d: "M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                })
                              ])),
                              createTextVNode("  SHARE ")
                            ], 8, ["onClick"])) : createCommentVNode("", true),
                            $setup.addUser && $options.canChangeRole ? (openBlock(), createBlock("button", {
                              key: 1,
                              type: "button",
                              class: "inline-flex items-center px-4 py-2 border shadow-sm font-medium rounded-md text-dark bg-white-600 hover:bg-white-700 focus:outline-none sm:text-sm",
                              onClick: ($event) => $setup.addUser = false
                            }, [
                              (openBlock(), createBlock("svg", {
                                xmlns: "http://www.w3.org/2000/svg",
                                class: "h-6 w-6",
                                fill: "none",
                                viewBox: "0 0 24 24",
                                stroke: "currentColor",
                                "stroke-width": "2"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  d: "M11 17l-5-5m0 0l5-5m-5 5h12"
                                })
                              ]))
                            ], 8, ["onClick"])) : createCommentVNode("", true)
                          ])
                        ]),
                        !$setup.addUser ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "flow-root mt-1"
                        }, [
                          $props.members.length > 0 ? (openBlock(), createBlock("ul", {
                            key: 0,
                            role: "list",
                            class: "my-5 shadow divide-y divide-gray-200"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList($props.members, (person) => {
                              return openBlock(), createBlock("li", {
                                key: person.email
                              }, [
                                createVNode("a", { class: "block" }, [
                                  createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                    createVNode("div", { class: "min-w-0 flex-1 pt-3 flex items-center" }, [
                                      createVNode("div", { class: "flex-shrink-0" }, [
                                        (openBlock(), createBlock("img", {
                                          key: person.id,
                                          class: "h-12 w-12 rounded-full",
                                          src: person.profile_photo_url,
                                          alt: person.first_name
                                        }, null, 8, ["src", "alt"]))
                                      ]),
                                      createVNode("div", { class: "min-w-0 flex-1 px-4" }, [
                                        createVNode("div", null, [
                                          createVNode("p", { class: "text-sm font-medium text-gray-600" }, [
                                            createTextVNode(toDisplayString(person.first_name) + " " + toDisplayString(person.last_name) + " ", 1),
                                            _ctx.$page.props.user.email == person.email ? (openBlock(), createBlock("span", { key: 0 }, "(you)")) : createCommentVNode("", true)
                                          ]),
                                          createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                            (openBlock(), createBlock("svg", {
                                              class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                              xmlns: "http://www.w3.org/2000/svg",
                                              viewBox: "0 0 20 20",
                                              fill: "currentColor",
                                              "aria-hidden": "true"
                                            }, [
                                              createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                              createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                            ])),
                                            createVNode("span", null, toDisplayString(person.email), 1)
                                          ])
                                        ])
                                      ])
                                    ]),
                                    createVNode("div", { class: "-mt-4" }, [
                                      $options.canChangeRole && !$options.isProjectAlreadyShared ? (openBlock(), createBlock("div", { key: 0 }, [
                                        $options.personRole(
                                          person
                                        ) && $options.personRole(
                                          person
                                        ) == "creator" ? (openBlock(), createBlock("div", { key: 0 }, [
                                          createVNode("span", { class: "ml-6 text-sm text-dark-500" }, " Creator ")
                                        ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                          createVNode("button", {
                                            class: "cursor-pointer mr-3 text-sm text-red-500 hover:text-red-700",
                                            onClick: ($event) => $options.removeModelMember(
                                              person
                                            )
                                          }, " Remove ", 8, ["onClick"]),
                                          createVNode(_component_Menu, {
                                            as: "div",
                                            class: "relative inline-block text-left"
                                          }, {
                                            default: withCtx(() => [
                                              createVNode("div", null, [
                                                createVNode(_component_MenuButton, { class: "inline-flex justify-center w-full rounded-md capitalize border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" }, {
                                                  default: withCtx(() => [
                                                    createTextVNode(toDisplayString($options.personRole(
                                                      person
                                                    )) + " ", 1),
                                                    createVNode(_component_ChevronDownIcon, {
                                                      class: "-mr-1 ml-2 h-5 w-5",
                                                      "aria-hidden": "true"
                                                    })
                                                  ]),
                                                  _: 2
                                                }, 1024)
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
                                                        (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role) => {
                                                          return openBlock(), createBlock("span", {
                                                            key: role.key
                                                          }, [
                                                            person[$props.model + "_membership"].role != role.key ? (openBlock(), createBlock(_component_MenuItem, {
                                                              key: 0,
                                                              onClick: ($event) => $options.updateRole(
                                                                role,
                                                                person
                                                              )
                                                            }, {
                                                              default: withCtx(({
                                                                active
                                                              }) => [
                                                                createVNode("a", {
                                                                  href: "#",
                                                                  class: [
                                                                    active ? "bg-gray-100 text-gray-900" : "text-gray-700",
                                                                    "block px-4 py-2 text-sm"
                                                                  ]
                                                                }, toDisplayString(role.name), 3)
                                                              ]),
                                                              _: 2
                                                            }, 1032, ["onClick"])) : createCommentVNode("", true)
                                                          ]);
                                                        }), 128))
                                                      ])
                                                    ]),
                                                    _: 2
                                                  }, 1024)
                                                ]),
                                                _: 2
                                              }, 1024)
                                            ]),
                                            _: 2
                                          }, 1024)
                                        ]))
                                      ])) : (openBlock(), createBlock("div", { key: 1 }, [
                                        $options.personRole(person) ? (openBlock(), createBlock("div", { key: 0 }, [
                                          createVNode("span", { class: "ml-6 text-sm capitalize text-dark-500" }, toDisplayString($options.personRole(
                                            person
                                          )), 1)
                                        ])) : createCommentVNode("", true)
                                      ]))
                                    ])
                                  ])
                                ])
                              ]);
                            }), 128))
                          ])) : createCommentVNode("", true),
                          $props.team && !$props.team.personal_team ? (openBlock(), createBlock("span", { key: 1 }, [
                            createVNode("div", { class: "mb-2" }, [
                              createVNode("h2", { class: "text-lg uppercase leading-6 font-medium text-gray-900" }, toDisplayString($props.team.name), 1),
                              createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " All the users (corresponding roles) in the team will be applicable to this " + toDisplayString($props.model) + ". ", 1)
                            ]),
                            createVNode("div", { class: "bg-white shadow overflow-hidden sm:rounded-md" }, [
                              createVNode("ul", {
                                role: "list",
                                class: "divide-y divide-gray-200"
                              }, [
                                (openBlock(true), createBlock(Fragment, null, renderList($props.team.users, (person) => {
                                  return openBlock(), createBlock("li", {
                                    key: person.email
                                  }, [
                                    createVNode("a", { class: "block hover:bg-gray-50" }, [
                                      createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                        createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                          createVNode("div", { class: "flex-shrink-0" }, [
                                            (openBlock(), createBlock("img", {
                                              key: person.id,
                                              class: "h-12 w-12 rounded-full",
                                              src: person.profile_photo_url,
                                              alt: person.first_name
                                            }, null, 8, ["src", "alt"]))
                                          ]),
                                          createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                            createVNode("div", null, [
                                              createVNode("p", { class: "text-sm font-medium text-gray-600 truncate" }, toDisplayString(person.first_name), 1),
                                              createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                                (openBlock(), createBlock("svg", {
                                                  class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                  xmlns: "http://www.w3.org/2000/svg",
                                                  viewBox: "0 0 20 20",
                                                  fill: "currentColor",
                                                  "aria-hidden": "true"
                                                }, [
                                                  createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                  createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                                ])),
                                                createVNode("span", { class: "truncate" }, toDisplayString(person.email), 1)
                                              ])
                                            ])
                                          ])
                                        ]),
                                        createVNode("div", null, [
                                          createVNode("span", { class: "inline-flex capitalize items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, toDisplayString(person.membership.role), 1)
                                        ])
                                      ])
                                    ])
                                  ]);
                                }), 128)),
                                createVNode("li", null, [
                                  createVNode("a", { class: "block hover:bg-gray-50" }, [
                                    createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                      createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                        createVNode("div", { class: "flex-shrink-0" }, [
                                          (openBlock(), createBlock("img", {
                                            key: $props.team.owner.id,
                                            class: "h-12 w-12 rounded-full",
                                            src: $props.team.owner.profile_photo_url,
                                            alt: $props.team.owner.first_name
                                          }, null, 8, ["src", "alt"]))
                                        ]),
                                        createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                          createVNode("div", null, [
                                            createVNode("p", { class: "text-sm font-medium text-gray-600 truncate" }, toDisplayString($props.team.owner.first_name), 1),
                                            createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                              (openBlock(), createBlock("svg", {
                                                class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400",
                                                xmlns: "http://www.w3.org/2000/svg",
                                                viewBox: "0 0 20 20",
                                                fill: "currentColor",
                                                "aria-hidden": "true"
                                              }, [
                                                createVNode("path", { d: "M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" }),
                                                createVNode("path", { d: "M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" })
                                              ])),
                                              createVNode("span", { class: "truncate" }, toDisplayString($props.team.owner.email), 1)
                                            ])
                                          ])
                                        ])
                                      ]),
                                      createVNode("div", null, [
                                        createVNode("span", { class: "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, "owner")
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          $options.modelInvitations.length > 0 ? (openBlock(), createBlock("div", {
                            key: 2,
                            class: "pb-5"
                          }, [
                            createVNode(_component_jet_modal_form_section, { class: "mt-10 sm:mt-0" }, {
                              form: withCtx(() => [
                                createVNode("div", { class: "mb-2 mt-5 col-span-12" }, [
                                  createVNode("h2", { class: "text-lg leading-6 font-medium text-gray-900" }, " Pending Project Invitations "),
                                  createVNode("p", { class: "mt-1 text-sm text-gray-500" }, " These people have been invited to your " + toDisplayString($props.model) + " and have been sent an invitation email. They may join the " + toDisplayString($props.model) + " by accepting the email invitation. ", 1)
                                ]),
                                createVNode("div", { class: "space-y-3 col-span-12 mb-3" }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList($options.modelInvitations, (invitation) => {
                                    return openBlock(), createBlock("div", {
                                      key: invitation.id,
                                      class: "flex px-4 items-center justify-between"
                                    }, [
                                      createVNode("div", { class: "text-gray-600" }, toDisplayString(invitation.email), 1),
                                      createVNode("div", { class: "flex items-center" }, [
                                        createVNode("button", {
                                          class: "cursor-pointer ml-6 text-sm text-red-500 focus:outline-none",
                                          onClick: ($event) => $options.cancelModelInvitation(
                                            invitation
                                          )
                                        }, " Cancel ", 8, ["onClick"])
                                      ])
                                    ]);
                                  }), 128))
                                ])
                              ]),
                              _: 1
                            })
                          ])) : createCommentVNode("", true)
                        ])) : (openBlock(), createBlock("div", { key: 1 }, [
                          createVNode(_component_jet_modal_form_section, {
                            class: "space-y-3",
                            onSubmitted: $options.addModelMember
                          }, {
                            form: withCtx(() => [
                              createVNode("div", { class: "col-span-12" }, [
                                createVNode("div", { class: "max-w-xl mt-2 text-sm text-gray-600" }, " Please provide the email address of the person you would like to add. ")
                              ]),
                              createVNode("div", { class: "col-span-12" }, [
                                createVNode(_component_jet_label, {
                                  for: "email",
                                  value: "Email"
                                }),
                                createVNode(_component_jet_input, {
                                  id: "email",
                                  modelValue: $data.addMemberForm.email,
                                  "onUpdate:modelValue": ($event) => $data.addMemberForm.email = $event,
                                  type: "email",
                                  class: "mt-1 block w-full"
                                }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                createVNode(_component_jet_input_error, {
                                  message: $data.addMemberForm.errors.email,
                                  class: "mt-2"
                                }, null, 8, ["message"])
                              ]),
                              $props.availableRoles.length > 0 ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "col-span-12"
                              }, [
                                createVNode(_component_jet_label, {
                                  for: "roles",
                                  value: "Role"
                                }),
                                createVNode(_component_jet_input_error, {
                                  message: $data.addMemberForm.errors.role,
                                  class: "mt-2"
                                }, null, 8, ["message"]),
                                createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer" }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role, i) => {
                                    return openBlock(), createBlock("button", {
                                      key: role.key,
                                      type: "button",
                                      class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                                        "border-t border-gray-200 rounded-t-none": i > 0,
                                        "rounded-b-none": i != Object.keys(
                                          $props.availableRoles
                                        ).length - 1
                                      }],
                                      onClick: ($event) => $data.addMemberForm.role = role.key
                                    }, [
                                      createVNode("div", {
                                        class: {
                                          "opacity-50": $data.addMemberForm.role && $data.addMemberForm.role != role.key
                                        }
                                      }, [
                                        createVNode("div", { class: "flex items-center" }, [
                                          createVNode("div", {
                                            class: ["text-sm text-gray-600", {
                                              "font-semibold": $data.addMemberForm.role == role.key
                                            }]
                                          }, toDisplayString(role.name), 3),
                                          $data.addMemberForm.role == role.key ? (openBlock(), createBlock("svg", {
                                            key: 0,
                                            class: "ml-2 h-5 w-5 text-green-400",
                                            fill: "none",
                                            "stroke-linecap": "round",
                                            "stroke-linejoin": "round",
                                            "stroke-width": "2",
                                            stroke: "currentColor",
                                            viewBox: "0 0 24 24"
                                          }, [
                                            createVNode("path", { d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" })
                                          ])) : createCommentVNode("", true)
                                        ]),
                                        createVNode("div", { class: "mt-2 text-xs text-gray-600" }, toDisplayString(role.description), 1)
                                      ], 2)
                                    ], 10, ["onClick"]);
                                  }), 128))
                                ])
                              ])) : createCommentVNode("", true),
                              createVNode("div", { class: "col-span-12" }, [
                                createVNode(_component_jet_label, {
                                  for: "message",
                                  value: "Message"
                                }),
                                createVNode(_component_jet_text_area, {
                                  id: "message",
                                  modelValue: $data.addMemberForm.message,
                                  "onUpdate:modelValue": ($event) => $data.addMemberForm.message = $event,
                                  rows: 3,
                                  class: "mt-1 block w-full"
                                }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                                createVNode(_component_jet_input_error, {
                                  message: $data.addMemberForm.errors.message,
                                  class: "mt-2"
                                }, null, 8, ["message"])
                              ])
                            ]),
                            actions: withCtx(() => [
                              createVNode(_component_jet_button, {
                                class: {
                                  "opacity-25": $data.addMemberForm.processing
                                },
                                disabled: $data.addMemberForm.processing
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(" SEND ")
                                ]),
                                _: 1
                              }, 8, ["class", "disabled"]),
                              createVNode(_component_jet_action_message, {
                                on: $data.addMemberForm.recentlySuccessful,
                                class: "mx-3 inline-flex"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(" Added. ")
                                ]),
                                _: 1
                              }, 8, ["on"])
                            ]),
                            _: 1
                          }, 8, ["onSubmitted"])
                        ]))
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                })
              ])
            ]),
            _: 1
          }, 8, ["onClose"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/AccessDialogue.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const AccessDialogue = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  AccessDialogue as A
};
