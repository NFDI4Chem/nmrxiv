# Dimensionality
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/dimensionality.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies

Despite the difficulty of finding definitions of NMR Dimensionality in ontologies, the term itself exists ( in [nuclear magnetic resonance CV](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv/terms?iri=http%3A%2F%2FnmrML.org%2FnmrCV%23NMR%3A1000117&viewMode=All&siblings=false), and in [Physico-chemical methods and properties](https://terminology.nfdi4chem.de/ts/ontologies/fix/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FFIX_0000140&viewMode=All&siblings=false)). However, its subclasses are not rich with either definitions or values, with the values mostly being missing in the case of 1D NMR, or provided ([as in CHMO](https://terminology.nfdi4chem.de/ts/ontologies/chmo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FCHMO_0000613&viewMode=All&siblings=false)), but not classified as 1D. Still, the available ontologies are sufficient for providing the piece of metadata describing the dimensionality as the subclasses can be added in another field in the repository. 

## Data Sanitisation and Missing Values

<table>
  <tr>
    <th></th>
    <th>Field Type</th>
    <th>Field Name</th>
    <th>Values Readability</th>
    <th>Unit</th>
    <th>Missing</th>
    <th>Comment</th>
  </tr>
  <tr>
    <td><b>MTBLS</b></td>
    <td>semi-dedicated</td>
    <td>Pulse sequence name</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the expressions "1D" or "2D" are not found in the field, and also the method mentioned is not available in the mapping between the method and dimensionality made with hard-coding; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null"</td>
    <td>Due to providing methods names in free text, much hard coding was needed</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>semi-dedicated</td>
    <td>NMR Eexperiment Type</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the expressions "1D" and "2D" are not found in the field, and also the method mentioned is not available in the mapping between the method and dimensionality made with hard-coding; or the value is provided as N/A or other similar expressions; or decoding the JSON file that contains the study details has failed due to syntax error there</td>
    <td>Due to providing methods names in free text, much hard coding was needed</td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>semi-dedicated</td>
    <td>Description</td>
    <td>machine-readable</td>
    <td>none</td>
    <td>Description field is always available, so missing means only that the expressions "1D" and "2D" are not found there</td>
    <td>It was machine-readable as looking for "1D" and "2D" expressions was enough in all the texts</td>
  </tr>
  <tr>
    <td><b>NMRShiftDB</b></td>
    <td>semi-dedicated</td>
    <td>[nmr:assignmentMethod, nmr:OBSERVENUCLEUS]</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the expressions "1D" and "2D" are not found in the field, and also the method mentioned is not available in the mapping between the method and dimensionality made with hard-coding. The value can also be given as "Unreported" or other similar expressions.</td>
    <td>Due to providing methods names in free text, much hard coding was needed</td>
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
    <td>["1D NOESY with presaturation (tnnoesy)", "2D JRES with homonuclear J-resolved 2D correlation, presaturation during relaxation delay with gradients (jresgpprqf)", "1H_PROTON"]</td>
    <td>"1d", "2d", "missing"</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["2D 1H-13C HSQC-TOCSY", "NOESY", "NOESY PR1D"]</td>
    <td>"1d", "2d", "missing"</td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>["NMR data of gossypol in DMSOd6 and CDCl3. The dataset contains 1D 1H 13C as well as 2D COSY, HSQC, HMBC, all acquired at 600MHz (Bruker 600MHz spectrometer with CryoProbe (CP DCH 600S3 C/H-D-05 Z)"]</td>
    <td>"1d", "2d", "missing"</td>
  </tr>
  <tr>
    <td><b>NMRShiftDB</b></td>
    <td>["1D shift positions", "2D INADEQUATE/NMRanalyst", "1H, H,H-COSY, H,H-NOESY, H,C-HMQC, H,C-HMBC, DEPTQ, 1H, 13C, 1H, H,H-COSY"]</td>
    <td>"1d", "2d", "missing"</td>
  </tr>
</table>

## Results
The Dimensionality was possible to obtain in most studies and repositories. It can be either mentioned explicitly as "1D" or "2D", or it can be implied by the technique name (e.g., HSQC). None of the repositories used ontologies to describe the value.

Most of the studies were only one-dimensional. Here you can see the percentage of studies based on their dimensionalities. If a study has both 1D and 2D spectra, it will appear twice.

<p align="center">
<img src="/img/analysis/dim/all.png" width="500"/>
<figcaption>The percentages of all studies in the four repositories based on the Dimensionality</figcaption>
</p>

[CENAPT](https://dataverse.harvard.edu/dataverse/cenapt) was the only exception to provide 2D NMR for almost all the studies as one can see below (NMRShiftDB data were not added due to the huge number of studies there).

<p align="center">
<img src="/img/analysis/dim/h.png" width="1000"/>
<figcaption>The number of studies in three repositories based on NMR dimensionalities</figcaption>
</p>

However, NMRShiftDB similarly has mostly 1D NMR data.
<p align="center">
<img src="/img/analysis/dim/shift.png" width="1000"/>
<figcaption>The number of studies in NMRShiftDB based on NMR dimensionalities</figcaption>
</p>

And here you can find the whole view of the four repositories with a logarithmic scale.

<p align="center">
<img src="/img/analysis/dim/log.png" width="1000"/>
<figcaption>The number of studies in the four repositories based on NMR dimensionalities (with a logarithmic scale)</figcaption>
</p>