<?php 

class MongoDb{

	private $bd;
	private $nomBd;

	private $experiences;

	function __construct($nom){

		$this->nomBd = $nom;

		$client = new MongoDB\CLient();
		$this->bd = $client->selectDatabase($this->nomBd);

		$this->experiences = $this->bd->selectCollection('experiences');
	}

	function getExperiences(){
		return $this->experiences;
	}

}

?>
