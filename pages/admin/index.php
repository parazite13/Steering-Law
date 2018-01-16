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
					if(strpos($key, "courbure-") === false && strpos($key, "angle-") === false && strpos($key, "orientation-") === false) continue;

					$primitiveId = intval(substr($key, strlen($key) - 1)) - 1;
					$input = explode("-", $key)[0];

					// Cas particulier pour le premier gauche/droite
					if($key == "orientation-1"){
						$value = ($value == "left") ? "normal" : "invert";
					}

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

			$experiences = $db->getExperiences()->find(array(), array('summary'=>true))->toArray();

		?>

		<div class="container">
			
			<ul id="tabbed-menu" class="nav nav-tabs">
				<li class="nav-item">
					<a href="#" data-content="all-experiences" class="nav-link active" onclick="$('#canvasAdmin').css('display', 'none');">
						Chemins
					</a>
				</li>
				<li class="nav-item">
					<a href="#" data-content="add-experience" class="nav-link" onclick="$('#canvasAdmin').css('display', 'block');">
						Ajouter un chemin
					</a>
				</li>
			</ul>
			
			<div id="details-content">

				<section id="all-experiences">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Id</th>
								<th>Longueur</th>
								<th>Largeur</th>
								<th style="width: 200px;">Primitives<br><small>(courbure, angle, longueur)</small></th>
								<th>Visualisation</th>
								<th>Expérience courante</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach($experiences as $experience): ?>
								<tr>
									<td><?= $experience->id ?></td>
									<td><?= $experience->length ?></td>
									<td><?= $experience->width ?></td>
									<td>
										<?php foreach($experience->primitives as $primitive): ?>
											(<?= $primitive['courbure'] ?>, <?= $primitive['angle'] ?>, <?= round(1 / $primitive['courbure'] * $primitive['angle'] * pi() / 180) ?>), <br>
										<?php endforeach; ?>
									</td>
									<td>
										<canvas id="visualisation-<?=$experience->id?>" class="visualisation" style="width: 100%;height: auto;"></canvas>
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
									<th>Longueur</th>
									<th>Orientation</th>
								</tr>
							</thead>
							<tbody id="primitives">
								<tr id="primitive-1">
									<td>1</td>
									<td>
										<input class="form-control" type="number" name="courbure-1" min="0" max="1" step="0.000001">
									</td>	
									<td>
										<input class="form-control" type="number" name="angle-1" min="0" max="360" step="0.1">
									</td>
									<td>
										<input class="form-control" type="number" disabled>
									</td>
									<td>
										<select class="form-control" name="orientation-1">
											<option value="left">Gauche</option>
											<option value="right">Droite</option>
										</select>
									</td>
								</tr>
								<tr id="add-primitive" style="cursor: pointer">
									<td colspan="5" style="text-align: center">
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

<?php if(isAdmin()): ?>
	<script type="text/javascript">

	//canvas et contexte
	var canvas = $('#canvasAdmin');
	canvas[0].width = $("#canvasAdmin").width();
	canvas[0].height = $("#canvasAdmin").height();
	var ctx = canvas[0].getContext('2d');

	var canvasVisualisation = $('.visualisation');
	$.each(canvasVisualisation, function(index, visualisation){
		visualisation.width = canvas.width();
		visualisation.height = canvas.height();
	});

	//couleurs
	var colorStart = '#00ff00';
	var colorEnd = '#ff0000';
	var colorWay = '#e8e8e8';
	var colorBackground = '#ffffff';
	var colorBackPixelsGood = '#00ff00';
	var colorBackPixelsBad = '#ff0000';

	$(document).ready(function(){

		// Déplacement des lignes du tableau
		$("#all-experiences tbody").sortable({
		    items: ">",
		    appendTo: "parent",
		    beforeStop: function(event, ui){
		    	
		    }
		}).disableSelection();

		// Menu onglet
		$('#tabbed-menu a').click(function(){
			$('#tabbed-menu a').removeClass('active');
			$(this).addClass('active');
			$('#details-content > section').addClass('d-none');
			$('#' + $(this).attr('data-content')).removeClass('d-none');
		});

		// Selection de l'experience courante
		$("#all-experiences input[type=radio]").click(function(){
			$.post("<?=ABSURL?>ajax/setCurrentExperience.php", {id: $(this).val()});
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
					<td>\
						<input class="form-control" type="number" disabled>\
					</td>\
					<td>\
						<div class="form-check">\
							<label class="form-check-label">\
								<input class="form-check-input" type="checkbox" name="orientation-'+primitive+'" value="invert">\
								Inverser\
							</label>\
						</div>\
					</td>\
				</tr>\
			';
			$(html).insertBefore("#add-primitive");
			setVisualisation();
		});

		// Affiche les visualisations
		<?php echo 'var json = ' . json_encode($experiences) . ';'; ?>
		$.each(json, function(index, experience){
			var ctx = $('#visualisation-' + experience.id)[0].getContext('2d');
			var path = new Path(ctx);
			$.each(experience.primitives, function(i, primitive){
				var radius = 1 / primitive.courbure;
				var angle =  Math.PI * primitive.angle / 180;
				path.add(new Arc(radius, angle, colorWay), primitive.orientation);
			});
			path.setWidth(experience.width);
			path.draw();
		});

		// Change la visualisation lorsque la largeur change
		$('#path-width').change(refreshPath);

		//setVisualisation est appelée dès le début pour la 1ère prmitive affichée et à chaque fois qu'on en crée une
		setVisualisation();

	});

	function setVisualisation(){
		$('#primitives>tr:not(:last-child)').change(refreshPath);
	}

	function refreshPath(){
		var path = new Path(ctx);
		var primitives = $('#primitives>tr');
		var pathLength = 0;
		$.each(primitives, function(index){
			//on prend pas le dernier tr -> Bouton
			if(index < primitives.length - 1){
				var inputs = $(this).find('input');
				if(inputs[0].value != "" && inputs[1].value != ""){
					var radius = 1 / inputs[0].value;
					var angle = Math.PI * inputs[1].value / 180;
					var orientation;
					if(index == 0){
						orientation = $(this).find("select").val();
						orientation = (orientation == "right") ? orientation = "invert" : undefined
					}else{
						orientation = inputs[3].checked;
						orientation =  (orientation) ? "invert" : undefined;
					}
					console.log(orientation);
					path.add(new Arc(radius, angle, colorWay), orientation);
					ctx.clearRect(0, 0, canvas[0].width, canvas[0].height); 

					var primitiveLength = angle * radius;
					inputs[2].value = Math.round(primitiveLength);
					pathLength += primitiveLength;
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


<?php endif; ?>
</html>
