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
$db = new MongoDb(DB_NAME);

/*echo '<pre>';
print_r($db->getExperiences()->find(array(), array('summary' => true))->toArray());
die();*/
//$db->getExperiences()->deleteOne(array("current" => true));

// Initialise les variables de session
require(ABSPATH . 'include/session.php');

// Charge les fonctions utiles
require(ABSPATH . 'include/functions.php');

if(!isset($ajax)){
	require(ABSPATH . 'include/dispatcher.php');
}
?>