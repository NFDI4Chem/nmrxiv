# Instruments Names
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/instruments-names.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies

[NMR instrument](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv/terms?iri=http%3A%2F%2FnmrML.org%2FnmrCV%23NMR%3A1400059)s are already provided in [nmr CV](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv), along with [instruments parts](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv/terms?iri=http%3A%2F%2FnmrML.org%2FnmrCV%23NMR%3A1000463&viewMode=All&siblings=false).

## Data Sanitisation and Missing Values
Instrument names are found only in MTBLS and MW.

<table>
  <tr>
    <th></th>
    <th>Field Type</th>
    <th>Field Name</th>
    <th>Values Readability</th>
    <th>Unit</th>
    <th>Missing</th>
  </tr>
  <tr>
    <td><b>MTBLS</b></td>
    <td>dedicated</td>
    <td>Instrument</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null".</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>dedicated</td>
    <td>Instrument</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or decoding the JSON file containing the study details has failed due to syntax error there.</td>
  </tr>
</table>

<table>
  <tr>
    <th></th>
    <th>Input Examples</th>
    <th>Output</th>
  </tr>
  <tr>
    <td><b>MTBLS</b></td>
    <td>["Bruker AVANCE 700 MHz spectrometer", "Bruker AVANCE II 700 MHz spectrometer", "Bruker AVANCE III 700 MHz spectrometer", "Bruker AVANCE III HD 700 MHz spectrometer"]</td>
    <td>["600-mhz-varian-inova-spectrometer", etc.]</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["Bruker 18.8 Tesla (800 MHz) NMR spectrometer ascend", "Bruker 500MHz spectrometer", "Bruker 600 MHz", "Bruker 600 MHz Avance III HD spectrometer", "Bruker 600-MHz AVANCE III solution NMR spectrometer", "Bruker 600MHZ", "FT NMR", "INOVA"]</td>
    <td>["600-mhz-varian-inova-spectrometer", etc.]</td>
  </tr>
</table>

## Results

Both repositories have a dedicated field for the instrument name, making it easily obtainable. However, none used ontology terms. As a result, one can see in the graph of the percentages of studies using the same instruments how small and plenty the sections are due to variations in the names used to indicate the same instrument. One can easily tell that Bruker and Agilent are the most used instruments, but getting exact numbers on how much a specific instrument is used was quite difficult.

<p align="center">
<img src="/img/analysis/inst/all.png" width="700"/>
<figcaption>A rough estimate of the percentages of all studies in the two repositories based on the NMR instrument</figcaption>
</p>


Looking at the distribution of instrument names in the two repositories, one can see that the same value (except for once) was never used in both, although the same instrument actually was. 

<p align="center">
<img src="/img/analysis/inst/h.png" width="1000"/>
<figcaption>The number of studies in MW and MTBLS based on the instrument name.</figcaption>
</p>
