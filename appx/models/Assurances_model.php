<?php
defined ('BASEPATH') OR exit ('No  direct   script   access   allowed');
//CREER LE CONSTRUCTEUR DU MODEL

class Assurances_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
    }
    
    protected $table_accepte_assur = "accepte_assur";
    protected $table_assurances = "assurances";

    public function getAssurancesActifs()
    {
       $query = $this->db->select('*')
                         ->from($this->table_assurances)
                         ->where('etat_assurances', 'A')
                         ->order_by("libelle_assurances  ","asc")
                         ->get();
        return $query->result();
    }

    public function getListeAssurByOfficines($entrepriseAssurID)
    {
       $query = $this->db->select('*')
                         ->from($this->table_accepte_assur)
                         ->join('assurances', 'assurances.id_assurances = accepte_assur.assuranceOfficineID', 'left')
                         ->where('accepte_assur.entrepriseAssurID', $entrepriseAssurID)
                         ->where('accepte_assur.etat_accepte_assur !=', 'S')
                         ->order_by("assurances.libelle_assurances  ","asc")
                         ->get();
        return $query->result();
    }

  

}