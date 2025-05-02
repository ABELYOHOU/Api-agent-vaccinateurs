<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Reversement extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Auth_model','authModel');
	$this->load->model('Global_model', 'globalModel');
	$this->load->model('Reversement_model', 'ReversModel');
}



public function getListeTotalReversement_post()
{
		if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')))
		{	
				$entID = $this->input->post('entID');
				$idLogin = $this->input->post('idLogin');
				$getVisiteurs = $this->authModel->getMonCompte($idLogin);
				if (empty($getVisiteurs)) 
				{
					$response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Ce utilisateur est inconnu !";
				}
				else
				{	
					$getreverse = $this->ReversModel->getReversements($entID);
					$response['code']=1;
				    $response['data']= $getreverse;
				    $response['msg']="Liste reversement affichée !";
				}
		}
		else
		{
		    $response['code']=0;
	        $response['data']= '';
	        $response['msg']="Vérifier les variables envoyées";
		}

		return $this->response($response, REST_Controller::HTTP_OK);
}





}