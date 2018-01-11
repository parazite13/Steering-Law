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

					$primitiveId = intval(substr($key, strlen($key) - 1)) - 1;
					$input = explode("-", $key)[0];

					$primitives[$primitiveId][$input] = $value;

				}
				
				$experience = array(
					"id" => ++$id,
					"primitives" => $primitives,
					"length" => intval($_POST['path-length']),
					"width" => intval($_POST['path-width']),
					"current" => true
				);

				$db->getExperiences()->updateMany(array(), array('$set' => array("current" => false)));

				$db->getExperiences()->insertOne($experience);

				header("Location: " . getCurrentUrl());
				exit();
			}

		?>

		<div class="container">
			
			<ul id="tabbed-menu" class="nav nav-tabs">
				<li class="nav-item">
					<a href="#" data-content="all-experiences" class="nav-link active" onclick="$('#canvasAdmin').css('display', 'none');">
						Expériences
					</a>
				</li>
				<li class="nav-item">
					<a href="#" data-content="add-experience" class="nav-link" onclick="$('#canvasAdmin').css('display', 'block');">
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
								<th>Longueur</th>
								<th>Largeur</th>
								<th>Visualisation</th>
								<th>Expérience courante</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach($db->getExperiences()->find(array(), array('summary'=>true))->toArray() as $experience): ?>
								<tr>
									<td><?= $experience->id ?></td>
									<td><?= $experience->length ?></td>
									<td><?= $experience->width ?></td>
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
								</tr>
							</thead>
							<tbody id="primitives">
								<tr id="primitive-1">
									<td>1</td>
									<td>
										<input class="form-control" type="number" name="courbure-1" min="0" max="1" step="0.00001">
									</td>	
									<td>
										<input class="form-control" type="number" name="angle-1" min="1" max="360" step="1">
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
							<div class="col">
								<label for="path-widrh">Largeur du chemin : </label>
								<input class="form-control" type="number" id="path-width" name="path-width" min="10" max="200" step="1" value="80">
							</div>
							<div class="col">
								<label for="path-length">Longueur du chemin : </label>
								<input class="form-control" readonly type="number" id="path-length" name="path-length" value="0">
							</div>
						</div>
						<div class="row mt-2">
							<button class="btn btn-primary" role="button" type="submit" name="add-experience" style="margin: auto">Ajouter l'expérience</button>
						</div>
					</form>
				</section>
			</div>
		</div>
		<canvas id="canvasAdmin" style="width: 100%; height:90vh; cursor: crosshair; background: #FFFFFF; display: none;">
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
					<input class="form-control" type="number" name="courbure-'+primitive+'" min="0" max="1" step="0.00001">\
				</td>\
				<td>\
					<input class="form-control" type="number" name="angle-'+primitive+'" min="1" max="360" step="1">\
				</td>\
			</tr>\
		';
		$(html).insertBefore("#add-primitive");
		setVisualisation();
	});

	$('#path-width').change(refreshPath);

	//setVisualisation est appelée dès le début pour la 1ère prmitive affichée et à chaque fois qu'on en crée une
	setVisualisation();

});

function setVisualisation(){
	$('#primitives>tr:not(:last-child)').change(refreshPath);
}

function refreshPath(){
	var path = new Path();
	var primitives = $('#primitives>tr');
	var pathLength = 0;
	$.each(primitives, function(index){
		//on prend pas le dernier tr -> Bouton
		if(index < primitives.length - 1){
			var inputs = $(this).find('input');
			if(inputs[0].value != "" && inputs[1].value != ""){
				var radius = 1 / inputs[0].value;
				var angle =  Math.PI * inputs[1].value / 180;
				path.add(new Arc(radius, angle, colorWay));
				ctx.clearRect(0, 0, canvas[0].width, canvas[0].height); 

				pathLength += angle * radius;
			}
		}
	});

	path.setWidth(parseInt($("#path-width").val()));
	path.draw();

	// Ecrit la longueur du chemin
	$('#path-length').val(Math.round(pathLength));
}

</script>
<script type="text/javascript" src="../js/Arc.js"></script>
<script type="text/javascript" src="../js/Path.js"></script>
</html>
