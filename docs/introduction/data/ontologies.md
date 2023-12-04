# Ontologies
nmrXiv aims to provide the data, present it unambiguously, and make it [FAIRer](https://www.go-fair.org/fair-principles) (Findable, Accessible, Interoperable, Reusable). It focuses on making the data not only human-readable, but machine-readable as well, so that researchers can leverage advanced Machine Learning, Named Entity Recognition, and other language processing techniques to re-analyze the data. This can be facilitated by using ontologies.

## Ontologies usage in nmrXiv
nmrXiv will use ontologies to annotate as many fields as possible, along with requesting some fields' values to be ontology-driven, including all units. In addition to being ontologies-consumers, being an NMR data repository, nmrXiv is suited for gathering missing terms via user submission and contributing them to ontologies relevant to NMR data, or repositories data in general (in other words, to domain-specific and domain-independent ontologies). This will be achieved through collaboration with the [NFDI4Chem Terminology Service](https://terminology.nfdi4chem.de/ts/index) from **Task Area 6** in NFDI4Chem.

### Ontology Component
The ontology component will be provided as an open-source, platform-agnostic tool compatible with JavaScript interfaces. This component will ensure a user-friendly experience in finding ontology values from any terminology service supported by the platform using the component ([NFDI4Chem Terminology Service](https://terminology.nfdi4chem.de/ts/index) in the case of nmrXiv). It will simplifiy the search for terms by providing auto-complete functionality, while enabling the platform to restrict the suggested terms to selected ontologies that are relavent to the field in hand. The development of the component is currently going on [this branch of nmrXiv repository](https://github.com/NFDI4Chem/nmrxiv/tree/ontology-component).

nmrXiv will make use of the available [APIs](https://terminology.nfdi4chem.de/ts/api) in the [Terminology Service](https://terminology.nfdi4chem.de/ts/index), which, along with more general possibilities such as retrieving an entire ontology, enable retrieving a specific term and restricting the search to a list of ontologies. Additionally, they support the suggestion of new terms. For a detailed description of the available endpoints and parameters, please check the [API Documentation](https://terminology.nfdi4chem.de/ts/api).

### Schemas Export with Ontologies 
Metadata describing the submitted projects/studies/datasets can be exported in nmrXiv as files complying with a schema chosen by the user from the [schemas supported in nmrXiv](/introduction/data/data-schema). Many of those schemas support ontologies usage such as [ISA](https://isa-tools.org/) and [Bioschemas](https://bioschemas.org/), which will efficiently reflect the use of ontology terms in the exported files by providing links and identifiers to the intended terms.

### Ontologies of Interest in nmrXiv
Several factors have been considered to pick the ontologies to use in nmrXiv,:
- Flexibility: A flexible workflow of new terms submission has a high priority for nmrXiv as it will contribute to ontologies curation. 
- Usage in the NMR domain: Ontologies frequently used by other NMR repositories and already known to the chemists have been considred.
- Specialisation: Specialised ontologies usually provide more extensive coverage of the field with more accurate definitions of terms.

#### Domain-Independent Ontologies:
The following ontologies provide identifiers, references, formats, and possible operations to define experiments:

* [Bioinformatics operations, data types, formats, identifiers and topics - EDAM](https://terminology.nfdi4chem.de/ts/ontologies/edam)
* [REPRODUCE-ME Ontology - REPR](https://www.ebi.ac.uk/ols/ontologies/reproduceme)
* [Medical Subject Headings - MeSH](https://meshb-prev.nlm.nih.gov/treeView)
* [NCI Thesaurus OBO Edition - NCIt](https://www.ebi.ac.uk/ols/ontologies/ncit)

#### Domain-Specific Ontologies:

* [nuclear magnetic resonance CV - NMR](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv):
nmrCV is highly relevant for nmrXiv as it covers many aspects of the NMR assay and the resulting spectral data, including the files formats, dimensionality, instruments, used materials, and later data processing.

* [Chemical Methods Ontology - CHMO](https://terminology.nfdi4chem.de/ts/ontologies/chmo):
 Methods used to collect data in chemical experiments, including NMR spectroscopy, are covered under this ontology.
 
* [NCBI organismal classification - NCBITAXON](https://www.ebi.ac.uk/ols/ontologies/ncbitaxon): 
 Widely used curated classification for organisms.
 
* [The BRENDA Tissue Ontology - BTO](https://www.ebi.ac.uk/ols/ontologies/bto):
 It covers organism parts and variants. Examples include tissues, cell lines, cell types and cell cultures.

* [Experimental Factor Ontology - EFO](https://www.ebi.ac.uk/ols/ontologies/efo):
 For many experimental variables, which can be used, e.g., to describe the tissue and the organism part.
 
* [NCI Thesaurus OBO Edition - NCIt](https://www.ebi.ac.uk/ols/ontologies/ncit): 
It includes broad coverage of the cancer domain, including cancer-related diseases, findings and abnormalities. 

* [Medical Subject Headings - MeSH](https://meshb-prev.nlm.nih.gov/treeView):
 Medical Subject Headings is a comprehensive controlled vocabulary for indexing journal articles and books in the life sciences.

* [Ontology for Biomedical Investigations - OBI](https://terminology.nfdi4chem.de/ts/ontologies/obi):
Ontology for Biomedical Investigations (OBI) serves as a resource for annotating biomedical investigations, including the study design, protocols and instrumentation used, the data generated and the types of analysis performed on the data. This ontology arose from the Functional Genomics Investigation Ontology (FuGO) and contains both terms common to all biomedical investigations, including functional genomics investigations and those that are more domain-specific.

* [CHEBI Integrated Role Ontology - CHIRO](https://terminology.nfdi4chem.de/ts/ontologies/chiro):
CHEBI provides a distinct role hierarchy for chemical entities.
* [chemical information ontology - CHEMINF](https://terminology.nfdi4chem.de/ts/ontologies/cheminf):
CHEMINF provides terms for cheminformatics software and algorithms, such as chemical identifiers and others.

* [Units of measurement ontology - UO](https://terminology.nfdi4chem.de/ts/ontologies/uo):
For metrical units.








