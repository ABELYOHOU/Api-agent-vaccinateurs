    <?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class Vaccinateurs_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
    }

    protected $table_vaccinateurs = "vaccinateurs";
    protected $table_status_commande = "status_cmd";
    protected $table_newsletters = "newsletters";
    protected $table_entreprise = "entreprise";
    protected $table_patients = "patients";
    protected $table_cat_vaccins = "cat_vaccins";   
    protected $table_sous_vaccins = "sous_vaccins";
    protected $table_dossiers_patients = "dossiers_patients";
    protected $table_patient_canal = "patient_canal";
    protected $table_motifs_status_cmd = "motifs_status_cmd";
    protected $table_zones_affecter = "zones_affecter";
    protected $table_vaccins = "vaccins";
    protected $table_vaccinBySousVaccins = "vaccinBySousVaccins";
    protected $table_vaccinations = "vaccinations";
    protected $table_transactions = "transactions";
    protected $table_reservations = "reservations";




    public function FinaliserCommandes($numeroLots, $refCommande, $montant_res, $qteProduits, $parentResFK, $date_res_deb, $date_res_end)
    {           

        $this->db->set('parentResFK', $parentResFK)
                 ->set('numLotsDistricts', $numeroLots)
                 ->set('montant_res', (float)$montant_res)
                 ->set('date_res_end',$date_res_end)
                 ->set('date_res_deb', $date_res_deb)
                 ->set('qteProduits', $qteProduits)
                 ->set('status_res', 'S') 
                 ->set('modePaieID', 1)
                 ->set('date_maj_res', date("Y-m-d H:i:s"))
                 ->where('code_res', $refCommande)
                 ->update($this->table_reservations);


    
        $this->db->set('operateur_trans', null)
                 ->set('lotsResTrans', $numeroLots)
                 ->set('mobile_paiement', null)
                 ->set('reference_syca', null)
                 ->set('modePayerId', 1)
                 ->set('montant_trans', (float)$montant_res)
                 ->set('frais_trans', 0)
                 ->set('date_maj_trans', date("Y-m-d H:i:s"))
               /*  ->set('resID', $reservationID)
                 ->set('patientsFK', $patientsResId)*/
                 ->set('etat_trans', 'A')
                 ->set('reversCode', 'N')
                 ->set('servicesTransID', 2)
                 ->set('status_trans', 'S')
                 ->set('date_create_trans', date("Y-m-d H:i:s"))
                 ->insert($this->table_transactions);

      // return $reservationID;
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


         public function getCatVaccinsPevActifs()
    {   
        $query = $this->db->select('*')
                           ->from($this->table_cat_vaccins)
                           ->where('etat_cat_vaccins', 'A')
                           ->where('rang_cat_vaccins', 1)
                           ->order_by("nom_cat_vaccins","asc")
                           ->get();
             return $query->result();
    }


         public function getCatVaccinsHorsPevActifs()
    {   
        $query = $this->db->select('*')
                           ->from($this->table_cat_vaccins)
                           ->where('etat_cat_vaccins', 'A')
                           ->where_in('id_cat_vaccins', array(1,2,9,10))
                           ->order_by("nom_cat_vaccins","asc")
                           ->get();
             return $query->result();
    }


    public function createVaccinations($reservationsFk, $vaccinsFk, 
    $patientsResId, $patientsFk)
    {           
                 $this->db
                          ->set('reservationsFk', $reservationsFk)
                          ->set('vaccinsFk', $vaccinsFk)
                          ->set('parentResID', $patientsResId)
                          ->set('patientsFk', $patientsFk)
                          ->set('etatVaccinations', 'A')
                          ->set('dateCreateVaccinations', date("Y-m-d H:i:s"))
                          ->insert($this->table_vaccinations);
          return $this->db->insert_id();
    }

     

     public function getSousVaccinsPevActifs()
        {   
            $query = $this->db->select('*')
                               ->from($this->table_sous_vaccins)
                               ->where('etat_sous_vaccins', 'A')
                               ->order_by("nom_sous_vaccins","asc")
                               ->get();
                 return $query->result();
        }

        public function getListesSousVaccinsPevByCategories($categorieVaccinsID)
        {       
               $query = $this->db->select('*')
                            ->from($this->table_sous_vaccins)
                            ->where('sous_vaccins.etat_sous_vaccins', 'A')
                             ->where('sous_vaccins.rang_sous_vaccins', 1)
                            ->where('sous_vaccins.categorieVaccinsID', $categorieVaccinsID)
                            ->get();
            return $query->result();
        }


         public function getListesSousVaccinsByCatChoisi($categorieVaccinsID)
    {       
           $query = $this->db->select('*')
                            ->from($this->table_sous_vaccins)
                            ->where('sous_vaccins.rang_sous_vaccins', 2)
                            ->where('sous_vaccins.etat_sous_vaccins', 'A')
                            ->where('sous_vaccins.categorieVaccinsID', $categorieVaccinsID)
                            ->get();
            return $query->result();
    }

    public function transfererCommande($ResCommandes,$motifStatusCmdID)
    {  
       return $this->db->set('status_status_cmd', 'T')
                        ->set('details_status_cmd', "TRANSFERER")
                        ->set('motifStatusCmdID', $motifStatusCmdID)
                        ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                        ->where('status_cmd.resStatusCmdID', $ResCommandes)
                        ->update($this->table_status_commande);

    }



    public function reporterCommande($ResCommandes,$motifStatusCmdID)
    {  
       return $this->db->set('status_status_cmd', 'R')
                        ->set('details_status_cmd', "REPORTER")
                        ->set('motifStatusCmdID', $motifStatusCmdID)
                        ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                        ->where('status_cmd.resStatusCmdID', $ResCommandes)
                        ->update($this->table_status_commande);

    }

    public function commandeTraitement($ResCommandes)
    {  
       return $this->db->set('status_status_cmd', 'L')
                        ->set('details_status_cmd', "TRAITEMENT")
                        ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                        ->where('status_cmd.resStatusCmdID', $ResCommandes)
                        ->update($this->table_status_commande);

    }



       public function commandeEffectuee($ResCommandes)
    {
        $req1 = $this->db->set('status_status_cmd', 'S')
                        ->set('details_status_cmd', "TERMINÉE")
                        ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                        ->where('status_cmd.resStatusCmdID', $ResCommandes)
                        ->update($this->table_status_commande);

        if ($req1) {
            $req2 = $this->db->set('status_res', 'S')
                             ->set('etat_res', 'A')
                             ->set('devicesID', 2)
                             ->set('serviceResID', 2)
                             ->set('date_maj_res', date("Y-m-d H:i:s"))
                             ->where('id_res', $ResCommandes) 
                             ->update($this->table_reservations);

            return $req2;
        }

        return FALSE;
    }



    public function isCodeRes($code_res)
    {   
       return $this->db->select('*')
                         ->from($this->table_reservations)
                         ->where('reservations.code_res', $code_res)
                         ->get()
                         ->row();
    }

    public function isDetailsMotifs($id_details_annulation)
    {   
       return $this->db->select('*')
                         ->from($this->table_motifs_status_cmd)
                         ->where('motifs_status_cmd.libelle_details_annulation', $id_details_annulation)
                         ->get()
                         ->row();
    }

    public function isDossiersByPatientsID($patientsDossiersID)
    {       
         return $this->db->select('*')
                         ->from($this->table_dossiers_patients)
                         ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = dossiers_patients.catVaccinsID', 'left')
                         ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = dossiers_patients.sousVaccinsID', 'left')
                         ->join('patients', 'patients.id_patients = dossiers_patients.patientsDossiersID', 'left')
                         ->where('dossiers_patients.etatDossiersPatients', 'A')
                         ->where('dossiers_patients.patientsDossiersID', $patientsDossiersID)
                         ->order_by("dossiers_patients.idDossiersPatients","desc")
                         ->limit(1)
                         ->get()
                         ->row();
    }



    public function getPatientsByMobiles($contact_patients)
    {   
         $query = $this->db->select('*')
                            ->from($this->table_patients)
                            ->join('communes', 'communes.id_commune = patients.communePatientsId', 'left')
                            ->join('dossiers_patients', 'dossiers_patients.patientsDossiersID = patients.id_patients', 'left')
                            ->where('patients.etat_patients', 'A')
                            ->where('dossiers_patients.etatDossiersPatients', "A")
                            ->where('patients.contact_patients', $contact_patients)
                            ->order_by("patients.id_patients","desc")
                            ->get();
               return $query->result();
    }

   
    public function getListCommandesDuJourEnCours($vaccinateurID)
    {       
        $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
             ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
            ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'P')
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->where("status_cmd.date_initiale_deb >= ", date('Y-m-d 00:00:00'))
            ->where("status_cmd.date_initiale_end <= ", date('Y-m-d 23:59:59'))
            ->order_by("reservations.id_res", "desc")
            ->get();
        
        return $query->result();
    }

    public function nombreCommandesDuJourEnCours($vaccinateurID)
    {       
        $query = $this->db->select('count(vaccinateursFk) as nombre')
            ->from($this->table_status_commande)
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'P')
            ->where('status_cmd.vaccinateursFk', $vaccinateurID)
            ->where("status_cmd.date_initiale_deb >= ", date('Y-m-d 00:00:00'))
            ->where("status_cmd.date_initiale_end <= ", date('Y-m-d 23:59:59'))
            ->get()
            ->row();  
        return $query; 
    }


    public function nombreCommandesDuJourEffectuee($vaccinateurID)
    {       
        $query = $this->db->select('count(vaccinateursFk) as nombre')
            ->from($this->table_status_commande)
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'S')
            ->where('status_cmd.vaccinateursFk', $vaccinateurID)
            ->where("status_cmd.date_initiale_deb >= ", date('Y-m-d 00:00:00'))
            ->where("status_cmd.date_initiale_end <= ", date('Y-m-d 23:59:59'))
            ->get()
            ->row();  
        
        return $query; 
    }


    public function getTotalCommandesConfirmes($vaccinateurID)
    {     
          return $this->db->select('count(vaccinateursFk) as nombre')
                         ->from($this->table_status_commande)
                         ->where('status_cmd.etat_status_cmd', 'A')
                         ->where('status_cmd.status_status_cmd', 'S')
                         ->where('status_cmd.vaccinateursFk', $vaccinateurID)
                         ->get()
                         ->row();
    }

    public function getTotalCommandesPending($vaccinateurID)
    {     
           return $this->db->select('count(vaccinateursFk) as nombre')
                         ->from($this->table_status_commande)
                         ->where('status_cmd.etat_status_cmd', 'A')
                         ->where('status_cmd.status_status_cmd', 'P')
                         ->where('status_cmd.vaccinateursFk', $vaccinateurID)
                         ->get()
                         ->row();
    }


    public function getMontantTotalPending($vaccinateurID)
    {     
       return $this->db->select('count(id_status_cmd) as nombre, sum(montant_res) as montant')
                       ->from($this->table_status_commande)
                       ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
                       ->where('reservations.etat_res', 'A')
                       ->where('status_cmd.status_status_cmd', 'P')
                     //  ->where('status_cmd.vaccinateursFk', $vaccinateurID)
                       ->where('reservations.agentsVaccinsFk', $vaccinateurID)
                       ->get()
                       ->row();    
    }


    public function getMontantTotalEffectue($vaccinateurID)
    {     
       return $this->db->select('count(id_status_cmd) as nombre, sum(montant_res) as montant')
                       ->from($this->table_status_commande)
                       ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
                       ->where('reservations.etat_res', 'A')
                       ->where('status_cmd.status_status_cmd', 'S')
                     //  ->where('status_cmd.vaccinateursFk', $vaccinateurID)
                       ->where('reservations.agentsVaccinsFk', $vaccinateurID)
                       ->get()
                       ->row();    
    }

    public function nombreCommandesDuJourByAgent($vaccinateurID)
    {       
        $query = $this->db->select('count(vaccinateursFk) as nombre')
            ->from($this->table_status_commande)
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.vaccinateursFk', $vaccinateurID)
            ->where("status_cmd.date_initiale_deb >= ", date('Y-m-d 00:00:00'))
            ->where("status_cmd.date_initiale_end <= ", date('Y-m-d 23:59:59'))
            ->get()
            ->row();  
        
        return $query; 
    }



    public function getListCommandesEnCoursBySemaine($vaccinateurID)

    {      
             $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
             ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
             ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'P')
            ->where('status_cmd.date_initiale_deb >=', date('Y-m-d 00:00:00', strtotime("monday this week")))
            ->where('status_cmd.date_initiale_end <=', date('Y-m-d 23:59:59'))
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->order_by("reservations.id_res","desc")
            ->get();
        
        return $query->result();
    }

    public function getListCommandes($vaccinateurID,$date_min, $date_max)

    {      
             $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
             ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
             ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.date_initiale_deb >=', $date_min)
            ->where('status_cmd.date_initiale_deb <=', $date_max)
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->order_by("reservations.id_res","desc")
            ->get();
        
        return $query->result();
    }


    public function getListCommandesEnCoursByAgents($vaccinateurID,$date_min, $date_max)

    {      
             $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
            ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
             ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'P')
            ->where('status_cmd.date_initiale_deb >=', $date_min)
            ->where('status_cmd.date_initiale_deb <=', $date_max)
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->order_by("reservations.id_res","desc")
            ->get();
        
        return $query->result();
    }


    public function getListCommandesTermineesByAgents($vaccinateurID)

    {      
             $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
             ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'S')
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->order_by("reservations.id_res","desc")
            ->get();
        
        return $query->result();
    }

   

    public function getListCommandesTransfererByAgents($vaccinateurID)

    {      
             $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
             ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
              ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'T')
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->order_by("reservations.id_res","desc")
            ->get();
        
        return $query->result();
    }


    public function getListCommandesEnTraitementByAgents($vaccinateurID)

    {      
             $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
            ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
            ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'L')
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->order_by("reservations.id_res","desc")
            ->get();
        
        return $query->result();
    }


    public function getListCommandesAnnuleesByAgents($vaccinateurID)

    {      
             $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
            ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'A')
            ->where('status_cmd.status_status_cmd', 'A')
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->order_by("reservations.id_res","desc")
            ->get();
        
        return $query->result();
    }


    public function getListCommandesReporteesByAgents($vaccinateurID)

    {      
             $query = $this->db->select('*')
            ->from($this->table_status_commande)
            ->join('reservations', 'reservations.id_res = status_cmd.resStatusCmdID', 'left')
            ->join('cat_vaccins', 'cat_vaccins.id_cat_vaccins = reservations.catVaccinsResID', 'left')
            ->join('communes', 'communes.id_commune = reservations.communesResID', 'left')
            ->join('quartiers', 'quartiers.id_quartiers = reservations.quartiersResID', 'left')
            ->join('sous_vaccins', 'sous_vaccins.id_sous_vaccins = reservations.sousVaccinsResID', 'left')
            ->join('patients', 'patients.id_patients = reservations.patientsResId', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = reservations.agentsVaccinsFk', 'left')
            ->where('status_cmd.etat_status_cmd', 'R')
            ->where('status_cmd.status_status_cmd', 'A')
            ->where('reservations.agentsVaccinsFk', $vaccinateurID)
            ->order_by("reservations.id_res","desc")
            ->get();
        
        return $query->result();
    }

  

    public function getListDetailTransfertCommandes()

    {      
             $query = $this->db->select('*')
            ->from($this->table_motifs_status_cmd)
            ->where('motifs_status_cmd.etat_details_annulation', 'A')
            ->order_by("motifs_status_cmd.libelle_details_annulation","asc")
            ->get();
        
        return $query->result();
    }


    public function getListZoneAffecterByAgents($vaccinateurID)

    {      
             $query = $this->db->select('*')
            ->from($this->table_zones_affecter)
            ->join('quartiers', 'quartiers.id_quartiers = zones_affecter.quartiersAffecterID', 'left')
            ->join('communes', 'communes.id_commune = zones_affecter.communesAffecterID', 'left')
            ->join('districts', 'districts.idDistricts = zones_affecter.districtsAffecterID', 'left')
            ->join('vaccinateurs', 'vaccinateurs.id_vaccinateurs = zones_affecter.vaccinateursAffecterID', 'left')
            ->where('zones_affecter.etat_zones_affecter', 'A')
            ->where('zones_affecter.vaccinateursAffecterID', $vaccinateurID)
            ->order_by("zones_affecter.date_create_zones_affecter","desc")
            ->get();
        
        return $query->result();
    }



    public function isIdentifier($login, $password)
    {
        $query = $this->db->select('*')
                         ->from($this->table_vaccinateurs)
                         ->where('vaccinateurs.etat_vaccinateurs', 'A')
                         ->where('vaccinateurs.mobile_vaccinateurs', $login)
                         ->get()
                         ->row();

        if($query && password_verify($password, $query->pass_vaccinateurs))
        {
            return $query;
        }
        else
        {
            return NULL;
        }
    }

    public function isGetsVisiteurs($id_vaccinateurs)
    {       
         $query = $this->db->select('*')
                         ->from($this->table_vaccinateurs)
                         ->where('vaccinateurs.etat_vaccinateurs', 'A')
                         ->where('vaccinateurs.id_vaccinateurs', $id_users)
                         ->get();
            return $query->result();
    }


    public function changer_mot_passe($id_vaccinateurs, $password)
    {       
        $pass_vaccinateurs = password_hash($password, PASSWORD_DEFAULT);
        return $this->db->set('pass_vaccinateurs', $pass_vaccinateurs)
                        ->set('date_maj_vaccinateurs', date("Y-m-d H:i:s"))
                        ->where('id_vaccinateurs', $id_vaccinateurs)
                        ->update($this->table_vaccinateurs);
    }

    public function majMonCompte($id_vaccinateurs, $nom_vaccinateurs, $prenoms_vaccinateurs)
    {       
        return $this->db->set('nom_vaccinateurs', $nom_vaccinateurs)
                        ->set('prenoms_vaccinateurs', $prenoms_vaccinateurs)
                        ->set('date_maj_vaccinateurs', date("Y-m-d H:i:s"))
                        ->where('id_vaccinateurs', $id_vaccinateurs)
                        ->update($this->table_vaccinateurs);
    }

    public function getMonCompte($id_vaccinateurs)
    {
         return $this->db->select('*')
                         ->from($this->table_vaccinateurs)
                         ->where('id_vaccinateurs', $id_vaccinateurs)
                         ->where('etat_vaccinateurs', "A")
                         ->get()
                         ->row();
    }

    public function existeVisiteurs($login)
    {
         return $this->db->select('*')
                         ->from($this->table_vaccinateurs)
                         ->where('mobile_vaccinateurs', $login)
                         ->or_where('email_vaccinateurs', $login)
                         ->get()
                         ->row();
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

        public function annulerCommandes($id_res)
    {  
       
       $req = $this->db->set('status_status_cmd', 'A')
                       ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                       ->where('status_cmd.resStatusCmdID', $id_res)
                       ->update($this->table_status_commande);

       if ($req) 
       {
           return $this->db->set('etatVaccinations', 'I')
                           ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                           ->where('reservationsFk', $id_res)
                           ->update($this->table_vaccinations);
       }
       else
       {
            return FALSE;
       }

    }

     public function reporterCommandes($id_res)
    {  
       
       $req = $this->db->set('status_status_cmd', 'R')
                       ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                       ->where('status_cmd.resStatusCmdID', $id_res)
                       ->update($this->table_status_commande);

       if ($req) 
       {
           return $this->db->set('etatVaccinations', 'I')
                           ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                           ->where('reservationsFk', $id_res)
                           ->update($this->table_vaccinations);
       }
       else
       {
            return FALSE;
       }

    }

    public function transfererCommandes($id_res)
    {  
       
       $req = $this->db->set('status_status_cmd', 'T')
                       ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                       ->where('status_cmd.resStatusCmdID', $id_res)
                       ->update($this->table_status_commande);

       if ($req) 
       {
           return $this->db->set('etatVaccinations', 'I')
                           ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                           ->where('reservationsFk', $id_res)
                           ->update($this->table_vaccinations);
       }
       else
       {
            return FALSE;
       }

    }

    public function enCoursDetraitementCommandes($id_res)
    {  
       
       $req = $this->db->set('status_status_cmd', 'L')
                       ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                       ->where('status_cmd.resStatusCmdID', $id_res)
                       ->update($this->table_status_commande);

       if ($req) 
       {
           return $this->db->set('etatVaccinations', 'I')
                           ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                           ->where('reservationsFk', $id_res)
                           ->update($this->table_vaccinations);
       }
       else
       {
            return FALSE;
       }

    }

    public function validerCommandes($id_res)
    {  
       
       $req = $this->db->set('status_status_cmd', 'S')
                       ->set('date_maj_status_cmd', date("Y-m-d H:i:s"))
                       ->where('status_cmd.resStatusCmdID', $id_res)
                       ->update($this->table_status_commande);

       if ($req) 
       {
           return $this->db->set('etatVaccinations', 'I')
                           ->set('dateMajVaccinations', date("Y-m-d H:i:s"))
                           ->where('reservationsFk', $id_res)
                           ->update($this->table_vaccinations);
       }
       else
       {
            return FALSE;
       }

    }
    }
    ?>