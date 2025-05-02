<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth_model extends CI_Model {

public function __construct()
{
    parent::__construct();
    date_default_timezone_set('UTC');
}

protected $table_users = "users";
protected $table_newsletters = "newsletters";
protected $table_entreprise = "entreprise";
protected $table_patients = "patients";
protected $table_cat_vaccins = "cat_vaccins";
protected $table_sous_vaccins = "sous_vaccins";
protected $table_dossiers_patients = "dossiers_patients";
protected $table_patient_canal = "patient_canal";


public function createCanalPatients($patientFk, $canalFk)
{
  return $this->db->set('canalFk', $canalFk)
                  ->set('patientFk', $patientFk)
                  ->set('etat_patient_canal', 'A')
                  ->set('date_patient_canal', date("Y-m-d H:i:s"))
                  ->insert($this->table_patient_canal);
}


public function isIdentifier($login, $password)
{
    $query = $this->db->select('*')
                     ->from($this->table_users)
                     ->join('entreprise', 'entreprise.id_entreprise = users.entrepriseId', 'left')
                     ->where('entreprise.etat_entreprise', 'A')
                     ->where('users.etat_users', 'A')
                     ->where('users.entrepriseId !=', null)
                     ->where('users.entrepriseId !=', 1)
                     //->where('users.mobile_users', $login)
                     ->where('users.email_users', $login)
                     ->get()
                     ->row();

    if($query && password_verify($password, $query->pass_users))
    {
        return $query;
    }
    else
    {
        return NULL;
    }
}

public function isGetsVisiteurs($id_users)
{       
     $query = $this->db->select('*')
                     ->from($this->table_users)
                     ->where('users.etat_users', 'A')
                     ->where('users.id_users', $id_users)
                     ->get();
        return $query->result();
}


public function creationsToutVenants($nom_patients, $prenoms_patients, $sexe_patients, 
$contact_patients, $communePatientsId, $password, $adderPatientsID, $sousVaccinsID, $catVaccinsID)
{     
                 $pass_patients = password_hash($password, PASSWORD_DEFAULT);
                 $this->db->set('pass_patients', $pass_patients)
                          ->set('nom_patients', $nom_patients)
                          ->set('sexe_patients', $sexe_patients)
                          ->set('communePatientsId', $communePatientsId)
                          ->set('contact_patients', $contact_patients)
                          ->set('prenoms_patients', $prenoms_patients)
                          ->set('adderPatientsID', $adderPatientsID)
                          ->set('etat_patients', "A")
                          ->set('metiersID', 9)
                          ->set('dateNaisPatients', date("2000-01-01"))
                          ->set('date_create_patients', date("Y-m-d H:i:s"))
                          ->insert($this->table_patients);
    $patientsDossiersID = $this->db->insert_id();

    if ($patientsDossiersID) 
    {   
        $this->db->set('isSousResponsabilite', $patientsDossiersID)
                 ->where('id_patients', $patientsDossiersID)
                 ->update($this->table_patients);
                 
           $this->db->set('sousVaccinsID', $sousVaccinsID)
                    ->set('patientsDossiersID', $patientsDossiersID)
                    ->set('etatDossiersPatients', "A")
                    ->set('catVaccinsID', $catVaccinsID)
                    ->set('dateCreateDossiers', date("Y-m-d H:i:s"))
                    ->insert($this->table_dossiers_patients);

            return $patientsDossiersID;
    }
    else
    {
      return FALSE;
    }
}


public function createursVisiteurs($nom_users, $prenoms_users, $sexe_users, 
$mobile_users, $communeusersId, $password)
{     
                 $pass_users = password_hash($password, PASSWORD_DEFAULT);
                 $this->db->set('pass_users', $pass_users)
                          ->set('nom_users', $nom_users)
                          ->set('sexe_users', $sexe_users)
                          ->set('communeusersId', $communeusersId)
                          ->set('mobile_users', $mobile_users)
                          ->set('prenoms_users', $prenoms_users)
                          ->set('etat_users', "A")
                          ->set('metiersID', 9)
                          ->set('dateNaisusers', date("2000-01-01"))
                          ->set('date_create_users', date("Y-m-d H:i:s"))
                          ->insert($this->table_users);
    $usersDossiersID = $this->db->insert_id();

    if ($usersDossiersID) 
    {   
        $this->db->set('isSousResponsabilite', $usersDossiersID)
                 ->where('id_users', $usersDossiersID)
                 ->update($this->table_users);
                 
        if ($sexe_users == 'Homme') 
        {
            return $this->db->set('sousVaccinsID', 20)
                            ->set('usersDossiersID', $usersDossiersID)
                            ->set('etatDossiersusers', "A")
                            ->set('catVaccinsID', 7)
                            ->set('dateCreateDossiers', date("Y-m-d H:i:s"))
                            ->insert($this->table_dossiers_users);
        }
        else
        {
            return $this->db->set('sousVaccinsID', 18)
                            ->set('usersDossiersID', $usersDossiersID)
                            ->set('etatDossiersusers', "A")
                            ->set('catVaccinsID', 5)
                            ->set('dateCreateDossiers', date("Y-m-d H:i:s"))
                            ->insert($this->table_dossiers_users);
        }
    }
    else
    {
      return FALSE;
    }
}

public function majVisiteurs($nom_users, $prenoms_users, $sexe_users, $metiersID, 
$dateNaisusers, $mobile_users, $communeusersId, $adresse_users, $email_users, $groupeSangusers)
{       
   return $this->db->set('nom_users', $nom_users)
                   ->set('sexe_users', $sexe_users)
                   ->set('metiersID', $metiersID)
                   ->set('dateNaisusers', $dateNaisusers)
                   ->set('mobile_users', $mobile_users)
                   ->set('communeusersId', $communeusersId)
                   ->set('adresse_users', $adresse_users)
                   ->set('email_users', $email_users)
                   ->set('prenoms_users', $prenoms_users)
                   ->set('isSousResponsabilite', $this->session->userdata('id_users'))
                   ->set('groupeSangusers', $groupeSangusers)
                   ->set('date_maj_users', date("Y-m-d H:i:s"))
                   ->where('id_users', $this->session->userdata('id_users'))
                   ->update($this->table_users);
}

public function changer_mot_passe($id_users, $password)
{       
    $pass_users = password_hash($password, PASSWORD_DEFAULT);
    return $this->db->set('pass_users', $pass_users)
                    ->set('date_maj_users', date("Y-m-d H:i:s"))
                    ->where('id_users', $id_users)
                    ->update($this->table_users);
}

public function majMonCompte($id_users, $nom_users, $prenoms_users)
{       
    return $this->db->set('nom_users', $nom_users)
                    ->set('prenoms_users', $prenoms_users)
                    ->set('date_maj_users', date("Y-m-d H:i:s"))
                    ->where('id_users', $id_users)
                    ->update($this->table_users);
}

public function getMonCompte($id_users)
{
     return $this->db->select('*')
                     ->from($this->table_users)
                     ->where('id_users', $id_users)
                     ->where('etat_users', "A")
                     ->get()
                     ->row();
}

public function existeVisiteurs($login)
{
     return $this->db->select('*')
                     ->from($this->table_users)
                     ->where('mobile_users', $login)
                     ->or_where('email_users', $login)
                     ->get()
                     ->row();
}

public function existePatientMobiles($contact_patients)
{
     return $this->db->select('*')
                     ->from($this->table_patients)
                     ->where('contact_patients', $contact_patients)
                     ->where('patients.etat_patients !=', 'S')
                     ->get()
                     ->row();
}

public function existeEntreprises($contact_entreprise, $email_entreprise)
{
     return $this->db->select('*')
                     ->from($this->table_entreprise)
                     ->where('etat_entreprise', 'A')
                     ->where('email_entreprise', $email_entreprise)
                     ->or_where('contact_entreprise', $contact_entreprise)
                     ->get()
                     ->row();
}

public function createNewsletter($email_newsletters, $code_promotions)
{
  return $this->db->set('code_promotions', $code_promotions)
                  ->set('email_newsletters', $email_newsletters)
                  ->set('etat_newsletters', 'A')
                  ->set('date_create_newsletters', date("Y-m-d H:i:s"))
                  ->insert($this->table_newsletters);
}


   
}
?>