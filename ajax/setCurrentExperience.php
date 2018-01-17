<?php

$ajax = true;
require("../init.php");

$details = $db->getExperiences()->updateOne(array("id" => intval($_POST['id'])), array('$set' => array("current" => $_POST['value'] === "true" ? true : false)));
?>