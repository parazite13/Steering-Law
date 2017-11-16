<?php 
require('../include/init.php');
require('../include/CrbTendance.php');
require('../include/JpGraph/jpgraph.php');
require('../include/JpGraph/jpgraph_scatter.php');
require('../include/JpGraph/jpgraph_line.php');
require('../include/JpGraph/jpgraph_error.php');

// Recherche de l'experience courante et de sa table
$result = $bd->getRowFromQuery('SELECT id, nom FROM all_experiences WHERE current=1');
$id = $result['id'];
$nom = $result['nom'];

$table = $id . "_" . slug($nom);
$columns = $bd->getColumnsNamesFromTable($table);
unset($columns[0]);
unset($columns[1]);

$participant = count($bd->getRowsFromQuery('SELECT id FROM `' . $table . '`;', false));

// Requete des moyennes pour chaque colonnes
$query = "SELECT AVG(`" . implode("`), AVG(`" , $columns) . "`) FROM `" . $table . "`";
$moyennes = $bd->getRowFromQuery($query, false);

// Réupération des valeurs dans $dataX, $dataY, $dataRegX et $dataRegY
$timesById = array();
foreach($moyennes as $column => $moyenne){

	// Temps
	$dataY[] = $moyenne;

	// Indice difficulté
	$distance = intval(substr($column, strlen("AVG(`("), strpos($column, ",")));
	$id = floatval(substr($column, strlen("AVG(`(" . $distance . ","), strpos($column, ")")));
	$dataX[] = $id;

	if(!isset($timesById[strval($id)])){
		$timesById[strval($id)] = array();
	}
	$timesById[strval($id)][] = $moyenne;

}

// Calcul des moyennes pour chaque ID
foreach($timesById as $id => $times){
	
	$moyenne = 0;
	foreach($times as $time){
		$moyenne += $time;
	}
	$moyenne /= 4;

	$dataRegX[] = $id;
	$dataRegY[] = $moyenne;
}


// Tri des valeurs
array_multisort($dataX, $dataY);
array_multisort($dataRegX, $dataRegY);

// Calcul de la courbe de tendance
$regressionLineaire = new RegLin($dataRegY, $dataRegX);
$a = number_format($regressionLineaire->OptMV(0)['A'], 0, ".", " ");
$b = number_format($regressionLineaire->OptMV(0)['B']);
$courbeTendance = $regressionLineaire->GetOpt($dataX);

$width = 400;
$height = 300;

// Initialisation
$graph = new Graph($width, $height);
$graph->img->SetMargin(40, 40, 40, 40);
$graph->SetScale("linlin");
$graph->SetShadow();
$graph->SetMargin(50, 50, 50, 50);
$graph->xaxis->title->Set("Log(D/W + 1)");
$graph->yaxis->title->Set("Temps (ms)");
$graph->yaxis->SetTitlemargin(35);
$graph->legend->SetAbsPos($width / 2, 5, "center", "top");

// Nuage de point
$p1 = new ScatterPlot($dataY, $dataX);
$p1->mark->SetType(MARK_FILLEDCIRCLE);
$p1->mark->SetFillColor("blue");
$p1->mark->SetWidth(2);
$p1->SetCenter();
$graph->Add($p1);

// 4 Points des moyennes
$p2 = new ScatterPlot($dataRegY, $dataRegX);
$p2->mark->SetType(MARK_FILLEDCIRCLE);
$p2->mark->SetFillColor("firebrick");
$p2->mark->SetWidth(3);
$p2->SetCenter();
$graph->Add($p2);

// Droite tendance
$p3 = new LinePlot(array_values($courbeTendance), array_keys($courbeTendance));
$p3->SetColor("red");
$p3->SetCenter();
$p3->SetLegend($b > 0 ? "y= " . $a . " x + " . $b : "y= " . $a . " x  - " . abs($b));
$graph->Add($p3);

$txt = new Text('Coeff R²: ' . number_format(pow($regressionLineaire->OptMV(0)['Cor'], 2), 3));
$txt->SetColor('black');
$txt->setPos($width - 90, 10);
$graph->Add($txt);

// Nombre de participant
$txt = new Text($participant . ' participant(s)');
$txt->SetColor('black');
$txt->setPos(5, 10);
$graph->Add($txt);

// Affiche le graphique
//$graph->Stroke();
$contentType = 'image/png';
$gdImgHandler = $graph->Stroke(_IMG_HANDLER);

ob_start();                        // start buffering
$graph->img->Stream();             // print data to buffer
$image_data = ob_get_contents();   // retrieve buffer contents
ob_end_clean();                    // stop buffer

echo "data:$contentType;base64," . base64_encode($image_data);

?>