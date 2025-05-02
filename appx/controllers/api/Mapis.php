<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Mapis extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Mapis_model', 'mapisModel');
	$this->load->model('Auth_model','authModel');
	$this->load->model('Global_model', 'globalModel');
	$this->load->model('Reservations_model', 'resModel');
	$this->load->library('mailjet');
}

public function getListeMapis_post()
{	

	if (!empty($this->input->post('entID')) AND !empty($this->input->post('idLogin')))
	{	
		$entID = $this->input->post('entID');
		$idLogin = $this->input->post('idLogin');
		$getVisiteurs = $this->authModel->getMonCompte($idLogin);
		if (empty($getVisiteurs)) 
		{
			$response['code']=0;
	  		$response['data']= array();
	  		$response['msg']="Ce compte est inconnu !";
		}
		else
		{	
			$getListeMapis = $this->mapisModel->getMapis($entID);
			if ($getListeMapis) 
			{
				$response['code']=1;
			    $response['data']=$getListeMapis;
			    $response['msg']="Liste des Mapis !";
			}
			else
			{
				$response['code']=0;
				$response['data']= array();
				$response['msg']="Aucune Mapi Pour le Moment !";
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


public function creationsMapis_post()
{
    //on vérifie si une session existe
    if (!empty($this->input->post('idLogin')) &&  !empty($this->input->post('entID')) 
    	&& !empty($this->input->post('districtsID')) && !empty($this->input->post('codeRes')) 
    	&& !empty($this->input->post('observationsMapis')))
	{    
          $entID = $this->input->post('entID');
          $idLogin = $this->input->post('idLogin');
          $districtsID = $this->input->post('districtsID');
          //$idDossiersPatients = $this->input->post('idDossiersPatients');
          $codeRes = $this->input->post('codeRes');
          $observationsMapis = $this->input->post('observationsMapis');

          $getRes = $this->resModel->isCodeRes($codeRes);
          $existe = $this->mapisModel->existance($getRes->patientsResId, $getRes->id_res);     
          if(empty($getRes)) 
          {
              $response['code']=0;
		      		$response['data']= '';
		      		$response['msg']="Aucune Correspondance Pour Ce RDV !";
          }
          elseif (empty($existe))
          {
              $addingID = $this->mapisModel->insererMapis($getRes->patientsResId, $getRes->id_res, $observationsMapis,
              $idLogin, $entID, $districtsID);
              if ($addingID) 
              {    
                  $message = "<p>Bonjour,</p>       
                  <span>Une Mapi a été déclarée sur Vaccipha.</span> <br />
                  <span>Prière vous connecter à votre espace Manager pour les détails.</span> <br />
                  <span>Cordialement,</span> <br /> <br />        
                  <p>Equipe de Enovpharm</p>
                  <p>Cet e-mail a été envoyé automatiquement. Merci de ne pas y répondre.</p>
                  <p>Pour plus d'informations, contactez le +225 25 22 01 86 44 ou envoyer un email à vaccipha@enovpharm.com</p>";
                  $this->mailjet->emailing('vaccipha@enovpharm.com', "OBSERVATION DE MAPI", $message);

                  $response['code']=1;
				          $response['data']= $addingID;
				          $response['msg']="Mapi Signalée avec succès !";

             }
             else
             {   
                $response['code']=0;
				        $response['data']= '';
				        $response['msg']="Opération impossible !";
             }

          }
          else
          {   
              $response['code']=0;
				      $response['data']= '';
				      $response['msg']="Cette Mapi Existe !";
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

?>