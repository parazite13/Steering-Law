<?php require('../include/init.php'); 

$result = $bd->getRowFromQuery('SELECT id, nom FROM all_experiences WHERE current=1');
$id = $result['id'];
$nom = $result['nom'];

$table = $id . "_" . slug($nom);

$query = 'DELETE FROM `' . $table . '` WHERE `id` = '. $_GET['id'] .';';
$bd->executeQuery($query);
?>