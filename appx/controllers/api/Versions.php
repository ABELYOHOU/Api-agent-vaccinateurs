<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Versions extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Versions_model','versModel');
	$this->load->model('Global_model', 'globalModel');
}


public function getVersions_get()
{
		$query = $this->versModel->getVersionsActifs();
		if ($query) 
		{
			$response['code']=1;
		    $response['data']=$query;
		    $response['msg']="Version récuperée";
		}
		else
		{
			  $response['code']=1;
			  $response['data']= array('versionsNumbers' => '1.1.8', 'buildNumbers' => '18');
			  $response['msg']="Aucune version active !";
		}

	return $this->response($response, REST_Controller::HTTP_OK);
}

}