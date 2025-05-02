<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
    class Quartiers_model extends CI_Model {

    protected $table_quartiers = "quartiers";

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
    }
    
    public function getQuartiersActifs()
    {   
        $query = $this->db->select('*')
                           ->from($this->table_quartiers)
                           ->where('etat_quartiers', 'A')
                           ->order_by("nom_quartiers","asc")
                           ->get();
             return $query->result();
    }

    public function getQuartiersByCommune($communeQuartiersId)
    {   
        $query = $this->db->select('*')
                           ->from($this->table_quartiers)
                           ->where('etat_quartiers', 'A')
                           ->where('communeQuartiersId', $communeQuartiersId)
                           ->order_by("nom_quartiers","asc")
                           ->get();
             return $query->result();
    }

}