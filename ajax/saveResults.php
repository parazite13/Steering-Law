<?php require('../include/init.php'); 

// Vérification si le nom existe deja dans la Bd
$result = $bd->getRowFromQuery('SELECT id, nom FROM all_experiences WHERE current=1');
$id = $result['id'];
$nom = $result['nom'];
$table = $id . "_" . slug($nom);

$username = $_POST['nom'];
$query = 'SELECT * FROM `'.$table.'` WHERE `nom`=\'' . $username . '\';';

// Si existe deja
if(!isEmpty($bd->getRowsFromQuery($query))){

	// On supprime les ancienes données
	$query = "DELETE FROM `". $table ."` WHERE `nom`='". $username ."'";
	$bd->executeQuery($query);

}

// Requete d'insertion
$array = array();
foreach($_POST as $key => $value){
	$array[str_replace("_", ".", $key)] = $value;
}

$bd->insertInto($table, $array);


?>