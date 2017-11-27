<!DOCTYPE html>
<html>

<head>
	<?php getHead(); ?>
	<title>404</title>
</head>

<style type="text/css">
.nb-error {
	margin: 0 auto;
	text-align: center;
	max-width: 500;
	padding: 60px 30px;
}

.nb-error .error-code {
	color: #2d353c;
	font-size: 96px;
	line-height: 100px;
}

.nb-error .error-desc {
	font-size: 12px;
	color: #647788;
}

.nb-error .input-group {
	margin: 30px 0;
}
</style>

<body>
	<div class="nb-error">
		<div class="error-code">404</div>
		<h3 class="font-bold">Nous n'avons pas pu trouver la page que vous demandez...</h3>
		<div class="error-desc">
			Desolé mais la page que vous essayer de charger n'existe pas ou a été supprimé
			Vous pouvez revenir sur la page d'accueil
			<div class="input-group">
				<a href="<?php echo ABSURL; ?>" role="button" class="mx-auto btn btn-primary">Revenir à l'accueil</a>
			</div>
			<?php getFooter(); ?>
		</div>
	</div>
</body>
</html>
