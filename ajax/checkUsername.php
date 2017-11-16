<?php require('../include/init.php'); 

if(isset($_GET['username'])){

	$result = $bd->getRowFromQuery('SELECT id, nom FROM all_experiences WHERE current=1');
	$id = $result['id'];
	$nom = $result['nom'];
	$table = $id . "_" . slug($nom);

	$nom = $_GET['username'];

	$query = 'SELECT * FROM `'.$table.'` WHERE `nom`=\'' . $nom . '\';';

	if(isEmpty($bd->getRowsFromQuery($query))) echo 'true';
	else echo 'false';
}

?>