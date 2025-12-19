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
                        'Email' => "vaccipha@enovpharm.com",
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
           curl_setopt($ch, CURLOPT_USERPWD, "2bf5237cd09a365ca7805674687411f0:0728dba8eb1634d03f608f3420881b7e");
           curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paramsend));
           $response = json_decode(curl_exec($ch));


           $err = curl_error($ch);
           curl_close ($ch);

           var_dump($response);
           exit();

           


          return true;

           
    }

}