@component('mail::message')

Dear nmrXiv user,

{{ __('Your project - :project was deleted on :deletedOn.', ['project' => $projectName, 'deletedOn' => $deletedOn]) }}

You may recover deleted projects and the data stored in them for a limited time before they are permanently deleted.
{{ __("If you'd like to recover your project, you must cancel the project's permanent deletion before :dueDate", ['dueDate' => $dueDate]) }}


To recover your deleted project:
Visit the thrash page (click the button below).
Select the project you want to recover, and click Project settings > Restore.
In the confirmation dialogue, click Restore.

{{ __('If you take no action by :dueDate, you will be unable to recover your project.', ['dueDate' => $dueDate]) }}

For further questions, please visit our documentation site or contact us at info@nmrxiv.org.

Regards,
The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
Restore Project
@endcomponent

@endcomponent