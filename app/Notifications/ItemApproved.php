<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public $documentType;
    public $documentId;
    public $prNumber;
    public $projectTitle;
    public $approvedBy;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($documentType, $documentId, $prNumber, $projectTitle, $approvedBy, $url)
    {
        $this->documentType = $documentType;
        $this->documentId = $documentId;
        $this->prNumber = $prNumber;
        $this->projectTitle = $projectTitle;
        $this->approvedBy = $approvedBy;
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
            ->subject("Approved: {$this->prNumber}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The {$documentName} {$this->prNumber} has been approved by {$this->approvedBy}.")
            ->line("Project: {$this->projectTitle}")
            ->action('View Details', $this->url)
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
            'approved_by' => $this->approvedBy,
            'url' => $this->url,
            'message' => "{$this->prNumber} approved by {$this->approvedBy}",
        ];
    }
}
