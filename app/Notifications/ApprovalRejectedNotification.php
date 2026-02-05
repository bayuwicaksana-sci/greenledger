<?php

namespace App\Notifications;

use App\Models\ApprovalInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ApprovalRejectedNotification extends Notification implements ShouldQueue
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
        $reason = $this->instance->actions()
            ->where('action_type', \App\Enums\ApprovalActionType::Reject)
            ->latest()
            ->value('comments') ?? 'No reason provided.';

        return (new MailMessage)
            ->subject('Request Rejected — ' . config('app.name'))
            ->markdown('emails.approval-rejected', [
                'itemName' => $this->getItemName(),
                'reason' => $reason,
                'reviewUrl' => $this->getReviewUrl(),
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "Your request \"{$this->getItemName()}\" has been rejected.",
            'url' => $this->getReviewUrl(),
            'type' => 'approval_rejected',
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
