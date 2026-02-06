<?php

namespace App\Notifications;

use App\Models\FiscalYear;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FiscalYearClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public FiscalYear $fiscalYear) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Fiscal Year {$this->fiscalYear->year} Closed")
            ->greeting("Hello {$notifiable->name},")
            ->line("Fiscal year {$this->fiscalYear->year} has been officially closed.")
            ->line("Period: {$this->fiscalYear->start_date->format('M d, Y')} - {$this->fiscalYear->end_date->format('M d, Y')}")
            ->line('No new programs or transactions can be created for this fiscal year.')
            ->action('View Fiscal Year Details', route('admin.fiscal-years.show', $this->fiscalYear))
            ->line('If you need to make changes to this fiscal year, please contact your administrator.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'fiscal_year_id' => $this->fiscalYear->id,
            'fiscal_year' => $this->fiscalYear->year,
            'message' => "Fiscal year {$this->fiscalYear->year} has been closed.",
            'period' => "{$this->fiscalYear->start_date->format('M d, Y')} - {$this->fiscalYear->end_date->format('M d, Y')}",
        ];
    }
}
