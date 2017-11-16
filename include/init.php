<?php
// Initialise la configuration de base
require(dirname(__FILE__) . '/config.php');

// Initialise les variables de session
require(dirname(__FILE__) . '/session.php');

// Charge la classe permettant l'accès à la base de données
require(dirname(__FILE__) . '/Bd.php');
$bd = new Bd(DB_NAME);

// Charge les fonctions utiles
require(dirname(__FILE__) . '/functions.php');

// Execute le setup si nécessaire
require(dirname(__FILE__) . '/setup.php');

?>