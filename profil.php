<?php require_once "inc/header.inc.php"; ?>
<?php

//Restriction d'acces à la page : SI l'utilisateur N'EST PAS connecté
if( !userConnect() ){

    //redirection vers la page connexion.php
    header('location:connexion.php');
    exit; //exit; permet de quitter à cet endroit precis le fichier et d'arreter la lecture du script
}

//-------------------------------------------------------------
//Si l'ADMIN EST CONNECTE, on affiche un titre pour le préciser
if( adminConnect() ){

    $content .= "<h2 style='color:tomato;'> ADMINISTRATEUR </h2>";
}

//-------------------------------------------------------------
//debug( $_SESSION );

//Ici, on récupère le pseudo de la personne connectée grâce au fichier de session que l'on a remplis lors de la connexion et on l'affihce dans la balise <h2>
$pseudo = $_SESSION['membre']['pseudo'];

$content .= "<h3>Vos infos personnelles</h3>";

$content .= "<p>Votre prénom : ". $_SESSION['membre']['prenom'] ."</p>";
//OBLIGATION de faire de la concaténation lorsque l'on sohaite afficher des valeurs d'un tableau multidimentionnel (meme si l'on est entre guillemets)

$content .= "<p>Votre nom : ". $_SESSION['membre']['nom'] ."</p>";

$content .= "<p>Votre email : ". $_SESSION['membre']['email'] ."</p>";

$content .= "<p>Votre adresse : ". $_SESSION['membre']['adresse'] ." ". $_SESSION['membre']['cp'] ." à ". $_SESSION['membre']['ville'] ."</p>";

//--------------------------------------------------------------------------------------------
?>
<h1>Profil</h1>

<h2>Bonjour <?php echo $pseudo; //affichage dela variable $pseudo ?> </h2>

<?= $content; //affichage du contenu ?>

<?php require_once "inc/footer.inc.php"; ?>