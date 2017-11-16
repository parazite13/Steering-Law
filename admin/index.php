<?php require('../include/init.php');
if(isAdmin()){
?>
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
	<div class="container">
		<div class="row mb-3">
			<div class="col text-center">
				<h1>Espace Administrateur</h1>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div id="accordion" role="tablist" aria-multiselectable="true">
					<div class="card">
						<div class="card-header" role="tab" id="heading-1">
							<h5 class="col-9 mb-0 float-left">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="false" aria-controls="collapse-1">
									Ajouter une expérience
								</a>
							</h5>
						</div>
						<div id="collapse-1" class="collapse" role="tabpanel" aria-labelledby="heading-1">
							<div class="card-block row">
								<div id="currentExperience" class="col-6 text-center">
									<form onsubmit="return saveExperience();" id="saveExperience" name="form" class="container">
										<div class="row">
											<div class="col">
												<div class="form-group row">
													<label for="nom" class="col my-0 py-2 text-right col-form-label">Nom : </label>
													<input name="nom" type="text" class="col form-control" id="nom" onchange="$('#buttonGoExperience').fadeOut();">
												</div>
												<div class="form-group row">
													<label for="distance" class="col my-0 py-2 text-right col-form-label">Distance min : </label>
													<input name="distance" type="number" class="col form-control" id="distance" placeholder="en pixels" min="0" onchange="refreshTableCouples()">
												</div>
												<div class="form-group row">
													<label for="indice_diff" class="col my-0 py-2 text-right col-form-label">Indice de difficulté max : </label>
													<input name="indice_diff" type="number"   step="any" class="col form-control" id="indice_diff" min="0" onchange="refreshTableCouples()">
												</div>
												<div class="form-group row">
													<label for="coeff" class="col my-0 py-2 text-right col-form-label">Coefficient : </label>
													<input name="coeff" type="number" step="any" class="col form-control" id="coefficient" min="1" onchange="refreshTableCouples()">
												</div>
												<div class="form-group row">
													<label for="mouvement" class="col my-0 py-2 text-right col-form-label">Nombre de mouvements : </label>
													<input name="mouvement" type="number" class="col form-control" id="mouvement" min="1" onchange="$('#buttonGoExperience').fadeOut();">
												</div>
											</div>
											<div class="col-1"></div>
										</div>
										<div class="row">
											<div class="col-4"></div>
											<div class="col-4 text-center">
												<button type="submit" id="saveBtn" role="button" class="btn btn-success">
													<i id="status-1" class="fa mr-1"></i>
													Enregistrer
												</button>
											</div>
											<div class="col-4 text-center" id="buttonGoExperience" style="display: none;">
												<a href="<?=ABSURL?>experience.php">
													<button type="button" class="btn btn-primary" role="button">
														Aller à l'expérience
													</button>
												</a>
											</div>
										</div>
									</form>
								</div>
								<div class="col-6">
										<table class="table table-bordered" id="couples">
											<thead>
												<tr>
 														<th style="padding: 0;"><img src="<?=ABSURL?>images/table_head2.png" style="float: right;"></th>
  													<th></th>
  													<th></th>
  													<th></th>
  													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr>
  													<th scope="row"></th>
  													<td></td>
  													<td></td>
  													<td></td>
  													<td></td>
												</tr>
												<tr>
  													<th scope="row"></th>
  													<td></td>
  													<td></td>
  													<td></td>
  													<td></td>
												</tr>
												<tr>
	  												<th scope="row"></th>
	 												<td></td>
													<td></td>	
													<td></td>
													<td></td>
												</tr>
												<tr>
	  												<th scope="row"></th>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
												</tr>
											</tbody>
										</table>
										<div class="row">
											<div class="mx-auto">
												<p class="mb-1">Les valeurs du tableau doivent vérifier :</p>
												<ul>
													<li>Distance + Largeur ≤ 1400</li>
													<li>Distance ≥ Largeur</li>
													<li>Largeur ≤ 490</li>
													<li>Largeur ≥ 10</li>
												</ul>
											</div>
										</div>
								</div>
							</div>
						</div>
						<div class="card-header" role="tab" id="heading-2">
							<h5 class="col-9 mb-0 float-left">
								<a onclick="getResult();" data-toggle="collapse" data-parent="#accordion" href="#collapse-2" aria-expanded="false" aria-controls="collapse-2">
									Résultats de l'expérience courante
								</a>
							</h5>
						</div>
						<div id="collapse-2" class="collapse" role="tabpanel" aria-labelledby="heading-2">
							<div class="card-block row">
								<div id="result" class="col text-center">
									<i id="status-2" class="fa fa-spin fa-spinner mr-1"></i>
								</div>
							</div>
						</div>
						<div class="card-header" role="tab" id="heading-3">
							<h5 class="col-9 mb-0 float-left">
								<a onclick="getGraph();" data-toggle="collapse" data-parent="#accordion" href="#collapse-3" aria-expanded="false" aria-controls="collapse-3">
									Graphique de l'expérience courante
								</a>
							</h5>
						</div>
						<div id="collapse-3" class="collapse" role="tabpanel" aria-labelledby="heading-3">
							<div class="row">
								<div class="col text-center">
									<i id="status-3-1" onclick="getGraph();" role="button" class="fa fa-refresh mt-1" style="display: none; font-size: 30px;"></i>
								</div>
							</div>
							<div class="card-block row">
								<div id="graph" class="col text-center">
									<i id="status-3-2" class="fa fa-spin fa-spinner mr-1"></i>
								</div>
							</div>
						</div>
						<div class="card-header" role="tab" id="heading-4">
							<h5 class="col-9 mb-0 float-left">
								<a onclick="getAllExperiences();" data-toggle="collapse" data-parent="#accordion" href="#collapse-4" aria-expanded="false" aria-controls="collapse-4">
									Toutes les expériences
								</a>
							</h5>
						</div>
						<div id="collapse-4" class="collapse" role="tabpanel" aria-labelledby="heading-4">
							<div class="card-block row">
								<div id="experiences" class="col text-center">
									<i id="status-4" class="fa fa-spin fa-spinner mr-1"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php getFooter(); ?>
</body>
<script type="text/javascript">

	function saveExperience(){
		
		var url = '<?php echo ABSURL . "ajax/saveExperience.php"; ?>';
		var urlBis = '<?php echo ABSURL . "ajax/checkExperience.php"; ?>';
		var formulaire = document.forms['form'];
		var nom = formulaire.nom.value;
		var distanceMin = formulaire.distance.value;
		var indice_diff = formulaire.indice_diff.value;
		var coeff = formulaire.coeff.value;
		var mouvement = formulaire.mouvement.value;

		if(distanceMin > 0 && indice_diff > 0 && coeff > 1 && mouvement > 0 && nom.length > 0 && paramOk){
			$("#status-1").addClass("fa-spin fa-spinner");
			//On vérifie si l'expérience est déjà créée
			$.post(urlBis, $("#saveExperience").serialize(),
				function(data){
					//expérience est nouvelle
					if(data == 'true'){
						
						//ajout de l'expérience
						$.post(url, $("#saveExperience").serialize(),
							function(dataBis){
								$("#status-1").removeClass("fa-spin fa-spinner");
								$("#status-1").addClass("fa-check");
								$("#buttonGoExperience").fadeIn();
								setTimeout(function(){
									$("#status-1").removeClass("fa-check");
								}, 2000);
							}
						);
					}
					//expérience existe déjà
					else{
						$("#status-1").removeClass("fa-spin fa-spinner");
						$("#status-1").addClass("fa-times");
						$("#saveBtn").removeClass("btn-primary").addClass("btn-danger");
						var html = '<div class="alert alert-danger col mt-2 text-center" role="alert">\
										<strong> Erreur : L\'expérience existe déjà </strong>\
									</div>';
						$("#saveExperience").append(html);

						setTimeout(function(){
							$("#status-1").removeClass("fa-times");
							$("#saveBtn").removeClass("btn-danger").addClass("btn-primary");

							$(".alert").fadeOut(2000);
						}, 2000);
					}
				}
			);
		}
		else{
			$("#status-1").addClass("fa-times");
			$("#saveBtn").removeClass("btn-primary").addClass("btn-danger");
			var html = '<div class="alert alert-danger col mt-2 text-center" role="alert">\
							<strong> Erreur : Paramètres invalides </strong>\
						</div>';
			$("#saveExperience").append(html);

			setTimeout(function(){
				$("#status-1").removeClass("fa-times");
				$("#saveBtn").removeClass("btn-danger").addClass("btn-primary");
				$(".alert").fadeOut(2000);
			}, 2000);
		}
		
		return false; // Nécessaire pour empecher l'envoi du formulaire
	}

	function getResult(){

		var url = '<?php echo ABSURL . "ajax/getResult.php"; ?>';

		$.get(url, function(data){
			if(data != "")
				$("#result").html(data);
		});
	}

	function getAllExperiences(){

		var url = '<?php echo ABSURL . "ajax/getAllExperiences.php"; ?>';

		$.get(url, function(data){
			$("#experiences").html(data);
		});
	}

	function selectExperience(){

		var url = '<?php echo ABSURL . "ajax/selectExperience.php"; ?>';

		$("#status-4").addClass("fa-spin fa-spinner");
		$.post(url, $("#allExperiences").serialize(), function(data){
			$("#status-4").removeClass("fa-spin fa-spinner");
			$("#status-4").addClass("fa-check");
			setTimeout(function(){
				$("#status-4").removeClass("fa-check");
			}, 2000);
		});

		return false;
	}

	function getGraph(){

		$("#status-3-1").addClass("fa-spin");

		var url = '<?php echo ABSURL . "ajax/getGraph.php?"; ?>';

		$.get(url, function(data){
			var img = $("<img />").attr('src', data).css({ "width": "80%", "display" : "block", "margin" : "auto"});
			$("#graph").html(img);
			$("#status-3-1").removeClass("fa-spin");
			$("#collapse-3 i").css("display", "block");
		});
	}

	function copyExperience(element){

		var experience = element.name;
		var number = element.id.split('-')[2];

		var th = $('#experience-' + number + ' th')
		var td = $('#experience-' + number + ' td');

		var formulaire = document.forms['form'];
		formulaire.nom.value = th[0].innerHTML + ' (copie)';
		formulaire.distance.value = td[1].innerHTML;
		formulaire.indice_diff.value = td[2].innerHTML;
		formulaire.coeff.value = td[3].innerHTML;
		formulaire.mouvement.value = td[4].innerHTML;		

		//cache l'onglet 4 et affiche le 1
		//timeout sinon erreur bootstrap 4
		$('#collapse-4').collapse('hide');
		setTimeout( function(){
			$('#collapse-1').collapse('show')
		}, 400 );

		refreshTableCouples();
	}

	var paramOk = false;//en cas de non appel de la fonction on met false
	function refreshTableCouples(){

		//on cache le bouton qui lance l'expérience
		$('#buttonGoExperience').fadeOut();

		var allTd = $('#couples td');
		var allTh = $('#couples th');
		var distanceMin = parseInt( $('#distance').val() );
		var IDMax = parseFloat( $('#indice_diff').val() );
		var coeff = parseFloat( $('#coefficient').val() );
		var diamMin = distanceMin / (Math.pow(2, IDMax) - 1);

		var distances = [distanceMin];
		var diams = [diamMin];
		var allIds = [];
		paramOk = true;

		//on s'en moque si tous les champs sont pas remplis correctement.
		if(distanceMin > 0 && IDMax > 0 && coeff > 0){

			//on calcule les 4 premières distances 
			for(var i = 1; i < 4; i++){
				distances[i] = distances[i-1] * coeff;
			}

			//et les 4 premiers diametres
			for (var i = 1; i < 4; i++) {
				diams[i] = diams[i-1] * coeff;
			}

			//pour calculer les 4 ID
			for (var i = 0; i < 4; i++) {
				allIds.push(Math.log2( (distanceMin / diams[i]) + 1) ) ;
			}

			//on remplit la ligne et la colonne "titre"
			for (var i = 1; i <= 4; i++) {
				allTh[i].innerHTML = Math.round(100 * allIds[i - 1]) / 100;
				allTh[i + 4].innerHTML = Math.round(distances[i - 1]);
			}
			//on remplit la grille en calculant à chaque fois selon les "titre"
			//LARGEUR = d / (pow(2, id) - 1)
			for (var i = 0; i < allTd.length; i++) {
				//disyance et id de la case courante
				var distanceValue = distances[parseInt(i/4)];
				var idValue = allIds[i%4];
				//valeur de la case courante
				var diamValue = Math.round(distanceValue /( Math.pow(2, idValue)-1));
				allTd[i].innerHTML = diamValue;
				//on fait du style sur les valeurs incompatibles
				if(diamValue < 10 || distanceValue + diamValue > 1400
							|| distanceValue < diamValue || diamValue > 490){

					allTd[i].style.fontWeight = 'bold';
					allTd[i].style.color = 'red';

					paramOk = false;
				}
				//sinon on met en normal
				else{
					allTd[i].style.fontWeight = 'initial';
					allTd[i].style.color = 'initial';
				}
			}
		}
		//sinon quand les paramètres sont pourris, on remplit le tableau de vide 
		else{
			for (var i = 0; i < allTd.length; i++) {
				allTd[i].innerHTML =  "";
			}
			for (var i = 1; i < allTh.length; i++) {
				allTh[i].innerHTML =  "";
			}	
		}
	}

	function deleteExperience(element){

		var url = '<?php echo ABSURL . "ajax/deleteExperience.php"; ?>';

		var experience = element.name;
		var number = element.id.split('-')[2];

		$.get(url, {name: experience, id: number}, function(data){
			$('#experience-' + number).fadeOut(1000, function(){
				$(this).remove();
			});
			setTimeout('getAllExperiences()', 1000);
		});

	}
	
	function removeSubject(subjectId){
		var url = '<?php echo ABSURL . "ajax/removeSubject.php"; ?>';

		$.get(url, {id: subjectId}, function(data){
			$('#subject-' + subjectId).fadeOut('slow', function(){
				$(this).remove();
			});
		});
	}

	function alertRemove() {
		var html = '<div class="card text-center" id="alert" style="width:500px;">\
	  						<div class="card-block">\
							   <h4 class="card-title">Alerte</h4>\
							   <p class="card-text">Voulez-vous vraiment supprimer cet élément ?</p>\
							   <button class="btn btn-primary" role="button">Oui</button>\
							   <button class="btn btn-secondary" role="button" onclick="$(\'#alert\').fadeOut()">Non</button>\
						  	</div>\
						</div>';

		$('body').append(html);
	}

</script>

<script type="text/javascript" src="<?=ABSURL?>js/layout.js"></script>

</html>
<?php
}else{
	require('../include/authentification.php');
} ?>
