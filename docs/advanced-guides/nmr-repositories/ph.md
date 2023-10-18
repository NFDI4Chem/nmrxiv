# Sample pH
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/ph.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies
The [definition of the pH](https://terminology.nfdi4chem.de/ts/search?q=ph) is easily [found in ontologies](https://terminology.nfdi4chem.de/ts/search?q=ph), the definition of [Sample pH](https://terminology.nfdi4chem.de/ts/ontologies/nmrcv/terms?iri=http%3A%2F%2FnmrML.org%2FnmrCV%23NMR%3A1000019) too. However, when users provide this value, it is not clear whether it really means the pH of the sample, the solvent, the solvent with the buffer, or another value. Due to the possible confusion, users tend to provide a detailed explanation within the solvent field as a free text, which unfortunately affects the machine-readability of both the solvent and the pH. You can find examples in the [solvent](/advanced-guides/nmr-repositories/solvent.md) page.

## Data Sanitisation and Missing Values
It was possible to get the pH details only from [MetaboLights](https://www.ebi.ac.uk/metabolights/).

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
    <td>Sample pH</td>
    <td>free text</td>
    <td>none</td>
    <td>The field is not provided; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null"; or the value cannot be converted into a float.</td>
  </tr>
</table>

## Results

It was possible to obtain the pH only from MTBLS, even though most of the studies didn't provide this metadata. Most samples' pHs were leaning towards the neutral 7, while a small percentage was around 4.

<p align="center">
<img src="/img/analysis/ph/all.png" width="500"/>
<figcaption>A rough estimate of the percentages of all studies in MTBLS repository based on the sample pH</figcaption>
</p>

Here one can see the number of studies providing the pH of the sample and its value.
<p align="center">
<img src="/img/analysis/ph/h.png" width="1000"/>
<figcaption>The number of studies in MTBLS based on the sample pH</figcaption>
</p>