<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model Display Name Overrides
    |--------------------------------------------------------------------------
    |
    | Override the auto-generated display names for specific models.
    | Models that use the RequiresApproval trait are automatically discovered.
    | Only add entries here if you want to customize the display name.
    |
    | Auto-generated names convert class names to Title Case:
    | - PaymentRequest → "Payment Request" (auto)
    | - Settlement → "Settlement" (auto)
    |
    */

    'model_display_names' => [
        // Example: Override auto-generated name
        // \App\Models\PaymentRequest::class => 'Payment Requisition',
    ],

    /*
    |--------------------------------------------------------------------------
    | Step Purposes
    |--------------------------------------------------------------------------
    |
    | Define the available step purposes:
    | - 'approval': Requires approval from designated approvers (can be auto-skipped)
    | - 'action': Always executes regardless of who the requester is
    |
    */

    'step_purposes' => [
        'approval' => 'Approval (can be auto-skipped if requester is approver)',
        'action' => 'Action (always executes regardless of requester)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Skip Behavior
    |--------------------------------------------------------------------------
    |
    | When enabled, approval steps will be automatically skipped if the
    | requester is in the list of approvers for that step.
    |
    */

    'auto_skip_self_approval' => true,
];
