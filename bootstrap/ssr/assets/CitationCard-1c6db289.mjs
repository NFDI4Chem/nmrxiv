import { ssrRenderList, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import { useSSRContext } from "vue";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main$1 = {
  components: {},
  props: ["authors"],
  methods: {
    getOrcidLink(orcidId) {
      var link = "#";
      if (orcidId) {
        link = "https://orcid.org/" + orcidId;
      }
      return link;
    },
    getTarget(id) {
      var target = null;
      if (id) {
        target = "_blank";
      }
      return target;
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<!--[-->`);
  ssrRenderList($props.authors, (author) => {
    _push(`<div class="select-text relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-top space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"><div class="flex-1 min-w-0"><div><a class="focus:outline-none cursor-pointer select-text"${ssrRenderAttr("href", $options.getOrcidLink(author.orcid_id))}${ssrRenderAttr("target", $options.getTarget(author.orcid_id))}><div class="mb-1 flex items-center justify-between"><span class="text-sm font-medium text-teal-900">${ssrInterpolate(author.title)} ${ssrInterpolate(author.given_name)} ${ssrInterpolate(author.family_name)}</span>`);
    if (author.pivot && author.pivot.contributor_type) {
      _push(`<button class="text-sm text-gray-400"><span class="px-2 inline-flex text-xs leading-5 font-semibold capitalize rounded-full bg-green-100 text-green-800">${ssrInterpolate(author.pivot.contributor_type ? author.pivot.contributor_type : "Researcher")}</span></button>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
    if (author.affiliation) {
      _push(`<p class="text-xs text-gray-500">${ssrInterpolate(author.affiliation)}</p>`);
    } else {
      _push(`<!---->`);
    }
    if (author.orcid_id) {
      _push(`<p class="text-xs text-teal-900"><b class="text-gray-500">ORCID iD:</b> ${ssrInterpolate(author.orcid_id)}</p>`);
    } else {
      _push(`<!---->`);
    }
    if (author.email_id) {
      _push(`<p class="text-xs text-gray-500"><b class="text-gray-500">Email-Id:</b> ${ssrInterpolate(author.email_id)}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</a></div></div></div>`);
  });
  _push(`<!--]-->`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/AuthorCard.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const AuthorCard = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {},
  props: ["citations"],
  methods: {
    getCitationLink(doi) {
      var link = "#";
      if (doi) {
        link = "https://doi.org/" + doi;
      }
      return link;
    },
    getTarget(id) {
      var target = null;
      if (id) {
        target = "_blank";
      }
      return target;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<!--[-->`);
  ssrRenderList($props.citations, (citation) => {
    _push(`<div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-top space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-teal-500"><div class="flex-1 min-w-0"><a class="focus:outline-none cursor-pointer select-text"${ssrRenderAttr("href", $options.getCitationLink(citation.doi))}${ssrRenderAttr("target", $options.getTarget(citation.doi))}><p class="text-sm font-medium text-gray-900">${ssrInterpolate(citation.title)}</p><p class="text-sm text-teal-500">${ssrInterpolate(citation.authors)}</p><p class="text-sm text-gray-500">${ssrInterpolate(citation.citation_text)}</p>`);
    if (citation.doi) {
      _push(`<p class="text-sm font-sm text-gray-500"> DOI - <a${ssrRenderAttr("href", citation.doi)} class="text-teal-900">${ssrInterpolate(citation.doi)}</a></p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div class="text-sm text-gray-500 truncate line-clamp-2 ...">${citation.abstract}</div></a></div></div>`);
  });
  _push(`<!--]-->`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/CitationCard.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const CitationCard = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  AuthorCard as A,
  CitationCard as C
};
