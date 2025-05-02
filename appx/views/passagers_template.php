<?php

defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Passagers</title>
    <style type="text/css">

        #container {
            margin: 50px;
            
            
        }
    </style>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

<div id="container">
    <?php 
    
            echo    "<h1>{$info["gare"]}</h1>";
            echo    "<table class='table table-striped table-dark'>";
            echo    "<thead>
                    <tr>
                        <th scope='col'>#</th>
                        <th scope='col'>Nom</th>
                        <th scope='col'>Prenoms</th>
                        <th scope='col'>Téléphone</th>
                        <th scope='col'>Destination</th>
                        <th scope='col'>Date</th>
                    </tr>
                    </thead>";
            echo    "<tbody>";
            foreach($info["passagers"] as $key => $value){
                $key=$key+1;
                echo    "<tr>
                            <th scope='row'>$key</th>
                            <td>$value->c_fname</td>
                            <td>$value->c_lname</td>
                            <td>$value->c_phone</td>
                            <td>$value->content</td>
                            <td>$value->booking_datetime</td>
                        </tr>";        
            }
            
            echo    "</tbody>";
            echo    "</table>";

    ?>
    
</div>

</body>
</html>
