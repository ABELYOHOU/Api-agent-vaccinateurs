<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Reservations extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Auth_model','authModel');
	$this->load->model('Global_model', 'globalModel');
	$this->load->model('Dossiers_model', 'dosModel');
	$this->load->model('Vaccins_model', 'vaccinsModel');
	$this->load->model('Mapis_model', 'mapisModel');
	$this->load->model('Reservations_model', 'resModel');
}

public function getListeRDVsForMapisByPatientsId_post()
{
	if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')) AND !empty($this->input->post('idPatients')))
	{	
			$entID = $this->input->post('entID');
			$idLogin = $this->input->post('idLogin');
			$idPatients = $this->input->post('idPatients');
			$getPhone = $this->dosModel->isDossiersByPatientsID($idPatients);
			$getVisiteurs = $this->authModel->getMonCompte($idLogin);
			if (empty($getVisiteurs)) 
			{
				  $response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Ce utilisateur est inconnu !";
			}
			elseif(empty($getVisiteurs)) 
		  {
		    	$response['code']=0;
			    $response['data']= '';
			    $response['msg']="Ce Patient Est Inconnu !";
		  }
			else
			{	
				  $getReservations = $this->mapisModel->getListeRDVsByPatientsId($entID, $idPatients);
				  $response['code']=1;
			    $response['data']= $getReservations;
			    $response['msg']="Liste affichée !";
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

public function getListePaiementsByFiltre_post()
{	

	if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')) 
		  AND !empty($this->input->post('periodeLogin')))
	{	
			$entID = $this->input->post('entID');
			$idLogin = $this->input->post('idLogin');
			$periodeLogin = $this->input->post('periodeLogin');
			$getVisiteurs = $this->authModel->getMonCompte($idLogin);
			if (empty($getVisiteurs)) 
			{
				  $response['code']=0;
		  		$response['data']= array();
		  		$response['msg']="Ce compte est inconnu !";
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

						$getPaiements = $this->resModel->getListePaiementsByOfficine($entID, $date_min, $date_max);
						if ($getPaiements) 
						{
							  $response['code']=1;
						    $response['data']=$getPaiements;
						    $response['msg']="Liste des paiements !";
						}
						else
						{
							$response['code']=0;
							$response['data']=array();
							$response['msg']="Aucun paiement filtré !";
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

public function getListeRDVsByMobile_post()
{
		if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')) AND !empty($this->input->post('mobilePatients')))
		{	
				$entID = $this->input->post('entID');
				$idLogin = $this->input->post('idLogin');
				$mobilePatients = $this->input->post('mobilePatients');
				$getPhone = $this->globalModel->getCodeMobile($mobilePatients);
				$getVisiteurs = $this->authModel->getMonCompte($idLogin);
				if (empty($getVisiteurs)) 
				{
					  $response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Ce utilisateur est inconnu !";
				}
				elseif($getPhone == FALSE) 
	      {
			    	$response['code']=0;
				    $response['data']= '';
				    $response['msg']="Ce Format de Mobile Est Incorrect !";
	      }
				else
				{	
					  $getLastReservations = $this->resModel->getListeRDVsByMobile($entID, $mobilePatients);
					  $response['code']=1;
				    $response['data']= $getLastReservations;
				    $response['msg']="Liste affichée !";
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

public function getListeRDVsByPatientsIds_post()
{
	if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')) AND !empty($this->input->post('idPatients')))
	{	
		$entID = $this->input->post('entID');
		$idLogin = $this->input->post('idLogin');
		$idPatients = $this->input->post('idPatients');
		$getPhone = $this->dosModel->isDossiersByPatientsID($idPatients);
		$getVisiteurs = $this->authModel->getMonCompte($idLogin);
		if (empty($getVisiteurs)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Ce utilisateur est inconnu !";
		}
		elseif(empty($getVisiteurs)) 
	    {
	    	$response['code']=0;
		    $response['data']= '';
		    $response['msg']="Ce Patient Est Inconnu !";
	    }
		else
		{	
			$getReservations = $this->resModel->getListeRDVsByByPatientsIds($entID, $idPatients);
			$response['code']=1;
		    $response['data']= $getReservations;
		    $response['msg']="Liste affichée !";
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

public function getListeTotalRDVsEtRappels_post()
{
		if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')))
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
					  $getLastReservations = $this->resModel->getListeTotalRDVsEtRappels($entID);
					  $response['code']=1;
				    $response['data']= $getLastReservations;
				    $response['msg']="Liste affichée !";
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

public function getListeRDVsEtRappelsByDate_post()
{
	if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')) && !empty($this->input->post('dateDebRes')))
	{		
			$entID = $this->input->post('entID');
			$idLogin = $this->input->post('idLogin');
			$dateDebRes = $this->input->post('dateDebRes');
			$getVisiteurs = $this->authModel->getMonCompte($idLogin);
			if (empty($getVisiteurs)) 
			{
					  $response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Ce utilisateur est inconnu !";
			}
			elseif (DateTime::createFromFormat('d-m-Y', $dateDebRes) == FALSE)
	    {
	          $response['code']=0;
		  		  $response['data']= '';
		  		  $response['msg']="Ce format de date est incorrect $dateDebRes!";
	    }
			else
			{	
				  $getLastReservations = $this->resModel->getTotalRDVsEtRappelsByDate($entID, $dateDebRes);
				  $response['code']=1;
			    $response['data']= $getLastReservations;
			    $response['msg']="Liste affichée par date !";
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

public function getRDVsEtRappelsByTypeRes_post()
{
	if (!empty($this->input->post('entID')) && !empty($this->input->post('idLogin')) && !empty($this->input->post('typeResCode')))
	{	
		
		$entID = $this->input->post('entID');
		$idLogin = $this->input->post('idLogin');
		$typeResCode = $this->input->post('typeResCode');
		$getVisiteurs = $this->authModel->getMonCompte($idLogin);
		if (empty($getVisiteurs)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Ce utilisateur est inconnu !";
		}
		else
		{	
			if ($typeResCode == 'RAPPELS') 
			{
				$typeResCodeS = 'R';
			}
			else
			{
				$typeResCodeS = 'V';
			}

			$getLastReservations = $this->resModel->getRDVsEtRappelsByTypeRes($entID, $typeResCodeS);
			$response['code']=1;
		    $response['data']= $getLastReservations;
		    $response['msg']="Liste affichée par type !";
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

public function confirmerReservations_post()
{	
		if (!empty($this->input->post('entID')) 
			AND !empty($this->input->post('idLogin')) AND !empty($this->input->post('refCommande')) 
			AND !empty($this->input->post('idVaccins')))
		{		
				$modePaieID = 1;
				$refCommande = $this->input->post('refCommande');
				$entID = $this->input->post('entID');
				$idLogin = $this->input->post('idLogin');
				$numeroLots = $this->input->post('numeroLots');

				$idVaccinsINT = json_decode(str_replace('"', '', $this->input->post('idVaccins')));
        $idVaccins = array_map('strval', $idVaccinsINT);

				$getVisiteurs = $this->authModel->getMonCompte($idLogin);
				$getCommandes = $this->resModel->isCodeRes($refCommande);
				if (empty($getVisiteurs)) 
				{
					  $response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Cet utilisateur est inconnu !";
				}
				elseif (empty($getCommandes))
				{
					  $response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Ce RDV ou rappel est inexistant !";
				}
				else
				{	
						$addingRes = $this->resModel->confirmerReservations($getCommandes->id_res, 
						$numeroLots, $getCommandes->montant_res, $getCommandes->patientsResId, 
						$entID, $idLogin, $idVaccins, $modePaieID);
						if ($addingRes) 
						{
							  $response['code']=1;
						    $response['data']=array('refCommande' => $refCommande);
						    $response['msg']="RDV confirmé avec succès !";
						}
						else
						{
							  $response['code']=0;
							  $response['data']= '';
							  $response['msg']="Erreur système, Reprendre plus tard SVP !";
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

public function validerResPourAutreOfficines_post()
{	
		if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')) 
			AND !empty($this->input->post('refCommande')) AND !empty($this->input->post('idVaccins')))
		{	
				$refCommande = $this->input->post('refCommande');
				$entID = $this->input->post('entID');
				$idLogin = $this->input->post('idLogin');

				$idVaccinsINT = json_decode(str_replace('"', '', $this->input->post('idVaccins')));
        $idVaccins = array_map('strval', $idVaccinsINT);

				$getVisiteurs = $this->authModel->getMonCompte($idLogin);
				$getCommandes = $this->resModel->isCodeRes($refCommande);
				if (empty($getVisiteurs)) 
				{
					  $response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Cet utilisateur est inconnu !";
				}
				elseif (empty($getCommandes))
				{
					  $response['code']=0;
			  		$response['data']= '';
			  		$response['msg']="Ce RDV ou rappel est inexistant !";
				}
				else
				{	
						$addingRes = $this->resModel->validerResPourAutreOfficine($getCommandes->id_res, $idVaccins);
						if ($addingRes) 
						{
							  $response['code']=1;
						    $response['data']=array('refCommande' => $refCommande);
						    $response['msg']="RDV confirmé avec succès !";
						}
						else
						{
							$response['code']=0;
							$response['data']= '';
							$response['msg']="Erreur système, Reprendre plus tard SVP !";
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

public function validerPaiements_post()
{	
	    // var_dump($_POST);
	   //exit();
		if (!empty($this->input->post('purchaseinfo')) AND !empty($this->input->post('reference')) 
			AND !empty($this->input->post('montant')) 
			AND !empty($this->input->post('contact')) AND !empty($this->input->post('provider')))
		{
			  //on vérifie si une session existe
		    $purchaseinfo = $this->input->post('purchaseinfo');
		    $montant = $this->input->post('montant');
		    $status = (int)$this->input->post('status');
		    $reference = $this->input->post('reference');
		    $contact = $this->input->post('contact');
		    $operatorTrans = $this->input->post('provider');

		    if ($purchaseinfo)
		    {
		        $getRecus = $this->resModel->isCodeRes($purchaseinfo);
		        if ($getRecus) 
		        {
		            $id = $this->resModel->payerTrans($status, $operatorTrans, $getRecus->id_res, 
                $getRecus->montant_res, $reference, $contact);

				        if ($id)
				        {   
			              $response['code']=1;
	    	            $response['data']=$purchaseinfo;
	    	            $response['msg']="Votre paiement est un succes !";
				        }
				        else
				        {
				            $response['code']=0;
				            $response['data']= '';
				            $response['msg']="Erreur système, Priere contacter le support !";
				        }

		        }
		        else
		        {
		        	$response['code']=0;
			        $response['data']= '';
			        $response['msg']="Cette reference de paiement n est pas autorisee !";
		        }

		    }
		    else
		    {
		        $response['code']=0;
		        $response['data']= '';
		        $response['msg']="Votre paiement a echoue !";
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

public function creerResToutVenantsMoMo_post()
{	

	//var_dump($_POST);
	//exit();
	if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('mobilePatients')) 
		AND !empty($this->input->post('idVaccins')) AND !empty($this->input->post('sousVaccinsID')) 
		AND !empty($this->input->post('catVaccinsID')) AND !empty($this->input->post('entID')) 
		AND !empty($this->input->post('nomPatients')) AND !empty($this->input->post('sexePatients'))
	  AND !empty($this->input->post('idCommunes')))
	{	
		  $code_res = 'V'.$this->globalModel->generateUnik();
          $montant_res = 500;
          $idLogin = $this->input->post('idLogin');
          $numeroLots = $this->input->post('numeroLots');
          //Liste des vaccins (tableau)

          // Utilisez json_decode() pour convertir la chaîne en un tableau PHP
          $idVaccinsINT = json_decode(str_replace('"', '', $this->input->post('idVaccins')));
          $idVaccins = array_map('strval', $idVaccinsINT);
          //var_dump($idVaccins);
          //exit();

          $nomPatients = $this->input->post('nomPatients');
          $sexePatients = $this->input->post('sexePatients');
          $mobilePatients = $this->input->post('mobilePatients');
          $entID = $this->input->post('entID');
          $idCommunes = $this->input->post('idCommunes');
          $catVaccinsID = $this->input->post('catVaccinsID');
          $sousVaccinsID = $this->input->post('sousVaccinsID');

          $date_res_deb = date("Y-m-d H:00:00");
          $date_res_end = date("Y-m-d H:00:00", strtotime("$date_res_deb +1 hours"));
          $idPlageHoraires = 3;

          $canalID = $this->input->post('canalID');
          if (empty($canalID)) {
          	$canalID = 8;
          }

          $existance = $this->resModel->isCodeRes($code_res);
          $getVisiteurs = $this->authModel->getMonCompte($idLogin);
          $getExistePatients = $this->dosModel->getPatientsByIsMobiles($mobilePatients);
          $getPhone = $this->globalModel->getCodeMobile($mobilePatients);

          $getLastSaisieByUsers = $this->resModel->isGetDernierInsertResByUsers($idLogin);
          if ($getLastSaisieByUsers) 
          {
	          	$dateDebut = DateTime::createFromFormat('Y-m-d H:i:s', $getLastSaisieByUsers->date_create_res);
				$dateFin = new DateTime();
				$difference = $dateDebut->diff($dateFin);

				// Conversion de l'intervalle en secondes
                $diffEnSecondes = $difference->days * 24 * 60 * 60 + $difference->h * 60 * 60 + $difference->i * 60 + $difference->s;

          }
            
          if (!empty($getLastSaisieByUsers) AND (int)$diffEnSecondes < 60) 
		  {
				$response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Prière espacer vos saisies de 1 minute !";
		  }
		  elseif($getPhone == FALSE) 
		  {
		    	$response['code']=0;
			    $response['data']= '';
			    $response['msg']="Ce Format de Mobile Est Incorrect !";
		  }
		  elseif ($getExistePatients)
		  {
				$response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Ce Patient Existe Déjà !";
		  }		  
		  elseif (empty($getVisiteurs)) 
		  {
				$response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Cet utilisateur est inconnu !";
		  }
          elseif (empty($existance))
          {		

          	  $nom_patients = strtok($this->input->post('nomPatients'), ' ');
              $prenoms_patients = str_replace($nom_patients.' ', '', $this->input->post('nomPatients'));

              $mot_de_passe = strtoupper(substr($prenoms_patients, 0, 1)).rand(10000, 99999);
              $ClientsId = $this->authModel->creationsToutVenants($nom_patients, 
              $prenoms_patients, $sexePatients, $mobilePatients, 
              $idCommunes, $mot_de_passe, $idLogin, $catVaccinsID, $sousVaccinsID);


              $resID = $this->resModel->createResPaieMobileMoney($code_res, $entID, $ClientsId, 
              $montant_res, $date_res_deb, $date_res_end, $idPlageHoraires, 
              $sousVaccinsID, $catVaccinsID, count($idVaccins), null, $idLogin, $numeroLots);

                //foreach ($idVaccins as $vaccins)
                for ($i=0; $i < count($idVaccins); $i++) 
                {
                   $this->resModel->createVaccinations($entID, $resID, $idVaccins[$i], 
                   $ClientsId, null, 'V');

                   /*
                   $getVaccins = $this->vaccinsModel->isVaccinsActifs($idVaccins[$i]);
                   if ($getVaccins && (int)$getVaccins->isDoseUnique == 0) {

                      for ($j=1; $j < (int)$getVaccins->nombreDoses; $j++) { 

                        $nombre = (int)$getVaccins->nombreDoses*$j;
                        $codeRes = 'R'.$this->globalModel->generateUnik();
                        $dateCreateRes = date("Y-m-d", strtotime("$date_res_end +$nombre days")).' 07:00:00';

                        $dateResDebut = date("Y-m-d H:i:s", strtotime("$dateCreateRes +$idPlageHoraires hours"));
                        $dateResFinal = date("Y-m-d H:i:s", strtotime("$dateResDebut +1 hours"));


                        //$nombreDosesRestantes = (int)$getVaccins->isDoseUnique - (int)$i;
                        $idRes = $this->resModel->createResVaccins($codeRes, $entID, 
                        $ClientsId, $montant_res, $dateResDebut, $dateResFinal, 
                        $idPlageHoraires, $sousVaccinsID, $catVaccinsID, 1, $resID, 'R');

                        $this->resModel->createVaccinations($entID, $idRes, $idVaccins[$i], 
                        $ClientsId, $resID, 'R');

                      }

                    }
                    */
                }

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
					$emailClient = str_replace(array(' ', '-'), '', $mobilePatients);
		  		    $message = "Bienvenue sur Vaccipha. Votre mot de passe est $mot_de_passe";
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

				$this->authModel->createCanalPatients($ClientsId, $canalID);

	            $response['code']=1;
		        $response['data']=$code_res;
		        $response['msg']="Succès, Votre RDV est enregistré !";
	             
          }
          else
          {
                $response['code']=0;
	      		$response['data']='';
	      		$response['msg']="Erreur système, Veuillez reprendre SVP !";
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


public function creerCommandesToutVenants_post()
{	

	//var_dump($_POST);
	//exit();
	if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('mobilePatients')) 
		AND !empty($this->input->post('idVaccins')) AND !empty($this->input->post('sousVaccinsID')) 
		AND !empty($this->input->post('catVaccinsID')) AND !empty($this->input->post('entID')) 
		AND !empty($this->input->post('nomPatients')) AND !empty($this->input->post('sexePatients'))
	  AND !empty($this->input->post('idCommunes')))
	{	
		  $code_res = 'V'.$this->globalModel->generateUnik();
          $montant_res = 500;
          $idLogin = $this->input->post('idLogin');
          $numeroLots = $this->input->post('numeroLots');
          //Liste des vaccins (tableau)

          // Utilisez json_decode() pour convertir la chaîne en un tableau PHP
          $idVaccinsINT = json_decode(str_replace('"', '', $this->input->post('idVaccins')));
          $idVaccins = array_map('strval', $idVaccinsINT);
          //var_dump($idVaccins);
          //exit();

          $canalID = $this->input->post('canalID');
          if (empty($canalID)) {
          	$canalID = 8;
          }

          $nomPatients = $this->input->post('nomPatients');
          $sexePatients = $this->input->post('sexePatients');
          $mobilePatients = $this->input->post('mobilePatients');
          $entID = $this->input->post('entID');
          $idCommunes = $this->input->post('idCommunes');
          $catVaccinsID = $this->input->post('catVaccinsID');
          $sousVaccinsID = $this->input->post('sousVaccinsID');

          $date_res_deb = date("Y-m-d H:00:00");
          $date_res_end = date("Y-m-d H:00:00", strtotime("$date_res_deb +1 hours"));
          $idPlageHoraires = 3;

          $existance = $this->resModel->isCodeRes($code_res);
          $getVisiteurs = $this->authModel->getMonCompte($idLogin);
          $getExistePatients = $this->dosModel->getPatientsByIsMobiles($mobilePatients);
          $getPhone = $this->globalModel->getCodeMobile($mobilePatients);

          $getLastSaisieByUsers = $this->resModel->isGetDernierInsertResByUsers($idLogin);
          if ($getLastSaisieByUsers) 
          {
	          	$dateDebut = DateTime::createFromFormat('Y-m-d H:i:s', $getLastSaisieByUsers->date_create_res);
				$dateFin = new DateTime();
				$difference = $dateDebut->diff($dateFin);

				// Conversion de l'intervalle en secondes
                $diffEnSecondes = $difference->days * 24 * 60 * 60 + $difference->h * 60 * 60 + $difference->i * 60 + $difference->s;

          }
            
          if (!empty($getLastSaisieByUsers) AND (int)$diffEnSecondes < 60) 
		  {
				$response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Prière espacer vos saisies de 1 minute !";
		  }
		  elseif($getPhone == FALSE) 
		  {
		    	$response['code']=0;
			    $response['data']= '';
			    $response['msg']="Ce Format de Mobile Est Incorrect !";
		  }
		  elseif ($getExistePatients)
		  {
				$response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Ce Patient Existe Déjà !";
		  }		  
		  elseif (empty($getVisiteurs)) 
		  {
				$response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Cet utilisateur est inconnu !";
		  }
          elseif (empty($existance))
          {		

          	  $nom_patients = strtok($this->input->post('nomPatients'), ' ');
              $prenoms_patients = str_replace($nom_patients.' ', '', $this->input->post('nomPatients'));

              $mot_de_passe = strtoupper(substr($prenoms_patients, 0, 1)).rand(10000, 99999);
              $ClientsId = $this->authModel->creationsToutVenants($nom_patients, 
              $prenoms_patients, $sexePatients, $mobilePatients, 
              $idCommunes, $mot_de_passe, $idLogin, $catVaccinsID, $sousVaccinsID);


              $resID = $this->resModel->createResTransVaccins($code_res, $entID, $ClientsId, 
              $montant_res, $date_res_deb, $date_res_end, $idPlageHoraires, 
              $sousVaccinsID, $catVaccinsID, count($idVaccins), null, $idLogin, $numeroLots);

                //foreach ($idVaccins as $vaccins)
                for ($i=0; $i < count($idVaccins); $i++) 
                {
                   $this->resModel->createVaccinations($entID, $resID, $idVaccins[$i], 
                   $ClientsId, null, 'V');

                   /*
                   $getVaccins = $this->vaccinsModel->isVaccinsActifs($idVaccins[$i]);
                   if ($getVaccins && (int)$getVaccins->isDoseUnique == 0) {

                      for ($j=1; $j < (int)$getVaccins->nombreDoses; $j++) { 

                        $nombre = (int)$getVaccins->nombreDoses*$j;
                        $codeRes = 'R'.$this->globalModel->generateUnik();
                        $dateCreateRes = date("Y-m-d", strtotime("$date_res_end +$nombre days")).' 07:00:00';

                        $dateResDebut = date("Y-m-d H:i:s", strtotime("$dateCreateRes +$idPlageHoraires hours"));
                        $dateResFinal = date("Y-m-d H:i:s", strtotime("$dateResDebut +1 hours"));


                        //$nombreDosesRestantes = (int)$getVaccins->isDoseUnique - (int)$i;
                        $idRes = $this->resModel->createResVaccins($codeRes, $entID, 
                        $ClientsId, $montant_res, $dateResDebut, $dateResFinal, 
                        $idPlageHoraires, $sousVaccinsID, $catVaccinsID, 1, $resID, 'R');

                        $this->resModel->createVaccinations($entID, $idRes, $idVaccins[$i], 
                        $ClientsId, $resID, 'R');

                      }

                    }
                    */
                }

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
					$emailClient = str_replace(array(' ', '-'), '', $mobilePatients);
		  		    $message = "Bienvenue sur Vaccipha. Votre mot de passe est $mot_de_passe";
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

				$this->authModel->createCanalPatients($ClientsId, $canalID);

	            $response['code']=1;
		        $response['data']=$idVaccins;
		        $response['msg']="Succès, Votre RDV est enregistré !";
	             
          }
          else
          {
                $response['code']=0;
	      		$response['data']='';
	      		$response['msg']="Erreur système, Veuillez reprendre SVP !";
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

public function creerCommandesCarnetsExistants_post()
{	

	//var_dump($_POST);
	//exit();
	if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('idPatients')) 
		AND !empty($this->input->post('idVaccins')) AND !empty($this->input->post('entID')))
	{	
		  $code_res = 'V'.$this->globalModel->generateUnik();
          $montant_res = 500;
          $entID = $this->input->post('entID');
          $numeroLots = $this->input->post('numeroLots');
          $idLogin = $this->input->post('idLogin');
          //Liste des vaccins (tableau)

          // Utilisez json_decode() pour convertir la chaîne en un tableau PHP
          $idVaccinsINT = json_decode(str_replace('"', '', $this->input->post('idVaccins')));
          $idVaccins = array_map('strval', $idVaccinsINT);
          //var_dump($idVaccins);
          //exit();

          $ClientsId = $this->input->post('idPatients');

          // $idPatientsINT = json_decode(str_replace('"', '', $this->input->post('idPatients')));
          // $ClientsId = implode('', array_map('strval', $idPatientsINT));


          $date_res_deb = date("Y-m-d H:00:00");
          $date_res_end = date("Y-m-d H:00:00", strtotime("$date_res_deb +1 hours"));
          $idPlageHoraires = 3;

          $existance = $this->resModel->isCodeRes($code_res);
          $getVisiteurs = $this->authModel->getMonCompte($idLogin);	
          $getPatients = $this->dosModel->isDossiersByPatientsID($ClientsId);	


          $getLastSaisieByUsers = $this->resModel->isGetDernierInsertResByUsers($idLogin);
          if ($getLastSaisieByUsers) 
          {
	          	$dateDebut = DateTime::createFromFormat('Y-m-d H:i:s', $getLastSaisieByUsers->date_create_res);
				$dateFin = new DateTime();
				$difference = $dateDebut->diff($dateFin);

				// Conversion de l'intervalle en secondes
                $diffEnSecondes = $difference->days * 24 * 60 * 60 + $difference->h * 60 * 60 + $difference->i * 60 + $difference->s;

          }
            
          if (!empty($getLastSaisieByUsers) AND (int)$diffEnSecondes < 60) 
		  {
				$response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Prière espacer vos saisies de 1 minute !";
		  }		  		  
		  elseif (empty($getVisiteurs)) 
		  {
				  $response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Cet utilisateur est inconnu !";
		  }
		  elseif (empty($getPatients)) 
		  {
				  $response['code']=0;
		  		$response['data']= '';
	  			$response['msg']="Cet Patient Est Inconnu !";
		  }
          elseif (empty($existance))
          {		

              $resID = $this->resModel->createResTransVaccins($code_res, $entID, $ClientsId, 
              $montant_res, $date_res_deb, $date_res_end, $idPlageHoraires, 
              $getPatients->sousVaccinsID, $getPatients->catVaccinsID, count($idVaccins), 
              null, $idLogin, $numeroLots);

                //foreach ($idVaccins as $vaccins)
                for ($i=0; $i < count($idVaccins); $i++) 
                {
                   $this->resModel->createVaccinations($entID, $resID, $idVaccins[$i], 
                   $ClientsId, null, 'V');

                   /*
                   $getVaccins = $this->vaccinsModel->isVaccinsActifs($idVaccins[$i]);
                   if ($getVaccins && (int)$getVaccins->isDoseUnique == 0) {

                      for ($j=1; $j < (int)$getVaccins->nombreDoses; $j++) { 

                        $nombre = (int)$getVaccins->nombreDoses*$j;
                        $codeRes = 'R'.$this->globalModel->generateUnik();
                        $dateCreateRes = date("Y-m-d", strtotime("$date_res_end +$nombre days")).' 07:00:00';

                        $dateResDebut = date("Y-m-d H:i:s", strtotime("$dateCreateRes +$idPlageHoraires hours"));
                        $dateResFinal = date("Y-m-d H:i:s", strtotime("$dateResDebut +1 hours"));


                        //$nombreDosesRestantes = (int)$getVaccins->isDoseUnique - (int)$i;
                        $idRes = $this->resModel->createResVaccins($codeRes, $entID, 
                        $ClientsId, $montant_res, $dateResDebut, $dateResFinal, 
                        $idPlageHoraires, $getPatients->sousVaccinsID, $getPatients->catVaccinsID, 
                        1, $resID, 'R');

                        $this->resModel->createVaccinations($entID, $idRes, $idVaccins[$i], 
                        $ClientsId, $resID, 'R');

                      }

                    }
                    */
                }

	            $response['code']=1;
        		$response['data']=$idVaccins;
        		$response['msg']="Succès, Votre RDV est enregistré !";
	             
          }
          else
          {
              $response['code']=0;
	      	  $response['data']='';
	      	  $response['msg']="Erreur système, Veuillez reprendre SVP !";
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