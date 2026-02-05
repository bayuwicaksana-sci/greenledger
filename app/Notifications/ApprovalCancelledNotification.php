<?php

namespace App\Notifications;

use App\Models\ApprovalInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ApprovalCancelledNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Request Cancelled — ' . config('app.name'))
            ->markdown('emails.approval-cancelled', [
                'itemName' => $this->getItemName(),
                'reviewUrl' => $this->getReviewUrl(),
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "The approval request \"{$this->getItemName()}\" has been cancelled.",
            'url' => $this->getReviewUrl(),
            'type' => 'approval_cancelled',
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
