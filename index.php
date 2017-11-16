<?php require('include/init.php'); ?>
<!DOCTYPE html>
<html>
<!-- En-tête de la page -->

<head>
	<?php getHead(); ?>
	<title>Projet - Loi de Fitts</title>
</head>
<!-- Corps de la page -->

<body>

	<?php getHeader(); ?>

	<div class="jumbotron text-center">
		<div class="container">
			<h1 class="jumbotron-heading">Loi de Fitts</h1>
			<p class="lead text-muted">
				En psychologie expérimentale, en ergonomie et en interaction homme-machine, la loi de Fitts est un modèle du mouvement humain. Cette loi exprime le temps requis pour aller rapidement d'une position de départ à une zone finale de destination, en fonction du rapport distance à la cible / largeur de la cible (D/L). La loi de Fitts est utilisée pour modéliser l'acte de « pointer », à la fois dans le vrai monde, par exemple avec une main ou un doigt, et sur les ordinateurs, par exemple avec une souris.
			</p>
			<h1 class="jumbotron-heading my-5">Avant d'accéder à l'expérience,<br>mettez la fenêtre du navigateur en plein écran !</h1>
			<a class = "btn btn-primary" href="<?=ABSURL?>experience.php"> Accéder à l'expérience</a>
			
		</div>
	</div>

	<?php getFooter(); ?>

</body>
<script type="text/javascript" src="<?=ABSURL?>js/layout.js"></script>
</html>