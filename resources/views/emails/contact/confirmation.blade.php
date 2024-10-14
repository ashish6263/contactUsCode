{{-- <x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> --}}

@component('mail::message')
# Hello {{ $contact->name }},

Thank you for reaching out to us. Credentials shared are-

**Details:**
- **Name:** {{ $contact->name }}
- **Email:** {{ $contact->email }}
- **Phone:** {{ $contact->phone }}
- **Notes:** {{ $contact->notes }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent

