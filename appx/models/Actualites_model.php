<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Actualites_model extends CI_Model {

public function __construct()
{
    parent::__construct();
    date_default_timezone_set('UTC');
}

protected $table_actus_events = "actus_events";

public function getListeActualites()
{	
	$query = $this->db->select('*')
   	 				 ->from($this->table_actus_events)
   					 ->where('actus_events.etat_actus_events', "A")
   					 ->order_by("actus_events.date_create_actus_events","desc")
   					 ->get();
    return $query->result(); 
}

public function isIdActualites($id_actus_events)
{ 
   return $this->db->select('*')
                  ->from($this->table_actus_events)
                  ->where('actus_events.id_actus_events', $id_actus_events)
                  ->get()
                  ->row();    
}



}