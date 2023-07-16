<?php

namespace App\services\commands;

use App\core\Struck;

class BackupSystemDatabase
{
	/**
	 * The console command task name.
	 *
	 * @var string
	 */
	protected $taskName = 'Backup System & Database';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set scheduled to backup database & System';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle($scheduler): void
	{
		// BACKUP DATABASE (DAILY)
		$scheduler->call(function () {
			print "[" . timestamp('d/m/Y h:i A') . "]: {$this->taskName} currently is running\n";
			app('App\services\general\helper\BackupSystem')->backup_database();
		})
			->onlyOne()
			->before(function () {
				print "[" . timestamp('d/m/Y h:i A') . "]: Job '{$this->taskName}' Started\n";
				Struck::call('down'); // put system under maintenance before backup
			})
			->then(function () {
				print "[" . timestamp('d/m/Y h:i A') . "]: Job '{$this->taskName}' Finished\n";
				Struck::call('up'); // put system online after backup complete
			})->daily('00:15');


		// BACKUP FILE SYSTEM (WEEKLY ON SUNDAY)
		$scheduler->call(function () {
			print "[" . timestamp('d/m/Y h:i A') . "]: {$this->taskName} currently is running\n";
			app('App\services\general\helper\BackupSystem')->backup_folder();
		})
			->onlyOne()
			->before(function () {
				print "[" . timestamp('d/m/Y h:i A') . "]: Job '{$this->taskName}' Started\n";
				Struck::call('down'); // put system under maintenance before backup
			})
			->then(function () {
				print "[" . timestamp('d/m/Y h:i A') . "]: Job '{$this->taskName}' Finished\n";
				Struck::call('up'); // put system online after backup complete
			})->sunday();
	}
}
