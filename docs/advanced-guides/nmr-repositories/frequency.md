# Spectrometer Frequency
[Notebook link](https://github.com/NFDI4Chem/repo-scripts/blob/main/notebooks/frequency.ipynb) where you can find all the graphs.

Data created on 17.10.2022 at 19:32:45

Data updated on 17.10.2022 at 19:32:45

## Support by Ontologies

Frequency-wise, the NMR field seems to be full of terms such as Larmor frequency, instrument frequency, offset frequency, and others. Still, there is rare, if any, mention of them in NMR and Chemistry related ontologies. Being said, repositories still have the option to define this field, where the value of it is simply a float. The main issue here was the absence of an ontology-driven unit of frequency. Although it is somehow safe to assume that the unit is [MHz](https://terminology.nfdi4chem.de/ts/ontologies/uo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FUO_0000325), assumptions aren't ideal. More importantly, providing the unit was often the only possible way to deduct that a certain number corresponds to frequency. 

When talking about "Spectrometer Frequency" here, we mean during the NMR assay, not the instrument maximum frequency, except for Metabolights, as this is the value provided there.

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
    <td>absent</td>
    <td>none</td>
    <td>none</td>
    <td>none</td>
    <td>Slugified MHz was not found in the instrument name</td>
    <td>There is no field for the frequency. It can be available in the description, but still, it is difficult to extract due to possible confusion with the instrument's maximum frequency value. The pulse sequence name is provided, but it doesn't contain numerical values. Here, we only provide the instrument's maximum frequency value, not what was used in the study</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>dedicated</td>
    <td>Spectrometer Frequency</td>
    <td>machine-readable</td>
    <td>integrated</td>
    <td>The field is not provided; or multiple values were provided; or the value is provided as N/A or other similar expressions.</td>
    <td></td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>semi-dedicated</td>
    <td>Name</td>
    <td>machine-readable</td>
    <td>integrated</td>
    <td>The name is always provided, so "missing" means that a number plus slugified "MHz" were not found there.</td>
    <td></td>
  </tr>
  <tr>
    <td><b>NMRShiftDB</b></td>
    <td>dedicated</td>
    <td>cml:field</td>
    <td>float</td>
    <td>separate field</td>
    <td>The value is given as "Unreported" or other similar expressions</td>
    <td></td>
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
    <td></td>
    <td>n-n+50MHz e.g., 100-150MHz</td>
  </tr>
  <tr>
    <td><b>MW</b></td>
    <td>["500 MHz", "700.13 MHz", "600 MHz | 600 MHz | 600 MHz | 600 MHz | 601 MHz | 602 MHz | 603 MHz | 604 MHz NMR Probe | TCI 600 H-C/N-D | TCI 600 H-C/N-D | TCI 600 H-C/N-D | TCI 600 H-C/N-D | TCI 600 H-C/N-D | TCI 600 H-C/N-D | TCI 600 H-C/N-D | TCI 600 H-C/N-D", "600 MHz for 1H", "1H Larmor frequency"]</td>
    <td>n-n+50MHz e.g., 100-150MHz</td>
  </tr>
  <tr>
    <td><b>CENAPT</b></td>
    <td>["Gossypol 600 MHz CDCl3 DMSOd6 NMR data", "Ginsenoside Rb1 400/600 MHz in DMSOd6 NMR data", "Glycyrrhetinic acid/Enoxolone 900_400MHz DMSOd6 NMR data"]</td>
    <td>n-n+50MHz e.g., 100-150MHz</td>
  </tr>
  <tr>
    <td><b>NMRShiftDB</b></td>
    <td>["100 Mhz", "100.03250122070312", "100.0", "100", "Unknown", "Unreported", "Not clear in reference.", "Not mentioned."]</td>
    <td>n-n+50MHz e.g., 100-150MHz</td>
  </tr>
</table>

## Results
The four repositories provided frequency values in one way or another. However, Metabolights only provided the instrument's maximum frequency value. Only NMRShiftDB provided a field for the unit. Including the large number of studies in NMRShiftDB, the most used spectrometer frequency was 100-150MHz.

<p align="center">
<img src="/img/analysis/freq/all.png" width="600"/>
<figcaption>The percentages of all studies in three repositories (without MTBLS) based on the Spectrometer frequency</figcaption>
</p>

However, when NMRShiftDB is excluded, the most used spectrometer frequency is 600-650MHz.

<p align="center">
<img src="/img/analysis/freq/h.png" width="1000"/>
<figcaption>The number of studies in MW and CENAPT based on Spectrometer frequency</figcaption>
</p>

Looking into the instruments in Metabolights, the most used maximum frequency was also 600-650MHz.

<p align="center">
<img src="/img/analysis/freq/mtbls.png" width="1000"/>
<figcaption>The number of studies in MTBLS based on Maximum spectrometer frequency</figcaption>
</p>


And here you can find the whole view of the three repositories with a logarithmic scale.

<p align="center">
<img src="/img/analysis/freq/log.png" width="1000"/>
<figcaption>The number of studies in the three repositories based on Spectrometer frequency (with a logarithmic scale)</figcaption>
</p>