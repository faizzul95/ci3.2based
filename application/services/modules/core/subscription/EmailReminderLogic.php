<?php

namespace App\services\modules\core\subscription;

class EmailReminderLogic
{
	public function execute()
	{
		$queryDataUser = app('App\services\modules\core\users\processors\UserSearchProcessors')->execute(
			['conditions' => ['user_status' => 1]]
		);

		if ($queryDataUser) {
			foreach ($queryDataUser as $data) {
				// check if email is empty
				if (hasData($data['email']))
					log_message('error', "Email sent to {$data['email']} at " . timestamp('d/m/Y h:i A'));
			}
		}
	}
}
