import ProjectLayout from "./Layout-3465446f.mjs";
import { A as AuthorCard, C as CitationCard } from "./CitationCard-1c6db289.mjs";
import "ontology-elements/dist/index.js";
import { C as Citation } from "./Citation-80069afd.mjs";
import { Head } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createVNode, openBlock, createBlock, createCommentVNode, Fragment, renderList, toDisplayString, resolveDynamicComponent, createTextVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderStyle, ssrRenderList, ssrRenderAttr, ssrInterpolate, ssrRenderVNode } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import "./AppLayout-b93d1c11.mjs";
import "./ApplicationLogo-88e5413e.mjs";
import "@meilisearch/instant-meilisearch";
import "@heroicons/vue/24/solid";
import "@vueuse/core";
import "@heroicons/vue/24/outline";
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
import "./DOIBadge-164e4f67.mjs";
import "axios";
const _sfc_main = {
  components: {
    ProjectLayout,
    AuthorCard,
    CitationCard,
    Citation,
    Head
  },
  props: ["project", "tab"],
  data() {
    return {
      schema: {}
    };
  },
  computed: {},
  mounted() {
    axios.get(route("bioschemas.id", this.project.data.identifier)).then((response) => {
      this.schema = response.data;
    });
  },
  methods: {}
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_project_layout = resolveComponent("project-layout");
  const _component_Citation = resolveComponent("Citation");
  const _component_author_card = resolveComponent("author-card");
  const _component_citation_card = resolveComponent("citation-card");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, {
    title: $props.project.data.name
  }, null, _parent));
  _push(ssrRenderComponent(_component_project_layout, {
    project: $props.project,
    "selected-tab": $props.tab
  }, {
    "project-content": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6"${_scopeId}>`);
        if ($props.project.data.is_public && $props.project.data.doi != null) {
          _push2(`<div class="-mx-4"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Citation, {
            model: "project",
            doi: $props.project.data.doi
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="mt-2 space-y-4 divide-y divide-y-blue-gray-200"${_scopeId}><div${_scopeId}><h3 class="text-xl font-extrabold text-blue-gray-900"${_scopeId}> About project </h3><div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6"${_scopeId}><div class="col-span-6"${_scopeId}><p style="${ssrRenderStyle({ "max-width": "100ch !important" })}" class="prose mt-1 text-sm text-blue-gray-500"${_scopeId}>${_ctx.md($props.project.data.description)}</p></div></div></div>`);
        if ($props.project.data.species) {
          _push2(`<div class="pt-2"${_scopeId}><h3 class="text-xl font-extrabold text-blue-gray-900"${_scopeId}> Organism </h3><div class="pt-3"${_scopeId}><!--[-->`);
          ssrRenderList($props.project.data.species, (species, $index) => {
            _push2(`<div class="bg-gray-100 text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"${_scopeId}><ontology-term-annotation${ssrRenderAttr("annotation", species)}${_scopeId}></ontology-term-annotation></div>`);
          });
          _push2(`<!--]--></div></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="gap-y-6 sm:grid-cols-6 sm:gap-x-6"${_scopeId}><div class="pt-2 sm:col-span-6"${_scopeId}><h2 class="text-xl font-extrabold mb-3 text-blue-gray-900"${_scopeId}> Submitter(s) </h2></div><div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"${_scopeId}><!--[-->`);
        ssrRenderList($props.project.data.users, (user) => {
          _push2(`<div class="relative rounded-lg border border-gray-300 bg-white p-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"${_scopeId}><div class="flex-shrink-0"${_scopeId}><img class="h-10 w-10 rounded-full"${ssrRenderAttr("src", user.profile_photo_url)} alt=""${_scopeId}></div><div class="flex-1 min-w-0"${_scopeId}><a class="focus:outline-none"${_scopeId}><span class="absolute inset-0" aria-hidden="true"${_scopeId}></span><p class="text-sm font-medium text-gray-900"${_scopeId}>${ssrInterpolate(user.first_name + " " + user.last_name)}</p><p class="text-sm text-gray-500 truncate"${_scopeId}> @ ${ssrInterpolate(user.username)}</p></a></div></div>`);
        });
        _push2(`<!--]--></div>`);
        if ($props.project.data.authors && $props.project.data.authors.length > 0) {
          _push2(`<div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6"${_scopeId}><div class="sm:col-span-6"${_scopeId}><h2 class="text-xl font-extrabold mb-3 text-blue-gray-900"${_scopeId}> Author(s) </h2></div><div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_author_card, {
            authors: $props.project.data.authors
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
          if ($props.project.data.citations && $props.project.data.citations.length > 0) {
            _push2(`<div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6"${_scopeId}><div class="sm:col-span-6"${_scopeId}><h2 class="text-xl font-extrabold mb-3 text-blue-gray-900"${_scopeId}> Citation(s) </h2></div><dd class="mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto"${_scopeId}><div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_citation_card, {
              citations: $props.project.data.citations
            }, null, _parent2, _scopeId));
            _push2(`</div></dd></div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div></div>`);
      } else {
        return [
          createVNode("div", { class: "pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6" }, [
            $props.project.data.is_public && $props.project.data.doi != null ? (openBlock(), createBlock("div", {
              key: 0,
              class: "-mx-4"
            }, [
              createVNode(_component_Citation, {
                model: "project",
                doi: $props.project.data.doi
              }, null, 8, ["doi"])
            ])) : createCommentVNode("", true),
            createVNode("div", { class: "mt-2 space-y-4 divide-y divide-y-blue-gray-200" }, [
              createVNode("div", null, [
                createVNode("h3", { class: "text-xl font-extrabold text-blue-gray-900" }, " About project "),
                createVNode("div", { class: "grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6" }, [
                  createVNode("div", { class: "col-span-6" }, [
                    createVNode("p", {
                      style: { "max-width": "100ch !important" },
                      class: "prose mt-1 text-sm text-blue-gray-500",
                      innerHTML: _ctx.md($props.project.data.description)
                    }, null, 8, ["innerHTML"])
                  ])
                ])
              ]),
              $props.project.data.species ? (openBlock(), createBlock("div", {
                key: 0,
                class: "pt-2"
              }, [
                createVNode("h3", { class: "text-xl font-extrabold text-blue-gray-900" }, " Organism "),
                createVNode("div", { class: "pt-3" }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($props.project.data.species, (species, $index) => {
                    return openBlock(), createBlock("div", {
                      key: $index,
                      class: "bg-gray-100 text-gray-800 mb-0.5 inline-flex truncate break-words items-center px-3 py-2 rounded-full text-sm font-medium mr-1"
                    }, [
                      createVNode("ontology-term-annotation", { annotation: species }, null, 8, ["annotation"])
                    ]);
                  }), 128))
                ])
              ])) : createCommentVNode("", true),
              createVNode("div", { class: "gap-y-6 sm:grid-cols-6 sm:gap-x-6" }, [
                createVNode("div", { class: "pt-2 sm:col-span-6" }, [
                  createVNode("h2", { class: "text-xl font-extrabold mb-3 text-blue-gray-900" }, " Submitter(s) ")
                ]),
                createVNode("div", { class: "mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2" }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($props.project.data.users, (user) => {
                    return openBlock(), createBlock("div", {
                      key: user.email,
                      class: "relative rounded-lg border border-gray-300 bg-white p-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500"
                    }, [
                      createVNode("div", { class: "flex-shrink-0" }, [
                        createVNode("img", {
                          class: "h-10 w-10 rounded-full",
                          src: user.profile_photo_url,
                          alt: ""
                        }, null, 8, ["src"])
                      ]),
                      createVNode("div", { class: "flex-1 min-w-0" }, [
                        createVNode("a", { class: "focus:outline-none" }, [
                          createVNode("span", {
                            class: "absolute inset-0",
                            "aria-hidden": "true"
                          }),
                          createVNode("p", { class: "text-sm font-medium text-gray-900" }, toDisplayString(user.first_name + " " + user.last_name), 1),
                          createVNode("p", { class: "text-sm text-gray-500 truncate" }, " @ " + toDisplayString(user.username), 1)
                        ])
                      ])
                    ]);
                  }), 128))
                ]),
                $props.project.data.authors && $props.project.data.authors.length > 0 ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                }, [
                  createVNode("div", { class: "sm:col-span-6" }, [
                    createVNode("h2", { class: "text-xl font-extrabold mb-3 text-blue-gray-900" }, " Author(s) ")
                  ]),
                  createVNode("div", { class: "mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2" }, [
                    createVNode(_component_author_card, {
                      authors: $props.project.data.authors
                    }, null, 8, ["authors"])
                  ]),
                  $props.project.data.citations && $props.project.data.citations.length > 0 ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                  }, [
                    createVNode("div", { class: "sm:col-span-6" }, [
                      createVNode("h2", { class: "text-xl font-extrabold mb-3 text-blue-gray-900" }, " Citation(s) ")
                    ]),
                    createVNode("dd", { class: "mt-2 text-md text-gray-900 space-y-5 focus:pointer-events-auto" }, [
                      createVNode("div", { class: "mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2" }, [
                        createVNode(_component_citation_card, {
                          citations: $props.project.data.citations
                        }, null, 8, ["citations"])
                      ])
                    ])
                  ])) : createCommentVNode("", true)
                ])) : createCommentVNode("", true)
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  ssrRenderVNode(_push, createVNode(resolveDynamicComponent("script"), { type: "application/ld+json" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`${ssrInterpolate($data.schema)}`);
      } else {
        return [
          createTextVNode(toDisplayString($data.schema), 1)
        ];
      }
    }),
    _: 1
  }), _parent);
  _push(`<!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Project/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Show = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Show as default
};
