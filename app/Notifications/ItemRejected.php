<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $documentType;
    public $documentId;
    public $prNumber;
    public $projectTitle;
    public $rejectedBy;
    public $reason;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($documentType, $documentId, $prNumber, $projectTitle, $rejectedBy, $reason, $url)
    {
        $this->documentType = $documentType;
        $this->documentId = $documentId;
        $this->prNumber = $prNumber;
        $this->projectTitle = $projectTitle;
        $this->rejectedBy = $rejectedBy;
        $this->reason = $reason;
        $this->url = $url;
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
        $documentName = $this->documentType === 'PR' ? 'Purchase Request' : 'BAC Document';
        
        return (new MailMessage)
            ->subject("Rejected: {$this->prNumber}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The {$documentName} {$this->prNumber} has been rejected by {$this->rejectedBy}.")
            ->line("Project: {$this->projectTitle}")
            ->line("Reason: {$this->reason}")
            ->action('View Details', $this->url)
            ->line('Please review and make necessary revisions.')
            ->line('Thank you.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'document_type' => $this->documentType,
            'document_id' => $this->documentId,
            'pr_number' => $this->prNumber,
            'project_title' => $this->projectTitle,
            'rejected_by' => $this->rejectedBy,
            'reason' => $this->reason,
            'url' => $this->url,
            'message' => "{$this->prNumber} rejected by {$this->rejectedBy}: {$this->reason}",
        ];
    }
}
