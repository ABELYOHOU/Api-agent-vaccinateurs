<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Dossiers extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Auth_model','authModel');
	$this->load->model('Dossiers_model', 'dosModel');
	$this->load->model('Cat_vaccins_model', 'catModel');
	$this->load->model('Sous_vaccins_model', 'sousModel');
	$this->load->model('Reservations_model', 'resModel');
	$this->load->model('Global_model', 'globalModel');
}


public function getCatVaccins_get()
{
	$query = $this->catModel->getCatVaccinsActifs();
	if ($query) 
	{
		$response['code']=1;
	    $response['data']=$query;
	    $response['msg']="Catégoried récuperées";
	}
	else
	{
		$response['code']=0;
		$response['data']= '';
		$response['msg']="Aucune Catégorie récuperée !";
	}

	return $this->response($response, REST_Controller::HTTP_OK);
}

public function getListeSousVaccinsByCategories_post()
{
	if (!empty($this->input->post('idCategorie')))
	{	
			$idCategorie = $this->input->post('idCategorie');
			$getSousCategories = $this->sousModel->getListesSousVaccinsByCategories($idCategorie);
			if (empty($getSousCategories)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Aucune sous catégorie existante !";
			}
			else
			{	
			    $response['code']=1;
		        $response['data']= $getSousCategories;
		        $response['msg']="Liste recuperée !";
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

public function getListeDossiersByEntreprise_post()
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
				$getListesDossiers = $this->dosModel->getListesDossiersByEntreprises($entID);
			    $response['code']=1;
		        $response['data']= $getListesDossiers;
		        $response['msg']="Liste recuperée !";
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

public function getDossiersByPatientsMobiles_post()
{
	if (!empty($this->input->post('mobileSearch')))
	{	
			$mobileSearch = $this->input->post('mobileSearch');
			$getPatients = $this->dosModel->isPatientsByMobiles($mobileSearch);
			if (empty($getPatients)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Ce Patient est Inconnu !";
			}
			else
			{	
				$getListesDossiers = $this->dosModel->getListesDossiersByPatients($getPatients->id_patients);
			    $response['code']=1;
		        $response['data']= $getListesDossiers;
		        $response['msg']="Liste des dossiers recuperée !";
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

public function getPatientsCarnetsByMobiles_post()
{
	if (!empty($this->input->post('mobileSearch')))
	{	
			$mobileSearch = $this->input->post('mobileSearch');
			$getPatients = $this->dosModel->getPatientsByMobiles($mobileSearch);
			if (empty($getPatients)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Ce Patient est Inconnu !";
			}
			else
			{	
			    $response['code']=1;
		        $response['data']= $getPatients;
		        $response['msg']="Liste des dossiers recuperée !";
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

public function addingDossiersPatientsDirects_post()
{	
	//var_dump($_POST);
	if (!empty($this->input->post('sexe_patients')) && !empty($this->input->post('idLogin')) && 
		!empty($this->input->post('nom_visiteurs')) && !empty($this->input->post('catVaccinsID')) 
		&& !empty($this->input->post('sousVaccinsID')) && !empty($this->input->post('mobile_visiteurs')))
	{	
			$idLogin = $this->input->post('idLogin');
			$nom_dossiers = strtok($this->input->post('nom_visiteurs'), ' ');
            $prenoms_dossiers = str_replace($nom_dossiers.' ', '', $this->input->post('nom_visiteurs'));
            $catVaccinsID = $this->input->post('catVaccinsID');
            $sousVaccinsID = $this->input->post('sousVaccinsID');
            $mobile_visiteurs = $this->input->post('mobile_visiteurs');
            $sexe_patients = $this->input->post('sexe_patients');

            $getVisiteurs = $this->authModel->getMonCompte($idLogin);
            $getPhone = $this->globalModel->getCodeMobile($mobile_visiteurs);
            

            $getNomVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($nom_dossiers);
		    $getPreNomsVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($prenoms_dossiers);
		    // Check that data was sent to the mailer.
	        if ($getNomVisiteurs == FALSE OR $getPreNomsVisiteurs == FALSE) 
	        {
	            $response['code']=0;
			    $response['data']= '';
			    $response['msg']="Vérifier le Champ Nom & Prénoms SVP !";
	        }
			elseif (empty($getVisiteurs)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Ce utilisateur est inconnu !";
			}
			elseif($getPhone == FALSE) 
		    {
		    	$response['code']=0;
			    $response['data']= '';
			    $response['msg']="Ce format de mobile est incorrect !";
		    }
			else
			{	
				$existance = $this->authModel->existePatientMobiles($mobile_visiteurs);
				if (empty($existance)) 
				{
					$addingDossiers = $this->dosModel->creationsDossiers($mobile_visiteurs, 
					$nom_dossiers, $prenoms_dossiers, $sexe_patients, $sousVaccinsID, $catVaccinsID, $idLogin);

					if ($addingDossiers) 
					{
						$curl = curl_init();
						curl_setopt_array($curl, array(
						  CURLOPT_URL => 'https://api.orange.com/oauth/v3/token',
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'POST',
						  CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
						  CURLOPT_HTTPHEADER => array(
						    'Content-Type: application/x-www-form-urlencoded',
						    'Authorization: Basic ZUFYOFpMRnRhbnBFMDFjcjZwRnl4dExZeHZrb3lOcGo6NjlrQkYzNmZGa1RyTXpLbQ=='
						  ),
						));
						$response1 = json_decode(curl_exec($curl), true);
						curl_close($curl);
						if (isset($response1["access_token"]))
						{
							$emailClient = str_replace(array(' ', '-'), '', $mobile_visiteurs);
				  			$message = "Bienvenue sur Vaccipha. Votre mot de passe est demo";
							$ch = curl_init();
							curl_setopt_array($ch, array(
							  CURLOPT_URL => 'https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B2250000/requests',
							  CURLOPT_RETURNTRANSFER => true,
							  CURLOPT_ENCODING => '',
							  CURLOPT_MAXREDIRS => 10,
							  CURLOPT_TIMEOUT => 0,
							  CURLOPT_FOLLOWLOCATION => true,
							  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST => 'POST',
							  CURLOPT_POSTFIELDS =>'{
								"outboundSMSMessageRequest": {
									"address": "tel:'.$emailClient.'",
									"senderAddress":"tel:+2250000",
									"senderName":"VACCIPHA",
									"outboundSMSTextMessage": {
										"message": "'.$message.'"
									}
								}
							}',
							  CURLOPT_HTTPHEADER => array(
							    'Content-Type: application/json',
							    'Authorization: Bearer '.$response1["access_token"].''
							  ),
							));
							$resultats = json_decode(curl_exec($ch), true);
							curl_close($ch);
						}

					    $response['code']=1;
				        $response['data']= $addingDossiers;
				        $response['msg']="Dossier Crée Avec Succès !";

					}
					else
					{
				        $response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Echec, Veuillez reprendre plus tard !";
				    }
					
				}
				else
				{
			        $response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Ce utilisateur existe déjà !";
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

public function addingDossiersSousTutelle_post()
{	
	if (!empty($this->input->post('sexe_patients')) && !empty($this->input->post('idLogin')) && !empty($this->input->post('nom_visiteurs')) && !empty($this->input->post('catVaccinsID')) 
		&& !empty($this->input->post('sousVaccinsID')) && !empty($this->input->post('idVisiteurs')))
	{	
			$idLogin = $this->input->post('idLogin');
			$idVisiteurs = $this->input->post('idVisiteurs');

			$nom_dossiers = strtok($this->input->post('nom_visiteurs'), ' ');
            $prenoms_dossiers = str_replace($nom_dossiers.' ', '', $this->input->post('nom_visiteurs'));
            $catVaccinsID = $this->input->post('catVaccinsID');
            $sousVaccinsID = $this->input->post('sousVaccinsID');
            $sexe_patients = $this->input->post('sexe_patients');

            $getVisiteurs = $this->authModel->getMonCompte($idLogin);
            //$getPhone = $this->dosModel->getCodeMobile($mobile_visiteurs);

            $codeVisiteurs = json_decode(str_replace('"', '', $this->input->post('idVisiteurs')));
            $isSousResponsabilite = implode('', array_map('strval', $codeVisiteurs));
            

            $getNomVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($nom_dossiers);
		    $getPreNomsVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($prenoms_dossiers);
		    $getPatients = $this->dosModel->isDossiersByPatientsID($isSousResponsabilite);
		    // Check that data was sent to the mailer.
	        if ($getNomVisiteurs == FALSE OR $getPreNomsVisiteurs == FALSE) 
	        {
	            $response['code']=0;
			    $response['data']= '';
			    $response['msg']="Vérifier le Champ Nom & Prénoms SVP !";
	        }
			elseif (empty($getVisiteurs)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Ce utilisateur est Inconnu !";
			}
			elseif (empty($getPatients)) 
		    {
		    	$response['code']=0;
			    $response['data']= '';
			    $response['msg']="Ce Patient est Inconnu !";
		    }
			else
			{	
				$existance = $this->dosModel->existeSousMyResponsabilite($isSousResponsabilite, 
				$nom_dossiers, $prenoms_dossiers);
				if (empty($existance)) 
				{
					$addingDossiers = $this->dosModel->miseSousTutelleDossiers($isSousResponsabilite, 
					$nom_dossiers, $prenoms_dossiers, $sexe_patients, $sousVaccinsID, $catVaccinsID);

				    $response['code']=1;
			        $response['data']= $addingDossiers;
			        $response['msg']="Dossier Crée Avec Succès !";
				}
				else
				{
			        $response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Ce Patient existe déjà !";
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

public function getListeDossiersByFiltre_post()
{
	if (!empty($this->input->post('idLogin')) && !empty($this->input->post('entID')) && !empty($this->input->post('labelSearch')))
	{		
			$entID = $this->input->post('entID');
			$idLogin = $this->input->post('idLogin');
			$labelSearch = $this->input->post('labelSearch');
			$getVisiteurs = $this->authModel->getMonCompte($idLogin);
			if (empty($getVisiteurs)) 
			{
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Ce utilisateur est inconnu !";
			}
			else
			{	
				$getListesDossiers = $this->dosModel->getListesDossiersByFiltres($entID, $labelSearch);
			    $response['code']=1;
		        $response['data']= $getListesDossiers;
		        $response['msg']="Liste recuperée !";
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


public function getCarnetVaccinalByPatientId_post()
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
			$getListesDossiers = $this->dosModel->getCarnetVaccinalByPatientId($idPatients);
		    $response['code']=1;
	        $response['data']= $getListesDossiers;
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



}