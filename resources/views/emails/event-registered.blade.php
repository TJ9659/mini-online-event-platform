@component('mail::message')
# Registration Confirmed!

You're all set for **{{ $event->title }}**.

### Your Meeting Link
Click the button below to join the event when it starts:

@component('mail::button', ['url' => $event->meeting_link])
Join Event
@endcomponent

If the button doesn't work, use this link:  
[{{ $event->meeting_link }}]({{ $event->meeting_link }})

Thanks,<br>
{{ config('app.name') }}
@endcomponent