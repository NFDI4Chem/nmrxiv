# Atomic Nuclei
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/nuclei.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies
The term [acquisition nucleus](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv/terms?iri=http%3A%2F%2FnmrML.org%2FnmrCV%23NMR%3A1400083) is already provided in [nmr CV](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv). However, 2D NMR isotope-related terms are largely missing. The possible values such as "1H" are very well annotated, either simply as atoms ([in CHEBI](https://terminology.nfdi4chem.de/ts/ontologies/chebi/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FCHEBI_33250&viewMode=All&siblings=false)), or encoded in the name of the NMR method ([in CHMO](https://terminology.nfdi4chem.de/ts/ontologies/chmo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FCHMO_0000613&viewMode=All&siblings=false) despite the lack of consistency with dimensionality there). Currently, available ontologies are adequate to cover the atomic nuclei values in most cases.

## Data Sanitisation and Missing Values

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
    <td>semi-dedicated</td>
    <td>Pulse sequence name</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the regular expressions '\d+[A-CE-Z]\-\d+[A-CE-Z]' or '\d+[A-CE-Z]\-\d+[A-CE-Z]' are not found in the field; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null".</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>semi-dedicated</td>
    <td>NMR Experiment Type</td>
    <td>machine-readable</td>
    <td>none</td>
    <td>The field is not provided; or an expression of numbers followed directly with letters (but not 1D or 2D) is not found in the field; or the value is provided as N/A or other similar expressions; or decoding the JSON file that contains the study details has failed due to syntax error there.</td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>semi-dedicated</td>
    <td>Description</td>
    <td>free text</td>
    <td>none</td>
    <td>"1h" and "13c" were not found in the description, and checking for the regular expressions '\d+[a-zA-Z]+' didn't match any isotope.</td>
  </tr>
  <tr>
    <td><b>NMRShiftDB</b></td>
    <td>dedicated</td>
    <td>nmr:OBSERVENUCLEUS</td>
    <td>machine-readable</td>
    <td>none</td>
    <td>none</td>
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
    <td>["2D 1H-13C HSQC (hsqcetgppr)", "Conventional 1H spectrum (zg30)", "1D NOESY 12C custom filtered with presaturation, phase-sensitive gradients, and 13C decoupling (c12filternoesygpc13cpd)", "13C_CARBON", "M3S COSY", "2D homonuclear correlation via dipolar coupling (noesyesgpph)"]</td>
    <td>"1h", "1h-13c", "h-h", "missing"</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["1D 1H", "1D-1H", "1D1H", "2D 1H-13C HSQC-TOCSY", "2D-INADEQUATE", "NOESY PR1D"]</td>
    <td>"1h", "1h-13c", "h-h", "missing"</td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>["NMR data of gossypol in DMSOd6 and CDCl3. The dataset contains 1D 1H 13C as well as 2D COSY, HSQC, HMBC, all acquired at 600MHz (Bruker 600MHz spectrometer with CryoProbe (CP DCH 600S3 C/H-D-05 Z)"]</td>
    <td>"1h", "1h-13c", "h-h", "missing"</td>
  </tr>
  <tr>
    <td><b>NMRShiftDB</b></td>
    <td>["1H", "13C", "H,H-COSY"]</td>
    <td>"1h", "1h-13c", "h-h", "missing"</td>
  </tr>
</table>

## Results
Taking the large number of studies from NMRShiftDB into account or not, the most reported atomic nuclei are "1H" and "13C". But NMRSiftDB adds more 1D nuclei to the scene, such as "19F" and "11B". 

<p align="center">
<img src="/img/analysis/ncl/all.png" width="500"/>
<figcaption>The percentages of all studies in the four repositories based on the acquisition nucleus (only nuclei appearing in more than 200 studies can be seen here.)</figcaption>
</p>

Excluding NMRShiftDB, we see that the other repositories have only 1D and 2D NMR based on proton and carbon.

<p align="center">
<img src="/img/analysis/ncl/h.png" width="1000"/>
<figcaption>The number of studies in three repositories based on the acquisition nucleus</figcaption>
</p>

Here one can see the variation in nuclei reported in NMRShiftDB.
<p align="center">
<img src="/img/analysis/ncl/shift.png" width="1000"/>
<figcaption>The number of studies in NMRShiftDB based on the acquisition nucleus</figcaption>
</p>

And here you can find the whole view of the four repositories with a logarithmic scale.

<p align="center">
<img src="/img/analysis/ncl/log.png" width="1000"/>
<figcaption>The number of studies in the four repositories based on the acquisition nucleus (with a logarithmic scale)</figcaption>
</p>