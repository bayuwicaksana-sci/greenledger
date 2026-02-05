<?php

namespace App\Notifications;

use App\Models\ApprovalInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ApprovalStepAdvancedNotification extends Notification implements ShouldQueue
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
        $itemName = $this->getItemName();
        $stepName = $this->instance->currentStep?->name ?? 'Unknown Step';

        return (new MailMessage)
            ->subject('Approval Moved to Next Step — ' . config('app.name'))
            ->markdown('emails.approval-step-advanced', [
                'itemName' => $itemName,
                'stepName' => $stepName,
                'reviewUrl' => $this->getReviewUrl(),
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "Approval for \"{$this->getItemName()}\" has moved to step \"{$this->instance->currentStep?->name}\" and requires your review.",
            'url' => $this->getReviewUrl(),
            'type' => 'approval_step_advanced',
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
