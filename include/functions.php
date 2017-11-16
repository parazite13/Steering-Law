<?php
// Renvoie l'URL demandé par l'utilisateur
function getCurrentUrl(){
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
}

function isEmpty($array){
  return count($array) == 0;
}

// Renvoie true si on est admin
function isAdmin(){
	return $_SESSION['type'] == 'Admin';
}

// Affiche le header
function getHeader(){
	require ABSPATH . "include/header.php";
}

// Afficher le footer
function getFooter(){
	require ABSPATH . "include/footer.php";
}

// Ajoute le contenu de la balise <head>
function getHead(){
	require ABSPATH . "include/head.php";
}

function slug($string, $replace = array(), $delimiter = '-') {
  
  if (!extension_loaded('iconv')) {
    throw new Exception('iconv module not loaded');
  }
  // Save the old locale and set the new locale to UTF-8
  $oldLocale = setlocale(LC_ALL, '0');
  setlocale(LC_ALL, 'en_US.UTF-8');
  $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
  if (!isEmpty($replace)) {
    $clean = str_replace((array) $replace, ' ', $clean);
  }
  $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
  $clean = strtolower($clean);
  $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
  $clean = trim($clean, $delimiter);
  // Revert back to the old locale
  setlocale(LC_ALL, $oldLocale);
  return $clean;
}
?>