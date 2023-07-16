<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class SystemQueueJob_model extends CT_Model
{
	public $table = 'system_queue_job';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'uuid',
		'type',
		'payload',
		'attempt',
		'status',
		'message',
		'company_id',
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	public function __construct()
	{
		// returns the result: as 'array' or as 'object'. the default value is 'object'
		$this->return_as = 'array';

		parent::__construct();
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################

	public function getJob()
	{
		return $this->db->where_in('status', [1, 2])->get($this->table)->row_array();
	}

	public function getEmailQueueListDt($status = NULL, $dateSearch = NULL)
	{
		date_default_timezone_set("Asia/Kuala_Lumpur");

		$statusQuery = hasData($status) ? "AND `job`.`status` = " . escape($status) : NULL;
		$searchQuery = "AND `job`.`created_at` BETWEEN " . escape($dateSearch . ' 00:00:00') . " AND " . escape($dateSearch . ' 23:59:59') . $statusQuery;
		$superAdminAccess = currentUserProfileID() != 1 ? 'AND `company_id`=' . currentCompanyID() : NULL;

		$serverside = serversideDT();
		$serverside->query("SELECT 
        `job`.`uuid`,
        `job`.`payload`,
        `job`.`attempt`,
        `job`.`status`,
        `job`.`message`,
        `job`.`company_id`,
        `job`.`created_at`,
        `job`.`updated_at`,
        `job`.`id`
        FROM {$this->table} `job`
		WHERE `job`.`type` = 'email' $searchQuery $superAdminAccess
        ORDER BY {$this->primary_key} {$this->order}");

		$serverside->hide('uuid'); // hides column from the output
		$serverside->hide('message'); // hides column from the output
		$serverside->hide('company_id'); // hides column from the output
		$serverside->hide('created_at'); // hides column from the output
		$serverside->hide('updated_at'); // hides column from the output

		$serverside->edit('payload', function ($data) {
			$dataDecode = json_decode($data['payload'], true);

			if (hasData($dataDecode)) {
				$uuid = purify($data['uuid']);
				$name = hasData($dataDecode['name']) ? purify($dataDecode['name']) : '<small> - </small>';
				$to = purify($dataDecode['to']);
				$subject = purify($dataDecode['subject']);
				$cc = hasData($dataDecode['cc']) ? $dataDecode['cc'] : '<small><i> (not set) </i></small>';
				$bcc = hasData($dataDecode['bcc']) ? $dataDecode['bcc'] : '<small><i> (not set) </i></small>';

				return "<ul>
                        <li> <b> UUID </b> : <small> " . $uuid . " </small> </li>
                        <li> <b> Name </b> : <small> " . $name . " </small> </li>
                        <li> <b> To </b> : <small> " . $to . "  </small> </li>
                        <li> <b> Subject </b> : " . $subject . " </li>
                        <li> <b> CC </b> : <small> " . $cc . " </small> </li>
                        <li> <b> BCC </b> : <small> " . $bcc . " </small> </li>
                     </ul>";
			} else {
				return 'No payload detected';
			}
		});

		$serverside->edit('attempt', function ($data) {
			$attempt = purify($data['attempt']);
			$create = formatDate($data['created_at'], "d.m.Y h:i A");
			$update = hasData($data['updated_at']) ? formatDate($data['updated_at'], "d.m.Y h:i A") : '<small> - </small>';
			$message = hasData($data['message']) ? purify($data['message']) : '<small> - </small>';

			return "<ul>
						<li> <b> Count </b> : <small> " . $attempt . " </small> </li>
						<li> <b> Created At </b> : <small> " . $create . " </small> </li>
						<li> <b> Last update </b> : <small> " . $update . " </small> </li>
						<li> <b> Message </b> : <small> " . $message . "  </small> </li>
					</ul>";
		});

		$serverside->edit('status', function ($data) {
			$badge = [
				'1' => '<span class="badge badge-label bg-warning"> Pending </span>',
				'2' => '<span class="badge badge-label bg-info"> Running </span>',
				'3' => '<span class="badge badge-label bg-success"> Completed </span>',
				'4' => '<span class="badge badge-label bg-danger"> Failed </span>',
			];

			return $badge[$data['status']];
		});

		$serverside->edit('id', function ($data) {
			$preview = '<button class="btn btn-outline-primary btn-sm waves-effect" onclick="previewEmail(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Preview"> <i class="tf-icons ti ti-eye ti-xs"></i> </button>';
			$del = '<button class="btn btn-outline-danger btn-sm waves-effect" onclick="deleteRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Delete"> <i class="tf-icons ti ti-trash ti-xs"></i> </button>';
			return "<center> $del $preview </center>";
		});

		echo $serverside->generate();
	}
}
