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
	
	if (isset($_SESSION['perso']))
	{
		$perso = $_SESSION['perso'];
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
						break;
				}
			}
			else
			{
				$message = '<i class="fas fa-exclamation-triangle"></i> Impossible de trouver le personnage à frapper !';
			}
		}
		else 
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Impossible de frapper sans être connecté !';
		}
	}
	
	if (isset($_POST['creer']) && isset($_POST['nom']) && isset($_POST['type'])) // Si on a voulu créer un personnage.
	{
		if ($_POST['type'] == "magicien")
		{
			$perso = new Magicien([
				'nom' => $_POST['nom'],
				'type' => $_POST['type']
			]); // On crée un nouveau magicien.
		}
		elseif ($_POST['type'] == "guerrier")
		{
			$perso = new Guerrier([
				'nom' => $_POST['nom'],
				'type' => $_POST['type']
			]); // On crée un nouveau guerrier.
		}
		
		if (!$perso->nomValide())
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Le nom choisi est invalide.';
			unset($perso);
		}
		elseif ($manager->exists($perso->nom()))
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Le nom du personnage est déjà pris.';
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
		}
	}
	elseif (isset($_GET['ensorceler']))
	{
		if (!isset($perso))
		{
			$message = '<i class="fas fa-exclamation-triangle"></i> Impossible d\'ensorceler sans être connecté !';
		}
		else
		{
			if (!$manager->exists(intval($_GET['ensorceler'])))
			{
				$message = "Ce personnage à ensorceler n'existe pas !";
			}
			else
			{
				$persoAEnsorceler = $manager->get(intval($_GET['ensorceler']));
				$resu_sort = $perso->ensorceler($persoAEnsorceler);
				
				switch($resu_sort)
				{
					case Magicien::CEST_MOI:
						$message = '<i class="fas fa-exclamation-triangle"></i> Impossible de s\'ensorceler soi-même !';
						break;
						
					case Magicien::MANA_EMPTY:
						$message = '<i class="fas fa-exclamation-triangle"></i> Pas assez de magie !';
						break;
						
					case Magicien::SORT_REUSSI:
						$message = "Le personnage " . $persoAEnsorceler->nom() . " a bien été ensorcelé !";
						$manager->update($persoAEnsorceler);
						break;
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>TP : Mini jeu de combat</title>
		<meta charset="utf-8" />
    <link rel='stylesheet' href="css/style.css" />
		<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
	</head>
	<body><p>Nombre de personnages enregistrés : <?= $manager->count() ?></p>
    <p><a class="deco" href="?deconnexion=1"><i class="fas fa-power-off"></i> Déconnexion</a></p>
<?php
if (isset($message)) // On a un message à afficher ?
{
  echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
}
if (isset($perso)) // Si on utilise un personnage (nouveau ou pas).
{
	//var_dump($perso->dernierCoup());
?>
    <fieldset>
      <legend>Mes informations</legend>
      <p>
<?php
	echo '<div class="img_float_left">';
	if ($perso->type() == "magicien")
	{
		echo '<i class="fas fa-hat-wizard"></i>';
	}
	elseif ($perso->type() == "guerrier")
	{
		echo '<i class="fas fa-shield-alt"></i>';
	}
	echo '</div>';
?>
        Nom : <strong><?= htmlspecialchars($perso->nom()) ?></strong><br />
        Type : <?= htmlspecialchars($perso->type()) ?><br />
				<div class="clear_both"></div>
        Dégâts : <?= $perso->degats() ?>
				<br />
				<div class="sante_max">
					<div class="sante_actu" style="width:<?= $perso->degats() * 2 ?>px">&nbsp;</div>
				</div>
				<br />
        Expérience : <?= $perso->experience() ?><br />
        Level : <?= $perso->level() ?><br />
        Force : <?= $perso->forcePersonnage() ?><br />
        <!--NB de coups : <?= $perso->nbCoups() ?><br />
        Dernier coup : <?= ($perso->dernierCoup() == null ? "--" : DateTime::createFromFormat('d/m/Y', $perso->dernierCoup()->date)) ?>-->
<?php
	if ($perso->type() == "magicien")
	{
		echo "Magie : ", $perso->atout();
	}
	elseif ($perso->type() == "guerrier")
	{
		echo "Protection : ", $perso->atout();
	}
?>
      </p>
    </fieldset>
    
		<br />
		
    <fieldset>
      <legend>Qui frapper ?</legend>
      <p>
<?php
if ($perso->timeEndormi() > time()) // Perso endormi : moins de 24h
{
	$delai = $perso->timeEndormi() - time();
	$delai_txt = "";
	$nb_tmp = intval($delai / 3600);
	if ($nb_tmp > 0) { // HOURS
		$delai_txt .= " " . $nb_tmp . " h";
		$delai -= 3600 * $nb_tmp;
	}
	$nb_tmp = intval($delai / 60);
	if ($nb_tmp > 0) { // MINUTES
		$delai_txt .= " " . $nb_tmp . " min";
		$delai -= 60 * $nb_tmp;
	}
	echo "Un magicien vous a endormi ! Vous vous réveillerez dans", $delai_txt, " ", $delai, "s.";
}
else // Perso pas endormi
{
	$persos = $manager->getList($perso->nom());
	if (empty($persos))
	{
		echo 'Personne à frapper !';
	}
	else
	{
		foreach ($persos as $unPerso)
		{
			echo '<a class="lien_frapper" href="?frapper=', $unPerso->id(), '">';
			if ($unPerso->timeEndormi() > time())
				echo "zZz ";
			echo htmlspecialchars($unPerso->nom()), '</a>
						(dégâts : ', $unPerso->degats(), ' | type : ', $unPerso->type(), ')';
			if ($perso->type() == "magicien")
			{
				echo ' | <a href="?ensorceler=', $unPerso->id(), '">Lancer un sort</a>';
			}
			echo '<br />';
		}
	}
}
?>
      </p>
    </fieldset>
<?php
}
else
{
?>
    <form action="" method="post">
      <p>
        <label for="nom">Nom : </label>
				<input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" /><br />
				<label for="type">Type : </label>
				<select name="type">
					<option value="magicien">Magicien</option>
					<option value="guerrier">Guerrier</option>
				</select>
        <input type="submit" value="Créer ce personnage" name="creer" />
      </p>
    </form>
<?php
}
?>
  </body>
</html>
<?php
	if (isset($perso))
	{
		$_SESSION['perso'] = $perso;
	}
?>