<?php
	session_start();

	//la personne (admin) se déconnecte
	if(isset($_POST['disconnect'])){
		session_destroy();

		// Corrige le bug de retransmission du formulaire
		header("Location: " . ABSURL);
		exit;
	}

	if(!isset($_SESSION['type'])) $_SESSION['type'] = 'Utilisateur';

	//la personne essaie de se connecter
	if(isset($_POST['mdp'])){
		if($_POST['mdp'] == MDP_ADMIN){
			$_SESSION['type'] = 'Admin';
		}
		else{
			$_SESSION['alertMdp'] = true;
		}

		// Corrige le bug de retransmission du formulaire
		header("Location: " . ABSURL . "admin");
		exit;
	}
?>