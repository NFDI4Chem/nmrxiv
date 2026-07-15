const TERMINOLOGY_API_BASE_URL = "https://api.terminology.tib.eu";

function normalizeDescription(element) {
    const description = element.definition ?? element.description ?? [];

    if (Array.isArray(description)) {
        return description;
    }

    return description ? [description] : [];
}

export function mapClassElementToOntologyDoc(element) {
    const curie = element.curie ?? "";
    const colonIndex = curie.indexOf(":");

    return {
        label: Array.isArray(element.label)
            ? element.label[0]
            : element.label ?? "",
        iri: element.iri ?? "",
        ontology_prefix:
            colonIndex > 0
                ? curie.slice(0, colonIndex)
                : element.ontologyId ?? "",
        type: Array.isArray(element.type)
            ? element.type[0]
            : element.type ?? "",
        description: normalizeDescription(element),
        short_form: curie || element.iri,
        obo_id: curie,
    };
}

function deduplicateByIri(docs) {
    const seen = new Set();

    return docs.filter((doc) => {
        if (!doc.iri || seen.has(doc.iri)) {
            return false;
        }

        seen.add(doc.iri);

        return true;
    });
}

async function fetchClassResults(url) {
    const response = await fetch(url);

    if (!response.ok) {
        return [];
    }

    const data = await response.json();

    return (data.elements ?? []).map(mapClassElementToOntologyDoc);
}

export async function searchOntologyClasses(searchTerm, ontologies = "") {
    if (!searchTerm) {
        return [];
    }

    let ontologyId = ontologies;
    let query = searchTerm;

    const colonIndex = searchTerm.indexOf(":");
    if (colonIndex > 0) {
        ontologyId = searchTerm.slice(0, colonIndex);
        query = searchTerm.slice(colonIndex + 1);
    }

    const params = new URLSearchParams({
        search: query,
        page: "0",
        size: "10",
        includeObsoleteEntities: "false",
    });

    if (ontologyId) {
        const ontologyUrl = `${TERMINOLOGY_API_BASE_URL}/api/v2/ontologies/${encodeURIComponent(
            ontologyId
        )}/classes?${params}`;
        const ontologyResults = await fetchClassResults(ontologyUrl);

        if (ontologyResults.length > 0) {
            return deduplicateByIri(ontologyResults);
        }
    }

    const globalUrl = `${TERMINOLOGY_API_BASE_URL}/api/v2/classes?${params}`;

    return deduplicateByIri(await fetchClassResults(globalUrl));
}

export { TERMINOLOGY_API_BASE_URL };
