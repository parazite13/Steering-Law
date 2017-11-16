<header>
	<div class="collapse bg-inverse fixed-top" id="navbarHeader" style="margin-top: 56px;">
		<div class="container">
			<div class="row">
				<div class="col py-4">
					<h4 class="text-white">A propos</h4>
					<p class="text-muted">La loi de Fitts est un modèle du mouvement humain. Cette loi exprime le temps requis pour aller rapidement d'une position de départ à une zone finale de destination, en fonction du rapport distance à la cible / largeur de la cible (D/L).
					</p>
				</div>
				<div class="col py-4 text-right">
					<h4 class="text-white">Espace administrateur</h4>
					<?php if(!isAdmin()){ ?>
						<a href="<?php echo ABSURL ?>admin" class="btn btn-secondary" role="button">Se connecter</a>';
					<?php }else{ ?>
						<form action="<?php echo ABSURL ?>" method="post">';
							<button type="submit" value="true" class="btn btn-secondary" name="disconnect" role="button">Se déconnecter</button>';
						</form>';
						<?php if(getCurrentUrl() == ABSURL.'admin/' ){ ?>
							<a href="<?php echo ABSURL ?>experience.php" class="btn btn-secondary mt-2" role="button">Aller à l'expérience</a>';
						<?php }else{ ?>
							<a href="<?php echo ABSURL ?>admin" class="btn btn-secondary mt-2" role="button">Interface admin</a>';
						<?php } ?>
						
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<div class="navbar navbar-inverse bg-inverse fixed-top">
		<div class="row">
			<div class="col">
				<a href="<?php echo ABSURL ?>" class="navbar-brand">Loi de Fitts</a>
			</div>
			<div class="col text-right">
				<div class="mr-3 py-2 d-inline hidden-xs-down" style="color:white">
					Vous êtes : <?php echo '<u>' . $_SESSION['type'] . '</u>'; ?>
				</div>
				<button class="navbar-toggler d-inline" type="button" role="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
			</div>
		</div>
	</div>
</header>
<div style="margin-top: 56px;"></div>
