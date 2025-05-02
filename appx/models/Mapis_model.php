<?php
defined ('BASEPATH') OR exit ('No  direct   script   access   allowed');
//CREER LE CONSTRUCTEUR DU MODEL

  class Mapis_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
    }
    
    protected $table_mapis = "mapis";
    protected $table_reservations = "reservations";

    public function getListeRDVsByPatientsId($entID, $patientsResId)
    {     
          $query = $this->db->select('*')
                            ->from($this->table_reservations)
                            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
                            ->where('reservations.etat_res', 'A')
                            ->where('reservations.status_res', 'S')
                            ->where('reservations.serviceResID', 1)
                            ->where('reservations.entResId', $entID)
                            ->where('reservations.patientsResId', $patientsResId)
                            ->order_by("reservations.date_maj_res","desc")
                            ->get();
               return $query->result();
    }

    public function getMapis($entID)
    {  
       $query = $this->db->select('*')
                         ->from($this->table_mapis)
                         ->join('patients', 'patients.id_patients = mapis.patientsMapisId', 'left')
                         ->join('communes', 'communes.id_commune = patients.communePatientsId', 'left')
                         ->join('reservations', 'reservations.id_res = mapis.resMapisId', 'left')
                         ->where('mapis.entMapisId', $entID)
                         ->order_by("mapis.dateCreateMapis","asc")
                         ->get();
        return $query->result();
    }

    public function getMapisActifs()
    {  
       $query = $this->db->select('*')
                         ->from($this->table_mapis)
                         ->where('etatMapis', 'A')
                         ->where('entMapisId', $entID)
                         ->order_by("dateCreateMapis","asc")
                         ->get();
        return $query->result();
    }


    

    public function existance($patientsMapisId, $resMapisId)
    {     
          $query = $this->db->select('*')
                           ->from($this->table_mapis)
                           ->where('patientsMapisId', $patientsMapisId)
                           ->where('resMapisId', $resMapisId)
                           ->get();
          return $query->result();    
    }

    public function isGetsMapis($idMapis)
    {   
          $query = $this->db->select('*')
                           ->from($this->table_mapis)
                           ->where('idMapis', $idMapis)
                           ->get();
          return $query->result();    
    }

    public function isIdMapis($idMapis)
    {    
         return $this->db->select('*')
                        ->from($this->table_mapis)
                        ->join('patients', 'patients.id_patients = mapis.patientsMapisId', 'left')
                        ->where('mapis.idMapis', $idMapis)
                        ->get()
                        ->row();    
    }

    public function insererMapis($patientsMapisId, $resMapisId, $observationsMapis, $adderMapisFk, 
    $entMapisId, $districtsID)
    {         
                 $this->db->set('patientsMapisId', $patientsMapisId)
                          ->set('resMapisId', $resMapisId)
                          ->set('observationsMapis', $observationsMapis)
                          ->set('entMapisId', $entMapisId)
                          ->set('districtsMapisId', $districtsID)
                          ->set('etatMapis', 'I')
                          ->set('dateCreateMapis', date("Y-m-d H:i:s"))
                          ->set('adderMapisFk', $adderMapisFk)
                          ->insert($this->table_mapis);
          return $this->db->insert_id();
    }

     public function deleteMapis($idMapis)
     {
        return $this->db->where('idMapis', $idMapis)
                        ->delete($this->table_mapis);
     }


     public function activerMapis($idMapis)
     {
        return $this->db->set('etatMapis', "A")
                        ->set('dateMajMapis', date("Y-m-d H:i:s"))
                        ->where('idMapis', $idMapis)
                        ->update($this->table_mapis);
     }

     public function desactiverMapis($idMapis)
     {
        return $this->db->set('etatMapis', "S")
                        ->set('dateMajMapis', date("Y-m-d H:i:s"))
                        ->where('idMapis', $idMapis)
                        ->update($this->table_mapis);
     }

}