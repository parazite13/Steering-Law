<?php
// Chemin absolu vers le dossier racine
define('ABSPATH', dirname(__FILE__)  . "/");

// Initialise la configuration de base
require(ABSPATH . 'include/config.php');

// Charge les classes de composer
require(ABSPATH . 'vendor/autoload.php');

// Charge toutes les classes récursivement
require(ABSPATH . 'include/classLoader.php');

// Création de l'objet de connexion à la base de données
$db = new Steering\MongoDb(DB_NAME);

// ZONE DE RESET //
/*
$db->getTimes()->drop();
$db->getExperiences()->drop();
$db->getOrder()->drop();

$chemins = array(

	array(
		'id' => 1,
		'primitives' => array(
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'left'
			)
		),
		'length' => 1047,
		'width' => 80,
		'current' => true
	),

	array(
		'id' => 2,
		'primitives' => array(
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'left'
			)
		),
		'length' => 1047,
		'width' => 50,
		'current' => true
	),	

	array(
		'id' => 3,
		'primitives' => array(
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'left'
			)
		),
		'length' => 1047,
		'width' => 30,
		'current' => true
	),

	array(
		'id' => 4,
		'primitives' => array(
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00002,
				'angle' => 0.4,
				'orientation' => 'left'
			)
		),
		'length' => 1047,
		'width' => 20,
		'current' => true
	),

	array(
		'id' => 5,
		'primitives' => array(
			array(
				'courbure' => 0.000025,
				'angle' => 0.1,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00155,
				'angle' => 20,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00149,
				'angle' => 40,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00155,
				'angle' => 20,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00003,
				'angle' => 0.1,
				'orientation' => 'left'
			)
		),
		'length' => 1047,
		'width' => 80,
		'current' => true
	),

	array(
		'id' => 6,
		'primitives' => array(
			array(
				'courbure' => 0.000025,
				'angle' => 0.1,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00155,
				'angle' => 20,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00149,
				'angle' => 40,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00155,
				'angle' => 20,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00003,
				'angle' => 0.1,
				'orientation' => 'left'
			)
		),
		'length' => 1047,
		'width' => 50,
		'current' => true
	),

	array(
		'id' => 7,
		'primitives' => array(
			array(
				'courbure' => 0.000025,
				'angle' => 0.1,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00155,
				'angle' => 20,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00149,
				'angle' => 40,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00155,
				'angle' => 20,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00003,
				'angle' => 0.1,
				'orientation' => 'left'
			)
		),
		'length' => 1047,
		'width' => 30,
		'current' => true
	),

	array(
		'id' => 8,
		'primitives' => array(
			array(
				'courbure' => 0.000025,
				'angle' => 0.1,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00155,
				'angle' => 20,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00149,
				'angle' => 40,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.00155,
				'angle' => 20,
				'orientation' => 'right'
			),
			array(
				'courbure' => 0.00003,
				'angle' => 0.1,
				'orientation' => 'left'
			)
		),
		'length' => 1047,
		'width' => 20,
		'current' => true
	),

	array(
		'id' => 9,
		'primitives' => array(
			array(
				'courbure' => 0.003701,
				'angle' => 36.9,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.006,
				'angle' => 300,
				'orientation' => 'right'
			)
		),
		'length' => 1047,
		'width' => 80,
		'current' => true
	),

	array(
		'id' => 10,
		'primitives' => array(
			array(
				'courbure' => 0.003701,
				'angle' => 36.9,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.006,
				'angle' => 300,
				'orientation' => 'right'
			)
		),
		'length' => 1047,
		'width' => 50,
		'current' => true
	),

	array(
		'id' => 11,
		'primitives' => array(
			array(
				'courbure' => 0.003701,
				'angle' => 36.9,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.006,
				'angle' => 300,
				'orientation' => 'right'
			)
		),
		'length' => 1047,
		'width' => 30,
		'current' => true
	),

	array(
		'id' => 12,
		'primitives' => array(
			array(
				'courbure' => 0.003701,
				'angle' => 36.9,
				'orientation' => 'left'
			),
			array(
				'courbure' => 0.006,
				'angle' => 300,
				'orientation' => 'right'
			)
		),
		'length' => 1047,
		'width' => 20,
		'current' => true
	)

);

foreach($chemins as $chemin){
	$db->getExperiences()->insertOne($chemin);
}

$order = array(
	'order' => array(
		1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12
	)
);

$db->getOrder()->insertOne($order);

die();
*/
// FIN ZONE DE RESET //

// $db->getTimes()->drop();
// $db->getExperiences()->drop();
// $db->getOrder()->drop();
// $db->getExperiences()->deleteMany(array("id" => 2));
// echo "<pre>";
// print_r($db->getExperiences()->find(array("id" => 4), array('summary' => true))->toArray());
// die();


// Initialise les variables de session
require(ABSPATH . 'include/session.php');

// Charge les fonctions utiles
require(ABSPATH . 'include/functions.php');

if(!isset($ajax)){
	require(ABSPATH . 'include/dispatcher.php');
}
?>