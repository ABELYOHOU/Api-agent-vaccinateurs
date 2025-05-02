<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Vaccins extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Reservations_model', 'resModel');
	$this->load->model('Vaccins_model','vaccinsModel');
	$this->load->model('Dossiers_model', 'dosModel');
	$this->load->model('Global_model', 'globalModel');
	$this->load->model('Auth_model','authModel');
}


public function getVaccinsBySousCategories_post()
{
	if (!empty($this->input->post('idSousCategorie')))
	{	
			$idSousCategorie = $this->input->post('idSousCategorie');
			$getListesVaccins = $this->vaccinsModel->getListeVaccinsBySousVaccins($idSousCategorie);
			if (empty($getListesVaccins)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Aucun vaccin disponible !";
			}
			else
			{	
			    $response['code']=1;
		        $response['data']= $getListesVaccins;
		        $response['msg']="Liste des vaccins recuperée !";
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

public function getListeVaccinsByUsersId_post()
{
	if (!empty($this->input->post('idLogin')) && !empty($this->input->post('idPatients')))
	{	
		$idLogin = $this->input->post('idLogin');
		$idPatients = $this->input->post('idPatients');
		$getVisiteurs = $this->authModel->getMonCompte($idLogin);
		if (empty($getVisiteurs)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Ce utilisateur est inconnu !";
		}
		else
		{
			$getPatients = $this->dosModel->isDossiersByPatientsID($idPatients);
			if (empty($getPatients)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Ce carnet est introuvable !";
			}
			else
			{	
				$getListesVaccins = $this->vaccinsModel->getListeVaccinsBySousVaccins($getPatients->sousVaccinsID);
			    $response['code']=1;
		        $response['data']= $getListesVaccins;
		        $response['msg']="Liste de vaccins recuperée !";
			}
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

public function getListeVaccinsByUsersMobile_post()
{
	if (!empty($this->input->post('idLogin')) && !empty($this->input->post('mobilePatients')))
	{	
		$idLogin = $this->input->post('idLogin');
		$mobilePatients = $this->input->post('mobilePatients');
		$getPatients = $this->dosModel->isPatientsByMobiles($mobilePatients);
		$getVisiteurs = $this->authModel->getMonCompte($idLogin);
		if (empty($getVisiteurs)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Ce utilisateur est inconnu !";
		}
		elseif (empty($getPatients)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Ce Patient est Inconnu !";
		}
		else
		{
			$getListesVaccins = $this->vaccinsModel->getListeVaccinsBySousVaccins($getPatients->sousVaccinsID);
			if (empty($getListesVaccins)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Aucun Vaccin Disponible !";
			}
			else
			{	
			    $response['code']=1;
		        $response['data']= $getListesVaccins;
		        $response['msg']="$getPatients->id_patients";
			}
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


public function getListeVaccinsByResId_post()
{
	if (!empty($this->input->post('codeRes')))
	{	
		$codeRes = $this->input->post('codeRes');
		$getRes = $this->resModel->isCodeRes($codeRes);
		if (empty($getRes)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Cette Réservation N'existe Pas !";
		}
		else
		{	
			$getListesVaccins = $this->vaccinsModel->getVaccinations($getRes->id_res);
			if ($getListesVaccins) 
			{
				$response['code']=1;
		        $response['data']= $getListesVaccins;
		        $response['msg']="Liste de vaccins recuperée !";
			}
		    else
			{
				$response['code']=0;
		        $response['data']= '';
		        $response['msg']="Aucun vaccin choisi !";
			}
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