# Folder Structure Before Submission

Folders structuring means how data should ideally be structured in folders before submission. Even that **[nmrXiv](https://nmrxiv.org/)** has the capability to detect all the formats supported by NMRium in the submitted data, which enables it to structure the data into datasets/studies/projects, there is still no way to guarantee that the automatically generated structure is what the user was hoping to get. Therefore, it is recommended to have a look at this section before submitting data.

## nmrXiv Automated Structuring

**[nmrXiv](https://nmrxiv.org/)** can detect the formats supported by [NMRium](https://www.nmrium.org/). In some cases, as with Bruker files, spectrum gets generated using a folder of files. Whatever generates spectra (one .jdx file, one .jdf file, or one entire Bruker folder) is considered a dataset. The direct parent folder to the dataset (or multiple datasets) is the study. It can be noticed during the submission pipeline as a folder marked with a blue dot.

![Study Blue Dot](/img/study/dot.png)

In the above image, Bruker folder (named Bruker dataset) was detected by **[nmrXiv](https://nmrxiv.org/)**, hence the blue dot on the parent folder Bruker study. The group of JCAMP files were detected too, and the parent folder received a blue dot.

## Sources of Misleading Structuring

Considering that **[nmrXiv](https://nmrxiv.org/)** has specific definitions of what projects, studies, and datasets are, wrong structuring will lead to the wrong interpretation of the data (especially when read by machines). Study in **[nmrXiv](https://nmrxiv.org/)** is not only the folder containing datasets, but it is basically the folder containing all the datasets **of one sample**. Therefore, separating two datasets belonging to one sample into two different folders will lead to having two studies instead of the intended one study. To make this clearer, please check the following image:

![Wrong Study Structure](/img/wrong-structure.png)

In this case, the user has a 13 Carbon dataset that belongs with the other JCAMP files in the **JCAMP study** folder. As the user had separated this file (which corresponds to one dataset), **[nmrXiv](https://nmrxiv.org/)** recognized its parent folder as a new study (note the blue dot), resulting in two studies for one sample instead of one.

## Ideal Folders Structuring

What is considered a dataset in **[nmrXiv](https://nmrxiv.org/)** depends on the format used. Here you can find the ideal structuring you need to apply.

### Identify your datasets

- Bruker: The dataset is the entire Bruker **folder**. Many NMR experiments (1H, 13C, etc.) will lead to many Bruker **folders**, with each being a dataset.

- JCAMP-DX: The dataset is one jcamp-dx **file**. Many NMR experiments (1H, 13C, etc.) will lead to many jcamp-dx **files**, with each being a dataset.

- JEOL: The dataset is one jeol **file**. Many NMR experiments (1H, 13C, etc.) will lead to many jeol **files**, with each being a dataset.

<p align="center">

<img src="/img/dataset-structure.png" width="500" />

</p>

### Group Datasets per Studies

All experiments (datasets) belonging to one sample should go to one folder. It means one study can contain several Bruker folders. Alternatively, one study can contain several JCAMP files and so. In the below image, you can see two studies (Bruker study and JCMAP study). Bruker study has two datasets, while the JCAMP study has five datasets (the mol file isn't a dataset).

<p align="center">

<img src="/img/study/structure.png" width="600" />

</p>

#### Mixing Formats in One Study

The sample rules apply when the user needs, for example, to add an exported JCAMP file to their Bruker data. The JCAMP file should added to the level of study it represents as it is one dataset. Same goes for other formats such as **.nmrium**, or **.nmredata**. It means in one study folder you can find JCAMP files and NMReData files at the same level next to Bruker folders (as long all of them belong to the same sample).

### Group Studies per Project

All the data submitted to **[nmrXiv](https://nmrxiv.org/)** in one process is grouped as a project. Therefore, make sure that you group related studies together in one folder. How those studies are related is up to the user. They can be a collection of studies used for teaching, or a collection used in a thesis. They can also be a part of one investigation (several samples from one source).

## Final Step

Make sure to give your folders and datasets proper names. in **[nmrXiv](https://nmrxiv.org/)** you can rename projects and studies, but you can't rename the folders they came from or the datasets. After this step you are ready for [Data Submission](/submission-guides/submission/upload.md)
