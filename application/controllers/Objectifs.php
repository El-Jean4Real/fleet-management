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
        $data['chauffeurs'] = $this->Drivers_model->get_all(); // ?? ici aussi c'�tait mal �crit

        $data['header'] = $this->load->view('header', '', TRUE);
        $data['sidebar'] = $this->load->view('sidebar', '', TRUE);
        $data['content'] = $this->load->view('objectifs_list', $data, TRUE);
        $data['footer'] = $this->load->view('footer', '', TRUE);

        $this->load->view('template', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $type_cible = $this->input->post('type_cible');
            $cible_id = $this->input->post('cible_id');
            $groupe_id = $this->input->post('cible_id_groupe'); // ?? nouveau champ

            if (empty($cible_id) && empty($groupe_id)) {
                $this->session->set_flashdata('error', $this->lang->line('select_target_or_group'));
                redirect('objectifs/add');
                return;
            }

            $periode_type = $this->input->post('periode_type');
            $periode_debut = $this->input->post('periode_debut');
            $periode_fin = $this->input->post('periode_fin');
            $montant = $this->input->post('montant_objectif');

            if (!empty($groupe_id)) {
                // Objectif appliqu� � tous les v�hicules du groupe
                $vehicules = $this->Vehicle_model->get_by_group($groupe_id);

                foreach ($vehicules as $vehicule) {
                    $data = [
                        'type_cible' => 'vehicule',
                        'cible_id' => $vehicule['v_id'],
                        'periode_type' => $periode_type,
                        'periode_debut' => $periode_debut,
                        'periode_fin' => $periode_fin,
                        'montant_objectif' => $montant
                    ];
                    $this->Objectifs_model->insert($data);
                }

                $this->session->set_flashdata('success', $this->lang->line('objective_added_group'));
            } else {
                // Objectif individuel
                $data = [
                    'type_cible' => $type_cible,
                    'cible_id' => $cible_id,
                    'periode_type' => $periode_type,
                    'periode_debut' => $periode_debut,
                    'periode_fin' => $periode_fin,
                    'montant_objectif' => $montant
                ];

                if ($this->Objectifs_model->insert($data)) {
                    $this->session->set_flashdata('success', $this->lang->line('objective_added'));
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('objective_add_error'));
                }
            }

            redirect('objectifs');
        } else {
            $view_data = [
                'vehicules' => $this->Vehicle_model->get_all(),
                'vehicule_group' => $this->Vehicle_model->get_all_groups(), // m�thode � cr�er
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
                $this->session->set_flashdata('error', 'Veuillez s�lectionner une cible valide.');
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
                $this->session->set_flashdata('success', 'Objectif mis � jour avec succ�s.');
            } else {
                $this->session->set_flashdata('error', 'Une erreur est survenue lors de la mise � jour.');
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
            $this->session->set_flashdata('success', "Objectif supprim� avec succ�s.");
        } else {
            $this->session->set_flashdata('error', "�chec de la suppression de l�objectif.");
        }

        redirect('objectifs');
    }

    public function stats()
    {
        $type_cible = $this->input->get('type_cible'); // vehicule ou user
        $cible_id   = $this->input->get('cible_id'); // id v�hicule ou chauffeur

        $this->load->model('Recettes_model'); // � cr�er si pas encore

        // Objectifs
        $objectifs = $this->Objectifs_model->search($type_cible, $cible_id);

        // Recettes r�ellement enregistr�es
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
