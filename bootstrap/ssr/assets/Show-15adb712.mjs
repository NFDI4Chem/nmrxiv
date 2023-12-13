import { A as AppLayout } from "./AppLayout-b93d1c11.mjs";
import { A as AccessDialogue } from "./AccessDialogue-1fdd1bde.mjs";
import { Link, router } from "@inertiajs/vue3";
import StudyIndex from "./Index-a6c631d2.mjs";
import ProjectDetails from "./Details-5c390753.mjs";
import { ref, resolveComponent, withCtx, createTextVNode, useSSRContext, mergeProps, openBlock, createBlock, createVNode, createCommentVNode, toDisplayString, Fragment, renderList } from "vue";
import { StarIcon, PencilIcon, CalendarIcon } from "@heroicons/vue/24/solid";
import { M as ManageAuthor, a as ManageCitation } from "./ManageCitation-00abf851.mjs";
import { T as ToolTip } from "./ToolTip-cf9882a3.mjs";
import { C as Citation } from "./Citation-80069afd.mjs";
import { MagnifyingGlassIcon } from "@heroicons/vue/20/solid";
import { ChevronRightIcon, UsersIcon } from "@heroicons/vue/24/outline";
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, Dialog, DialogPanel, TransitionChild, TransitionRoot } from "@headlessui/vue";
import Datepicker from "@vuepic/vue-datepicker";
/* empty css                */import { ssrRenderAttrs, ssrRenderAttr, ssrRenderComponent, ssrIncludeBooleanAttr, ssrInterpolate, ssrRenderStyle, ssrRenderList } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { A as AuthorCard, C as CitationCard } from "./CitationCard-1c6db289.mjs";
import { D as DOIBadge } from "./DOIBadge-164e4f67.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@vueuse/core";
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
import "./InputError-6ae0b65c.mjs";
import "dropzone";
import "axios-retry";
import "./ConfirmationModal-d0b47856.mjs";
import "./DangerButton-9c23157c.mjs";
import "./ActionMessage-3207b224.mjs";
import "./ActionSection-c1b53bcc.mjs";
import "./SectionTitle-70590d9c.mjs";
import "./Input-f9a8cf77.mjs";
import "./Label-c5144ba4.mjs";
import "./SectionBorder-7d1f222a.mjs";
import "./Create-05bb83de.mjs";
import "./StudyCard-ce624709.mjs";
import "./Depictor2D-b60253ae.mjs";
import "./Activity-32434661.mjs";
import "vue3-colorpicker";
/* empty css                 */import "./LoadingButton-8ae902b0.mjs";
import "vuedraggable";
import "axios";
const _sfc_main$1 = {
  components: {
    Link,
    MagnifyingGlassIcon,
    ChevronRightIcon,
    UsersIcon,
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
    Datepicker
  },
  props: ["project"],
  data() {
    return {
      open: ref(false),
      query: ref(""),
      errors: null,
      status: null,
      form: this.$inertia.form({
        _method: "POST",
        conditions: false,
        terms: false,
        releaseDate: this.setReleaseDate()
      })
    };
  },
  mounted() {
    if (this.project && this.project.status != "" && this.project.status != "complete" && this.project.status != null) {
      this.status = this.project.status;
      this.open = true;
      this.trackProject();
    }
  },
  methods: {
    setReleaseDate() {
      var current_date = /* @__PURE__ */ new Date();
      var relase_date = /* @__PURE__ */ new Date();
      relase_date.setDate(current_date.getDate());
      return relase_date;
    },
    publish() {
      if (this.form.conditions && this.form.terms) {
        this.errors = null;
        axios.post(
          route("dashboard.project.publish", this.project.id),
          this.form
        ).catch((err) => {
          this.errors = err.response.data.errors;
        }).then((response) => {
          this.status = response.data.project.status;
          this.trackProject();
        });
      }
    },
    trackProject() {
      axios.get("/projects/status/" + this.project.id + "/queue").then((response) => {
        this.status = response.data.status;
        if (this.status != "complete") {
          setTimeout(() => this.trackProject(), 1e4);
        } else {
          return this.$inertia.visit(
            this.route("dashboard.projects", [this.project.id])
          );
        }
      });
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  if ($props.project.validation_status) {
    _push(`<div${ssrRenderAttrs(_attrs)}><a${ssrRenderAttr("href", "/publish/" + $props.project.id)} class="ml-4 inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-indigo-700 px-4 py-2 text-base font-medium text-white shadow-sm"> Publish </a></div>`);
  } else {
    _push(`<div${ssrRenderAttrs(_attrs)}>`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("dashboard.project.validation", [$props.project.id]),
      class: "text-gray-900 text-sm font-bold hover:text-blue-700"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(` Why can&#39;t I publish? `);
        } else {
          return [
            createTextVNode(" Why can't I publish? ")
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`<button${ssrIncludeBooleanAttr(true) ? " disabled" : ""} class="ml-4 inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-indigo-300 px-4 py-2 text-base font-medium cursor-not-allowed text-white shadow-sm"> Publish </button></div>`);
  }
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Publish.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Publish = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    Link,
    AppLayout,
    StudyIndex,
    ProjectDetails,
    StarIcon,
    PencilIcon,
    AccessDialogue,
    ManageAuthor,
    ToolTip,
    ManageCitation,
    CalendarIcon,
    Citation,
    Publish,
    AuthorCard,
    CitationCard,
    DOIBadge
  },
  props: [
    "project",
    "team",
    "members",
    "availableRoles",
    "projectPermissions",
    "role",
    "teamRole",
    "license"
  ],
  setup() {
    const projectDetailsElement = ref(null);
    const manageAuthorElement = ref(null);
    const manageCitationElement = ref(null);
    return {
      projectDetailsElement,
      manageAuthorElement,
      manageCitationElement
    };
  },
  data() {
    return {};
  },
  computed: {
    canDeleteProject() {
      return this.projectPermissions ? this.projectPermissions.canDeleteProject : false;
    },
    canUpdateProject() {
      return this.projectPermissions ? this.projectPermissions.canUpdateProject : false;
    }
  },
  mounted() {
    const urlSearchParams = new URLSearchParams(window.location.search);
    const params = Object.fromEntries(urlSearchParams.entries());
    let editOperation = params["edit"];
    if (editOperation) {
      if (editOperation == "license" || editOperation == "title" || editOperation == "description" || editOperation == "keywords" || editOperation == "profile_image") {
        this.toggleDetails();
      } else if (editOperation == "citation") {
        this.toggleManageCitation();
      } else if (editOperation == "authors") {
        this.toggleManageAuthor();
      }
    }
  },
  methods: {
    toogleStarred() {
      const url = "/projects/" + this.project.id + "/toggleStarred";
      axios.get(url).catch((err) => {
        if (err.response.status !== 200 || err.response.status !== 201) {
          throw new Error(
            `API call failed with status code: ${err.response.status}`
          );
        }
      }).then(function(response) {
        router.reload({ only: ["project"] });
      });
    },
    toggleDetails() {
      this.projectDetailsElement.toggleDetails();
    },
    toggleManageAuthor() {
      this.manageAuthorElement.toggleDialog();
    },
    toggleManageCitation() {
      this.manageCitationElement.toggleDialog();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Citation = resolveComponent("Citation");
  const _component_StarIcon = resolveComponent("StarIcon");
  const _component_access_dialogue = resolveComponent("access-dialogue");
  const _component_project_details = resolveComponent("project-details");
  const _component_manage_author = resolveComponent("manage-author");
  const _component_manage_citation = resolveComponent("manage-citation");
  const _component_Link = resolveComponent("Link");
  const _component_DOIBadge = resolveComponent("DOIBadge");
  const _component_CalendarIcon = resolveComponent("CalendarIcon");
  const _component_Publish = resolveComponent("Publish");
  const _component_PencilIcon = resolveComponent("PencilIcon");
  const _component_ToolTip = resolveComponent("ToolTip");
  const _component_citation_card = resolveComponent("citation-card");
  const _component_author_card = resolveComponent("author-card");
  const _component_study_index = resolveComponent("study-index");
  _push(ssrRenderComponent(_component_app_layout, mergeProps({
    title: $props.project.name
  }, _attrs), {
    header: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if ($props.project.is_deleted) {
          _push2(`<div class="text-center px-3 py-2 bg-red-50 text-red-700 border-b"${_scopeId}><b${_scopeId}>Warning: </b> This project is deleted. At the end of the 30-day period, this project and all of its resources will be deleted permanently and cannot be recovered. You can only restore a deleted project within the 30-day recovery period. </div>`);
        } else {
          _push2(`<!---->`);
        }
        if (!$props.project.is_public && $props.project.is_published) {
          _push2(`<div class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"${_scopeId}><b${_scopeId}>Info: </b> This project is published and set to be released on ${ssrInterpolate(_ctx.formatDate($props.project.release_date))}. You cannot edit a published project, please create a new version to updated the project. </div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.project.is_public) {
          _push2(`<div${_scopeId}>`);
          if ($props.project.is_archived) {
            _push2(`<div class="text-center px-3 py-2 bg-yellow-50 text-yellow-700 border-b"${_scopeId}><b${_scopeId}>Warning: </b> This project is archived. It is now read-only. </div>`);
          } else {
            _push2(`<div class="text-center px-3 py-2 bg-green-50 text-green-700 border-b"${_scopeId}><b${_scopeId}>Info: </b> This project is published. You cannot edit a published project, please create a new version to updated the project. </div>`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if ($props.project.is_public && $props.project.doi != null) {
          _push2(`<div${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Citation, {
            model: "project",
            doi: $props.project.doi
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="bg-white border-b"${_scopeId}><div class="px-12"${_scopeId}><div class="flex flex-nowrap justify-between pt-6 w-full"${_scopeId}><div class=""${_scopeId}><div class="flex pr-20 cursor-pointer items-center text-xl text-gray-700 font-bold"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_StarIcon, {
          class: [
            $props.project.is_bookmarked ? "text-yellow-400" : "text-gray-200",
            "h-5 w-5 flex-shrink-0 -ml-1 mr-1"
          ],
          "aria-hidden": "true",
          onClick: $options.toogleStarred
        }, null, _parent2, _scopeId));
        _push2(` ${ssrInterpolate($props.project.name)} `);
        if ($options.canUpdateProject) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-4 h-4 mr-2 text-gray-600"${_scopeId}><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"${_scopeId}></path></svg><span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div>`);
        if (!$props.project.is_deleted) {
          _push2(`<div class="inline-flex items-center mt-3"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_access_dialogue, {
            "available-roles": $props.availableRoles,
            role: $props.role,
            team: $props.team,
            members: $props.members,
            project: $props.project,
            "called-from": "projectView",
            model: "project"
          }, null, _parent2, _scopeId));
          _push2(`<a class="cursor-pointer hover:text-teal-900 inline-flex items-center ml-7"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4"${_scopeId}><path d="M4 15a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7a1 1 0 0 1 .7.3L13.42 5H21a1 1 0 0 1 .9 1.45L19.61 11l2.27 4.55A1 1 0 0 1 21 17h-8a1 1 0 0 1-.7-.3L10.58 15H4z" class="fill-current text-gray-400"${_scopeId}></path><rect width="2" height="20" x="2" y="2" rx="1" class="fill-current text-gray-600"${_scopeId}></rect></svg><span class="ml-2"${_scopeId}>View details</span></a><a${_scopeId}>`);
          if ($props.project.is_public) {
            _push2(`<span class="inline-flex items-center"${_scopeId}><svg class="h-3 w-3 ml-4 text-green-400 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="512" height="512"${_scopeId}><g id="globe"${_scopeId}><path d="M53.85,47.85A27,27,0,0,1,24,57.8V56l3-3V49l4-4V42l4,4h5l2-2h8Z"${_scopeId}></path><path d="M42,20.59v2.56L38.07,27H31l-5.36,5.26L31,37.51v5.06L27.44,39H22.86L16,32.11V24.2L11.8,20h-4A27,27,0,0,1,32,5a26.55,26.55,0,0,1,7.06.94L36,9H30v4l4,4h4.33Z"${_scopeId}></path><path d="M32,60A28,28,0,1,1,60,32,28,28,0,0,1,32,60ZM32,6A26,26,0,1,0,58,32,26,26,0,0,0,32,6Z"${_scopeId}></path></g></svg><span class="ml-2"${_scopeId}>Public</span></span>`);
          } else {
            _push2(`<span class="inline-flex ml-7 items-center"${_scopeId}><svg id="Capa_1" class="h-3 w-3 text-gray-400 inline" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="${ssrRenderStyle({ "enable-background": "new 0 0 512\n                                                    512" })}" xml:space="preserve"${_scopeId}><g${_scopeId}><g${_scopeId}><path d="M437.333,192h-32v-42.667C405.333,66.99,338.344,0,256,0S106.667,66.99,106.667,149.333V192h-32
                                C68.771,192,64,196.771,64,202.667v266.667C64,492.865,83.135,512,106.667,512h298.667C428.865,512,448,492.865,448,469.333
                                V202.667C448,196.771,443.229,192,437.333,192z M287.938,414.823c0.333,3.01-0.635,6.031-2.656,8.292
                                c-2.021,2.26-4.917,3.552-7.948,3.552h-42.667c-3.031,0-5.927-1.292-7.948-3.552c-2.021-2.26-2.99-5.281-2.656-8.292l6.729-60.51
                                c-10.927-7.948-17.458-20.521-17.458-34.313c0-23.531,19.135-42.667,42.667-42.667s42.667,19.135,42.667,42.667
                                c0,13.792-6.531,26.365-17.458,34.313L287.938,414.823z M341.333,192H170.667v-42.667C170.667,102.281,208.948,64,256,64
                                s85.333,38.281,85.333,85.333V192z"${_scopeId}></path></g></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g><g${_scopeId}></g></svg><span class="ml-2"${_scopeId}>Private</span></span>`);
          }
          _push2(`</a>`);
          _push2(ssrRenderComponent(_component_project_details, {
            ref: "projectDetailsElement",
            role: $props.role,
            "project-permissions": $props.projectPermissions,
            project: $props.project
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_manage_author, {
            ref: "manageAuthorElement",
            project: $props.project
          }, null, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_manage_citation, {
            ref: "manageCitationElement",
            project: $props.project
          }, null, _parent2, _scopeId));
          _push2(`<span class="capitalize inline-flex pr-4 ml-7 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"${_scopeId}>`);
          if ($props.role == "reviewer") {
            _push2(`<span${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 py-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"${_scopeId}></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"${_scopeId}></path></svg></span>`);
          } else {
            _push2(`<!---->`);
          }
          if ($props.role == "collaborator") {
            _push2(`<span${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 py-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"${_scopeId}></path></svg></span>`);
          } else {
            _push2(`<!---->`);
          }
          if ($props.role == "owner" || $props.role == "creator") {
            _push2(`<span${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 py-1 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"${_scopeId}></path></svg></span>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(` ${ssrInterpolate($props.role)}</span>`);
          if ($props.project.identifier) {
            _push2(`<span class="inline-flex pr-4 ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 py-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"${_scopeId}></path></svg><b${_scopeId}>${ssrInterpolate($props.project.identifier)}</b></span>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div><div class="flex-nowrap"${_scopeId}>`);
        if ($options.canDeleteProject) {
          _push2(ssrRenderComponent(_component_Link, {
            href: _ctx.route(
              "dashboard.project.settings",
              $props.project.id
            ),
            class: "text-sm flex-nowrap text-gray-800 font-bold"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Project Settings `);
              } else {
                return [
                  createTextVNode(" Project Settings ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="flex flex-nowrap justify-between pb-3"${_scopeId}>`);
        if ($props.project.identifier) {
          _push2(`<div class="text-gray-400 mt-2"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_DOIBadge, {
            doi: $props.project.doi
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="mt-2 flex items-center text-xs text-gray-400"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_CalendarIcon, {
          class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-300",
          "aria-hidden": "true"
        }, null, _parent2, _scopeId));
        _push2(` Updated on ${ssrInterpolate(_ctx.formatDateTime($props.project.updated_at))}</div>`);
        if (!$props.project.is_public && !$props.project.is_published && !$props.project.is_deleted) {
          _push2(`<div class="flex-nowrap"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Publish, { project: $props.project }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        if (!$props.project.is_public && $props.project.is_published) {
          _push2(`<div${_scopeId}><span class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-red-800 capitalize"${_scopeId}> PUBLISHED -  <b${_scopeId}>Release date: ${ssrInterpolate(_ctx.formatDate($props.project.release_date))}</b></span></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div>`);
      } else {
        return [
          $props.project.is_deleted ? (openBlock(), createBlock("div", {
            key: 0,
            class: "text-center px-3 py-2 bg-red-50 text-red-700 border-b"
          }, [
            createVNode("b", null, "Warning: "),
            createTextVNode(" This project is deleted. At the end of the 30-day period, this project and all of its resources will be deleted permanently and cannot be recovered. You can only restore a deleted project within the 30-day recovery period. ")
          ])) : createCommentVNode("", true),
          !$props.project.is_public && $props.project.is_published ? (openBlock(), createBlock("div", {
            key: 1,
            class: "text-center px-3 py-2 bg-green-50 text-green-700 border-b"
          }, [
            createVNode("b", null, "Info: "),
            createTextVNode(" This project is published and set to be released on " + toDisplayString(_ctx.formatDate($props.project.release_date)) + ". You cannot edit a published project, please create a new version to updated the project. ", 1)
          ])) : createCommentVNode("", true),
          $props.project.is_public ? (openBlock(), createBlock("div", { key: 2 }, [
            $props.project.is_archived ? (openBlock(), createBlock("div", {
              key: 0,
              class: "text-center px-3 py-2 bg-yellow-50 text-yellow-700 border-b"
            }, [
              createVNode("b", null, "Warning: "),
              createTextVNode(" This project is archived. It is now read-only. ")
            ])) : (openBlock(), createBlock("div", {
              key: 1,
              class: "text-center px-3 py-2 bg-green-50 text-green-700 border-b"
            }, [
              createVNode("b", null, "Info: "),
              createTextVNode(" This project is published. You cannot edit a published project, please create a new version to updated the project. ")
            ]))
          ])) : createCommentVNode("", true),
          $props.project.is_public && $props.project.doi != null ? (openBlock(), createBlock("div", { key: 3 }, [
            createVNode(_component_Citation, {
              model: "project",
              doi: $props.project.doi
            }, null, 8, ["doi"])
          ])) : createCommentVNode("", true),
          createVNode("div", { class: "bg-white border-b" }, [
            createVNode("div", { class: "px-12" }, [
              createVNode("div", { class: "flex flex-nowrap justify-between pt-6 w-full" }, [
                createVNode("div", { class: "" }, [
                  createVNode("div", { class: "flex pr-20 cursor-pointer items-center text-xl text-gray-700 font-bold" }, [
                    createVNode(_component_StarIcon, {
                      class: [
                        $props.project.is_bookmarked ? "text-yellow-400" : "text-gray-200",
                        "h-5 w-5 flex-shrink-0 -ml-1 mr-1"
                      ],
                      "aria-hidden": "true",
                      onClick: $options.toogleStarred
                    }, null, 8, ["class", "onClick"]),
                    createTextVNode(" " + toDisplayString($props.project.name) + " ", 1),
                    $options.canUpdateProject ? (openBlock(), createBlock("button", {
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
                  !$props.project.is_deleted ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "inline-flex items-center mt-3"
                  }, [
                    createVNode(_component_access_dialogue, {
                      "available-roles": $props.availableRoles,
                      role: $props.role,
                      team: $props.team,
                      members: $props.members,
                      project: $props.project,
                      "called-from": "projectView",
                      model: "project"
                    }, null, 8, ["available-roles", "role", "team", "members", "project"]),
                    createVNode("a", {
                      class: "cursor-pointer hover:text-teal-900 inline-flex items-center ml-7",
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
                    createVNode("a", null, [
                      $props.project.is_public ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "inline-flex items-center"
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-3 w-3 ml-4 text-green-400 inline",
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
                        class: "inline-flex ml-7 items-center"
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
                    ]),
                    createVNode(_component_project_details, {
                      ref: "projectDetailsElement",
                      role: $props.role,
                      "project-permissions": $props.projectPermissions,
                      project: $props.project
                    }, null, 8, ["role", "project-permissions", "project"]),
                    createVNode(_component_manage_author, {
                      ref: "manageAuthorElement",
                      project: $props.project
                    }, null, 8, ["project"]),
                    createVNode(_component_manage_citation, {
                      ref: "manageCitationElement",
                      project: $props.project
                    }, null, 8, ["project"]),
                    createVNode("span", { class: "capitalize inline-flex pr-4 ml-7 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" }, [
                      $props.role == "reviewer" ? (openBlock(), createBlock("span", { key: 0 }, [
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
                      $props.role == "collaborator" ? (openBlock(), createBlock("span", { key: 1 }, [
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
                      $props.role == "owner" || $props.role == "creator" ? (openBlock(), createBlock("span", { key: 2 }, [
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
                      createTextVNode(" " + toDisplayString($props.role), 1)
                    ]),
                    $props.project.identifier ? (openBlock(), createBlock("span", {
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
                      createVNode("b", null, toDisplayString($props.project.identifier), 1)
                    ])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true)
                ]),
                createVNode("div", { class: "flex-nowrap" }, [
                  $options.canDeleteProject ? (openBlock(), createBlock(_component_Link, {
                    key: 0,
                    href: _ctx.route(
                      "dashboard.project.settings",
                      $props.project.id
                    ),
                    class: "text-sm flex-nowrap text-gray-800 font-bold"
                  }, {
                    default: withCtx(() => [
                      createTextVNode(" Project Settings ")
                    ]),
                    _: 1
                  }, 8, ["href"])) : createCommentVNode("", true)
                ])
              ]),
              createVNode("div", { class: "flex flex-nowrap justify-between pb-3" }, [
                $props.project.identifier ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "text-gray-400 mt-2"
                }, [
                  createVNode(_component_DOIBadge, {
                    doi: $props.project.doi
                  }, null, 8, ["doi"])
                ])) : createCommentVNode("", true),
                createVNode("div", { class: "mt-2 flex items-center text-xs text-gray-400" }, [
                  createVNode(_component_CalendarIcon, {
                    class: "flex-shrink-0 mr-1.5 h-5 w-5 text-gray-300",
                    "aria-hidden": "true"
                  }),
                  createTextVNode(" Updated on " + toDisplayString(_ctx.formatDateTime($props.project.updated_at)), 1)
                ]),
                !$props.project.is_public && !$props.project.is_published && !$props.project.is_deleted ? (openBlock(), createBlock("div", {
                  key: 1,
                  class: "flex-nowrap"
                }, [
                  createVNode(_component_Publish, { project: $props.project }, null, 8, ["project"])
                ])) : createCommentVNode("", true),
                !$props.project.is_public && $props.project.is_published ? (openBlock(), createBlock("div", { key: 2 }, [
                  createVNode("span", { class: "ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-red-800 capitalize" }, [
                    createTextVNode(" PUBLISHED -  "),
                    createVNode("b", null, "Release date: " + toDisplayString(_ctx.formatDate($props.project.release_date)), 1)
                  ])
                ])) : createCommentVNode("", true)
              ])
            ])
          ])
        ];
      }
    }),
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="p-12 pt-8"${_scopeId}><div${_scopeId}><div class="mb-8"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"${_scopeId}> Description </span>`);
        if ($options.canUpdateProject) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><dd class="mt-1 text-gray-900 space-y-5"${_scopeId}><p style="${ssrRenderStyle({ "max-width": "100ch !important" })}" class="prose mt-1 text-sm text-blue-gray-500"${_scopeId}>${_ctx.md($props.project.description)}</p></dd></div><div class="mb-8"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"${_scopeId}> Keywords </span>`);
        if ($options.canUpdateProject) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><dd class="mt-1 text-md text-gray-900 space-y-5"${_scopeId}><p${_scopeId}><!--[-->`);
        ssrRenderList($props.project.tags, (tag) => {
          _push2(`<span class="mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"${_scopeId}>${ssrInterpolate(tag.name["en"])}</span>`);
        });
        _push2(`<!--]--></p></dd></div><div class="mb-4"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"${_scopeId}> License </span>`);
        if ($options.canUpdateProject) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div${_scopeId}>`);
        if ($props.license) {
          _push2(`<dd class="mt-1 text-gray-900 space-y-5"${_scopeId}><p style="${ssrRenderStyle({ "max-width": "100ch !important" })}" class="prose mt-1 text-sm text-blue-gray-500"${_scopeId}>${ssrInterpolate($props.license.title)} `);
          if ($props.project.license_id) {
            _push2(ssrRenderComponent(_component_ToolTip, {
              class: "inline h-4 w-4 ml-0",
              text: $props.license.description
            }, null, _parent2, _scopeId));
          } else {
            _push2(`<!---->`);
          }
          _push2(`</p></dd>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="mb-8"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"${_scopeId}> Citation </span>`);
        if ($options.canUpdateProject) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><dd class="mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto"${_scopeId}><div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_citation_card, {
          citations: $props.project.citations
        }, null, _parent2, _scopeId));
        _push2(`</div></dd></div><div class="mb-8"${_scopeId}><div class="relative"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-300"${_scopeId}></div></div><div class="relative flex items-center justify-between"${_scopeId}><span class="px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500"${_scopeId}> Author </span>`);
        if ($options.canUpdateProject) {
          _push2(`<button type="button" class="inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>Edit</span></button>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><dd class="mt-2 text-md text-gray-900 space-y-5"${_scopeId}><div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-3"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_author_card, {
          authors: $props.project.authors
        }, null, _parent2, _scopeId));
        _push2(`</div></dd></div><div class="relative py-5"${_scopeId}><div class="absolute inset-0 flex items-center" aria-hidden="true"${_scopeId}><div class="w-full border-t border-gray-100"${_scopeId}></div></div></div>`);
        _push2(ssrRenderComponent(_component_study_index, {
          editable: _ctx.editable,
          project: $props.project,
          role: $props.role,
          "team-role": $props.teamRole
        }, null, _parent2, _scopeId));
        _push2(`</div></div>`);
      } else {
        return [
          createVNode("div", { class: "p-12 pt-8" }, [
            createVNode("div", null, [
              createVNode("div", { class: "mb-8" }, [
                createVNode("div", { class: "relative" }, [
                  createVNode("div", {
                    class: "absolute inset-0 flex items-center",
                    "aria-hidden": "true"
                  }, [
                    createVNode("div", { class: "w-full border-t border-gray-300" })
                  ]),
                  createVNode("div", { class: "relative flex items-center justify-between" }, [
                    createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500" }, " Description "),
                    $options.canUpdateProject ? (openBlock(), createBlock("button", {
                      key: 0,
                      type: "button",
                      class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                      onClick: $options.toggleDetails
                    }, [
                      createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                      createVNode("span", null, "Edit")
                    ], 8, ["onClick"])) : createCommentVNode("", true)
                  ])
                ]),
                createVNode("dd", { class: "mt-1 text-gray-900 space-y-5" }, [
                  createVNode("p", {
                    style: { "max-width": "100ch !important" },
                    class: "prose mt-1 text-sm text-blue-gray-500",
                    innerHTML: _ctx.md($props.project.description)
                  }, null, 8, ["innerHTML"])
                ])
              ]),
              createVNode("div", { class: "mb-8" }, [
                createVNode("div", { class: "relative" }, [
                  createVNode("div", {
                    class: "absolute inset-0 flex items-center",
                    "aria-hidden": "true"
                  }, [
                    createVNode("div", { class: "w-full border-t border-gray-300" })
                  ]),
                  createVNode("div", { class: "relative flex items-center justify-between" }, [
                    createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500" }, " Keywords "),
                    $options.canUpdateProject ? (openBlock(), createBlock("button", {
                      key: 0,
                      type: "button",
                      class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                      onClick: $options.toggleDetails
                    }, [
                      createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                      createVNode("span", null, "Edit")
                    ], 8, ["onClick"])) : createCommentVNode("", true)
                  ])
                ]),
                createVNode("dd", { class: "mt-1 text-md text-gray-900 space-y-5" }, [
                  createVNode("p", null, [
                    (openBlock(true), createBlock(Fragment, null, renderList($props.project.tags, (tag) => {
                      return openBlock(), createBlock("span", {
                        key: tag.id,
                        class: "mt-1 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                      }, toDisplayString(tag.name["en"]), 1);
                    }), 128))
                  ])
                ])
              ]),
              createVNode("div", { class: "mb-4" }, [
                createVNode("div", { class: "relative" }, [
                  createVNode("div", {
                    class: "absolute inset-0 flex items-center",
                    "aria-hidden": "true"
                  }, [
                    createVNode("div", { class: "w-full border-t border-gray-300" })
                  ]),
                  createVNode("div", { class: "relative flex items-center justify-between" }, [
                    createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500" }, " License "),
                    $options.canUpdateProject ? (openBlock(), createBlock("button", {
                      key: 0,
                      type: "button",
                      class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                      onClick: $options.toggleDetails
                    }, [
                      createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                      createVNode("span", null, "Edit")
                    ], 8, ["onClick"])) : createCommentVNode("", true)
                  ])
                ]),
                createVNode("div", null, [
                  $props.license ? (openBlock(), createBlock("dd", {
                    key: 0,
                    class: "mt-1 text-gray-900 space-y-5"
                  }, [
                    createVNode("p", {
                      style: { "max-width": "100ch !important" },
                      class: "prose mt-1 text-sm text-blue-gray-500"
                    }, [
                      createTextVNode(toDisplayString($props.license.title) + " ", 1),
                      $props.project.license_id ? (openBlock(), createBlock(_component_ToolTip, {
                        key: 0,
                        class: "inline h-4 w-4 ml-0",
                        text: $props.license.description
                      }, null, 8, ["text"])) : createCommentVNode("", true)
                    ])
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode("div", { class: "mb-8" }, [
                createVNode("div", { class: "relative" }, [
                  createVNode("div", {
                    class: "absolute inset-0 flex items-center",
                    "aria-hidden": "true"
                  }, [
                    createVNode("div", { class: "w-full border-t border-gray-300" })
                  ]),
                  createVNode("div", { class: "relative flex items-center justify-between" }, [
                    createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500" }, " Citation "),
                    $options.canUpdateProject ? (openBlock(), createBlock("button", {
                      key: 0,
                      type: "button",
                      class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                      onClick: $options.toggleManageCitation
                    }, [
                      createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                      createVNode("span", null, "Edit")
                    ], 8, ["onClick"])) : createCommentVNode("", true)
                  ])
                ]),
                createVNode("dd", { class: "mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto" }, [
                  createVNode("div", { class: "mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2" }, [
                    createVNode(_component_citation_card, {
                      citations: $props.project.citations
                    }, null, 8, ["citations"])
                  ])
                ])
              ]),
              createVNode("div", { class: "mb-8" }, [
                createVNode("div", { class: "relative" }, [
                  createVNode("div", {
                    class: "absolute inset-0 flex items-center",
                    "aria-hidden": "true"
                  }, [
                    createVNode("div", { class: "w-full border-t border-gray-300" })
                  ]),
                  createVNode("div", { class: "relative flex items-center justify-between" }, [
                    createVNode("span", { class: "px-3 -ml-4 rounded text-sm bg-gray-100 font-medium text-gray-500" }, " Author "),
                    $options.canUpdateProject ? (openBlock(), createBlock("button", {
                      key: 0,
                      type: "button",
                      class: "inline-flex items-center shadow-sm px-4 py-1.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                      onClick: $options.toggleManageAuthor
                    }, [
                      createVNode(_component_PencilIcon, { class: "w-4 h-4 mr-1 text-gray-600" }),
                      createVNode("span", null, "Edit")
                    ], 8, ["onClick"])) : createCommentVNode("", true)
                  ])
                ]),
                createVNode("dd", { class: "mt-2 text-md text-gray-900 space-y-5" }, [
                  createVNode("div", { class: "mt-1 grid grid-cols-1 gap-4 sm:grid-cols-3" }, [
                    createVNode(_component_author_card, {
                      authors: $props.project.authors
                    }, null, 8, ["authors"])
                  ])
                ])
              ]),
              createVNode("div", { class: "relative py-5" }, [
                createVNode("div", {
                  class: "absolute inset-0 flex items-center",
                  "aria-hidden": "true"
                }, [
                  createVNode("div", { class: "w-full border-t border-gray-100" })
                ])
              ]),
              createVNode(_component_study_index, {
                editable: _ctx.editable,
                project: $props.project,
                role: $props.role,
                "team-role": $props.teamRole
              }, null, 8, ["editable", "project", "role", "team-role"])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Project/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Show = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Show as default
};
