<?php
defined ('BASEPATH') OR exit ('No  direct   script   access   allowed');
//CREER LE CONSTRUCTEUR DU MODEL

class Reservations_model extends CI_Model {

public function __construct()
{
    parent::__construct();
    date_default_timezone_set('UTC');
}


protected $table_vaccinations = "vaccinations";
protected $table_entreprise = "entreprise";
protected $table_vaccins = "vaccins";
protected $table_patients = "patients";
protected $table_reservations = "reservations";
protected $table_transactions = "transactions";
protected $table_reversement = "reversements";

public function payerTrans($statusTrans, $operateur_trans, $resID, $montant_trans, $reference_syca, $mobile_paiement)
{  

    if ((int)$statusTrans == 0)
    {     
           $frais_trans = (float)$montant_trans*0.03;
           $status_trans = 'S';
    }
    elseif ((int)$statusTrans == -200)
    {     
          $frais_trans = 0;
          $status_trans = 'P';
    }
    else
    {    
          $frais_trans = 0;
          $status_trans = 'E';
    }

    if (!empty($operateur_trans) AND !empty($reference_syca)) 
    {
          $modePaysId = 2;
    }
    else
    {
          $modePaysId = 2;
          $operateur_trans = 'Neant';
    }   

    $req1 = $this->db->set('modePaieID', $modePaysId)
                     ->set('date_maj_res', date("Y-m-d H:i:s"))
                     ->where('id_res', $resID)
                     ->update($this->table_reservations);

    if ($req1) 
    {
         return $this->db->set('operateur_trans', $operateur_trans)
                         ->set('mobile_paiement', $mobile_paiement)
                         ->set('reference_syca', $reference_syca)
                         ->set('modePayerId', $modePaysId)
                         ->set('montant_trans', (float)$montant_trans)
                         ->set('frais_trans', (double)$frais_trans)
                         ->set('date_maj_trans', date("Y-m-d H:i:s"))
                         ->set('status_trans', $status_trans)
                         ->where('resID', $resID)
                         ->update($this->table_transactions);


   }
   else
   {
        return FALSE;
   }

}


public function createResPaieMobileMoney($code_res, $entResId, $patientsResId, $montant_res, $date_res_deb, 
$date_res_end, $plageHoraireID, $sousVaccinsResID, $catVaccinsResID, $qteProduits, 
$parentResFK, $adderResFK, $numeroLots)
{           

             $this->db->set('entResId', $entResId)
                      ->set('parentResFK', $parentResFK)
                      ->set('code_res', $code_res)
                      ->set('numLotsDistricts', $numeroLots)
                      ->set('montant_res', (float)$montant_res)
                      ->set('date_res_end', date("Y-m-d H:i:s", strtotime($date_res_end)))
                      ->set('date_res_deb', date("Y-m-d H:i:s", strtotime($date_res_deb)))
                      ->set('patientsResId', $patientsResId)
                      ->set('plageHoraireID', $plageHoraireID)
                      ->set('sousVaccinsResID', $sousVaccinsResID)
                      ->set('qteProduits', $qteProduits)
                      ->set('catVaccinsResID', $catVaccinsResID)
                      ->set('adderResFK', $adderResFK)
                      ->set('status_res', 'P')
                      ->set('typeResCode', 'V')
                      ->set('etat_res', 'A')
                      ->set('devicesID', 2)
                      ->set('modePaieID', 1)
                      ->set('serviceResID', 1)
                      ->set('date_create_res', date("Y-m-d H:i:s"))
                      ->insert($this->table_reservations);
      $resID = $this->db->insert_id();

      if ($resID) 
      {  
                    $this->db->set('operateur_trans', null)
                             ->set('lotsResTrans', $numeroLots)
                             ->set('mobile_paiement', null)
                             ->set('reference_syca', null)
                             ->set('modePayerId', 2)
                             ->set('montant_trans', (float)$montant_res)
                             ->set('frais_trans', 0)
                             ->set('date_maj_trans', date("Y-m-d H:i:s"))
                             ->set('resID', $resID)
                             ->set('entID', $entResId)
                             ->set('patientsFK', $patientsResId)
                             ->set('usersEntrepriseFK', $adderResFK)
                             ->set('etat_trans', 'A')
                             ->set('reversCode', 'N')
                             ->set('servicesTransID', 1)
                             ->set('status_trans', 'P')
                             ->set('date_create_trans', date("Y-m-d H:i:s"))
                             ->insert($this->table_transactions);
                             
             return $resID;

      }
      else
      { 
           return FALSE;
      }
      
}

public function createResPlusTardPaieMobileMoney($code_res, $entResId, $patientsResId, $montant_res, $date_res_deb, 
$date_res_end, $plageHoraireID, $sousVaccinsResID, $catVaccinsResID, $qteProduits, 
$parentResFK, $adderResFK, $numeroLots)
{           

             $this->db->set('entResId', $entResId)
                      ->set('parentResFK', $parentResFK)
                      ->set('code_res', $code_res)
                      ->set('numLotsDistricts', null)
                      ->set('montant_res', (float)$montant_res)
                      ->set('date_res_end', date("Y-m-d H:i:s", strtotime($date_res_end)))
                      ->set('date_res_deb', $date_res_deb)
                      ->set('patientsResId', $patientsResId)
                      ->set('plageHoraireID', $plageHoraireID)
                      ->set('sousVaccinsResID', $sousVaccinsResID)
                      ->set('qteProduits', $qteProduits)
                      ->set('catVaccinsResID', $catVaccinsResID)
                      ->set('adderResFK', $adderResFK)
                      ->set('status_res','P')
                      ->set('typeResCode', 'V')
                      ->set('etat_res', 'A')
                      ->set('devicesID', 2)
                      ->set('modePaieID', 1)
                      ->set('serviceResID', 1)
                      ->set('date_create_res', date("Y-m-d H:i:s"))
                      ->insert($this->table_reservations);
      $resID = $this->db->insert_id();

      if ($resID) 
      {  
                    $this->db->set('operateur_trans', null)
                             ->set('lotsResTrans', $numeroLots)
                             ->set('mobile_paiement', null)
                             ->set('reference_syca', null)
                             ->set('modePayerId', 2)
                             ->set('montant_trans', (float)$montant_res)
                             ->set('frais_trans', 0)
                             ->set('date_maj_trans', date("Y-m-d H:i:s"))
                             ->set('resID', $resID)
                             ->set('entID', $entResId)
                             ->set('patientsFK', $patientsResId)
                             ->set('usersEntrepriseFK', $adderResFK)
                             ->set('etat_trans', 'A')
                             ->set('reversCode', 'N')
                             ->set('servicesTransID', 1)
                             ->set('status_trans', 'P')
                             ->set('date_create_trans', date("Y-m-d H:i:s"))
                             ->insert($this->table_transactions);
                             
             return $resID;

      }
      else
      { 
           return FALSE;
      }
      
}

public function isIdsRes($id_res)
{   
   return $this->db->select('*')
                     ->from($this->table_reservations)
                     ->where('reservations.id_res', $id_res)
                     ->get()
                     ->row();
}

public function isGetDernierInsertResByUsers($adderResFK)
{     
      return $this->db->select('*')
                     ->from($this->table_reservations)
                     //->where('reservations.etat_res', 'A')
                     ->where('serviceResID', 1)
                     ->where('reservations.adderResFK', $adderResFK)
                     ->order_by("reservations.date_create_res","desc")
                     ->limit(1)
                     ->get()
                     ->row();
}

public function getListeRDVsByMobile($entResId, $contact_patients)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.status_res', 'P')
                        ->where('reservations.serviceResID', 1)
                        ->where('patients.contact_patients', $contact_patients)
                        //->where('reservations.entResId', $entResId)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->order_by("reservations.date_res_deb","asc")
                        ->get();
           return $query->result();
}

public function getListeRDVsByByPatientsIds($entResId, $patientsResId)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.status_res', 'P')
                        ->where('reservations.serviceResID', 1)
                        ->where('reservations.patientsResId', $patientsResId)
                        //->where('reservations.entResId', $entResId)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->order_by("reservations.date_res_deb","asc")
                        ->get();
           return $query->result();
}

public function getListeTotalRDVsEtRappels($entID)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                        ->where('reservations.etat_res', 'A')
                         //->where('reservations.status_res', 'P')
                        ->where('reservations.serviceResID', 1)
                        ->where('reservations.entResId', $entID)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function getListeTotalRDVsEtRappelsEnCours($entID)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                        ->where('reservations.etat_res', 'A')
                         ->where('reservations.status_res', 'P')
                        ->where('reservations.serviceResID', 1)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->where('reservations.entResId', $entID)
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function getTotalRDVsEtRappelsByDate($entID, $dateDebRes)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.serviceResID', 1)
                        ->where('reservations.date_res_deb >=', date('Y-m-d 00:00:00', strtotime($dateDebRes)))
                        ->where('reservations.date_res_deb <=', date('Y-m-d 23:59:59', strtotime($dateDebRes)))
                        ->where('reservations.entResId', $entID)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function getRDVsEtRappelsByTypeRes($entID, $status_res)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.status_res', $status_res)
                        ->where('reservations.serviceResID', 1)
                        //->where('reservations.typeResCode', $typeResCode)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->where('reservations.entResId', $entID)
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}


public function getRDVsEtRappelsByPeriode($entID, $date_min, $date_max)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                        ->where('reservations.etat_res', 'A')
                       // ->where('reservations.status_res', $status_res)
                        ->where('reservations.serviceResID', 1)
                        //->where('reservations.typeResCode', $typeResCode)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->where('reservations.entResId', $entID)
                        ->where('reservations.date_create_res >=', $date_min)
                        ->where('reservations.date_create_res <=', $date_max)
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function getListesRDVsEtRappelsAVenir($entID)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.status_res', 'P')
                        ->where('reservations.serviceResID', 1)
                        ->where('reservations.entResId', $entID)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function getListeRendezVousAVenir($patientsResId)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                        ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.status_res', 'P')
                        ->where('reservations.serviceResID', 1)
                        ->where('reservations.parentResFK', NULL)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->where('patients.isSousResponsabilite', $patientsResId)
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function getListeRappelsAVenir($patientsResId)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                       ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.status_res', 'P')
                        ->where('reservations.serviceResID', 1)
                        ->where('reservations.parentResFK !=', NULL)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->where('patients.isSousResponsabilite', $patientsResId)
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function isResPatientsByIds($patientsResId)
{     
      return $this->db->select('*')
                     ->from($this->table_reservations)
                     ->where('reservations.etat_res', 'A')
                     ->where('reservations.serviceResID', 1)
                     ->where('reservations.patientsResId', $patientsResId)
                     ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                     ->get()
                     ->row();
}

public function getGlobalComptesFonds($entID, $date_min, $date_max)
{     
   return $this->db->select('count(id_trans) as nombre, sum(montant_trans) as montant')
                  ->from($this->table_transactions)
                  ->where('transactions.date_create_trans >=', $date_min)
                  ->where('transactions.date_create_trans <=', $date_max)
                  ->where('transactions.status_trans', 'S')
                  ->where('transactions.etat_trans', 'A')
                  ->where('transactions.reversCode', 'N')
                  ->where('transactions.reversId', null)
                  ->where('transactions.servicesTransID', 1)
                  ->where('transactions.entID', $entID)
                  ->get()
                  ->row();    
}

public function getStatsVaccinsByOfficine($entID, $date_min, $date_max)
{     
      return $this->db->select('count(vaccinsFk) as nombre')
                     ->from($this->table_vaccinations)
                     ->join('reservations', 'reservations.id_res = vaccinations.reservationsFk', 'left')
                     ->where('reservations.date_create_res >=', $date_min)
                     ->where('reservations.date_create_res <=', $date_max)
                     ->where('vaccinations.etatVaccinations', 'A')
                     ->where('reservations.etat_res', 'A')
                     ->where('reservations.serviceResID', 1)
                     ->where('reservations.status_res', 'S')
                     ->where('reservations.entResId', $entID)
                     ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                     ->get()
                     ->row();
}

public function getListePaiementsByOfficine($entID, $date_min, $date_max)
{     
      $query = $this->db->select('*')
                        ->from($this->table_transactions)
                        ->join('reservations', 'reservations.id_res = transactions.resID', 'left')
                        ->where('transactions.date_create_trans >=', $date_min)
                        ->where('transactions.date_create_trans <=', $date_max)
                       // ->where('transactions.status_trans', 'S')
                        ->where('transactions.etat_trans', 'A')
                        ->where('transactions.servicesTransID', 1)
                        ->where('transactions.entID', $entID)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->order_by("transactions.id_trans","desc")
                       ->get();
          return $query->result(); 
}

public function getTotalRdvsConfirmes($entID, $date_min, $date_max)
{     
      return $this->db->select('count(id_res) as nombre')
                     ->from($this->table_reservations)
                     ->where('reservations.date_create_res >=', $date_min)
                     ->where('reservations.date_create_res <=', $date_max)
                     ->where('reservations.etat_res', 'A')
                     ->where('reservations.serviceResID', 1)
                     ->where('reservations.status_res', 'S')
                     ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                     ->where('reservations.entResId', $entID)
                     ->get()
                     ->row();
}

public function getOfficinesStatsByStatus($status_res, $entID, $date_min, $date_max)
{     
      return $this->db->select('count(id_res) as nombre, sum(montant_res) as montant, sum(qteProduits) as quantite')
                     ->from($this->table_reservations)
                     ->where('reservations.date_create_res >=', $date_min)
                     ->where('reservations.date_create_res <=', $date_max)
                     ->where('reservations.etat_res', 'A')
                     ->where('reservations.serviceResID', 1)
                     ->where('reservations.entResId', $entID)
                     ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                     ->where('reservations.status_res', $status_res)
                     ->get()
                     ->row();
}

public function getListesRappelsByUsers($patientsResId)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.serviceResID', 1)
                        ->where('reservations.parentResFK !=', NULL)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->where('patients.isSousResponsabilite', $patientsResId)
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function getListesRDVsByUsers($patientsResId)
{     
      $query = $this->db->select('*')
                        ->from($this->table_reservations)
                        ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                        ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->where('reservations.etat_res', 'A')
                        ->where('reservations.serviceResID', 1)
                        ->where('reservations.parentResFK', NULL)
                        ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                        ->where('patients.isSousResponsabilite', $patientsResId)
                        ->order_by("reservations.id_res","desc")
                        ->get();
           return $query->result();
}

public function annulerReservations($id_res)
{  
   
   $req = $this->db->set('status_res', 'A')
                   ->set('date_maj_res', date("Y-m-d H:i:s"))
                   ->where('id_res', $id_res)
                   ->update($this->table_reservations);

   if ($req) 
   {
       return $this->db->set('etatVaccinations', 'I')
                       ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                       ->where('reservationsFk', $id_res)
                       ->update($this->table_vaccinations);
   }
   else
   {
        return FALSE;
   }

}

public function confirmerReservations($resID, $numeroLots, $montant_trans, 
$patientsFK, $entID, $usersEntrepriseFK, $idVaccins, $modePaieID)
{  

   $req1 = $this->db->set('status_res', 'S')
                    ->set('modePaieID', $modePaieID)
                    ->set('adderResFK', $usersEntrepriseFK)
                    ->set('numLotsDistricts', $numeroLots)
                    ->set('date_maj_res', date("Y-m-d H:i:s"))
                    ->where('id_res', $resID)
                    ->update($this->table_reservations);

   if ($req1 && (int)$modePaieID == 1) 
   {
       $this->db->set('etatVaccinations', 'I')
                ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                ->where('reservationsFk', $resID)
                ->where_not_in('vaccinsFk', $idVaccins)
                ->update($this->table_vaccinations);

        return $this->db->set('modePayerId', $modePaieID)
                        ->set('lotsResTrans', $numeroLots)
                        ->set('mobile_paiement', null)
                        ->set('reference_syca', null)
                        ->set('frais_trans', 0)
                        ->set('operateur_trans', 'Caisse')
                        ->set('usersEntrepriseFK', $usersEntrepriseFK)
                        ->set('status_trans', 'S')
                        ->set('date_maj_trans', date("Y-m-d H:i:s"))
                        ->where('resID', $resID)
                        ->where('entID', $entID)
                        ->update($this->table_transactions);

   }
   elseif ($req1 && (int)$modePaieID == 2) 
   {
       $this->db->set('etatVaccinations', 'I')
                ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                ->where('reservationsFk', $resID)
                ->where_not_in('vaccinsFk', $idVaccins)
                ->update($this->table_vaccinations);

        return $this->db->set('modePayerId', $modePaieID)
                        ->set('lotsResTrans', $numeroLots)
                        ->set('usersEntrepriseFK', $usersEntrepriseFK)
                        ->set('status_trans', 'S')
                        ->set('date_maj_trans', date("Y-m-d H:i:s"))
                        ->where('resID', $resID)
                        ->where('entID', $entID)
                        ->update($this->table_transactions);

   }
   else
   {
        return FALSE;
   }

}

public function validerResPourAutreOfficine($resID, $idVaccins)
{  
   $req1 = $this->db->set('status_res', 'S')
                    ->set('montant_res', 0)
                    ->set('commentaire_res', 'Vaccins effectués dans un autre centre de santé.')
                    ->set('date_maj_res', date("Y-m-d H:i:s"))
                    ->where('id_res', $resID)
                    ->update($this->table_reservations);

   if ($req1) 
   {
      return $this->db->set('etatVaccinations', 'I')
                       ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                       ->where('reservationsFk', $resID)
                       ->where_not_in('vaccinsFk', $idVaccins)
                       ->update($this->table_vaccinations);

   }
   else
   {
        return FALSE;
   }

}

public function createVaccinations($entreprisesID, $reservationsFk, $vaccinsFk, $patientsFk, 
$parentResID, $typeResVaccins)
{           
             $this->db->set('entreprisesID', $entreprisesID)
                      ->set('reservationsFk', $reservationsFk)
                      ->set('vaccinsFk', $vaccinsFk)
                      ->set('typeResVaccins', $typeResVaccins)
                      ->set('parentResID', $parentResID)
                      ->set('patientsFk', $patientsFk)
                      ->set('etatVaccinations', 'A')
                      ->set('dateCreateVaccinations', date("Y-m-d H:i:s"))
                      ->insert($this->table_vaccinations);
      return $this->db->insert_id();
}

public function createResTransVaccins($code_res, $entResId, $patientsResId, $montant_res, $date_res_deb, 
$date_res_end, $plageHoraireID, $sousVaccinsResID, $catVaccinsResID, $qteProduits, 
$parentResFK, $adderResFK, $numeroLots, $modePaieID, $zeroDosePatient)
{           
             $this->db->set('entResId', $entResId)
                      ->set('parentResFK', $parentResFK)
                      ->set('code_res', $code_res)
                      ->set('montant_res', (float)$montant_res)
                      ->set('date_res_end', date("Y-m-d H:i:s", strtotime($date_res_end)))
                      ->set('date_res_deb', date("Y-m-d H:i:s", strtotime($date_res_deb)))
                      ->set('patientsResId', $patientsResId)
                      ->set('plageHoraireID', $plageHoraireID)
                      ->set('sousVaccinsResID', $sousVaccinsResID)
                      ->set('qteProduits', $qteProduits)
                      ->set('catVaccinsResID', $catVaccinsResID)
                      ->set('adderResFK', $adderResFK)
                      ->set('numLotsDistricts', $numeroLots)
                      ->set('status_res', 'S')
                      ->set('typeResCode', 'V')
                      ->set('etat_res', 'A')
                      ->set('devicesID', 2)
                      ->set('zeroDosePatient', $zeroDosePatient)
                      ->set('modePaieID', $modePaieID)
                      ->set('serviceResID', 1)
                      ->set('date_create_res', date("Y-m-d H:i:s"))
                      ->insert($this->table_reservations);
      $resID = $this->db->insert_id();

      if ($resID) 
      {  
                    $this->db->set('operateur_trans', 'Caisse')
                             ->set('lotsResTrans', $numeroLots)
                             ->set('mobile_paiement', null)
                             ->set('reference_syca', null)
                             ->set('modePayerId', $modePaieID)
                             ->set('montant_trans', (float)$montant_res)
                             ->set('frais_trans', 0)
                             ->set('date_maj_trans', date("Y-m-d H:i:s"))
                             ->set('resID', $resID)
                             ->set('entID', $entResId)
                             ->set('patientsFK', $patientsResId)
                             ->set('usersEntrepriseFK', $adderResFK)
                             ->set('etat_trans', 'A')
                             ->set('reversCode', 'N')
                             ->set('servicesTransID', 1)
                             ->set('status_trans', 'S')
                             ->set('date_create_trans', date("Y-m-d H:i:s"))
                             ->insert($this->table_transactions);
                             
             return $resID;

      }
      else
      { 
           return FALSE;
      }
}



public function createResTransVaccinsDateUlterieur($code_res, $entResId, $patientsResId, $montant_res, $date_res_deb, 
$date_res_end, $plageHoraireID, $sousVaccinsResID, $catVaccinsResID, $qteProduits, 
$parentResFK, $adderResFK, $numeroLots)
{           
             $this->db->set('entResId', $entResId)
                      ->set('parentResFK', $parentResFK)
                      ->set('code_res', $code_res)
                      ->set('montant_res', (float)$montant_res)
                      ->set('date_res_end', date("Y-m-d H:i:s", strtotime($date_res_end)))
                      ->set('date_res_deb', $date_res_deb)
                      ->set('patientsResId', $patientsResId)
                      ->set('plageHoraireID', $plageHoraireID)
                      ->set('sousVaccinsResID', $sousVaccinsResID)
                      ->set('qteProduits', $qteProduits)
                      ->set('catVaccinsResID', $catVaccinsResID)
                      ->set('adderResFK', $adderResFK)
                      ->set('numLotsDistricts', null)
                      ->set('status_res', 'P')
                      ->set('typeResCode', 'V')
                      ->set('etat_res', 'A')
                      ->set('devicesID', 2)
                      ->set('modePaieID', 1)
                      ->set('serviceResID', 1)
                      ->set('date_create_res', date("Y-m-d H:i:s"))
                      ->insert($this->table_reservations);
      $resID = $this->db->insert_id();

      if ($resID) 
      {  
                    $this->db->set('operateur_trans', 'Caisse')
                             ->set('lotsResTrans', $numeroLots)
                             ->set('mobile_paiement', null)
                             ->set('reference_syca', null)
                             ->set('modePayerId', 1)
                             ->set('montant_trans', (float)$montant_res)
                             ->set('frais_trans', 0)
                             ->set('date_maj_trans', date("Y-m-d H:i:s"))
                             ->set('resID', $resID)
                             ->set('entID', $entResId)
                             ->set('patientsFK', $patientsResId)
                             ->set('usersEntrepriseFK', $adderResFK)
                             ->set('etat_trans', 'A')
                             ->set('reversCode', 'N')
                             ->set('servicesTransID', 1)
                             ->set('status_trans', 'S')
                             ->set('date_create_trans', date("Y-m-d H:i:s"))
                             ->insert($this->table_transactions);
                             
             return $resID;

      }
      else
      { 
           return FALSE;
      }
}

public function createResVaccins($code_res, $entResId, $patientsResId, $montant_res, $date_res_deb, 
$date_res_end, $plageHoraireID, $sousVaccinsResID, $catVaccinsResID, $qteProduits, $parentResFK)
{           
             $this->db->set('entResId', $entResId)
                      ->set('parentResFK', $parentResFK)
                      ->set('code_res', $code_res)
                      ->set('montant_res', (float)$montant_res)
                      ->set('date_res_end', date("Y-m-d H:i:s", strtotime($date_res_end)))
                      ->set('date_res_deb', date("Y-m-d H:i:s", strtotime($date_res_deb)))
                      ->set('patientsResId', $patientsResId)
                      ->set('plageHoraireID', $plageHoraireID)
                      ->set('sousVaccinsResID', $sousVaccinsResID)
                      ->set('qteProduits', $qteProduits)
                      ->set('catVaccinsResID', $catVaccinsResID)
                      ->set('status_res', 'P')
                      ->set('etat_res', 'A')
                      ->set('devicesID', 2)
                      ->set('modePaieID', 1)
                      ->set('serviceResID', 1)
                      ->set('date_create_res', date("Y-m-d H:i:s"))
                      ->insert($this->table_reservations);
      return $this->db->insert_id();
}

public function isSavoirSiDejaRdvsVaccins($patientsResId, $entResId, $date_res_deb)
{   
   return $this->db->select('*')
                     ->from($this->table_reservations)
                     ->where('reservations.patientsResId', $patientsResId)
                     ->where('reservations.entResId', $entResId)
                     ->where('DATE(reservations.date_res_deb)', date("Y-m-d", strtotime($date_res_deb)))
                     ->where('reservations.serviceResID', 1)
                     ->where('reservations.parentResFK', NULL)
                     ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                     ->where('reservations.etat_res', 'A')
                     ->get()
                     ->row();
}

public function getRangRdvsVaccins($entResId, $date_res_deb)
{   
   return $this->db->select('count(*) as nombre')
                     ->from($this->table_reservations)
                     ->where('reservations.entResId', $entResId)
                     ->where('DATE(reservations.date_res_deb)', date("Y-m-d", strtotime($date_res_deb)))
                     ->where('reservations.serviceResID', 1)
                     ->where('reservations.parentResFK', NULL)
                     ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                     ->where('reservations.etat_res', 'A')
                     ->get()
                     ->row();
}


public function isCodeRes($code_res)
{   
   return $this->db->select('*')
                     ->from($this->table_reservations)
                     ->where('reservations.code_res', $code_res)
                     ->get()
                     ->row();
}

public function isAfficherResByCodes($code_res)
{   
   return $this->db->select('*')
                     ->from($this->table_reservations)
                     ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                     ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                     ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
                     ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
                     ->where('reservations.code_res', $code_res)
                     ->where('reservations.etat_res', 'A')
                     ->get()
                     ->row();
}


// le montant a payer par l'officine pour le mois en cours


/*public function getMontantAverser($entID)
{   

 return $this->db->select('sum(reservations.montant_res) AS montant')
                ->from($this->table_reservations)
                ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                ->join('districts', 'districts.idDistricts = entreprise.districtEntrepriseId', 'left')
                ->join('communes', 'communes.id_commune = entreprise.communeEntrepriseId', 'left')
                ->where('reservations.status_res', 'S')
                ->where('reservations.reversResCode', 'N')
                ->where('reservations.etat_res', 'A')
                ->where('reservations.entResId', $entID)
                ->group_by("reservations.entResId")
                ->get()
                ->row();   
}*/

public function getMontantAverser($entID)
{   
    return $this->db->select('sum(reservations.montant_res) AS montant')
                    ->from($this->table_reservations)
                    ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                    ->join('districts', 'districts.idDistricts = entreprise.districtEntrepriseId', 'left')
                    ->join('communes', 'communes.id_commune = entreprise.communeEntrepriseId', 'left')
                    ->where('reservations.status_res', 'S')
                    ->where('reservations.reversResCode', 'N')
                    ->where('reservations.etat_res', 'A')
                    ->where('reservations.serviceResID', 1)
                    ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                    ->where('reservations.entResId', $entID)
                    ->group_by("reservations.entResId")
                    ->get()
                    ->row();   
}





// la liste des reversements non effectuer 


public function getListeNonPayer($entID)
{   

  $query = $this->db->select('*')
                ->from($this->table_reservations)
                ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                ->join('districts', 'districts.idDistricts = entreprise.districtEntrepriseId', 'left')
                ->join('communes', 'communes.id_commune = entreprise.communeEntrepriseId', 'left')
                ->where('reservations.status_res', 'S')
                ->where('reservations.reversResCode', 'N')
                ->where('reservations.etat_res', 'A')
                ->where('reservations.date_create_res <=', date("Y-m-d 23:59:59", strtotime("last day of last month")))
                ->where('reservations.entResId', $entID)
                ->where('reservations.serviceResID', 1)
                ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                ->order_by("reservations.entResId", 'asc')
               // ->group_by("reservations.date_create_res")
                ->get();
    return $query->result();
}  


// liste des montant non versés par mois 


public function getRemboursementParMois($entID)
  {

       $query = $this->db->select("DATE_FORMAT(reservations.date_create_res, '%Y-%m') AS mois,
                                respo_entreprise, entResId, nom_entreprise,
                                COUNT(reservations.id_res) AS nombre,
                                SUM(reservations.montant_res) * 0.3 AS montant,
                                contact_entreprise, nom_commune, nomDistricts")
                      ->from($this->table_reservations)
                      ->join('entreprise', 'entreprise.id_entreprise = reservations.entResId', 'left')
                        ->join('reversements', 'reversements.idRevers  = reservations.reversResId', 'left')
                      ->join('districts', 'districts.idDistricts = entreprise.districtEntrepriseId', 'left')
                      ->join('communes', 'communes.id_commune = entreprise.communeEntrepriseId', 'left')

                      ->where('reservations.reversResCode','N')
                      ->where('reservations.status_res','S')
                      ->where('reservations.etat_res', 'A')
                      ->where('reservations.serviceResID', 1)
                      ->where_not_in('reservations.adderResFK', array(451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 467, 468, 469, 470, 472, 490, 491))
                      ->where('reservations.entResId', $entID)
                      ->group_by("mois")
                      ->order_by("mois", "desc")
                      ->get();
                      return $query->result();
  }

}