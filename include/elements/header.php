<nav class="navbar navbar-light bg-faded">
	<?php if(isAdmin()): ?>
		<form action="" method="post">
			<button type="submit" value="true" class="btn btn-secondary" name="disconnect" role="button">Se déconnecter</button>
		</form>
	<?php else: ?>
		<a href="admin/">
			<button id="btn-admin" class="btn btn-secondary" role="button">
				Admin
			</button>
		</a>
	<?php endif; ?>
</nav>