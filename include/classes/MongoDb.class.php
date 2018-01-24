<?php 

namespace Steering;

class MongoDb{

	private $bd;
	private $nomBd;

	private $experiences;
	private $order;
	private $times;

	function __construct($nom){

		$this->nomBd = $nom;

		if(class_exists("\MongoDB\Driver\Manager")){
			$client = new \MongoDB\CLient();
		}else{
			throw new \Exception("Driver MongoDB not found ! Please check phpinfo to see if mongodb extension is enabled!");
		}

		$this->bd = $client->selectDatabase($this->nomBd);

		$this->experiences = $this->bd->selectCollection('experiences');
		$this->order = $this->bd->selectCollection('order');
		$this->times = $this->bd->selectCollection('times');
	}

	function getExperiences(){
		return $this->experiences;
	}

	function getOrder(){
		return $this->order;
	}

	function getTimes(){
		return $this->times;
	}
}

?>
