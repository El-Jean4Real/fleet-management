<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthLib {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('session');
    }

    public function has_permission($permission)
    {
        $user_id = $this->CI->session->userdata('user_id');
        if (!$user_id) return false;

        $query = $this->CI->db->query("
            SELECT COUNT(*) AS count
            FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.id
            WHERE ur.user_id = ?
            AND p.name = ?
        ", [$user_id, $permission]);

        return $query->row()->count > 0;
    }
}
