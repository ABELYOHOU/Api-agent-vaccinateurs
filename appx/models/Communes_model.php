<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
	class Communes_model extends CI_Model {

    protected $table_communes = "communes";

    public function __construct()
    {
        parent::__construct();
    	date_default_timezone_set('UTC');
    }
	
    public function getCommunesActifs()
    {	
        $query = $this->db->select('*')
                           ->from($this->table_communes)
                           ->where('etat_commune', 'A')
                           ->where('villeCommuneFK', 1)
                           ->order_by("nom_commune","asc")
                           ->get();
             return $query->result();
    }

}