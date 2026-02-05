<x-mail::message>
    # New Approval Request

    Hello,

    **{{ $submitterName }}** has submitted a new request that requires your approval.

    **Details:**
    - Item: {{ $itemName }}
    - Current Step: {{ $stepName }}

    <x-mail::button :url="$reviewUrl">
        Review Request
    </x-mail::button>

    Please review at your earliest convenience.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
