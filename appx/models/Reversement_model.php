<?php
defined ('BASEPATH') OR exit ('No  direct   script   access   allowed');
//CREER LE CONSTRUCTEUR DU MODEL

class Reversement_model extends CI_Model {

public function __construct()
{
    parent::__construct();
    date_default_timezone_set('UTC');
}



protected $table_reversement = "reversements";



public function getReversements($entID)
{  
  // $entID = $this->session->userdata('entID'); 
   $query = $this->db->select('*')
                     ->from($this->table_reversement)
                      ->join('entreprise', 'entreprise.id_entreprise = reversements.entReversID', 'left')
                    ->join('districts', 'districts.idDistricts = entreprise.districtEntrepriseId', 'left')
                   ->join('mode_paiement', 'mode_paiement.id_mode_paiement = reversements.modeReversID', 'left')
                     ->where('reversements.entReversID', $entID)
                     ->where('reversements.etatRevers !=', 'S')
                      ->where('reversements.wavePaymentStatus', 'succeeded')
                     ->order_by('reversements.idRevers', 'desc')
                     ->get();
        return $query->result();
}

 




}



