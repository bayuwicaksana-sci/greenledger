<x-mail::message>
    # Request Cancelled

    Hello,

    The following approval request has been cancelled.

    **Details:**
    - Item: {{ $itemName }}

    <x-mail::button :url="$reviewUrl">
        View Details
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
