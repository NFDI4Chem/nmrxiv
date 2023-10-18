# Variant
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/variant.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies
[The BRENDA Tissue Ontology - BTO](https://www.ebi.ac.uk/ols/ontologies/bto) and [Experimental Factor Ontology - EFO](https://www.ebi.ac.uk/ols/ontologies/efo) are good sources for variants. 

Variants are found only in metabolomics-related repositories, i.e., MTBLS and MW. 

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
    <td>Variant</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null"; or the organism is not found in NCBI taxonomy.</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>dedicated</td>
    <td>GENOTYPE_STRAIN</td>
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
    <td>["Mus musculus str. SAMP1/YitFc", "BY4741", "Thoroughbred", "EFO:Thalassiosira pseudonana CCMP1335"]</td>
    <td>["c57Bl-6", "c3h-hen", etc.]</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["C57BL/6", "Swiss Webster Mice", "C3H/HeN"]</td>
    <td>["c57Bl-6", "c3h-hen", etc.]</td>
  </tr>
</table>

## Results
Variants details are available in metabolomics repositories. The most used variant was "C57BL/6J". 

<p align="center">
<img src="/img/analysis/var/all.png" width="700"/>
<figcaption>A rough estimate of the percentages of all studies in MTBLS and MW repositories based on the variant</figcaption>
</p>

Here one can see the number of studies providing the variant and its value.
<p align="center">
<img src="/img/analysis/var/h.png" width="1000"/>
<figcaption>The number of studies in MTBLS and MW based on the variant</figcaption>
</p>