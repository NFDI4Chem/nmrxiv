import { XMarkIcon } from "@heroicons/vue/24/outline";
import Popper from "popper.js";
import { useSSRContext, mergeProps, resolveComponent } from "vue";
import { ssrRenderAttrs, ssrRenderSlot, ssrRenderTeleport, ssrRenderStyle, ssrRenderList, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-cc2b3d55.mjs";
const _sfc_main$1 = {
  props: {
    placement: {
      type: String,
      default: "bottom-end"
    },
    boundary: {
      type: String,
      default: "scrollParent"
    },
    autoClose: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      show: false
    };
  },
  watch: {
    show(show) {
      if (show) {
        this.$nextTick(() => {
          this.popper = new Popper(this.$el, this.$refs.dropdown, {
            placement: this.placement,
            modifiers: {
              preventOverflow: {
                boundariesElement: this.boundary
              }
            }
          });
        });
      } else if (this.popper) {
        setTimeout(() => this.popper.destroy(), 100);
      }
    }
  },
  mounted() {
    document.addEventListener("keydown", (e) => {
      if (e.keyCode === 27) {
        this.show = false;
      }
    });
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<button${ssrRenderAttrs(mergeProps({ type: "button" }, _attrs))}>`);
  ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
  ssrRenderTeleport(_push, (_push2) => {
    if ($data.show) {
      _push2(`<div><div style="${ssrRenderStyle({ "position": "fixed", "top": "0", "right": "0", "left": "0", "bottom": "0", "z-index": "99998", "background": "black", "opacity": "0.2" })}"></div><div style="${ssrRenderStyle({ "position": "absolute", "z-index": "99999" })}">`);
      ssrRenderSlot(_ctx.$slots, "dropdown", {}, null, _push2, _parent);
      _push2(`</div></div>`);
    } else {
      _push2(`<!---->`);
    }
  }, "body", false, _parent);
  _push(`</button>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Dropdown.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Dropdown = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    XMarkIcon,
    Button: Dropdown
  },
  props: {},
  setup() {
  },
  data() {
    return {
      ustring: null,
      show: false
    };
  },
  computed: {
    announcements() {
      let announcements = this.$page.props.config.announcements;
      this.ustring = this.getHash(
        this.slugify(announcements.map((a) => a.message).toString())
      );
      let userAcknowledged = localStorage.getItem(
        "announcement_" + this.ustring
      );
      this.show = announcements.length > 0 && userAcknowledged == null;
      return announcements;
    }
  },
  methods: {
    acknowledgeAnnouncement() {
      let localItems = this.findLocalItems("announcement_*");
      if (localItems.length > 0) {
        localItems.forEach((element) => {
          localStorage.removeItem(element.key);
        });
      }
      localStorage.setItem("announcement_" + this.ustring, true);
      this.show = false;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_XMarkIcon = resolveComponent("XMarkIcon");
  if ($options.announcements.length > 0 && $data.show) {
    _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed z-50 bg-yellow-400 border-b w-screen border-black-800" }, _attrs))}><div class="max-w-7xl mx-auto py-1 px-6"><p class="text-sm text-justify text-dark font-semibold animate-pulse"><!--[-->`);
    ssrRenderList($options.announcements, (announcement) => {
      _push(`<span><span>${announcement.message}</span></span>`);
    });
    _push(`<!--]--></p><div class="absolute inset-y-0 right-0 pt-1 pr-1 flex items-start sm:pt-1 sm:pr-2 sm:items-start"><button type="button" class="flex p-1 rounded-md focus:outline-none hover:bg-yellow-300 focus:ring-2 focus:ring-white"><span class="sr-only">Dismiss</span>`);
    _push(ssrRenderComponent(_component_XMarkIcon, {
      class: "h-3 w-3 text-dark",
      "aria-hidden": "true"
    }, null, _parent));
    _push(`</button></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/AnnouncementBanner.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const AnnouncementBanner = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  AnnouncementBanner as A,
  Dropdown as D
};
