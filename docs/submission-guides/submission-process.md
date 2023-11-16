# Data Submission

## Before You start

Before you start submitting your data, you need to:

- First, [register](/submission-guides/registration.md) to create an account in **[nmrXiv](https://www.nmrxiv.org)**, or login to **[nmrXiv](https://www.nmrxiv.org)** via single sign-on with your GitHub or Twitter ID. Please be aware that the single sign-on will result in registering you on **[nmrXiv](https://www.nmrxiv.org)**.
- Make sure to structure your folder in the way described [here](/submission-guides/submission/folder-structure).
- Make sure to include at least one [file format supported by NMRium](https://docs.nmrium.org/#2-open-spectra) in your folder to generate spectra.

### What You Need to Know

- Keep in mind that your submission will be structured as a **Project**.
- If you submit more than one folder at once, they will still be one **Project**, and the underlying folders with datasets will be studies.

## The Submission Pipeline

### Files Upload - Step 1

- Click on the `UPLOAD` button in your Dashboard or the [team Dahsboard](/submission-guides/data-model/team.md) to start the submission pipeline.

<p align="center">
<img src="/img/upload/upload-btn.png" width="1000"/>
</p>
- The window **Files Upload** will open.
<p align="center">
<img src="/img/upload/upload.png" width="1000"/>
</p>
- Provide details about your **Project**. Every field marked with a red asterisk is **mandatory**.
  - **Project Name** must be unique within the workspace (personal workspace or team).
  - **Project Description** must be at least 20 characters.
  - **Keywords** can be added singly by typing the keyword and pressing "enter", or in bulk by typing a list of keywords separated by commas and pressing enter. Keywords increase the visibility of the **Project**. 
- Drag and drop your folder to the dedicated field.
- The loading bar will get green as the upload progresses.
- Wait until the upload has ended.
- Your folders are now added to the root folder "\".
- Clicking on any folder on the left side will show an overview of its content on the right side. You can also navigate the folders with the arrows.
<p align="center">
<img src="/img/upload/folders.png" width="1000"/>
</p>
- At this step, you can delete any unwanted folders or files by selecting them from the panel on the left side and pressing the `Delete` button on the right side.

![File Upload](/img/upload/fileupload.gif)

### Assignement & Meta data - Step 2

Within this step, you can use [NMRium](https://www.nmrium.org/) to edit your spectra and assign peaks to the corresponding chemical groups. Additionally, you can provide the metadata of the studies and samples within the project.

- **STUDY** can be seen as a title at the top left side of the panel with the total number of studies within the project. In this panel, you can switch between studies and see the names of the underlying datasets. The dataset will have one of three colors:
  - **Green**: When the dataset is successfully viewed.
  - **Gray**: If there were no attempts to view the dataset yet, such as when you first open the study before you start viewing the datasets.
  - **Red**: When the attempt to view the dataset fails.

<p align="center">
<img src="/img/upload/study-panel.png" width="1000"/>
</p>

- **Experiments (Spectra)** is the first tab shown by default under the study name on the left side. Here you can navigate between different datasets (experiments) from the dropdown **Select Experiment**, which enables you to visualize and edit the corresponding spectra in the NMRium window in the middle. For more details on how to use NMRium, please check its [documentation](https://docs.nmrium.org/).

<p align="center">
<img src="/img/upload/experiments.png" width="1000"/>
</p> 

Directly above the NMRium window, you can see a gray message on the left that informs the user about the current status of the spectra (being uploaded, saved, etc.). On the right side, there exist three buttons. `Reset` will reset all the edits done on the spectra, getting it back to its original status when uploaded. Resetting is an edit that is saved along with other edits in the history. To view the history, click `Edit History`, which opens a side menu to show the editors, and the dates of edits.

<p align="center">
<img src="/img/upload/history.png" width="1000"/>
</p> 

The last button `Preview` helps is to refresh the spectra when you face any issues viewing your edits.

Below, there is [NMRium](https://www.nmrium.org/) window. Clicking on the `User manual` leads to [NMRium Documentation page](https://docs.nmrium.org/). You can control the panels and tools you have by default in [NMRium](https://www.nmrium.org/) from `General settings`. You can also click `Full screen` to enable that mode.

<p align="center">
<img src="/img/upload/manual.png" width="1000"/>
</p> 

Below the [NMRium](https://www.nmrium.org/) window there are two tables:

- **Info** includes all the information extracted by [NMRium](https://www.nmrium.org/) about the NMR experiment.

- **Meta** is the second table coming after **Info**. It includes the metadata from the instrument file.

![Assignement & Meta data](/img/upload/assignment-metadata.gif)

- **Sample Info** tab shows details about the chemical composition of the sample (available molecules and their percentages). You can load structures by adding **SMILES** to the corresponding field and pressing `Load Structure`. It will view the structure in the [structure editor](/submission-guides/submission/editor.md), where you can apply any needed changes. Alternatively, you can draw the structure in the [editor](/submission-guides/submission/editor.md) from scratch. When you feel satisfied with the structure, set its percentage using the bar and press `ADD`. The molecule with its percentage and INChI will appear in the molecules list on the left side. Added structures can be `Edit`ed and `Delete`d using the corresponding buttons. For more details about the structure editor, please [visit its documentation](/submission-guides/submission/editor.md).

:::caution
The purpose of the **Sample chemical composition** section is to enable the user to add the molecules they are aware of their existence in the sample **before the NMR spectroscopy** along with their percentages. Molecules identified through the NMR analysis are located at the dataset level.
:::

:::caution Please note
If you don't know the percentage of a molecule in your sample, you have to set it to **zero**.
:::

<p align="center">
<img src="/img/upload/sample.png" width="1000"/>
</p> 

- **Meta Data** tab shows an overview of the selected study's metadata. You can edit this metadata at this level or after submission as long your study is private. You can press on **Auto generate** to auto-generate the study description. Please don't forget to press `SAVE` before you proceed.

After providing all the details in this tab, you can go back to step 1 with the button `BACK`, you can `CANCEL` the submission, or you can `PROCEED` to step 3.

<p align="center">
<img src="/img/upload/meta.png" width="1000"/>
</p> 

### Complete - Step 3

In step 3, you can find how your project compares to [recommended community standards](https://nfdi4chem.github.io/workshops/). Whenever a piece of information is missing, you will have it marked in red with an `Edit` button for a quick update. You can press `FINISH` to end the submission process even while having red marks, but your project will remain private, and complying with the standards is a prerequisite for publishing. Here are more details about the validation of [projects](/submission-guides/data-model/project#validation), [studies](/submission-guides/data-model/study#validation), and [datasets](/submission-guides/data-model/dataset#validation)

<p align="center">
<img src="/img/upload/check.png" width="1000"/>
</p> 

## After Submission

After completing your submission, you can still manage to add and delete folders and files to and from your project. This option is available in the [project view](/submission-guides/data-model/project#project-view) as the [`Manage Studies` button](/submission-guides/data-model/project#manage-studies) and in the [dataset view](/submission-guides/data-model/dataset#dataset-view) as the [`Manage Datasets` button](/submission-guides/data-model/dataset#manage-datasets).
