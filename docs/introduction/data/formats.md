# NMR File Formats

**[nmrXiv](https://nmrxiv.org)** is the first FAIR and Open archive to preserve NMR data in its original instrument format, and promote Open Data and Open Standards to maximize the long-term sustainability of the resource and FAIRness of the archived data. 

To reduce user burden and foster immediate utility, **[nmrXiv](https://nmrxiv.org)** accepts raw NMR data in all original instrument formats. Upon uploading files, the platform converts all those files to available **Open** data formats, such as [JCAMP-DX](https://opg.optica.org/as/abstract.cfm?uri=as-47-8-1093), [NMReData](https://doi.org/10.1002/mrc.4737), and [nmrML](https://doi.org/10.1021/acs.analchem.7b02795). The content stored in existing formats is heterogeneous, and no single format is currently capable of fully capturing all information from NMR experiments and data processing.

As native instrument formats represent the most available data, they will be preserved and made available to users. This approach ensures traceability and secure long-term access to every detail of measured data as new formats appear or existing ones evolve. At the same time, the availability and interoperability of several formats will facilitate the re-use of data with external tools. 

### NMR Vendor Formats
FAIR sharing raw NMR data enhances the quality of research and dissemination quality. Historically, the sharing of original spectroscopic data has been hampered by technological limitations such as data storage size and gaps between digital data and paper/printed publishing format. Therefore, researchers are accustomed to extracting the (perceived most) important information from NMR spectra and summarising them in a very compact text form, typically in tables or text compilations of chemical shifts and coupling constants (e.g., ACS-style listings). More recently, they became accompanied by spectra images as evidence of discovering a new molecule. However, this practice constitutes a drastic loss of information relative to the wealth
of detail in the raw NMR data. This approach leads to incomplete information and curbs peer review and re-use processes. It can lead to wrong or misidentified structures, affecting ~15% of terpenoid NPs, as shown here [1,2,3].

Moreover, peaks associated with impurities or byproducts are not reported and remain invisible to the reader or reviewer. The AnaPurNa [4,5] and UIC CENAPT Raw Data Initiative studies [6] provide evidence that purity information can be gleaned from raw NMR data. Until such data are shared broadly, it remains difficult or even impossible for subsequent studies to resolve discrepancies about the structure, activity, and purity of a given compound or sample(s). **[nmrXiv](https://nmrxiv.org)** will allow the community to leverage the vast informational power of raw NMR data to enhance the reproducibility of reported outcomes, enable innovative re-evaluation and advance the quality of NP research more broadly.

#### Vendor format examples

| Short Name | Maintainer | File Extension |
|------------|------------|----------------|
| Bruker     | Bruker     | Folder         |
| JEOL       | JEOL       | .jdf           |
| Agilent    | Agilent    | Folder         |
| Magritek   | Magritek   | .1d, .2d       |

### Software-Specific NMR Formats

Tools such as [TopSpin](https://www.bruker.com/en/products-and-solutions/mr/nmr-software/topspin.html), [Mnova](https://mestrelab.com/software/mnova/nmr/), and [ACD/Labs](https://www.acdlabs.com/solutions/nmr-spectroscopy/) are widely used among the NMR research community to process and analyze NMR spectra.

It is impossible and highly undesirable to reproduce everything these tools offer on **[nmrXiv](https://nmrxiv.org)**. At the same time, we cannot request the researcher to recreate the analysis using the tools we support in **[nmrXiv](https://nmrxiv.org)**.

These tools' output usually gets stored in proprietary formats, such as .spectrus and .mnova. These formats are closed, and they are often updated with breaking changes.

To avoid losing all the rich (meta-)data from these third-party tools, it is advantageous to export it into standard formats (nmreData, Jcamp, nmrML) rather than being able to parse their proprietary output formats.

These tools already support export in some of the standard/widely known formats, such as [JCAMP-DX](https://opg.optica.org/as/abstract.cfm?uri=as-47-8-1093) ([Mnova](https://resources.mestrelab.com/top-highlights-in-mnova-14-3/), [ACD/Labs](https://www.acdlabs.com/technical-support/supported-data-formats/) (for 1D NMR only)), and [NMReData](https://doi.org/10.1002/mrc.4737) ([Mnova, ACD/Labs, TopSpin](https://analyticalsciencejournals.onlinelibrary.wiley.com/doi/10.1002/mrc.5146)), which contain a subset (molecular structure, assignments, peaks, integrals etc.) of the complete data but are still valuable.

**[nmrXiv](https://nmrxiv.org)** will drive the collective efforts to collaborate with [ACD/Labs](https://www.acdlabs.com) and [Mestrelab](https://mestrelab.com/) to enable better or up to date export functions, and also to improve the parsers to process the standard output file format with the rich metadata.

For further details on loading a script in Mnova, please check this [link](https://resources.mestrelab.com/how-to-get-started-with-mnova-scripts/)

Example output:
- [Mnova - nmredata](https://drive.google.com/file/d/1a6na29Gc8FHDP38QC19BLsOzWtj5H2sp/view?usp=sharing)
- [ACD/Labs - jdx](https://drive.google.com/drive/folders/1P5NAVrI0tiIShLkhnckr2vHHMxMKnrzt)

#### Software format examples

| Short Name | Maintainer | File Extension |
|------------|------------|----------------|
| ACD/Labs   | ACD/Labs   | .spectrus      |
| Mnova      | Mestrelab  | .mnova         |

### NMR Open Formats

[JCAMP-DX](https://opg.optica.org/as/abstract.cfm?uri=as-47-8-1093) is a widely used **Open** NMR data exchange format. However, due to the broad scope and complexity of this format, along with maintenance issues, alternative approaches with peer-maintained ontologies would be beneficial. In **[nmrXiv](https://nmrxiv.org)**, within NFDI4Chem, we are working on extending the available **Open** NMR formats [NMReData](https://doi.org/10.1002/mrc.4737) and [nmrML](https://doi.org/10.1021/acs.analchem.7b02795) as candidate alternatives.

| Short Name | Maintainer          | File Extension  | Parent Format | Specification |
|------------|---------------------|-----------------|---------------|---------------|
| nmrML      | COSMOS              | .nmrML          | XML           | Open          |
| NMReDATA   | NMReDATA Initiative | .sdf            | SDF           | Open          |
| JCAMP-DX   | IUPAC               | .dx, .jdx, .jcm | ASCII, Text   | Open          | 

#### nmrML
It is an open XML-based exchange and storage format for NMR spectral data. It can capture raw NMR data, spectral data acquisition parameters, and- where available- spectral metadata, such as chemical structures associated with spectral assignments. The nmrML format is compatible with pure-compound NMR data for reference spectral libraries and NMR data from complex bio-mixtures. There already exists nmrML converters for Bruker, JEOL, and Agilent/Varian vendor formats. In addition, easy-to-use Web-based spectral viewing, processing, and spectral assignment tools that read and write nmrML have been developed. Software libraries and Web services for data validation are available for tool developers and end-users. The nmrML format has already been adopted for capturing and disseminating NMR data for small molecules by several open-source data processing tools and metabolomics reference spectral libraries, e.g., serving as a storage format for the [MetaboLights](https://www.ebi.ac.uk/metabolights/) data repository. The nmrML open access data standard has been endorsed by the Metabolomics Standards Initiative (MSI). 

<p align="center">
<img src="/img/nmrml.png" width="1000"/>
<figcaption>Non-exhaustive list of nmrML compatible open source software, clustered by tool category</figcaption>
</p>

#### NMReDATA
NMReData is the only standard file format available for the NMR data relevant to the structural characterization of small molecules as it associates the NMR parameters extracted from 1D and 2D spectra of organic compounds to the proposed chemical structure. These NMR parameters include chemical shift values, signal integrals, intensities, multiplicities, scalar coupling constants, lists of 2D correlations, relaxation times, and diffusion rates. The file format is an extension of the existing Structure Data Format .sdf, which is compatible with the commonly used MOL format. The association of an NMReDATA file with the raw and spectral data from which it originates constitutes an NMR record. 

<p align="center">
<img src="/img/nmredata-w.png" width="1000"/>
<figcaption>NMReData working compatible software and tools</figcaption>
</p>
<p align="center">
<img src="/img/nmredata-a.png" width="1000"/>
<figcaption>NMReData announced compatible software and tools</figcaption>
</p>

#### JCAMP-DX
It is one of the most used open data exchange formats for NMR data, but due to its broad scope and complexity many different vendor-dependent variants exist. Coordinated updating for all variants to reflect the state-of-the-art in NMR methodology is rarely seen in this 30-year-old format. This variability can lead to incompatibilities between different software packages, and as a result, no content-based (semantic) validation of JCAMP-DX is available. However, JCAMP-DX is likely to remain in use for NMR data capture for many years, and therefore it will be supported in **[nmrXiv](https://nmrxiv.org)**.

## NMR File Formats Converters

### [NMRium](https://www.nmrium.org/)
[NMRium](https://www.nmrium.org/) can convert open and vendor file formats to other formats. As an input, it can handle:
- Jcamp DX (.dx, .jdx, .jcamp)
- zipped folder in Bruker format (raw data or processed)
- Jeol (.jdf)
- NMRium file (.nmrium)
- NMReData (.nmredata, coming soon)

And as output, it gives back:
- NMRium file (.nmrium)
- NMReData (.nmredata, coming soon)
- images

### nmrML Converters
There are converters from vendor formats (Bruker, JEOL, and Agilent/Varian) to nmrML along with other converters covered in the following table from [nmrML: A Community Supported Open Data Standard for the Description, Storage, and Exchange of NMR Data](https://pubs.acs.org/doi/10.1021/acs.analchem.7b02795).


| Converter Name           | Key Functions                                      | Developer                                                    |
|--------------------------|----------------------------------------------------|--------------------------------------------------------------|
| nmrML converter (Java)   | Converts vendor to nmrML format (recommended)      | Institut National de la Recherche Agronomique (INRA), France |
| nmrML converter (Python) | Converts vendor to nmrML format                    | The Metabolomics Innovation Center (TMIC), Canada            |
| nmrML to ISA converter   | Generates prepopulated ISA files from nmrML files  | EMBL-EBI, United Kingdom                                     |
| BMSxNmrML                | Converts BMRB metabolomics entries to nmrML format | Institute for Protein Research (IPR), Japan                  |

### NMReData Converters
Conversion to NMReData is already being worked on by the NMRium team. Furthermore, non-open software such as [Mnova, ACD/Labs, and TopSpin](https://analyticalsciencejournals.onlinelibrary.wiley.com/doi/10.1002/mrc.5146) also provide the possibility to export their data as NMReData.

## nmrXiv approach

By now, it is evident that the type of information stored in existing formats is heterogeneous (see below), and no single format is currently capable of fully capturing all information from NMR experiments and data processing. Currently, NMReData (co-developed by nmrXiv team members) is the most promising Open format, as it supports all data types except for raw spectral data (see below). As native instrument formats represent the majority of available data, they will be preserved and made available to users. This approach ensures traceability and secure, long-term access to every detail of measured data as new formats appear or existing ones evolve. At the same time, the availability and interoperability of several formats will facilitate the re-use of data with external tools.

| Data Format    | RAW Spectra | Peaks           | Annotation | Meta data | Acquisition parameters | Spin parameters | Structure |
|----------------|-------------|-----------------|------------|-----------|------------------------|-----------------|-----------|
| Bruker         | I           | I               |            | I         | I                      |                 | I         |
| JEOL           | I           | I               |            | I         | I                      |                 | I         |
| Varian/Agilent | I           | I               |            | I         | I                      |                 | I         |
| Mnova          | I           | I               |            | I         | I                      |                 | I         |
| NMReData       |             | I/O             | I/O        | I/O       | I/O                    | I/O (partially) | I/O       |
| CML Spect      | I/O         | I/O             | I/O        | I/O       | I/O                    |                 |           |
| nmrML          | I/O         | I/O             | I/O        |           |                        |
| Allotrope ADF  | I/O         | I/O             | I/O        |           |                        |
| JCAMP-DX       | I/O         | I/O (sometimes) |            |
| NMR Star       | I/O         | I/O             | I/O        |

I: input
O: output

#### Column definitions

- **RAW Spectra**: The availability of the raw spectral data (the free induction decays (FIDs), which represent the actual (raw) spectroscopic data from the excited nuclear spins in the NMR experiment). Here is an example from an nmrML on how this data looks like:

  ```<fidData byteFormat="Complex128" encodedLength="324160" compressed="true">eJwMl4dfzl8Ux7U3lYZKy0qiomQLPd9zHlmhslMJ2aIoGe29hyhKKURIaWhoPd9zn6zEz46skFKKZMXPX3DPva/PPZ/3u9xlqVhxpdX8Udd0Gp02JIodL2UIN34vE/sdO4la8ZHCQ7lCseEBM/E981xh2fNnbNDxOyCwLRF+XBYhvrlcna1elI65.../jyvhLLF5zln64UoklPU75xwQ3IPL4Dx/Nfkc91ZJlO3dz/ABn2jzA=</fidData>```

- **Peaks**: The list of the peaks (the list of the chemical shifts and other possible attributes such as the peak type, as in doublets or multiplet). Here is an example from an NMReData on how this data looks like (Find more about NMReData [here](https://nmredata.org/wiki/NMReDATA_tag_format#Agregated_data_tags)): 
  - A, 48.301, 1 ;A corresponds to the carbon of CH2\
  - B, 20.322, 2 ;B corresponds to the carbon of CH3\

- **Annotation**: The possibility to annotate the data with more details provided by the users, as one can see in the same example from the NMReData file here,:
  - A, 48.301, 1 ;A corresponds to the carbon of CH2\
  - B, 20.322, 2 ;B corresponds to the carbon of CH3\

  Where, in addition to the peaks and the atoms labels, the user was able to further clarify the chemical groups.

- **Metadata**: The availability of Metadata, e.g., locations of files. Another example from the NMReData file: 

    ```Spectrum_Location=file:./nmr/11/1/pdata/1\```

    ```>  <NMREDATA_VERSION>1.1\```

    ```>  <NMREDATA_LEVEL>0\```

- **Acquisition parameters**: The availability of acquisition parameters related to the NMR assay, which can be usually provided by the instrument:

```bash
<acquisition>
  <acquisition1D>
      <acquisitionParameterSet numberOfScans="160" numberOfSteadyStateScans="0">
          <sampleContainer name="tube" cvRef="NMRCV" accession="NMR:400128"/>
          <sampleAcquisitionTemperature unitName="kelvin" unitCvRef="UO" value="299.15" unitAccession="UO:0000012"/>
          <spinningRate unitName="hertz" unitCvRef="UO" value="0" unitAccession="UO:0000106"/>
          <relaxationDelay unitName="second" unitCvRef="UO" value="22.2737024" unitAccession="UO:0000010"/>
          <pulseSequence/>
          <DirectDimensionParameterSet numberOfDataPoints="65536" decoupled="false">
              <acquisitionNucleus name="1H" cvRef="ChEBI" accession="CHEBI_29236"/>
              <effectiveExcitationField value="34482.7586207" unitName="megaHertz" unitCvRef="UO" unitAccession="UO:0000325"/>                  
              <sweepWidth value="12019.2307692" unitName="hertz" unitCvRef="UO" unitAccession="UO:0000106"/>
              <pulseWidth value="7.25" unitName="microsecond" unitCvRef="UO" unitAccession="UO:0000029"/>
              <irradiationFrequency unitName="hertz" unitCvRef="UO" value="599.8311617" unitAccession="UO:0000106"/>
              <decouplingNucleus name="1H" cvRef="ChEBI" accession="CHEBI_29236"/>
              <samplingStrategy accession="1000349" cvRef="NMRCV" name="uniform sampling"/>
          </DirectDimensionParameterSet>
      </acquisitionParameterSet>
      ....
  </acquisition1D>
</acquisition>
```
- **Structure**: The availability of the structure, where in some files, the mol data can be integrated with the rest of the data, and in others, it can be provided as a separate mol file.

- Additionally to what traditional formats usually provide, NMReData associates the NMR parameters extracted from 1D and 2D spectra of organic compounds to the proposed chemical structure.

For further details, please check [NFDI4Chem Knowledgebase - Data format standard](https://knowledgebase.nfdi4che/knowledge_base/topics/format_standards/)