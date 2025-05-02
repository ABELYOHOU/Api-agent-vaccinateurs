<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Quartiers extends REST_Controller {

public function __construct()
{
		parent::__construct();
		$this->load->model('Quartiers_model', 'quartiersModel');
}


public function getListeQuartiers_get()
{
		$query = $this->quartiersModel->getQuartiersActifs();
		if ($query) 
		{
				$response['code']=1;
		       $response['data']=$query;
		       $response['msg']="Horaires récuperées";
		}
		else
		{
			  $response['code']=0;
			  $response['data']= '';
			  $response['msg']="Aucune horaire active !";
		}

	return $this->response($response, REST_Controller::HTTP_OK);
}

public function getQuartiersByCommune_post()
{
		if (!empty($this->input->post('idCommunes')))
		{	
				$idCommunes = $this->input->post('idCommunes');
				$getListeQuartiers = $this->quartiersModel->getQuartiersByCommune($idCommunes);
			  $response['code']=1;
		    $response['data']= $getListeQuartiers;
				$response['msg']="Liste des quartiers !";
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