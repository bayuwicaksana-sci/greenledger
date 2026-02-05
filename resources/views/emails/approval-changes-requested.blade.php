<x-mail::message>
    # Changes Requested

    Hello,

    Changes have been requested on your submission. Please review the feedback below and resubmit when ready.

    **Details:**
    - Item: {{ $itemName }}
    - Feedback: {{ $feedback }}

    <x-mail::button :url="$reviewUrl">
        View Details
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
