
<?php require('../include/init.php'); 

$experience = $bd->getRowFromQuery('SELECT * FROM all_experiences WHERE current=1;');
foreach($experience as $key => $value){
	if(is_numeric($key)) unset($experience[$key]);
}
header('Content-Type: application/json');
echo json_encode($experience);
?>