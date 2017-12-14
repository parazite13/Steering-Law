<?php

$ajax = true;
require("../init.php");

$db->getExperiences()->updateMany(array(), array('$set' => array("current" => false)));
$details = $db->getExperiences()->updateOne(array("id" => intval($_POST['id'])), array('$set' => array("current" => true)));
?>