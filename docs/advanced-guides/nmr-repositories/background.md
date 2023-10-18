# Background

**[nmrXiv](https://nmrxiv.org/)** will host NMR data from multiple sources, starting from pure compounds to provide reference spectra, reaching spectra generated from metabolites found in biological samples as mixtures. To facilitate dealing with this range of heterogeneity in future data and metadata, we have started by performing some quantitative and qualitative analysis on the available NMR repositories, including domain-specific and generic databases, to see how data have been handled so far, and to make use of the experience accumulated with every new database and dataset. 

## Aim of The Analysis and How It Will Influence nmrXiv
We are interested in knowing what metadata was covered, and how often (in how many studies) this metadata was available. That will help decide what metadata to be asked for from users when submitting their datasets. Also, what metadata to be optional/recommended and what to be mandatory. We plan to allow users to submit any metadata they have while recommending or requesting others. 

We would also like to find out what aspects were challenging and how they were tackled. Then, we move to the ontologies to see which ones are commonly used and relevant to NMR data to help us shape the process of data reporting in **[nmrXiv](https://nmrxiv.org/)**. We hope to make our data [FAIRer](https://www.go-fair.org/fair-principles/), and more machine-readable by using this knowledge.

By identifying common issues encountered while reporting data, we can avoid repeating the same mistakes. We can also work on approaches to retrospectively correct the data before importing it into nmrXiv.

## The Overview Scope

* Domain-specific databases (WIP)
    * Organic Chemistry Databases: [NMRShiftDB](https://nmrshiftdb.nmr.uni-koeln.de/)
    * Metabolomics: [MetaboLights](https://www.ebi.ac.uk/metabolights/) and [Metabolomics Workbench](https://www.metabolomicsworkbench.org/).
* Public Datasets:
    * [CENAPT](https://dataverse.harvard.edu/dataverse/cenapt)
* Generic Databases and Journals
    * [EuropePMC](https://europepmc.org/) (Will be done in the future)

So far, all covered NMR repositories are biology-oriented ones, but non-biological metadata was also covered.  

### Aspects Investigated
 - Meta-Data inconsistencies
 - Controlled Vocabulary - Machine readability
 - Common parameters reported and missing information

### Parameters of Interest
The covered metadata includes sample and assay (experiment) metadata. It also considers the ontologies used. Please find the GitHub repository with Python scripts and Jupyter notebooks to extract the metadata and visualize it [here](https://github.com/NFDI4Chem/repo-scripts).

Here is a list of the covered parameters
 - Dimensionality
 - Spectrometer frequency
 - Atomic nuclei
 - Temperature
 - Solvent 
 - Instruments
 - pH
 - Organism
 - Organism part
 - Variant
 - Dataset size

 We will look into more parameters, such as the tube type, NMR probe, and magnetic field strength. 