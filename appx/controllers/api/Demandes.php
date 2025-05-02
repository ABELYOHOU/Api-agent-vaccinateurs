<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Demandes extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Demandes_model', 'demandesModel');
	$this->load->model('Auth_model', 'authModel');
}

public function creerDemandes_post()
{	
	if (/*!empty($this->input->post('entID'))*/  !empty($this->input->post('idLogin')) AND !empty($this->input->post('qteDemandes')) AND !empty($this->input->post('stockDemandeID')) AND !empty($this->input->post('commentDemandes')) AND !empty($this->input->post('vaccinsDemandesID')) )
	{	
        $qteDemandes = $this->input->post('qteDemandes');
        $stockDemandeID = $this->input->post('stockDemandeID');
        $commentDemandes = $this->input->post('commentDemandes');
         $vaccinsDemandesID = $this->input->post('vaccinsDemandesID');
		/*$entID = $this->input->post('entID');*/
		$idLogin = $this->input->post('idLogin');

		$getVisiteurs = $this->authModel->getMonCompte($idLogin);
		if (empty($getVisiteurs)) 
		{
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="utilisateur Non Connecté !";
		}
		else
		{
			$addingDemende = $this->demandesModel->insererDemandes($qteDemandes, $stockDemandeID, 
            $vaccinsDemandesID, $commentDemandes, $codeDemandes);
			if ($addingDemende) 
			{
				$response['code']=1;
			    $response['data']=$addingDemende;
			    $response['msg']="Avis ajouté avec succès !";
			}
			else
			{
				$response['code']=0;
				$response['data']= '';
				$response['msg']="Prière reprendre plus tard SVP !";
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




}

?>