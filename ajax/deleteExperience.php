<?php require('../include/init.php'); 

if(isset($_GET['name'], $_GET['id'])){

	$query = 'DELETE FROM `all_experiences` WHERE `id` = ' . $_GET['id'];
	$bd->executeQuery($query);

	$query = 'DROP TABLE `'. $_GET['id'] . '_'. slug($_GET['name']) .'`';
	$bd->executeQuery($query);

	$query = 'SELECT * FROM all_experiences WHERE `current`=1;';
	if(isEmpty($bd->getRowsFromQuery($query))){

		if(!isEmpty($rows = $bd->getRowsFromTable('all_experiences'))){
			$query = 'UPDATE all_experiences SET `current`=1 WHERE id=' . $rows[0]['id'];
			$bd->executeQuery($query);
		}
	}
}

?>