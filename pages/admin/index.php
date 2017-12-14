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
										<input class="form-control" type="number" name="courbure-1" min="0" max="1" step="0.01">
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
	
$(document).ready(function(){

	// Menu onglet
	$('#tabbed-menu a').click(function(){
		$('#tabbed-menu a').removeClass('active');
		$(this).addClass('active');
		$('#details-content > section').addClass('d-none');
		$('#' + $(this).attr('data-content')).removeClass('d-none');
	});

	// Ajout de primitive
	$("#add-primitive").click(function(){
		var primitive = $("#add-experience table tr").length - 1;
		html = '\
			<tr id="primitive-'+primitive+'">\
				<td>'+primitive+'</td>\
				<td>\
					<input class="form-control" type="number" name="courbure-'+primitive+'" min="0" max="1" step="0.01">\
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
	})

})

</script>

</html>
