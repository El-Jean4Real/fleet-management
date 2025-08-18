<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Permission_model');
        $this->check_permission('view_permission');
    }

    public function index() {
        $data['permissions'] = $this->Permission_model->get_all();
        $this->load->view('permissions', $data);
    }

    public function create() {
        $this->check_permission('create_permission');

        if ($this->input->post()) {
            $name = $this->input->post('name');
            $description = $this->input->post('description');

            $this->Permission_model->create($name, $description);
            redirect('Permission');
        }

        $this->load->view('permissions_create');
    }
}
