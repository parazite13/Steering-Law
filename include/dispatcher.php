<?php
$url = getCurrentUrl(ABSURL);

$folder = ABSPATH . 'pages';
$pages = getAllFiles($folder);

foreach($pages as $key => $page){
	$pos = strrpos($page, "/");
	$pages[$key] = substr($page, 0, $pos);
}

$pageFound = false;
// Page d'accueil
if($url == '' || $url == 'index.php'){
	require($folder . "/index.php");
	$pageFound = true;

// Autre page
}else{
	foreach($pages as $page){
		if($url == $page || $url == $page . '/' || $url == $page . '/index.php'){

			// S'il n'y a pas d'extension
			if(strpos($page, '.') === false){
				require($folder . "/" . $page . '/index.php');
			}else{
				require($folder . "/" . $page);
			}
			$pageFound = true;
		}
	}
}

if(!$pageFound){
	require(ABSPATH . "include/elements/404.php");
}

?>