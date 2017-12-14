<?php 

class MongoDb{

	private $bd;
	private $nomBd;

	/*
	[id] => 1
	[primitives] => MongoDB\Model\BSONArray Object
		(
			[storage:ArrayObject:private] => Array
				(
					[0] => MongoDB\Model\BSONDocument Object
						(
							[storage:ArrayObject:private] => Array
								(
									[courbure] => 0
									[angle] => 1
								)
						)
					[1] => MongoDB\Model\BSONDocument Object
						(
							[storage:ArrayObject:private] => Array
								(
									[courbure] => 0.5
									[angle] => 2
								)
						)
				)
		)
	[current] => 1
	 */
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
