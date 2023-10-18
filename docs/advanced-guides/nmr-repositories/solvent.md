# Solvent
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/solvent.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies
The concept of [NMR solvent](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv/terms?iri=http%3A%2F%2FnmrML.org%2FnmrCV%23NMR%3A1000330) already exists in [nmr CV](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv), unfortunately, without a definition. The same happens with [NMR buffer](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv/terms?iri=http%3A%2F%2FnmrML.org%2FnmrCV%23NMR%3A1000331). Despite that, the values for [solvents, buffers,](https://terminology.nfdi4chem.de/ts/ontologies/chebi) and their [concentrations units](https://terminology.nfdi4chem.de/ts/ontologies/uo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FUO_0000051&viewMode=All&siblings=false) can be ideally provided with ontology terms.

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
    <td>dedicated</td>
    <td>Solvent</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null".</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>dedicated</td>
    <td>NMR Solvent</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or decoding the JSON file containing the study details has failed due to syntax error there.</td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>semi-dedicated</td>
    <td>Description</td>
    <td>free text</td>
    <td>none</td>
    <td>'in ' was not found in the description, or it was not relevant to the solvent (hard-coding).</td>
  </tr>
  <tr>
    <td><b>NMDShiftDB</b></td>
    <td>dedicated</td>
    <td>cml:solvent</td>
    <td>free text</td>
    <td>none</td>
    <td>The value is 'unreported' or 'unknown'</td>
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
    <td>["0.01 M phosphate buffered D2O", "0.154 M saline D20", "0.6 ml of 0.1M phosphate buffered D2O (pH=7.0) solution containing 0.5 mM 3-trimethylsilyl-propionate-2, 2, 3, 3, -d4 (TMSP, δ =0.0 ppm) ", "CD3OD", "H20 + D20", "water"]</td>
    <td>["0-01-m-phosphate-buffered-d2o", etc.]</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["10%D20", "100 mM phosphate buffer at pH 7.4 (prepared in D2O) containing 0.1 mM 3-(trimethylsilyl)-propionic-2,2,3,3-d4 acid", "90:10 H2O/D2O (99.96% atom D2O; Cambridge Isotope Labs) with 0.2 mM phosphate buffer (pH 7.4) and 0.25 mM 3-(trimethylsilyl)propionic-2,2,3,3d4-acid", "CDCl3", "D2O and MeOD"]</td>
    <td>["0-01-m-phosphate-buffered-d2o", etc.]</td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>["NMR data of gossypol in DMSOd6 and CDCl3. The dataset contains 1D 1H 13C as well as 2D COSY, HSQC, HMBC, all acquired at 600MHz (Bruker 600MHz spectrometer with CryoProbe (CP DCH 600S3 C/H-D-05 Z)"]</td>
    <td>["0-01-m-phosphate-buffered-d2o", etc.]</td>
  </tr>
  <tr>
    <td><b>NMRSiftDB</b></td>
    <td>["acetone", "Acetone-D6 ((CD3)2CO)", "acetonitril", "chloroform", "Chloroform-D1 (CDCl3)", "water", "Water (D2O)"]</td>
    <td>"288", "293"</td>
  </tr>
</table>

## Results
The solvent was possible to obtain in most studies and repositories, but it was rarely machine-readable, with a high level of inconsistencies. The following graph can give a rough estimate of popular solvents. However, it is far from accurate when it comes to exact numbers, as one solvent can be mentioned tens of times with different expressions. 

<p align="center">
<img src="/img/analysis/slv/all.png" width="750"/>
<figcaption>A rough estimate of the percentages of all studies in the four repositories based on the NMR solvent (only solvent used in more than 50 studies are shown)</figcaption>
</p>

Here you can find the whole view of the four repositories with a logarithmic scale where you can easily notice how some solvents get repeated with different expressions.

<p align="center">
<img src="/img/analysis/slv/log.png" width="1000"/>
<figcaption>The number of studies in the four repositories based on NMR solvent (with a logarithmic scale)</figcaption>
</p>