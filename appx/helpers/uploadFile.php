<?php 
	
	defined('BASEPATH') OR exit('No direct script access allowed');

    function uploadfile($dir,$files_name=null) {
	    $target_dir = "uploads/".$dir."/";
        if (!is_dir($target_dir))
        {
            mkdir($target_dir, 0755, TRUE);
        }

        $nom_fichier = $target_dir.time().'_'.date("d_m_Y").'.jpg';
        
        if($files_name!=null && !empty($files_name)){
            $url_logo = base_url().$nom_fichier;
            file_put_contents($nom_fichier, base64_decode($files_name), FILE_APPEND);
            return $url_logo;
        }
        return null;
	}

?>