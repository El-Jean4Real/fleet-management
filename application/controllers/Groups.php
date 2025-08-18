<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Group_model');
        $this->load->model('Permission_model');
        $this->check_permission('manage_groups');
    }

    public function index() {
        $data['groups'] = $this->Group_model->get_all_groups();
        $this->load->view('groups_list', $data);
    }

    public function create() {
        if ($this->input->post()) {
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            $permissions = $this->input->post('permissions') ?? [];

            $this->Group_model->create_group($name, $description, $permissions);
            redirect('groups');
        }

        $data['permissions'] = $this->Permission_model->get_all();
        $this->load->view('group_create', $data);
    }

    public function edit($id) {
        if ($this->input->post()) {
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            $permissions = $this->input->post('permissions') ?? [];

            $this->Group_model->update_group($id, $name, $description, $permissions);
            redirect('groups');
        }

        $data['group'] = $this->Group_model->get_group($id);
        $data['permissions'] = $this->Permission_model->get_all();
        $this->load->view('group_create', $data);
    }

    public function delete($id) {
        $this->Group_model->delete_group($id);
        redirect('groups');
    }
}
