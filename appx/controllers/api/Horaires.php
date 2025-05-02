<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Horaires extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Horaires_model','heuresModel');
	$this->load->model('Global_model', 'globalModel');
}


public function getListeHoraires_get()
{
		$query = $this->heuresModel->getHorairesActifs();
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

}