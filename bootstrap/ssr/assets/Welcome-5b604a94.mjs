import { Head, Link } from "@inertiajs/vue3";
import { J as JetApplicationLogo } from "./ApplicationLogo-88e5413e.mjs";
import { P as ProjectCard } from "./ProjectCard-c2ec56ff.mjs";
import { resolveComponent, mergeProps, useSSRContext, unref, withCtx, createVNode, toDisplayString, openBlock, createBlock, defineComponent, h, createTextVNode, resolveDynamicComponent, Fragment, renderList, Transition } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderComponent, ssrInterpolate, ssrRenderVNode, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
import { Disclosure, DisclosureButton, DisclosurePanel, Popover, PopoverButton, PopoverGroup, PopoverPanel } from "@headlessui/vue";
import { PlusSmallIcon, MinusSmallIcon, ChatBubbleLeftIcon, ChatBubbleLeftRightIcon, DocumentTextIcon, HeartIcon, InboxIcon, Bars3Icon, PencilSquareIcon, MagnifyingGlassIcon, ArrowUturnLeftIcon, SparklesIcon, TrashIcon, ShareIcon, UsersIcon, XMarkIcon, ScaleIcon, CircleStackIcon } from "@heroicons/vue/24/outline";
import { ChevronDownIcon } from "@heroicons/vue/24/solid";
import { T as ToolTip } from "./ToolTip-cf9882a3.mjs";
import { F as FlashMessages } from "./FlashMessages-f0051c19.mjs";
const _sfc_main$2 = {
  components: {
    ProjectCard
  },
  props: ["limit"],
  setup() {
  },
  data() {
    return {
      projects: []
    };
  },
  mounted() {
    let max = this.limit ? this.limit : 8;
    axios.get("/api/v1/projects").then((response) => {
      this.projects = response.data.data.slice(0, max);
    });
  },
  methods: {}
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_project_card = resolveComponent("project-card");
  if ($data.projects && $data.projects.length > 0) {
    _push(`<div${ssrRenderAttrs(mergeProps({ class: "mt-4 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl" }, _attrs))}><!--[-->`);
    ssrRenderList($data.projects, (project) => {
      _push(`<span>`);
      _push(ssrRenderComponent(_component_project_card, {
        mode: "mini",
        project
      }, null, _parent));
      _push(`</span>`);
    });
    _push(`<!--]--></div>`);
  } else {
    _push(`<!---->`);
  }
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Projects.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const Projects = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$1 = {
  __name: "FAQs",
  __ssrInlineRender: true,
  props: ["limit"],
  setup(__props) {
    const faqs = [
      {
        question: "How can I submit my data to nmrXiv?",
        answer: " Register to nmrXiv. Structure your data in folders similar to nmrXiv structuring of projects, studies, and datasets. This step might not be intuitive so we recommend checking its docummentation. Upload your data, edit it, and provide its metadata via the submission pipeline."
      },
      {
        question: "What should I do if I think I've found a bug?",
        answer: "If you believe you've found a bug in nmrXiv, please write to us at info.nmrxiv@uni-jena.de or reach out to our Helpdesk https://www.nfdi4chem.de/helpdesk/."
      },
      {
        question: "Who can use my public/published resources?",
        answer: "If you make your resources public (projects, studies, datasets), you are making them open for access to everyone (even to the non-registered user of nmrXiv), but you can specify rights by choosing licenses for your projects and studies (study license propagate to its datasets). Once your project is made public, you cannot edit, delete or make it private again."
      },
      {
        question: "What are supported files format in nmrXiv?",
        answer: "nmrXiv accepts all NMR formats uploaded. However, not all of them are readable at the moment. So far, only NMRium-supported formats can be translated into spectra in nmrXiv. Those formats are jcamp-dx, jeol, Bruker folders, NMReData, and nmrium. For validation purposes, the uploaded data should have at least one readable format."
      }
      // More questions...
    ];
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white" }, _attrs))}><div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8 lg:py-40"><div class="mx-auto max-w-4xl divide-y divide-gray-900/10"><h2 class="text-2xl font-bold leading-10 tracking-tight text-gray-900"> Frequently asked questions </h2><dl class="mt-10 space-y-6 divide-y divide-gray-900/10"><!--[-->`);
      ssrRenderList(faqs.slice(0, __props.limit), (faq) => {
        _push(ssrRenderComponent(unref(Disclosure), {
          as: "div",
          key: faq.question,
          class: "pt-6"
        }, {
          default: withCtx(({ open }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<dt${_scopeId}>`);
              _push2(ssrRenderComponent(unref(DisclosureButton), { class: "flex w-full items-start justify-between text-left text-gray-900" }, {
                default: withCtx((_, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span class="text-base font-semibold leading-7"${_scopeId2}>${ssrInterpolate(faq.question)}</span><span class="ml-6 flex h-7 items-center"${_scopeId2}>`);
                    if (!open) {
                      _push3(ssrRenderComponent(unref(PlusSmallIcon), {
                        class: "h-6 w-6",
                        "aria-hidden": "true"
                      }, null, _parent3, _scopeId2));
                    } else {
                      _push3(ssrRenderComponent(unref(MinusSmallIcon), {
                        class: "h-6 w-6",
                        "aria-hidden": "true"
                      }, null, _parent3, _scopeId2));
                    }
                    _push3(`</span>`);
                  } else {
                    return [
                      createVNode("span", { class: "text-base font-semibold leading-7" }, toDisplayString(faq.question), 1),
                      createVNode("span", { class: "ml-6 flex h-7 items-center" }, [
                        !open ? (openBlock(), createBlock(unref(PlusSmallIcon), {
                          key: 0,
                          class: "h-6 w-6",
                          "aria-hidden": "true"
                        })) : (openBlock(), createBlock(unref(MinusSmallIcon), {
                          key: 1,
                          class: "h-6 w-6",
                          "aria-hidden": "true"
                        }))
                      ])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</dt>`);
              _push2(ssrRenderComponent(unref(DisclosurePanel), {
                as: "dd",
                class: "mt-2 pr-12"
              }, {
                default: withCtx((_, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<p class="text-base leading-7 text-gray-600"${_scopeId2}>${ssrInterpolate(faq.answer)}</p>`);
                  } else {
                    return [
                      createVNode("p", { class: "text-base leading-7 text-gray-600" }, toDisplayString(faq.answer), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            } else {
              return [
                createVNode("dt", null, [
                  createVNode(unref(DisclosureButton), { class: "flex w-full items-start justify-between text-left text-gray-900" }, {
                    default: withCtx(() => [
                      createVNode("span", { class: "text-base font-semibold leading-7" }, toDisplayString(faq.question), 1),
                      createVNode("span", { class: "ml-6 flex h-7 items-center" }, [
                        !open ? (openBlock(), createBlock(unref(PlusSmallIcon), {
                          key: 0,
                          class: "h-6 w-6",
                          "aria-hidden": "true"
                        })) : (openBlock(), createBlock(unref(MinusSmallIcon), {
                          key: 1,
                          class: "h-6 w-6",
                          "aria-hidden": "true"
                        }))
                      ])
                    ]),
                    _: 2
                  }, 1024)
                ]),
                createVNode(unref(DisclosurePanel), {
                  as: "dd",
                  class: "mt-2 pr-12"
                }, {
                  default: withCtx(() => [
                    createVNode("p", { class: "text-base leading-7 text-gray-600" }, toDisplayString(faq.answer), 1)
                  ]),
                  _: 2
                }, 1024)
              ];
            }
          }),
          _: 2
        }, _parent));
      });
      _push(`<!--]--></dl></div></div></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/App/FAQs.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Search = [
  {
    name: "Browse",
    description: "Know more about the data deposited in nmrXiv by browsing projects and datasets. You can also learn more about our data schema on our documentation site.",
    href: "#",
    icon: InboxIcon
  },
  {
    name: "Advanced search",
    description: "Search similar spectra by simple drag and drop of your machine output files or search spectra by structures. Need further guidance or found any missing information. Reach out to us or check out our documentation site.",
    href: "#",
    icon: MagnifyingGlassIcon
  }
];
const features = [
  {
    name: "Advanced search",
    description: "",
    icon: MagnifyingGlassIcon
  },
  {
    name: "Open Source",
    description: "",
    icon: ScaleIcon
  },
  {
    name: "Auto Assignments",
    description: "",
    icon: ShareIcon
  },
  {
    name: "Prediction",
    description: "",
    icon: PencilSquareIcon
  },
  {
    name: "Schemas and MIChI",
    description: "",
    icon: DocumentTextIcon
  },
  {
    name: "Community challenges",
    description: "",
    icon: ChatBubbleLeftIcon
  },
  {
    name: "Docs & API",
    description: "",
    icon: DocumentTextIcon
  },
  {
    name: "Backups",
    description: "",
    icon: CircleStackIcon
  }
];
const footerNavigation = {
  // Search: [
  //   { name: "Browse", href: "/projects" },
  //   { name: "Advanced Search", href: "/projects" },
  // ],
  quicklinks: [
    { name: "Projects", href: "/projects" },
    { name: "Spectra", href: "/spectra" }
  ],
  support: [
    { name: "Documentation", href: "https://docs.nmrxiv.org" },
    {
      name: "Guides",
      href: "https://docs.nmrxiv.org/docs/submission-guides/overview"
    },
    {
      name: "API Status",
      href: "https://docs.nmrxiv.org/docs/developer-guides/API"
    }
  ],
  About: [
    {
      name: "Adivsory Board",
      href: "https://docs.nmrxiv.org/docs/contributing/contributors-and-steering-committee"
    }
  ],
  legal: [
    { name: "Privacy", href: "/privacy-policy" },
    { name: "Terms", href: "/terms-of-service" }
  ],
  social: [
    {
      name: "GitHub",
      href: "https://github.com/nfdi4chem/nmrxiv",
      icon: defineComponent({
        render: () => h("svg", { fill: "currentColor", viewBox: "0 0 24 24" }, [
          h("path", {
            "fill-rule": "evenodd",
            d: "M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z",
            "clip-rule": "evenodd"
          })
        ])
      })
    }
  ]
};
const _sfc_main = {
  components: {
    Head,
    Link,
    JetApplicationLogo,
    ChatBubbleLeftIcon,
    ChatBubbleLeftRightIcon,
    DocumentTextIcon,
    HeartIcon,
    InboxIcon,
    Bars3Icon,
    PencilSquareIcon,
    MagnifyingGlassIcon,
    ArrowUturnLeftIcon,
    SparklesIcon,
    TrashIcon,
    ShareIcon,
    UsersIcon,
    XMarkIcon,
    ScaleIcon,
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
    ChevronDownIcon,
    CircleStackIcon,
    ToolTip,
    Projects,
    FAQs: _sfc_main$1,
    FlashMessages
  },
  props: {
    spectra: String,
    projects: String,
    compounds: String,
    techniques: String
  },
  setup() {
    return {
      Search,
      features,
      footerNavigation
    };
  },
  data() {
    return {
      schema: {}
    };
  },
  mounted() {
    axios.get(route("bioschemas.datacatalog")).then((response) => {
      this.schema = response.data;
    });
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_FlashMessages = resolveComponent("FlashMessages");
  const _component_Popover = resolveComponent("Popover");
  const _component_Link = resolveComponent("Link");
  const _component_jet_application_logo = resolveComponent("jet-application-logo");
  const _component_PopoverButton = resolveComponent("PopoverButton");
  const _component_Bars3Icon = resolveComponent("Bars3Icon");
  const _component_PopoverGroup = resolveComponent("PopoverGroup");
  const _component_PopoverPanel = resolveComponent("PopoverPanel");
  const _component_XMarkIcon = resolveComponent("XMarkIcon");
  const _component_ToolTip = resolveComponent("ToolTip");
  const _component_InboxIcon = resolveComponent("InboxIcon");
  const _component_Projects = resolveComponent("Projects");
  const _component_SparklesIcon = resolveComponent("SparklesIcon");
  const _component_FAQs = resolveComponent("FAQs");
  _push(`<!--[--><div class="bg-white">`);
  _push(ssrRenderComponent(_component_Head, { title: "Welcome to nmrXiv" }, null, _parent));
  _push(ssrRenderComponent(_component_FlashMessages, null, null, _parent));
  _push(`<main><div class="relative index_beams"><header>`);
  _push(ssrRenderComponent(_component_Popover, { class: "relative" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="flex justify-between items-center mx-auto px-4 py-6 sm:px-6 md:justify-start md:space-x-10 lg:px-8"${_scopeId}><div class="flex justify-start lg:w-0 lg:flex-1"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, { href: "/" }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_jet_application_logo, { class: "block h-10 p-0.5 ml-1.5 w-auto" }, null, _parent3, _scopeId2));
            } else {
              return [
                createVNode(_component_jet_application_logo, { class: "block h-10 p-0.5 ml-1.5 w-auto" })
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div><div class="-mr-2 -my-2 md:hidden"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_PopoverButton, { class: "rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500" }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<span class="sr-only"${_scopeId2}>Open menu</span>`);
              _push3(ssrRenderComponent(_component_Bars3Icon, {
                class: "h-6 w-6",
                "aria-hidden": "true"
              }, null, _parent3, _scopeId2));
            } else {
              return [
                createVNode("span", { class: "sr-only" }, "Open menu"),
                createVNode(_component_Bars3Icon, {
                  class: "h-6 w-6",
                  "aria-hidden": "true"
                })
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
        _push2(ssrRenderComponent(_component_PopoverGroup, {
          as: "nav",
          class: "hidden md:flex space-x-10"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_Link, {
                href: "/projects",
                class: "text-base font-medium text-gray-500 hover:text-gray-900"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Projects `);
                  } else {
                    return [
                      createTextVNode(" Projects ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_Link, {
                href: "/spectra",
                class: "text-base font-medium text-gray-500 hover:text-gray-900"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Spectra `);
                  } else {
                    return [
                      createTextVNode(" Spectra ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_Link, {
                href: "/compounds",
                class: "text-base font-medium text-gray-500 hover:text-gray-900"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Compounds `);
                  } else {
                    return [
                      createTextVNode(" Compounds ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<a target="_blank" href="https://docs.nmrxiv.org" class="text-base font-medium text-gray-500 hover:text-gray-900"${_scopeId2}> Docs </a>`);
            } else {
              return [
                createVNode(_component_Link, {
                  href: "/projects",
                  class: "text-base font-medium text-gray-500 hover:text-gray-900"
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Projects ")
                  ]),
                  _: 1
                }),
                createVNode(_component_Link, {
                  href: "/spectra",
                  class: "text-base font-medium text-gray-500 hover:text-gray-900"
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Spectra ")
                  ]),
                  _: 1
                }),
                createVNode(_component_Link, {
                  href: "/compounds",
                  class: "text-base font-medium text-gray-500 hover:text-gray-900"
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Compounds ")
                  ]),
                  _: 1
                }),
                createVNode("a", {
                  target: "_blank",
                  href: "https://docs.nmrxiv.org",
                  class: "text-base font-medium text-gray-500 hover:text-gray-900"
                }, " Docs ")
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        if (_ctx.$page.props.auth.user.first_name != null) {
          _push2(`<div class="hidden md:flex items-center justify-end md:flex-1 lg:w-0"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Link, {
            href: "/dashboard",
            class: "ml-8 whitespace-nowrap inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Dashboard `);
              } else {
                return [
                  createTextVNode(" Dashboard ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          _push2(`<div class="hidden md:flex items-center justify-end md:flex-1 lg:w-0"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Link, {
            href: "/login",
            class: "whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Login `);
              } else {
                return [
                  createTextVNode(" Login ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_Link, {
            href: "/register",
            class: "ml-8 whitespace-nowrap inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(` Register `);
              } else {
                return [
                  createTextVNode(" Register ")
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        }
        _push2(`</div><div${_scopeId}>`);
        _push2(ssrRenderComponent(_component_PopoverPanel, {
          focus: "",
          class: "absolute z-30 top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y-2 divide-gray-50"${_scopeId2}><div class="pt-5 pb-6 px-5"${_scopeId2}><div class="flex items-center justify-between"${_scopeId2}><div${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_jet_application_logo, { class: "block h-10 p-0.5 ml-1.5 w-auto" }, null, _parent3, _scopeId2));
              _push3(`</div><div class="-mr-2"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_PopoverButton, { class: "bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500" }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(`<span class="sr-only"${_scopeId3}>Close menu</span>`);
                    _push4(ssrRenderComponent(_component_XMarkIcon, {
                      class: "h-6 w-6",
                      "aria-hidden": "true"
                    }, null, _parent4, _scopeId3));
                  } else {
                    return [
                      createVNode("span", { class: "sr-only" }, "Close menu"),
                      createVNode(_component_XMarkIcon, {
                        class: "h-6 w-6",
                        "aria-hidden": "true"
                      })
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`</div></div><div class="mt-6"${_scopeId2}><nav class="grid grid-cols-1 gap-7"${_scopeId2}><!--[-->`);
              ssrRenderList($setup.Search, (item) => {
                _push3(ssrRenderComponent(_component_Link, {
                  key: item.name,
                  href: item.href,
                  class: "-m-3 p-3 flex items-center rounded-lg hover:bg-gray-50"
                }, {
                  default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                    if (_push4) {
                      _push4(`<div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-indigo-600 to-teal-600 text-white"${_scopeId3}>`);
                      ssrRenderVNode(_push4, createVNode(resolveDynamicComponent(item.icon), {
                        class: "h-6 w-6",
                        "aria-hidden": "true"
                      }, null), _parent4, _scopeId3);
                      _push4(`</div><div class="ml-4 text-base font-medium text-gray-900"${_scopeId3}>${ssrInterpolate(item.name)}</div>`);
                    } else {
                      return [
                        createVNode("div", { class: "flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-indigo-600 to-teal-600 text-white" }, [
                          (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                            class: "h-6 w-6",
                            "aria-hidden": "true"
                          }))
                        ]),
                        createVNode("div", { class: "ml-4 text-base font-medium text-gray-900" }, toDisplayString(item.name), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent3, _scopeId2));
              });
              _push3(`<!--]--></nav></div></div><div class="py-6 px-5"${_scopeId2}><div class="grid grid-cols-2 gap-4"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_Link, {
                href: "/projects",
                class: "text-base font-medium text-gray-900 hover:text-gray-700"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Projects `);
                  } else {
                    return [
                      createTextVNode(" Projects ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_Link, {
                href: "/datasets",
                class: "text-base font-medium text-gray-900 hover:text-gray-700"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Datasets `);
                  } else {
                    return [
                      createTextVNode(" Datasets ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(ssrRenderComponent(_component_Link, {
                href: "/compounds",
                class: "text-base font-medium text-gray-900 hover:text-gray-700"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Compounds `);
                  } else {
                    return [
                      createTextVNode(" Compounds ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<a target="_blank" href="https://docs.nmrxiv.org" class="text-base font-medium text-gray-900 hover:text-gray-700"${_scopeId2}> Docs </a></div><div class="mt-6"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_Link, {
                href: "/login",
                class: "w-full flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Login `);
                  } else {
                    return [
                      createTextVNode(" Login ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`<p class="mt-6 text-center text-base font-medium text-gray-500"${_scopeId2}>`);
              _push3(ssrRenderComponent(_component_Link, {
                href: "/register",
                class: "text-gray-900"
              }, {
                default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                  if (_push4) {
                    _push4(` Register `);
                  } else {
                    return [
                      createTextVNode(" Register ")
                    ];
                  }
                }),
                _: 1
              }, _parent3, _scopeId2));
              _push3(`</p></div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y-2 divide-gray-50" }, [
                  createVNode("div", { class: "pt-5 pb-6 px-5" }, [
                    createVNode("div", { class: "flex items-center justify-between" }, [
                      createVNode("div", null, [
                        createVNode(_component_jet_application_logo, { class: "block h-10 p-0.5 ml-1.5 w-auto" })
                      ]),
                      createVNode("div", { class: "-mr-2" }, [
                        createVNode(_component_PopoverButton, { class: "bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "sr-only" }, "Close menu"),
                            createVNode(_component_XMarkIcon, {
                              class: "h-6 w-6",
                              "aria-hidden": "true"
                            })
                          ]),
                          _: 1
                        })
                      ])
                    ]),
                    createVNode("div", { class: "mt-6" }, [
                      createVNode("nav", { class: "grid grid-cols-1 gap-7" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList($setup.Search, (item) => {
                          return openBlock(), createBlock(_component_Link, {
                            key: item.name,
                            href: item.href,
                            class: "-m-3 p-3 flex items-center rounded-lg hover:bg-gray-50"
                          }, {
                            default: withCtx(() => [
                              createVNode("div", { class: "flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-indigo-600 to-teal-600 text-white" }, [
                                (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                                  class: "h-6 w-6",
                                  "aria-hidden": "true"
                                }))
                              ]),
                              createVNode("div", { class: "ml-4 text-base font-medium text-gray-900" }, toDisplayString(item.name), 1)
                            ]),
                            _: 2
                          }, 1032, ["href"]);
                        }), 128))
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "py-6 px-5" }, [
                    createVNode("div", { class: "grid grid-cols-2 gap-4" }, [
                      createVNode(_component_Link, {
                        href: "/projects",
                        class: "text-base font-medium text-gray-900 hover:text-gray-700"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Projects ")
                        ]),
                        _: 1
                      }),
                      createVNode(_component_Link, {
                        href: "/datasets",
                        class: "text-base font-medium text-gray-900 hover:text-gray-700"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Datasets ")
                        ]),
                        _: 1
                      }),
                      createVNode(_component_Link, {
                        href: "/compounds",
                        class: "text-base font-medium text-gray-900 hover:text-gray-700"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Compounds ")
                        ]),
                        _: 1
                      }),
                      createVNode("a", {
                        target: "_blank",
                        href: "https://docs.nmrxiv.org",
                        class: "text-base font-medium text-gray-900 hover:text-gray-700"
                      }, " Docs ")
                    ]),
                    createVNode("div", { class: "mt-6" }, [
                      createVNode(_component_Link, {
                        href: "/login",
                        class: "w-full flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Login ")
                        ]),
                        _: 1
                      }),
                      createVNode("p", { class: "mt-6 text-center text-base font-medium text-gray-500" }, [
                        createVNode(_component_Link, {
                          href: "/register",
                          class: "text-gray-900"
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Register ")
                          ]),
                          _: 1
                        })
                      ])
                    ])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "flex justify-between items-center mx-auto px-4 py-6 sm:px-6 md:justify-start md:space-x-10 lg:px-8" }, [
            createVNode("div", { class: "flex justify-start lg:w-0 lg:flex-1" }, [
              createVNode(_component_Link, { href: "/" }, {
                default: withCtx(() => [
                  createVNode(_component_jet_application_logo, { class: "block h-10 p-0.5 ml-1.5 w-auto" })
                ]),
                _: 1
              })
            ]),
            createVNode("div", { class: "-mr-2 -my-2 md:hidden" }, [
              createVNode(_component_PopoverButton, { class: "rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500" }, {
                default: withCtx(() => [
                  createVNode("span", { class: "sr-only" }, "Open menu"),
                  createVNode(_component_Bars3Icon, {
                    class: "h-6 w-6",
                    "aria-hidden": "true"
                  })
                ]),
                _: 1
              })
            ]),
            createVNode(_component_PopoverGroup, {
              as: "nav",
              class: "hidden md:flex space-x-10"
            }, {
              default: withCtx(() => [
                createVNode(_component_Link, {
                  href: "/projects",
                  class: "text-base font-medium text-gray-500 hover:text-gray-900"
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Projects ")
                  ]),
                  _: 1
                }),
                createVNode(_component_Link, {
                  href: "/spectra",
                  class: "text-base font-medium text-gray-500 hover:text-gray-900"
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Spectra ")
                  ]),
                  _: 1
                }),
                createVNode(_component_Link, {
                  href: "/compounds",
                  class: "text-base font-medium text-gray-500 hover:text-gray-900"
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Compounds ")
                  ]),
                  _: 1
                }),
                createVNode("a", {
                  target: "_blank",
                  href: "https://docs.nmrxiv.org",
                  class: "text-base font-medium text-gray-500 hover:text-gray-900"
                }, " Docs ")
              ]),
              _: 1
            }),
            _ctx.$page.props.auth.user.first_name != null ? (openBlock(), createBlock("div", {
              key: 0,
              class: "hidden md:flex items-center justify-end md:flex-1 lg:w-0"
            }, [
              createVNode(_component_Link, {
                href: "/dashboard",
                class: "ml-8 whitespace-nowrap inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
              }, {
                default: withCtx(() => [
                  createTextVNode(" Dashboard ")
                ]),
                _: 1
              })
            ])) : (openBlock(), createBlock("div", {
              key: 1,
              class: "hidden md:flex items-center justify-end md:flex-1 lg:w-0"
            }, [
              createVNode(_component_Link, {
                href: "/login",
                class: "whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900"
              }, {
                default: withCtx(() => [
                  createTextVNode(" Login ")
                ]),
                _: 1
              }),
              createVNode(_component_Link, {
                href: "/register",
                class: "ml-8 whitespace-nowrap inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
              }, {
                default: withCtx(() => [
                  createTextVNode(" Register ")
                ]),
                _: 1
              })
            ]))
          ]),
          createVNode(Transition, {
            "enter-active-class": "duration-200 ease-out",
            "enter-from-class": "opacity-0 scale-95",
            "enter-to-class": "opacity-100 scale-100",
            "leave-active-class": "duration-100 ease-in",
            "leave-from-class": "opacity-100 scale-100",
            "leave-to-class": "opacity-0 scale-95"
          }, {
            default: withCtx(() => [
              createVNode("div", null, [
                createVNode(_component_PopoverPanel, {
                  focus: "",
                  class: "absolute z-30 top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden"
                }, {
                  default: withCtx(() => [
                    createVNode("div", { class: "rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y-2 divide-gray-50" }, [
                      createVNode("div", { class: "pt-5 pb-6 px-5" }, [
                        createVNode("div", { class: "flex items-center justify-between" }, [
                          createVNode("div", null, [
                            createVNode(_component_jet_application_logo, { class: "block h-10 p-0.5 ml-1.5 w-auto" })
                          ]),
                          createVNode("div", { class: "-mr-2" }, [
                            createVNode(_component_PopoverButton, { class: "bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500" }, {
                              default: withCtx(() => [
                                createVNode("span", { class: "sr-only" }, "Close menu"),
                                createVNode(_component_XMarkIcon, {
                                  class: "h-6 w-6",
                                  "aria-hidden": "true"
                                })
                              ]),
                              _: 1
                            })
                          ])
                        ]),
                        createVNode("div", { class: "mt-6" }, [
                          createVNode("nav", { class: "grid grid-cols-1 gap-7" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList($setup.Search, (item) => {
                              return openBlock(), createBlock(_component_Link, {
                                key: item.name,
                                href: item.href,
                                class: "-m-3 p-3 flex items-center rounded-lg hover:bg-gray-50"
                              }, {
                                default: withCtx(() => [
                                  createVNode("div", { class: "flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-indigo-600 to-teal-600 text-white" }, [
                                    (openBlock(), createBlock(resolveDynamicComponent(item.icon), {
                                      class: "h-6 w-6",
                                      "aria-hidden": "true"
                                    }))
                                  ]),
                                  createVNode("div", { class: "ml-4 text-base font-medium text-gray-900" }, toDisplayString(item.name), 1)
                                ]),
                                _: 2
                              }, 1032, ["href"]);
                            }), 128))
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "py-6 px-5" }, [
                        createVNode("div", { class: "grid grid-cols-2 gap-4" }, [
                          createVNode(_component_Link, {
                            href: "/projects",
                            class: "text-base font-medium text-gray-900 hover:text-gray-700"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Projects ")
                            ]),
                            _: 1
                          }),
                          createVNode(_component_Link, {
                            href: "/datasets",
                            class: "text-base font-medium text-gray-900 hover:text-gray-700"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Datasets ")
                            ]),
                            _: 1
                          }),
                          createVNode(_component_Link, {
                            href: "/compounds",
                            class: "text-base font-medium text-gray-900 hover:text-gray-700"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Compounds ")
                            ]),
                            _: 1
                          }),
                          createVNode("a", {
                            target: "_blank",
                            href: "https://docs.nmrxiv.org",
                            class: "text-base font-medium text-gray-900 hover:text-gray-700"
                          }, " Docs ")
                        ]),
                        createVNode("div", { class: "mt-6" }, [
                          createVNode(_component_Link, {
                            href: "/login",
                            class: "w-full flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:from-indigo-700 hover:to-teal-700"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Login ")
                            ]),
                            _: 1
                          }),
                          createVNode("p", { class: "mt-6 text-center text-base font-medium text-gray-500" }, [
                            createVNode(_component_Link, {
                              href: "/register",
                              class: "text-gray-900"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(" Register ")
                              ]),
                              _: 1
                            })
                          ])
                        ])
                      ])
                    ])
                  ]),
                  _: 1
                })
              ])
            ]),
            _: 1
          })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</header><div class="mx-auto sm:px-6 lg:px-8"><div class="relative sm:rounded-2xl"><div class="relative px-4 py-8 sm:px-6 sm:py-12 lg:py-12 lg:px-8"><h1 class="text-center text-2xl font-extrabold tracking-tight sm:text-5xl lg:text-4xl"><span class="block text-teal-900">Open, FAIR and Consensus-Driven</span><span class="block text-teal-200">NMR spectroscopy data repository and analysis platform.</span></h1><p class="mt-6 max-w-lg mx-auto text-center text-xl text-dark-200 sm:max-w-3xl"> We archive raw and processed NMR data, providing support for browsing, search, analysis, and dissemination of NMR data worldwide. </p><div class="mt-10 max-w-sm mx-auto sm:max-w-none sm:flex sm:justify-center"><div class="space-y-4 sm:space-y-0 sm:mx-auto sm:inline-grid sm:grid-cols-2 sm:gap-5">`);
  _push(ssrRenderComponent(_component_Link, {
    href: "/register",
    class: "flex items-center justify-center px-4 py-3 border border-1 text-base font-medium rounded-md shadow-sm text-teal-700 bg-white hover:bg-teal-50 sm:px-8"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Submit data `);
        _push2(ssrRenderComponent(_component_ToolTip, { text: "To submit data you will need an account with nmrXiv, so you will be redirected to our register page and once registered you can then go ahead and submit data. For more information please checkout our <a target='_blank' href='//docs.nmrxiv.org' class='text-gray-400' target='_blank'>documentation</a>." }, null, _parent2, _scopeId));
      } else {
        return [
          createTextVNode(" Submit data "),
          createVNode(_component_ToolTip, { text: "To submit data you will need an account with nmrXiv, so you will be redirected to our register page and once registered you can then go ahead and submit data. For more information please checkout our <a target='_blank' href='//docs.nmrxiv.org' class='text-gray-400' target='_blank'>documentation</a>." })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_Link, {
    href: "/projects",
    class: "flex items-center justify-center px-4 py-3 border border-1 text-base font-medium rounded-md shadow-sm text-white bg-teal-500 sm:px-8"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Browse data `);
      } else {
        return [
          createTextVNode(" Browse data ")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div></div></div><div class="relative bg-transparent"><div class="absolute inset-x-0 top-0 overflow-hidden pl-[50%]"><img src="/img/beams-basic.png" alt="" class="-ml-[39rem] w-[113.125rem] max-w-none"></div><div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8"><div class="relative pt-12 sm:p-24 xl:col-start-1 border-b border-slate-100"><h2 class="text-sm font-semibold tracking-wide uppercase"><span class="bg-clip-text text-gray-600">Metrics</span></h2><p class="mt-3 text-xl font-bold"> Validate your spectral findings and develop new/accurate tools using state of the art AI/ML models. </p><div class="mt-4 grid grid-cols-1 gap-y-12 gap-x-6 sm:grid-cols-2"><p><span class="block text-2xl font-bold">${ssrInterpolate($props.projects)} Projects</span></p><p><span class="block text-2xl font-bold">${ssrInterpolate($props.compounds)} Compounds</span></p><p><span class="block text-2xl font-bold">${ssrInterpolate($props.spectra)} Spectra</span></p><p><span class="block text-2xl font-bold">${ssrInterpolate($props.techniques)} Techniques</span></p></div></div></div></div><div class="relative pt-16"><div><div class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24 overflow-x-hidden py-10 border-r"><div class="px-4 max-w-xl mx-auto sm:px-6 lg:py-16 lg:max-w-none lg:mx-0 lg:px-0"><div><div><span class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600">`);
  _push(ssrRenderComponent(_component_InboxIcon, {
    class: "h-6 w-6 text-white",
    "aria-hidden": "true"
  }, null, _parent));
  _push(`</span></div><div class="mt-6"><h2 class="text-3xl font-extrabold tracking-tight text-gray-900"> Submit spectral data to us </h2><p class="mt-4 text-lg text-gray-500"> nmrXiv offers a ready-to-use NMR data management platform to host your raw instrument data and processed files for free and provides you with tools and services to analyse data. Additionally, get community support for any of your NMR spectra-related queries or provide one yourself. </p><div class="mt-6">`);
  _push(ssrRenderComponent(_component_Link, {
    href: "/login",
    class: "inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Get started `);
      } else {
        return [
          createTextVNode(" Get started ")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div><div class="mt-12 sm:mt-16 lg:mt-0"><div class="pl-4 -mr-48 sm:pl-6 md:-mr-16 lg:px-0 lg:m-0 lg:relative lg:h-full"><img class="w-full rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 lg:absolute lg:left-0 lg:h-full lg:w-auto lg:max-w-none" src="/img/welcome1.jpg" alt=""></div></div></div><div class="my-5 py-10"><div class="lg:mx-auto lg:max-w-7xl px-8 lg:grid lg:grid-cols-1 lg:grid-flow-col-dense lg:gap-24"><div><div class="md:flex md:items-center md:justify-between"><h2 class="text-2xl font-extrabold tracking-tight text-gray-900"> Trending projects </h2>`);
  _push(ssrRenderComponent(_component_Link, {
    href: "/projects",
    class: "hidden text-sm font-medium text-indigo-600 hover:text-indigo-500 md:block"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`Explore more<span aria-hidden="true"${_scopeId}> →</span>`);
      } else {
        return [
          createTextVNode("Explore more"),
          createVNode("span", { "aria-hidden": "true" }, " →")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div><div class="lg:mx-auto lg:max-w-7xl px-8 lg:grid lg:grid-cols-1 lg:grid-flow-col-dense lg:gap-24"><div><div class="max-w-7xl mx-auto py-3 px-0 sm:flex sm:items-center"><div class="mt-2 sm:mt-0"><div class="-m-1 flex flex-wrap items-center">`);
  _push(ssrRenderComponent(_component_Link, {
    href: "/projects?filter=recent",
    class: "m-1 inline-flex rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<span${_scopeId}>Recent</span>`);
      } else {
        return [
          createVNode("span", null, "Recent")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_Link, {
    href: "/projects?filter=trending",
    class: "m-1 inline-flex rounded-full border border-gray-200 items-center py-1.5 pl-3 pr-3 text-sm font-medium bg-white text-gray-900 hover:text-white hover:bg-black"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<span${_scopeId}>Trending</span>`);
      } else {
        return [
          createVNode("span", null, "Trending")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div></div><div class="lg:mx-auto lg:max-w-7xl lg:px-8 md:grid grid-cols-1 grid-flow-col-dense lg:gap-24 px-4">`);
  _push(ssrRenderComponent(_component_Projects, { limit: 8 }, null, _parent));
  _push(`</div></div></div><div class="mt-24"><div class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24 overflow-x-hidden py-10 border-l"><div class="px-4 max-w-xl mx-auto sm:px-6 lg:py-32 lg:max-w-none lg:mx-0 lg:px-0 lg:col-start-2"><div><div><span class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600">`);
  _push(ssrRenderComponent(_component_SparklesIcon, {
    class: "h-6 w-6 text-white",
    "aria-hidden": "true"
  }, null, _parent));
  _push(`</span></div><div class="mt-6"><h2 class="text-3xl font-extrabold tracking-tight text-gray-900"> Develop tools with our API </h2><p class="mt-4 text-lg text-gray-500"> Software programmers and data scientists can leverage nmrXiv API and its rich data/metadata descriptions to develop advanced toolsets/software. </p><div class="mt-6"><a target="_blank" href="https://docs.nmrxiv.org/docs/developer-guides/API" class="inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"> API Documentation </a></div></div></div></div><div class="mt-12 sm:mt-16 lg:mt-0 lg:col-start-1"><div class="pr-4 -ml-48 sm:pr-6 md:-ml-16 lg:px-0 lg:m-0 lg:relative lg:h-full"><img class="w-full rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 lg:absolute lg:right-0 lg:h-full lg:w-auto lg:max-w-none" src="https://docs.nmrxiv.org/img/postman.png" alt=""></div></div></div></div><div class="mt-24"><div class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:grid-flow-col-dense lg:gap-24 overflow-x-hidden py-10 border-r"><div class="px-4 max-w-xl mx-auto sm:px-6 lg:py-16 lg:max-w-none lg:mx-0 lg:px-0"><div><div><span class="h-12 w-12 rounded-md flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600">`);
  _push(ssrRenderComponent(_component_InboxIcon, {
    class: "h-6 w-6 text-white",
    "aria-hidden": "true"
  }, null, _parent));
  _push(`</span></div><div class="mt-6"><h2 class="text-3xl font-extrabold tracking-tight text-gray-900"> Review and analyse the spectral assignments </h2><p class="mt-4 text-lg text-gray-500"> Data deposited in nmrXiv requires original machine output files or processed raw data. With this data, researchers can annotate the missing assignments in the spectra, reanalyse previous work and offer additional help there by sharing their knowledge and expertise. </p><div class="mt-6">`);
  _push(ssrRenderComponent(_component_Link, {
    href: "#",
    class: "inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Need help with structure ellucidation? <i${_scopeId}><small${_scopeId}> (coming soon)</small></i>`);
      } else {
        return [
          createTextVNode(" Need help with structure ellucidation? "),
          createVNode("i", null, [
            createVNode("small", null, " (coming soon)")
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<br>`);
  _push(ssrRenderComponent(_component_Link, {
    href: "#",
    class: "inline-flex bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700 mt-4"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Check out active challenges <i${_scopeId}><small${_scopeId}> (coming soon)</small></i>`);
      } else {
        return [
          createTextVNode(" Check out active challenges "),
          createVNode("i", null, [
            createVNode("small", null, " (coming soon)")
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div><div class="mt-12 sm:mt-16 lg:mt-0"><div class="pl-4 -mr-48 sm:pl-6 md:-mr-16 lg:px-0 lg:m-0 lg:relative lg:h-full"><img class="w-full rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 lg:absolute lg:left-0 lg:h-full lg:w-auto lg:max-w-none" src="/img/welcome2.jpg" alt=""></div></div></div></div></div><div>`);
  _push(ssrRenderComponent(_component_FAQs, null, null, _parent));
  _push(`</div><div class="bg-gradient-to-r from-indigo-800 to-teal-700"><div class="max-w-4xl mx-auto px-4 py-16 sm:px-6 sm:pt-20 sm:pb-24 lg:max-w-7xl lg:pt-24 lg:px-8"><h2 class="text-3xl font-extrabold text-white tracking-tight"> Built to be FAIR from ground up </h2><div class="mt-12 grid grid-cols-1 gap-x-6 gap-y-12 sm:grid-cols-2 lg:mt-16 lg:grid-cols-4 lg:gap-x-8 lg:gap-y-16"><!--[-->`);
  ssrRenderList($setup.features, (feature) => {
    _push(`<div><div><span class="flex items-center justify-center h-12 w-12 rounded-md bg-white bg-opacity-10">`);
    ssrRenderVNode(_push, createVNode(resolveDynamicComponent(feature.icon), {
      class: "h-6 w-6 text-white",
      "aria-hidden": "true"
    }, null), _parent);
    _push(`</span></div><div class="mt-6"><h3 class="text-lg font-medium text-white">${ssrInterpolate(feature.name)}</h3><p class="mt-2 text-base text-indigo-200">${ssrInterpolate(feature.description)}</p></div></div>`);
  });
  _push(`<!--]--></div></div></div><div class="bg-gray-50 border-t border-b border-gray-100"><div class="max-w-7xl mx-auto content-center py-16 px-4 sm:px-6 lg:px-8"><p class="text-center text-sm font-semibold uppercase text-gray-600 tracking-wide"></p><div class="mt-6 grid grid-cols-3 gap-8"><div class="col-span-3 md:col-span-1 flex justify-center"><a target="_blank" href="https://cheminf.uni-jena.de/"><img class="h-12" src="https://www.uni-jena.de/unijenamedia/universitaet/abteilung-hochschulkommunikation/marketing/wort-bildmarke-universitaet-jena.jpg?height=335&amp;width=1000" alt="FSU Jena"></a></div><div class="col-span-3 md:col-span-1 flex justify-center"><a target="_blank" href="https://www.nfdi4chem.de/"><img class="h-12" src="/img/nmrxiv-logo.png" alt="NFDI4Chem"></a></div><div class="col-span-3 md:col-span-1 flex justify-center"><a target="_blank" href="https://www.nmrium.org/"><img class="h-12" src="/img/nmrium-logo.png" alt="NMRium"></a></div></div></div></div><div class="bg-white"><div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8 lg:flex lg:items-center lg:justify-between"><h2 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"><span class="block">Want to submit data?</span><span class="-mb-1 pb-1 block bg-gradient-to-r from-indigo-600 to-teal-600 bg-clip-text text-transparent">Get in touch or create an account.</span></h2><div class="mt-6 space-y-4 sm:space-y-0 sm:flex sm:space-x-5">`);
  _push(ssrRenderComponent(_component_Link, {
    href: "/login",
    class: "flex items-center justify-center bg-gradient-to-r from-indigo-600 to-teal-600 bg-origin-border px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white hover:from-indigo-700 hover:to-teal-700"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Get started `);
      } else {
        return [
          createTextVNode(" Get started ")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<a target="_blank" href="//docs.nmrxiv.org" class="flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-teal-800 bg-teal-50 hover:text-white hover:bg-teal-100"> Learn more </a></div></div></div></main><footer class="bg-gray-50" aria-labelledby="footer-heading"><h2 id="footer-heading" class="sr-only">Footer</h2><div class="max-w-7xl mx-auto pt-16 pb-8 px-4 sm:px-6 lg:pt-24 lg:px-8"><div class="xl:grid xl:grid-cols-3 xl:gap-8"><div class="mt-12 xl:mt-0 items center content-center">`);
  _push(ssrRenderComponent(_component_jet_application_logo, { class: "p-0.5 ml-1.5" }, null, _parent));
  _push(`</div><div class="grid grid-cols-2 gap-8 xl:col-span-2"><div class="md:grid md:grid-cols-2 md:gap-8"><div><h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase"> Quick links </h3><ul role="list" class="mt-4 space-y-4"><!--[-->`);
  ssrRenderList($setup.footerNavigation.quicklinks, (item) => {
    _push(`<li>`);
    _push(ssrRenderComponent(_component_Link, {
      href: item.href,
      class: "text-base text-gray-500 hover:text-gray-900"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate(item.name)}`);
        } else {
          return [
            createTextVNode(toDisplayString(item.name), 1)
          ];
        }
      }),
      _: 2
    }, _parent));
    _push(`</li>`);
  });
  _push(`<!--]--><li><a target="_blank" href="https://docs.nmrxiv.org/FAQs.html" class="text-base text-gray-500 hover:text-gray-900"> FAQs </a></li></ul></div><div class="mt-12 md:mt-0"><h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase"> Support </h3><ul role="list" class="mt-4 space-y-4"><!--[-->`);
  ssrRenderList($setup.footerNavigation.support, (item) => {
    _push(`<li><a target="_blank"${ssrRenderAttr("href", item.href)} class="text-base text-gray-500 hover:text-gray-900">${ssrInterpolate(item.name)}</a></li>`);
  });
  _push(`<!--]--></ul></div></div><div class="md:grid md:grid-cols-2 md:gap-8"><div><h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase"> About </h3><ul role="list" class="mt-4 space-y-4"><!--[-->`);
  ssrRenderList($setup.footerNavigation.About, (item) => {
    _push(`<li><a target="_blank"${ssrRenderAttr("href", item.href)} class="text-base text-gray-500 hover:text-gray-900">${ssrInterpolate(item.name)}</a></li>`);
  });
  _push(`<!--]--></ul></div><div class="mt-12 md:mt-0"><h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase"> Legal </h3><ul role="list" class="mt-4 space-y-4"><!--[-->`);
  ssrRenderList($setup.footerNavigation.legal, (item) => {
    _push(`<li>`);
    _push(ssrRenderComponent(_component_Link, {
      href: item.href,
      class: "text-base text-gray-500 hover:text-gray-900"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate(item.name)}`);
        } else {
          return [
            createTextVNode(toDisplayString(item.name), 1)
          ];
        }
      }),
      _: 2
    }, _parent));
    _push(`</li>`);
  });
  _push(`<!--]--></ul></div></div></div></div><div class="my-12 px-12 border-t border-gray-200 pt-8 md:flex md:items-center md:justify-between lg:mt-16"><div class="flex space-x-6 md:order-2"><!--[-->`);
  ssrRenderList($setup.footerNavigation.social, (item) => {
    _push(`<a${ssrRenderAttr("href", item.href)} class="text-gray-400 hover:text-gray-500"><span class="sr-only">${ssrInterpolate(item.name)}</span>`);
    ssrRenderVNode(_push, createVNode(resolveDynamicComponent(item.icon), {
      class: "h-6 w-6",
      "aria-hidden": "true"
    }, null), _parent);
    _push(`</a>`);
  });
  _push(`<!--]--></div><p class="mt-8 text-base text-gray-400 md:mt-0 md:order-1"> © 2023 nmrXiv. All rights reserved. </p></div></div></footer></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Welcome.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Welcome = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Welcome as default
};
