<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('user_model');
        $this->load->helper(array('form', 'url', 'string'));
        $this->load->library(['form_validation','session','upload']);
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

    /** Traite upload si présent, renvoie nom de fichier ou '' */
    private function _handle_photo_upload()
    {
        if (empty($_FILES['u_photo']['name'])) return '';

        $config = [
            'upload_path'   => FCPATH.'uploads/user_photos/',
            'allowed_types' => 'jpg|jpeg|png|gif',
            'encrypt_name'  => true,
            'max_size'      => 4096,
        ];

        if (!is_dir($config['upload_path'])) {
            @mkdir($config['upload_path'], 0755, true);
        }

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('u_photo')) {
            // Tu peux afficher l'erreur si besoin :
            // $this->session->set_flashdata('warningmessage', $this->upload->display_errors('', ''));
            return '';
        }

        $data = $this->upload->data();
        return $data['file_name'] ?? '';
    }

    public function insertuser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->session->set_flashdata('warningmessage', 'Requête non autorisée.');
            return redirect('users');
        }

        $data['basic'] = $this->input->post('basic') ?: [];
        $data['permissions'] = $this->input->post('permissions') ?: [];

        // Upload photo
        $photo = $this->_handle_photo_upload();
        if ($photo !== '') {
            $data['basic']['u_photo'] = $photo;
        }

        // Normalisation de quelques champs
        if (!isset($data['basic']['u_isactive'])) {
            $data['basic']['u_isactive'] = 1;
        }

        $response = $this->user_model->add_user($data);

        if ($response) {
            $this->session->set_flashdata('successmessage', 'Nouvel utilisateur créé avec succès.');
        } else {
            $this->session->set_flashdata('warningmessage', 'Erreur lors de la création de l\'utilisateur.');
        }

        redirect('users');
    }

    public function edituser()
    {
        $u_id = $this->uri->segment(3);
        $data['userdetails'] = $this->user_model->get_userdetails($u_id);
        $this->template->template_render('user_add', $data);
    }

    public function updateuser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->session->set_flashdata('warningmessage', 'Requête non autorisée.');
            return redirect('users');
        }

        $data = [];
        $data['basic'] = $this->input->post('basic') ?: [];
        $data['permissions'] = $this->input->post('permissions') ?: [];

        // Upload photo (écrase seulement si nouveau fichier)
        $photo = $this->_handle_photo_upload();
        if ($photo !== '') {
            $data['basic']['u_photo'] = $photo;
        }

        $response = $this->user_model->update_user($data);

        if ($response) {
            $this->session->set_flashdata('successmessage', 'Utilisateur mis à jour avec succès.');
        } else {
            $this->session->set_flashdata('warningmessage', 'Erreur lors de la mise à jour.');
        }

        redirect('users');
    }

    public function deleteuser($u_id = null)
    {
        if (!userpermission('lr_user_delete')) {
            $this->session->set_flashdata('warningmessage', 'Vous n\'avez pas les droits pour supprimer un utilisateur.');
            return redirect('users');
        }

        $current_user_id = $this->session->userdata('session_data')['u_id'] ?? 0;

        if ($u_id && is_numeric($u_id)) {
            if ((int)$u_id === (int)$current_user_id) {
                $this->session->set_flashdata('warningmessage', 'Vous ne pouvez pas supprimer votre propre compte.');
                return redirect('users');
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

    public function delete_selected_users()
    {
        if (!userpermission('lr_user_delete')) {
            $this->session->set_flashdata('warningmessage', 'Accès refusé.');
            return redirect('users');
        }

        $user_ids = $this->input->post('selected_users');
        $current_user_id = $this->session->userdata('session_data')['u_id'] ?? 0;

        if (!empty($user_ids) && is_array($user_ids)) {
            foreach ($user_ids as $id) {
                if ((int)$id === (int)$current_user_id) continue; // pas soi-même
                $this->user_model->delete_user($id);
            }
            $this->session->set_flashdata('successmessage', 'Utilisateurs sélectionnés supprimés.');
        } else {
            $this->session->set_flashdata('warningmessage', 'Aucun utilisateur sélectionné.');
        }

        redirect('users');
    }
}
