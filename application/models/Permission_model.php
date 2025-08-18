<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_model extends CI_Model {

    public function get_all_permissions() {
        return $this->db->get('permissions')->result();
    }

    public function add_permission($name, $description) {
        $data = [
            'name' => $name,
            'description' => $description
        ];
        return $this->db->insert('permissions', $data);
    }

    public function delete_permission($id) {
        return $this->db->delete('permissions', ['id' => $id]);
    }

    public function get_permissions_by_role($role_id) {
        $this->db->select('permissions.*');
        $this->db->from('permissions');
        $this->db->join('role_permissions', 'permissions.id = role_permissions.permission_id');
        $this->db->where('role_permissions.role_id', $role_id);
        return $this->db->get()->result();
    }

    public function assign_permission_to_role($role_id, $permission_id) {
        $data = [
            'role_id' => $role_id,
            'permission_id' => $permission_id
        ];
        return $this->db->insert('role_permissions', $data);
    }

    public function remove_permission_from_role($role_id, $permission_id) {
        $this->db->where('role_id', $role_id);
        $this->db->where('permission_id', $permission_id);
        return $this->db->delete('role_permissions');
    }
}
