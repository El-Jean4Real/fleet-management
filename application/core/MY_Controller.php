<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
	// ? Forcer l'encodage UTF-8 dans les en-têtes
	header('Content-Type: text/html; charset=utf-8');

        // Chargement des helpers, librairies, DB
        $this->load->helper(['url', 'form', 'language']);
        $this->load->library(['session', 'form_validation', 'template']);
        $this->load->database();

        // ? Éviter les redirections infinies (exclusion des pages publiques)
        $current_controller = $this->router->fetch_class(); // ex: 'login'
        $public_pages = ['login', 'api', 'frontendtracker', 'frontendbooking']; // à adapter à ton contexte

        if (!isset($this->session->userdata['session_data']) && !in_array(strtolower($current_controller), $public_pages)) {
            redirect('login');
        }

        // ? Langue du site par session
        $site_lang = $this->session->userdata('site_lang') ?? 'french';
        $this->lang->load('main', $site_lang);
    }
}
