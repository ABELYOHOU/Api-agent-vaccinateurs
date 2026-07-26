<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Auth extends REST_Controller {

public function __construct()
{
	parent::__construct();
	$this->load->model('Auth_model','authModel');
	$this->load->model('Global_model', 'globalModel');
	$this->load->model('Agences_model', 'agencesModel');
	$this->load->model('Institutions_model', 'instModel');
	$this->load->library('mailjet');
}

public function login_post()
{
	if (!empty($this->input->post('loginLogin')) AND !empty($this->input->post('passLogin')))
	{		
		$login = $this->input->post('loginLogin');
		$password = $this->input->post('passLogin');
		$query = $this->authModel->isIdentifier($login, $password);
		if ($query) 
		{	

			$this->globalModel->createConnectivite($query->id_users);
			$response['code']=1;
		    $response['data']=$query;
		    $response['msg']="Bienvenue sur Vaccipha !";

			/*   
            $getConnectivite = $this->globalModel->getConnectivites($query->id_users);
			if ($getConnectivite) 
			{
				$this->globalModel->createConnectivite($query->id_users);
				$response['code']=1;
			    $response['data']=$query;
			    $response['msg']="Bienvenue sur Vaccipha !";
			}
			else
			{
				//Diriger le client vers la page de changement de mot de passe par défaut
				$response['code']=1;
			    $response['data']=$query;
			    $response['msg']="Bienvenu(e) sur Vaccipha !";
			}
			*/

		}
		else
		{	
			$response['code']=0;
	  		$response['data']= array('id_users' => 'Aucun');
	  		$response['msg']="Ce utilisateur est inconnu !";
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


//  mettre a jour le mot de pass a la 1er connexion

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
		$query = $this->authModel->isIdentifier($loginLogin, $passLogin);
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
			$getMiseJours = $this->authModel->changer_mot_passe($query->id_users, $password);
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

public function createCompteEntreprises_post()
{	
	//log_message('info', $this->input->post('nom_visiteurs'));
	if (!empty($this->input->post('typeEntrepriseID')) AND !empty($this->input->post('respo_entreprise')) AND !empty($this->input->post('contact_entreprise')) AND !empty($this->input->post('contact2_entreprise')) AND !empty($this->input->post('isAgree')) AND !empty($this->input->post('communeID'))AND !empty($this->input->post('nom_entreprise')) AND !empty($this->input->post('email_entreprise')) AND !empty($this->input->post('situationGeoEntreprise')))
	{		


		$nom_users = strtok($this->input->post('respo_entreprise'), ' ');
        $prenoms_users = str_replace($nom_users.' ', '', $this->input->post('respo_entreprise'));

        $contact_entreprise = $this->input->post('contact_entreprise');
		$communeID = $this->input->post('communeID');
		$isAgree = $this->input->post('isAgree');
		$respo_entreprise = $this->input->post('respo_entreprise');


		$contact_entreprise = $this->input->post('contact_entreprise');
		$contact2_entreprise = $this->input->post('contact2_entreprise');
		$email_entreprise = $this->input->post('email_entreprise');
		$situationGeoEntreprise = $this->input->post('situationGeoEntreprise');
		$nom_entreprise = $this->input->post('nom_entreprise');
		$typeEntrepriseID = $this->input->post('typeEntrepriseID');

        $getPhone = $this->globalModel->getCodeMobile($contact_entreprise,$contact2_entreprise);
        $query = $this->authModel->existeEntreprises($contact_entreprise,$contact2_entreprise, $email_entreprise);
        // Check that data was sent to the mailer.
        $getNomVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($nom_users);
	    $getPreNomsVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($prenoms_users);
	    // Check that data was sent to the mailer.
        if ($getNomVisiteurs == FALSE OR $getPreNomsVisiteurs == FALSE) 
        {
            $response['code']=0;
		    $response['data']= '';
		    $response['msg']="Vérifier les champs renseignés SVP !";
        }
        elseif ((int)$typeEntrepriseID != 1 && (int)$typeEntrepriseID != 2) 
        {
            $response['code']=0;
		    $response['data']= '';
		    $response['msg']="Veuillez choisir Homme ou Femme !";
        }
        elseif ( $isAgree != true ) 
        {
            // Set a 400 (bad request) response code and exit.
            $response['code']=0;
		    $response['data']= '';
		    $response['msg']="Veuillez accepter nos conditions SVP !";
        } 
        elseif ($email_entreprise && filter_var($email_entreprise, FILTER_VALIDATE_EMAIL) == FALSE) 
        {
            $response['code']=0;
			$response['data']= '';
			$response['msg']="Cette Adresse Email Est Incorrecte !";
        } 
	    elseif($getPhone == FALSE) 
	    {
	    	$response['code']=0;
		    $response['data']= '';
		    $response['msg']="Ce Format de Mobile Est Incorrect !";
	    }
		elseif (empty($query)) 
		{	
			$isAgree = 1;
	    	$addingRes = $this->instModel->insererClients($typeEntrepriseID, $nom_entreprise,
            $respo_entreprise, $situationGeoEntreprise, $contact_entreprise, $contact2_entreprise, $email_entreprise, 
            $communeID, $isAgree, $nom_users, $prenoms_users);

			if ($addingRes) 
			{	
				$messageADMIN = "<p>Bonjour ".$nom_entreprise.",</p>       
		        <span>Une demande d'inscription à Vaccipha a été envoyé avec succès.</span> <br />
		        <span>Prière vous connecter au Manager pour une prise en charge.</span><br \>
		        <span>Cordialement,</span> <br /> <br />        
		        <p>Equipe de Vaccipha</p>
		        <p>Cet e-mail a été envoyé automatiquement. Merci de ne pas y répondre.</p>
		        <p>Pour plus d'informations, contactez le +2252522018644 ou envoyer un email à vaccipha@enovpharm.com</p>";
		        $this->mailjet->emailing('vaccipha@enovpharm.com', "INSCRIPTION D'UNE INSTITUTION", $messageADMIN);

		    	$response['code']=1;
				$response['data']=$addingRes;
				$response['msg']="Votre Officine est enregistrée avec succès !";
			}
			else
			{	
				$response['code']=0;
		  		$response['data']= '';
		  		$response['msg']="Veuillez ressayer ou contacter le support !";
			}
		}
		else
		{	
			$response['code']=0;
	  		$response['data']= '';
	  		$response['msg']="Ce Mobile Existe Déjà!";
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

        $query = $this->authModel->existeVisiteurs($loginLogin);
		if ($query) 
		{	
			$id_users = $query->id_users;
		  	$nom_users = $query->nom_users;

		  	if ($typeLogin == 'S') 
		  	{
		  	
			  	$getNombreReinitialise = $this->globalModel->getUsersByReinitialize($id_users);
			  	if ((int)$getNombreReinitialise->nombre >= 2)
			  	{
			  		$response['code']=0;
				    $response['data']= '';
				    $response['msg']="Nombre quotidien limite de SMS atteint. Contactez le Support SVP !";
			  	}
			  	else
			  	{	
	  				
	  				$this->globalModel->createReinitialize($id_users, 'U');

			  		$mot_de_passe = strtoupper(substr($nom_users, 0, 2)).rand(10000, 99999);
			  	    $getMiseJours = $this->authModel->changer_mot_passe($id_users, $mot_de_passe);
			  		if ($getMiseJours)
				  	{
				  		$message = 'Bonjour.%20Votre%20mot%20de%20passe%20est%20:%20'.$mot_de_passe.'';				
			  			$mobile_visiteurs = str_replace('+', '', $emailClient);

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
		        $mot_de_passe = strtoupper(substr($nom_users, 0, 1)).rand(10000, 99999);
				$getMiseJours = $this->authModel->changer_mot_passe($id_users, $mot_de_passe);
		  		if ($getMiseJours)
			  	{

					$message = "<p>Bonjour ".$nom_users.",</p>       
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
		$getComptes = $this->authModel->getMonCompte($idLogin);
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

	if (!empty($this->input->post('idLogin')) AND !empty($this->input->post('noms_visiteurs')) AND !empty($this->input->post('prenoms_visiteurs')))
	{	
		$idLogin = $this->input->post('idLogin');
		$nom_users = $this->input->post('noms_visiteurs');
		$prenoms_users = $this->input->post('prenoms_visiteurs');
		$getNomVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($nom_users);
	    $getPreNomsVisiteurs = $this->globalModel->jeBloqueLesFauxComptes($prenoms_users);
	    // Check that data was sent to the mailer.
		$getComptes = $this->authModel->getMonCompte($idLogin);
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
			$addIng = $this->authModel->majMonCompte($idLogin, $nom_users, $prenoms_users);
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


public function sendCardEmailMessage_post()
{   
    //on vérifie si une session existe 
    if (!empty($this->input->post('nomLogin')) AND !empty($this->input->post('prenomsLogin')) AND !empty($this->input->post('mobileLogin')) AND !empty($this->input->post('sujet_message')) AND !empty($this->input->post('templatesEmail')))
	{
		$nom_transporteurs = $this->input->post('nomLogin').' '.$this->input->post('prenomsLogin');
        $mobile_transporteurs = $this->input->post('mobileLogin');
        $sujet_message = $this->input->post('sujet_message');
        $templatesEmail = $this->input->post('templatesEmail');

      	$paramsend = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "infos.2imservices@gmail.com",
                    'Name' => $nom_transporteurs.' ('.$mobile_transporteurs.')',
                ],
                'To' => [
                    [
                        'Email' => 'vaccipha@enovpharm.com',
                    ]
                ],
                'Subject' => $sujet_message,
                'TextPart' => $templatesEmail,
                'HTMLPart' => $templatesEmail,
                "CustomID" => uniqid()
              ]
          ]
       ];

        $messageId = $this->globalModel->postInformations($paramsend);
        if($messageId)
        {   
            $response['code']=1;
			$response['data']=$messageId;
			$response['msg']="Votre message a été envoyé !";
        }
        else
        {
            $response['code']=0;
            $response['data']= '';
            $response['msg']="Prière reprendre plus tard SVP !";
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