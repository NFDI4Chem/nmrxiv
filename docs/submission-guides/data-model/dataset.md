# Dataset
The concept of the dataset in **[nmrXiv](https://nmrxiv.org/)** is similar to the one of [assay](https://isa-specs.readthedocs.io/en/latest/isamodel.html#assay) in [ISA model](https://isa-tools.org/format/specification.html) yet broader, as it refers to the files corresponding to experiments along with their metadata. For instance, one dataset could be a Bruker folder with all the underlying files corresponding to a 1H assay. **[nmrXiv](https://nmrxiv.org/)** is capable of detecting datasets automatically and grouping them in studies, which requires the user to ensure submitting their data in a compatible way. For more details, it is highly recommended to check the [folder structuring section](/docs/submission-guides/submission/folder-structure.md) before submitting data.

## Dataset View
<p align="center">
<img src="/img/dataset/view.png" width="1000"/>
<figcaption>Dataset view</figcaption>
</p>


The view of a public dataset is similar to the private's one. In fact, it is just a tab from the [study view](/docs/submission-guides/data-model/study/#study-view), and it is similar to the [Step 2](/docs/submission-guides/submission/upload#assignement--meta-data---step-2) Experiments (Spectra) tab of the [Data Submission Pipeline](/docs/submission-guides/submission/upload). At the top, there is a drop-down menu to select an experiment (within a study, thus, sample). 
<p align="center">
<img src="/img/dataset/experiment.png" width="1000"/>
<figcaption>Select experiment menu</figcaption>
</p>


The selected experiment will be viewed in the embedded NMRium. For more details about [NMRium](https://www.nmrium.org/), please visit our [NMRium documentation page](/docs/advanced-guides/nmrium/) and [NMRium official documentation](https://docs.nmrium.org/).

Next to the `Select Experiment` menu, there is a `Manage Datasets` button, which enables adding datasets or deleting them to and from a study through the [submission pipeline](/docs/submission-guides/submission/upload.md).

Directly above the NMRium window, you can see a gray message on the left that informs the user about the current status of the spectra (being uploaded, saved, etc.). On the right side, there exist three buttons. `Reset` will reset all the edits done on the spectra, getting it back to its original status when uploaded. Resetting is an edit that is saved along with other edits in the history, which can be viewed by clicking `Edit History`, which opens a side menu to show the editors, and the dates of edits.

<p align="center">
<img src="/img/dataset/history.png" width="1000"/>
</p> 

Below, there is [NMRium](https://www.nmrium.org/) window. Clicking on the `User manual` leads to [NMRium Documentation page](https://docs.nmrium.org/). You can control the panels and tools you have by default in [NMRium](https://www.nmrium.org/) from `General settings`. You can also click `Full screen` to enable that mode.

If any structure is added to `Chemical structures` field in NMRium, it appears directly below NMRium. Then there is the metadata of the experiment:
- **Info** includes all the information extracted by [NMRium](https://www.nmrium.org/) about the NMR experiment.

- **Meta** is the second table coming after **Info**. It includes the metadata from the instrument file.

## Create
There are two ways to create datasets. First is through the [submission pipeline](/docs/submission-guides/submission/upload.md), where the datasets will be automatically detected. The second is after submission from the `Datasets` tab within a study by clicking on [`Manage Datasets` button](#manage-datasets) and dragging and dropping files or folders to the opened [submission pipeline](/docs/submission-guides/submission/upload.md), but the second option is possible only for private ones. Please pay attention to dragging the datasets to the right study folder. 

## Access
You can access your created datasets and the ones shared with you by [entering their parent studies](/docs/submission-guides/data-model/study/#access) and going to the `Datasets` or `Files` tabs. All the public datasets on **[nmrXiv](https://nmrxiv.org/)** are in the `Datasets` folder.

## Edit
To edit a dataset, you should have **editing** access to it, which is the case when you are its creator or when it is shared with you as an owner or a collaborator. The dataset should also still be private. You can edit the dataset through NMRium, and a history of changes is kept.

### Manage Datasets
From the `Datasets` tab within a study, you can add more datasets by dragging and dropping files or folders to the opened [submission pipeline](/docs/submission-guides/submission/upload.md). Please pay attention to dragging the datasets to the right study folder. Or you can delete them by selecting a dataset **in the left panel** and pressing `Delete`.

## Validation

To publish a dataset, i.e., to make it public, you need to publish its parent project, which must comply with community standards. At the moment, we are checking for [DataCite](https://datacite.org/) mandatory metadata needed to receive a [DOI](https://www.doi.org/) in addition to requesting an image for the project. If any piece of metadata is missing, you will see a **Why can't I publish?** link message at the top of the project view.

<p align="center">
<img src="/img/project/publish.png" width="1000"/>
</p>

Clicking on **Why can't I publish?** leads to a new page similar to the [step-3 of the submission pipeline](/docs/submission-guides/submission/upload#complete---step-3). Here you can find either red <span style="color:red;">x</span>, or a green <span style="color:green;">✓</span> to indicate the existence or absence of the mandatory metadata respectively. A yellow <span style="color:orange;">⚠</span> indicates the absence of recommended (not mandatory) metadata. Whenever the red <span style="color:red;">x</span> or the yellow <span style="color:orange;">⚠</span> exist, they are accompanied by an `Edit` button to facilitate providing the missing data. Here are more details about why a certain dataset validation fails:
- Files: This field checks whether there are spectral NMR data files. Since the [project submission](/docs/submission-guides/submission/upload.md) (not publishing) is not possible without spectral files, this field always passes the validation before submission <span style="color:green;">✓</span>.
- NMRium info: This field is about whether the dataset was processed completely by NMRium, where its metadata gets parsed and the spectrum gets viewed. If this field doesn't pass the validation <span style="color:red;">x</span>, please go to the respective dataset (with the edit button), and there click on `Preview`, which will update the preview and save it. You can make sure that the metadata is generated by checking the existence of the `Info` table below NMRium <span style="color:green;">✓</span>.
- Assay (Metadata): This feature is in development and, at the moment, the corresponding field will always pass the validation <span style="color:green;">✓</span>.
- Assignments: The molecular assignment with NMRium is recommended by **[nmrXiv](https://nmrxiv.org/)**. Therefore, whenever it is not provided, the field gets marked with a yellow <span style="color:orange;">⚠</span>, but it is still possible to publish without it. To provide the molecular assignment, please refer to [NMRium documentation](https://docs.nmrium.org/structure_assignment/assign/add).

<p align="center">
<img src="/img/dataset/validation.png" width="1000"/>
<figcaption>Validation Checklist of the Dataset (within a study)</figcaption>
</p>

## Share
[Sharing a study](/docs/submission-guides/data-model/study#share) results in sharing all the underlying datasets. There is no way to share individual datasets.

## Delete and Archive
Only private datasets can be deleted, while public ones can be archived. The reason is that once a dataset is public, it can be referenced somewhere else, which requires the repository to keep an active link to this dataset. Therefore, when the creators of a public dataset decide that it is no longer maintained, or that the data provided is not accurate, they can only archive it instead of deleting it, while deletion is always an option for private datasets.

Deleting/Archiving a [project](/docs/submission-guides/data-model/project#delete-and-archive) or a [study](/docs/submission-guides/data-model/study#delete-and-archive) will delete/archive all the underlying datasets. In the case of project deletion, this action is reversible for 30 days as the deleted project will be stored in the **Trash** folder, where the user can find the project and restore it from the `Project Settings`. After 30 days, deletion leads to permanently deleting the project and all of its content data. In the case of archiving, the user can unarchive the project anytime from the `Project Settings`. Archived projects are not moved to the trash.

However, there is another way to delete datasets, which is from the [`Manage Datasets` button](#manage-datasets) in the [dataset view](#dataset-view).