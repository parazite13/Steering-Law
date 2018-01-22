<?php

$ajax = true;
require '../init.php';

header("content-type: application/x-javascript");

$datasets = array();

$data = array();

$dbTimes = $db->getTimes()->find(array(), array('summary' => true))->toArray();
foreach($dbTimes as $i => $dbTime){
	
	$dbPath = $db->getExperiences()->findOne(array("id" => $dbTime->id_path));

	if($dbPath->current !== true) continue;

	$indD = $dbPath->length / $dbPath->width;

	$averageTime = 0;
	foreach($dbTime->times as $j => $time){
		$averageTime += floatval($time);
	}
	$averageTime = $averageTime / ($j + 1);

	$data[] = array(
		'x' => $indD,
		'y' => $averageTime
	);

}

$datasets[] = array(
	'label' => "Moyenne des temps",
	'data' => $data,
	'borderColor' => 'red',
	'pointBackgroundColor' => 'red',
	'showLine' => false,
	'pointRadius' => 5,
	'fill' => false
);

$datasets[] = array(
	'label' => "Droite de Regression",
	'data' => array(),
	'borderColor' => 'green',
	'fill' => false
);

echo json_encode($datasets);


?>