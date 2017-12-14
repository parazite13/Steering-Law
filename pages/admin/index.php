<!DOCTYPE html>
<html>

<head>
	<?php getHead(); ?>
	<title>Projet - Loi de Steering</title>
</head>

<body>
	
	<?php getHeader(); ?>
	
	<?php if(isAdmin()) : ?>

		<?php 

			if(isset($_POST['add-experience'])){

				// Cherche le dernier id
				$id = 0;
				$experiences = $db->getExperiences()->find(array(), array("summary" => true))->toArray();
				foreach($experiences as $experience){
					if($experience->id > $id){
						$id = $experience->id;
					}
				}

				// Rempli le tableau des primitives
				$primitives = array();
				foreach($_POST as $key => $value){
					if($key == "add-experience") continue;

					$id = intval(substr($key, strlen($key) - 1)) - 1;
					$input = explode("-", $key)[0];

					$primitives[$id][$input] = $value;

				}
				
				$experience = array(
					"id" => $id,
					"primitives" => $primitives,
					"current" => true
				);

				$db->getExperiences()->updateMany(array(), array('$set' => array("current" => false)));

				$db->getExperiences()->insertOne($experience);

			}

		?>

		<div class="container">
			
			<ul id="tabbed-menu" class="nav nav-tabs">
				<li class="nav-item">
					<a href="#" data-content="all-experiences" class="nav-link active">
						Expériences
					</a>
				</li>
				<li class="nav-item">
					<a href="#" data-content="add-experience" class="nav-link">
						Ajouter une expérience
					</a>
				</li>
			</ul>
			
			<div id="details-content">

				<section id="all-experiences">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Expérience</th>
								<th>Visualisation</th>
								<th>Expérience courante</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach($db->getExperiences()->find(array(), array('summary'=>true))->toArray() as $experience): ?>
								<tr>
									<td><?= $experience->id ?></td>
									<td>
										<!-- <canvas></canvas> -->
										<?= json_encode($experience->primitives) ?>
									</td>
									<td>
										<?php if($experience->current) : ?>										
											<input type="radio" name="current-experience" value="<?= $experience->id ?>" checked>
										<?php else: ?>
											<input type="radio" name="current-experience" value="<?= $experience->id ?>">
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							
						</tbody>
					</table>

				</section>

				<section id="add-experience" class="d-none">
					
					<form action="" method="post">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Primitives</th>
									<th>Courbure</th>
									<th>Angle</th>
									<th>Visualisation</th>
								</tr>
							</thead>
							<tbody>
								<tr id="primitive-1">
									<td>1</td>
									<td>
										<input class="form-control" type="number" name="courbure-1" min="0" max="0.01" step="0.001">
									</td>
									<td>
										<input class="form-control" type="number" name="angle-1" min="1" max="360" step="1">
									</td>
									<td>
										<canvas class="visualisation-1"></canvas>
									</td>
								</tr>
								<tr id="add-primitive" style="cursor: pointer">
									<td colspan="4" style="text-align: center">
										<i class="fa fa-plus" aria-hidden="true"></i> Ajouter une primitive
									</td>
								</tr>
							</tbody>
						</table>
						<div class="row">
							<button class="btn btn-primary" role="button" type="submit" name="add-experience" style="margin: auto">Ajouter l'expérience</button>
						</div>
					</form>
				</section>
			</div>
		</div>
		<canvas id="canvasAdmin" style="width: 100%; height:90vh; cursor: crosshair; background: #FFFFFF;">
			Je suis un Canvas et je me porte mal.
		</canvas>

	<?php else: ?>

		<div class="container" id="container">
			<div class="row mb-3">
				<div class="col text-center">
					<h1>Espace Administrateur</h1>
				</div>
			</div>
			<form action="" method="post">
				<div class="row">
					<div class="col"></div>
					<div class="col">
						<div class="form-group">
							<input class="form-control" type="password" name="mdp" autofocus/>
						</div>
					</div>
					<div class="col"></div>
				</div>
				<div class="row">
					<div class="col">
						<button class="btn btn-secondary d-block mx-auto" role="button" type="submit" value="Valider">Valider</button>
					</div>
				</div>
			</form>
		</div>


	<?php endif; ?>

	<?php getFooter(); ?>

</body>


<script type="text/javascript">

//canvas et contexte
var canvas = $('#canvasAdmin');
canvas[0].width = $("#canvasAdmin").width();
canvas[0].height = $("#canvasAdmin").height();
var ctx = canvas[0].getContext('2d');
//couleurs
var colorStart = '#00ff00';
var colorEnd = '#ff0000';
var colorWay = '#e8e8e8';
var colorBackground = '#ffffff';
var colorBackPixelsGood = '#00ff00';
var colorBackPixelsBad = '#ff0000';

$(document).ready(function(){

	// Menu onglet
	$('#tabbed-menu a').click(function(){
		$('#tabbed-menu a').removeClass('active');
		$(this).addClass('active');
		$('#details-content > section').addClass('d-none');
		$('#' + $(this).attr('data-content')).removeClass('d-none');
	});

	// Selection de l'experience courante
	$("#all-experiences input[type=radio]").click(function(){
		$.post("ajax/setCurrentExperience.php", {id: $(this).val()});
	});

	// Ajout de primitive
	$("#add-primitive").click(function(){
		var primitive = $("#add-experience table tr").length - 1;
		html = '\
			<tr id="primitive-'+primitive+'">\
				<td>'+primitive+'</td>\
				<td>\
					<input class="form-control" type="number" name="courbure-'+primitive+'" min="0" max="0.01" step="0.001">\
				</td>\
				<td>\
					<input class="form-control" type="number" name="angle-'+primitive+'" min="1" max="360" step="1">\
				</td>\
				<td>\
					<canvas id="visualisation-'+primitive+'"></canvas>\
				</td>\
			</tr>\
		';
		$(html).insertBefore("#add-primitive");
	});

	//Prévisualisation
	//primitives
	var inputs = $('tbody>tr input');
	$('tbody>tr input').change(function(){
		if(inputs[0].value != "" && inputs[1].value != ""){
			var radius = 1 / inputs[0].value;
			var angle =  inputs[1].value;
			var path = new Path();
			path.add(new Arc(radius, angle, "#e8e8e8"));
			ctx.clearRect(0, 0, canvas[0].width, canvas[0].height); 
			path.draw();
		}
	});
})

</script>
<script type="text/javascript" src="../js/Arc.js"></script>
<script type="text/javascript" src="../js/Path.js"></script>
</html>
