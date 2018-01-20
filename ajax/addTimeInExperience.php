<?php

$ajax = true;
require("../init.php");

//récupère les temps du chemin qui a l'id donné
$times_current_path = array();
if($db->getTimes()->findOne(array("id_path" => intval($_POST['id_path'])), array('summary' => true)) !== null){
	$times_current_path = $db->getTimes()->findOne(array("id_path" => intval($_POST['id_path'])), array('summary' => true))->times;
	$times_current_path_array = iterator_to_array($times_current_path);
}else{
	$times_current_path_array = array();
}
array_push($times_current_path_array, $_POST['time']);


//update ou insert dans la table selon s'il y a déjà des temps ou non
if($db->getTimes()->find(array("id_path" => intval($_POST['id_path'])), array('summary' => true))->toArray() != null){
	$db->getTimes()->updateOne(array("id_path" => intval($_POST['id_path'])), array('$set' => array("times" => $times_current_path_array)));
}
else{
	$insert = array(
	"id_path" => intval($_POST['id_path']),
	"times" => $times_current_path_array
	);

	$db->getTimes()->insertOne($insert);
}

?>