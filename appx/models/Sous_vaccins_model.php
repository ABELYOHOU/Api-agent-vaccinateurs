<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
	class Sous_vaccins_model extends CI_Model {

    protected $table_sous_vaccins = "sous_vaccins";

    public function __construct()
    {
        parent::__construct();
    	date_default_timezone_set('UTC');
    }
	
    public function getSousVaccinsActifs()
    {	
        $query = $this->db->select('*')
		 				   ->from($this->table_sous_vaccins)
		 				   ->where('etat_sous_vaccins', 'A')
		 				   ->order_by("nom_sous_vaccins","asc")
						   ->get();
		     return $query->result();
    }

    public function getListesSousVaccinsByCategories($categorieVaccinsID)
    {       
           $query = $this->db->select('*')
                            ->from($this->table_sous_vaccins)
                            ->where('sous_vaccins.etat_sous_vaccins', 'A')
                            ->where('sous_vaccins.categorieVaccinsID', $categorieVaccinsID)
                            ->get();
            return $query->result();
    }

}