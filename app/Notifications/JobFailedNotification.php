<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;

use Log;

class JobFailedNotification extends Notification
{
    private $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        Log::info('Sent slack notification for order #' . $this->event['id'] . ' for ' . $this->event['name']);

        return (new SlackMessage)
            ->from(MyWebapp)
                    ->to(env('SLACK_CHANNEL'))
        ->error()
        ->content('Queued job failed: ' . $this->event['job'])
        ->attachment(function ($attachment) {
            $attachment->title($this->event['exception']['message'])
                ->fields([
                    'ID' => $this->event['id'],
                    'Name' => $this->event['name'],
                    'File' => $this->event['exception']['file'],
                    'Line' => $this->event['exception']['line'],
                    'Server' => env('APP_ENV'),
                    'Queue' => $this->event['queue'],
                ]);
        });
    }
}