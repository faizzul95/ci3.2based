<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class SystemLogger_model extends CT_Model
{
	public $table = 'system_logger';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'errno',
		'errtype',
		'errstr',
		'errfile',
		'errline',
		'user_agent',
		'ip_address',
		'time'
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

	public function getErrorListDt($dateSearch = NULL, $errorType = NULL)
	{
		$dateFrom = !empty($dateSearch) ? escape($dateSearch . ' 00:00:00') : '';
		$dateEnd = !empty($dateSearch) ? escape($dateSearch . ' 23:59:59') : '';

		$errorQuery = NULL;
		if (!empty($errorType)) {
			$errorQuery = !empty($dateSearch) ? " AND `errtype` = " . escape($errorType) : " WHERE `errtype` = " . escape($errorType);
		}

		$searchQuery = !empty($dateSearch) ? " WHERE `time` BETWEEN $dateFrom AND $dateEnd $errorQuery" : $errorQuery;

		$serverside = serversideDT();
		$serverside->query("SELECT 
		`errstr`, 
		`errfile`, 
		`errline`, `errtype`, `ip_address`, `user_agent`, `time`, `id` 
		FROM {$this->table} 
		{$searchQuery} 
		ORDER BY {$this->primary_key} {$this->order}");

		$serverside->hide('errline'); // hides column from the output
		$serverside->hide('errtype'); // hides column from the output
		$serverside->hide('time'); // hides column from the output
		$serverside->hide('ip_address'); // hides column from the output
		$serverside->hide('user_agent'); // hides column from the output

		$serverside->edit('errfile', function ($data) {

			if (in_array($data['errtype'], [
				'Error',
				'Warning',
				'Parsing Error',
				'Notice',
				'Core Error',
				'Core Warning',
				'Compile Error',
				'Compile Warning',
				'User Error',
				'User Warning',
				'User Notice',
				'Runtime Notice',
				'Catchable Notice',
			])) {
				$badge = [
					'Warning' => '<span class="badge badge-label bg-warning"> ' . $data['errtype'] . ' </span>',
					'Core Warning' => '<span class="badge badge-label bg-warning"> ' . $data['errtype'] . ' </span>',
					'Compile Warning' => '<span class="badge badge-label bg-warning"> ' . $data['errtype'] . ' </span>',
					'User Warning' => '<span class="badge badge-label bg-warning"> ' . $data['errtype'] . ' </span>',
					'Notice' => '<span class="badge badge-label bg-info"> ' . $data['errtype'] . ' </span>',
					'User Notice' => '<span class="badge badge-label bg-info"> ' . $data['errtype'] . ' </span>',
					'Runtime Notice' => '<span class="badge badge-label bg-info"> ' . $data['errtype'] . ' </span>',
					'Error' => '<span class="badge badge-label bg-danger"> ' . $data['errtype'] . ' </span>',
					'Parsing Error' => '<span class="badge badge-label bg-danger"> ' . $data['errtype'] . ' </span>',
					'Core Error' => '<span class="badge badge-label bg-danger"> ' . $data['errtype'] . ' </span>',
					'Compile Error' => '<span class="badge badge-label bg-danger"> ' . $data['errtype'] . ' </span>',
					'User Error' => '<span class="badge badge-label bg-danger"> ' . $data['errtype'] . ' </span>',
					'Catchable Error' => '<span class="badge badge-label bg-danger"> ' . $data['errtype'] . ' </span>',
				];
				$typeErr = $badge[$data['errtype']];
			} else {
				$typeErr = '<span class="badge badge-label bg-primary"> ' . $data['errtype'] . ' </span>';
			}

			return "<ul>
                        <li> <b> File </b> : <small> " . $data['errfile'] . " </small> </li>
                        <li> <b> Line </b> : <small> " . $data['errline'] . "  </small> </li>
                        <li> <b> Type </b> : " . $typeErr . " </li>
                        <li> <b> IP Address </b> : <small> " . $data['ip_address'] . " </small> </li>
                        <li> <b> User Agent </b> : <small> " . $data['user_agent'] . " </small> </li>
                        <li> <b> Timestamp </b> : <small> " . formatDate($data['time'], 'd.m.Y h:i A') . " </small> </li>
                     </ul>";
		});

		$serverside->edit('id', function ($data) {
			$del = '';
			$del = '<button class="btn btn-outline-danger btn-sm waves-effect" onclick="deleteRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Delete"> <i class="tf-icons ti ti-trash ti-xs"></i> </button>';
			return "<center> $del  </center>";
		});

		echo $serverside->generate();
	}

	public function truncateLogTable()
	{
		return delete($this->table);
	}

	public function deleteLogsByFilter($dateFrom = NULL, $dateTo = NULL, $errorType = NULL)
	{
		date_default_timezone_set("Asia/Kuala_Lumpur");

		if (!empty($dateFrom) && !empty($dateTo)) {
			$from = escape(min($dateFrom, $dateTo) . ' 00:00:00');
			$to = escape(max($dateFrom, $dateTo) . ' 23:59:59');
			return deleteWithCondition($this->table, [
				"time BETWEEN $from AND $to",
				"errtype" => $errorType,
			]);
		} else if (!empty($dateFrom) && empty($dateTo)) {
			$dateFrom = escape($dateFrom . ' 00:00:00');
			return deleteWithCondition($this->table, [
				"time >= $dateFrom",
				"errtype" => $errorType,
			]);
		} else if (empty($dateFrom) && !empty($dateTo)) {
			$dateTo = $dateTo . ' 23:59:59';
			return deleteWithCondition($this->table, [
				"time <= $dateTo",
				"errtype" => $errorType,
			]);
		} else if (empty($dateFrom) && empty($dateTo)) {
			$dateFrom = escape(date('Y-m-d 00:00:00'));
			$dateTo = escape(date('Y-m-d 23:59:59'));
			return deleteWithCondition($this->table, [
				"time BETWEEN $dateFrom AND $dateTo",
				"errtype" => $errorType,
			]);
		}
	}
}
