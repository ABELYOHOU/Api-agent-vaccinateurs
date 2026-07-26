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
                        'Email' => "",
                        'Name' => "API TEST"
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
                          
           $url = "";
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_VERBOSE, 1);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           curl_setopt($ch, CURLOPT_USERPWD, ":");
           curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paramsend));
           $response = json_decode(curl_exec($ch));


           $err = curl_error($ch);
           curl_close ($ch);

           var_dump($response);
           exit();

           


          return true;

           
    }

}