<?php

#[\AllowDynamicProperties]

class myjobs
{
	function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model("SystemQueueJob_model");
	}

	/**
	 * Get a undo job from database table
	 *
	 * @return array
	 */
	public function getJob()
	{
		return $this->CI->SystemQueueJob_model->getJob();
	}

	/**
	 * Process then update a job
	 *
	 * @param array $job
	 * @return boolean
	 */
	public function processJob($job)
	{
		$status = $job['status'];
		$attempt = $job['attempt'];
		$message = NULL;

		$data = json_decode($job['payload'], true);

		// Check if payload is not empty
		if (!empty($data)) {

			// update job record to running before process
			$this->CI->SystemQueueJob_model->updateQueueJob([
				'queue_id' => $job['queue_id'],
				'attempt' => $attempt,
				'status' => 2, // running
				'message' => $message,
			]);

			// Check if job is email
			if ($job['type'] == 'email') {

				$recipientData = [
					'recipient_name' => $data['name'],
					'recipient_email' => $data['to'],
					'recipient_cc' => $data['cc'],
					'recipient_bcc' => $data['bcc'],
				];

				$sentEmail = sentMail($recipientData, $data['subject'], $data['body'], $data['attachment']);

				if ($sentEmail['success']) {
					$status = 3; // completed
					$message = $sentEmail['message'];
				} else {
					$status = 4; // failed
					$message = 'Failed to sent at ' . timestamp('d/m/Y h:i A') . ', ' . $sentEmail['message'];
				}
			}
		} else {
			$status = 4; // failed
			$message = 'payload is empty';
		}

		// Update job record to complete / failed after finishing process
		return $this->CI->SystemQueueJob_model->updateQueueJob([
			'queue_id' => $job['queue_id'],
			'status' => $status,
			'attempt' => $attempt + 1,
			'message' => $message,
		]);
	}
}
