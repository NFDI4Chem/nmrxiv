## Purpose
This directory has python files to help:
- Downloading NMRShiftDB database as a collection of NMReData files.
- Downloading the experimental data from the links in the NMReData.
- Unzipping and structuring the files and folders in a compatible manner with nmrXiv submission requirements.

## Running
After navigating to the current folder, "nmrshiftdb", run: python3 nmrshiftdb-download.py

## Structure
The folder contains two python files:
- helpers.py has the functions.
- nmrshiftdb-download.py is the script that we run.
- output folder generated after running the script, containing the NMReData, along with folders organized and named after the unique combinations of authors found in NMRShiftDB. Within each folder one can find all the molecules credited to those authors structured as folders containing the Bruker datasets folder and the corresponding NMReData files. Datasets are named after the original instrument name. 

## Findings: 
- There are 36 unique combinations of authors, which corresponds to 36 nmrXiv projects.
- There are 102 molecules corresponding to nmrXiv studies.
  - 1D + 2D : 89
  - 2D : 3
  - 1D : 10
- There are 562 spectra corresponding to nmrXiv datasets.
- All temperatures are in Kelvin.


## Projects on nmrXiv:
All projects have the suffix "NMRShiftDB datasets" to facilitate searching for them. "NMRShiftDB" will also be provided in the keywords.
Projects are named after the corresponding article when possible. Molecules with no citations are grouped together unuder one project.
Studies are named after the molecules, when the name is not available, InChI was used instead.
Datasets are named after the original instrument name.

