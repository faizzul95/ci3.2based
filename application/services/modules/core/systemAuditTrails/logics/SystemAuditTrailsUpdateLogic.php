<?php

namespace App\services\modules\core\systemAuditTrails\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemAuditTrails\processors\SystemAuditTrailsStoreProcessors;

class SystemAuditTrailsUpdateLogic
{
    private $ci;

    public function __construct()
    {
        $this->ci = ci();
        $this->ci->load->library('form_validation');
    }

    public function logic($request)
    {
        $this->_rules($request);

        if ($this->ci->form_validation->run() !== FALSE) {
            return app(new SystemAuditTrailsStoreProcessors)->execute($request);
        } else {
            return ['code' => 422, 'message' => validation_errors(), 'data' => $request];
        }
    }

    public function _rules($data)
    {
        $this->ci->form_validation->reset_validation(); // Reset validation.

        // Validation rules for the fields.
        $this->ci->form_validation->set_rules('id', 'Id', 'trim|required|integer');
        $this->ci->form_validation->set_rules('user_id', 'User Id', 'trim|integer');
        $this->ci->form_validation->set_rules('role_id', 'Role Id', 'trim|integer');
        $this->ci->form_validation->set_rules('user_fullname', 'User Fullname', 'trim|min_length[1]|max_length[255]');
        $this->ci->form_validation->set_rules('event', 'Event', 'trim|min_length[1]|max_length[20]');
        $this->ci->form_validation->set_rules('table_name', 'Table Name', 'trim|min_length[1]|max_length[80]');
        $this->ci->form_validation->set_rules('old_values', 'Old Values', 'trim');
        $this->ci->form_validation->set_rules('new_values', 'New Values', 'trim');
        $this->ci->form_validation->set_rules('url', 'Url', 'trim|min_length[1]|max_length[150]');
        $this->ci->form_validation->set_rules('ip_address', 'Ip Address', 'trim|min_length[1]|max_length[150]');
        $this->ci->form_validation->set_rules('user_agent', 'User Agent', 'trim|min_length[1]|max_length[150]');

        // Set the data to be validated
        $this->ci->form_validation->set_data($data);
    }
}
