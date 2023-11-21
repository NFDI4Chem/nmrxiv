# Spectra

The concept of the spectra in **[nmrXiv](https://nmrxiv.org/)** is similar to the one of [assay](https://isa-specs.readthedocs.io/en/latest/isamodel.html#assay) in [ISA model](https://isa-tools.org/format/specification.html) yet broader, as it refers to the files corresponding to experiments along with their metadata. For instance, one spectra could be a Bruker folder with all the underlying files corresponding to a 1H assay. **[nmrXiv](https://nmrxiv.org/)** is capable of detecting datasets automatically and grouping them in samples, which requires the user to ensure submitting their data in a compatible way. For more details, it is highly recommended to check the [folder structuring section](/submission-guides/submission/folder-structure.md) before submitting data.

## Spectra View

<p align="center">
<img src="/img/spectra/view.png" width="1000"/>
<figcaption>spectra view</figcaption>
</p>

The selected experiment will be viewed in the embedded [NMRium](https://www.nmrium.org/). For more details about [NMRium](https://www.nmrium.org/), please visit our [NMRium documentation page](/advanced-guides/nmrium/nmrium.html) and [NMRium official documentation](https://docs.nmrium.org/) to learn more.


Directly above the NMRium window, you can see a gray message on the left that informs the user about the current status of the spectra (being uploaded, saved, etc.). On the right side. You can navigate through individual experiments by double-clicking on the three dots on the right, marked by an arrow in the screenshot above. `Reset` will reset all the edits done on the spectra, getting it back to its original status when uploaded. Resetting is an edit that is saved along with other edits in the history, which can be viewed by clicking `Edit History`, which opens a side menu to show the editors, and the dates of edits.

<p align="center">
<img src="/img/spectra/history.png" width="1000"/>
</p> 

Below, there is [NMRium](https://www.nmrium.org/) window. Clicking on the `User manual` leads to [NMRium Documentation page](https://docs.nmrium.org/). You can control the panels and tools you have by default in [NMRium](https://www.nmrium.org/) from `General settings`. You can also click `Full screen` to enable that mode.

If any structure is added to `Chemical structures` field in NMRium, it appears directly below NMRium. Then there is the metadata of the experiment:

- **Info** includes all the information extracted by [NMRium](https://www.nmrium.org/) about the NMR experiment.

- **Meta** is the second table coming after **Info**. It includes the metadata from the instrument file.

## Access

You can access your created datasets and the ones shared with you by [entering their parent sample](/submission-guides/data-model/sample/#access) and going to the `Spectra` or `Files` tabs. All the public datasets on **[nmrXiv](https://nmrxiv.org/)** are in the `Spectra` folder.

## Edit

To edit a spectra, you should have **editing** access to it, which is the case when you are its creator or when it is shared with you as an owner or a collaborator. The spectra should also still be private. You can edit the spectra through NMRium, and a history of changes is kept.

## Validation

To publish a spectra, i.e., to make it public, you need to publish its parent project, which must comply with community standards. At the moment, we are checking for [DataCite](https://datacite.org/) mandatory metadata needed to receive a [DOI](https://www.doi.org/) in addition to requesting an image for the project. If any piece of metadata is missing, you will see a **Why can't I publish?** link message at the top of the project view.

<p align="center">
<img src="/img/project/publish.png" width="1000"/>
</p>

Clicking on **Why can't I publish?** leads to the second step of the submission process. Here you can find either red <span style="color:red;">x</span>, or a green <span style="color:green;">✓</span> to indicate the existence or absence of the mandatory metadata respectively. A yellow <span style="color:orange;">⚠</span> indicates the absence of recommended (not mandatory) metadata. Whenever the red <span style="color:red;">x</span> or the yellow <span style="color:orange;">⚠</span> exist, they are accompanied by an `Edit` button to facilitate providing the missing data. Here are more details about why a certain spectra validation fails:

- Files: This field checks whether there are spectral NMR data files. Since the [project submission](/submission-guides/submission-process.md) (not publishing) is not possible without spectral files, this field always passes the validation before submission <span style="color:green;">✓</span>.
- NMRium info: This field is about whether the spectra was processed completely by NMRium, where its metadata gets parsed and the spectrum gets viewed. If this field doesn't pass the validation <span style="color:red;">x</span>, please go to the respective spectra (with the edit button), and there click on `Preview`, which will update the preview and save it. You can make sure that the metadata is generated by checking the existence of the `Info` table below NMRium <span style="color:green;">✓</span>.
- Assay (Metadata): This feature is in development and, at the moment, the corresponding field will always pass the validation <span style="color:green;">✓</span>.
- Assignments: The molecular assignment with NMRium is recommended by **[nmrXiv](https://nmrxiv.org/)**. Therefore, whenever it is not provided, the field gets marked with a yellow <span style="color:orange;">⚠</span>, but it is still possible to publish without it. To provide the molecular assignment, please refer to [NMRium documentation](https://docs.nmrium.org/structure_assignment/assign/add).

<p align="center">
<img src="/img/spectra/validation.png" width="1000"/>
<figcaption>Validation Checklist of the spectra (within a sample)</figcaption>
</p>

## Share

[Sharing a sample](/submission-guides/data-model/sample#share) results in sharing all the underlying datasets. There is no way to share individual datasets.

## Delete and Archive

Only private datasets can be deleted, while public ones can be archived. The reason is that once a sample is public, it can be referenced somewhere else, which requires the repository to keep an active link to this spectra. Therefore, when the creators of a public spectra decide that it is no longer maintained, or that the data provided is not accurate, they can only archive it instead of deleting it, while deletion is always an option for private datasets.

Deleting/Archiving a [project](/submission-guides/data-model/project#delete-and-archive) or a [sample](/submission-guides/data-model/sample#delete-and-archive) will delete/archive all the underlying datasets. In the case of project deletion, this action is reversible for 30 days as the deleted project will be stored in the **Trash** folder, where the user can find the project and restore it from the `Project Settings`. After 30 days, deletion leads to permanently deleting the project and all of its content data. In the case of archiving, the user can unarchive the project anytime from the `Project Settings`. Archived projects are not moved to the trash.

However, there is another way to delete spectras, which is from the [`Manage Sample` button](/submission-guides/data-model/project.html#manage-samples) in the sample view.
