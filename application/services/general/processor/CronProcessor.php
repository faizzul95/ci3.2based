<?php

// reff : https://github.com/peppeocchi/php-cron-scheduler

namespace App\services\general\processor;

class CronProcessor
{
    public function execute($data = NULL)
    {
        $scheduler = cronScheduler();

        // $scheduler->clearJobs();

        // Schedule a task to run every minute
        // $scheduler->call(function () {
        //     echo 'Task executed every minute';
        // })->everyMinute()->onlyOne();

        $scheduler->call(function () {
            log_message('error', 'Task executed every minute');
        })
            ->before(function () {
                log_message('error', 'started at : ' . timestamp());
            })
            ->then(function () {
                log_message('error', 'completed at : ' . timestamp());
            })->everyMinute();

        // Reset the scheduler after a previous run
        $scheduler->run(); // now we can run it again

        return $scheduler;
    }
}
