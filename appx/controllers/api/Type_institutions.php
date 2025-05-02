<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Type_institutions extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Type_institutions_model', 'typesModel');
	$this->load->model('Global_model', 'globalModel');
}

public function getTypeEntreprises_get()
{
	$query = $this->typesModel->getTypeInstitutions();
	if ($query) 
	{
		$response['code']=1;
	    $response['data']=$query;
	    $response['msg']="Donnée type institution récuperées";
	}
	else
	{
		$response['code']=0;
		$response['data']= '';
		$response['msg']="Aucun type institution actif pour le moment !";
	}

	return $this->response($response, REST_Controller::HTTP_OK);
}



}

?>