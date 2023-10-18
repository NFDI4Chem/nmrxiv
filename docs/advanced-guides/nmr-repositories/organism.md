# Organism
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/organism.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies
[NCBI Taxonomy](https://www.ncbi.nlm.nih.gov/taxonomy) provides an excellent source for organisms. 

## Data Sanitisation and Missing Values
Organisms are found only in metabolomics-related repositories, i.e., MTBLS and MW. 

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
    <td>Organism</td>
    <td>ontology-driven</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null"; or the organism is not found in NCBI taxonomy.</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>dedicated</td>
    <td>Subject Species</td>
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
    <td>["Homo sapiens", "Blank sample", "Lactobacillus sp. asf360;Parabacteroides sp. asf519",   "Sus scrofa domesticus", "NCBITAXON:Thalassiosira pseudonana;NCBITAXON:Ruegeria pomeroyi"]</td>
    <td>["homo-sapiens", "mus-musculus", etc.]</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["Homo sapiens", "Sus scrofa", "Sus Scrofa", "C57BL/6J Mouse",  "Multi-species non-defined biofilm consortium", "Alexandrium catenella; Alexandrium tamarense"]</td>
    <td>["homo-sapiens", "mus-musculus", etc.]</td>
  </tr>
</table>

## Results
Organism details are available in metabolomics repositories. The users usually use the scientific name that can be obtained from [NCBI Taxonomy](https://www.ncbi.nlm.nih.gov/taxonomy). However, some inconsistencies were encountered such as with writing the scientific name (e.g.,  Mus Musculus instead of Mus musculus), providing a common name (Goat instead of Capra hircus), or providing the source along with the value (e.g., NCBITAXON:Homo sapiens), or even typos.

Some values were ambiguous such as "Various", "Extract", "Multi-species non-defined biofilm consortium" or not even species, such as NMR buffer. Additionally, sometimes, more than one species was mentioned. The combination of species is usually standardized by putting ";" or "/" between the names, but still, the relation between the species is not clear (samples from multiple species vs one sample from a species tissue infected by another species). Lastly, the organism provided varys in rank. Mostly the species is provided, but sometimes it is the genus or strain.  

However, even after taking all that was mentioned above, it is still clear that the most studied species are humans (Homo sapiens) and mice (Mus musculus)
<p align="center">
<img src="/img/analysis/org/all.png" width="700"/>
<figcaption>A rough estimate of the percentages of all studies in MTBLS and MW repositories based on the organism</figcaption>
</p>

Here one can see the number of studies providing the organism and its value.
<p align="center">
<img src="/img/analysis/org/h.png" width="1000"/>
<figcaption>The number of studies in MTBLS and MW based on the organism</figcaption>
</p>