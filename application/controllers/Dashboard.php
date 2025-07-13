<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('dashboard_model');
        $this->load->model('geofence_model');
    }

    public function index()
    {
        // Données existantes
        $data['iechart'] = $this->dashboard_model->get_iechartdata();
        $data['todayreminder'] = $this->dashboard_model->get_todayreminder();
        $data['dashboard'] = $this->dashboard_model->getdashboard_info();
        $data['vechicle_currentlocation'] = $this->dashboard_model->get_vechicle_currentlocation();
        $data['vechicle_status'] = $this->dashboard_model->getvechicle_status();

        // Données géofence enrichies
        $returndata = array();
        $geofenceevents = $this->geofence_model->get_geofenceevents(20);
        if (!empty($geofenceevents)) {
            foreach ($geofenceevents as $key => $geeodata) {
                $geo_name = $this->db->select('geo_name')->from('geofences')->where('geo_id', $geeodata['ge_geo_id'])->get()->result_array();
                if (isset($geo_name[0]['geo_name'])) {
                    $returndata[] = $geeodata;
                    $returndata[$key]['geo_name'] = $geo_name[0]['geo_name'];
                }
            }
        }
        $data['geofenceevents'] = $returndata;

        // ? NOUVELLES DONNÉES : Objectifs hebdo et mensuels
        $data['objectifs_hebdo'] = $this->dashboard_model->get_objectifs_avec_depenses('hebdomadaire');
	$labels = [];
	$objectif = [];
	$realise = [];

	foreach ($data['objectifs_hebdo'] as $item) {
	    $labels[] = $item['vehicule_nom'];
	    $objectif[] = (int)$item['montant_objectif'];
	    $realise[] = (int)$item['montant_realise'];
	}

	$data['graph_labels'] = json_encode($labels);
	$data['graph_objectif'] = json_encode($objectif);
	$data['graph_realise'] = json_encode($realise);

        $data['objectifs_mensuel'] = $this->dashboard_model->get_objectifs_avec_depenses('mensuelle');

        // ? Données préparées pour le graphe Objectif vs Réalisé
        $labels = [];
        $objectif = [];
        $realise = [];
        $depenses = [];

        foreach ($data['objectifs_hebdo'] as $key => $item) {
            $labels[] = $item['vehicule_nom'];
            $objectif[] = (float) $item['montant_objectif'];
            $realise[] = (float) $item['montant_realise'] + (float) $item['montant_manuel'];
            $depenses[] = (float) $item['depenses'];

            // Calcul du taux d'atteinte
            $objectif_val = (float) $item['montant_objectif'];
            $total_realise = (float) $item['montant_realise'] + (float) $item['montant_manuel'];

            $taux = $objectif_val > 0 ? round(($total_realise / $objectif_val) * 100, 2) : 0;

            // Injecter le taux dans le tableau (très important pour la vue)
            $data['objectifs_hebdo'][$key]['taux_atteinte'] = $taux;
        }

        $data['graph_labels'] = json_encode($labels);
        $data['graph_objectif'] = json_encode($objectif);
        $data['graph_realise'] = json_encode($realise);
	$data['graph_depenses'] = json_encode($depenses);


        // Vue dashboard

        $this->template->template_render('dashboard', $data);
    }

    public function iechart()
    {
        $data = $this->dashboard_model->get_iechartdata();
        $res = "['" . implode("', '", array_keys($data)) . "']";
        $income = "['" . implode("', '", array_column($data, 'income')) . "']";
        $expense = "['" . implode("', '", array_column($data, 'expense')) . "']";
        echo json_encode(array('res' => $res, 'income' => $income, 'expense' => $expense));
    }

    public function remindermark()
    {
        $data = array('r_isread' => 1);
        $this->db->where('r_id', $this->input->post('r_id'));
        echo $this->db->update('reminder', $data);
    }
}
