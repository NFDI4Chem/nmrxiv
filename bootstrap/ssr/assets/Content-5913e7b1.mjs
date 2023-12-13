import StudyLayout from "./Layout-114f561c.mjs";
import { Link } from "@inertiajs/vue3";
import { CircleStackIcon, Squares2X2Icon, FolderOpenIcon } from "@heroicons/vue/24/outline";
import { resolveComponent, withCtx, createVNode, resolveDynamicComponent, openBlock, createBlock, toDisplayString, Fragment, renderList, renderSlot, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrIncludeBooleanAttr, ssrInterpolate, ssrRenderVNode, ssrRenderSlot } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./AppLayout-b93d1c11.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
import "@vueuse/core";
import "@headlessui/vue";
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
import "./Details-f3d36d90.mjs";
import "./ActionMessage-3207b224.mjs";
import "./Activity-90a526a4.mjs";
import "vue3-colorpicker";
/* empty css                 */import "./AccessDialogue-1fdd1bde.mjs";
import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Input-f9a8cf77.mjs";
import "./Label-c5144ba4.mjs";
import "./SectionBorder-7d1f222a.mjs";
import "./Citation-80069afd.mjs";
import "axios";
const subNavigation = [
  { name: "About", route: "dashboard.studies", icon: CircleStackIcon },
  {
    name: "Spectra",
    route: "dashboard.study.datasets",
    icon: Squares2X2Icon
  },
  { name: "Files", route: "dashboard.study.files", icon: FolderOpenIcon }
];
const _sfc_main = {
  components: {
    StudyLayout,
    Link
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
  setup() {
    return {
      subNavigation
    };
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_study_layout = resolveComponent("study-layout");
  const _component_Link = resolveComponent("Link");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_study_layout, {
    model: $props.model,
    project: $props.project,
    study: $props.study,
    team: $props.team,
    members: $props.members,
    "available-roles": $props.availableRoles,
    "study-permissions": $props.studyPermissions,
    "study-role": $props.studyRole
  }, {
    scontent: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="bg-white shadow-md rounded-lg"${_scopeId}><div class="md:hidden"${_scopeId}><label for="tabs" class="sr-only"${_scopeId}>Select a tab</label><select class="block w-full focus:ring-teal-500 focus:border-teal-500 border-gray-300 rounded-md"${_scopeId}><!--[-->`);
        ssrRenderList($setup.subNavigation, (tab) => {
          _push2(`<option${ssrIncludeBooleanAttr(tab.name === $props.current) ? " selected" : ""}${_scopeId}>${ssrInterpolate(tab.name)}</option>`);
        });
        _push2(`<!--]--></select></div><div class="hidden md:block"${_scopeId}><div class="border-b border-t rounded-t-md border-gray-200 pl-4"${_scopeId}><nav class="-mb-px flex space-x-8" aria-label="Tabs"${_scopeId}><!--[-->`);
        ssrRenderList($setup.subNavigation, (tab) => {
          _push2(ssrRenderComponent(_component_Link, {
            key: tab.name,
            href: _ctx.route(tab.route, [$props.study.id]),
            class: [
              tab.name == $props.current ? "border-teal-500 text-teal-600" : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300",
              "group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm"
            ],
            "aria-current": tab.name === $props.current ? "page" : void 0
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                ssrRenderVNode(_push3, createVNode(resolveDynamicComponent(tab.icon), {
                  class: [
                    tab.name === $props.current ? "text-teal-500" : "text-gray-400 group-hover:text-gray-500",
                    "-ml-0.5 mr-2 h-5 w-5"
                  ],
                  "aria-hidden": "true"
                }, null), _parent3, _scopeId2);
                _push3(`<span${_scopeId2}>${ssrInterpolate(tab.name)}</span>`);
              } else {
                return [
                  (openBlock(), createBlock(resolveDynamicComponent(tab.icon), {
                    class: [
                      tab.name === $props.current ? "text-teal-500" : "text-gray-400 group-hover:text-gray-500",
                      "-ml-0.5 mr-2 h-5 w-5"
                    ],
                    "aria-hidden": "true"
                  }, null, 8, ["class"])),
                  createVNode("span", null, toDisplayString(tab.name), 1)
                ];
              }
            }),
            _: 2
          }, _parent2, _scopeId));
        });
        _push2(`<!--]--></nav></div></div>`);
        ssrRenderSlot(_ctx.$slots, "study-section", {}, null, _push2, _parent2, _scopeId);
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "bg-white shadow-md rounded-lg" }, [
            createVNode("div", { class: "md:hidden" }, [
              createVNode("label", {
                for: "tabs",
                class: "sr-only"
              }, "Select a tab"),
              createVNode("select", { class: "block w-full focus:ring-teal-500 focus:border-teal-500 border-gray-300 rounded-md" }, [
                (openBlock(true), createBlock(Fragment, null, renderList($setup.subNavigation, (tab) => {
                  return openBlock(), createBlock("option", {
                    key: tab.name,
                    selected: tab.name === $props.current
                  }, toDisplayString(tab.name), 9, ["selected"]);
                }), 128))
              ])
            ]),
            createVNode("div", { class: "hidden md:block" }, [
              createVNode("div", { class: "border-b border-t rounded-t-md border-gray-200 pl-4" }, [
                createVNode("nav", {
                  class: "-mb-px flex space-x-8",
                  "aria-label": "Tabs"
                }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($setup.subNavigation, (tab) => {
                    return openBlock(), createBlock(_component_Link, {
                      key: tab.name,
                      href: _ctx.route(tab.route, [$props.study.id]),
                      class: [
                        tab.name == $props.current ? "border-teal-500 text-teal-600" : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300",
                        "group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm"
                      ],
                      "aria-current": tab.name === $props.current ? "page" : void 0
                    }, {
                      default: withCtx(() => [
                        (openBlock(), createBlock(resolveDynamicComponent(tab.icon), {
                          class: [
                            tab.name === $props.current ? "text-teal-500" : "text-gray-400 group-hover:text-gray-500",
                            "-ml-0.5 mr-2 h-5 w-5"
                          ],
                          "aria-hidden": "true"
                        }, null, 8, ["class"])),
                        createVNode("span", null, toDisplayString(tab.name), 1)
                      ]),
                      _: 2
                    }, 1032, ["href", "class", "aria-current"]);
                  }), 128))
                ])
              ])
            ]),
            renderSlot(_ctx.$slots, "study-section")
          ])
        ];
      }
    }),
    _: 3
  }, _parent));
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/Content.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const StudyContent = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  StudyContent as default
};
