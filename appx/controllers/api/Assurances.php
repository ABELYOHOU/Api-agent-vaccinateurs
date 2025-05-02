<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Assurances extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Assurances_model','assurModel');
	$this->load->model('Global_model', 'globalModel');
}


public function getAssurancesByPharmacies_post()
{
		
	if (!empty($this->input->post('idEntreprise')))
	{
		$query = $this->assurModel->getListeAssurByOfficines($this->input->post('idEntreprise'));
		if ($query) 
		{
			$response['code']=1;
		    $response['data']=$query;
		    $response['msg']="Assurances récuperées";
		}
		else
		{
			$response['code']=0;
			$response['data']= '';
			$response['msg']="Aucune Assurance Active Pour Le Moment !";
		}

	}
	else
	{
		$response['code']=0;
        $response['data']= '';
        $response['msg']="Vérifier les variables envoyées !";
	}

	return $this->response($response, REST_Controller::HTTP_OK);
}

}