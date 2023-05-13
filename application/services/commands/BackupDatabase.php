<?php

namespace App\services\commands;

class BackupDatabase
{
	/**
	 * The console command task name.
	 *
	 * @var string
	 */
	protected $taskName = 'Backup Database';

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
			print "[" . timestamp('d/m/Y h:i A') . "]: {$this->taskName} currently is running\n";
			app('App\services\BackupSystem')->backup_database();
		})
			->onlyOne(FCPATH . "application" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "scheduler")
			->before(function () {
				print "[" . timestamp('d/m/Y h:i A') . "]: Job {$this->taskName} Started\n";
			})
			->then(function () {
				print "[" . timestamp('d/m/Y h:i A') . "]: Job {$this->taskName} Finished\n\n";
			})->everyMinute();
	}
}
