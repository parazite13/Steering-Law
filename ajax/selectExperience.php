<?php require('../include/init.php'); 

$bd->executeQuery('UPDATE all_experiences SET current=0');
$bd->executeQuery('UPDATE all_experiences SET current=1 WHERE id='. $_POST['currentExp']);

?>