<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>	
	<title>Projet - Loi de Fitts</title>
</head>
<body>

	<?php getHeader(); ?>

	<div class="container" id="container">
		<div class="row mb-3">
			<div class="col text-center">
				<h1>Espace Administrateur</h1>
			</div>
		</div>
		<form action="" method="post">
			<input type="text" style="display: none;" name="url" value="<?php echo getCurrentUrl(); ?>" disabled />
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


	<?php getFooter(); ?>

</body>
<script type="text/javascript">
	$(document).ready(function(){
		<?php 
		if(isset($_SESSION['alertMdp']) && $_SESSION['alertMdp']){
			echo 'var html = \'	<div class="alert alert-danger" role="alert">\
			Le mot de passe est incorrect...\
		</div>\'
		$("#container").append(html);
		setTimeout(function(){
			$(".alert").fadeOut(2000);
		}, 3000);';
		unset($_SESSION['alertMdp']);
	}
	?>
});
</script>
<script type="text/javascript" src="<?=ABSURL?>js/fitts.js"></script>
<script type="text/javascript" src="<?=ABSURL?>js/layout.js"></script>
</html>