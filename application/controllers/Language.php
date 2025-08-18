<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // ?? Charge la librairie user_agent ici
        $this->load->library('user_agent');
    }

    public function switch($lang = 'french')
    {
        if (!in_array($lang, ['french', 'english'])) {
            $lang = 'french'; // langue par défaut
        }

        $this->session->set_userdata('site_lang', $lang);
        redirect($this->agent->referrer() ?? base_url());
    }
}
