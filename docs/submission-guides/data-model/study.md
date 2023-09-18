# Study
The concept of study in **[nmrXiv](https://nmrxiv.org/)** is similar to the one of [Study](https://isa-specs.readthedocs.io/en/latest/isamodel.html#study) in [ISA model](https://isa-tools.org/format/specification.html). Therefore, the study helps group related datasets and describe their collective metadata. Those datasets are related as they correspond to experiments applied to the same sample. Therefore, one study corresponds to one sample in **[nmrXiv](https://nmrxiv.org/)**. **[nmrXiv](https://nmrxiv.org/)** is capable of detecting datasets automatically and grouping them in studies, which requires the user to make sure that they submit their data in a compatible way. For more details, it is highly recommended to check the [folder structuring section](/docs/submission-guides/submission/folder-structure.md) before submitting data.


## Study View
### Private Study View
<p align="center">
<img src="/img/study/private-view.png" width="1000"/>
<figcaption>Private study view</figcaption>
</p>


As with the project, the study's public view differs from the private one. Starting with the private one, at the top, there is the path to the study (Dashboard > CENAPTnmr), followed by a somehow similar view to the project. Below is the study **name** (Eugenol 400 MHz CDCl3 NMR data), which in contrast to the project, doesn't have to be unique within the project namespace. Next to the name is a star to indicate whether the study is starred and to enable starring/de-starring, which helps to bookmark studies. Starring a project doesn't propagate to the underlying studies. Starred studies can all be found in the `Starred` folder in the left panel. Below, one can see the avatars of the users with whom the study is shared as circles with either their images or names' first letters. Clicking on those avatars enables sharing control (more on that in [Share](#share)). One can also click on `View details` to view and edit the study details (more on that in [Edit](#edit)), and see the privacy of the study, when it was last updated, and the ownership by the user. Those three are not clickable but editable from `View details` and `Share`.

<p align="center">
<img src="/img/study/top.png" width="1000"/>
<figcaption>The top of study view</figcaption>
</p>


Moving down, there are three tabs: `About`, `Datasets`, and `Files`, with `About` being the one shown by default when entering a study, and the one similar to the project view. Its view differs in the private study from the public's one by having `edit` buttons next to the fields in the private one. Those fields include the study `Description` must be at least 20 characters describing its content and/or its purpose, while the `Keywords` are tags used to search for the study and increase its findability. Then comes the `License` with an `i`nformation sign. When hovering over the `i`, a brief description of the license appears. More about **[nmrXiv](https://nmrxiv.org/)** licenses can be found [here](/docs/submission-guides/licenses.md) 

<p align="center">
<img src="/img/study/edit.png" width="1000"/>
</p>

At the bottom is the **Sample chemical composition**, which also differs between private and public studies. This section is similar to the [**Sample Info**](/docs/submission-guides/submission/upload#assignement--meta-data---step-2) tab during the [submission upload](/docs/submission-guides/submission/upload.md). More on how to edit this section in [Sample chemical composition section](#sample-chemical-composition). More on how to use the structure editor in [Structure Editor Page](/docs/submission-guides/submission/editor.md).


:::caution 
The purpose of the **Sample chemical composition** section is to enable the user to add the molecules they are aware of their existence in the sample **before the NMR spectroscopy** along with their percentages. Molecules identified through the NMR analysis are located at the dataset level.
:::

The `Datasets` tab has a dropdown menu with all the datasets within the study. This tab will be covered in [Dataset page](/docs/submission-guides/data-model/dataset.md).

<p align="center">
<img src="/img/study/datasets.png" width="1000"/>
<figcaption>Study Datasets tab</figcaption>
</p>


The `Files` tab view is similar in both private and public studies where the user can select folders on the left side to show an overview of their content on the right side. They can also navigate the folders with arrows. Within the folder, they can select files to show their metadata on the right side. For both selected folders and files there is a `Download` button.

<p align="center">
<img src="/img/study/files.png" width="1000"/>
<figcaption>Study Files tab</figcaption>
</p>



### Public Study View

<p align="center">
<img src="/img/study/public-view.png" width="1000"/>
<figcaption>Public study view</figcaption>
</p>


The public study view is similar to the [private's](#private-project-view), but still, there are some differences. Once a project is public, a green bar appears at the top informing the user that no more edits are possible, and any needed changes can be made as a new version.
Moving down, a blue bar contains the citation details which can be adjusted according to the citation system (can be picked from the drop-down menu on the right). The citation includes a DOI to the study, which contains the study identifier (S46 in the image). This identifier always has the structure of "S", which stands for the study, and a number. Studies identifiers are [**Persistent Identifiers (PID)**](https://en.wikipedia.org/wiki/Persistent_identifier) within the repository. Additionally, all the `Edit` buttons disappear in public study as no more editing is allowed after publishing.

With public studies, the option of adding more structures disappears, removing the entire right side of the section, including the structure editor with the [SMILES](https://www.daylight.com/dayhtml/doc/theory/theory.smiles.html) field and the percentage bar. On the left side, one can see all the added molecules with their percentage and [INChI](https://iupac.org/who-we-are/divisions/division-details/inchi/) in the molecules list.

## Create
There are two ways to create studies. First is through the [submission pipeline](/docs/submission-guides/submission/upload.md) where the studies will be created within the submitted project. Second is after submission from within the project view using [`Manage Studies` button](/docs/submission-guides/data-model/project#manage-studies), which leads back to the [submission pipeline](/docs/submission-guides/submission/upload.md). 

## Access
You can access your created studies by [entering their parent projects](/docs/submission-guides/data-model/project/#access) and scrolling down to **Studies**. You can also find the ones shared with you by others in the `Shared with me` folder directly if the parent project is not shared with you. Studies that you have starred can be found in the `Starred` folder. Other folders, such as `Recent` and `Trash`, can include projects only. Lists of studies show a quick view of them, including their **name**, **privacy**, and **last updating date**.

<p align="center">
<img src="/img/project/studies.png" width="1000"/>
<figcaption>Studies within a project</figcaption>
</p>


## Edit
To edit a study, you should have **editing** access to it, which is the case when you are its creator or when it is shared with you as an owner or a collaborator. The study should also still be private.

You can edit the study from `View details` at the top of the study view or from any `Edit` buttons found next to the fields, while you can edit the sample in the  **Sample chemical composition** section at the bottom.

### View Details
Clicking on `View details` opens a side menu where the user can edit the study name, description, keywords, color, and license, and they can view the privacy.

<p align="center">
<img src="/img/study/view-details.png" width="1000"/>
<figcaption>View details side menu</figcaption>
</p>


At the bottom, there is a link to the [Share](#share) section and an `activity` button, which opens a new side menu to show the updates history of the study.

<p align="center">
<img src="/img/study/activity.png" width="1000"/>
<figcaption>Study activity log</figcaption>
</p>


### Edit Button
For all the fields `Description`, `Keywords`, and the `License`, the `Edit` button will open the same side menu as with `View details`.

### Sample chemical composition

In the private mode, you can load structures by adding **SMILES** to the corresponding field and pressing `Load Structure`. It will view the structure in the [structure editor](/docs/submission-guides/submission/editor.md), where you can apply any needed changes. Alternatively, you can draw the structure in the editor from scratch. Here are more [details on how to use the structure editor](/docs/submission-guides/submission/editor.md). When you feel satisfied with the structure, set its percentage using the bar and press `ADD`. The molecule with its percentage and INChI will appear in the molecules list on the left side. Added structures can be `Edit`ed and `Delete`d using the corresponding buttons.

:::caution
The purpose of the **Sample chemical composition** section is to enable the user to add the molecules they are aware of their existence in the sample **before the NMR spectroscopy** along with their percentages. Molecules identified through the NMR analysis are located at the dataset level.
:::

:::caution
If you don't know the percentage of a molecule in your sample, you have to set it to **zero**.
:::

<p align="center">
<img src="/img/study/private-sample.png" width="1000"/>
<figcaption>Sample chemical composition section of a private study</figcaption>
</p>


## Validation

To publish a study, i.e., to make it public, you need to publish its parent project, which must comply with community standards. At the moment, we are checking for [DataCite](https://datacite.org/) mandatory metadata needed to receive a [DOI](https://www.doi.org/) in addition to requesting an image for the project. If any piece of metadata is missing, you will see a **Why can't I publish?** link message at the top of the project view.

<p align="center">
<img src="/img/project/publish.png" width="1000"/>
</p>

Clicking on **Why can't I publish?** leads to a new page similar to the [step-3 of the submission pipeline](/docs/submission-guides/submission/upload#complete---step-3). Here you can find either red <span style="color:red;">x</span> or green <span style="color:green;">✓</span> to indicate the existence or absence of the metadata respectively. Whenever the red <span style="color:red;">x</span> exists, it is accompanied by an `Edit` button to facilitate providing the missing data. If the datasets validation fails, the study validation fails too as a parent, which can be inferred from the red highlighting of its name. More on validating datasets in the [dataset page](/docs/submission-guides/data-model/dataset#validation)

<p align="center">
<img src="/img/study/validation.png" width="1000"/>
<figcaption>Validation Checklist of the Study</figcaption>
</p>

## Share

Studies are shared by default with the members of the parent project. At the top of the study view, there are avatars of the users with whom the study is shared as circles with either their images or names' first letters. Clicking on those avatars enables modifying the sharing settings by first opening a window that shows with whom the study is already shared along with the roles.

<p align="center">
<img src="/img/study/new-share.png" width="1000"/>
<figcaption>Study sharing status</figcaption>
</p>

Then the user can click on the button `Share`, which will open another window to share the study with other users.
<p align="center">
<img src="/img/study/select-share.png" width="1000"/>
<figcaption>Share with users window</figcaption>
</p>

In the opened window, the user needs to fill in the email and select the role of the user to share with, and they can optionally add a message. There are three roles for sharing:
- **Owner**: read/update/delete the study/datasets.
- **Collaborator**: read/update the study/datasets. A collaborator cannot delete any of the study content.
- **Viewer**: read the study/datasets. A viewer cannot update or delete any of the study content.

When the user is satisfied with their entries, they can click `SEND` to send an email to the other user. Then the window gets refreshed to enable entering details of other users to share with them. When all the invitations are sent, the user can click the back arrow to go back to the project-sharing status window, which will be updated to show the status of the new invitations. Sharing can be canceled as long the invitation is still pending (not accepted or rejected by the receivers). 

<p align="center">
<img src="/img/study/update-share.png" width="1000"/>
<figcaption>Updated project sharing status</figcaption>
</p>

After an invitation is accepted, the creator can still remove any of the members, while the owners can remove any of them but the creator.

## Delete and Archive
Only private studies can be deleted, while public ones can be archived. The reason is that once a study is public, it can be referenced somewhere else, which requires the repository to keep an active link to this study. Therefore, when the creators of a public study decide that it is no longer maintained, or that the data provided is not accurate, they can only archive it instead of deleting it, while deletion is always an option for private studies.

[Deleting/Archiving](/docs/submission-guides/data-model/project#delete-and-archive) will delete/archive all the underlying studies. In the case of project deletion, this action is reversible for 30 days as the deleted project will be stored in the **Trash** folder, where the user can find the project and restore it from the `Project Settings`. After 30 days, deletion leads to permanently deleting the project and all of its content data. In the case of archiving, the user can unarchive the project anytime from the `Project Settings`. Archived projects are not moved to the trash.

However, there is another way to delete studies, which is from the [`Manage Studies` button](/docs/submission-guides/data-model/project#manage-studies) in the [project view](/docs/submission-guides/data-model/project#project-view).


