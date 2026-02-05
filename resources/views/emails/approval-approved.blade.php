<x-mail::message>
    # Request Approved

    Hello,

    Your request has been approved successfully.

    **Details:**
    - Item: {{ $itemName }}

    <x-mail::button :url="$reviewUrl">
        View Details
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
