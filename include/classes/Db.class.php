<?php 

class Db{

	private $bd;

	private $nomBd;
	private $host;
	private $user;
	private $mdp;

	function __construct($nom){

		$this->nomBd = $nom;
		$this->host = DB_HOST;
		$this->user = DB_USER;
		$this->mdp = DB_PASSWORD;

		try{
			$this->bd = new PDO('mysql:host='. $this->host .';dbname='. $this->nomBd . ';charset=utf8', $this->user, $this->mdp, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

		}catch (Exception $e){
			die("Impossible d'établir une connexion avec la BD veuillez vérifier le fichier de configuration<br>" . $e->getMessage());
		}
	}

	// Renvoie une unique entrée pour la requete $query
	function getRowFromQuery($query, $keep_numeric = true){
		return $this->getRowsFromQuery($query, $keep_numeric)[0];
	}

	// Renvoie l'ensemble des entrées qui correspondes à la $query
	function getRowsFromQuery($query, $keep_numeric = true){
		$rows = array();

		$res = $this->bd->query($query); 
        while($var = $res->fetch()){
        	$rows[] = $var;
        }

        if(!$keep_numeric){
        	foreach($rows as $id => $row){
        		foreach($row as $column => $value){
        			if(is_numeric($column)){
        				unset($rows[$id][$column]);
        			}
        		}
        	}
        }

        return $rows;
	}

	// Renvoie l'ensemble des entrées de la table $table
	function getRowsFromTable($table, $keep_numeric = true){
		
		$query = "SELECT * FROM `" . $table ."`";

		return $this->getRowsFromQuery($query, $keep_numeric);
	}

	// Renvoie un tableau contenant les noms de colonnes de la table $table
	function getColumnsNamesFromTable($table){

		$query = "SELECT * FROM `" . $table . "` LIMIT 0";

		$res = $this->bd->query($query);
		for($i = 0; $i < $res->columnCount(); $i++) {
    		$col = $res->getColumnMeta($i);
    		$columns[] = $col['name'];
		}

		return $columns;
	}

	// Renvoie le nombre de colonne de la table $table
	function getColumnsCountFromTable($table){
		return count($this->getColumnsNamesFromTable($table));
	}

	// Insert les données de $array dans la table $table
	function insertInto($table, $array){

		// Si le tableau est trop grand
		if(count($array) > $this->getColumnsCountFromTable($table)){
			die("Il y a trop d'éléments dans le tableau pour la table: ". $table);

		// S'il a exactement un element de moins
		}else if(count($array) + 1 == $this->getColumnsCountFromTable($table)){

			// Et que la table contient la colonne id
			if($this->getColumnsNamesFromTable($table)[0] == "id"){
				$this->insertInto($table, array('id' => NULL) + $array);
			}else{
				die("Il y a pas assez d'éléments dans le tableau pour la table: ". $table);
			}

		// S'il n'y a pas assez d'éléments
		}else if(count($array) + 1 < $this->getColumnsCountFromTable($table)){
			die("Il y a pas assez d'éléments dans le tableau pour la table: ". $table);
		
		// Le cas normal
		}else{

			// Vérification des clés du tableau
			$isNumeric = false;
			foreach($array as $key => $value){
				if(is_numeric($key)){
					$isNumeric = true;
					break;	
				}
			}

			// Si le tableau contient que des clefs numériques (pas associatif)
			// on récupere les colonnes dans l'ordre de la BD
			if($isNumeric){
				$fields = '`' . implode('`, `', $this->getColumnsNamesFromTable($table))  . '`';

			// Sinon on prend les clefs comme nom de colonnes
			}else{
				$fields = '`' . implode('`, `' , array_keys($array)) . '`';
			}

			$values =  ":val" . implode(", :val", array_keys($this->getColumnsNamesFromTable($table)));

			$query = "INSERT INTO `". $table ."` (". $fields .") VALUES (". $values .")";

			$statement = $this->bd->prepare($query);

			$i = 0;
			foreach($array as $value){
				//si le champs est un integer
				if(is_int($value))
					$type = PDO::PARAM_INT;
				//si le champs est un boolean
				elseif(is_bool($value))
					$type = PDO::PARAM_BOOL;
				//si le champs est un NULL
				elseif(is_null($value))
					$type = PDO::PARAM_NULL;
				//si le champs est un string
				elseif(is_string($value))
					$type = PDO::PARAM_STR;
				//sinon
				else
					$type = FALSE;

				$statement->bindValue(":val" . $i, $value, $type);

				$i++;
			}

			$statement->execute();
		}
	}

	function executeQuery($query){
		$this->bd->exec($query);
	}
}

?>
