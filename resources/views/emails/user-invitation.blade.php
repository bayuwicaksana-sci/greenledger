<x-mail::message>
# Welcome to GreenLedger

Hello {{ $invitation->full_name }},

You have been invited to join GreenLedger by {{ $invitation->invitedBy->name }}.

**Your Account Details:**
- Email: {{ $invitation->email }}
- Primary Site: {{ $invitation->primarySite->site_name }}

<x-mail::button :url="$acceptUrl">
Accept Invitation
</x-mail::button>

This invitation will expire on **{{ $expiresAt }}**.

If you have any questions, please contact your administrator.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
