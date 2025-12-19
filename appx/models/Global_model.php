<?php
   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
    class Global_model extends CI_Model {

      protected $table_users = "users";
      protected $table_reinitialize = "reinitialize";
      protected $table_newsletters = "newsletters";
      protected $table_connexion = "connexion";

        public function __construct()
        {
            parent::__construct();
            date_default_timezone_set('UTC');
       }


       // Liste des 90 prochains jours à venir en format Dimanche 30 Septembre 2023
       public function jeConvertisDateEnFormatAnglais($jourVaccination)
        {   
            $conversionMois = array(
                'Janvier' => 'January',
                'Février' => 'February',
                'Mars' => 'March',
                'Avril' => 'April',
                'Mai' => 'May',
                'Juin' => 'June',
                'Juillet' => 'July',
                'Août' => 'August',
                'Septembre' => 'September',
                'Octobre' => 'October',
                'Novembre' => 'November',
                'Décembre' => 'December'
            );

            $premierMots = strtok($jourVaccination, ' ');
            //substr($dateString, strpos($dateString, ' ') + 1);
         $texteRestants = str_replace($premierMots.' ', '', $jourVaccination);
         $formattedDate = str_replace(array_keys($conversionMois), array_values($conversionMois), $texteRestants);

           // Créer une instance de DateTime pour analyser la date
            $dateObj = DateTime::createFromFormat('j F Y', $formattedDate);
            //$maDateFormatee = $dateObj->format('Y-m-d');
            $maDateFormatee = $dateObj->format('Y-m-d');

            return $maDateFormatee;
        }

       // Liste des 90 prochains jours à venir en format Dimanche 30 Septembre 2023
       public function jeChequeLesProchainsJours()
        {
                // Tableau des noms des jours de la semaine en français
                $nomsJoursSemaine = array(
                    'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'
                );

                $conversionMois = array(
                    'January' => 'Janvier',
                    'February' => 'Février',
                    'March' => 'Mars',
                    'April' => 'Avril',
                    'May' => 'Mai',
                    'June' => 'Juin',
                    'July' => 'Juillet',
                    'August' => 'Août',
                    'September' => 'Septembre',
                    'October' => 'Octobre',
                    'November' => 'Novembre',
                    'December' => 'Décembre'
                );

                // Date de départ (aujourd'hui)
                $date = new DateTime();

                // Tableau pour stocker les dates
                $dates = array();

                // Générer les 10 prochains jours
                for ($i = 0; $i < 30; $i++) {
                    // Obtenir le numéro du jour de la semaine (0 pour dimanche, 1 pour lundi, etc.)
                    $numeroJour = $date->format('w');
                    
                    // Obtenir le nom du jour de la semaine en français
                    $jourSemaine = $nomsJoursSemaine[$numeroJour];
                    
                    // Formater la date au format "vendredi 29 septembre 2023"
                    $formattedDate = $jourSemaine . ' ' . str_replace(array_keys($conversionMois), array_values($conversionMois), $date->format('j F Y'));
                    
                    // Ajouter la date au tableau
                    $dates[] = $formattedDate;

                    // Ajouter un jour à la date
                    $date->modify('+1 day');
                }
        

                return $dates;
        }


       public function jeBloqueLesFauxComptes($infosSaisies)
        {    
            $laisserComptes = TRUE;
            if ($infosSaisies) 
            {
                if (filter_var($infosSaisies, FILTER_VALIDATE_EMAIL) == true) 
                {
                     if (strstr($infosSaisies, '@') !== '@gmail.com' AND strstr($infosSaisies, '@') !== '@yahoo.com' AND strstr($infosSaisies, '@') !== '@yahoo.fr' AND strstr($infosSaisies, '@') !== '@live.fr' AND strstr($infosSaisies, '@') !== '@outlook.com' AND strstr($infosSaisies, '@') !== '@outlook.fr') 
                     {
                          return  FALSE;
                     }
                     else
                     {
                          return  TRUE;
                     }

                }
                else
                {   
                     if (strpos($infosSaisies, '0') === false AND strpos($infosSaisies, '1') === false AND strpos($infosSaisies, '2') === false AND strpos($infosSaisies, '3') === false AND strpos($infosSaisies, '4') === false AND strpos($infosSaisies, '5') === false AND strpos($infosSaisies, '6') === false AND strpos($infosSaisies, '7') === false AND strpos($infosSaisies, '8') === false AND strpos($infosSaisies, '9') === false AND strpos($infosSaisies, '@') === false AND strpos($infosSaisies, '&') === false AND strpos($infosSaisies, '#') === false AND strpos($infosSaisies, '"') === false AND strpos($infosSaisies, '*') === false AND strpos($infosSaisies, '/') === false AND strpos($infosSaisies, ';') === false AND strpos($infosSaisies, '~') === false) 
                     {
                          return  TRUE;
                     }
                     else
                     {
                          return  FALSE;
                     }
                }

            }
           else
         {
            return FALSE;
         }

        }

       public function generateUnik($length = 7)
        {
              $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
              $charactersLength = strlen($characters);
              $randomString = '';

              for ($i = 0; $i < $length; $i++)
              {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
              }

              //return strtoupper($randomString.time());
              return strtoupper($randomString);
        }

       //fonction pour vérifier la conformité d'un numéro de tel ivoirien
        public function getCodeMobile($mobile)
        {
            if (preg_match('/^\+[2-5]{3}([-. ]?[0-9]{2}){5}$/', $mobile))
            {
                return true; 
            } 
            else
            {
                return false;
            }
        }


        public function createConnectivite($usersConnexionId)
        {           
            return $this->db->set('usersConnexionId', $usersConnexionId)
                            ->set('etatConnexion', 'A')
                            ->set('dateCreateConnexion', date("Y-m-d H:i:s"))
                            ->insert($this->table_connexion);
        }


        public function createConnectiviteV($vaccinateurConnexionID)
        {           
            return $this->db->set('vaccinateurConnexionID', $vaccinateurConnexionID)
                            ->set('etatConnexion', 'A')
                            ->set('dateCreateConnexion', date("Y-m-d H:i:s"))
                            ->insert($this->table_connexion);
        }


         public function getConnectivites($usersConnexionId)
        { 
             return $this->db->select('*')
                             ->from($this->table_connexion)
                             ->where('usersConnexionId', $usersConnexionId)
                             ->where('etatConnexion', 'A')
                             ->get()
                             ->row();   
        }


          public function getConnectivitesV($vaccinateurConnexionID)
        { 
             return $this->db->select('*')
                             ->from($this->table_connexion)
                             ->where('vaccinateurConnexionID', $vaccinateurConnexionID)
                             ->where('etatConnexion', 'A')
                             ->get()
                             ->row();   
        }

        public function createReinitialize($usersID, $usersTypeID)
        {           
            return $this->db->set('usersTypeID', $usersTypeID)
                            ->set('usersID', $usersID)
                            ->set('etatReinitialize', 'A')
                            ->set('date_create_reinit', date("Y-m-d H:i:s"))
                            ->insert($this->table_reinitialize);
        }

        public function getUsersByReinitialize($usersID)
        { 
             return $this->db->select('count(idReinitialize) as nombre')
                             ->from($this->table_reinitialize)
                             ->where('usersID', $usersID)
                             ->where('usersTypeID', "P")
                             ->where('etatReinitialize', "A")
                             ->where('date_create_reinit >=', date('Y-m-d 00:00:00'))
                             ->where('date_create_reinit <=', date('Y-m-d 23:59:59'))
                             ->get()
                             ->row();   
        }

        public function getPatientsByReinitialize($usersID)
        { 
             return $this->db->select('count(idReinitialize) as nombre')
                             ->from($this->table_reinitialize)
                             ->where('usersID', $usersID)
                             ->where('usersTypeID', "P")
                             ->where('etatReinitialize', "A")
                             ->where('date_create_reinit >=', date('Y-m-d 00:00:00'))
                             ->where('date_create_reinit <=', date('Y-m-d 23:59:59'))
                             ->get()
                             ->row();   
        }

    

        public function postInformations($paramsend)
        {   
            $messageid = NULL;
            $headers = array(
                "content-type: application/json"
              );
                              
            $url = "https://api.mailjet.com/v3.1/send";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERPWD, "8f5cd707703db5a0c38fba60c77b5190:21b836e44c7ec5f7080f9a52f44967d0");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paramsend));
            $response = json_decode(curl_exec($ch));
            $err = curl_error($ch);
            curl_close ($ch);

            if ($response)
            {
               foreach ($response as $reponses)
               {  
                  foreach ($reponses as $valeurs)
                  { 
                      $valeurs = $valeurs->To;
                      foreach ($valeurs as $resp)
                      {
                          $messageid = $resp->MessageID;
                          $email = $resp->Email;
                      }
                  }
               }
            }


            return $messageid;
        }   
      
}
?>