<?php

$ajax = true;
require("../init.php");

$db->getOrder()->drop();
$db->getOrder()->insertOne($_POST);

?>