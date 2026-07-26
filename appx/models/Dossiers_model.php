<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dossiers_model extends CI_Model {

protected $table_patients = "patients";
protected $table_reservations = "reservations";
protected $table_cat_vaccins = "cat_vaccins";
protected $table_sous_vaccins = "sous_vaccins";
protected $table_vaccinations = "vaccinations";
protected $table_dossiers_patients = "dossiers_patients";

public function __construct()
{
    parent::__construct();
    date_default_timezone_set('UTC');
}

public function getListesDossiersByPatients($id_patients)
{   
     $query = $this->db->select('*')
                        ->from($this->table_patients)
                        ->join('communes', 'communes.id_commune = patients.communePatientsId', 'left')
                        ->join('dossiers_patients', 'dossiers_patients.patientsDossiersID = patients.id_patients', 'left')
                        ->where('patients.etat_patients', 'A')
                        ->where('dossiers_patients.etatDossiersPatients', 'A')
                        ->where('patients.isSousResponsabilite', $id_patients)
                        ->order_by("patients.id_patients","desc")
                        ->get();
           return $query->result();
}

public function getPatientsByMobiles($contact_patients)
{   
     $query = $this->db->select('*')
                        ->from($this->table_patients)
                        ->join('communes', 'communes.id_commune = patients.communePatientsId', 'left')
                        ->join('dossiers_patients', 'dossiers_patients.patientsDossiersID = patients.id_patients', 'left')
                        ->where('patients.etat_patients', 'A')
                        ->where('dossiers_patients.etatDossiersPatients', "A")
                        ->where('patients.contact_patients', $contact_patients)
                        ->order_by("patients.id_patients","desc")
                        ->get();
           return $query->result();
}

public function getPatientsByIsMobiles($contact_patients)
{       
   return $this->db->select('*')
                ->from($this->table_patients)
                ->where('patients.etat_patients', 'A')
                ->where('patients.contact_patients', $contact_patients)
                ->get()
                ->row();
}

public function isPatientsByMobiles($contact_patients)
{       
   return $this->db->select('*')
                ->from($this->table_patients)
                ->join('dossiers_patients', 'dossiers_patients.patientsDossiersID = patients.id_patients', 'left')
                ->where('patients.etat_patients', 'A')
                ->where('dossiers_patients.etatDossiersPatients', "A")
                ->where('patients.contact_patients', $contact_patients)
                ->get()
                ->row();
}

public function isPatientsWithCategories($id_patients)
{       
       $query = $this->db->select('*')
                        ->from($this->table_patients)
                        ->join('communes', 'communes.id_commune = patients.communePatientsId', 'left')
                        ->join('dossiers_patients', 'dossiers_patients.patientsDossiersID = patients.id_patients', 'left')
                        ->where('patients.etat_patients', 'A')
                        ->where('dossiers_patients.etatDossiersPatients', "A")
                        ->where('patients.id_patients', $id_patients)
                        ->get();
        return $query->result();
}


public function getTotalDossiersByOfficines($entrepriseId, $date_min, $date_max)
{       
     return $this->db->select('count(patients.id_patients) as nombre')
                     ->from($this->table_patients)
                     ->join('users', 'users.id_users = patients.adderPatientsID', 'left')
                     ->where('patients.etat_patients !=', 'S')
                     ->where('patients.date_create_patients >=', $date_min)
                     ->where('patients.date_create_patients <=', $date_max)
                     ->where('users.entrepriseId', $entrepriseId)
                     ->get()
                     ->row();
}

public function getTotalDossiersByUsers($isSousResponsabilite)
{       
     return $this->db->select('count(id_patients) as nombre')
                     ->from($this->table_patients)
                     ->where('etat_patients', 'A')
                     ->where('isSousResponsabilite', $isSousResponsabilite)
                     ->get()
                     ->row();
}

public function existeSousMyResponsabilite($isSousResponsabilite, $nom_patients, $prenoms_patients)
{       
     return $this->db->select('*')
                     ->from($this->table_patients)
                     ->where('patients.etat_patients', 'A')
                     ->where('patients.nom_patients', $nom_patients)
                     ->where('patients.prenoms_patients', $prenoms_patients)
                     ->where('patients.isSousResponsabilite', $isSousResponsabilite)
                     ->get()
                     ->row();
}

public function creerVisiteurs($nom_patients, $prenoms_patients, $sexe_patients, $metiersID, 
$dateNaisPatients, $contact_patients, $communePatientsId, $adresse_patients, $email_patients, 
$groupeSangPatients, $catVaccinsID, $sousVaccinsID)
{     
                 $isSousResponsabilite = $this->session->userdata('id_users');
                 $this->db->set('isSousResponsabilite', $isSousResponsabilite)
                          ->set('nom_patients', $nom_patients)
                          ->set('sexe_patients', $sexe_patients)
                          ->set('communePatientsId', $communePatientsId)
                          ->set('adresse_patients', $adresse_patients)
                          //->set('contact_patients', $contact_patients)
                          //->set('email_patients', $email_patients)
                          ->set('prenoms_patients', $prenoms_patients)
                          ->set('groupeSangPatients', $groupeSangPatients)
                          ->set('etat_patients', "A")
                          ->set('metiersID', $metiersID)
                          ->set('dateNaisPatients', date("Y-m-d", strtotime($dateNaisPatients)))
                          ->set('date_create_patients', date("Y-m-d H:i:s"))
                          ->insert($this->table_patients);
    $patientsDossiersID = $this->db->insert_id();

    if ($patientsDossiersID) 
    {
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

public function majProfilsVisiteurs($id_patients, $nom_patients, $prenoms_patients, $sexe_patients, 
$metiersID, $dateNaisPatients, $communePatientsId, $adresse_patients, $groupeSangPatients, 
$catVaccinsID, $sousVaccinsID)
{       
     $query1 = $this->db->set('nom_patients', $nom_patients)
                       ->set('sexe_patients', $sexe_patients)
                       ->set('metiersID', $metiersID)
                       ->set('dateNaisPatients', $dateNaisPatients)
                       //->set('contact_patients', $contact_patients)
                       ->set('communePatientsId', $communePatientsId)
                       ->set('adresse_patients', $adresse_patients)
                       //->set('email_patients', $email_patients)
                       ->set('prenoms_patients', $prenoms_patients)
                       ->set('groupeSangPatients', $groupeSangPatients)
                       ->set('date_maj_patients', date("Y-m-d H:i:s"))
                       ->where('id_patients', $id_patients)
                       ->update($this->table_patients);

    if ($query1) 
    {       
            $query2 = $this->db->set('etatDossiersPatients', "I")
                               ->set('dateMajDossiers', date("Y-m-d H:i:s"))
                               ->where('patientsDossiersID', $id_patients)
                               ->update($this->table_dossiers_patients);

            if ($query2) 
            {
                 return $this->db->set('sousVaccinsID', $sousVaccinsID)
                                 ->set('patientsDossiersID', $id_patients)
                                 ->set('etatDossiersPatients', "A")
                                 ->set('catVaccinsID', $catVaccinsID)
                                 ->set('dateCreateDossiers', date("Y-m-d H:i:s"))
                                 ->insert($this->table_dossiers_patients);

            }
            else
            {
                return FALSE;
            }
    }
    else
    {
            return FALSE;
    }
}

public function creationsDossiers($contact_patients, $nom_patients, $prenoms_patients, 
$sexe_patients, $sousVaccinsID, $catVaccinsID, $adderPatientsID)
{                 
                 $password = 'demo';
                 $pass_patients = password_hash($password, PASSWORD_DEFAULT);
                 $this->db->set('contact_patients', $contact_patients)
                          ->set('nom_patients', $nom_patients)
                          ->set('adderPatientsID', $adderPatientsID)
                          ->set('pass_patients', $pass_patients)
                          ->set('sexe_patients', $sexe_patients)
                          ->set('prenoms_patients', $prenoms_patients)
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

public function miseSousTutelleDossiers($isSousResponsabilite, $nom_patients, $prenoms_patients, 
$sexe_patients, $sousVaccinsID, $catVaccinsID)
{     
                 $this->db->set('isSousResponsabilite', $isSousResponsabilite)
                          ->set('nom_patients', $nom_patients)
                          ->set('sexe_patients', $sexe_patients)
                          ->set('prenoms_patients', $prenoms_patients)
                          ->set('etat_patients', "A")
                          ->set('metiersID', 9)
                          ->set('dateNaisPatients', date("2000-01-01"))
                          ->set('date_create_patients', date("Y-m-d H:i:s"))
                          ->insert($this->table_patients);
    $patientsDossiersID = $this->db->insert_id();

    if ($patientsDossiersID) 
    {
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

public function getListesSousVaccinsByCategories($categorieVaccinsID)
{       
       $query = $this->db->select('*')
                        ->from($this->table_sous_vaccins)
                        ->where('sous_vaccins.etat_sous_vaccins', 'A')
                        ->where('sous_vaccins.rang_sous_vaccins', '1')
                        ->where('sous_vaccins.categorieVaccinsID', $categorieVaccinsID)
                        ->get();
        return $query->result();
}

public function getListesDossiers($id_patients)
{       
       $query = $this->db->select('*')
                        ->from($this->table_patients)
                        ->where('patients.etat_patients', 'A')
                        ->where('patients.isSousResponsabilite', $id_patients)
                        ->get();
        return $query->result();
}

public function getListesDossiersByEntreprises($entrepriseId)
{       
       $query = $this->db->select('*')
                        ->from($this->table_patients)
                        ->join('communes', 'communes.id_commune = patients.communePatientsId', 'left')
                        ->join('users', 'users.id_users = patients.adderPatientsID', 'left')
                        ->join('dossiers_patients', 'dossiers_patients.patientsDossiersID = patients.id_patients', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = dossiers_patients.catVaccinsID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = dossiers_patients.sousVaccinsID', 'left')
                        ->where('patients.etat_patients !=', 'S')
                        ->where('users.entrepriseId', $entrepriseId)
                        ->order_by("patients.id_patients","desc")
                        ->get();
        return $query->result();
}

public function getListesDossiersByFiltres($entrepriseId, $labelSearch)
{       
       $query = $this->db->select('*')
                        ->from($this->table_patients)
                        ->join('communes', 'communes.id_commune = patients.communePatientsId', 'left')
                        ->join('users', 'users.id_users = patients.adderPatientsID', 'left')
                        ->join('dossiers_patients', 'dossiers_patients.patientsDossiersID = patients.id_patients', 'left')
                        ->where('patients.etat_patients !=', 'S')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = dossiers_patients.catVaccinsID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = dossiers_patients.sousVaccinsID', 'left')
                        //->where('users.entrepriseId', $entrepriseId)
                        ->where('patients.contact_patients', $labelSearch)
                        ->get();
        return $query->result();
}

public function isDossiersByPatientsID($patientsDossiersID)
{       
     return $this->db->select('*')
                     ->from($this->table_dossiers_patients)
                     ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = dossiers_patients.catVaccinsID', 'left')
                     ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = dossiers_patients.sousVaccinsID', 'left')
                     ->join('patients', 'patients.id_patients = dossiers_patients.patientsDossiersID', 'left')
                     ->where('dossiers_patients.etatDossiersPatients', 'A')
                     ->where('dossiers_patients.patientsDossiersID', $patientsDossiersID)
                     ->order_by("dossiers_patients.idDossiersPatients","desc")
                     ->limit(1)
                     ->get()
                     ->row();
}


public function supprimerDossiers($patientsDossiersID)
{     
         $dossiersID = $this->db->set('etat_patients', "S")
                                ->set('date_maj_patients', date("Y-m-d H:i:s"))
                                ->where('id_patients', $patientsDossiersID)
                                ->update($this->table_patients);
        if ($dossiersID) 
        {
             return $this->db->set('etatDossiersPatients', "S")
                              ->set('dateMajDossiers', date("Y-m-d H:i:s"))
                              ->where('patientsDossiersID', $patientsDossiersID)
                              ->update($this->table_dossiers_patients);
        }
        else
        {
            return FALSE;
        }
}


public function majDossiersPersos($patientsDossiersID, $catVaccinsID, $sousVaccinsID)
{     
                 $this->db->set('sousVaccinsID', $sousVaccinsID)
                          ->set('catVaccinsID', $catVaccinsID)
                          ->set('patientsDossiersID', $patientsDossiersID)
                          ->set('etatDossiersPatients', "A")
                          ->set('dateMajDossiers', date("Y-m-d H:i:s"))
                          ->set('dateCreateDossiers', date("Y-m-d H:i:s"))
                          ->insert($this->table_dossiers_patients);
    $dossiersID = $this->db->insert_id();
    if ($dossiersID) 
    {
         return $this->db->set('etatDossiersPatients', "I")
                          ->set('dateMajDossiers', date("Y-m-d H:i:s"))
                          ->where('idDossiersPatients !=', $dossiersID)
                          ->where('patientsDossiersID', $patientsDossiersID)
                          ->update($this->table_dossiers_patients);
    }
    else
    {
        return FALSE;
    }
}


public function majCarnetsDossiersPatients($id_patients, $nom_patients, $prenoms_patients, 
$sexe_patients, $catVaccinsID, $sousVaccinsID)
{       

    $query1 = $this->db->set('nom_patients', $nom_patients)
                       ->set('sexe_patients', $sexe_patients)
                       ->set('prenoms_patients', $prenoms_patients)
                       ->set('date_maj_patients', date("Y-m-d H:i:s"))
                       ->where('id_patients', $id_patients)
                       ->update($this->table_patients);

    if ($query1) 
    {       
            $query2 = $this->db->set('etatDossiersPatients', "I")
                               ->set('dateMajDossiers', date("Y-m-d H:i:s"))
                               ->where('patientsDossiersID', $id_patients)
                               ->update($this->table_dossiers_patients);

            if ($query2) 
            {
                 return $this->db->set('sousVaccinsID', $sousVaccinsID)
                                 ->set('patientsDossiersID', $id_patients)
                                 ->set('etatDossiersPatients', "A")
                                 ->set('catVaccinsID', $catVaccinsID)
                                 ->set('dateCreateDossiers', date("Y-m-d H:i:s"))
                                 ->insert($this->table_dossiers_patients);

            }
            else
            {
                return FALSE;
            }
    }
    else
    {
            return FALSE;
    }
}


public function getCarnetVaccinalByPatientId($patientsResId)
{     
   $query = $this->db->select('idVaccinations, date_create_res, date_res_deb, nom_cat_vaccins, maladieTraitee, nom_sous_vaccins, nomVaccins, status_res, codeVaccins, patientsResId')
                     ->from($this->table_reservations)
                     ->join('vaccinations', 'vaccinations.reservationsFk = reservations.id_res', 'left')
                     ->join('vaccins', 'vaccins.idVaccins = vaccinations.vaccinsFk', 'left')
                     ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                     ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                     ->where('reservations.serviceResID', 1)
                     ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                     ->where('reservations.patientsResId', $patientsResId)
                      ->order_by("reservations.date_maj_res","desc")
                     ->get();
        return $query->result();
}




   
}
?>