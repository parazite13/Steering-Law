<?php

$ajax = true;
require("../init.php");

header('Content-Type: application/json');

echo json_encode($db->getExperiences()->find(array("current" => true))->toArray());


?>