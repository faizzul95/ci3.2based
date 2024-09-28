<?php 

defined('BASEPATH') or exit('No direct script access allowed');

class Validation
{
    public $CI;
    public $errors = [];

    /**
     * Constructor
     * 
     * Initializes the Validation object and loads necessary CodeIgniter resources
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('form_validation');
    }

    /**
     * Validate the input data against the given rules
     * 
     * @param array $data The input data to validate
     * @param array $rules The validation rules
     * @return bool True if validation passes, false otherwise
     */
    public function validate($data, $rules)
    {
        $this->CI->form_validation->reset_validation();
        $this->CI->form_validation->set_data($data);

        foreach ($rules as $field => $rule_string) {
            $rule_array = explode('|', $rule_string);
            $this->CI->form_validation->set_rules($field, ucfirst($field), $rule_array);
        }

        if ($this->CI->form_validation->run() === FALSE) {
            $this->errors = $this->CI->form_validation->error_array();
            return false;
        }

        return true;
    }

    /**
     * Get all validation errors
     * 
     * @return array An array of validation errors
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Get the first error message for a given field
     * 
     * @param string $field The field name
     * @return string|null The first error message for the field, or null if no errors
     */
    public function first($field)
    {
        return isset($this->errors[$field]) ? $this->errors[$field] : null;
    }

    /**
     * Check if the validation failed
     * 
     * @return bool True if validation failed, false otherwise
     */
    public function fails()
    {
        return !empty($this->errors);
    }

    /**
     * Check if the validation passed
     * 
     * @return bool True if validation passed, false otherwise
     */
    public function passes()
    {
        return empty($this->errors);
    }
}
