<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

use App\services\generals\constants\GeneralStatus;
use App\services\generals\constants\MasterGroupRoles;

use App\services\modules\core\files\processors\FileSearchProcessors;
use App\services\modules\core\users\submodules\profiles\processors\ProfileSearchProcessors;

class User_model extends CT_Model
{
	public $table = 'users';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'name',
		'user_preferred_name',
		'user_staff_no',
		'user_nric_visa',
		'email',
		'user_contact_no',
		'user_gender',
		'user_dob',
		'username',
		'password',
		'user_marital_status',
		'user_status',
		'social_id',
		'social_type',
		'two_factor_status',
		'two_factor_type',
		'two_factor_secret',
		'two_factor_recovery_codes',
		'remember_token',
		'joined_date',
		'resigned_date',
		'resigned_reason',
		'email_verified_at',
		'company_id',
		'impersonator_id'
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	// relationship ONE TO ONE
	public $has_one = [
		'company' => ['Company_model', 'id', 'company_id'],
		'main_profile' => ['UserProfile_model', 'user_id', 'id'],
		'current_profile' => ['UserProfile_model', 'user_id', 'id'],
	];

	// relationship ONE TO MANY
	public $has_many = [
		'profile' => ['UserProfile_model', 'user_id', 'id']
	];

	public function __construct()
	{
		// returns the result: as 'array' or as 'object'. the default value is 'object'
		$this->return_as = 'array';

		parent::__construct();
		$this->abilities = permission([]);
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################

	public function getUserListDt($filterStatus = NULL, $filterRoleID = NULL, $filterCompanyID = NULL)
	{
		$companyFilterQuery = NULL;
		$rolesFilterQuery = NULL;

		// if current user is superadmin
		if (isSuperadmin()) {
			$rolesFilterQuery = hasData($filterRoleID) ? "`roles`.`role_group` = " . escape($filterRoleID) : NULL;
			$companyFilterQuery = hasData($filterCompanyID) ? "`user`.`company_id` = " . escape($filterCompanyID) : NULL;
		} else {
			$companyFilterQuery = "`user`.`company_id` = " . currentCompanyID();
			$rolesFilterQuery = hasData($filterRoleID) ? "`roles`.`role_group` != 1 AND `roles`.`role_group` = " . escape($filterRoleID) : "`roles`.`role_group` != 1"; // exclude superadmin group
		}

		$statusFilterQuery = hasData($filterStatus) || $filterStatus === '0' ? "`user`.`user_status` = " . escape($filterStatus) : '`user`.`user_status` != 3'; // dont show deleted data

		$filterQuery = "WHERE {$statusFilterQuery}";

		if (hasData($filterRoleID)) {
			$filterQuery .= " AND {$rolesFilterQuery}";
		}

		if (hasData($filterCompanyID)) {
			$filterQuery .= " AND {$companyFilterQuery}";
		}

		$serverside = serversideDT();
		$serverside->query("SELECT DISTINCT
		`user`.`two_factor_status`, 
        `user`.`name`, 
        `user`.`user_staff_no`, 
        `user`.`email`, 
        `user`.`user_contact_no`, 
        `user`.`user_gender`, 
        `user`.`user_dob`, 
        `user`.`user_status`, 
        `user`.`two_factor_type`, 
        `user`.`id` 
        FROM 
        {$this->table} `user`
		LEFT JOIN `users_profile` `profile` ON `user`.`id`=`profile`.`user_id`
		LEFT JOIN `company_profile_roles` `roles` ON `profile`.`roles_id`=`roles`.`id`
        $filterQuery
        ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output
		$serverside->hide('user_contact_no'); // hides column from the output
		$serverside->hide('user_gender'); // hides column from the output
		$serverside->hide('user_dob'); // hides column from the output
		$serverside->hide('two_factor_type'); // hides column from the output

		// Dummy : show avatar / user profile image
		$serverside->edit('two_factor_status', function ($data) {
			$main_profile = app(new ProfileSearchProcessors)->execute(
				[
					'conditions' => [
						'is_main' => 1,
						'user_id' => $data[$this->primary_key]
					]
				],
				'get'
			);

			if (hasData($main_profile)) {
				$avatar = app(new FileSearchProcessors)->execute(
					[
						'conditions' => [
							'entity_id' => $main_profile['id'],
							'entity_file_type' => 'PROFILE_PHOTO'
						]
					],
					'get'
				);

				$pathFiles = hasData($avatar) ? fileImage($avatar, 'user') : defaultImage('user');
			} else {
				$pathFiles = defaultImage('user'); // use default image
			}
			return '<center><img src="' . $pathFiles . '" alt="avatar" class="avatar-sm rounded-circle img-fluid"></center>';
		});

		$serverside->edit('name', function ($data) {
			$name = (hasData($data['name'])) ? $data['name'] : '-';
			$email = (hasData($data['email'])) ? $data['email'] : '-';

			$contactNo = (hasData($data['user_contact_no'])) ? $data['user_contact_no'] : '<i>(not set)</i>';

			return "<ul>
                        <li> <b> Name </b> : <small> " . purify($name) . " </small> </li>
                        <li> <b> Email </b> : <small> " . purify($email) . " </small> </li>
                        <li> <b> Contact No </b> : <small> " . purify($contactNo) . " </small> </li>
                     </ul>";
		});

		// Dummy : show staff no, user position & department name
		$serverside->edit('user_staff_no', function ($data) {

			$roles = app(new ProfileSearchProcessors)->execute(
				[
					'fields' => 'id, roles_id, is_main',
					'conditions' => [
						'user_id' => $data[$this->primary_key],
						'is_main' => 1
					],
					'with' => [
						'roles' => ['fields' => 'role_name, role_code, role_group'],
						'department' => ['fields' => 'department_name'],
					]
				],
				'get'
			);

			$staffNo = (hasData($data['user_staff_no'])) ? $data['user_staff_no'] : '-';

			return "<ul>
						<li> <b> Staff ID </b> : <small> " . purify($staffNo) . " </small> </li>
                        <li> <b> Position </b> : <small> " . purify($roles['roles']['role_name']) . " </small> </li>
                        <li> <b> Department </b> : <small> " . purify($roles['department']['department_name']) . " </small> </li>
                     </ul>";
		});

		// Dummy : show group roles
		$serverside->edit('email', function ($data) {
			$dataProfile = app(new ProfileSearchProcessors)->execute(
				[
					'fields' => 'id, roles_id, is_main',
					'conditions' => [
						'user_id' => $data[$this->primary_key]
					],
					'with' => ['roles' => ['fields' => 'role_name, role_code, role_group']]
				]
			);

			$profileList = '';

			if ($dataProfile) {
				if (hasData($dataProfile)) {
					$profileArr = [];
					foreach ($dataProfile as $profile) {
						$mainProfile = ($profile['is_main'] == 1) ? ' &nbsp; <i class="fa fa-user-circle" style="color:orange" title="Main profile"></i>' : '';
						array_push($profileArr, '<small>' . MasterGroupRoles::LIST[$profile['roles']['role_group']]['badge'] . $mainProfile . '</small>');
					}
					$profileList = '<ul><li>' . implode('</li><li>', $profileArr) . '</li></ul>';
				}
			}

			return $profileList;
		});

		$serverside->edit('user_status', function ($data) {
			return '<center>' . GeneralStatus::BADGE[$data['user_status']] . '</center>';
		});

		$serverside->edit('id', function ($data) {
			$del = $edit = $impersonate = '';

			// if ($this->abilities[''])
			$del = '<button class="btn btn-outline-danger btn-sm waves-effect" onclick="deleteRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Delete"> <i class="ri-delete-bin-5-line"></i> </button>';

			// if ($this->abilities[''])
			$edit = '<button class="btn btn-outline-info btn-sm waves-effect" onclick="updateRecord(' . $data[$this->primary_key] . ')" title="Update"><i class="ri-edit-box-line"></i> </button>';

			// if ($this->abilities[''])
			$impersonate = isSuperadmin() && currentUserID() != $data[$this->primary_key] ? '<button class="btn btn-outline-success btn-sm waves-effect" onclick="impersonateUser(' . $data[$this->primary_key] . ', \'' . $data['name'] . '\')" title="Impersonate as this user"><i class="ri-user-shared-2-line"></i> </button>' : '';

			return "<center> $del $edit $impersonate </center>";
		});

		echo $serverside->generate();
	}
}
