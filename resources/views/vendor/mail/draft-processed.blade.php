@component('mail::message')

We processed your submission. Check your dashboard for more details.
Please get in touch if you need any further support.

You can click the following link to go to your project and provide more meta data.
Once you have submitted all the recommended community standards you can make your submission public.

@component('mail::button', ['url' =>  $url, 'color' => 'green'])
Edit Project
@endcomponent

@endcomponent

