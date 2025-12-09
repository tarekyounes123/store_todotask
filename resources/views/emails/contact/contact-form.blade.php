@component('mail::message')
# Contact Form Submission

You have received a new contact form submission from {{ $data['name'] }}.

**Details:**
- **Name:** {{ $data['name'] }}
- **Email:** {{ $data['email'] }}
- **Subject:** {{ $data['subject'] }}
- **Message:**
{{ $data['message'] }}

@component('mail::button', ['url' => config('app.url')])
View in Admin Panel
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent