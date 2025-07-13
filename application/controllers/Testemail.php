<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testemail extends MY_Controller {

	 function __construct()
     {
          parent::__construct();
		  $this->load->model('email_model');	       	  
     }

	public function index()
	{
		  $this->email_model->sendemail();
	}
	

}
