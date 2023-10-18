# Temperature
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/temperature.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies
While searching in NMR-related ontologies, finding terms about the temperature through an NMR assay wasn't possible. When saying "Temperature" here, we mean the temperature at which the NMR spectroscopy was conducted.  Unit-wise, one can easily use [ontology-driven units](https://terminology.nfdi4chem.de/ts/ontologies/uo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FUO_0000005&viewMode=All&siblings=false).

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
    <td>dedicated</td>
    <td>Temperature</td>
    <td>float</td>
    <td>separate field</td>
    <td>The field is not provided; or the unit was not found in the list of units we provided; or the value is provided as N/A or other similar expressions; or the study "assays" value is "null".</td>
    <td>The provided list of units is ['UO:kelvin', 'UO:kelvin:K','Kelvin','kelvin', 'degree Celsius','celsius', 'degree celsius']</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>dedicated</td>
    <td>Temperature</td>
    <td>machine-readable</td>
    <td>integrated</td>
    <td>The field is not provided; or an expression of numbers was not found in the field; or the value is provided as N/A or other similar expressions; or the unit (words with c or d for Celsius and k for Kelvin) was not found; or decoding the JSON file that contains the study details has failed due to syntax error there.</td>
    <td></td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>none</td>
    <td>none</td>
    <td>none</td>
    <td>none</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td><b>NMRShiftDB</b></td>
    <td>dedicated</td>
    <td>cml:temp</td>
    <td>float</td>
    <td>separate field</td>
    <td>none</td>
    <td>The unit is ontology-driven as it is always Kelvin</td>
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
    <td>["288.1"]</td>
    <td>"288", "293"</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["4C", "4 oC",  "281 ± 0.5 K", "15 celsius", "23d", "22 degree Celsius", "27", "5mm", "295.9", "36,85"]</td>
    <td>"288", "293"</td>
  </tr>
  <tr>
    <td><b>NMRSiftDB</b></td>
    <td>["243","300.0136", "Unreported", "Unknown"]</td>
    <td>"288", "293"</td>
  </tr>
</table>

## Results
The temperature was easy to obtain in all the three repositories that provide it. However, the inconsistency encountered in the values and units made it time-consuming to harmonize the data.  In the end, having units coming (or possibly to be mapped) from ontologies made most of the data clear and machine-readable. The temperature provided in Celsius was converted to Kelvin, and with visualization, one can see that most of the assays were held around room temperature.

<p align="center">
<img src="/img/analysis/tmp/all.png" width="500"/>
<figcaption>The percentages of all studies in the three repositories based on the Temperature</figcaption>
</p>

Still, some of the values were reported wrongly in Kelvin, as values like 0 and 37 can be seen in the graph showing the number of studies for each temperature (with a logarithmic scale).

<p align="center">
<img src="/img/analysis/tmp/log.png" width="1000"/>
<figcaption>The number of studies in the three repositories based on the Temperature (with a logarithmic scale)</figcaption>
</p>
