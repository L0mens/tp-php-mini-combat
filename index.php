<html lang="fr">
    <head>
        <title>Mini jeu de combat</title>
    </head>
    <body>
    <h1>Mini jeu de Combat - Sélectionne ton personnage</h1>
        <form action="gestion-perso.php" method="POST">
            <p>
                <label>Nom :
                    <input type="text" name="nom" maxlength="50" />
                </label>
                <input type="submit" value="Créer ce personnage" name="creer" />
                <input type="submit" value="Utiliser ce personnage" name="utiliser" />
            </p>
        </form>
    </body>
</html>