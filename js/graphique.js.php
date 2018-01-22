<?php

$ajax = true;
require '../init.php';

header("content-type: application/x-javascript");

$datasets = array();

$data = array();

$dbTimes = $db->getTimes()->find(array(), array('summary' => true))->toArray();
foreach($dbTimes as $i => $dbTime){
	
	$dbPath = $db->getExperiences()->findOne(array("id" => $dbTime->id_path));

	if($dbPath->current !== true) continue;

	$indD = $dbPath->length / $dbPath->width;

	$averageTime = 0;
	foreach($dbTime->times as $j => $time){
		$averageTime += floatval($time);
	}
	$averageTime = $averageTime / ($j + 1);

	$data[] = array(
		'x' => $indD,
		'y' => $averageTime
	);

}

$dataRegression = array();
$maxX = 0;
$minX = 1000;
foreach($data as $value){
	$dataRegression[] = array($value['x'], $value['y']);
	if($value['x'] > $maxX) $maxX = $value['x'];
	if($value['x'] < $minX) $minX = $value['x'];
}
$dataRegression = json_encode($dataRegression);


$datasets[] = array(
	'label' => "Moyenne des temps",
	'data' => $data,
	'borderColor' => 'red',
	'pointBackgroundColor' => 'red',
	'showLine' => false,
	'pointRadius' => 5,
	'fill' => false
);

$datasets[] = array(
	'label' => "Droite de Regression",
	'data' => array(),
	'borderColor' => 'green',
	'fill' => false
);

$datasets = json_encode($datasets);

$js = <<< EOD

Chart.pluginService.register({
	beforeInit: function(chart, options) {

            var data = chart.config.data;
            var result = regression('linear', $dataRegression);
            $('#regression-equation').html(result.string + " <br> R² = " + Math.round(result.r2 * 1000) / 1000);
			var indexRegression = 1;
			var values = [$minX, $maxX];

            for (var index in values) {

            	var x = values[index];
                var y = result.equation[0] * x + result.equation[1];

                data.datasets[indexRegression].data.push({x: x, y: y});
            }
        }
});

var chart = new Chart($("#canvas-graphique"), {
	type: 'line',
    data: {
      datasets: $datasets
    },
    options: {
        title: {
			display: true,
			text: "Temps en fonction de l'indice de difficulté"
    	},
    	scales: {
			xAxes: [{
				type: 'linear',
				position: 'bottom',
				scaleLabel: {
					display: true,
					labelString: 'Indice de diffultée (Longueur / Largeur)'
				}
			}],
			yAxes: [{
				position: 'left',
				scaleLabel: {
					display: true,
					labelString: 'Temps (ms)'
				}
			}]
		},
		steppedLine: 'false',

    },
});


EOD;

echo $js;

?>