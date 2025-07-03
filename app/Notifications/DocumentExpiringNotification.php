<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\Workflow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class DocumentExpiringNotification extends Notification
{
    use Queueable;

    protected $employee;
    protected $workflow;
    protected $documents;
    protected $daysUntilExpiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(Employee $employee, Workflow $workflow, Collection $documents, int $daysUntilExpiry)
    {
        $this->employee = $employee;
        $this->workflow = $workflow;
        $this->documents = $documents;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }

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
        $mailMessage = (new MailMessage)
            ->subject("Document Expiry Alert: {$this->employee->full_name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is to inform you that the following documents for {$this->employee->full_name} will expire in {$this->daysUntilExpiry} days:")
            ->line("");
        
        foreach ($this->documents as $document) {
            $mailMessage->line("• {$document->documentType->name} - Expires on {$document->expiry_date->format('F j, Y')}");
        }
        
        $mailMessage->line("")
            ->line("These documents are required for the '{$this->workflow->name}' workflow.")
            ->action('View Employee', url("/employees/{$this->employee->id}"))
            ->line("Please ensure these documents are renewed before they expire to maintain compliance.");
        
        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->full_name,
            'workflow_id' => $this->workflow->id,
            'workflow_name' => $this->workflow->name,
            'documents' => $this->documents->map(function($document) {
                return [
                    'id' => $document->id,
                    'type' => $document->documentType->name,
                    'expiry_date' => $document->expiry_date->format('Y-m-d'),
                ];
            }),
            'days_until_expiry' => $this->daysUntilExpiry,
        ];
    }
}