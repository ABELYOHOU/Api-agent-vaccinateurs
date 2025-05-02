<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Type_institutions_model extends CI_Model {

public function __construct()
{
   parent::__construct();
   date_default_timezone_set('UTC');
}

protected $table_type_entreprise = "type_entreprise";
protected $table_type_stations = "type_stations";

public function getTypeInstitutions()
{	
	$query = $this->db->select('*')
		 				 ->from($this->table_type_entreprise)
		 				 //->where('type_entreprise.id_type_entreprise !=', 5)
						 ->where('type_entreprise.etat_type_entreprise', "A")
						 ->get();
	   return $query->result(); 
}

public function getTypeStationsActifs()
{	
  	$query = $this->db->select('*')
		 				   ->from($this->table_type_stations)
		 				   ->where('etat_type_stations', 'A')
		 				   ->order_by("libelle_type_stations","asc")
						   ->get();
     	  return $query->result();
}



}