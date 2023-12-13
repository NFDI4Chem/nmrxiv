import { J as JetActionMessage } from "./ActionMessage-3207b224.mjs";
import { J as JetActionSection } from "./ActionSection-c1b53bcc.mjs";
import { J as JetButton } from "./Button-8d6976fd.mjs";
import { J as JetConfirmationModal } from "./ConfirmationModal-d0b47856.mjs";
import { J as JetDangerButton } from "./DangerButton-9c23157c.mjs";
import { J as JetDialogModal } from "./DialogModal-f54eea4c.mjs";
import { J as JetFormSection } from "./FormSection-d2d52d64.mjs";
import { J as JetInput } from "./Input-f9a8cf77.mjs";
import { J as JetInputError } from "./InputError-6ae0b65c.mjs";
import { J as JetLabel } from "./Label-c5144ba4.mjs";
import { J as JetSecondaryButton } from "./SecondaryButton-3cee9e05.mjs";
import { J as JetSectionBorder } from "./SectionBorder-7d1f222a.mjs";
import { resolveComponent, withCtx, createTextVNode, createVNode, openBlock, createBlock, Fragment, renderList, toDisplayString, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrRenderClass, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Modal-b2fb4cf2.mjs";
const _sfc_main = {
  components: {
    JetActionMessage,
    JetActionSection,
    JetButton,
    JetConfirmationModal,
    JetDangerButton,
    JetDialogModal,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
    JetSectionBorder
  },
  props: ["team", "availableRoles", "userPermissions"],
  data() {
    return {
      addTeamMemberForm: this.$inertia.form({
        email: "",
        role: null
      }),
      updateRoleForm: this.$inertia.form({
        role: null
      }),
      leaveTeamForm: this.$inertia.form({}),
      removeTeamMemberForm: this.$inertia.form({}),
      currentlyManagingRole: false,
      managingRoleFor: null,
      confirmingLeavingTeam: false,
      teamMemberBeingRemoved: null
    };
  },
  methods: {
    addTeamMember() {
      this.addTeamMemberForm.post(
        route("team-members.store", this.team),
        {
          errorBag: "addTeamMember",
          preserveScroll: true,
          onSuccess: () => this.addTeamMemberForm.reset()
        }
      );
    },
    cancelTeamInvitation(invitation) {
      this.$inertia.delete(
        route("team-invitations.destroy", invitation),
        {
          preserveScroll: true
        }
      );
    },
    manageRole(teamMember) {
      this.managingRoleFor = teamMember;
      this.updateRoleForm.role = teamMember.membership.role;
      this.currentlyManagingRole = true;
    },
    updateRole() {
      this.updateRoleForm.put(
        route("team-members.update", [this.team, this.managingRoleFor]),
        {
          preserveScroll: true,
          onSuccess: () => this.currentlyManagingRole = false
        }
      );
    },
    confirmLeavingTeam() {
      this.confirmingLeavingTeam = true;
    },
    leaveTeam() {
      this.leaveTeamForm.delete(
        route("team-members.destroy", [
          this.team,
          this.$page.props.auth.user
        ])
      );
    },
    confirmTeamMemberRemoval(teamMember) {
      this.teamMemberBeingRemoved = teamMember;
    },
    removeTeamMember() {
      this.removeTeamMemberForm.delete(
        route("team-members.destroy", [
          this.team,
          this.teamMemberBeingRemoved
        ]),
        {
          errorBag: "removeTeamMember",
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => this.teamMemberBeingRemoved = null
        }
      );
    },
    displayableRole(role) {
      return this.availableRoles.find((r) => r.key === role).name;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_jet_section_border = resolveComponent("jet-section-border");
  const _component_jet_form_section = resolveComponent("jet-form-section");
  const _component_jet_label = resolveComponent("jet-label");
  const _component_jet_input = resolveComponent("jet-input");
  const _component_jet_input_error = resolveComponent("jet-input-error");
  const _component_jet_action_message = resolveComponent("jet-action-message");
  const _component_jet_button = resolveComponent("jet-button");
  const _component_jet_action_section = resolveComponent("jet-action-section");
  const _component_jet_dialog_modal = resolveComponent("jet-dialog-modal");
  const _component_jet_secondary_button = resolveComponent("jet-secondary-button");
  const _component_jet_confirmation_modal = resolveComponent("jet-confirmation-modal");
  const _component_jet_danger_button = resolveComponent("jet-danger-button");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  if ($props.userPermissions.canAddTeamMembers) {
    _push(`<div>`);
    _push(ssrRenderComponent(_component_jet_section_border, null, null, _parent));
    _push(ssrRenderComponent(_component_jet_form_section, { onSubmitted: $options.addTeamMember }, {
      title: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` Add Team Member `);
        } else {
          return [
            createTextVNode(" Add Team Member ")
          ];
        }
      }),
      description: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` Add a new team member to your team, allowing them to collaborate with you. `);
        } else {
          return [
            createTextVNode(" Add a new team member to your team, allowing them to collaborate with you. ")
          ];
        }
      }),
      form: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="col-span-6"${_scopeId}><div class="max-w-xl text-sm text-gray-600"${_scopeId}> Please provide the email address of the person you would like to add to this team. </div></div><div class="col-span-6 sm:col-span-4"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_jet_label, {
            for: "email",
            value: "Email"
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_input, {
            id: "email",
            modelValue: $data.addTeamMemberForm.email,
            "onUpdate:modelValue": ($event) => $data.addTeamMemberForm.email = $event,
            type: "email",
            class: "mt-1 block w-full"
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_input_error, {
            message: $data.addTeamMemberForm.errors.email,
            class: "mt-2"
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
          if ($props.availableRoles.length > 0) {
            _push2(`<div class="col-span-6 lg:col-span-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_jet_label, {
              for: "roles",
              value: "Role"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_jet_input_error, {
              message: $data.addTeamMemberForm.errors.role,
              class: "mt-2"
            }, null, _parent2, _scopeId));
            _push2(`<div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer text-left"${_scopeId}><!--[-->`);
            ssrRenderList($props.availableRoles, (role, i) => {
              _push2(`<button type="button" class="${ssrRenderClass([{
                "border-t border-gray-200 rounded-t-none": i > 0,
                "rounded-b-none": i != Object.keys($props.availableRoles).length - 1
              }, "relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"])}"${_scopeId}><div class="${ssrRenderClass({
                "opacity-50": $data.addTeamMemberForm.role && $data.addTeamMemberForm.role != role.key
              })}"${_scopeId}><div class="flex items-center"${_scopeId}><div class="${ssrRenderClass([{
                "font-semibold": $data.addTeamMemberForm.role == role.key
              }, "text-sm text-gray-600"])}"${_scopeId}>${ssrInterpolate(role.name)}</div>`);
              if ($data.addTeamMemberForm.role == role.key) {
                _push2(`<svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId}></path></svg>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="mt-2 text-xs text-left text-gray-600"${_scopeId}>${ssrInterpolate(role.description)}</div></div></button>`);
            });
            _push2(`<!--]--></div></div>`);
          } else {
            _push2(`<!---->`);
          }
        } else {
          return [
            createVNode("div", { class: "col-span-6" }, [
              createVNode("div", { class: "max-w-xl text-sm text-gray-600" }, " Please provide the email address of the person you would like to add to this team. ")
            ]),
            createVNode("div", { class: "col-span-6 sm:col-span-4" }, [
              createVNode(_component_jet_label, {
                for: "email",
                value: "Email"
              }),
              createVNode(_component_jet_input, {
                id: "email",
                modelValue: $data.addTeamMemberForm.email,
                "onUpdate:modelValue": ($event) => $data.addTeamMemberForm.email = $event,
                type: "email",
                class: "mt-1 block w-full"
              }, null, 8, ["modelValue", "onUpdate:modelValue"]),
              createVNode(_component_jet_input_error, {
                message: $data.addTeamMemberForm.errors.email,
                class: "mt-2"
              }, null, 8, ["message"])
            ]),
            $props.availableRoles.length > 0 ? (openBlock(), createBlock("div", {
              key: 0,
              class: "col-span-6 lg:col-span-4"
            }, [
              createVNode(_component_jet_label, {
                for: "roles",
                value: "Role"
              }),
              createVNode(_component_jet_input_error, {
                message: $data.addTeamMemberForm.errors.role,
                class: "mt-2"
              }, null, 8, ["message"]),
              createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer text-left" }, [
                (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role, i) => {
                  return openBlock(), createBlock("button", {
                    key: role.key,
                    type: "button",
                    class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                      "border-t border-gray-200 rounded-t-none": i > 0,
                      "rounded-b-none": i != Object.keys($props.availableRoles).length - 1
                    }],
                    onClick: ($event) => $data.addTeamMemberForm.role = role.key
                  }, [
                    createVNode("div", {
                      class: {
                        "opacity-50": $data.addTeamMemberForm.role && $data.addTeamMemberForm.role != role.key
                      }
                    }, [
                      createVNode("div", { class: "flex items-center" }, [
                        createVNode("div", {
                          class: ["text-sm text-gray-600", {
                            "font-semibold": $data.addTeamMemberForm.role == role.key
                          }]
                        }, toDisplayString(role.name), 3),
                        $data.addTeamMemberForm.role == role.key ? (openBlock(), createBlock("svg", {
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
                      createVNode("div", { class: "mt-2 text-xs text-left text-gray-600" }, toDisplayString(role.description), 1)
                    ], 2)
                  ], 10, ["onClick"]);
                }), 128))
              ])
            ])) : createCommentVNode("", true)
          ];
        }
      }),
      actions: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_jet_action_message, {
            on: $data.addTeamMemberForm.recentlySuccessful,
            class: "mr-3"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Added. `);
              } else {
                return [
                  createTextVNode(" Added. ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_jet_button, {
            class: { "opacity-25": $data.addTeamMemberForm.processing },
            disabled: $data.addTeamMemberForm.processing
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Add `);
              } else {
                return [
                  createTextVNode(" Add ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
        } else {
          return [
            createVNode(_component_jet_action_message, {
              on: $data.addTeamMemberForm.recentlySuccessful,
              class: "mr-3"
            }, {
              default: withCtx(() => [
                createTextVNode(" Added. ")
              ]),
              _: 1
            }, 8, ["on"]),
            createVNode(_component_jet_button, {
              class: { "opacity-25": $data.addTeamMemberForm.processing },
              disabled: $data.addTeamMemberForm.processing
            }, {
              default: withCtx(() => [
                createTextVNode(" Add ")
              ]),
              _: 1
            }, 8, ["class", "disabled"])
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.team.team_invitations.length > 0 && $props.userPermissions.canAddTeamMembers) {
    _push(`<div>`);
    _push(ssrRenderComponent(_component_jet_section_border, null, null, _parent));
    _push(ssrRenderComponent(_component_jet_action_section, { class: "mt-10 sm:mt-0" }, {
      title: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` Pending Team Invitations `);
        } else {
          return [
            createTextVNode(" Pending Team Invitations ")
          ];
        }
      }),
      description: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation. `);
        } else {
          return [
            createTextVNode(" These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation. ")
          ];
        }
      }),
      content: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="space-y-6"${_scopeId}><!--[-->`);
          ssrRenderList($props.team.team_invitations, (invitation) => {
            _push2(`<div class="flex items-center justify-between"${_scopeId}><div class="text-gray-600"${_scopeId}>${ssrInterpolate(invitation.email)}</div><div class="flex items-center"${_scopeId}>`);
            if ($props.userPermissions.canRemoveTeamMembers) {
              _push2(`<button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"${_scopeId}> Cancel </button>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
          });
          _push2(`<!--]--></div>`);
        } else {
          return [
            createVNode("div", { class: "space-y-6" }, [
              (openBlock(true), createBlock(Fragment, null, renderList($props.team.team_invitations, (invitation) => {
                return openBlock(), createBlock("div", {
                  key: invitation.id,
                  class: "flex items-center justify-between"
                }, [
                  createVNode("div", { class: "text-gray-600" }, toDisplayString(invitation.email), 1),
                  createVNode("div", { class: "flex items-center" }, [
                    $props.userPermissions.canRemoveTeamMembers ? (openBlock(), createBlock("button", {
                      key: 0,
                      class: "cursor-pointer ml-6 text-sm text-red-500 focus:outline-none",
                      onClick: ($event) => $options.cancelTeamInvitation(invitation)
                    }, " Cancel ", 8, ["onClick"])) : createCommentVNode("", true)
                  ])
                ]);
              }), 128))
            ])
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.team.users.length > 0) {
    _push(`<div>`);
    _push(ssrRenderComponent(_component_jet_section_border, null, null, _parent));
    _push(ssrRenderComponent(_component_jet_action_section, { class: "mt-10 sm:mt-0" }, {
      title: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` Team Members `);
        } else {
          return [
            createTextVNode(" Team Members ")
          ];
        }
      }),
      description: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` All of the people that are part of this team. `);
        } else {
          return [
            createTextVNode(" All of the people that are part of this team. ")
          ];
        }
      }),
      content: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="space-y-6"${_scopeId}><!--[-->`);
          ssrRenderList($props.team.users, (user) => {
            _push2(`<div class="flex items-center justify-between"${_scopeId}><div class="flex items-center"${_scopeId}><img class="w-8 h-8 rounded-full"${ssrRenderAttr("src", user.profile_photo_url)}${ssrRenderAttr(
              "alt",
              user.first_name + " " + user.last_name
            )}${_scopeId}><div class="ml-4"${_scopeId}>${ssrInterpolate(user.first_name)} ${ssrInterpolate(user.last_name)}</div></div><div class="flex items-center"${_scopeId}>`);
            if ($props.userPermissions.canAddTeamMembers && $props.availableRoles.length) {
              _push2(`<button class="ml-2 text-sm text-gray-400 underline"${_scopeId}>${ssrInterpolate($options.displayableRole(user.membership.role))}</button>`);
            } else if ($props.availableRoles.length) {
              _push2(`<div class="ml-2 text-sm text-gray-400"${_scopeId}>${ssrInterpolate($options.displayableRole(user.membership.role))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (_ctx.$page.props.auth.user.id === user.id) {
              _push2(`<button class="cursor-pointer ml-6 text-sm text-red-500"${_scopeId}> Leave </button>`);
            } else {
              _push2(`<!---->`);
            }
            if ($props.userPermissions.canRemoveTeamMembers) {
              _push2(`<button class="cursor-pointer ml-6 text-sm text-red-500"${_scopeId}> Remove </button>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
          });
          _push2(`<!--]--></div>`);
        } else {
          return [
            createVNode("div", { class: "space-y-6" }, [
              (openBlock(true), createBlock(Fragment, null, renderList($props.team.users, (user) => {
                return openBlock(), createBlock("div", {
                  key: user.id,
                  class: "flex items-center justify-between"
                }, [
                  createVNode("div", { class: "flex items-center" }, [
                    createVNode("img", {
                      class: "w-8 h-8 rounded-full",
                      src: user.profile_photo_url,
                      alt: user.first_name + " " + user.last_name
                    }, null, 8, ["src", "alt"]),
                    createVNode("div", { class: "ml-4" }, toDisplayString(user.first_name) + " " + toDisplayString(user.last_name), 1)
                  ]),
                  createVNode("div", { class: "flex items-center" }, [
                    $props.userPermissions.canAddTeamMembers && $props.availableRoles.length ? (openBlock(), createBlock("button", {
                      key: 0,
                      class: "ml-2 text-sm text-gray-400 underline",
                      onClick: ($event) => $options.manageRole(user)
                    }, toDisplayString($options.displayableRole(user.membership.role)), 9, ["onClick"])) : $props.availableRoles.length ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "ml-2 text-sm text-gray-400"
                    }, toDisplayString($options.displayableRole(user.membership.role)), 1)) : createCommentVNode("", true),
                    _ctx.$page.props.auth.user.id === user.id ? (openBlock(), createBlock("button", {
                      key: 2,
                      class: "cursor-pointer ml-6 text-sm text-red-500",
                      onClick: $options.confirmLeavingTeam
                    }, " Leave ", 8, ["onClick"])) : createCommentVNode("", true),
                    $props.userPermissions.canRemoveTeamMembers ? (openBlock(), createBlock("button", {
                      key: 3,
                      class: "cursor-pointer ml-6 text-sm text-red-500",
                      onClick: ($event) => $options.confirmTeamMemberRemoval(user)
                    }, " Remove ", 8, ["onClick"])) : createCommentVNode("", true)
                  ])
                ]);
              }), 128))
            ])
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(ssrRenderComponent(_component_jet_dialog_modal, {
    show: $data.currentlyManagingRole,
    onClose: ($event) => $data.currentlyManagingRole = false
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Manage Role `);
      } else {
        return [
          createTextVNode(" Manage Role ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($data.managingRoleFor) {
          _push2(`<div${_scopeId}><div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"${_scopeId}><!--[-->`);
          ssrRenderList($props.availableRoles, (role, i) => {
            _push2(`<button type="button" class="${ssrRenderClass([{
              "border-t border-gray-200 rounded-t-none": i > 0,
              "rounded-b-none": i !== Object.keys($props.availableRoles).length - 1
            }, "relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"])}"${_scopeId}><div class="${ssrRenderClass({
              "opacity-50": $data.updateRoleForm.role && $data.updateRoleForm.role !== role.key
            })}"${_scopeId}><div class="flex items-center"${_scopeId}><div class="${ssrRenderClass([{
              "font-semibold": $data.updateRoleForm.role === role.key
            }, "text-sm text-gray-600"])}"${_scopeId}>${ssrInterpolate(role.name)}</div>`);
            if ($data.updateRoleForm.role === role.key) {
              _push2(`<svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"${_scopeId}><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId}></path></svg>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="mt-2 text-xs text-gray-600"${_scopeId}>${ssrInterpolate(role.description)}</div></div></button>`);
          });
          _push2(`<!--]--></div></div>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          $data.managingRoleFor ? (openBlock(), createBlock("div", { key: 0 }, [
            createVNode("div", { class: "relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer" }, [
              (openBlock(true), createBlock(Fragment, null, renderList($props.availableRoles, (role, i) => {
                return openBlock(), createBlock("button", {
                  key: role.key,
                  type: "button",
                  class: ["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200", {
                    "border-t border-gray-200 rounded-t-none": i > 0,
                    "rounded-b-none": i !== Object.keys($props.availableRoles).length - 1
                  }],
                  onClick: ($event) => $data.updateRoleForm.role = role.key
                }, [
                  createVNode("div", {
                    class: {
                      "opacity-50": $data.updateRoleForm.role && $data.updateRoleForm.role !== role.key
                    }
                  }, [
                    createVNode("div", { class: "flex items-center" }, [
                      createVNode("div", {
                        class: ["text-sm text-gray-600", {
                          "font-semibold": $data.updateRoleForm.role === role.key
                        }]
                      }, toDisplayString(role.name), 3),
                      $data.updateRoleForm.role === role.key ? (openBlock(), createBlock("svg", {
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
          ])) : createCommentVNode("", true)
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.currentlyManagingRole = false
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
        _push2(ssrRenderComponent(_component_jet_button, {
          class: ["ml-2", { "opacity-25": $data.updateRoleForm.processing }],
          disabled: $data.updateRoleForm.processing,
          onClick: $options.updateRole
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
            onClick: ($event) => $data.currentlyManagingRole = false
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_button, {
            class: ["ml-2", { "opacity-25": $data.updateRoleForm.processing }],
            disabled: $data.updateRoleForm.processing,
            onClick: $options.updateRole
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
  _push(ssrRenderComponent(_component_jet_confirmation_modal, {
    show: $data.confirmingLeavingTeam,
    onClose: ($event) => $data.confirmingLeavingTeam = false
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Leave Team `);
      } else {
        return [
          createTextVNode(" Leave Team ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Are you sure you would like to leave this team? `);
      } else {
        return [
          createTextVNode(" Are you sure you would like to leave this team? ")
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.confirmingLeavingTeam = false
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
        _push2(ssrRenderComponent(_component_jet_danger_button, {
          class: ["ml-2", { "opacity-25": $data.leaveTeamForm.processing }],
          disabled: $data.leaveTeamForm.processing,
          onClick: $options.leaveTeam
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Leave `);
            } else {
              return [
                createTextVNode(" Leave ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, {
            onClick: ($event) => $data.confirmingLeavingTeam = false
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_danger_button, {
            class: ["ml-2", { "opacity-25": $data.leaveTeamForm.processing }],
            disabled: $data.leaveTeamForm.processing,
            onClick: $options.leaveTeam
          }, {
            default: withCtx(() => [
              createTextVNode(" Leave ")
            ]),
            _: 1
          }, 8, ["class", "disabled", "onClick"])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_jet_confirmation_modal, {
    show: $data.teamMemberBeingRemoved,
    onClose: ($event) => $data.teamMemberBeingRemoved = null
  }, {
    title: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Remove Team Member `);
      } else {
        return [
          createTextVNode(" Remove Team Member ")
        ];
      }
    }),
    content: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Are you sure you would like to remove this person from the team? `);
      } else {
        return [
          createTextVNode(" Are you sure you would like to remove this person from the team? ")
        ];
      }
    }),
    footer: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_jet_secondary_button, {
          onClick: ($event) => $data.teamMemberBeingRemoved = null
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
        _push2(ssrRenderComponent(_component_jet_danger_button, {
          class: ["ml-2", { "opacity-25": $data.removeTeamMemberForm.processing }],
          disabled: $data.removeTeamMemberForm.processing,
          onClick: $options.removeTeamMember
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(` Remove `);
            } else {
              return [
                createTextVNode(" Remove ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_jet_secondary_button, {
            onClick: ($event) => $data.teamMemberBeingRemoved = null
          }, {
            default: withCtx(() => [
              createTextVNode(" Cancel ")
            ]),
            _: 1
          }, 8, ["onClick"]),
          createVNode(_component_jet_danger_button, {
            class: ["ml-2", { "opacity-25": $data.removeTeamMemberForm.processing }],
            disabled: $data.removeTeamMemberForm.processing,
            onClick: $options.removeTeamMember
          }, {
            default: withCtx(() => [
              createTextVNode(" Remove ")
            ]),
            _: 1
          }, 8, ["class", "disabled", "onClick"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Teams/Partials/TeamMemberManager.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const TeamMemberManager = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  TeamMemberManager as default
};
