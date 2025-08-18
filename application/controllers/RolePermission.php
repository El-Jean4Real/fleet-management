<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RolePermission extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Role_model');
        $this->load->model('Permission_model');
        $this->check_permission('assign_permission');
    }

    public function assign($role_id) {
        $role = $this->Role_model->get($role_id);
        $permissions = $this->Permission_model->get_all();
        $assigned_permissions = $this->Permission_model->get_permissions_by_role($role_id);

        if ($this->input->post()) {
            $selected = $this->input->post('permissions') ?? [];
            $this->Permission_model->update_role_permissions($role_id, $selected);
            redirect('Roles'); // ajuster selon ton contrôleur de rôle
        }

        $data = compact('role', 'permissions', 'assigned_permissions');
        $this->load->view('assign_permissions', $data);
    }
}
