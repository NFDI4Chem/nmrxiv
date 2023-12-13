import ProjectLayout from "./Layout-3465446f.mjs";
import { S as StudyPublicCard } from "./StudyCardPublic-060b9838.mjs";
import { Head } from "@inertiajs/vue3";
import { resolveComponent, withCtx, createVNode, openBlock, createBlock, withDirectives, vModelText, createCommentVNode, Fragment, renderList, createTextVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
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
import "./Depictor2D-b60253ae.mjs";
const _sfc_main = {
  components: {
    ProjectLayout,
    StudyCard: StudyPublicCard,
    Head
  },
  props: ["project", "tab"],
  data() {
    return {
      loading: false,
      studies: [],
      query: ""
    };
  },
  computed: {},
  mounted() {
    if (this.project) {
      this.fetchStudies(route("project.studies", this.project.data.id));
    }
  },
  methods: {
    fetchStudies(url) {
      axios.get(url).then((response) => {
        this.loading = false;
        this.studies = response.data;
      });
    },
    update(link) {
      if (!link && this.query != "") {
        link = {};
        link["url"] = route("project.studies", this.project.data.id) + "?page=1";
      }
      if (link.url) {
        this.loading = true;
        this.executeQuery(link);
      }
    },
    reset() {
      let link = {};
      link["url"] = route("project.studies", this.project.data.id) + "?page=1";
      this.query = "";
      this.loading = true;
      this.executeQuery(link);
    },
    executeQuery(link) {
      this.fetchStudies(link.url + "&search=" + this.query);
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_project_layout = resolveComponent("project-layout");
  const _component_study_card = resolveComponent("study-card");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, {
    title: "Samples - " + $props.project.data.name
  }, null, _parent));
  _push(ssrRenderComponent(_component_project_layout, {
    project: $props.project,
    "selected-tab": $props.tab
  }, {
    "project-content": withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6"${_scopeId}><div class="flex items-baseline justify-between"${_scopeId}><div${_scopeId}><h2 class="text-lg mb-3 font-bold"${_scopeId}>Studies</h2>`);
        if (!$data.loading) {
          _push2(`<div class="flex items-center mr-4 w-full"${_scopeId}><div class="flex w-full bg-white shadow rounded-full"${_scopeId}><input${ssrRenderAttr("value", $data.query)} class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Search…"${_scopeId}></div><button type="button" class="ml-2 inline-flex items-center rounded-full px-6 py-3 shadow rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"${_scopeId}> GO </button><button class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500" type="button"${_scopeId}> Reset </button></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div>`);
        if (!$data.loading && $data.studies.data) {
          _push2(`<div${_scopeId}>`);
          if ($data.studies.data.length <= 0) {
            _push2(`<div${_scopeId}><div class="mt-4 px-12 py-8 mx-auto max-w-4xl"${_scopeId}><div class="px-6 py-4 bg-white shadow-md rounded-lg"${_scopeId}><div class="flex items-center"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6"${_scopeId}><path d="M3 6l9 4v12l-9-4V6zm14-3v2c0 1.1-2.24 2-5 2s-5-.9-5-2V3c0 1.1 2.24 2 5 2s5-.9 5-2z" class="fill-current text-gray-400"${_scopeId}></path><polygon points="21 6 12 10 12 22 21 18" class="fill-current text-gray-600"${_scopeId}></polygon></svg><div class="ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider"${_scopeId}> No studies </div></div></div></div></div>`);
          } else {
            _push2(`<div${_scopeId}><div class="mt-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl"${_scopeId}><!--[-->`);
            ssrRenderList($data.studies.data, (study) => {
              _push2(`<div${_scopeId}>`);
              _push2(ssrRenderComponent(_component_study_card, {
                project: $props.project.data,
                study
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            });
            _push2(`<!--]--></div>`);
            if ($data.studies.meta && $data.studies.meta.total > $data.studies.meta.per_page) {
              _push2(`<div class="block w-100 mt-10"${_scopeId}><nav class="border-t border-gray-200 px-4 flex items-center justify-between sm:px-0"${_scopeId}><div class="-mt-px w-0 flex-1 flex"${_scopeId}> </div><div class="hidden md:-mt-px md:flex"${_scopeId}><!--[-->`);
              ssrRenderList($data.studies.meta.links, (link) => {
                _push2(`<div class="${ssrRenderClass([
                  link.active ? "text-teal-600 border-teal-500" : "",
                  "cursor-pointer border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium"
                ])}"${_scopeId}>${link.label}</div>`);
              });
              _push2(`<!--]--></div><div class="-mt-px w-0 flex-1 flex justify-end"${_scopeId}>   </div></nav></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
          }
          _push2(`</div>`);
        } else {
          _push2(`<div class="text-gray-400 mt-10"${_scopeId}><svg class="animate-spin inline -ml-1 mr-2 h-5 w-5 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"${_scopeId}><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"${_scopeId}></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"${_scopeId}></path></svg> Loading... </div>`);
        }
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6" }, [
            createVNode("div", { class: "flex items-baseline justify-between" }, [
              createVNode("div", null, [
                createVNode("h2", { class: "text-lg mb-3 font-bold" }, "Studies"),
                !$data.loading ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "flex items-center mr-4 w-full"
                }, [
                  createVNode("div", { class: "flex w-full bg-white shadow rounded-full" }, [
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => $data.query = $event,
                      class: "relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline",
                      autocomplete: "off",
                      type: "text",
                      name: "search",
                      placeholder: "Search…"
                    }, null, 8, ["onUpdate:modelValue"]), [
                      [vModelText, $data.query]
                    ])
                  ]),
                  createVNode("button", {
                    type: "button",
                    class: "ml-2 inline-flex items-center rounded-full px-6 py-3 shadow rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500",
                    onClick: ($event) => $options.update()
                  }, " GO ", 8, ["onClick"]),
                  createVNode("button", {
                    class: "ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500",
                    type: "button",
                    onClick: ($event) => $options.reset()
                  }, " Reset ", 8, ["onClick"])
                ])) : createCommentVNode("", true)
              ])
            ]),
            !$data.loading && $data.studies.data ? (openBlock(), createBlock("div", { key: 0 }, [
              $data.studies.data.length <= 0 ? (openBlock(), createBlock("div", { key: 0 }, [
                createVNode("div", { class: "mt-4 px-12 py-8 mx-auto max-w-4xl" }, [
                  createVNode("div", { class: "px-6 py-4 bg-white shadow-md rounded-lg" }, [
                    createVNode("div", { class: "flex items-center" }, [
                      (openBlock(), createBlock("svg", {
                        xmlns: "http://www.w3.org/2000/svg",
                        viewBox: "0 0 24 24",
                        class: "h-6 w-6"
                      }, [
                        createVNode("path", {
                          d: "M3 6l9 4v12l-9-4V6zm14-3v2c0 1.1-2.24 2-5 2s-5-.9-5-2V3c0 1.1 2.24 2 5 2s5-.9 5-2z",
                          class: "fill-current text-gray-400"
                        }),
                        createVNode("polygon", {
                          points: "21 6 12 10 12 22 21 18",
                          class: "fill-current text-gray-600"
                        })
                      ])),
                      createVNode("div", { class: "ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider" }, " No studies ")
                    ])
                  ])
                ])
              ])) : (openBlock(), createBlock("div", { key: 1 }, [
                createVNode("div", { class: "mt-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl" }, [
                  (openBlock(true), createBlock(Fragment, null, renderList($data.studies.data, (study) => {
                    return openBlock(), createBlock("div", {
                      key: study.uuid
                    }, [
                      createVNode(_component_study_card, {
                        project: $props.project.data,
                        study
                      }, null, 8, ["project", "study"])
                    ]);
                  }), 128))
                ]),
                $data.studies.meta && $data.studies.meta.total > $data.studies.meta.per_page ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "block w-100 mt-10"
                }, [
                  createVNode("nav", { class: "border-t border-gray-200 px-4 flex items-center justify-between sm:px-0" }, [
                    createVNode("div", { class: "-mt-px w-0 flex-1 flex" }, " "),
                    createVNode("div", { class: "hidden md:-mt-px md:flex" }, [
                      (openBlock(true), createBlock(Fragment, null, renderList($data.studies.meta.links, (link) => {
                        return openBlock(), createBlock("div", {
                          key: link.url,
                          class: [
                            link.active ? "text-teal-600 border-teal-500" : "",
                            "cursor-pointer border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium"
                          ],
                          onClick: ($event) => $options.update(link),
                          innerHTML: link.label
                        }, null, 10, ["onClick", "innerHTML"]);
                      }), 128))
                    ]),
                    createVNode("div", { class: "-mt-px w-0 flex-1 flex justify-end" }, "   ")
                  ])
                ])) : createCommentVNode("", true)
              ]))
            ])) : (openBlock(), createBlock("div", {
              key: 1,
              class: "text-gray-400 mt-10"
            }, [
              (openBlock(), createBlock("svg", {
                class: "animate-spin inline -ml-1 mr-2 h-5 w-5 text-dark",
                xmlns: "http://www.w3.org/2000/svg",
                fill: "none",
                viewBox: "0 0 24 24"
              }, [
                createVNode("circle", {
                  class: "opacity-25",
                  cx: "12",
                  cy: "12",
                  r: "10",
                  stroke: "currentColor",
                  "stroke-width": "4"
                }),
                createVNode("path", {
                  class: "opacity-75",
                  fill: "currentColor",
                  d: "M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                })
              ])),
              createTextVNode(" Loading... ")
            ]))
          ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Project/Samples.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Samples = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Samples as default
};
