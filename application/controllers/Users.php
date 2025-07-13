<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('user_model');
        $this->load->helper(array('form', 'url', 'string'));
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    public function index()
    {
        $data['userlist'] = $this->user_model->getall_user();
        $this->template->template_render('user_management', $data);
    }

    public function adduser()
    {
        $this->template->template_render('user_add');
    }

    public function insertuser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['basic'] = $this->input->post('basic');
            $data['permissions'] = $this->input->post('permissions');

            $response = $this->user_model->add_user($data);

            if ($response) {
                $this->session->set_flashdata('successmessage', 'Nouvel utilisateur créé avec succès.');
            } else {
                $this->session->set_flashdata('warningmessage', 'Erreur lors de la création de l\'utilisateur.');
            }

            redirect('users');
        } else {
            $this->session->set_flashdata('warningmessage', 'Requête non autorisée.');
            redirect('users');
        }
    }

    public function edituser()
    {
        $u_id = $this->uri->segment(3);
        $data['userdetails'] = $this->user_model->get_userdetails($u_id);
        $this->template->template_render('user_add', $data);
    }

    public function updateuser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input->post();
            $response = $this->user_model->update_user($data);

            if ($response) {
                $this->session->set_flashdata('successmessage', 'Utilisateur mis à jour avec succès.');
            } else {
                $this->session->set_flashdata('warningmessage', 'Erreur lors de la mise à jour.');
            }

            redirect('users');
        } else {
            $this->session->set_flashdata('warningmessage', 'Requête non autorisée.');
            redirect('users');
        }
    }

    // ?? Supprimer un utilisateur par ID
    public function deleteuser($u_id = null)
    {
        if (!userpermission('lr_user_delete')) {
            $this->session->set_flashdata('warningmessage', 'Vous n\'avez pas les droits pour supprimer un utilisateur.');
            redirect('users');
            return;
        }

        $current_user_id = $this->session->userdata('session_data')['u_id'] ?? 0;

        if ($u_id && is_numeric($u_id)) {
            if ((int)$u_id === (int)$current_user_id) {
                $this->session->set_flashdata('warningmessage', 'Vous ne pouvez pas supprimer votre propre compte.');
                redirect('users');
                return;
            }

            $deleted = $this->user_model->delete_user($u_id);
            if ($deleted) {
                $this->session->set_flashdata('successmessage', 'Utilisateur supprimé avec succès.');
            } else {
                $this->session->set_flashdata('warningmessage', 'Échec de la suppression de l\'utilisateur.');
            }
        } else {
            $this->session->set_flashdata('warningmessage', 'ID utilisateur invalide.');
        }

        redirect('users');
    }

    // ??? Suppression multiple avec sécurité
    public function delete_selected_users()
    {
        if (!userpermission('lr_user_delete')) {
            $this->session->set_flashdata('warningmessage', 'Accès refusé.');
            redirect('users');
            return;
        }

        $user_ids = $this->input->post('selected_users');
        $current_user_id = $this->session->userdata('session_data')['u_id'] ?? 0;

        if (!empty($user_ids) && is_array($user_ids)) {
            foreach ($user_ids as $id) {
                if ((int)$id === (int)$current_user_id) {
                    continue; // Ne pas supprimer soi-même
                }
                $this->user_model->delete_user($id);
            }
            $this->session->set_flashdata('successmessage', 'Utilisateurs sélectionnés supprimés.');
        } else {
            $this->session->set_flashdata('warningmessage', 'Aucun utilisateur sélectionné.');
        }

        redirect('users');
    }
}
