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
	<?php 
	getHeader();
	?>

	<div class="jumbotron text-center py-2" id="experience">
		<div class="container-fluid">
			<div id="headExperience">
				<p>
					Quand vous êtes prêt, cliquez sur les deux cibles en alternant de gauche à droite, le plus vite et le plus précisément possible (essayez de minimiser les erreurs), en commençant par la cible bleue.<br>
					Le chronomètre démarre dès le premier clique…
				</p>
				<button class="btn btn-primary mb-2" id="startExperience" role="button" onclick="popUpStart()">Démarrer l'expérience</button>
			</div>

			<div id="topButtons" style="visibility: hidden;">
				<button id="replayButton" class="btn btn-primary mb-1" onclick="init('<?=ABSURL?>')" role="button" disabled="true">Recommencer</button>
				<button class="btn btn-primary mb-1" onclick="username = undefined; popUpStart();" role="button">Nouveau Sujet</button>
			</div>
			<input class="form-control mx-auto" type="text" id="chronotime" value="" style="text-align: center; width: initial; visibility: hidden;"/>
			<canvas id="canvas" style="width: 100%; cursor: crosshair; background: #CED2D2;">
				Je suis un Canvas et je me porte mal.
			</canvas>
			<div class="progress">
				<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
			</div>
			<div class="mt-1" id="bottomButtons" style="visibility: hidden;">
				<button id="buttonDl" onclick="download_csv();" class="btn btn-primary" role="button" role="button">Télécharger</button>
				<button id="buttonGraph" class="btn btn-primary" role="button" onclick="getGraph();">Graphique</button>
			</div>
		</div>
	</div>

	<?php getFooter(); ?>

</body>
<script type="text/javascript">
var isPopUpVisible;
var height = $("#canvas").width();
$("#canvas").css("height", height/3 + "px");
document.onselectstart = new Function ("return false");

function popUpStart(){

	isPopUpVisible = true;

	$('#startExperience').fadeOut(500, function(){
		$(this).remove();
	});

	pauseChrono();

	var inputUsername = 
		'<div id="inputUsername" class="card p-3" style="position: absolute; top: ' + ($("#canvas").offset().top + 100) + 'px; left: 30%; right: 30%; width: 40%;">\
			<label for="username" class="lead">Veuillez entrer votre nom :</label>\
			<input class="form-control mx-auto" type="text" id="username" style="text-align: center; width: 100%;"/>\
			<div id="feedback-username" class="d-none form-control-feedback"></div>\
			<button role="button" onclick="checkUsername();" class="mt-2 btn btn-primary">Démarrer</button>\
		</div>';
	$(inputUsername).hide().appendTo('#experience').fadeIn(1000);
}

function getGraph(){

	var url = '<?php echo ABSURL . "ajax/getGraph.php"; ?>';

	$('#buttonGraph').popover('dispose');
	$("#status").addClass("fa-spin");

	$.get(url, function(data){
		var refresh = $("<i />").attr('id', 'status').attr('class', 'fa fa-refresh mt-1').attr('onclick', 'getGraph();').attr('role', 'button').css('font-size', '30px');
		var img = $("<img />").attr('src', data).css({ "width": "80%", "display" : "block", "margin" : "auto"});
		$("#status").removeClass("fa-spin");
		$('#buttonGraph').popover({
		    	container: 'body',
		    	content: refresh.add(img),
		    	html: true,
		    	placement: 'top',
		    	trigger: 'manual'
		  	});
		setTimeout(function(){
			$('#buttonGraph').popover('show');
		}, 200);
	});
}

function checkUsername(){

	var url = '<?php echo ABSURL . "ajax/checkUsername.php"; ?>';
	var value = $('#username').val();

	if(value != ""){
		$.get(url, {username: value}, function(data){

			// Le nom n'existe pas encore 
			if(data == 'true'){
				//autorise à jouer et lance le jeu
				isPopUpVisible = false;
				init('<?=ABSURL?>');
			}else{
				var error = "Ce nom a déjà été utilisé pour l'expérience";
				$('#inputUsername').addClass('has-danger')
				$('#username').addClass('form-control-danger');
				$('#feedback-username').addClass('form-control-feedback').removeClass('d-none');
				$('#feedback-username').html(error);
			}
		});

	}else{
		var error = "Ce champ est obligatoire !";
		$('#inputUsername').addClass('has-danger')
		$('#username').addClass('form-control-danger');
		$('#feedback-username').addClass('form-control-feedback').removeClass('d-none');
		$('#feedback-username').html(error);
	}
}

</script>

<script type="text/javascript" src="<?=ABSURL?>js/fitts.js"></script>
<script type="text/javascript" src="<?=ABSURL?>js/layout.js"></script>

</html>