<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
	class Institutions_model extends CI_Model {

    protected $table_entreprise = "entreprise";
    protected $table_pharmaciesGarde = "pharmaciesGarde";
    protected $table_periodeGarde = "periodeGarde";
    protected $table_communes = "communes";
    protected $table_users = "users";
    
    public function __construct()
    {
        parent::__construct();
    	date_default_timezone_set('UTC');
    }

    public function isEntrepriseByQuartiersId($quartierEntrepriseId, $communeEntrepriseId, 
    $indexJourVaccination)
    {   
        $query =  $this->db->select('*')
                           ->from($this->table_entreprise)
                           ->join('quartiers', 'quartiers.id_quartiers = entreprise.quartierEntrepriseId', 'left')
                           ->join('JourVaccination', 'JourVaccination.centreId = entreprise.id_entreprise', 'left')
                           ->where('entreprise.etat_entreprise', 'A')
                           ->where('entreprise.isAgree', '1')
                           //LES LIGNES A DECOMMENTER
                           //->where('JourVaccination.indexJourVaccination', $indexJourVaccination)
                           ->where('entreprise.quartierEntrepriseId', $quartierEntrepriseId)
                           ->order_by("entreprise.nom_entreprise","asc")
                           ->limit(1)
                           ->get();
        return $query->result();            
    }
	
    public function getPharmaciesByCommunesId($communeEntrepriseId)
    {	
        $query = $this->db->select('*')
		 				   ->from($this->table_pharmaciesGarde)
                           ->join('entreprise', 'entreprise.id_entreprise = pharmaciesGarde.pharmaciesID', 'left')
                           ->join('periodeGarde', 'periodeGarde.idPeriodeGarde = pharmaciesGarde.periodeGardeID', 'left')
                           ->join('communes', 'communes.id_commune = entreprise.communeEntrepriseId', 'left')
		 				   ->where('pharmaciesGarde.etatPharmaciesGarde', 'A');

                        if($communeEntrepriseId != 'T')
                        {
                           $this->db->where('entreprise.communeEntrepriseId', $communeEntrepriseId);
                        }

        $query = $this->db->order_by("entreprise.nom_entreprise","asc")
                          ->get();
            return $query->result();			 
    }

    public function isPeriodesGarde()
    {       
         return $this->db->select('*')
                         ->from($this->table_periodeGarde)
                         ->where('etatPeriodePeriode', 'A')
                         ->get()
                         ->row();
    }

    public function getInstitutionsByCommunesId($communeEntrepriseId)
    {   
        $query = $this->db->select('*')
                           ->from($this->table_entreprise)
                           ->join('communes', 'communes.id_commune = entreprise.communeEntrepriseId', 'left')
                           ->join('quartiers', 'quartiers.id_quartiers = entreprise.quartierEntrepriseId', 'left')
                           ->where('entreprise.typeEntrepriseID', 2)
                           ->where('entreprise.etat_entreprise', 'A');

                        if($communeEntrepriseId != 'T')
                        {
                           $this->db->where('entreprise.communeEntrepriseId', $communeEntrepriseId);
                        }

        $query = $this->db->order_by("entreprise.nom_entreprise","asc")
                          ->get();
            return $query->result();             
    }

    public function insererClients($typeEntrepriseID, $nom_entreprise,
    $respo_entreprise, $situationGeoEntreprise, $contact_entreprise,$contact2_entreprise, $email_entreprise, 
    $communeEntrepriseId, $isAgree, $nom_users, $prenoms_users)
    {               
         $villeEntrepriseId = $this->isCommunes($communeEntrepriseId)->villeCommuneFK;
                 $this->db->set('nom_entreprise', $nom_entreprise)
                          ->set('situationGeoEntreprise', $situationGeoEntreprise)
                          ->set('respo_entreprise', $respo_entreprise)
                          ->set('typeEntrepriseID', $typeEntrepriseID)
                          ->set('contact2_entreprise', $contact2_entreprise)
                          ->set('isAgree', $isAgree)
                          ->set('communeEntrepriseId', $communeEntrepriseId)
                          ->set('contact_entreprise', $contact_entreprise)
                          ->set('email_entreprise', $email_entreprise)
                          ->set('villeEntrepriseId', $villeEntrepriseId)
                          ->set('etat_entreprise', 'I')
                          ->set('date_create_entreprise', date("Y-m-d H:i:s"))
                          ->insert($this->table_entreprise);
        $entID = $this->db->insert_id();

        if ($entID) 
        {         

                  if ((int)$typeEntrepriseID == 1) 
                  {
                      $rolesId = 6;
                  }
                  else
                  {
                      $rolesId = 4;
                  }

                   $password = 'demo';
                   $pass_users = password_hash($password, PASSWORD_DEFAULT);
                   $this->db->set('prenoms_users', $prenoms_users)
                            ->set('rolesId', $rolesId)
                            ->set('mobile_users', $contact_entreprise)
                            ->set('email_users', $email_entreprise)
                            ->set('pass_users', $pass_users)
                            ->set('entrepriseId', $entID)
                            ->set('nom_users', $nom_users)
                            ->set('etat_users', 'I')
                            ->set('date_create_users', date("Y-m-d H:i:s"))
                            ->insert($this->table_users);
            return $this->db->insert_id();
        }
        else
        {
           return FALSE;
        }
    }

    public function isCommunes($id_commune)
    {   
        return $this->db->select('*')
                       ->from($this->table_communes)
                       ->where('id_commune', $id_commune)
                       ->get()
                       ->row();
    }

}

