<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Objectifs extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
	$this->load->helper(['url', 'form']); // ? ? Ici on ajoute 'form'
        $this->load->model('Objectifs_model');
        $this->load->model('Vehicle_model');
        $this->load->model('Drivers_model');
    }

    public function index() {
        $type_cible     = $this->input->get('type_cible');
        $cible_id       = $this->input->get('cible_id');
        $periode_debut  = $this->input->get('periode_debut');
        $periode_fin    = $this->input->get('periode_fin');

        $objectifs = $this->Objectifs_model->search($type_cible, $cible_id, $periode_debut, $periode_fin);

        $data['objectifs'] = $objectifs;
        $data['vehicules'] = $this->Vehicle_model->get_all();
        $data['chauffeurs'] = $this->Drivers_model->get_all(); // ?? ici aussi c'était mal écrit

        $data['header'] = $this->load->view('header', '', TRUE);
        $data['sidebar'] = $this->load->view('sidebar', '', TRUE);
        $data['content'] = $this->load->view('objectifs_list', $data, TRUE);
        $data['footer'] = $this->load->view('footer', '', TRUE);

        $this->load->view('template', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cible_id = $this->input->post('cible_id');
            if (empty($cible_id)) {
                $this->session->set_flashdata('error', 'Veuillez sélectionner une cible valide.');
                redirect('objectifs/add');
                return;
            }

            $data = [
                'type_cible' => $this->input->post('type_cible'),
                'cible_id' => $cible_id,
                'periode_type' => $this->input->post('periode_type'),
                'periode_debut' => $this->input->post('periode_debut'),
                'periode_fin' => $this->input->post('periode_fin'),
                'montant_objectif' => $this->input->post('montant_objectif')
            ];

            if ($this->Objectifs_model->insert($data)) {
                $this->session->set_flashdata('success', 'Objectif ajouté avec succès.');
            } else {
                $this->session->set_flashdata('error', 'Une erreur est survenue lors de l’enregistrement.');
            }

            redirect('objectifs');
        } else {
            $view_data = [
                'vehicules' => $this->Vehicle_model->get_all(),
                'chauffeurs' => $this->Drivers_model->get_all()
            ];

            $data['header'] = $this->load->view('header', '', TRUE);
            $data['sidebar'] = $this->load->view('sidebar', '', TRUE);
            $data['content'] = $this->load->view('objectifs_form', $view_data, TRUE);
            $data['footer'] = $this->load->view('footer', '', TRUE);

            $this->load->view('template', $data);
        }
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cible_id = $this->input->post('cible_id');
            if (empty($cible_id)) {
                $this->session->set_flashdata('error', 'Veuillez sélectionner une cible valide.');
                redirect('objectifs/edit/' . $id);
                return;
            }

            $data = [
                'type_cible' => $this->input->post('type_cible'),
                'cible_id' => $cible_id,
                'periode_type' => $this->input->post('periode_type'),
                'periode_debut' => $this->input->post('periode_debut'),
                'periode_fin' => $this->input->post('periode_fin'),
                'montant_objectif' => $this->input->post('montant_objectif')
            ];

            if ($this->Objectifs_model->update($id, $data)) {
                $this->session->set_flashdata('success', 'Objectif mis à jour avec succès.');
            } else {
                $this->session->set_flashdata('error', 'Une erreur est survenue lors de la mise à jour.');
            }

            redirect('objectifs');
        } else {
            $view_data = [
                'edit_mode' => true,
                'objectifs_data' => $this->Objectifs_model->get_by_id($id),
                'vehicules' => $this->Vehicle_model->get_all(),
                'chauffeurs' => $this->Drivers_model->get_all()
            ];

            $data['header'] = $this->load->view('header', '', TRUE);
            $data['sidebar'] = $this->load->view('sidebar', '', TRUE);
            $data['content'] = $this->load->view('objectifs_form', $view_data, TRUE);
            $data['footer'] = $this->load->view('footer', '', TRUE);

            $this->load->view('template', $data);
        }
    }

    public function delete($id) {
        if (!is_numeric($id)) {
            $this->session->set_flashdata('error', "ID invalide.");
            redirect('objectifs');
        }

        $deleted = $this->Objectifs_model->delete($id);

        if ($deleted) {
            $this->session->set_flashdata('success', "Objectif supprimé avec succès.");
        } else {
            $this->session->set_flashdata('error', "Échec de la suppression de l’objectif.");
        }

        redirect('objectifs');
    }

    public function stats()
    {
        $type_cible = $this->input->get('type_cible'); // vehicule ou user
        $cible_id   = $this->input->get('cible_id'); // id véhicule ou chauffeur

        $this->load->model('Recettes_model'); // à créer si pas encore

        // Objectifs
        $objectifs = $this->Objectifs_model->search($type_cible, $cible_id);

        // Recettes réellement enregistrées
        $recettes = $this->Recettes_model->get_total_by_objectifs($type_cible, $cible_id);

        $data = [
            'objectifs' => $objectifs,
            'recettes'  => $recettes,
            'vehicules' => $this->Vehicle_model->get_all(),
            'chauffeurs' => $this->Drivers_model->get_all()
        ];

        $data['header'] = $this->load->view('header', '', TRUE);
        $data['sidebar'] = $this->load->view('sidebar', '', TRUE);
        $data['content'] = $this->load->view('objectifs_stats', $data, TRUE);
        $data['footer'] = $this->load->view('footer', '', TRUE);

        $this->load->view('template', $data);
    }

}
