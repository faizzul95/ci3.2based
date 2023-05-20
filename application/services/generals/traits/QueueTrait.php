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
            'queue_uuid' => uuid(),
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

        $status = $job['status'];
        $attempt = $job['attempt'];
        $message = NULL;

        $data = json_decode($job['payload'], true);

        // Check if payload is not empty
        if (hasData($data)) {

            // update job record to running before process
            $this->updateQueue([
                'id' => $job['id'],
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
            'id' => $job['id'],
            'status' => $status,
            'attempt' => $attempt + 1,
            'message' => $message,
        ]);
    }
}
