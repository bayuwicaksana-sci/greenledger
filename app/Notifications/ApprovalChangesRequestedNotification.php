<?php

namespace App\Notifications;

use App\Models\ApprovalInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ApprovalChangesRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ApprovalInstance $instance,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $feedback = $this->instance->actions()
            ->where('action_type', \App\Enums\ApprovalActionType::RequestChanges)
            ->latest()
            ->value('comments') ?? 'No specific feedback provided.';

        return (new MailMessage)
            ->subject('Changes Requested — ' . config('app.name'))
            ->markdown('emails.approval-changes-requested', [
                'itemName' => $this->getItemName(),
                'feedback' => $feedback,
                'reviewUrl' => $this->getReviewUrl(),
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "Changes have been requested on \"{$this->getItemName()}\". Please review the feedback and resubmit.",
            'url' => $this->getReviewUrl(),
            'type' => 'approval_changes_requested',
        ];
    }

    protected function getItemName(): string
    {
        $approvable = $this->instance->approvable;

        return $approvable->account_name
            ?? $approvable->name
            ?? $approvable->title
            ?? "Item #{$approvable->id}";
    }

    protected function getReviewUrl(): string
    {
        return URL::signedRoute('approvals.show', [
            'site' => $this->instance->approvable->site?->site_code ?? 'default',
            'approvalInstance' => $this->instance->id,
        ]);
    }
}
