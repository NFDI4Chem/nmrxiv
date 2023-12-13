import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { Link, router } from "@inertiajs/vue3";
import StudyDetails from "./Details-f3d36d90.mjs";
import { ExclamationCircleIcon, BriefcaseIcon, CalendarIcon, CheckIcon, ChevronDownIcon, ChevronRightIcon, CurrencyDollarIcon, LinkIcon, MapPinIcon, PencilIcon, StarIcon } from "@heroicons/vue/24/solid";
import { ref, resolveComponent, mergeProps, withCtx, createTextVNode, toDisplayString, openBlock, createBlock, createVNode, createCommentVNode, renderSlot, useSSRContext } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { A as AccessDialogue } from "./AccessDialogue-1fdd1bde.mjs";
import { C as Citation } from "./Citation-80069afd.mjs";
import { ssrRenderComponent, ssrInterpolate, ssrRenderStyle, ssrRenderSlot } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
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
import "./ActionMessage-3207b224.mjs";
import "./Activity-90a526a4.mjs";
import "vue3-colorpicker";
/* empty css                 */import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Input-f9a8cf77.mjs";
import "./Label-c5144ba4.mjs";
import "./SectionBorder-7d1f222a.mjs";
import "axios";
const _sfc_main = {
  components: {
    Link,
    AppLayout,
    StudyDetails,
    ExclamationCircleIcon,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    BriefcaseIcon,
    CalendarIcon,
    CheckIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    CurrencyDollarIcon,
    LinkIcon,
    MapPinIcon,
    PencilIcon,
    StarIcon,
    AccessDialogue,
    Citation
  },
  props: [
    "study",
    "project",
    "team",
    "members",
    "availableRoles",
    "studyPermissions",
    "studyRole",
    "model"
  ],
  setup() {
    const studyDetailsElement = ref(null);
    return {
      studyDetailsElement
    };
  },
  data() {
    return {};
  },
  computed: {
    canUpdateStudy() {
      return this.studyPermissions ? this.studyPermissions.canUpdateStudy : false;
    }
  },
  methods: {
    toggleDetails() {
      this.studyDetailsElement.toggleDetails();
    },
    toogleStarred() {
      const url = "/studies/" + this.study.id + "/toggleStarred";
      axios.get(url).catch((err) => {
        if (err.response.status !== 200 || err.response.status !== 201) {
          throw new Error(
            `API call failed with status code: ${err.response.status}`
          );
        }
      }).then(function(response) {
        router.reload({ only: ["study"] });
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Citation = resolveComponent("Citation");
  const _component_Link = resolveComponent("Link");
  const _component_ChevronRightIcon = resolveComponent("ChevronRightIcon");
  const _component_StarIcon = resolveComponent("StarIcon");
  const _component_access_dialogue = resolveComponent("access-dialogue");
  const _component_study_details = resolveComponent("study-details");
  const _component_CalendarIcon = resolveComponent("CalendarIcon");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({
    title: $props.study.name
  }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($props.study.is_deleted) {
          _push2(`<div class="text-center px-3 py-1 bg-red-50 text-red-700 border-b"${_scopeId}><b${_scopeId}>Warning: </b> This study is deleted. At the end of the 30-day period, this study and all of its resources will be deleted permanently and cannot be recovered. You can only restore a deleted study/project within the 30-day recovery period. </div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div${_scopeId}>`);
        if ($props.study.is_public && $props.study.is_archived) {
          _push2(`<div class="text-center px-3 py-2 bg-yellow-50 text-yellow-700 border-b"${_scopeId}><b${_scopeId}>Warning: </b> This project is archived. It is now read-only. </div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.study.is_public && !$props.study.is_archived) {
          _push2(`<div class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"${_scopeId}><b${_scopeId}>Info: </b> This project is published. You cannot edit a published project, please create a new version to updated the project. </div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
        if ($props.study.is_public && $props.study.doi != null) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Citation, {
            model: "study",
            doi: $props.study.doi
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between py-6"${_scopeId}><div class="lg:flex lg:items-center lg:justify-between w-full"${_scopeId}><div class="flex-1 min-w-0"${_scopeId}><nav class="flex" aria-label="Breadcrumb"${_scopeId}><ol role="list" class="flex items-center space-x-4"${_scopeId}><li${_scopeId}><div class="flex"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("dashboard"),
          class: "text-sm font-medium text-gray-500 hover:text-gray-700"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`Dashboard`);
            } else {
              return [
                createTextVNode("Dashboard")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></li><li${_scopeId}><div class="flex items-center"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_ChevronRightIcon, {
          class: "flex-shrink-0 h-5 w-5 text-gray-400",
          "aria-hidden": "true"
        }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route(
            "dashboard.projects",
            [$props.project.id]
          ),
          class: "ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($props.project.name)}`);
            } else {
              return [
                createTextVNode(toDisplayString($props.project.name), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></li></ol></nav><div class="flex pr-20 mt-2 cursor-pointer items-center text-xl text-gray-700 font-bold"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_StarIcon, {
          class: [
            $props.study.is_bookmarked ? "text-yellow-400" : "text-gray-200",
            "h-5 w-5 flex-shrink-0 -ml-1 mr-1"
          ],
          "aria-hidden": "true",
          onClick: $options.toogleStarred
        }, null, _parent2, _scopeId));
        _push2(` ${ssrInterpolate($props.study.name)} `);
        if ($options.canUpdateStudy) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-4 h-4 mr-2 text-gray-600"${_scopeId}><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"${_scopeId}></path></svg><span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div><div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6"${_scopeId}>`);
        if (!$props.study.is_deleted) {
          _push2(`<div class="mt-2 flex items-center text-sm text-gray-700"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_access_dialogue, {
            "available-roles": $props.availableRoles,
            role: $props.studyRole,
            team: $props.team,
            study: $props.study,
            members: $props.members,
            project: $props.project,
            model: $props.model,
            "called-from": "studyView"
          }, null, _parent2, _scopeId));
          _push2(`<a class="cursor-pointer inline-flex items-center"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4"${_scopeId}><path d="M4 15a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a1 1 0 0 1 .7.3L13.42 5H21a1 1 0 0 1 .9 1.45L19.61 11l2.27 4.55A1 1 0 0 1 21 17h-8a1 1 0 0 1-.7-.3L10.58 15H4z" class="fill-current text-gray-400"${_scopeId}></path><rect width="2" height="20" x="2" y="2" rx="1" class="fill-current text-gray-600"${_scopeId}></rect></svg><span class="ml-2"${_scopeId}>View details</span></a><div class="ml-3"${_scopeId}>`);
          if ($props.study.is_public) {
            _push2(`<span class="inline-flex items-center"${_scopeId}><svg class="h-3 w-3 text-green-400 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="512" height="512"${_scopeId}><g id="globe"${_scopeId}><path d="M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z"${_scopeId}></path><path d="M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z"${_scopeId}></path><path d="M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z"${_scopeId}></path></g></svg><span class="ml-2"${_scopeId}>Public</span></span>`);
          } else {
            _push2(`<span class="inline-flex items-center"${_scopeId}><svg id="Capa_1" class="h-3 w-3 text-gray-400 inline" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="${ssrRenderStyle({ "enable-background": "new 0\n                                                            0 512 512" })}" xml:space="preserve"${_scopeId}><g${_scopeId}><g${_scopeId}><path d="M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32
                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333
                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292
                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51
                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667
                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64
                                s85.333,38.281,85.333,85.333V192z"${_scopeId}></path></g></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g></svg><span class="ml-2"${_scopeId}>Private</span></span>`);
          }
          _push2(`</div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="mt-2 flex items-center text-sm text-gray-500"${_scopeId}><span class="capitalize inline-flex pr-4 ml-7 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"${_scopeId}>`);
        if ($props.studyRole == "reviewer") {
          _push2(`<span${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 py-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"${_scopeId}></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"${_scopeId}></path></svg></span>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.studyRole == "collaborator") {
          _push2(`<span${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 py-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"${_scopeId}></path></svg></span>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.studyRole == "owner" || $props.studyRole == "creator") {
          _push2(`<span${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 py-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"${_scopeId}></path></svg></span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(` ${ssrInterpolate($props.studyRole)}</span>`);
        if ($props.study.identifier) {
          _push2(`<span class="inline-flex pr-4 ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 py-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"${_scopeId}></path></svg><b${_scopeId}>${ssrInterpolate($props.study.identifier)}</b></span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
        _push2(ssrRenderComponent(_component_study_details, {
          ref: "studyDetailsElement",
          study: $props.study,
          role: $props.studyRole,
          "study-permissions": $props.studyPermissions
        }, null, _parent2, _scopeId));
        _push2(`</div><div class="flex flex-nowrap justify-between pb-3"${_scopeId}><div class="mt-2 flex items-center text-xs text-gray-400"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_CalendarIcon, {
          class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-300",
          "aria-hidden": "true"
        }, null, _parent2, _scopeId));
        _push2(` Updated on ${ssrInterpolate(_ctx.formatDateTime($props.study.updated_at))}</div><div${_scopeId}>`);
        if (!$props.study.is_public && $props.study.is_published) {
          _push2(`<span class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-red-800 capitalize"${_scopeId}> PUBLISHED -  `);
          if ($props.study.release_date) {
            _push2(`<b${_scopeId}>Release date: ${ssrInterpolate(_ctx.formatDate(
              $props.study.release_date
            ))}</b>`);
          } else {
            _push2(`<b${_scopeId}>Release date: ${ssrInterpolate(_ctx.formatDate(
              $props.project.release_date
            ))}</b>`);
          }
          _push2(`</span>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div></div></div></div></div>`);
      } else {
        return [
          $props.study.is_deleted ? (openBlock(), createBlock("div", {
            key: 0,
            class: "text-center px-3 py-1 bg-red-50 text-red-700 border-b"
          }, [
            createVNode("b", null, "Warning: "),
            createTextVNode(" This study is deleted. At the end of the 30-day period, this study and all of its resources will be deleted permanently and cannot be recovered. You can only restore a deleted study/project within the 30-day recovery period. ")
          ])) : createCommentVNode("", true),
          createVNode("div", null, [
            $props.study.is_public && $props.study.is_archived ? (openBlock(), createBlock("div", {
              key: 0,
              class: "text-center px-3 py-2 bg-yellow-50 text-yellow-700 border-b"
            }, [
              createVNode("b", null, "Warning: "),
              createTextVNode(" This project is archived. It is now read-only. ")
            ])) : createCommentVNode("", true),
            $props.study.is_public && !$props.study.is_archived ? (openBlock(), createBlock("div", {
              key: 1,
              class: "text-center px-3 py-2 bg-green-50 text-green-700 border-b"
            }, [
              createVNode("b", null, "Info: "),
              createTextVNode(" This project is published. You cannot edit a published project, please create a new version to updated the project. ")
            ])) : createCommentVNode("", true)
          ]),
          $props.study.is_public && $props.study.doi != null ? (openBlock(), createBlock("div", { key: 1 }, [
            createVNode(_component_Citation, {
              model: "study",
              doi: $props.study.doi
            }, null, 8, ["doi"])
          ])) : createCommentVNode("", true),
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between py-6" }, [
                createVNode("div", { class: "lg:flex lg:items-center lg:justify-between w-full" }, [
                  createVNode("div", { class: "flex-1 min-w-0" }, [
                    createVNode("nav", {
                      class: "flex",
                      "aria-label": "Breadcrumb"
                    }, [
                      createVNode("ol", {
                        role: "list",
                        class: "flex items-center space-x-4"
                      }, [
                        createVNode("li", null, [
                          createVNode("div", { class: "flex" }, [
                            createVNode(_component_Link, {
                              href: _ctx.route("dashboard"),
                              class: "text-sm font-medium text-gray-500 hover:text-gray-700"
                            }, {
                              default: withCtx(() => [
                                createTextVNode("Dashboard")
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])
                        ]),
                        createVNode("li", null, [
                          createVNode("div", { class: "flex items-center" }, [
                            createVNode(_component_ChevronRightIcon, {
                              class: "flex-shrink-0 h-5 w-5 text-gray-400",
                              "aria-hidden": "true"
                            }),
                            createVNode(_component_Link, {
                              href: _ctx.route(
                                "dashboard.projects",
                                [$props.project.id]
                              ),
                              class: "ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString($props.project.name), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "flex pr-20 mt-2 cursor-pointer items-center text-xl text-gray-700 font-bold" }, [
                      createVNode(_component_StarIcon, {
                        class: [
                          $props.study.is_bookmarked ? "text-yellow-400" : "text-gray-200",
                          "h-5 w-5 flex-shrink-0 -ml-1 mr-1"
                        ],
                        "aria-hidden": "true",
                        onClick: $options.toogleStarred
                      }, null, 8, ["class", "onClick"]),
                      createTextVNode(" " + toDisplayString($props.study.name) + " ", 1),
                      $options.canUpdateStudy ? (openBlock(), createBlock("button", {
                        key: 0,
                        type: "button",
                        class: "inline-flex items-center shadow-sm px-4 py-1.5 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                        onClick: $options.toggleDetails
                      }, [
                        (openBlock(), createBlock("svg", {
                          xmlns: "http://www.w3.org/2000/svg",
                          viewBox: "0 0 20 20",
                          fill: "currentColor",
                          "aria-hidden": "true",
                          class: "w-4 h-4 mr-2 text-gray-600"
                        }, [
                          createVNode("path", { d: "M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" })
                        ])),
                        createVNode("span", null, "Edit")
                      ], 8, ["onClick"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6" }, [
                      !$props.study.is_deleted ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-2 flex items-center text-sm text-gray-700"
                      }, [
                        createVNode(_component_access_dialogue, {
                          "available-roles": $props.availableRoles,
                          role: $props.studyRole,
                          team: $props.team,
                          study: $props.study,
                          members: $props.members,
                          project: $props.project,
                          model: $props.model,
                          "called-from": "studyView"
                        }, null, 8, ["available-roles", "role", "team", "study", "members", "project", "model"]),
                        createVNode("a", {
                          class: "cursor-pointer inline-flex items-center",
                          onClick: $options.toggleDetails
                        }, [
                          (openBlock(), createBlock("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            viewBox: "0 0 24 24",
                            class: "w-4 h-4"
                          }, [
                            createVNode("path", {
                              d: "M4 15a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a1 1 0 0 1 .7.3L13.42 5H21a1 1 0 0 1 .9 1.45L19.61 11l2.27 4.55A1 1 0 0 1 21 17h-8a1 1 0 0 1-.7-.3L10.58 15H4z",
                              class: "fill-current text-gray-400"
                            }),
                            createVNode("rect", {
                              width: "2",
                              height: "20",
                              x: "2",
                              y: "2",
                              rx: "1",
                              class: "fill-current text-gray-600"
                            })
                          ])),
                          createVNode("span", { class: "ml-2" }, "View details")
                        ], 8, ["onClick"]),
                        createVNode("div", { class: "ml-3" }, [
                          $props.study.is_public ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: "inline-flex items-center"
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
                            ])),
                            createVNode("span", { class: "ml-2" }, "Public")
                          ])) : (openBlock(), createBlock("span", {
                            key: 1,
                            class: "inline-flex items-center"
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
                              style: { "enable-background": "new 0\n                                                            0 512 512" },
                              "xml:space": "preserve"
                            }, [
                              createVNode("g", null, [
                                createVNode("g", null, [
                                  createVNode("path", { d: "M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32\n                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333\n                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292\n                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51\n                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667\n                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64\n                                s85.333,38.281,85.333,85.333V192z" })
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
                            ])),
                            createVNode("span", { class: "ml-2" }, "Private")
                          ]))
                        ])
                      ])) : createCommentVNode("", true),
                      createVNode("div", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                        createVNode("span", { class: "capitalize inline-flex pr-4 ml-7 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, [
                          $props.studyRole == "reviewer" ? (openBlock(), createBlock("span", { key: 0 }, [
                            (openBlock(), createBlock("svg", {
                              xmlns: "http://www.w3.org/2000/svg",
                              class: "h-6 w-6 py-1 mr-1",
                              fill: "none",
                              viewBox: "0 0 24 24",
                              stroke: "currentColor",
                              "stroke-width": "2"
                            }, [
                              createVNode("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                d: "M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                              }),
                              createVNode("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                d: "M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                              })
                            ]))
                          ])) : createCommentVNode("", true),
                          $props.studyRole == "collaborator" ? (openBlock(), createBlock("span", { key: 1 }, [
                            (openBlock(), createBlock("svg", {
                              xmlns: "http://www.w3.org/2000/svg",
                              class: "h-6 w-6 py-1 mr-1",
                              fill: "none",
                              viewBox: "0 0 24 24",
                              stroke: "currentColor",
                              "stroke-width": "2"
                            }, [
                              createVNode("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                d: "M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                              })
                            ]))
                          ])) : createCommentVNode("", true),
                          $props.studyRole == "owner" || $props.studyRole == "creator" ? (openBlock(), createBlock("span", { key: 2 }, [
                            (openBlock(), createBlock("svg", {
                              xmlns: "http://www.w3.org/2000/svg",
                              class: "h-6 w-6 py-1 mr-1",
                              fill: "none",
                              viewBox: "0 0 24 24",
                              stroke: "currentColor",
                              "stroke-width": "2"
                            }, [
                              createVNode("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                d: "M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"
                              })
                            ]))
                          ])) : createCommentVNode("", true),
                          createTextVNode(" " + toDisplayString($props.studyRole), 1)
                        ]),
                        $props.study.identifier ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "inline-flex pr-4 ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                        }, [
                          (openBlock(), createBlock("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            class: "h-6 w-6 py-1",
                            fill: "none",
                            viewBox: "0 0 24 24",
                            stroke: "currentColor",
                            "stroke-width": "2"
                          }, [
                            createVNode("path", {
                              "stroke-linecap": "round",
                              "stroke-linejoin": "round",
                              d: "M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"
                            })
                          ])),
                          createVNode("b", null, toDisplayString($props.study.identifier), 1)
                        ])) : createCommentVNode("", true)
                      ]),
                      createVNode(_component_study_details, {
                        ref: "studyDetailsElement",
                        study: $props.study,
                        role: $props.studyRole,
                        "study-permissions": $props.studyPermissions
                      }, null, 8, ["study", "role", "study-permissions"])
                    ]),
                    createVNode("div", { class: "flex flex-nowrap justify-between pb-3" }, [
                      createVNode("div", { class: "mt-2 flex items-center text-xs text-gray-400" }, [
                        createVNode(_component_CalendarIcon, {
                          class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-300",
                          "aria-hidden": "true"
                        }),
                        createTextVNode(" Updated on " + toDisplayString(_ctx.formatDateTime($props.study.updated_at)), 1)
                      ]),
                      createVNode("div", null, [
                        !$props.study.is_public && $props.study.is_published ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-red-800 capitalize"
                        }, [
                          createTextVNode(" PUBLISHED -  "),
                          $props.study.release_date ? (openBlock(), createBlock("b", { key: 0 }, "Release date: " + toDisplayString(_ctx.formatDate(
                            $props.study.release_date
                          )), 1)) : (openBlock(), createBlock("b", { key: 1 }, "Release date: " + toDisplayString(_ctx.formatDate(
                            $props.project.release_date
                          )), 1))
                        ])) : createCommentVNode("", true)
                      ])
                    ])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="pb-12 pt-6 px-10"${_scopeId}>`);
        ssrRenderSlot(_ctx.$slots, "scontent", {}, null, _push2, _parent2, _scopeId);
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "pb-12 pt-6 px-10" }, [
            renderSlot(_ctx.$slots, "scontent")
          ])
        ];
      }
    }),
    _: 3
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Study/Layout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const StudyLayout = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  StudyLayout as default
};
