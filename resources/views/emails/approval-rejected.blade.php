<x-mail::message>
    # Request Rejected

    Hello,

    Your request has been rejected.

    **Details:**
    - Item: {{ $itemName }}
    - Reason: {{ $reason }}

    <x-mail::button :url="$reviewUrl">
        View Details
    </x-mail::button>

    If you have questions, please contact your supervisor.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
