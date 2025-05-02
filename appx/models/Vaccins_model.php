<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
	class Vaccins_model extends CI_Model {

    protected $table_vaccins = "vaccins";
    protected $table_vaccinBySousVaccins = "vaccinBySousVaccins";
    protected $table_vaccinations = "vaccinations";

    public function __construct()
    {
        parent::__construct();
    	  date_default_timezone_set('UTC');
    }
	
    public function isVaccinsActifs($idVaccins)
    {	
       return $this->db->select('*')
			 				   ->from($this->table_vaccins)
			 				   ->where('etatVaccins', 'A')
                        ->where('idVaccins', $idVaccins)
                        ->get()
                        ->row();
    }

    public function getListeVaccinsBySousVaccins($sousVaccinsFk)
    {       
           $query = $this->db->select('*')
                            ->from($this->table_vaccinBySousVaccins)
                            ->join('vaccins', 'vaccins.idVaccins = vaccinBySousVaccins.vaccinsFk', 'left')
                            ->where('vaccinBySousVaccins.etatVaccinBySousVaccins', 'A')
                            ->where('vaccinBySousVaccins.sousVaccinsFk', $sousVaccinsFk)
                            ->order_by("vaccins.nomVaccins","asc")
                            ->get();
            return $query->result();
    }

    public function getVaccinsActifs()
    {   
        $query = $this->db->select('*')
                           ->from($this->table_vaccins)
                           ->where('etatVaccins', 'A')
                           ->order_by("nomVaccins","asc")
                           ->get();
             return $query->result();
    }

    public function getVaccinations($reservationsFk)
    {     
       $query = $this->db->select('idVaccinations, reservationsFk, idVaccins, maladieTraitee, nomVaccins, nombreDoses, codeVaccins')
                     ->from($this->table_vaccinations)
                     ->join('vaccins', 'vaccins.idVaccins = vaccinations.vaccinsFk', 'left')
                     ->where('vaccinations.reservationsFk', $reservationsFk)
                      ->order_by("vaccinations.idVaccinations","desc")
                     ->get();
        return $query->result();
    }

    public function getVaccinsByArrays($tab)
    {   
        $query = $this->db->select('*')
                           ->from($this->table_vaccins)
                           ->where('id_vaccins', $tab)
                           ->get();
             return $query->result();
    }

}