<!DOCTYPE html>
<html>
<head>
	<?php getHead(); ?>
	<title>Authentification</title>
</head>
<body>


	<div class="container" id="container">
		<div class="row mb-3">
			<div class="col text-center">
				<h1>Authentification nécessaire</h1>
			</div>
		</div>
		<form action="" method="post">
			<input type="text" style="display: none;" name="request_url" value="<?php echo getCurrentUrl(); ?>" disabled />
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

<?php 
if(isset($_SESSION['alertMdp']) && $_SESSION['alertMdp']){
	echoAlert("danger", "Le mot de passe est incorrect");
	unset($_SESSION['alertMdp']);
}
?>

</html>