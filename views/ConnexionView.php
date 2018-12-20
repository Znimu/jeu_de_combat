<?php $title = 'Jeu de combat - connexion'; ?>

<?php ob_start(); ?>
<?php
if (isset($message)) // On a un message à afficher ?
{
  echo '<p class="message ' . $typeMessage . '">', $message, '</p>'; // Si oui, on l'affiche.
}
?>

<form class="formConnexion" action="" method="post">
    <br />
    <p>
        <label for="nom">Nom : </label>
        <input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" /><br />
        <label for="type">Type : </label>
        <select name="type">
            <option value="brute">Brute</option>
            <option value="guerrier">Guerrier</option>
            <option value="magicien">Magicien</option>
            <option value="sorcier">Sorcier</option>
        </select>
        <input type="submit" value="Créer ce personnage" name="creer" />
    </p>
    <br />
</form>
<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>