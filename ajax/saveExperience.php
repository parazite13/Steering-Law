<?php require('../include/init.php'); 

// Desactive la précedente expérience
$bd->executeQuery('UPDATE all_experiences SET current=0');

// Insertion de la nouvelle expérience
$array = $_POST;
$array[] = true;
$bd->insertInto('all_experiences', $array);

// Recerche de l'ID correspondant
$id = $bd->getRowFromQuery('SELECT id FROM all_experiences ORDER BY id DESC LIMIT 1')['id'];

$nom = $_POST['nom'];
$distance = intval($_POST['distance']);
$indiceDiff = floatval($_POST['indice_diff']);
$coeff = floatval($_POST['coeff']);
$mouvement = intval($_POST['mouvement']);

$diam = $distance / (pow(2, $indiceDiff) - 1);

// Créations des tableaux contenant toutes les combinaisons pour l'expérience
$distances = array($distance);
for($i = 1; $i < 4; $i++){
	$distances[] = $distances[$i-1] * $coeff;
}

$diams = array($diam);
for($i = 1; $i < 4; $i++){
	$diams[] = $diams[$i-1] * $coeff;
}

$ids = array();
for($i = 0; $i < 4; $i++) {
	$ids[] = round(log(($distance / $diams[$i]) + 1, 2), 2);
}

// Construction de la requete de création de la table correspondante
$query = "CREATE TABLE `" . $id . "_" . slug($nom) . "` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`nom` VARCHAR(50) NOT NULL ,";

for ($i = 0; $i < 4; $i++) {
	for ($j = 0; $j < 4; $j++) {
		$query .= "`(" . round($distances[$i]) . "," . $ids[$j] . ")` INT NOT NULL , ";
	}
}
$query .= "PRIMARY KEY (`id`)) ENGINE = InnoDB;";

// Ajout de la table
$bd->executeQuery($query);

?>