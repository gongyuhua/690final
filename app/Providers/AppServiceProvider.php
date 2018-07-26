<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobFailed;

use App\Notifications\JobFailedNotification;
use Laravel\Horizon\Horizon;
use Notification;
use Queue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Horizon::auth(function ($request) {
            // Always show admin if local development
            if (env('APP_ENV') == 'local') {
                return true;
            }
        });

        // Send notification to Slack when a job fails
        Queue::failing(function (JobFailed $event) {

            $eventData = [];
            $eventData['connectionName'] = $event->connectionName;
            $eventData['job'] = $event->job->resolveName();
            $eventData['queue'] = $event->job->getQueue();
            $eventData['exception'] = [];
            $eventData['exception']['message'] = $event->exception->getMessage();
            $eventData['exception']['file'] = $event->exception->getFile();
            $eventData['exception']['line'] = $event->exception->getLine();

            // Get some details about the failed job
            $job = unserialize($event->job->payload()['data']['command']);
            if (property_exists($job, 'order')) {
                $eventData['id'] = $job->order->id;
                $eventData['name'] = $job->order->name;
            }

            // Send slack notification of the failed job
            Notification::route('slack', env('SLACK_WEBHOOK'))->notify(new JobFailedNotification($eventData));

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
