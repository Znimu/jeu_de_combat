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

	$typeMessage = "Confirm";
	
	if (isset($_SESSION['perso_id']))
	{
		if ($manager->exists(intval($_SESSION['perso_id'])))
			$perso = $manager->get(intval($_SESSION['perso_id']));
	}
	
	if (isset($_GET['frapper']))
	{
		if (isset($perso))
		{
			if ($manager->exists(intval($_GET['frapper'])))
			{
				$persoAFrapper = $manager->get(intval($_GET['frapper']));
				$resu_frappe = $perso->frapper($persoAFrapper);
				
				switch($resu_frappe)
				{
					case Personnage::CEST_MOI:
						$message = '<i class="fas fa-exclamation-triangle"></i> Impossible de se frapper soi-même !';
						$typeMessage = "Erreur";
						break;
						
					case Personnage::PERSONNAGE_TUE:
						$manager->update($perso);
						$message = '<i class="fas fa-check-circle"></i> Le personnage ' . $persoAFrapper->nom() . ' est mort !';
						$manager->delete($persoAFrapper);
						break;
						
					case Personnage::PERSONNAGE_FRAPPE:
						$manager->update($perso);
						$message = '<i class="fas fa-check-circle"></i> Le personnage ' . $persoAFrapper->nom() . ' a été frappé !';
						$manager->update($persoAFrapper);
						break;
						
					case Personnage::COUPS_EPUISES:
						$message = '<i class="fas fa-exclamation-triangle"></i> Le personnage ' . $perso->nom() . ' a épuisé ses frappes !';
						$typeMessage = "Erreur";
						break;
				}
			}
			else
			{
				$message = '<i class="fas fa-exclamation-triangle"></i> Impossible de trouver le personnage à frapper !';
				$typeMessage = "Erreur";
			}
		}
		else 
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Impossible de frapper sans être connecté !';
			$typeMessage = "Erreur";
		}
	}
	
	if (isset($_POST['creer']) && isset($_POST['nom']) && isset($_POST['type'])) // Si on a voulu créer un personnage.
	{
		switch($_POST['type']) {
			case "magicien":
				$perso = new Magicien([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			case "guerrier":
				$perso = new Guerrier([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			case "brute":
				$perso = new Brute([
					'nom' => $_POST['nom'],
					'type' => $_POST['type']
				]);
				break;
			default:
				$message = '<i class="fas fa-exclamation-triangle"></i> Type de personnage inconnu.';
				$typeMessage = "Erreur";
				break;
		}
		
		if (!$perso->nomValide())
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Le nom choisi est invalide.';
			$typeMessage = "Erreur";
			unset($perso);
		}
		elseif ($manager->exists($perso->nom()))
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Le nom du personnage est déjà pris.';
			$typeMessage = "Erreur";
			unset($perso);
		}
		else
		{
			$manager->add($perso);
			$perso = $manager->get($_POST['nom']);
		}
	}
	elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) // Si on a voulu utiliser un personnage.
	{
		if ($manager->exists($_POST['nom'])) // Si celui-ci existe.
		{
			$perso = $manager->get($_POST['nom']);
		}
		else
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage n\'existe pas !'; // S'il n'existe pas, on affichera ce message.
			$typeMessage = "Erreur";
		}
	}
	elseif (isset($_GET['ensorceler']))
	{
		if (!isset($perso))
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Impossible d\'ensorceler sans être connecté !';
			$typeMessage = "Erreur";
		}
		else
		{
			if ($perso->type() != "magicien")
			{
				$message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage ne peut pas encorceler !';
				$typeMessage = "Erreur";
			}
			else
				{
				if (!$manager->exists(intval($_GET['ensorceler'])))
				{
					$message = '<i class="fas fa-exclamation-triangle"></i> Ce personnage à ensorceler n\'existe pas !';
					$typeMessage = "Erreur";
				}
				else
				{
					$persoAEnsorceler = $manager->get(intval($_GET['ensorceler']));
					$resu_sort = $perso->ensorceler($persoAEnsorceler);
					
					switch($resu_sort)
					{
						case Magicien::CEST_MOI:
							$message = '<i class="fas fa-exclamation-triangle"></i> Impossible de s\'ensorceler soi-même !';
							$typeMessage = "Erreur";
							break;
							
						case Magicien::MANA_EMPTY:
							$message = '<i class="fas fa-exclamation-triangle"></i> Pas assez de magie !';
							$typeMessage = "Erreur";
							break;
							
						case Magicien::SORT_REUSSI:
							$message = '<i class="fas fa-check-circle"></i> Le personnage ' . $persoAEnsorceler->nom() . ' a bien été ensorcelé !';
							$manager->update($persoAEnsorceler);
							break;
					}
				}
			}
		}
	}

	$nb_perso_enregistres = $manager->count();

if (isset($perso)) { // On est connecté
	require('Views/MainView.php');
}
else {
	require('Views/ConnexionView.php');
}

if (isset($perso)) {
	$_SESSION['perso_id'] = $perso->id();
}
?>