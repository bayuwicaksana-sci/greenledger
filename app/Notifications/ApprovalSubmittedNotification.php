<?php

namespace App\Notifications;

use App\Models\ApprovalInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ApprovalSubmittedNotification extends Notification implements ShouldQueue
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
            ->subject('New Approval Request — '.config('app.name'))
            ->view('emails.approval-submitted', [
                'itemName' => $itemName,
                'stepName' => $stepName,
                'submitterName' => $this->instance->submittedBy?->name ?? 'Unknown',
                'reviewUrl' => $this->getReviewUrl(),
                'statusLabel' => 'Action Required',
                'statusBg' => '#eff6ff',
                'statusBadgeBg' => '#dbeafe',
                'statusBadgeText' => '#1e40af',
                'statusBadgeBorder' => '#bfdbfe',
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "New approval request: \"{$this->getItemName()}\" requires your review at step \"{$this->instance->currentStep?->name}\".",
            'url' => $this->getReviewUrl(),
            'type' => 'approval_submitted',
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
