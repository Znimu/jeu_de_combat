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
					case 1:
						$message = "Impossible de se frapper soi-même !";
						break;
					case 2:
						$message = "Le personnage " . $persoAFrapper->nom() . " est mort !";
						$manager->delete($persoAFrapper);
						break;
					case 3:
						$message = "Le personnage " . $persoAFrapper->nom() . " a été frappé !";
						$manager->update($persoAFrapper);
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
	
	if (isset($_POST['creer']) && isset($_POST['nom'])) // Si on a voulu créer un personnage.
	{
		$perso = new Personnage(['nom' => $_POST['nom']]); // On crée un nouveau personnage.
		
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
?>
    <p><a href="?deconnexion=1">Déconnexion</a></p>
    <fieldset>
      <legend>Mes informations</legend>
      <p>
        Nom : <?= htmlspecialchars($perso->nom()) ?><br />
        Dégâts : <?= $perso->degats() ?>
      </p>
    </fieldset>
    
    <fieldset>
      <legend>Qui frapper ?</legend>
      <p>
<?php
$persos = $manager->getList($perso->nom());
if (empty($persos))
{
  echo 'Personne à frapper !';
}
else
{
  foreach ($persos as $unPerso)
    echo '<a href="?frapper=', $unPerso->id(), '">', htmlspecialchars($unPerso->nom()), '</a> (dégâts : ', $unPerso->degats(), ')<br />';
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
        <input type="submit" value="Créer ce personnage" name="creer" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" />
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