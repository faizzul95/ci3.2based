<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class SystemAuditTrails_model extends CT_Model
{
	public $table = 'system_audit_trails';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'user_id',
		'role_id',
		'user_fullname',
		'event',
		'table_name',
		'created_at',
		'old_values',
		'new_values'
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

	public function getAuditTrailsListDt($dateSearch = NULL, $eventType = NULL)
	{
		date_default_timezone_set("Asia/Kuala_Lumpur");

		$eventQuery = NULL;
		if (!empty($eventType)) {
			$eventQuery = !empty($dateSearch) ? " AND `event` = " . escape($eventType) : " WHERE `event` = " . escape($eventType);
		}

		$searchQuery = !empty($dateSearch) ? " WHERE `audit`.`created_at` BETWEEN " . escape($dateSearch . ' 00:00:00') . " AND " . escape($dateSearch . ' 23:59:59') . " $eventQuery" : $eventQuery;

		$serverside = serversideDT();
		$serverside->query("SELECT 
        `audit`.`user_id`,
        `audit`.`role_id`,
        `roles`.`role_name`,
        `audit`.`user_fullname`, 
        `audit`.`event`, 
        `audit`.`table_name`, 
        `audit`.`created_at`, 
        `audit`.`old_values`, 
        `audit`.`new_values`, 
        `audit`.`id`
        FROM {$this->table} `audit`
        LEFT JOIN master_roles roles ON `audit`.role_id = `roles`.role_id
        $searchQuery
        ORDER BY {$this->primary_key} {$this->order}");

		$serverside->hide('user_id'); // hides column from the output
		$serverside->hide('role_id'); // hides column from the output
		$serverside->hide('role_name'); // hides column from the output
		$serverside->hide('table_name'); // hides column from the output
		$serverside->hide('old_values'); // hides column from the output
		$serverside->hide('new_values'); // hides column from the output

		$serverside->edit('user_fullname', function ($data) {
			return purify($data['user_fullname']) . '<br> <b> Profile </b> : <small> ' . purify($data['role_name']) . '</small>';
		});

		$serverside->edit('event', function ($data) {
			$badge = [
				'insert' => '<span class="badge bg-info"> ' . $data['event'] . ' </span>',
				'update' => '<span class="badge bg-success"> ' . $data['event'] . ' </span>',
				'delete' => '<span class="badge bg-danger"> ' . $data['event'] . ' </span>',
			];

			return "<ul>
                        <li> <b> Type </b> : <small> " . $badge[$data['event']] . " </small> </li>
                        <li> <b> Table </b> : <small> " . $data['table_name'] . "  </small> </li>
                    </ul>";
		});

		$serverside->edit('created_at', function ($data) {
			return formatDate($data['created_at'], 'd.m.Y h:i A');
		});

		$serverside->edit('id', function ($data) {
			$del = $view = '';
			$del = '<button class="btn btn-outline-danger btn-sm waves-effect" onclick="deleteRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Delete"> <i class="tf-icons ti ti-trash ti-xs"></i> </button>';
			$view = '<button class="btn btn-outline-success btn-sm waves-effect" onclick="viewRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="View"> <i class="tf-icons ti ti-eye ti-xs"></i> </button>';
			return "<center> $del $view </center>";
		});

		echo $serverside->generate();
	}
}
