<nav class="navbar navbar-light bg-faded">
	<?php if(isAdmin()): ?>
		<div class="btn-toolbar justify-content-between" role="toolbar">
			<?php if(getCurrentUrl() == ABSURL): ?>
				<a href="<?=ABSURL?>admin/">
					<button type="submit" value="true" class="btn btn-secondary" role="button">
						Éspace Admin
					</button>
				</a>
			<?php endif; ?>

			<?php if(getCurrentUrl() == ABSURL . "admin/"): ?>
				<a href="<?=ABSURL?>">
					<button type="submit" value="true" class="btn btn-secondary d-inline-block" role="button">
						Faire l'expérience
					</button>
				</a>
			<?php endif; ?>

			<form action="" method="post">
				<button type="submit" value="true" class="btn btn-secondary d-inline-block" name="disconnect" role="button">
					Se déconnecter
				</button>
			</form>
		</div>
	<?php else: ?>
		<a href="admin/">
			<button id="btn-admin" class="btn btn-secondary" role="button">
				Admin
			</button>
		</a>
	<?php endif; ?>
</nav>