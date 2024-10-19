<?php

require_once 'Personnage.php';
require_once 'PersonnageManager.php';

var_dump($_POST);

function displayErrorAndIndex($message){
    echo '<p>'.$message.'</p>';
    echo '<a href="index.php">Retour à l\'index</a>';
    die(); //permet d'arrêter l'exec
}

//On peut déjà vérifier si le nom est envoyé et n'est pas vide
if(!isset($_POST['nom']) or empty($_POST['nom'])){
    displayErrorAndIndex('Veuillez choisir un nom à choisir ou à créer');
}

//On instancie notre connecteur PDO
$db = new PDO ( 'mysql:host=localhost;dbname=eseo', 'root', '') ;
//On émet une alerte à chaque fois qu'une requête a échoué.
$db->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_WARNING ) ;

//On crée notre Manager
$manager = new PersonnagesManager($db);

if(isset($_POST['creer'])){
    $perso = new Personnage ( array ( 'nom' => $_POST ['nom'])); // On crée un nouveau personnage .
    if ( $manager -> exists ( $perso->getNom())){
        displayErrorAndIndex('Le nom du personnage est déjà pris.');
        unset( $perso ) ;
    }
    else{
        $manager-> add ( $perso ) ;
    }

}
else if (isset($_POST['utiliser'])){
    if ( $manager->exists ($_POST ['nom']) ) // Si celui -ci existe .
    {
        $perso = $manager->get($_POST ['nom']) ;
    }
    else {
        displayErrorAndIndex('Ce personnage n\' existe pas ! ');
    }

}
else if (isset($_POST['frapper'])){
    if ( $manager->exists ($_POST ['nom']) ) // Si celui -ci existe .
    {
        $perso = $manager->get($_POST ['nom']) ;
    }
    else {
        displayErrorAndIndex('Ce personnage n\' existe pas ! ');
    }

    if ( isset($_POST ['target']) and $manager->exists (intval($_POST ['target'])) ) // Si celui -ci existe .
    {
        $target = $manager->get(intval($_POST ['target']));
    }
    else {
        displayErrorAndIndex('La cible n\' existe pas ! ');
    }

    $retour = $perso->frapper($target);
    switch ( $retour )
    {
        case Personnage::CEST_MOI :
            $message = 'Mais ... pourquoi voulez-vous vous frapper ??? ';
            break ;

        case Personnage::PERSONNAGE_FRAPPE :
            $message = 'Le personnage a bien été frappé ! ';
            $manager -> update ( $perso ) ;
            $manager -> update ( $target ) ;
            break ;
        case Personnage::PERSONNAGE_TUE :
            $message = 'Vous avez tué ce personnage ! ';
            $manager -> update ( $perso ) ;
            $manager -> delete ( $target ) ;
            break ;
    }

}

$allPersoExceptMe = $manager->getListExcludingName($perso->getNom());

if(empty($allPersoExceptMe))
    displayErrorAndIndex('Pas de personnage à frapper, veuillez créer d\'autres personnages');
?>

<html lang="fr">
    <head>
        <title>Mini jeu de combat</title>
    </head>
    <body>
        <h1>Mini jeu de Combat - Combat</h1>

        <?php if(isset($message)): ?>
            <p><?= $message ?></p>
        <?php endif ?>
        <fieldset>
            <legend> Mes informations </legend>
            <p>
                Nom : <?php echo htmlspecialchars ( $perso ->getNom()) ; ?> <br>
                Dégâts : <?php echo $perso -> getDegats() ; ?>
            </p>
        </fieldset>

        <form action="gestion-perso.php" method="POST">
            <p>
                <input type="hidden" name="nom" maxlength="50" value="<?= $perso->getNom() ?>"/>
                <label> Cible :
                    <select name="target">
                    <?php foreach($allPersoExceptMe as $p):?>
                        <option value="<?= $p->getId() ?>"><?= $p->getNom() ?></option>
                    <?php endforeach; ?>
                    </select>
                </label>
                <input type="submit" value="Frapper" name="frapper" />
            </p>
        </form>
        <a href="index.php">Annuler et retourner à l'index</a>

    </body>
</html>