<?php $title = 'Jeu de combat'; ?>

<?php ob_start(); ?>

			<div class="deco_div"><a class="deco" href="?deconnexion=1"><i class="fas fa-power-off"></i> Déconnexion</a></div>

<?php
if (isset($message)) // On a un message à afficher ?
{
  	echo '<p class="message ' . $typeMessage . '">', $message, '</p>'; // Si oui, on l'affiche.
}
?>
            <div class="column-layout">
                <div class="div_block1">
                    <p>
<?php
	echo '<span class="img_float_left">';
	if ($perso->type() == "magicien")
	{
		echo '<i class="fas fa-hat-wizard"></i>';
	}
	elseif ($perso->type() == "guerrier")
	{
		echo '<i class="fas fa-shield-alt"></i>';
	}
	elseif ($perso->type() == "brute")
	{
		echo '<i class="fas fa-fist-raised"></i>';
	}
	echo '</span>';
?>
                        <span class="span_name">Nom : <strong><?= htmlspecialchars($perso->nom()) ?></strong></span><br />
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
	elseif ($perso->type() == "brute")
	{
		echo "Attaque : +", $perso->atout();
	}
?>
                    </p>
                </div>
                
                <div class="div_block2">
                    <h3>Qui frapper ?</h3>
                    <p class="listCharacters">
<?php
if ($perso->timeEndormi() > time()) // Perso endormi : moins de 24h
{
	$delai = $perso->timeEndormi() - time();
	echo "Un magicien vous a endormi !<br />Vous vous réveillerez dans", $perso->timeEndormiString($delai), "s.";
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
            if ($unPerso->type() == "magicien")
            {
                echo '<i class="fas fa-hat-wizard"></i> ';
            }
            elseif ($unPerso->type() == "guerrier")
            {
                echo '<i class="fas fa-shield-alt"></i> ';
            }
            elseif ($unPerso->type() == "brute")
            {
                echo '<i class="fas fa-fist-raised"></i> ';
            }
			echo htmlspecialchars($unPerso->nom()), '</a>
						(dégâts : ', $unPerso->degats(), ')';
			if ($perso->type() == "magicien")
			{
				echo ' <a class="lien_ensorceler" href="?ensorceler=', $unPerso->id(), '">Lancer un sort</a>';
			}
			echo '<br />';
		}
	}
}
?>
                    </p>
                </div>
            </div>

<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>