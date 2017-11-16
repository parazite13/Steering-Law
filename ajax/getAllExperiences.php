<?php require('../include/init.php'); 

$table = 'all_experiences';
$columns = $bd->getColumnsNamesFromTable($table);
$rows = $bd->getRowsFromTable($table);

if(!isEmpty($rows)){

	$html = '<form onsubmit="return selectExperience();" id="allExperiences" name="form" class="container">';
	$html .= '<table class="table table-responsive table-striped table-bordered" style="width: 1000px; margin: auto;"><thead><tr>';
	foreach($columns as $column){
		if($column != 'id'){
			if($column == 'nom') $html .= '<th>Nom</th><th>Participants</th>';
			elseif($column == 'current') $html .= '<th>Expérience<br>courante</th>';
			else $html .= '<th class="align-middle">' . ucfirst($column) . '</th>';
		}
	}
	$html .= '</tr></thead><tbody>';

	for($i = 0; $i < count($rows); $i++){
		$html .= '<tr id="experience-'. $rows[$i]['id'] .'">';
		$html .= '<th scope="row">'. $rows[$i]['nom'] .'</th><td>';
		$html .= $bd->getRowFromQuery("SELECT COUNT(`id`) FROM `" . $rows[$i]['id'] . "_" . slug($rows[$i]['nom']) . "`")[0] . '</td>';
		for($j = 2; $j < $count = $bd->getColumnsCountFromTable($table); $j++){
			if($j == $count - 1){
				$html .= '<td class="container"><div class="row">';
				if($rows[$i][$columns[$j]] == '1'){
					$html .= '<input class="col my-2" type="radio" onchange="selectExperience()" name="currentExp" value="'. $rows[$i]['id'] .'" checked>';
				}else{
					$html .= '<input class="col my-2" type="radio"onchange="selectExperience()" name="currentExp" value="'. $rows[$i]['id'] .'">';
				}
				$html .= '<button id="button-copy-'. $rows[$i][$columns[0]] .'" name="'. $rows[$i][$columns[1]] .'" onclick="copyExperience(this);" type="button" role="button" class="btn btn-info mr-1 mb-1 col">Copier</button>';
				$html .= '<button id="button-suppr-'. $rows[$i][$columns[0]] .'" name="'. $rows[$i][$columns[1]] .'" onclick="deleteExperience(this);" type="button" role="button" class="btn btn-danger mr-1 mb-1 col">Supprimer</button>';
				$html .= '</div></td>';
			}else $html .= '<td>'. $rows[$i][$columns[$j]] .'</td>';
		}
		$html .= '</tr>';
	}

	$html .= '</tbody></table></form>';

	echo $html;

}


?>