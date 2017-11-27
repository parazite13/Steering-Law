<?php
	session_start();

	//la personne (admin) se déconnecte
	if(isset($_POST['disconnect'])){
		session_destroy();

		// Corrige le bug de retransmission du formulaire
		header("Location: " . ABSURL);
		exit;
	}

	if(!isset($_SESSION['connect'])) $_SESSION['connect'] = false;

	//la personne essaie de se connecter
	if(isset($_POST['mdp'])){
		if($_POST['mdp'] == MDP_ADMIN){
			$_SESSION['connect'] = true;
			if(isset($_POST['request_url'])){
				header('Location: ' . $_POST['request_url']);
				exit;
			}
		}else{
			$_SESSION['alertMdp'] = true;
		}

		// Corrige le bug de retransmission du formulaire
		header("Location: " . ABSURL);
		exit;
	}
?>