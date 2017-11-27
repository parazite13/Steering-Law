<?php
$directory = ABSPATH . 'include/classes/';
if(is_dir($directory)){
	$dossier = opendir($directory);
	while($fichier = readdir($dossier)){
		if(is_file($directory.'/'.$fichier) && $fichier != '/' && $fichier != '.' && $fichier != '..' && strtolower(substr($fichier, -4)) == ".php"){
			require_once($directory.'/'.$fichier);
		}
	}
	closedir($dossier);
}
?>