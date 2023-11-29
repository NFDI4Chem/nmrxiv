# Data Schemas

Data schemas enable structuring the data in a well-designed form by providing precise definitions of entities with relations among them. Additionally, they enable data indexing and feasible metadata export making those schema a valuable asset to improve data findability on the web, and to enhance machine-readability and data analysis, especially when considering that many of the available schemas support ontologies usage by accepting/requesting values to be provided as ontology terms and providing links and identifiers to the terms.

## nmrXiv Data Model 
nmrXiv uses the ISA [(Investigation, Study, Assay)](https://isa-tools.org/format/specification.html) approach to capture the experimental metadata in a structured format, though it has opted for a different terminology than the one used by ISA:
- Investigation ⇛ Project
- Study ⇛ Study 
- Assay ⇛ Experiment 

For more details about the objects and relations among them, please check the [Data Model folder](/submission-guides/data-model/project.html)

## Data Schemas in nmrXiv
nmrXiv focus on obtaining DOIs for the different entities and describing the data with ontology terms led to prioritizing a list of schemas. Some have been already implemented, and others will be coming soon. Here you can find a list of those schemas:

### Bioschemas (Repository, Project, Studay, Dataset, Sample)

[Bioschemas](https://bioschemas.org/) aims to improve the Findability of life sciences resources (such as datasets) on the Web by using [Schema.org](https://schema.org/) markup in the websites, so that they are indexable by search engines and other services. Bioschemas is making two main contributions:
- Proposing new [types and properties](https://bioschemas.org/types) to [Schema.org](https://schema.org/) to allow for the description of life science resources.
- Defining usage [profiles](https://bioschemas.org/profiles) over the [Schema.org](https://schema.org/) types that identify the essential properties to use in describing a resource.[1]

nmrXiv uses Bioschemas profiles to describe both data structuring components (such as datasets) and experiment-related components (such as samples and molecules). Missing profiles will be contributed to Bioschemas and discussion about NMR-specific schemas is going on. Here is a list of the used profiles/types:

#### [DataCatalog](https://bioschemas.org/profiles/DataCatalog/0.3-RELEASE-2019_07_01)
DataCatalog profile can be used as a guide to describe repositories, and thus, it is used to describe nmrXiv repository.[DataCatalog API endpoint link](https://www.postman.com/nmrxiv-jena/workspace/nmrxiv/request/17195598-31c24120-14f0-41c4-b511-292fd310db79)

#### [Project](https://github.com/NFDI4Chem/nmrxiv/blob/main/app/Models/Bioschemas/Project.php) 
[Project type](https://schema.org/Project) already exists in [Schema.org](https://schema.org/), but it refers to enterprises rather than a scientific investigation. However, as the project metadata is somehow similar to the study metadata (id, name, URL, etc.), nmrXiv initially defines a new project type similarly to the [study](https://bioschemas.org/types/Study/0.3-DRAFT) provided by [Bioschemas](https://bioschemas.org/). Further enhancements will be applied upon discussion with field experts, and possible submission to [Bioschemas](https://bioschemas.org/) will take place. Project properties allow linking with the included studies and their inner data. [Project API endpoint link](https://www.postman.com/nmrxiv-jena/workspace/nmrxiv/request/17195598-d7481762-e431-4975-b786-9cfd7f007d56).

#### [Study](https://bioschemas.org/profiles/Study/0.2-DRAFT)
Study profile is currently still a draft in Bioschemas. It is used in nmrXiv to describe the study, and its properties are used to link the study to the corresponding sample and the contained datasets, while being a part of a project. [Study API endpoint link](https://www.postman.com/nmrxiv-jena/workspace/nmrxiv/request/17195598-910d6c75-2a1d-42ff-9f0d-b669bbfa6db5).

#### [Sample](https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10)
Sample profile is used in nmrXiv to describe NMR samples. It is attached to the study as a value of "about" property indicating that the study is about this sample. Furthermore, the molecules existing in the Sample are added to it as a value of the property "additionalProperty". There is no API endpoint for the Sample as it can be found in the Study.

#### [MolecularEntity](https://bioschemas.org/profiles/MolecularEntity/0.5-RELEASE)
MolecularEntity can be used for any constitutionally or isotopically distinct atom, molecule, ion, etc., identifiable as a separately distinguishable entity. At the moment, it is used in nmrXiv only to describe the molecules in a sample, but further usages such as describing the nucleus or the solvent are possible too. There is no API endpoint for the MolecularEntity as it can be found in the Study ⇛ Sample.

#### [Dataset](https://bioschemas.org/profiles/Dataset/1.0-RELEASE)
Dataset schema is used in nmrXiv to describe the datasets there. Each dataset is linked to the parent project and study through the property "isPartOf". [Dataset API endpoint link](https://www.postman.com/nmrxiv-jena/workspace/nmrxiv/request/17195598-0408e3df-3c8f-4899-b440-eda6d8f71193).


### ISA (Sample, Assay, Ontology)

[ISA is a metadata framework](https://isa-tools.org/) to manage a diverse set of life science, environmental, and biomedical experiments that employ one or a combination of technologies, built around the **I**nvestigation (the project context), **S**tudy (a unit of research), and **A**ssay (analytical measurement) data model and serializations (tabular, JSON and RDF).[2]

nmrXiv will comply with ISA Schema specifications to capture NMR metadata (Assay, Sample, and Ontology) to provide a detailed description of the experimental metadata for both synthetic and biological experiments (i.e., sample characteristics, technology and measurement types, sample-to-data relationships). ISA enables interoperability with other platforms and services while keeping the information in a simplistic and open-text format that is FAIR compliant. nmrXiv back-end will be designed to support ISA for newly entered data. However, ISA still comes with few limitations from the data management (repository) perspective, configurable templates on the go and redundant data being some of them. nmrXiv will contribute to extending the existing ISA models with NMR domain-specific requirements to ensure total flexibility for the end user to define their own templates while still being complaint with ISA Specifications and the minimum information standards. 

![ISA Specifications](/img/nmrXiv-isa.png) 

#### ISA Sample Schema
In a Study object, ISA records the provenance of biological [samples](https://github.com/ISA-tools/isa-api/blob/master/isatools/resources/schemas/isa_model_version_1_0_schemas/core/sample_schema.json) from [source](https://github.com/ISA-tools/isa-api/blob/master/isatools/resources/schemas/isa_model_version_1_0_schemas/core/source_schema.json) material through a collection process to sample material represented with directed acyclic graphs (direct graphs with no loops/cycles). The pattern of nodes is usually formed of a source material node, followed by a sample collection process node, followed by a sample material node.

(source material)->(sample collection)->(sample material)

These study graphs MAY split and pool depending on how the samples are collected.

In a splitting example, multiple samples might be derived from the same source:

(source material 1)->(sample collection)->(sample material 1)

(source material 1)->(sample collection)->(sample material 2)

In a pooling example, multiple sources may be used to create a single sample:

(source material 1)->(sample collection)->(sample material 1)

(source material 2)->(sample collection)->(sample material 1)

However, sample collection applies only to biological samples where the source comes from a certain organism, while with synthetic samples we have a certain compound that gets dissolved in a solvent to create the sample. We are trying to capture this concept in ISA adopting mock-ups derived from actual samples/molecules found in non-biological repositories such as [Chemotion](https://www.chemotion-repository.net/welcome) and [NMRShiftDB](https://nmrshiftdb.nmr.uni-koeln.de/), and compare them with a biological sample coming from the biological repository [MetaboLights](https://www.ebi.ac.uk/metabolights/). Please find the mentioned mock-ups [here](https://github.com/NFDI4Chem/schema/tree/main/ISA). Those mock-ups are still under development, especially due to the absence of the relevant ISA configurations in addition to the continuous update upon discussion.

nmrXiv approach to represent a synthetic sample is to take the compound found in Chemotion or NMRShiftDB as a source, and apply an NMR sample protocol where a solvent gets added to generate the sample, and where the solvent becomes one of the sample characteristics. This NMR sample undergoes an NMR spectroscopy protocol with parameters such as the instrument, magnetic field strength, pulse sequence name, temperature and others. This protocol will result in a data file, which, in return, undergoes an NMR assay protocol with some NMR processing software. This approach is still under discussion and not confirmed.

However, we are still facing issues regarding:
* Missing ontology terms to represent the characteristics and factors categories.
* The limited use of ontologies in both Chemotion and NMRShiftDB, so we picked terms representing the fields and values in the mock-ups despite the frequent use of free text in the actual models.
* Representing characteristics and factors categories when the category is a combination of two or more ontology terms. e.g., "number of rings", "duration of incubation".

#### ISA Ontology Schema
[ISA ontology reference schema](https://github.com/ISA-tools/isa-api/blob/master/isatools/resources/schemas/isa_model_version_1_0_schemas/core/ontology_annotation_schema.json) can capture ontology terms where those "annotation_values" are linked to their "term_source" which is the ontology the term comes from, and the "term_accession", which is the link to that term.

#### ISA Converters
nmrXiv will contribute to developing data converters as independent microservices (python-based applications) to convert data from and to ISA, which enables nmrXiv and other repositories to utilize these converters as plugins to capture and export data. nmrXiv will also leverage the existing tool set in the domain, such as [nmrml2isa](https://github.com/ISA-tools/nmrml2isa) converter, to capture rich metadata from the open nmrML files. **nmrml2isa** is a Python3 program that can be used to generate an ISA-Tab structured investigation out of nmrML files, providing the backbone of a study that can then be edited further to provide additional metadata that cannot be automatically extracted. 

### IUPAC - FAIRSpec (Spectra)
nmrXiv will contribute to and adopt the definition of standardized metadata being developed within IUPAC [Project](https://github.com/IUPAC/IUPAC-FAIRSpec) "Development of a Standard for FAIR Data Management for Spectroscopic Data", which covers spectroscopic data including NMR spectroscopy. But this project is still preliminary and under development. 

### DataCite (Data Citation and Discovery)

The [DataCite Metadata Schema](https://schema.datacite.org/) is a list of core metadata properties chosen for accurate and consistent identification of a resource for citation and retrieval purposes, along with recommended use instructions. It primarily supports citation of data rather than discipline-specific metadata. 

Complying with the DataCite schema enables the creation of new [Digital Object Identifiers (DOIs)](https://www.doi.org/) and their assignment to content on nmrXiv, which is essential for identifiers' persistence. Only mandatory property values are needed to obtain the DOI, but still, there is the ability of rich citing-metadata description with recommended and optional properties. nmrXiv will mostly treat some recommended fields as mandatory since they are expected to become mandatory in DataCite in the near future and to make sure all essential metadata is captured. Mandatory fields include the DOI, Creator, Title, Publisher, PublicationYear, and ResourceType. Additionally, using DataCite schema will enable easy export of the metadata in XML format.

### OpenAIRE Guidelines (Data Citation and Discovery)

Complying with [OpenAIRE Guidelines for Data Archive Managers](https://guidelines.openaire.eu/en/latest/) ensures compatibility with the OpenAIRE infrastructure, which facilitates interoperability with other repositories already adhering to those guidelines, enhancing data exposure and visibility. OpenAIRE has already adopted the DataCite Metadata Schema, yet with some minor adjustments, such as accepting other persistent identifier schemes rather than the DOI and some changes in the obligations of properties.

 # References
1- [https://bioschemas.org/](https://bioschemas.org/)
2- [https://isa-tools.org/](https://isa-tools.org/)