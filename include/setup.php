<?php

$tables = $bd->getRowsFromQuery("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '". DB_NAME ."'");

// Vérifie si la table principale existe
$install = false;
foreach($tables as $table){
	if($table['TABLE_NAME'] == "all_experiences"){
		$install = true;
	}
}

// S'il manque la table des expériences on l'a crée
if(!$install){

	$query = "CREATE TABLE `all_experiences` ( `id` INT NOT NULL AUTO_INCREMENT , `nom` VARCHAR(50) NOT NULL, `distance` INT NOT NULL , `indice_diff` FLOAT NOT NULL , `coeff` FLOAT NOT NULL , `mouvement` INT NOT NULL , `current` BOOLEAN NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;";

	$bd->executeQuery($query);

	die("L'installation s'est déroulée avec succès !<br>Veuillez actualiser la page");
}

?>