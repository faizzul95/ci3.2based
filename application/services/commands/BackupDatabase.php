<?php

namespace App\services\commands;

class BackupDatabase
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set scheduled to backup database';
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle($scheduler): void
    {
        $scheduler->call(function () {
            log_message('info', $this->description . ' is running');
            app('App\services\BackupSystem')->backup_database();
        })
            ->onlyOne(FCPATH . "application" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "scheduler")
            ->before(function () {
                log_message('error', 'started at : ' . timestamp());
            })
            ->then(function () {
                log_message('error', 'completed at : ' . timestamp());
            })->everyMinute();
    }
}
