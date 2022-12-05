@component('mail::message')

Dear nmrXiv user,

{{ __('Your project - **:project** has been deleted successfully on **:deletedOn**.', ['project' => $projectName, 'deletedOn' => $deletedOn]) }}

You may recover deleted projects and the data stored in them for a limited time before they are permanently deleted.

{{ __("If you'd like to recover your project, you must cancel the project's permanent deletion before **:dueDate**", ['dueDate' => $dueDate]) }}

{{ __('Please note, you will be unable to recover your project, if you donot take any action before the due data.', ['dueDate' => $dueDate]) }}

To recover your deleted project:

Go to thrash section,
Select the project you want to recover, and click Project settings > Restore.

Follow our [documentation](https://docs.nmrxiv.org/) to learn more.

If you do not recognise this action contact us at info@nmrxiv.org.

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
Restore Project
@endcomponent

@endcomponent