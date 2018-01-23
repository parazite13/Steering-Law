<!DOCTYPE html>
<html>

<head>
	<?php getHead(); ?>
	<title>Projet - Loi de Steering</title>
</head>

<body>

	<?php getHeader(); ?>
	
	<audio id="buzzer" src="audio/buzzer.mp3" type="audio/mp3"></audio>
	<audio id="success" src="audio/success.mp3" type="audio/mp3"></audio>

	<div class="jumbotron text-center py-2" id="experience">
		<div class="container-fluid">
			<h1 class="jumbotron-heading">Loi de Steering</h1>
			<div id="headExperience">
				<p>
					Quand vous êtes prêt, passez sur la zone verte et suivez le chemin pour lancer l'expérience puis effectuez le chemin tracé le plus rapidement et le plus précisément possible.<br>
					Le chronomètre démarre dès que vous passez de la zone de départ au chemin.
				</p>
				<button class="btn btn-primary mb-2" id="trainExperience" role="button" onclick="isTraining = true; start()">S'entrainer</button>
				<button class="btn btn-primary mb-2" id="startExperience" role="button" onclick="isTraining = false; start()">Démarrer</button>
			</div>
			<input class="form-control mx-auto" type="text" id="chronotime" value="00 : 00 : 000" style="text-align: center; width: initial; visibility: hidden;"/>
			<canvas id="canvas" style="width: 100%; height:90vh; cursor: crosshair; background: #FFFFFF;">
				Je suis un Canvas et je me porte mal.
			</canvas>
			<input class="form-control mx-auto" type="text" id="coordMouse" value="" style="text-align: center; width: initial;"/>
		</div>
	</div>


	<?php getFooter(); ?>

</body>

<script type="text/javascript" src="js/Arc.js"></script>
<script type="text/javascript" src="js/Path.js"></script>
<script type="text/javascript" src="js/script.js"></script>

</html>
