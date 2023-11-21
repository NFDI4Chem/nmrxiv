# FAQs

### How can I submit my data to **[nmrXiv](https://nmrxiv.org/)**?

- [Register to nmrXiv](/submission-guides/registration.md).
- Structure your data in folders similar to **[nmrXiv](https://nmrxiv.org/)** structuring of projects, studies, and datasets. This step might not be intuitive so we recommend [checking its docummentation](/submission-guides/folder-structure.html).
- Upload your data, edit it, and provide its metadata via the [submission pipeline](/submission-guides/submission-process.html).

### Do I need to register before submitting data to nmrXiv?

Yes. Registration is a prerequisite to submitting the data. Although you can submit data by logging in via Single sign-on with your GitHub or Twitter ID, this logging-in will result in registering you on **[nmrXiv](https://nmrxiv.org/)**. Alternatively, you can register via your email id. More on the registration [here](/submission-guides/registration.md).

### How should I structure my data in folders before submitting it to **[nmrXiv](https://nmrxiv.org/)**?

**[nmrXiv](https://nmrxiv.org/)** can structure the submitted data into datasets/studies/projects, but there is still no way to guarantee that the automatically generated structure is what the user was hoping to get. Therefore, it is recommended to have a look at the [folder structuring page](/submission-guides/folder-structure.html).

### What are supported files format in **[nmrXiv](https://nmrxiv.org/)**?

**[nmrXiv](https://nmrxiv.org/)** accepts all NMR formats uploaded. However, not all of them are readable at the moment. So far, only NMRium-supported formats can be translated into spectra in **[nmrXiv](https://nmrxiv.org/)**. Those formats are **jcamp-dx, jeol, Bruker folders, NMReData, and nmrium**. For validation purposes, the uploaded data should have at least one readable format.

### What happens to my data once submitted?
After you submit your data, it will remain private and will only be visible to you, unless you decide to make it public.

All the data uploaded to nmrXiv are stored in the S3 bucket provisioned by the University Computing Center of [Friedrich-Schiller-Universität Jena](https://www.uni-jena.de/) and are backed up daily to the [Google Cloud Storage Archive Storage](https://cloud.google.com/storage/docs/storage-classes#archive) with multiple regions located in the European Union.

### What are public and private objects in **[nmrXiv](https://nmrxiv.org/)**?

**Public/Published** objects (projects, studies, and datasets) are visible and accessible to everyone (even to the non-registered users of **[nmrXiv](https://nmrxiv.org/)**). You can see all the open projects [here](https://nmrxiv.org/projects) or find them in the Projects tab in the left-hand panel of your dashboard.

**Private** objects (projects, studies, and datasets) are only visible and accessible to the people with whom they are shared by [single sharing](/submission-guides/data-model/sharing.html#single-sharing) or in a [team](/submission-guides/data-model/sharing.html#teams-sharing).

:::danger Caution
Once an object is made public, it cannot be edited, versioned, or deleted anymore, nor be made private again.
:::

### Who can use my public/published resources?

If you make your resources public (projects, studies, datasets), you are making them open for access to everyone (even to the non-registered user of **[nmrXiv](https://nmrxiv.org/)**), but you can specify rights by choosing [licenses](/submission-guides/licenses.md) for your projects and studies (study license propagate to its datasets). Once your project is made public, you cannot edit, delete or make it private again.

### How can I edit my public resources?

You cannot edit a resource (project, study, dataset) once it's made public, but you can always create another version and make changes on top of it.

### How can I delete my projects, studies or dataset?

You can only delete private ones. For more details, check deletion of [projects](/submission-guides/data-model/project#delete), [samples](/submission-guides/data-model/sample#delete), and [spectra](/submission-guides/data-model/spectra#delete).

### How can I share my resources?

You can share your resources (projects, studies, datasets) singly or in bulks within teams. For more details on sharing, please check the [sharing page](/submission-guides/data-model/sharing.md).

### What are the available roles when sharing a resource?

You can assign roles to people with whom you share content:

- **Owner** - Can read and/or update, including deleting the project, study, and dataset.
- **Collaborator** -  Can read and/or update the project, study, and dataset.
- **Reviewer** - Can only read the project, study, and dataset.

### How can I delete or edit my account?

You can edit your account details by heading to your name at the top right corner and clicking on the `Account` tab from the drop-down. To delete your account, please reach out to our [Helpdesk](https://www.nfdi4chem.de/index.php/helpdesk/), or write to us at info.nmrxiv@uni-jena.de.

### How can I license my resources, and which license to choose?

You can license your project during or after submission as long as it is private. The license will propagate to the underlying studies, but you can still change the studies licenses. For more details about licenses and when to use each one, please visit the [licenses page](/submission-guides/licenses.md).

### Can I use **[nmrXiv](https://nmrxiv.org/)** for my teaching and demo purposes?

Yes, you can use our [dev site](https://dev.nmrxiv.org) for all kind of training, demo and teaching purpose. Please avoid using the official **[nmrXiv](https://nmrxiv.org/)** site for the mentioned purpose. However, the [dev site](https://dev.nmrxiv.org) is just a sandbox, and all the data there could be reset anytime.
To learn more about our environment click [here](/developer-guides/deployment/environment).

### How to report a bug?

Before reporting a new issue

- Check the existing [issues](https://github.com/NFDI4Chem/nmrxiv/issues) to avoid duplication. [Here](https://docs.github.com/en/issues/tracking-your-work-with-issues/filtering-and-searching-issues-and-pull-requests#searching-for-issues-and-pull-requests) are some tips that can help you to narrow down your search. You can also comment on existing issues to provide additional details.
- Check our [documentation](https://docs.nmrxiv.org/) first, if you can find an answer to your question.

If the above criteria are not met

- Click [here](https://github.com/NFDI4Chem/nmrxiv/issues/new/choose) to report a new one under appropiate category.

### How to reach to you?

Please write to us at info.nmrxiv@uni-jena.de or reach out to our [Helpdesk](https://www.nfdi4chem.de/index.php/helpdesk/).
You can also leave us a message via our support bubble which you find in the left down corner on our [application](https://nmrxiv.org/) page.
We will try to get back to your queries as soon as possible.
