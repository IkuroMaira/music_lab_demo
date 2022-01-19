<?php require_once "inc/header.inc.php"; ?>
<?php

//DECONNEXION : le script de la deconnexion se positionne AVANT LA REDIRECTION/RESTRICTION sinon, ELLE NE SERA PAS INTERPRETEE par l'interpréteur php à cause du "exit;" dans al redirection donc nous aurions déjà quitté le fichier.
//debug( $_GET );

if( isset( $_GET['action'] ) && $_GET['action'] == "deconnexion" ){ //SI il existe une 'action' dans l'URL ET que cette 'action' est égale à "deconnexion", alors on détruit le fichier de session

    //session_destroy(); //detruit le fichier de session

    unset( $_SESSION['membre'] ); //supprimera la session/membre (et donc entrainera la deco)
}

//----------------------------------------------------------
//restriction d'accès à la page SI ON EST CONNECTE
if( userConnect() ){

    //redirection vers la page de profil
    header('location:profil.php');
    exit;
}

//----------------------------------------------------------
if( $_POST ){ //Si on a validé le formulaire

    //debug( $_POST );

    //Comparaison du pseudo posté et celui en BDD 
    $r = execute_requete(" SELECT * FROM membre WHERE pseudo = '$_POST[pseudo]' ");
        //Ici, on récupère TOUTES les infos provenant de la table 'membre' A CONDITION que dans la colonne pseudo, ce soit égal à la saisie de l'itnernaute
        //debug( $r );

    if( $r->rowCount() >= 1 ){ //SI il y a une correspondance dans la table 'membre', '$r' renverra 1 ligne de résultat et donc c'est que le pseudo est valide (il existe en BDD)

        //Récupération des données pour les exploiter :
        $membre = $r->fetch( PDO::FETCH_ASSOC );
            debug( $membre );

        //Verification du mot de passe:
        if( password_verify( $_POST['mdp'], $membre['mdp'] ) ){
            //password_verify( arg1, arg2 ); retourne true ou false, et permet de comparer une chaine à une chaine cryptée
                //arg1 : le mot de passe saisie par l'utilisateur
                //arg2 : la chaine cryptée par la fonction password_hash(), ici le mdp en BDD

            //insertion des infos ($membre) de la personne qui se connecte dans le fichier de session
            $_SESSION['membre'] = $membre;
                //debug( $_SESSION );

            //redirection vers la page profil :
            header('location:profil.php');
            exit; //exit; permet de quitter A CET ENDROIT PRECIS le script courant et donc de ne pas interpréter le code qui suit cette instruction.

            //-----------------------------------------
            // //Autre méthode "manuelle" pour insérer le sinfos dans le fichier de session:
            // $_SESSION['membre']['id_membre'] = $membre['id_membre'];
            // $_SESSION['membre']['pseudo'] = $membre['pseudo'];
            // $_SESSION['membre']['mdp'] = $membre['mdp'];
            // $_SESSION['membre']['prenom'] = $membre['prenom'];
            // $_SESSION['membre']['nom'] = $membre['nom'];
            // $_SESSION['membre']['email'] = $membre['email'];
            // $_SESSION['membre']['adresse'] = $membre['adresse'];
            // $_SESSION['membre']['ville'] = $membre['ville'];
            // $_SESSION['membre']['cp'] = $membre['cp'];
            // $_SESSION['membre']['statut'] = $membre['statut'];
            
            // //-----------------------------------------
            // //Boucle foreach pour isnérer les données dans le fichier de session :
            // foreach( $membre as $indice => $valeur ){
                
            //     $_SESSION['membre'][$indice] = $valeur;
            // }
        }
        else{ //SINON, c'est que le mot de passe est faux

            $error .= "<div class='alert alert-danger'> Mot de passe incorrect </div>";
        }
    }
    else{ //SINON, c'est que le pseudo n'est pas valide

        $error .= "<div class='alert alert-danger'> Pseudo incorrect </div>";
    }
}

//----------------------------------------------------------------------------------
?>
    <h1> Connexion </h1>

    <?php echo $error; //affichage de la variable $error ?>

    <form method="post">
    
        <label>Pseudo</label><br>
        <input type="text" name="pseudo" placeholder="Votre pseudo"> <br><br>
    
        <label>Mot de passe</label><br>
        <input type="text" name="mdp" placeholder="Votre mot de passe"> <br><br>

        <input type="submit" value="Se connecter" class="btn btn-secondary">

    </form>

<?php require_once "inc/footer.inc.php"; ?>