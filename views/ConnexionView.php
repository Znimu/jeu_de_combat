<?php $title = 'Jeu de combat - connexion'; ?>

<?php ob_start(); ?>
<?php
if (isset($message)) // On a un message à afficher ?
{
  echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
}
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
<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>