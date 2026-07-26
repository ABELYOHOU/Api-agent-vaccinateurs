			<?php
			defined('BASEPATH') OR exit('No direct script access allowed');

			require APPPATH . 'libraries/REST_Controller.php';
			require APPPATH . 'libraries/Format.php';

			class Vaccinateurs extends REST_Controller {

			public function __construct()
			{
				parent::__construct();
				$this->load->model('Auth_model','authModel');
				$this->load->model('Global_model', 'globalModel');
				$this->load->model('Agences_model', 'agencesModel');
				$this->load->model('Institutions_model', 'instModel');
				$this->load->model('Vaccinateurs_model', 'vacModel');
				$this->load->model('Dossiers_model', 'dosModel');
				$this->load->library('mailjet');
			}



public function insertPosition_post()
{
    if (
        !empty($this->input->post('idLogin')) &&
        !empty($this->input->post('latitude_agent_positions')) &&
        !empty($this->input->post('longitude_agent_positions'))
    ) {

        $agentID = $this->input->post('idLogin');
        $patientPositionsID = $this->input->post('patientsResId');
        $lat = $this->input->post('latitude_agent_positions');
        $lng = $this->input->post('longitude_agent_positions');

        $refCommande = $this->input->post('refCommande');

    
        $getCommandes = $this->vacModel->isCodeRes($refCommande);

        if (!$getCommandes) {
            $response['code'] = 0;
            $response['data'] = '';
            $response['msg']  = "Commande introuvable !";
            return $this->response($response, REST_Controller::HTTP_OK);
        }

   
        $inserted = $this->vacModel->insertPosition(
            $agentID,
            $getCommandes->id_res,
            $lat,
            $lng,
            $patientPositionsID
        );

        if ($inserted) {
            $response['code'] = 1;
            $response['data'] = $inserted;
            $response['msg']  = "Position ajoutée avec succès !";
        } else {
            $response['code'] = 0;
            $response['data'] = '';
            $response['msg']  = "Position non enregistrée !";
        }

    } else {
        $response['code'] = 0;
        $response['data'] = '';
        $response['msg']  = "Vérifier les variables envoyées !";
    }

    return $this->response($response, REST_Controller::HTTP_OK);
}


	   
	    public function commandeTraitement_post()
		{	
				if (!empty($this->input->post('vaccinateurID')) AND !empty($this->input->post('idLogin')) 
					AND !empty($this->input->post('refCommande')))
				{	
						$vaccinateurID = $this->input->post('vaccinateurID');
						$refCommande = $this->input->post('refCommande');
						$idLogin = $this->input->post('idLogin');
						$id_res = $this->input->post('id_res');
						
						$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
						$getCommandes = $this->vacModel->isCodeRes($refCommande);
						$statusActif = $this->vacModel->getDernierStatusActif($getCommandes->id_res);


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
					  		$response['msg']="Cette commande est inexistant !";
						}

						elseif (empty($statusActif))
						{
							$response['code']=0;
					  		$response['data']= '';
					  		$response['msg']="Aucun statut actif trouvé pour cette commande !";
						}

						else
						{	


						$addingRes = $this->vacModel->commandeTraitement($getCommandes->id_res, $vaccinateurID, 
									$statusActif->date_create_status_cmd, $statusActif->date_initiale_deb, 
									$statusActif->date_initiale_end);

								if ($addingRes) 
								{
									$response['code']=1;
								    $response['data']=array('refCommande' => $refCommande);
								    $response['msg']="Agent se met en route !";
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

   
    public function getPositionActive_post() {

        $agentID = $this->input->post('agentsVaccinateursPositionID');
        $resID   = $this->input->post('resPositionID');

        $query = $this->vacModel->getPositionActive($agentID, $resID);

       if ($query) 
	 {
		$response['code']=1;
	    $response['data']=$query;
	    $response['msg']="Dernière Position de l'agent récuperée";
	 }
	else
	{
		$response['code']=0;
		$response['data']= '';
		$response['msg']="Aucune positions active pour le moment !";
	}

	return $this->response($response, REST_Controller::HTTP_OK);
    }


  

    public function historiqueTrajet_get()

	{
		$query = $this->vacModel->getHistoriquePositions($resID);

		if ($query) 
		{
		    $response['code']=1;
		    $response['data']=$query;
		    $response['msg']="Liste des historiques de positions récupérées";
		}
		else
		{
			$response['code']=0;
			$response['data']= '';
			$response['msg']="Aucune positions récuperée !";
		}

		return $this->response($response, REST_Controller::HTTP_OK);
	}


 public function FinaliserCommandes_post()
    {
		      if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('refCommande')) 
		    	AND !empty($this->input->post('numeroLots')) AND !empty($this->input->post('idVaccins'))  
		    	AND !empty($this->input->post('vaccinateurID')) AND !empty($this->input->post('modePaieID'))
                  AND (
                  (int)$this->input->post('modePaieID') == 1 
                   OR !empty($this->input->post('referencePaiment'))
    )
		    )
		{

			$refCommande   = $this->input->post('refCommande');
			$modePaieID = $this->input->post('modePaieID');
			$referencePaiment = $this->input->post('referencePaiment');


			$vaccinateurID = $this->input->post('vaccinateurID');
			$numeroLots    = $this->input->post('numeroLots');
			$qteProduits    = $this->input->post('qteProduits');
			$montant_res = $this->input->post('montant_res');
			$idLogin = $this->input->post('idLogin');


	        $ClientsId = $this->input->post('idPatients');
			$patientsResId = $this->input->post('patientsResId');
			$getPatients = $this->dosModel->isDossiersByPatientsID($ClientsId);
			  
			$getCommandes = $this->vacModel->isCodeRes($refCommande);

			$statusActif = $this->vacModel->getDernierStatusActif($getCommandes->id_res);

		    $date_res_deb = date("Y-m-d H:00:00");
            $date_res_end = date("Y-m-d H:00:00", strtotime("$date_res_deb +1 hours"));

			if (empty($getCommande)) 
			{

				$response['code']=0;
			  	$response['data']= '';
		  		$response['msg']="Cette commande est inexistante !";
			}

	            $idVaccinsPOST = $this->input->post('idVaccins');


		   if (empty($idVaccinsPOST))
		    {

	 	    $idVaccins = [];

		    }
		    else 
		    {

	       $decoded = json_decode($idVaccinsPOST, true);

	       if (is_array($decoded)) 
	       {

	        $idVaccins = array_map('strval', $decoded);

	        } 
	        else

	        {
	       
	        $idVaccins = [(string) $decoded];

	       }
	     }


		  $resID = $this->vacModel->FinaliserCommandes($numeroLots,$getCommandes->code_res, $statusActif->date_create_status_cmd, $montant_res, count($idVaccins),null, $date_res_deb , $date_res_end, $ClientsId, $modePaieID, $referencePaiment,$vaccinateurID
			);

			 //foreach ($idVaccins as $vaccins)
              for ($i=0; $i < count($idVaccins); $i++) 
             {
                   $this->vacModel->createVaccinations($resID, $idVaccins[$i], 
                   $patientsResId, null, 'V');
                   
              }

			return $this->response([
				"code" => 1,
				"data" => ["refCommande" => $refCommande],
				"msg" => "Commande finalisée avec succès !"
			], REST_Controller::HTTP_OK);
		}

		else
		{
			$response['code']=0;
			$response['data']= '';
			$response['msg']="Vérifier les variables envoyées";
		}
	}

	public function getCatVaccinsHorsPev_get()
	{
		$query = $this->vacModel->getCatVaccinsHorsPevActifs();

		if ($query) 
		{
			$response['code']=1;
		    $response['data']=$query;
		    $response['msg']="Catégorie hors pev récuperées";
		}
		else
		{
			$response['code']=0;
			$response['data']= '';
			$response['msg']="Aucune Catégorie récuperée !";
		}

		return $this->response($response, REST_Controller::HTTP_OK);
	}



	public function getCatVaccinsPev_get()
	{
		$query = $this->vacModel->getCatVaccinsPevActifs();

		if ($query) 
		{
			$response['code']=1;
		    $response['data']=$query;
		    $response['msg']="Catégorie pev récuperées";
		}
		else
		{
			$response['code']=0;
			$response['data']= '';
			$response['msg']="Aucune Catégorie récuperée !";
		}

		return $this->response($response, REST_Controller::HTTP_OK);
	}



	public function getListesSousVaccinsPevByCategories_post()
	{
		if (!empty($this->input->post('idCategorie')))
		{	
				$idCategorie = $this->input->post('idCategorie');
				$getSousCategories = $this->vacModel->getListesSousVaccinsPevByCategories($idCategorie);

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


	 public function getListesSousVaccinsByCatChoisi_post()
{
	if (!empty($this->input->post('idCategorie')))
	{	
			$idCategorie = $this->input->post('idCategorie');
			
			$getSousCategories = $this->vacModel->getListesSousVaccinsByCatChoisi($idCategorie);
			
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



	public function getCatVaccins_get()
	{
		$query = $this->vacModel->getCatVaccinsActifs();

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



 	public function getCatSousVaccins_get()
	{
		$query = $this->vacModel->getSousVaccinsPevActifs();

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


	public function transfererCommande_post()

		{
			if (!empty($this->input->post('vaccinateurID')) AND !empty($this->input->post('idLogin')) 
					AND !empty($this->input->post('refCommande')))

				{	
						$vaccinateurID = $this->input->post('vaccinateurID');
						$refCommande = $this->input->post('refCommande');
						$idLogin = $this->input->post('idLogin');
						$motifStatusCmdID = $this->input->post('motifStatusCmdID');
						$id_res = $this->input->post('id_res');
					

						$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
						$getCommandes = $this->vacModel->isCodeRes($refCommande);
						$statusActif = $this->vacModel->getDernierStatusActif($getCommandes->id_res);
					
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
					  		$response['msg']="Cette commande est inexistant !";
						}
						elseif (empty($statusActif))
						{
							$response['code']=0;
					  		$response['data']= '';
					  		$response['msg']="Aucun statut actif trouvé pour cette commande !";
						}
						else
						{	
								$addingRes = $this->vacModel->transfererCommandes($getCommandes->id_res,
								   $vaccinateurID, $statusActif->date_create_status_cmd,
									$motifStatusCmdID);

								if ($addingRes) 
								{
									  $response['code']=1;
								    $response['data']=array('refCommande' => $refCommande);
								    $response['msg']="Agent a tranféré la commande !";
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



		public function reporterCommande_post()
		{	
				if (!empty($this->input->post('vaccinateurID')) AND !empty($this->input->post('idLogin')) 
					AND !empty($this->input->post('refCommande')))
				{	
						$vaccinateurID = $this->input->post('vaccinateurID');
						$refCommande = $this->input->post('refCommande');
						$idLogin = $this->input->post('idLogin');
						$motifStatusCmdID = $this->input->post('motifStatusCmdID');
						$id_res = $this->input->post('id_res');
						

						$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
						$getCommandes = $this->vacModel->isCodeRes($refCommande);
						$statusActif = $this->vacModel->getDernierStatusActif($getCommandes->id_res);
						$getVaccinateursAgents = $this->vacModel->isGetVaccinateur($vaccinateurID);
					
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
					  		$response['msg']="Cette commande est inexistant !";
						}
						elseif (empty($statusActif))
						{
							$response['code']=0;
					  		$response['data']= '';
					  		$response['msg']="Aucun statut actif trouvé pour cette commande !";
						}

						elseif (empty($getVaccinateursAgents))
						{
							$response['code']=0;
					  		$response['data']= '';
					  		$response['msg']="Cet Agent est inconnu !";
						}
						else
						{	
								$addingRes = $this->vacModel->reporterCommandes($getCommandes->id_res, $vaccinateurID,$statusActif->date_create_status_cmd,
									$motifStatusCmdID);
								if ($addingRes) 
								{
									$response['code']=1;
								    $response['data']=array('refCommande' => $refCommande);
								    $response['msg']="Agent a reporté la commande !";
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




		public function commandeEffectuee_post()
		{	
				if (!empty($this->input->post('vaccinateurID')) AND !empty($this->input->post('idLogin')) 
					AND !empty($this->input->post('refCommande')))

				{	
						$vaccinateurID = $this->input->post('vaccinateurID');
						$refCommande = $this->input->post('refCommande');
						$idLogin = $this->input->post('idLogin');
						$getCommandes = $this->vacModel->isCodeRes($refCommande);
						$id_res = $this->input->post('id_res');
						$statusActif = $this->vacModel->getDernierStatusActif($getCommandes->id_res);


						$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
						

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
					  		$response['msg']="Cette commande est inexistant !";
						}

						elseif (empty($statusActif))
						{
							$response['code']=0;
					  		$response['data']= '';
					  		$response['msg']="Aucun statut actif trouvé pour cette commande !";
						}
						else
						{	
								$addingRes = $this->vacModel->commandeEffectuee($getCommandes->id_res, $vaccinateurID,$statusActif->date_create_status_cmd, $statusActif->date_initiale_deb, 
									$statusActif->date_initiale_end);

								if ($addingRes) 
								{
									  $response['code']=1;
								    $response['data']=array('refCommande' => $refCommande);
								    $response['msg']="Agent à terminé le service de vaccination !";
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



		



			public function getPatientsCarnetsByMobiles_post()
			{
				if (!empty($this->input->post('mobileSearch')))
				{	
						$mobileSearch = $this->input->post('mobileSearch');
						$getPatients = $this->vacModel->getPatientsByMobiles($mobileSearch);

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



			public function nombreCommandesDuJourEnCours_post()

			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					

					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);

					if (empty($getVisiteurs)) 
					{
						  $response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getGlobalChiffres = $this->vacModel->nombreCommandesDuJourEnCours($vaccinateurID);

					  	$response['code']=1;
					    $response['data']= $getGlobalChiffres;
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

			

			public function accueilVaccinateurs_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');

					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getMontantTotalEffectueChiffres = $this->vacModel->getMontantTotalEffectue($vaccinateurID);
						$getMontantTotalPendingChiffres = $this->vacModel->getMontantTotalPending($vaccinateurID);
						$getCommandesConfirmes = $this->vacModel->getTotalCommandesConfirmes($vaccinateurID);
						$getCommandesPending = $this->vacModel->getTotalCommandesPending($vaccinateurID);
						$getNombreCommandesEnCoursByDay = $this->vacModel->nombreCommandesDuJourEnCours($vaccinateurID);
						$getNombreCommandesEffectueesByDay = $this->vacModel->nombreCommandesDuJourEffectuee($vaccinateurID);
						$getCommandesPending = $this->vacModel->getTotalCommandesPending($vaccinateurID);
						$nombreCommandesDuJourByAgent = $this->vacModel->nombreCommandesDuJourByAgent($vaccinateurID);

						if (empty($nombreCommandesDuJourByAgent)) 
						{
							$nbreTotalCmd = 0;
						}
						else
						{
							$nbreTotalCmd = (int)$nombreCommandesDuJourByAgent->nombre;
						}

						// NOMBRE DE COMMANDES EN COURS DU JOURS

						if (empty($getNombreCommandesEnCoursByDay)) 
						{
							$nbreCmdEnCours = 0;
						}
						else
						{
							$nbreCmdEnCours = (int)$getNombreCommandesEnCoursByDay->nombre;
						}

			             // NOMBRE DE COMMANDES EFFECTUEE DU JOURS

						if (empty($getNombreCommandesEffectueesByDay)) 
						{
							$nbreCmdEffectuee = 0;
						}
						else
						{
							$nbreCmdEffectuee = (int)$getNombreCommandesEffectueesByDay->nombre;
						}

						// GET POURCENTAGE

						if ((int)$nbreTotalCmd == 0) 
						   {
			                   $pourcentage = 0;
			               } 
			               else 
			               {
			                   $pourcentage = ($nbreCmdEffectuee / $nbreTotalCmd) * 100;

			               }

						// CHIFFRE D'AFFAIRE 

						if (empty($getMontantTotalEffectueChiffres)) 
						{
							$montantConfirmes = 0;
						}
						else
						{
							$montantConfirmes = (int)$getMontantTotalEffectueChiffres->montant;
						}
						///////////////////////////

						if (empty($getMontantTotalPendingChiffres)) 
						{
							$montantPending = 0;
						}
						else
						{
							$montantPending = (int)$getMontantTotalPendingChiffres->montant;
						}

						if (empty($getCommandesConfirmes)) 
						{
							$nbreCmdConfirmes = 0;
						}
						else
						{
							$nbreCmdConfirmes = (int)$getCommandesConfirmes->nombre;
						}

						if (empty($getCommandesPending)) 
						{
							$nbreCmdDPending = 0;
						}
						else
						{
							$nbreCmdDPending = (int)$getCommandesPending->nombre;
						}



						$response['code']=1;
					    $response['data']= array('montantConfirmes' => $montantConfirmes , 'montantPending' => $montantPending,'nbreCmdConfirmes' => $nbreCmdConfirmes, 'nbreCmdDPending' => $nbreCmdDPending, 'nbreCmdEnCours' => $nbreCmdEnCours,'nbreCmdEffectuee' => $nbreCmdEffectuee, 'pourcentage' => $pourcentage, 'nbreTotalCmd' => $nbreTotalCmd);
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


			public function getListCommandesDuJourEnCours_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);

					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListCommandesDuJourEnCours($vaccinateurID);
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

			public function getListCommandes_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')) AND !empty($this->input->post('periodeLogin')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$periodeLogin = $this->input->post('periodeLogin');


					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
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


						$getLastReservations = $this->vacModel->getListCommandes($vaccinateurID,$date_min, $date_max);
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

			public function getAllCommandes_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					


					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{

						$getLastReservations = $this->vacModel->getAllCommandes($vaccinateurID);
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

			public function getListCommandesEnCoursByAgents_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')) 
					AND !empty($this->input->post('periodeLogin')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$periodeLogin = $this->input->post('periodeLogin');

					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
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


						$getLastReservations = $this->vacModel->getListCommandesEnCoursByAgents($vaccinateurID,$date_min, $date_max);
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


			public function getListCommandesEnCoursBySemaine_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);

					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListCommandesEnCoursBySemaine($vaccinateurID);
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

			public function getListCommandesTermineesByAgents_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListCommandesTermineesByAgents($vaccinateurID);
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

			public function getAllCommandesByAgents_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getAllCommandesByAgent($vaccinateurID);
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

			public function getListCommandesTransfererByAgents_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListCommandesTransfererByAgents($vaccinateurID);
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

			public function getListCommandesEnTraitementByAgents_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListCommandesEnTraitementByAgents($vaccinateurID);
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

			public function getListCommandesAnnuleesByAgents_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListCommandesAnnuleesByAgents($vaccinateurID);
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

			public function getListCommandesReporteesByAgents_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListCommandesReporteesByAgents($vaccinateurID);
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

			public function getListDetailTransfertCommandes_post()
			{
				if (!empty($this->input->post('idLogin')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListDetailTransfertCommandes($vaccinateurID);
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

			public function getListZoneAffecterByAgents_post()
			{
				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('vaccinateurID')))
				{	
					$vaccinateurID = $this->input->post('vaccinateurID');
					$idLogin = $this->input->post('idLogin');
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{	
						$getLastReservations = $this->vacModel->getListZoneAffecterByAgents($vaccinateurID);
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

			public function login_post()
			{
				if (!empty($this->input->post('loginLogin')) AND !empty($this->input->post('passLogin')))
				{		
					$login = $this->input->post('loginLogin');
					$password = $this->input->post('passLogin');
					$query = $this->vacModel->isIdentifier($login, $password);

					if ($query) 
					{	
			            $getConnectivite = $this->globalModel->getConnectivitesV($query->id_vaccinateurs);

						if ($getConnectivite) 
						{
							$this->globalModel->createConnectiviteV($query->id_vaccinateurs);
							$response['code']=1;
						    $response['data']=$query;
						    $response['msg']="Bienvenue sur Vaccipha Plus !";
						}
						else
						{
							//Diriger le client vers la page de changement de mot de passe par défaut
							$response['code']=1;
						    $response['data']=$query;
						    $response['msg']="Bienvenu(e) sur Vaccipha Plus !";
						}

					}
					else
					{	
						$response['code']=0;
				  		$response['data']= array('id_vaccinateurs' => 'Aucun');
				  		$response['msg']="Cet agent est inconnu !";
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



			public function majPassword_post()
			{ 
			    if (!empty($this->input->post('rpassword')) && !empty($this->input->post('password')) 
			    	&& !empty($this->input->post('idLogin'))) 
				{	
			        $rpassword = $this->input->post('rpassword');
			        $password = $this->input->post('password');
			        $idLogin = $this->input->post('idLogin');
				    
				    $getVisiteurs = $this->authModel->getMonCompte($this->input->post('idLogin'));
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="utilisateur Non Connecté !";
					}
					else
					{	
						if ($this->input->post('password') == $this->input->post('rpassword'))
				        {
				            if (strlen($this->input->post('password')) >= 6)
				            {	
				                $completed = $this->authModel->changer_mot_passe($idLogin, $this->input->post('password'));
				                if ($completed)
				                {  
				                    $this->globalModel->createConnectivite($idLogin);
							  		$response['code']=1;
							  		$response['data']= '';
							  		$response['msg']="Succès, Mot de passe modifié !";
				                }
				                else
				                { 
				                    $response['code']=0;
							  		$response['data']= '';
							  		$response['msg']="Veuillez réessayer plus tard SVP !";
				                }
				            }
				            else
				            { 
				                $response['code']=0;
						  		$response['data']= '';
						  		$response['msg']="Longueur de mot de passe incorrecte !";
				            }
				        }
				        else
				        { 
				            $response['code']=0;
					  		$response['data']= '';
					  		$response['msg']="Les Mots de Passe Sont Différents !";
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


			public function password_post()
			{
				if (!empty($this->input->post('loginLogin')) AND !empty($this->input->post('passLogin')) AND !empty($this->input->post('password')) AND !empty($this->input->post('rpassword')))
				{		
					$loginLogin = $this->input->post('loginLogin');
					$passLogin = $this->input->post('passLogin');
					$password = $this->input->post('password');
					$rpassword = $this->input->post('rpassword');
					$query = $this->vacModel->isIdentifier($loginLogin, $passLogin);
					if (empty($query)) 
					{	
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce Mot de Passe est incorrect !";
					}
					elseif ($password != $rpassword) 
					{	
						$response['code']=0;
			            $response['data']= '';
			            $response['msg']="Les mots de passe sont différents !";
					}
					else
					{	
						$getMiseJours = $this->vacModel->changer_mot_passe($query->id_vaccinateurs, $password);
				  		if ($getMiseJours)
					  	{
							$response['code']=1;
					        $response['data']= '';
					        $response['msg']="Mot de passe mis à jour !";
					  	}
						else
						{
							$response['code']=0;
			            	$response['data']= '';
			            	$response['msg']="Oups, veuillez réessayer plus tard SVP !";
						}

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


		public function reinitialize_post()
		{	
			if (!empty($this->input->post('loginLogin')) && !empty($this->input->post('typeLogin'))) 
			{	
		        $loginLogin = $this->input->post('loginLogin');
		        $typeLogin = $this->input->post('typeLogin');

		        $query = $this->vacModel->existeVisiteurs($loginLogin);
				if ($query) 
				{	
					$id_vaccinateurs = $query->id_vaccinateurs;
				  	$nom_vaccinateurs = $query->nom_vaccinateurs;


				  	if ($typeLogin == 'S') 
				  	{
				  	
					  	$getNombreReinitialise = $this->globalModel->getAgentByReinitialize($id_vaccinateurs);
					  	if ((int)$getNombreReinitialise->nombre >= 2)
					  	{
					  		$response['code']=0;
						    $response['data']= '';
						    $response['msg']="Nombre quotidien limite de SMS atteint. Contactez le Support SVP !";
					  	}
					  	else
					  	{	
			  				
			  				$this->globalModel->createReinitializeV($id_vaccinateurs, 'A');

					  		$mot_de_passe = strtoupper(substr($nom_vaccinateurs, 0, 2)).rand(10000, 99999);
					  	    $getMiseJours = $this->vacModel->changer_mot_passe($id_vaccinateurs, $mot_de_passe);
					  		if ($getMiseJours)
						  	{
						  		$message = 'Bonjour.%20Votre%20mot%20de%20passe%20est%20:%20'.$mot_de_passe.'';				
					  			//$mobile_visiteurs = str_replace('+', '', $emailClient);
					  			$mobile_visiteurs = $query->mobile_vaccinateurs;

					            $curl = curl_init();
					            curl_setopt_array($curl, array(
					              CURLOPT_URL => 'https://app.smspro.africa/api/v3/sms/send?recipient='.$mobile_visiteurs.'&sender_id=VACCIPHA&type=plain&message='.$message.'',
					              CURLOPT_RETURNTRANSFER => true,
					              CURLOPT_ENCODING => '',
					              CURLOPT_MAXREDIRS => 10,
					              CURLOPT_TIMEOUT => 0,
					              CURLOPT_FOLLOWLOCATION => true,
					              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					              CURLOPT_CUSTOMREQUEST => 'POST',
					              CURLOPT_HTTPHEADER => array(
					                'Authorization: Bearer 991|thLHOMOtvWj4QoXNrhvhrdXQN981PrFtOIIHhHAVcc6c4e0b',
					                'Content-Type: application/json',
					                'Accept: application/json'
					              ),
					            ));
					            $response1 = json_decode(curl_exec($curl), true);
					            curl_close($curl);
								if (isset($response1['status']) && $response1['status'] == "success")
						        {
						        	$response['code']=1;
							        $response['data']= '';
							        $response['msg']="Succès, Consulter votre mobile !";
							    }
							    else
							    {
									$response['code']=0;
						        	$response['data']= '';
						        	$response['msg']="L'utilisateur n'a pas pu être notifié !";
							    }		
					       
						    }
							else
							{
								$response['code']=0;
						        $response['data']= '';
						        $response['msg']="Mise à jour impossible !";
							}
					  	}
				  	}
				  	else
					{
				        $mot_de_passe = strtoupper(substr($nom_vaccinateurs, 0, 1)).rand(10000, 99999);
						$getMiseJours = $this->vacModel->changer_mot_passe($id_vaccinateurs, $mot_de_passe);
				  		if ($getMiseJours)
					  	{
		                    //var_dump($loginLogin);

							$message = "<p>Bonjour ".$nom_vaccinateurs.",</p>       
				                <span>Pour le changement de votre mot de passe, veuillez vous connecter avec le mot de passe temporaire.</span> <br />
				                <span>Votre mot de passe temporaire est : ".$mot_de_passe."</span> <br />
				                <span>Veuillez noter que lors de votre prochaine connexion, nous vous suggérons de changer ce mot de passe temporaire dans votre espace utilisateur.</span><br \>
				                <span>Cordialement,</span> <br /> <br />        
				                <p>Equipe Vaccipha</p>
				                <p>Cet e-mail a été envoyé automatiquement. Merci de ne pas y répondre.</p>
				                <p>Pour plus d'informations, contactez le +225 25 22 01 86 44 ou envoyer un email à vaccipha@enovpharm.com</p>";
				                $messageid = $this->mailjet->emailing($loginLogin, "MOT DE PASSE TEMPORAIRE", 
				                $message);

				            if ($messageid) 
				            {
				                $response['code']=1;
						        $response['data']= '';
						        $response['msg']="Succès, Consulter votre boîte à lettre !";
				            }
				            else
				            {
				                $response['code']=0;
						        $response['data']= '';
						        $response['msg']="Veuillez contacter le support SVP !";
				            }

				        }
				        else
						{
					        $response['code']=0;
					        $response['data']= '';
					        $response['msg']="Adresse Email Inconnue !";
						}

					}

				}
				else
				{
			        $response['code']=0;
			        $response['data']= '';
			        $response['msg']="Mobile ou Email Inconnu(e) !";
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
			public function getMonCompte_post()
			{	

				if (!empty($this->input->post('idLogin')))
				{	
					$idLogin = $this->input->post('idLogin');
					$getComptes = $this->vacModel->getMonCompte($idLogin);
					if (empty($getComptes)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce compte est inconnu !";
					}
					else
					{	
						$response['code']=1;
					    $response['data']=$getComptes;
					    $response['msg']="Mon compte est récuperé !";
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

			public function majMonCompte_post()
			{	

				if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('nom_vaccinateurs')) AND !empty($this->input->post('prenoms_vaccinateurs')))
				{	
					$idLogin = $this->input->post('idLogin');
					$nom_vaccinateurs = $this->input->post('nom_vaccinateurs');
					$prenoms_vaccinateurs = $this->input->post('prenoms_vaccinateurs');
					$getNomVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($nom_vaccinateurs);
				    $getPreNomsVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($prenoms_vaccinateurs);
				    // Check that data was sent to the mailer.
					$getComptes = $this->vacModel->getMonCompte($idLogin);
					if (empty($getComptes)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce compte est inconnu !";
					}
					elseif ($getNomVisiteurs == FALSE OR $getPreNomsVisiteurs == FALSE) 
				    {
				    	$response['code']=0;
					    $response['data']= '';
					    $response['msg']="Le nom et/ou prénom(s) est incorrect !";
				    }
					else
					{	
						$addIng = $this->vacModel->majMonCompte($idLogin, $nom_vaccinateurs, $prenoms_vaccinateurs);
						if ($addIng) 
						{
							$response['code']=1;
						    $response['data']=$idLogin;
						    $response['msg']="Mon compte est mis à jour !";
						}
						else
						{	
							$response['code']=0;
					  		$response['data']= '';
					  		$response['msg']="Impossible à mettre à jour !";
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

			public function getVaccinsBySousCategories_post()
			{
				if (!empty($this->input->post('idSousCategorie')))
				{	
						$idSousCategorie = $this->input->post('idSousCategorie');
						$getListesVaccins = $this->vacModel->getListeVaccinsBySousVaccins($idSousCategorie);
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
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
					if (empty($getVisiteurs)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Ce utilisateur est inconnu !";
					}
					else
					{
						$getPatients = $this->vacModel->isDossiersByPatientsID($idPatients);
						if (empty($getPatients)) 
						{
							$response['code']=0;
					  		$response['data']= '';
					  		$response['msg']="Ce carnet est introuvable !";
						}
						else
						{	
							$getListesVaccins = $this->vacModel->getListeVaccinsBySousVaccins($getPatients->sousVaccinsID);
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
					$getVisiteurs = $this->vacModel->getMonCompte($idLogin);
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
						$getListesVaccins = $this->vacModel->getListeVaccinsBySousVaccins($getPatients->sousVaccinsID);
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
					$getRes = $this->vacModel->isCodeRes($codeRes);
					if (empty($getRes)) 
					{
						$response['code']=0;
				  		$response['data']= '';
				  		$response['msg']="Cette Réservation N'existe Pas !";
					}
					else
					{	
						$getListesVaccins = $this->vacModel->getVaccinations($getRes->id_res);
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