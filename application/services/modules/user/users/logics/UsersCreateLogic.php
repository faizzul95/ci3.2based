<?php

namespace App\services\modules\user\users\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\users\processors\UsersStoreProcessors;

class UsersCreateLogic
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
            return app(new UsersStoreProcessors)->execute($request);
        } else {
            return ['code' => 422, 'message' => validation_errors(), 'data' => $request];
        }
    }

    public function _rules($data)
    {
        $this->ci->form_validation->reset_validation(); // Reset validation.

        // Validation rules for the fields.
        $this->ci->form_validation->set_rules('file_no', 'File No', 'trim|required|min_length[2]|max_length[15]');
        $this->ci->form_validation->set_rules('name', 'Full Name', 'trim|required|min_length[3]|max_length[12]');
        $this->ci->form_validation->set_rules('user_nric', 'User NRIC', 'trim|required|min_length[5]|max_length[15]');
        $this->ci->form_validation->set_rules('email', 'Email', 'trim|valid_email|required|min_length[5]|max_length[250]');
        $this->ci->form_validation->set_rules('user_contact_no', 'Contact No', 'trim|min_length[8]|max_length[12]');
        $this->ci->form_validation->set_rules('user_dob', 'Date of Birth', 'trim');
        $this->ci->form_validation->set_rules('user_join_date', 'Join Date', 'trim');
        $this->ci->form_validation->set_rules('user_resign_date', 'Resign Date', 'trim');
        $this->ci->form_validation->set_rules('ptb_no', 'PTB No', 'trim|min_length[3]|max_length[15]');
        $this->ci->form_validation->set_rules('ptb_no_date', 'PTB No Date', 'trim');

        // Set the data to be validated
        $this->ci->form_validation->set_data($data);
    }
}
