<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailjet
{

    public function emailing($destinataire, $subject, $message)
    {   
        $messageid = NULL;
        $headers = array ('Content-Type: application/json');
        $paramsend = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "info.2imservices@gmail.com",
                        'Name' => "VACCIPHA CÔTE D'IVOIRE"
                    ],
                    'To' => [
                        [
                            'Email' => $destinataire,
                        ]
                    ],
                    'Subject' => $subject,
                    'TextPart' => $message,
                    'HTMLPart' => $message,
                  ]
              ]
           ];
                          
           $url = "https://api.mailjet.com/v3.1/send";
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_VERBOSE, 1);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           curl_setopt($ch, CURLOPT_USERPWD, "6037bd1bb23d64b7f9ab83c99f32f23c:3d9a70176f7472c3c5f315f47bcb4d4d");
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