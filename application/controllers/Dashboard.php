<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->helper('url');
        $this->load->library('session');

        // Chargement des fichiers de langue
        $lang = $this->session->userdata('site_lang') ?? 'french';
        $this->lang->load('dashboard', $lang);

        // Chargement des modèles
        $this->load->model('dashboard_model');
        $this->load->model('geofence_model');
    }

    public function index()
    {
        // Récupération des offsets depuis l'URL (GET)
        $week_offset = (int) $this->input->get('week_offset');
        $month_offset = (int) $this->input->get('month_offset');

        // Calcul de la période hebdomadaire (lundi -> dimanche) avec offset
        $start_of_week = strtotime("monday this week +{$week_offset} week");
        $end_of_week   = strtotime("sunday this week +{$week_offset} week");
        $data['week_period'] = [
            'start'  => date('Y-m-d', $start_of_week),
            'end'    => date('Y-m-d', $end_of_week),
            'offset' => $week_offset,
        ];

        // Calcul de la période mensuelle (1er -> dernier jour du mois) avec offset
        $start_of_month = strtotime(date('Y-m-01') . " +{$month_offset} month");
        $end_of_month   = strtotime(date('Y-m-t', $start_of_month));
        $data['month_period'] = [
            'start'  => date('Y-m-d', $start_of_month),
            'end'    => date('Y-m-d', $end_of_month),
            'offset' => $month_offset,
        ];

        // Données diverses pour le dashboard
        $data['iechart'] = $this->dashboard_model->get_iechartdata();
        $data['todayreminder'] = $this->dashboard_model->get_todayreminder();
        $data['dashboard'] = $this->dashboard_model->getdashboard_info();
        $data['vechicle_currentlocation'] = $this->dashboard_model->get_vechicle_currentlocation();
        $data['vechicle_status'] = $this->dashboard_model->getvechicle_status();

        // Récupération et enrichissement des événements géofence
        $returndata = array();
        $geofenceevents = $this->geofence_model->get_geofenceevents(20);
        if (!empty($geofenceevents)) {
            foreach ($geofenceevents as $key => $geeodata) {
                $geo_name = $this->db->select('geo_name')
                    ->from('geofences')
                    ->where('geo_id', $geeodata['ge_geo_id'])
                    ->get()->result_array();

                if (isset($geo_name[0]['geo_name'])) {
                    $returndata[] = $geeodata;
                    $returndata[$key]['geo_name'] = $geo_name[0]['geo_name'];
                }
            }
        }
        $data['geofenceevents'] = $returndata;

        // --- IMPORTANT : On demande au modèle uniquement les objectifs correspondant
        // aux périodes (filtrage par dates) sélectionnées par l'utilisateur.
        $data['objectifs_hebdo'] = $this->dashboard_model->get_objectifs_avec_depenses(
            'hebdomadaire',
            $data['week_period']['start'],
            $data['week_period']['end']
        );

        $data['objectifs_mensuel'] = $this->dashboard_model->get_objectifs_avec_depenses(
            'mensuelle',
            $data['month_period']['start'],
            $data['month_period']['end']
        );

        // Préparation des données pour graphiques (Objectif vs Réalisé) et calcul taux
        $labels = [];
        $objectif = [];
        $realise = [];
        $depenses = [];

        // Hebdo : ajout du taux d'atteinte s'il n'est pas présent
        if (!empty($data['objectifs_hebdo'])) {
            foreach ($data['objectifs_hebdo'] as $key => $item) {
                $labels[] = $item['vehicule_nom'];
                $objectif[] = (float) $item['montant_objectif'];
                $realise[] = (float) $item['montant_realise'] + (float) $item['montant_manuel'];
                $depenses[] = (float) $item['depenses'];

                $objectif_val = (float) $item['montant_objectif'];
                $total_realise = (float) $item['montant_realise'] + (float) $item['montant_manuel'];
                $taux = $objectif_val > 0 ? round(($total_realise / $objectif_val) * 100, 2) : 0;
                $data['objectifs_hebdo'][$key]['taux_atteinte'] = $taux;
            }
        }

        // Mensuel : calculer aussi le taux d'atteinte pour affichage
        if (!empty($data['objectifs_mensuel'])) {
            foreach ($data['objectifs_mensuel'] as $key => $item) {
                $objectif_val = (float) $item['montant_objectif'];
                $total_realise = (float) $item['montant_realise'] + (float) $item['montant_manuel'];
                $taux = $objectif_val > 0 ? round(($total_realise / $objectif_val) * 100, 2) : 0;
                $data['objectifs_mensuel'][$key]['taux_atteinte'] = $taux;
            }
        }

        $data['graph_labels'] = json_encode($labels ?: []);
        $data['graph_objectif'] = json_encode($objectif ?: []);
        $data['graph_realise'] = json_encode($realise ?: []);
        $data['graph_depenses'] = json_encode($depenses ?: []);

        // Affichage de la vue avec les données préparées
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

    public function afficher_roles()
    {
        echo "<pre>";
        print_r($_SESSION['userroles']);
        echo "</pre>";
    }
}
