<?php

namespace App\services\commands;

class EmailSubscription
{
	/**
	 * The console command task name.
	 *
	 * @var string
	 */
	protected $taskName = 'Email Reminder Subscription';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set scheduled to sent Email Subscription';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle($scheduler): void
	{
		$scheduler->call(function () {
			print "[" . timestamp('d/m/Y h:i A') . "]: {$this->taskName} currently is running\n";
			app('App\services\modules\core\subscription\EmailReminderLogic')->execute();
		})
			->onlyOne()
			->before(function () {
				print "[" . timestamp('d/m/Y h:i A') . "]: Job '{$this->taskName}' Started\n";
			})
			->then(function () {
				print "[" . timestamp('d/m/Y h:i A') . "]: Job '{$this->taskName}' Finished\n\n";
			})->everyMinute();
	}
}
