<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
	class CanalDiffusions_model extends CI_Model {

    protected $table_canal_diffusion = "canal_diffusion";

    public function __construct()
    {
        parent::__construct();
    	date_default_timezone_set('UTC');
    }
	
    public function getCanalDiffusions()
    {	
        $query = $this->db->select('*')
                           ->from($this->table_canal_diffusion)
                           ->where('etat_canal_diffusion', 'A')
                           ->order_by("libelle_canal_diffusion","asc")
                           ->get();
             return $query->result();
    }

}