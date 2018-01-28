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

				$db->getExperiences()->insertOne($experience);

				// modifie l'ordre
				if(($order = $db->getOrder()->findOne(array(), array('summary' => true))) !== null){
					$order->order[] = $id;
					$db->getOrder()->drop();
				}else{
					$order = array("order" => array($id));
				}
				$db->getOrder()->insertOne($order);

				header("Location: " . getCurrentUrl());
				exit();
			}

			$allPath = $db->getExperiences()->find(array(), array('summary' => true))->toArray();


			if(($order = $db->getOrder()->findOne(array(), array('summary' => true))) !== null){
				$allPathWithOrder = array();

				// on parcoure l'ordre
				foreach($order->order as $id){

					// on cherche le chemin avec l'id
					foreach($allPath as $path){
						if($path->id == $id){
							$allPathWithOrder[] = $path;
						}
					}
				}
				
			}else{
				$allPathWithOrder = $allPath;
			}


		?>

		<div class="container">
			
			<ul id="tabbed-menu" class="nav nav-tabs">
				<li class="nav-item">
					<a href="#" data-content="all-experiences" class="nav-link active" onclick="$('#canvasAdmin').css('display', 'none');">
						Visualiser les Chemins
					</a>
				</li>
				<li class="nav-item">
					<a href="#" data-content="add-experience" class="nav-link" onclick="$('#canvasAdmin').css('display', 'block');">
						Ajouter un chemin
					</a>
				</li>
				<li class="nav-item">
					<a href="#" data-content="times" class="nav-link" onclick="$('#canvasAdmin').css('display', 'none');">
						Consulter les temps
					</a>
				</li>
				<li class="nav-item">
					<a href="#" data-content="graphique" class="nav-link" onclick="$('#canvasAdmin').css('display', 'none'); createGraph();">
						Afficher le graphique
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
								<th>Chemins utilisé</th>
								<th>Copier le chemin</th>
							</tr>
						</thead>
						<tbody>

							<?php foreach($allPathWithOrder as $path): ?>
								<tr>
									<td><?= $path->id ?></td>
									<td><?= $path->length ?></td>
									<td><?= $path->width ?></td>
									<td>
										<?php foreach($path->primitives as $primitive): ?>
											(<?= $primitive['courbure'] ?>, <?= $primitive['angle'] ?>, <?= round(1 / $primitive['courbure'] * $primitive['angle'] * pi() / 180) ?>), <br>
										<?php endforeach; ?>
									</td>
									<td>
										<canvas id="visualisation-<?=$path->id?>" class="visualisation" style="width: 100%;height: auto;"></canvas>
									</td>
									<td>
										<?php if($path->current) : ?>										
											<input type="checkbox" name="current-<?=$path->id?>" chemin="<?=$path->id?>" checked>
										<?php else: ?>
											<input type="checkbox" name="current-<?=$path->id?>" chemin="<?=$path->id?>">
										<?php endif; ?>
									</td>
									<td><button type="button" role="button" class="btn btn-info" onclick="copyPath(<?=$path->id?>)">Copier</button></td>
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

				<section id="times" class="d-none">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Temps (ms)</th>
									<?php 
									foreach($allPathWithOrder as $path):
										//remplit le tooltip
										$htmlTooltip = "";
										$htmlTooltip .= "Longueur : " . $path->length . "<br>";
										$htmlTooltip .= "Largeur : " . $path->width . "<br>";
										foreach ($path->primitives as $primitive){
											$htmlTooltip .= "(" . $primitive['courbure']  .", " . $primitive['angle'] . ", " . round(1 / $primitive['courbure'] * $primitive['angle'] * pi() / 180) . ") <br>";
										}
								?>
									<th class="text-center" data-toggle="tooltip" data-placement="bottom" title="<?=$htmlTooltip ?>">Chemin <?=$path->id ?></th>
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
				</section>

				<section id="graphique" class="d-none">
					<canvas id="canvas-graphique"></canvas>
					<p id="regression-equation" class="text-center"></p>
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

	<?php echo 'var json = ' . json_encode($allPathWithOrder) . ';'; ?>

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

		//tooltip 
		$('[data-toggle="tooltip"]').tooltip({html: true}); 

		// Derniere ligne du tableau des temps affiche la moyenne
		var lastTr = $("#times tbody tr:last-child");
		lastTr.addClass("table-active");
		lastTr.find("th").html("Moyenne");
		var length = $("#times thead tr:first").children().length;
		for(var i = 2; i <= length; i++){

			var sum = 0;
			var trueValueCount = 0;
			var tds = $("#times tbody tr:not(:last-child) td:nth-child("+i+")");

			$.each(tds, function(j, td){
				var val = parseInt(td.innerHTML);
				if(!isNaN(val)){
					trueValueCount++;
					sum += val;
				}
			});
			
			sum /= trueValueCount;
			$("#times tbody tr:last-child td:nth-child("+i+")").addClass("text-center").html(Math.round(sum));
		}

		// Déplacement des lignes du tableau
		$("#all-experiences tbody").sortable({
		    items: ">",
		    appendTo: "parent",
		    beforeStop: function(event, ui){
		    	var order = [];

		    	$.each($("#all-experiences tbody tr:not(.ui-sortable-placeholder)"), function(index, value){
		    		order.push(parseInt($(value).find("td").first()[0].innerHTML));
		    	});

		    	$.post("<?=ABSURL?>ajax/setExperienceOrder.php", {order: order});
		    }
		});

		// Menu onglet
		$('#tabbed-menu a').click(function(){
			$('#tabbed-menu a').removeClass('active');
			$(this).addClass('active');
			$('#details-content > section').addClass('d-none');
			$('#' + $(this).attr('data-content')).removeClass('d-none');
		});

		// Selection de l'experience courante
		$("#all-experiences input[type=checkbox]").click(function(){
			$.post("<?=ABSURL?>ajax/setCurrentExperience.php", {value: this.checked, id: $(this).attr("chemin")});
		});

		// Ajout de primitive
		$("#add-primitive").click(addRowToPath);

		// Affiche les visualisations
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

		// Petit tricks pour envoyer les checkboxes non checked
		$("#add-experience form").submit(
			function() {

			    // For each unchecked checkbox on the form...
			    $(this).find($("input:checkbox:not(:checked)")).each(

			        // Create a hidden field with the same name as the checkbox and a value of 0
			        // You could just as easily use "off", "false", or whatever you want to get
			        // when the checkbox is empty.
			        function(index) {
			            var input = $('<input />');
			            input.attr('type', 'hidden');
			            input.attr('name', $(this).attr("name")); // Same name as the checkbox
			            input.attr('value', "normal");

			            // append it to the form the checkbox is in just as it's being submitted
			            var form = $(this)[0].form;
			            $(form).append(input);

			        }   // end function inside each()
			    );      // end each() argument list

			    return true;    // Don't abort the form submit

			}   // end function inside submit()
			);    

	});

	function copyPath(id){

		// Enleve les anciens tr en trop
		$("#add-experience tbody tr:not(:first):not(:last)").remove();

		// Affiche le canvas
		$('#canvasAdmin').css('display', 'block');

		// Change d'onglet
		$('#tabbed-menu a').removeClass('active');
		$('a[data-content="add-experience"]').addClass('active');
		$('#details-content > section').addClass('d-none');
		$('#' + $('a[data-content="add-experience"]').attr('data-content')).removeClass('d-none');

		$.each(json, function(i, experience){
			if(experience.id == id){
				
				$("input#path-width").val(experience.width);

				$.each(experience.primitives, function(j, primitive){
					var tr = $("#primitive-" + (j + 1));
					var inputs = tr.find("input, select, checkbox");
					
					inputs[0].value = primitive.courbure;
					inputs[1].value = primitive.angle;

					// cas particulier pour le premier
					if(j == 0){
						inputs[3].value = (primitive.orientation == "normal") ? "left" : "right";
					}else{
						inputs[3].checked = (primitive.orientation == "invert");
					}


					// s'il y a encore des primitives on ajoute des lignes
					if(j + 1 < experience.primitives.length){
						addRowToPath();
					}

				});
			}
		});

		refreshPath();
	}

	function addRowToPath(){
		var primitive = $("#add-experience table tr").length - 1;
		html = '\
			<tr id="primitive-'+primitive+'">\
				<td>'+primitive+'</td>\
				<td>\
					<input class="form-control" type="number" name="courbure-'+primitive+'" min="0" max="1" step="0.00001">\
				</td>\
				<td>\
					<input class="form-control" type="number" name="angle-'+primitive+'" min="0.1" max="360" step="0.1">\
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
	}

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

	function createGraph(){

		$.get('<?=ABSURL?>ajax/getGraphDataset.php', function(dataset){

			var chart = new Chart($("#canvas-graphique"), {
				type: 'line',
			    data: {
			      datasets: JSON.parse(dataset)
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
								labelString: 'Indice de diffultée (Longueur / (Largeur * ln(2)))'
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
			    plugins: [{
			    	beforeInit: function(chart, options) {
			            var data = chart.config.data;
						var indexRegression = 1;
						var values = [1000, 0];

						var dataRegression = [];
						for(var index in data.datasets[0].data){
							var x = data.datasets[0].data[index].x;
							var y = data.datasets[0].data[index].y;

							if(x < values[0]) values[0] = x;
							if(x > values[1]) values[1] = x;

							dataRegression.push([data.datasets[0].data[index].x, data.datasets[0].data[index].y]);
						}

			            var result = regression('linear', dataRegression);
			            $('#regression-equation').html(result.string + " <br> R² = " + Math.round(result.r2 * 1000) / 1000);

			            for(var index in values){

			            	var x = values[index];
			                var y = result.equation[0] * x + result.equation[1];

			                data.datasets[indexRegression].data.push({x: x, y: y});
			            }
			        }
			    }]
			});

		});

	}

	</script>
	<script type="text/javascript" src="../js/Arc.js"></script>
	<script type="text/javascript" src="../js/Path.js"></script>


<?php endif; ?>
</html>
