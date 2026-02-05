<x-mail::message>
    # Approval Moved to Your Step

    Hello,

    An approval request has advanced to your step and requires your review.

    **Details:**
    - Item: {{ $itemName }}
    - Your Step: {{ $stepName }}

    <x-mail::button :url="$reviewUrl">
        Review Request
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
