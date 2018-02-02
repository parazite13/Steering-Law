<?php

$ajax = true;
require("../init.php");

$id = intval($_POST['id']);

$db->getExperiences()->deleteOne(array("id" => $id));
$db->getTimes()->deleteOne(array("id_path" => $id));


$order = iterator_to_array($db->getOrder()->find(array(), array("summary" => true))->toArray()[0]->order);
unset($order[array_search($id, $order)]);
$order = array_values($order);
$db->getOrder()->drop();

$db->getOrder()->insertOne(array("order" => $order));

?>