# Exemplary Data
In this section, you can find links and explanations about exemplary data ready for submission to **[nmrXiv](https://nmrxiv.org)**.

## Bruker Datasets
[Following this link](https://cloud.uni-jena.de/s/W3JHNPDY5isXo5F), you can download a project with two samples analyzed with Bruker instrument. After unzipping, the project "Bruker Example" is ready for submission.
You can find the needed hierarchy for submission there : 
- The main folder "Bruker Example" corresponds to the project. This is the folder that should be submitted (dragged and dropped) to nmrXiv.
  - Within "Bruker Example" you find two folders, each corresponding to a sample (Eserine and Cucurbitacin E). 
    - Within each sample folder, you find separate **folders** each corresponding to the spectrum/dataset folder. at the same level (within the sample folder), you can find additional files representing the whole sample, such as mol files for the structure or NMReData files and such.

<p align="center">
<img src="/img/examples/Bruker.png" width="350"/>
<figcaption>The hierarchy needed for a Bruker samples project</figcaption>
</p>

### Wrong Bruker Hierarchy

::: danger Wrong Hierarchy
It is common in Bruker datasets to find, for instance, the 2D datasets provided in a parent folder with the experiment name (e.g., HSQC), and within this folder you find the traditional Bruker named folders such as 1,2,3, etc. Such hierarchy will result in considering the HSQC dataset as a separate sample by nmrXiv, thus you need to be careful and avoid such inconveniences
:::

In the following photo, because there is an "HSQC" folder above the actual spectrum folder "58", nmrXiv recognizes 3 samples (Eserine, Cucurbitacin E, and HSQC)

<p align="center">
<img src="/img/examples/Bruker-wrong.png" width="350"/>
<figcaption>Adding a parent "HSQC folder to dataset 58 makes that folder a new sample to nmrXiv system."</figcaption>
</p>

<p align="center">
<img src="/img/examples/Bruker-studies.png" width="700"/>
<figcaption>The samples in nmrXiv resulting from submitting the project in the above image.</figcaption>
</p>

## JCAMP Datasets

[Following this link](https://cloud.uni-jena.de/s/CrNXN46N9GBQ7cG), you can download a project with two analyzed samples. The analysis output is provided in JCAMP format.  After unzipping, the project "JCAMP Example" is ready for submission.
You can find the needed hierarchy for submission there : 
- The main folder "JCAMP Example" corresponds to the project. This is the folder that should be submitted (dragged and dropped) to nmrXiv.
  - Within "JCAMP Example" you find two folders, each corresponding to a sample (196793-29-0 and 1155308-25-0). 
    - Within each sample folder, you find separate **JCAMP files** each corresponding to the spectrum/dataset file. At the same level (within the sample folder), you can find additional files corresponding to the whole sample, such as mol files for the structure or NMReData files and such.

<p align="center">
<img src="/img/examples/JCAMP.png" width="350"/>
<figcaption>The hierarchy needed for a Bruker samples project</figcaption>
</p>

### Wrong JCAMP Hierarchy

:::danger Wrong Hierarchy
It is common in JCAMP datasets to find, for instance, the 2D datasets provided in a parent folder with the experiment name (e.g., HSQC), and within this folder, you find the jcamp file such as hsqc.dx. Such hierarchy will result in considering the HSQC dataset as a separate sample by nmrXiv, thus you need to be careful and avoid such inconveniences
:::

In the following photo, because there is an "HSQC" folder above the actual spectrum file "hsqc.dx", nmrXiv recognizes 3 samples (196793-29-0, 1155308-25-0, and HSQC)

<p align="center">
<img src="/img/examples/JCAMP-wrong.png" width="350"/>
<figcaption>Adding a parent "hsqc folder to dataset hsqc.dx makes that folder a new sample to nmrXiv system."</figcaption>
</p>

<p align="center">
<img src="/img/examples/JCAMP-studies.png" width="700"/>
<figcaption>The samples in nmrXiv resulting from submitting the project in the above image.</figcaption>
</p>