<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_master_email_templates extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'company_config_email_templates';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'email_type' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_subject' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_body' => ['type' => 'LONGTEXT', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'email_footer' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_cc' => ['type' => 'LONGTEXT', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'email_bcc' => ['type' => 'LONGTEXT', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'email_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => '1', 'comment' => '0-Inactive, 1-Active'],
			'company_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'refer table company'],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('id', TRUE);
		if ($this->dbforge->create_table($this->table_name, FALSE, ['ENGINE' => 'InnoDB', 'COLLATE' => 'utf8mb4_general_ci'])) {
			if (!isAjax()) {
				$this->seeder();
				echo "<p> <span style='color: blue;'><b> {$this->table_name} </b></span> successfully created! </p>";
			} else {
				echo "<b> {$this->table_name} </b></span> created";
			}
		}
	}

	public function down()
	{
		$this->dbforge->drop_table($this->table_name, TRUE);
		if (isAjax()) {
			echo "<b> {$this->table_name} </b></span> drop";
		}
	}

	public function seeder()
	{
		$data = [
			[
				'email_type'	=> 'SECURE_LOGIN',
				'email_subject'	=> env('APP_NAME') . ': Secure Login',
				'email_body'	=> 'Hi %name%,
									<br><br>
									Your account <b>%email%</b> was just used to sign in from <b>%browsers% on %os%</b>.
									<br><br>
									%details%
									<br><br>
									Don\'t recognise this activity?
									<br>
									Secure your account, from this link.
									<br>
									<a href="%url%"><b>Login.</b></a>
									<br><br>
									Why are we sending this?<br>We take security very seriously and we want to keep you in the loop on important actions in your account.
									<br><br>
									Sincerely,<br>
									' . env('APP_NAME'),
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'company_id'	=> 1,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			],
			[
				'email_type'	=> 'FORGOT_PASSWORD',
				'email_subject'	=> env('APP_NAME') . ': Forgot Password',
				'email_body'	=> '<table class="body-wrap" style="background-color: transparent; color: var(--vz-body-color); font-weight: var(--vz-body-font-weight); text-align: var(--vz-body-text-align); font-family: Roboto, sans-serif; font-size: 14px; width: 100%; margin: 0px;"><tbody><tr style="margin: 0px;"><td style="vertical-align: top; margin: 0px;" valign="top"></td><td class="container" width="600" style="vertical-align: top; margin-top: 0px; margin-bottom: 0px; display: block !important; max-width: 600px !important; clear: both !important;" valign="top"><div class="content" style="max-width: 600px; margin: 0px auto; padding: 20px;"><table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope="" itemtype="http://schema.org/ConfirmAction" style="border-radius: 3px; margin: 0px; border: none;"><tbody><tr style="margin: 0px;"><td class="content-wrap" style="color: rgb(73, 80, 87); vertical-align: top; margin: 0px; padding: 30px; box-shadow: rgba(30, 32, 37, 0.06) 0px 3px 15px; border-radius: 7px;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px;"><tbody><tr style="margin: 0px;"><td class="content-block" style="vertical-align: top; margin: 0px; padding: 0px 0px 20px;" valign="top"><div style="text-align: center;"><br></div></td></tr><tr style="margin: 0px;"><td class="content-block" style="font-size: 24px; vertical-align: top; margin: 0px; padding: 0px 0px 10px; text-align: center;" valign="top"><h4 style="font-family: Roboto, sans-serif; margin-bottom: 0px; line-height: 1.5;">Change or reset your password</h4></td></tr><tr style="margin: 0px;"><td class="content-block" style="color: rgb(135, 138, 153); font-size: 15px; vertical-align: top; margin: 0px; padding: 0px 0px 12px; text-align: center;" valign="top"><p style="margin-bottom: 13px; line-height: 1.5;"><span style="font-weight: var(--vz-body-font-weight);">Dear</span><b> %to%</b><span style="font-weight: var(--vz-body-font-weight);">,&nbsp;</span></p><p style="margin-bottom: 13px; line-height: 1.5;">You can reset&nbsp;your password by clicking the button below.&nbsp;</p></td></tr><tr style="margin: 0px;"><td class="content-block" itemprop="handler" itemscope="" itemtype="http://schema.org/HttpActionHandler" style="vertical-align: top; margin: 0px; padding: 0px 0px 22px; text-align: center;" valign="top"><a href="%url%" itemprop="url" style="font-size: 0.8125rem; color: rgb(255, 255, 255); cursor: pointer; display: inline-block; border-radius: 0.25rem; text-transform: capitalize; background-color: rgb(64, 81, 137); margin: 0px; border-color: rgb(64, 81, 137); border-style: solid; border-width: 1px; padding: 0.5rem 0.9rem;">Reset Password</a></td></tr></tbody></table></td></tr></tbody></table><div style="text-align: center; margin: 28px auto 0px auto;"><h4>Need Help ?</h4><p style="color: #878a99;">Please send any feedback or bug info to <a href="info@' . env('APP_DOMAIN') . '" target="_blank">info@' . env('APP_DOMAIN') . '</a></p><p style="color: rgb(152, 166, 173); margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">2023 ' . env('APP_NAME') . '. Developed by ' . env('COMPANY_NAME') . '</p></div></div></td></tr></tbody></table>',
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'company_id'	=> 1,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			],
			[
				'email_type'	=> 'TICKET_CREATED',
				'email_subject'	=> env('APP_NAME') . ': Ticket Received',
				'email_body'	=> '<p>Dear %to%,</p>
									<p> We would like to acknowledge that we have received your request and a ticket has been created. A support representative will be reviewing your request and will send you a personal response. (usually within %hours% hours). </p> 
									<p> To view the status of the ticket or add comments, please visit your client area at our website. You may also request urgent follow up via live chat agent at %url%. </p> 
									<p> Thank you for your patience.</p>
									Sincerely.<br>' . env('APP_NAME'),
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'company_id'	=> 1,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			],
			[
				'email_type'	=> 'TICKET_CLOSED',
				'email_subject'	=> env('APP_NAME') . ': Ticket Received',
				'email_body'	=> '<p>Dear %to%,</p>
									<p> Your ticket %subject% has been closed. </p> 
									<p> We hope that the ticket was resolved to your satisfaction. If you feel that the ticket should not be closed or if the ticket has not been resolved, please reply to this email. </p> 
									<p> You may also request urgent follow up via live chat agent at %url% if still required.</p>
									Sincerely.<br>' . env('APP_NAME'),
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'company_id'	=> 1,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			],
			[
				'email_type'	=> 'SECURE_LOGIN',
				'email_subject'	=> env('APP_NAME') . ': Secure Login',
				'email_body'	=> 'Hi %name%,
									<br><br>
									Your account <b>%email%</b> was just used to sign in from <b>%browsers% on %os%</b>.
									<br><br>
									%details%
									<br><br>
									Don\'t recognise this activity?
									<br>
									Secure your account, from this link.
									<br>
									<a href="%url%"><b>Login.</b></a>
									<br><br>
									Why are we sending this?<br>We take security very seriously and we want to keep you in the loop on important actions in your account.
									<br><br>
									Sincerely,<br>
									' . env('APP_NAME'),
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'company_id'	=> 2,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			],
			[
				'email_type'	=> 'SIGN_UP_CUSTOMER',
				'email_subject'	=> env('APP_NAME') . ': New Account Created',
				'email_body'	=> 'Hi %name%, 
									<br><br>
									Welcome! you have signed up with our ' . env('APP_NAME') . ' system with the following information: 
									<br><br> 
									<ul>
										<li> URL : ' . baseURL() . ' </li>
										<li> Username : %email% </li>
										<li> Password : %password% </li>
									</ul>
									<p>Alternatively you can login via Google / Facebook authentication if your email is the same as registered.</p>
									<p> Before you can login, you need to activate your account by clicking on this link : <a href="%url%"> Verify Account </a> <p>
									<p><span style="color: rgb(0, 0, 0);">Thank you.</span></p>
									Sincerely,<br>
									' . env('APP_NAME'),
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'company_id'	=> 2,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			],
			[
				'email_type'	=> 'FORGOT_PASSWORD',
				'email_subject'	=> env('APP_NAME') . ': Forgot Password',
				'email_body'	=> '<table class="body-wrap" style="background-color: transparent; color: var(--vz-body-color); font-weight: var(--vz-body-font-weight); text-align: var(--vz-body-text-align); font-family: Roboto, sans-serif; font-size: 14px; width: 100%; margin: 0px;"><tbody><tr style="margin: 0px;"><td style="vertical-align: top; margin: 0px;" valign="top"></td><td class="container" width="600" style="vertical-align: top; margin-top: 0px; margin-bottom: 0px; display: block !important; max-width: 600px !important; clear: both !important;" valign="top"><div class="content" style="max-width: 600px; margin: 0px auto; padding: 20px;"><table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope="" itemtype="http://schema.org/ConfirmAction" style="border-radius: 3px; margin: 0px; border: none;"><tbody><tr style="margin: 0px;"><td class="content-wrap" style="color: rgb(73, 80, 87); vertical-align: top; margin: 0px; padding: 30px; box-shadow: rgba(30, 32, 37, 0.06) 0px 3px 15px; border-radius: 7px;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px;"><tbody><tr style="margin: 0px;"><td class="content-block" style="vertical-align: top; margin: 0px; padding: 0px 0px 20px;" valign="top"><div style="text-align: center;"><br></div></td></tr><tr style="margin: 0px;"><td class="content-block" style="font-size: 24px; vertical-align: top; margin: 0px; padding: 0px 0px 10px; text-align: center;" valign="top"><h4 style="font-family: Roboto, sans-serif; margin-bottom: 0px; line-height: 1.5;">Change or reset your password</h4></td></tr><tr style="margin: 0px;"><td class="content-block" style="color: rgb(135, 138, 153); font-size: 15px; vertical-align: top; margin: 0px; padding: 0px 0px 12px; text-align: center;" valign="top"><p style="margin-bottom: 13px; line-height: 1.5;"><span style="font-weight: var(--vz-body-font-weight);">Dear</span><b> %to%</b><span style="font-weight: var(--vz-body-font-weight);">,&nbsp;</span></p><p style="margin-bottom: 13px; line-height: 1.5;">You can reset&nbsp;your password by clicking the button below.&nbsp;</p></td></tr><tr style="margin: 0px;"><td class="content-block" itemprop="handler" itemscope="" itemtype="http://schema.org/HttpActionHandler" style="vertical-align: top; margin: 0px; padding: 0px 0px 22px; text-align: center;" valign="top"><a href="%url%" itemprop="url" style="font-size: 0.8125rem; color: rgb(255, 255, 255); cursor: pointer; display: inline-block; border-radius: 0.25rem; text-transform: capitalize; background-color: rgb(64, 81, 137); margin: 0px; border-color: rgb(64, 81, 137); border-style: solid; border-width: 1px; padding: 0.5rem 0.9rem;">Reset Password</a></td></tr></tbody></table></td></tr></tbody></table><div style="text-align: center; margin: 28px auto 0px auto;"><h4>Need Help ?</h4><p style="color: #878a99;">Please send any feedback or bug info to <a href="info@' . env('APP_DOMAIN') . '" target="_blank">info@' . env('APP_DOMAIN') . '</a></p><p style="color: rgb(152, 166, 173); margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">2023 ' . env('APP_NAME') . '. Developed by ' . env('COMPANY_NAME') . '</p></div></div></td></tr></tbody></table>',
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'company_id'	=> 2,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			],
			[
				'email_type'	=> 'TWO_FACTOR_VERIFY',
				'email_subject'	=> env('APP_NAME') . ': Login Verification',
				'email_body'	=> '<p>Dear %to%,</p>
									<p> Your TAC for for login is <b> %code% </b></p>
									Don\'t recognise this activity?
									<br>
									Secure your account, from this link.
									<br>
									<a href="%url%"><b>Login.</b></a>
									<br><br>
									Why are we sending this?<br>We take security very seriously and we want to keep you in the loop on important actions in your account.
									<br><br>
									Sincerely,<br>
									' . env('APP_NAME'),
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'company_id'	=> 2,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			]
		];

		if (!empty($data))
			$this->db->insert_batch($this->table_name, $data);

		if (isAjax()) {
			echo "<b> {$this->table_name} </b></span> seed";
		}
	}

	public function truncate()
	{
		if (isAjax()) {
			ci()->db->truncate($this->table_name);
			echo "<b> {$this->table_name} </b></span> truncate";
		}
	}
}
