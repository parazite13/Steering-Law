<?php
///////////////////////////
////    GET CONTENT    ////
///////////////////////////

// Affiche le header
function getHeader(){
	include ABSPATH . "include/elements/header.php";
}

// Afficher le footer
function getFooter(){
	include ABSPATH . "include/elements/footer.php";
}

// Ajoute le contenu de la balise <head>
function getHead(){
	include ABSPATH . "include/elements/head.php";
}


///////////////////////////
////   ADMIN RELATED   ////
///////////////////////////

// Renvoie true si on est admin
function isAdmin(){
	return $_SESSION['connect'];
}


///////////////////////////
////     DIRECTORY     ////
///////////////////////////

// Supprime dossier et fichier de manière récursive (i.e: rm -rf)
function rrmdir($dir){ 

    if(is_dir($dir)){ 
        $objects = scandir($dir); 
        foreach($objects as $object){ 
            if($object != "." && $object != ".."){ 
                if(filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); 
                else unlink($dir."/".$object); 
            } 
        } 
        reset($objects); 
        rmdir($dir); 
    } 
}

// Identique à scandir mais récursif
function scandirRec($dir) { 

    $result = array(); 

    $cdir = scandir($dir); 
    foreach ($cdir as $key => $value){ 
        if (!in_array($value,array(".",".."))){ 
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){ 
                $result[$value] = scandirRec($dir . DIRECTORY_SEPARATOR . $value); 
            }else{ 
                $result[] = $value; 
            } 
        } 
    } 
   
   return $result; 
}

// Renvoie tous les fichiers présents dans le dossier $dir et dans ses sous-dossiers
function getAllFiles($dir){

    $root = scandir($dir); 
    foreach($root as $value){ 
        if($value === '.' || $value === '..') continue; 
        if(is_file($dir . "/" . $value)){
            $result[] = $value;
            continue;
        } 
        foreach(getAllFiles($dir . "/" . $value) as $value2){ 
            $result[] = $value . "/" . $value2; 
        }
    } 
    return $result; 
} 

///////////////////////////
////  MISCELLANEOUS    ////
///////////////////////////

// Renvoie l'URL demandé par l'utilisateur
function getCurrentUrl($baseUrl = ''){

    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = substr($url, strlen($baseUrl));

    return $url;
} 

// Affiche une alert Bootstrap de type "danger", "success", "warning" avec le message $msg
function echoAlert($type, $msg){
    echo 	'<script type="text/javascript">
            	$(document).ready(function(){
                	var html = \'<div style="position:absolute;top:0px; left: 15px; right: 15px; width:calc(100% - 30px);" class="alert alert-'. $type .' mt-2" role="alert">\
                    	            '. $msg .'\
                        	    </div>\'
                  	$("body").append(html);
                  	setTimeout(function(){
                    	$(".alert").fadeOut(2000);
                  	}, 3000);
                })
           	</script>';
}
?>