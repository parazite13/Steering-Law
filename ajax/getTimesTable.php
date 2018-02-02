<?php

$ajax = true;
require("../init.php");

$allPath = $db->getExperiences()->find(array("current" => true))->toArray();
$order = $db->getOrder()->findOne(array(), array('summary' => true))->order;

$allPathWithOrder = array();

// on parcoure l'ordre
foreach($order as $id){

	// on cherche le chemin avec l'id
	foreach($allPath as $path){
		if($path->id == $id){
			$allPathWithOrder[] = $path;
		}
	}
}
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Sujet</th>
				<?php 
				foreach($allPathWithOrder as $path):
					//remplit le tooltip
					$htmlTooltip = "";
					$htmlTooltip .= "Longueur : " . $path->length . "<br>";
					$htmlTooltip .= "Largeur : " . $path->width . "<br>";
					foreach ($path->primitives as $primitive){
						$htmlTooltip .= "(" . $primitive['courbure']  .", " . $primitive['angle'] . ", " . round(1 / $primitive['courbure'] * $primitive['angle'] * pi() / 180) . ", ".$primitive['orientation'].") <br>";
					}
				?>
				<th class="text-center" data-toggle="tooltip" data-placement="bottom" title="<?=$htmlTooltip ?>">Chemin #<?=$path->id ?></th>
				<?php 
				endforeach 
				?>
		</tr>
	</thead>
	<tbody>
		<?php 
		$still_time = true;
		$line = 0;
		while($still_time){
			$still_time = false;
		?>
			<tr>
				<th scope="row"><?=$line + 1?></th>
				<?php
				//pour chaque chemin
				foreach($allPathWithOrder as $path):
					if($db->getTimes()->findOne(array("id_path" => $path->id), array('summary' => true)) !== null){
						$current_times = $db->getTimes()->findOne(array("id_path" => $path->id), array('summary' => true))->times;
						$current_times_array = iterator_to_array($current_times);
					}else{
						$current_times_array = array();
					}
					if($line < count($current_times_array)){
						$still_time = true;?>	
						<td class="text-center"><?=$current_times_array[$line]?></td>
					<?php 	
					}else{
					?>
						<td></td>
					<?php 
					}
				endforeach;
				?>
			</tr>
		<?php  
		$line++;
		}
		?>
	</tbody>
</table>