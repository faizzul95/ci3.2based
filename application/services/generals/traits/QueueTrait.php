<?php

namespace App\services\generals\traits;

use App\services\generals\traits\EmailTrait;
use App\services\modules\core\systemQueueJob\logics\SystemQueueJobDeleteLogic;
use App\services\modules\core\systemQueueFailedJob\processors\SystemQueueFailedJobStoreProcessors;
use App\services\modules\core\systemQueueFailedJob\processors\SystemQueueFailedJobSearchProcessors;
use App\services\modules\core\systemQueueFailedJob\logics\SystemQueueFailedJobDeleteLogic;
use App\services\modules\core\systemQueueJob\processors\SystemQueueJobSearchProcessors;
use App\services\modules\core\systemQueueJob\processors\SystemQueueJobStoreProcessors;

trait QueueTrait
{
	use EmailTrait;

	/**
	 * Add a new job to database table
	 *
	 * @return array
	 */
	public function addQueue($dataQueue, $type = 'email', $securityXss = false)
	{
		$defaultData = [
			'uuid' => uuid(),
			'type' => $type,
		];

		return app(new SystemQueueJobStoreProcessors)->execute(array_merge($defaultData, $dataQueue), $securityXss);
	}

	/**
	 * update a job to database table
	 *
	 * @return array
	 */
	public function updateQueue($dataQueue, $securityXss = false)
	{
		// initialize model.
		return app(new SystemQueueJobStoreProcessors)->execute($dataQueue, $securityXss);
	}

	/**
	 * Get a undo job from database table
	 *
	 * @return array
	 */
	public function getQueue()
	{
		return app(new SystemQueueJobSearchProcessors)->execute(
			[
				'conditions' => [
					'status' => ['IN', [1, 2, 4]],
				]
			],
			'get'
		);
	}

	/**
	 * Process then update a job
	 *
	 * @param array $job
	 * @return boolean
	 */
	public function processQueue($job)
	{
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
			$addFailed = app(new SystemQueueFailedJobStoreProcessors)->execute($job, false);

			if (isSuccess($addFailed['code'])) {
				app(new SystemQueueJobDeleteLogic)->logic(['id' => $jobID]);
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
		$getRequeueData = app(new SystemQueueFailedJobSearchProcessors)->execute();

		if (hasData($getRequeueData)) {
			foreach ($getRequeueData as $queue) {

				$id = $queue['id'];
				$uuid = $queue['uuid'];

				unset($queue['id']);

				$dataSave = app(new SystemQueueJobStoreProcessors)->execute($queue, false);

				if (isSuccess($dataSave['code'])) {
					app(new SystemQueueFailedJobDeleteLogic)->logic(['id' => $id]);
					output('success', "{$uuid} has successfully re-queue");
				} else {
					output('error', "{$uuid} failed to re-queue");
				}
			}

			echo "\n";
		} else {
			output('info', "No failed jobs found");
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
		$getRequeueData = ci()->failedM::find($uuid, 'uuid');

		if (hasData($getRequeueData)) {
			$id = $getRequeueData['id'];

			unset($getRequeueData['id']);
			$dataSave = app(new SystemQueueJobStoreProcessors)->execute($getRequeueData, false);

			if (isSuccess($dataSave['code'])) {
				app(new SystemQueueFailedJobDeleteLogic)->logic(['id' => $id]);
				output('success', "{$uuid} has successfully re-queue");
			} else {
				output('error', "{$uuid} failed to re-queue");
			}
			echo "\n";
		} else {
			output('warning', "No UUID ({$uuid}) found on failed jobs.");
		}
	}
}
