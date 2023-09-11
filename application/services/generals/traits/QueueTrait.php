<?php

namespace App\services\generals\traits;

use App\services\generals\traits\EmailTrait;

trait QueueTrait
{
	use EmailTrait;

	/**
	 * Add a new job to database table
	 *
	 * @return array
	 */
	public function addQueue($dataQueue, $type = 'email')
	{
		// initialize model.
		model('SystemQueueJob_model', 'queueM');

		$defaultData = [
			'uuid' => uuid(),
			'type' => $type,
		];

		$saveData = array_merge($defaultData, $dataQueue);

		return ci()->queueM::save($saveData, false);
	}

	/**
	 * update a job to database table
	 *
	 * @return array
	 */
	public function updateQueue($dataQueue)
	{
		// initialize model.
		model('SystemQueueJob_model', 'queueM');
		return ci()->queueM::save($dataQueue, false);
	}

	/**
	 * Get a undo job from database table
	 *
	 * @return array
	 */
	public function getQueue()
	{
		// initialize model.
		model('SystemQueueJob_model', 'queueM');
		return ci()->queueM->getJob();
	}

	/**
	 * Process then update a job
	 *
	 * @param array $job
	 * @return boolean
	 */
	public function processQueue($job)
	{
		// initialize model.
		model('SystemQueueJob_model', 'queueM');
		model('SystemQueueFailedJob_model', 'failedM');

		$jobID = $job['id'];
		$attempt = $job['attempt'];
		$message = $job['message'];

		if ($attempt <= 3) {
			$status = $job['status'];
			$message = NULL;

			$data = json_decode($job['payload'], true);

			// Check if payload is not empty
			if (hasData($data)) {

				// update job record to running before process
				$this->updateQueue([
					'id' => $jobID,
					'attempt' => $attempt,
					'status' => 2, // running
					'message' => $message,
				]);

				$response = NULL;

				// Check if job is email
				if ($job['type'] == 'email') {
					$response = $this->queueMail($data);
				}

				if (!empty($response)) {
					$status = $response['status'];
					$message = $response['message'];
				}
			} else {
				$status = 4; // failed
				$message = 'payload is empty';
			}

			// Update job record to complete / failed after finishing process
			return $this->updateQueue([
				'id' => $jobID,
				'status' => $status,
				'attempt' => $attempt + 1,
				'message' => $message,
			]);
		} else {
			unset($job['id']); // remove id
			$job['exception'] = $message;
			$job['failed_at'] = timestamp();
			$addFailed = ci()->failedM::save($job, false);

			if (isSuccess($addFailed['code'])) {
				ci()->queueM::remove($jobID);
			}

			return $addFailed;
		}
	}

	/**
	 * Process the failed Queue by UUID
	 *
	 * @param array $job
	 * @return boolean
	 */
	public function processAllFailedQueue()
	{
		// initialize model.
		model('SystemQueueJob_model', 'queueM');
		model('SystemQueueFailedJob_model', 'failedM');

		$getRequeueData = ci()->failedM::all();

		if (hasData($getRequeueData)) {
			foreach ($getRequeueData as $queue) {

				$id = $queue['id'];
				$uuid = $queue['uuid'];

				unset($queue['id']);

				$dataSave = ci()->queueM::save($queue, false);

				if (isSuccess($dataSave['code'])) {
					ci()->failedM::remove($id);
					echo "{$uuid} has successfully re-queue.\n";
				} else {
					echo "{$uuid} failed to re-queue.\n";
				}
			}

			echo "\n";
		} else {
			echo "No failed jobs found.\n\n";
		}
	}

	/**
	 * Process the failed Queue by UUID
	 *
	 * @param array $job
	 * @return boolean
	 */
	public function processFailedQueueByUUID($uuid)
	{
		// initialize model.
		model('SystemQueueJob_model', 'queueM');
		model('SystemQueueFailedJob_model', 'failedM');

		$getRequeueData = ci()->failedM::find($uuid, 'uuid');

		if (hasData($getRequeueData)) {
			$id = $getRequeueData['id'];

			unset($getRequeueData['id']);
			$dataSave = ci()->queueM::save($getRequeueData, false);

			if (isSuccess($dataSave['code'])) {
				ci()->failedM::remove($id);
				echo "{$uuid} has successfully re-queue.\n";
			} else {
				echo "{$uuid} failed to re-queue.\n";
			}
			echo "\n";
		} else {
			echo "No UUID ({$uuid}) found on failed jobs.\n\n";
		}
	}
}
