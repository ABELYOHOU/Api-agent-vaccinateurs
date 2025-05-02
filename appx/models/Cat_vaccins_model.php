<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
	class Cat_vaccins_model extends CI_Model {

    protected $table_cat_vaccins = "cat_vaccins";

    public function __construct()
    {
        parent::__construct();
    	date_default_timezone_set('UTC');
    }
	
    public function getCatVaccinsActifs()
    {	
        $query = $this->db->select('*')
		 				   ->from($this->table_cat_vaccins)
		 				   ->where('etat_cat_vaccins', 'A')
		 				   ->order_by("nom_cat_vaccins","asc")
						   ->get();
		     return $query->result();
    }

}