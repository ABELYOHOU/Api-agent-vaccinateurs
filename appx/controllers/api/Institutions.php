<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Institutions extends REST_Controller {

public function __construct()
{
	//	Chargement des ressources pour tout le contrôleur
	parent::__construct();
	$this->load->model('Global_model', 'globalModel');
	$this->load->model('Institutions_model', 'instModel');
	$this->load->model('Reservations_model', 'resModel');
	date_default_timezone_set('UTC');
}


public function getListePharmacies_post()
{	
	//log_message('info', $this->input->post('nom_visiteurs'));
	if (!empty($this->input->post('communeID')))
	{	
		$communeID = $this->input->post('communeID');
		$getPeriodesGarde = $this->instModel->isPeriodesGarde();

		if ($getPeriodesGarde) {
			$PeriodesGardeDebut = date("d/m/Y",strtotime($getPeriodesGarde->dateDebutPeriode));
			$PeriodesGardeFinal = date("d/m/Y",strtotime($getPeriodesGarde->dateFinPeriode));
		}
		else
		{
			$PeriodesGardeDebut = date("d/m/Y");
			$PeriodesGardeFinal = date("d/m/Y",strtotime("+15 days"));
		}

		$getPharmacies = $this->instModel->getPharmaciesByCommunesId($communeID);
		if ($getPharmacies) 
		{
			$response['code']=1;
		    $response['data']=$getPharmacies;
		    $response['msg']="$PeriodesGardeDebut - $PeriodesGardeFinal";
		}
		else
		{
			$response['code']=0;
	        $response['data']= '';
	        $response['msg']="$PeriodesGardeDebut - $PeriodesGardeFinal";
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

public function getListeEntrepriseByFiltre_post()
{	
	if (!empty($this->input->post('communeID')))
	{
		$query = $this->instModel->getInstitutionsByCommunesId($this->input->post('communeID'));
		if ($query) 
		{
			$response['code']=1;
		    $response['data']=$query;
		    $response['msg']="Listes des Officines Disponibles !";
		}
		else
		{
			$response['code']=0;
			$response['data']= '';
			$response['msg']="Aucune Officine Disponible !";
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


public function getEntrepriseByQuartiersId_post()
{	
	if (!empty($this->input->post('idLogin')) && !empty($this->input->post('quartierEntrepriseId')) && !empty($this->input->post('communeEntrepriseId')) && !empty($this->input->post('date_deb_res')))
	{	
		
		$quartierEntrepriseId = $this->input->post('quartierEntrepriseId');
		$communeEntrepriseId = $this->input->post('communeEntrepriseId');
		$date_deb_res = date("Y-m-d", strtotime($this->input->post('date_deb_res')));
		$indexJourVaccination = date_create($date_deb_res)->format('w');
		$query = $this->instModel->isEntrepriseByQuartiersId($quartierEntrepriseId, 
		$communeEntrepriseId, $indexJourVaccination);
		if ($query) 
		{	
			foreach ($query as $entreprise) {
				$idEntreprise = $entreprise->id_entreprise;
			}

			$getRangs = $this->resModel->getRangRdvsVaccins($idEntreprise, $date_deb_res)->nombre;
			if ((int)$getRangs > 0)
			{	
				 $randPatients = (int)$getRangs + 1;
			}
			else
			{
				 $randPatients = 1;
			}

			$response['code']=1;
		    $response['data']=$query;
		    $response['msg']="Vous êtes à la $randPatients Place !";
		}
		else
		{
			$response['code']=0;
			$response['data']= '';
			$response['msg']="Aucun Centre Disponible !";
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
