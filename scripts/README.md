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
- There are 33 unique combinations of authors, which corresponds to 33 nmrXiv projects.
- There are 84 molecules corresponding to nmrXiv studies.
- There are 534 spectra corresponding to nmrXiv datasets.
- All molecules except ethyl (E)-but-2-enoate from Nils Schlörer.
- All temperatures are in Kelvin.

## Projects on nmrXiv:
All projects will have the prefix "NMRShiftDB" to facilitate searching for them. "NMRShiftDB" will also be provided in the keywords.
Projects are going to be named after the first and last author last names, e.g., : "NMRShiftDB-Jones-Williams".
Studies are named after the molecules, when the name is not available, InChI was used instead.
Datasets are named after the original instrument name.

[In this document]([url](https://docs.google.com/document/d/1xtgL0ha8BpF1GDNsgbI35EVuECZlusqrP0d-Z6sYL50/edit)), you can find the links to all the NMRShiftDB projects on nmrXiv (still not published).
