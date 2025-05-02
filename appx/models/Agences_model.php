<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Agences_model extends CI_Model {

public function __construct()
{
    parent::__construct();
    date_default_timezone_set('UTC');
}

protected $table_entreprise = "entreprise";

public function getListeAgences()
{	
	$query = $this->db->select('*')
	 				 ->from($this->table_entreprise)
					 ->where('entreprise.etat_entreprise', "A")
                // ->where('entreprise.id_entreprise !=', 1)
					 ->order_by("entreprise.raison_sociale","asc")
					 ->get();
    return $query->result(); 
}

public function isIdAgences($id_entreprise)
{ 
   return $this->db->select('*')
                  ->from($this->table_entreprise)
                  ->where('entreprise.id_entreprise', $id_entreprise)
                  ->where('entreprise.etat_entreprise', "A")
                  ->get()
                  ->row();    
}



}