<?php

$ajax = true;
require("../init.php");

header('Content-Type: application/json');

$allExperiences = $db->getExperiences()->find(array("current" => true))->toArray();
$order = $db->getOrder()->findOne(array(), array('summary' => true))->order;

$experiences = array();

// on parcoure l'ordre
foreach($order as $id){

	// on cherche le chemin avec l'id
	foreach($allExperiences as $experience){
		if($experience->id == $id){
			$experiences[] = $experience;
		}
	}
}

echo json_encode($experiences);


?>