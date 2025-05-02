<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
    class Horaires_model extends CI_Model {

    protected $table_plageHoraires = "plageHoraires";

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
    }
    
    public function getHorairesActifs()
    {   
        $query = $this->db->select('*')
                           ->from($this->table_plageHoraires)
                           ->where('etatPlageHoraires', 'A')
                           ->order_by("rangPlageHoraires","asc")
                           ->get();
             return $query->result();
    }

}