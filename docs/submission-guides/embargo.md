# Embargo Publication

Embargo publication lets you prepare a project for public release while keeping the data private until a selected release date. This is useful when you need a stable project record before a manuscript is accepted, during peer review, or while coordinating a planned publication.

During the embargo period, the project remains private to the project owner and permitted collaborators. nmrXiv stores the selected release date and will try to publish the project automatically when that date is reached.

::: warning
Once a project is successfully published, the underlying project data becomes publicly available and the project can no longer be edited as a private draft.
:::

## When to Use Embargo

Use an embargo release when:

-   You want to prepare a complete project now but make it public later.
-   You need time for manuscript review, publication coordination, or internal checks.
-   You want the project to keep a scheduled release date while the data remains private.

Use immediate publication instead when:

-   The data is ready to be public today.
-   You already have all required metadata, authors, citations, license, and files.
-   You do not need a private review period.

## Before You Schedule an Embargo

Before setting an embargo release, complete the required project information:

-   Project name and description.
-   Keywords.
-   Authors.
-   Citation information, when required.
-   License.
-   Project profile image.
-   Required sample metadata.
-   Required spectra, files, molecule, assay, and assignment information where applicable.

nmrXiv validates the project before publication. If required information is missing, the project cannot be published automatically when the release date is reached.

## Schedule an Embargo Release

1. Upload your data and complete the submission workflow.
2. On the final publish screen, choose `Embargo` as the release option.
3. Select the release date when the project should become public.
4. Review the validation checklist.
5. Accept the terms and conditions.
6. Confirm the embargo publication.

After confirmation, the project is shown as an embargo project in your dashboard. The scheduled release section displays the release date and provides quick access to sharing and editing actions.

## Screenshots

Below are screenshots showing the embargo workflow in the nmrXiv application. Place the image files in `docs/.vitepress/public/images/` (create the directory if it doesn't exist) with the filenames shown below so they render correctly in the documentation site.

![Embargo modal confirmation](/images/embargo-modal-confirmation.png)

![Embargo release settings](/images/embargo-release-settings.png)

![Update release date modal](/images/embargo-update-release-date.png)

If you prefer the images stored elsewhere, update the paths above to match the actual locations.

## What Happens During Embargo

While a project is under embargo:

-   The project is not public.
-   The project can be viewed and edited by users with permission.
-   The scheduled release date can be updated.
-   The dashboard displays the project with an `Embargo` status.
-   The public project page shows an embargo notice with the planned release date.

You can open the project from the dashboard to review metadata, manage authors, update citations, inspect samples, and complete any missing validation items.

## Automatic Publication

On or after the release date, nmrXiv automatically checks embargo projects that are due for publication. If the project passes validation, nmrXiv publishes it and sends the usual publication notifications.

After successful publication:

-   The project becomes publicly accessible.
-   The project data can be viewed and downloaded by others.
-   The DOI/public project link can be shared.
-   The project is no longer editable as a private embargo project.

## If Automatic Publication Fails

Automatic publication may fail if required information is missing or if a technical issue occurs. When this happens:

-   The project remains in embargo.
-   The project remains private.
-   The project remains editable.
-   The dashboard scheduled release section is highlighted in light red when the release date has passed.
-   The public project page shows an embargo notice explaining that publication could not be completed.
-   The project owner and nmrXiv administrators are notified by email with the failure details.

The project is not published until the issue is fixed and publication succeeds.

## Fix a Failed Embargo Publication

If your embargo project did not publish after the release date:

1. Open the project from your dashboard.
2. Check the validation checklist and complete the missing required fields.
3. Review project-level metadata such as authors, citations, keywords, license, and profile image.
4. Review sample-level metadata, spectra, files, molecules, assay information, and assignments.
5. Open the release date modal from the project page if you need to update the release date or publish again.
6. If the project still cannot be published, contact info.nmrxiv@uni-jena.de.

If the project missed its automatic release because validation failed, it can be published after the missing information is completed. Depending on the release date and system schedule, automatic publication may be retried later, or you can use the available publish action from the project page.

## Update the Release Date

You can update the release date while the project is still private and under embargo.

1. Open the embargo project.
2. Select `Edit release date`.
3. Choose a new release date.
4. Accept the required terms.
5. Save the updated release date.

If the new date is today or in the past, nmrXiv applies the publication validation rules before publishing. If the validation fails, the modal displays the required checklist so you can complete the missing items.

## Notifications

nmrXiv sends email notifications for important embargo events:

-   Release reminders before the scheduled release date.
-   Successful publication notifications when a project becomes public.
-   Failure notifications when automatic publication cannot be completed.

Failure notifications include the reason for failure, project details, and any required items that must be completed before publication can succeed.

## Frequently Asked Questions

### Can I edit a project after the release date has passed?

Yes, if automatic publication failed and the project is still private. A crossed release date alone does not make the project public. The project remains editable until publication succeeds.

### Why is my scheduled release highlighted in red?

The red scheduled release section means the release date has passed but the project is still not public. Open the project, complete the required validation items, and try publishing again.

### Does an embargo project become public automatically even if metadata is missing?

No. nmrXiv validates the project before publication. If required information is missing, publication is blocked and the project remains private.

### Can I change the release date after scheduling an embargo?

Yes, as long as the project is still private and editable.

### Who should I contact if I cannot resolve the issue?

Contact info.nmrxiv@uni-jena.de with the project name or identifier and a short description of the issue.
