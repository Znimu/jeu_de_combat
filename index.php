<?php
	require 'bootstrap.php';
	session_start();
	
	if (isset($_GET['deconnexion']))
	{
		session_destroy();
		header('Location: .');
		exit();
	}
		
	$db = new PDO('mysql:host=localhost;dbname=test2', 'root', '');
	$manager = new PersonnagesRepository($db);

	$FEController = new FrontendController();

	$typeMessage = "Confirm";
	
	if (isset($_SESSION['perso_id']))
	{
		if ($manager->exists(intval($_SESSION['perso_id']))) {
			$perso = $manager->get(intval($_SESSION['perso_id']));
			$FEController->perso = $manager->get(intval($_SESSION['perso_id']));
		}
	}
	
	if (isset($_POST['creer']) && isset($_POST['nom']) && isset($_POST['type'])) // Si on a voulu créer un personnage.
	{
		$FEController->creerPerso();
	}
	elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) // Si on a voulu utiliser un personnage.
	{
		$FEController->utiliserPerso();
	}
	elseif (isset($_GET['frapper']))
	{
		$FEController->frapper();
	}
	elseif (isset($_GET['ensorceler']))
	{
		$FEController->ensorceler();
	}
	elseif (isset($_GET['bouleDeFeu']))
	{
		$FEController->bouleDeFeu();
	}

$nb_perso_enregistres = $FEController->nbPersoEnregistres();
$message = $FEController->message;
$typeMessage = $FEController->typeMessage;

if (isset($FEController->perso)) { // On est connecté
	$perso = $FEController->perso;
	require('Views/MainView.php');
}
else {
	require('Views/ConnexionView.php');
}

if (isset($perso)) {
	$_SESSION['perso_id'] = $perso->id();
}
?>