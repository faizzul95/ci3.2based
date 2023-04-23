<?php

// reff : https://github.com/peppeocchi/php-cron-scheduler

namespace App\services\general\processor;

class TaskScheduleProcessor
{
    public $CI;
    public function handler()
    {
        $this->CI = &get_instance();
        $this->CI->load->config('scheduler');
        $allNamespace = $this->CI->config->item('commands');

        if (hasData($allNamespace)) {
            $scheduler = cronScheduler();

            $scheduler->clearJobs(); // clear previous jobs
            foreach ($allNamespace as $namspaces) {
                app($namspaces)->handle($scheduler);
            }
            // Reset the scheduler after a previous run
            $scheduler->resetRun()->run(); // now we can run it again
        } else {
            echo "No task/command to execute\n\n";
        }
    }
}
