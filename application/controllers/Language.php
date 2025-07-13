<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language extends MY_Controller {

    public function switch($lang = "french") {
        $allowed = ['english', 'french'];
        if (in_array($lang, $allowed)) {
            $this->session->set_userdata('site_lang', $lang);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
}
