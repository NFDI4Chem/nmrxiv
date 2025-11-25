@component('mail::message')

Dear nmrXiv user,

@if($daysUntilRelease === 7)
{{ __('Your nmrXiv embargo project - **:project** is scheduled to be automatically released in **1 week** on **:releaseDate**.', ['project' => $projectName, 'releaseDate' => $releaseDate]) }}
@elseif($daysUntilRelease === 3)
{{ __('Your nmrXiv embargo project - **:project** is scheduled to be automatically released in **3 days** on **:releaseDate**.', ['project' => $projectName, 'releaseDate' => $releaseDate]) }}
@elseif($daysUntilRelease === 1)
{{ __('Your nmrXiv embargo project - **:project** is scheduled to be automatically released **tomorrow** on **:releaseDate**.', ['project' => $projectName, 'releaseDate' => $releaseDate]) }}
@else
{{ __('Your nmrXiv embargo project - **:project** is scheduled to be automatically released on **:releaseDate**.', ['project' => $projectName, 'releaseDate' => $releaseDate]) }}
@endif

**What happens when your project is released:**
- Your project will become publicly accessible
- All project data and files will be visible to the public

**If you need to extend the embargo period:**
- You can modify the release date from your project settings
- Contact our support team if you need assistance

**If you want to publish immediately:**
- You can manually publish your project anytime before the automatic release date

Please review your project and ensure all information is ready for public release.

Follow our [documentation](https://docs.nmrxiv.org/) to learn more about embargo projects and release procedures.

If you do not recognise this action contact us at {{env('MAIL_FROM_ADDRESS')}}.

Regards,

The nmrXiv Team

@component('mail::button', ['url' =>  $url, 'color' => 'primary'])
View Project
@endcomponent

@endcomponent