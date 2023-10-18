# Organism Part
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/organism-part.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies
[The BRENDA Tissue Ontology - BTO](https://www.ebi.ac.uk/ols/ontologies/bto) and [Experimental Factor Ontology - EFO](https://www.ebi.ac.uk/ols/ontologies/efo) are excellent sources for organisms parts. 

## Data Sanitisation and Missing Values
Organisms parts are found only in metabolomics-related repositories, i.e., MTBLS and MW. 


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
    <td>Organism part</td>
    <td>ontology-driven</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null"; or the organism is not found in NCBI taxonomy.</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>dedicated</td>
    <td>SAMPLE_TYPE</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or decoding the JSON file containing the study details has failed due to syntax error there; or the organism was not found in NCBI taxonomy.</td>
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
    <td>["blood serum", "serum", "A2780cisR cell", "muscle", "feces", "Acetonitrile:H2O (1:3)"]</td>
    <td>["blood serum", "urine", etc.]</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["Urine", "urine", "BLOOD", "Serum", "Plasma, Liver"]</td>
    <td>["blood serum", "urine", etc.]</td>
  </tr>
</table>

## Results
Organism parts details are available in metabolomics repositories. The use of different sources of ontologies was easy to see when using terms such as "Blood" vs "blood". Additionally, values other than organism parts were sometimes provided, such as "Acetonitrile:H2O (1:3)". 

The most used part was the blood serum, then come the urine, blood plasma, liver, and others.

<p align="center">
<img src="/img/analysis/part/all.png" width="700"/>
<figcaption>A rough estimate of the percentages of all studies in MTBLS and MW repositories based on the organism part</figcaption>
</p>

Here one can see the number of studies providing the organism part and its value.
<p align="center">
<img src="/img/analysis/part/h.png" width="1000"/>
<figcaption>The number of studies in MTBLS and MW based on the organism part</figcaption>
</p>