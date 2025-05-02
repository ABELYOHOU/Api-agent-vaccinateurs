<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Dash extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Auth_model','authModel');
	$this->load->model('Global_model', 'globalModel');
	$this->load->model('Dossiers_model', 'dosModel');
	$this->load->model('Reservations_model', 'resModel');
}

public function accueilResponsable_post()
{
	if (!empty($this->input->post('periodeLogin')) AND !empty($this->input->post('idLogin')) AND !empty($this->input->post('entID')))
	{	
		$entID = $this->input->post('entID');
		$idLogin = $this->input->post('idLogin');
		$periodeLogin = $this->input->post('periodeLogin');

		$getVisiteurs = $this->authModel->getMonCompte($idLogin);
		if (empty($getVisiteurs)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Ce utilisateur est inconnu !";
		}
		else
		{	

			if ($periodeLogin == "07 DERNIERS JOURS") 
			{	
				$date_min = date("Y-m-d 00:00:00", strtotime("-7 days"));
				$date_max = date("Y-m-d 23:59:59");
			}
			elseif ($periodeLogin == "30 DERNIERS JOURS") 
			{	
				$date_min = date("Y-m-d 00:00:00", strtotime("-30 days"));
				$date_max = date("Y-m-d 23:59:59");
			}
			else
			{
				$date_min = date("Y-m-d 00:00:00", strtotime($periodeLogin));
				$date_max = date("Y-m-d 23:59:59", strtotime($periodeLogin));
			}

			$getGlobalChiffres = $this->resModel->getGlobalComptesFonds($entID, $date_min, $date_max);
			$getRdvsConfirmes = $this->resModel->getTotalRdvsConfirmes($entID, $date_min, $date_max);
			$getTotalDossiers = $this->dosModel->getTotalDossiersByOfficines($entID, $date_min, $date_max);

			if (empty($getGlobalChiffres)) 
			{
				$nbreGlobalChiffres = 0;
				$sommeGlobalChiffres = 0;
				$partsOfficines = 0;
				$partsvaccipha = 0;
			}
			else
			{
				$nbreGlobalChiffres = (int)$getGlobalChiffres->nombre;
				$sommeGlobalChiffres = (float)$getGlobalChiffres->montant;
				$partsOfficines = (float)$nbreGlobalChiffres*350;
				$partsvaccipha = (float)$nbreGlobalChiffres*150;
			}

			if (empty($getRdvsConfirmes)) 
			{
				$nbreRdvConfirmes = 0;
			}
			else
			{
				$nbreRdvConfirmes = (int)$getRdvsConfirmes->nombre;
			}

			if (empty($getTotalDossiers)) 
			{
				$nbreTotalDossiers = 0;
			}
			else
			{
				$nbreTotalDossiers = (int)$getTotalDossiers->nombre;
			}


			$response['code']=1;
		    $response['data']= array('nbreGlobalChiffres' => $nbreGlobalChiffres , 'sommeGlobalChiffres' => $sommeGlobalChiffres, 'partsOfficines' => $partsOfficines, 'partsvaccipha' => $partsvaccipha, 'nbreRdvConfirmes' => $nbreRdvConfirmes, 'nbreTotalDossiers' => $nbreTotalDossiers);
		    $response['msg']="Connecté(e) !";
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

public function accueilPointFocale_post()
{
	if (!empty($this->input->post('periodeLogin')) AND !empty($this->input->post('idLogin')) AND !empty($this->input->post('entID')))
	{	
		$entID = $this->input->post('entID');
		$idLogin = $this->input->post('idLogin');
		$periodeLogin = $this->input->post('periodeLogin');

		$getVisiteurs = $this->authModel->getMonCompte($idLogin);
		if (empty($getVisiteurs)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Ce utilisateur est inconnu !";
		}
		else
		{	

			if ($periodeLogin == "07 DERNIERS JOURS") 
			{	
				$date_min = date("Y-m-d 00:00:00", strtotime("-7 days"));
				$date_max = date("Y-m-d 23:59:59");
			}
			elseif ($periodeLogin == "30 DERNIERS JOURS") 
			{	
				$date_min = date("Y-m-d 00:00:00", strtotime("-30 days"));
				$date_max = date("Y-m-d 23:59:59");
			}
			else
			{
				$date_min = date("Y-m-d 00:00:00", strtotime($periodeLogin));
				$date_max = date("Y-m-d 23:59:59", strtotime($periodeLogin));
			}

			$getTotalRdvAFaire = $this->resModel->getOfficinesStatsByStatus('P', $entID, $date_min, 
			$date_max);
			$getRdvsConfirmes = $this->resModel->getOfficinesStatsByStatus('S', $entID, $date_min, 
			$date_max);
			$getVaccinsAdmin = $this->resModel->getStatsVaccinsByOfficine($entID, $date_min, $date_max);
			$getTotalDossiers = $this->dosModel->getTotalDossiersByOfficines($entID, $date_min, $date_max);
			if (empty($getTotalRdvAFaire)) 
			{
				$nbreRDVsPending = 0;
			}
			else
			{
				$nbreRDVsPending = (int)$getTotalRdvAFaire->nombre;
			}

			if (empty($getRdvsConfirmes)) 
			{
				$nbreRdvConfirmes = 0;
			}
			else
			{
				$nbreRdvConfirmes = (int)$getRdvsConfirmes->nombre;
			}

			if (empty($getTotalDossiers)) 
			{
				$nbreTotalDossiers = 0;
			}
			else
			{
				$nbreTotalDossiers = (int)$getTotalDossiers->nombre;
			}

			if (empty($getVaccinsAdmin)) 
			{
				$nbreVaccinsAdmin = 0;
			}
			else
			{
				$nbreVaccinsAdmin = (int)$getVaccinsAdmin->nombre;
			}


			$response['code']=1;
		    $response['data']= array('nbreRDVsPending' => $nbreRDVsPending , 'nbreRdvConfirmes' => $nbreRdvConfirmes, 'nbreTotalDossiers' => $nbreTotalDossiers, 'nbreVaccinsAdmin' => $nbreVaccinsAdmin);
		    $response['msg']="Connecté(e) !";
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

//Liste 
public function getListesRDVsEtRappelsAVenir_post()
{
	if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('entID')))
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
			$getLastReservations = $this->resModel->getListesRDVsEtRappelsAVenir($entID);
			$response['code']=1;
		    $response['data']= $getLastReservations;
		    $response['msg']="Connecté(e) !";
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