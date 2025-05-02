<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Communes extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Communes_model', 'communesModel');
	$this->load->model('Global_model', 'globalModel');
}

public function getListeCommunes_get()
{
	$query = $this->communesModel->getCommunesActifs();
	if ($query) 
	{
		$response['code']=1;
	    $response['data']=$query;
	    $response['msg']="Données des communes récuperées";
	}
	else
	{
		$response['code']=0;
		$response['data']= '';
		$response['msg']="Aucune commune active pour le moment !";
	}

	return $this->response($response, REST_Controller::HTTP_OK);
}

}

?>