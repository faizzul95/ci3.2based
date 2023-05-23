<?php

namespace App\services\modules\core\users\processors;

class UsersSearchProcessors
{
    public function __construct()
    {
        model('User_model', 'userM');
    }

    public function execute($filter = NULL, $fetchType = 'result_array')
    {
        $ci = ci();

        if (hasData($filter)) {

            if (hasData($filter, 'searchQuery')) {
                $ci->db->like('name', $filter['searchQuery']);
                $ci->db->or_like('user_nric_visa', $filter['searchQuery']);
                $ci->db->or_like('user_staff_no', $filter['searchQuery']);
            }

            if (hasData($filter, 'condition')) {
                $ci->db->where($filter['condition']);
            }

            if (hasData($filter, 'order')) {
                $ci->db->order_by($filter['order']);
            }
        }

        return $ci->db->get('users')->$fetchType();
    }
}
