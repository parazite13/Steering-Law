<?php require('../include/init.php'); 

	$nom = $_POST['nom'];
	$query = 'SELECT * FROM all_experiences WHERE 
			`nom`=\'' . $nom . '\';';

	if(isEmpty($bd->getRowsFromQuery($query))) echo 'true';
	else echo 'false';

?>