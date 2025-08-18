<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // === Helpers, librairies et DB ===
        $this->load->helper(['url', 'form', 'language']);
        $this->load->library(['session', 'form_validation', 'template']);
        $this->load->database();

        // === Forcer l'encodage UTF-8 ===
        header('Content-Type: text/html; charset=UTF-8');

        // === Déterminer la langue du site (session ou 'french' par défaut) ===
        $site_lang = $this->session->userdata('site_lang') ?? 'french';

        // === Charger les fichiers de langue globaux nécessaires ===
        // (nom du fichier sans le suffixe _lang.php)
        $this->lang->load('main', $site_lang);
        $this->lang->load('dashboard', $site_lang);
        $this->lang->load('sidebar', $site_lang); // <-- Ajouté : charger sidebar_lang.php

        // === Éviter les redirections infinies (pages publiques) ===
        $current_controller = strtolower($this->router->fetch_class());
        $public_pages = ['login', 'api', 'frontendtracker', 'frontendbooking'];

        if (!isset($this->session->userdata['session_data']) && !in_array($current_controller, $public_pages)) {
            redirect('login');
        }
    }
}
