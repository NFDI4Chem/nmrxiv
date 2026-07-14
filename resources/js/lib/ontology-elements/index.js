import { defineCustomElement } from "vue";

import OntologyAutocomplete from "./OntologyAutocomplete.ce.vue";
import OntologyTermAnnotation from "./OntologyTermAnnotation.ce.vue";

const OntologyAutoCompleteElement = defineCustomElement(OntologyAutocomplete);
const OntologyTermAnnotationElement = defineCustomElement(
    OntologyTermAnnotation
);

if (typeof window !== "undefined") {
    const { customElements } = window;

    if (!customElements.get("ontology-autocomplete")) {
        customElements.define(
            "ontology-autocomplete",
            OntologyAutoCompleteElement
        );
    }

    if (!customElements.get("ontology-term-annotation")) {
        customElements.define(
            "ontology-term-annotation",
            OntologyTermAnnotationElement
        );
    }
}
