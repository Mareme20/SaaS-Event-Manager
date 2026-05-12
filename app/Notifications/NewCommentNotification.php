<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $task = $this->comment->commentable;
        return (new MailMessage)
                    ->subject("Nouveau commentaire sur : {$task->title}")
                    ->line("{$this->comment->user->name} a laissé un commentaire sur la tâche \"{$task->title}\".")
                    ->line("\"{$this->comment->content}\"")
                    ->action('Répondre', route('tasks.index', $task->event_id))
                    ->line('Merci d\'utiliser notre application !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $task = $this->comment->commentable;
        return [
            'comment_id' => $this->comment->id,
            'task_id' => $task->id,
            'message' => "{$this->comment->user->name} a commenté la tâche \"{$task->title}\".",
            'action_url' => route('tasks.index', $task->event_id),
        ];
    }
}
