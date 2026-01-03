<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequired extends Notification implements ShouldQueue
{
    use Queueable;

    public $documentType;
    public $documentId;
    public $prNumber;
    public $projectTitle;
    public $sequence;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($documentType, $documentId, $prNumber, $projectTitle, $sequence, $url)
    {
        $this->documentType = $documentType;
        $this->documentId = $documentId;
        $this->prNumber = $prNumber;
        $this->projectTitle = $projectTitle;
        $this->sequence = $sequence;
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
            ->subject("Approval Required: {$this->prNumber}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have a pending approval for {$documentName} {$this->prNumber}.")
            ->line("Project: {$this->projectTitle}")
            ->line("Your approval is sequence #{$this->sequence} in the approval chain.")
            ->action('Review and Approve', $this->url)
            ->line('Please review and take action at your earliest convenience.')
            ->line('Thank you for your attention.');
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
            'sequence' => $this->sequence,
            'url' => $this->url,
            'message' => "Approval required for {$this->prNumber}: {$this->projectTitle}",
        ];
    }
}
