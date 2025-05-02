<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Demandes_model extends CI_Model {

public function __construct()
{
    parent::__construct();
    date_default_timezone_set('UTC');
}

protected $table_demandes = "demandes";

 public function insererDemandes($qteDemandes, $stockDemandeID, 
    $vaccinsDemandesID, $commentDemandes, $codeDemandes)
    {         
             $id_users = $this->session->userdata('id_users');
             $entID = $this->session->userdata('entID');

             if ((int)$stockDemandeID == 1) 
             {
                  $districtDemandeID = $this->session->userdata('districtsID');
                  $sanitaireDemandeID = NULL;
             }
             else
             {
                  $districtDemandeID = NULL;
                  $sanitaireDemandeID = $this->session->userdata('aireSanitaireID');
             }

                   $this->db->set('sanitaireDemandeID', $sanitaireDemandeID)
                            ->set('entDemandeID', $entID)
                            ->set('districtDemandeID', $districtDemandeID)
                            ->set('vaccinsDemandesID', $vaccinsDemandesID)
                            ->set('commentDemandes', $commentDemandes)
                            ->set('stockDemandeID', $stockDemandeID)
                            ->set('codeDemandes', $codeDemandes)
                            ->set('qteDemandes', (int)$qteDemandes)
                            ->set('etatDemandes', 'A')
                            ->set('statusDemandes', 'P')
                            ->set('dateCreateDemandes', date("Y-m-d H:i:s"))
                            ->set('adderDemandeID', $id_users)
                            ->insert($this->table_demandes);
            return $this->db->insert_id();
    }



}
