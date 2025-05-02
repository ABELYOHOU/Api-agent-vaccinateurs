<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
    class Versions_model extends CI_Model {

    protected $table_versions = "versions";

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
    }
    
    public function getVersionsActifs()
    {   
        return $this->db->select('buildNumbers, versionsNumbers')
                       ->from($this->table_versions)
                       ->where('etatVersions', 'A')
                       ->get()
                       ->row();
    }

}