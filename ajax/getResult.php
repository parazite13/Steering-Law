<?php require('../include/init.php'); 

$result = $bd->getRowFromQuery('SELECT id, nom FROM all_experiences WHERE current=1');
$id = $result['id'];
$nom = $result['nom'];

$table = $id . "_" . slug($nom);
$columns = $bd->getColumnsNamesFromTable($table);
$rows = $bd->getRowsFromTable($table);

if(!isEmpty($rows)){

	$html = '<table class="table table-responsive table-striped table-bordered"><thead><tr>';
	foreach($columns as $column){
		if($column == 'id') $html .= '<th style="padding: 0px;"><img src="'. ABSURL .'images/table_head.png" style="width: 80px; float: right;"></th>';
		else if($column != 'nom') $html .= '<th class="align-middle">' . ucfirst($column) . '</th>';
	}
	$html .= '</tr></thead><tbody>';

	for($i = 0; $i < count($rows); $i++){
		$html .= '<tr id="subject-'.$rows[$i]['id'].'"> <th scope="row" style="display:flex"><i class="fa fa-times mr-1" style = "color:red" onclick="removeSubject('.$rows[$i]['id'] . ')" role="button"></i>' . $rows[$i]['nom'] . '</th>';
		for($j = 2; $j < $bd->getColumnsCountFromTable($table); $j++){
			$html .= '<td>'. $rows[$i][$columns[$j]] .'</td>';
		}
		$html .= '</tr>';
	}
	$html .= '</tbody></table>';

	echo $html;

}

?>