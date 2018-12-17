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
						$message = "Impossible de se frapper soi-même !";
						break;
						
					case Personnage::PERSONNAGE_TUE:
						$manager->update($perso);
						$message = "Le personnage " . $persoAFrapper->nom() . " est mort !";
						$manager->delete($persoAFrapper);
						break;
						
					case Personnage::PERSONNAGE_FRAPPE:
						$manager->update($perso);
						$message = "Le personnage " . $persoAFrapper->nom() . " a été frappé !";
						$manager->update($persoAFrapper);
						break;
						
					case Personnage::COUPS_EPUISES:
						$message = "Le personnage " . $perso->nom() . " a épuisé ses frappes !";
						break;
				}
			}
			else
			{
				$message = "Impossible de trouver le personnage à frapper !";
			}
		}
		else 
		{
			$message = "Impossible de frapper sans être connecté !";
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
			$message = 'Le nom choisi est invalide.';
			unset($perso);
		}
		elseif ($manager->exists($perso->nom()))
		{
			$message = 'Le nom du personnage est déjà pris.';
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
			$message = 'Ce personnage n\'existe pas !'; // S'il n'existe pas, on affichera ce message.
		}
	}
	elseif (isset($_GET['ensorceler']))
	{
		if (!isset($perso))
		{
			$message = "Impossible d'ensorceler sans être connecté !";
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
						$message = "Impossible de s'ensorceler soi-même !";
						break;
						
					case Magicien::MANA_EMPTY:
						$message = "Pas assez de magie !";
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
	</head>
	<body><p>Nombre de personnages créés : <?= $manager->count() ?></p>
<?php
if (isset($message)) // On a un message à afficher ?
{
  echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
}
if (isset($perso)) // Si on utilise un personnage (nouveau ou pas).
{
	//var_dump($perso->dernierCoup());
?>
    <p><a href="?deconnexion=1">Déconnexion</a></p>
    <fieldset>
      <legend>Mes informations</legend>
      <p>
        Nom : <?= htmlspecialchars($perso->nom()) ?><br />
        Type : <?= htmlspecialchars($perso->type()) ?><br />
        Dégâts : <?= $perso->degats() ?><br />
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
    
    <fieldset>
      <legend>Qui frapper ?</legend>
      <p>
<?php
$delai = time() - $perso->timeEndormi();
if ($delai < 3600 * 24) // Perso endormi : moins de 24h
{
	echo "Un magicien vous a endormi ! Vous vous réveillerez dans ", ($delai + 3600 * 24), "s.";
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
			echo '<a href="?frapper=', $unPerso->id(), '">', htmlspecialchars($unPerso->nom()), '</a>
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
        Nom : <input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" /><br />
				Type : 	<select name="type">
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