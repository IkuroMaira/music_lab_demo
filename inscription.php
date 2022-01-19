<?php require_once "inc/header.inc.php"; //inclusion du header ?>
<?php

//----------------------------------------------
//INSCRIPTION => INSERTION

if( $_POST ){ //Si on a validé le formulaire

    debug( $_POST );

    //Controles des saisies de l'internaute (il faudrait faire des controles pour TOUS les champs du formulaire)

    //Controle la taille du pseudo (3 et 15 caractères) :
    if( strlen( $_POST['pseudo'] ) <= 3 || strlen( $_POST['pseudo'] ) > 15 ){
        //SI la taille du pseudo est inférieure ou égale à 3 - OU QUE - la taille du pseudo est supérieure à 15, alors on affiche un message d'erreur
            //strlen( $arg ) : retourne la taille d'une chaine ($arg)

        $error .= "<div class='alert alert-danger' >Erreur taille pseudo ( doit etre compris entre 3 et 15 )</div>";
    }

    //Teste SI le pseudo est disponible : (On ne peut pas avoir 2 fois le meme pseudo car nous avons indiqué une clé UNIQUE lors de la création de la BDD pour le champ 'pseudo')
    $r = execute_requete(" SELECT pseudo FROM membre WHERE pseudo = '$_POST[pseudo]' ");
    //Sélectionne moi le pseudo dans la table 'membre' A CONDITION que dans la colonne 'pseudo', ce soit égal à la saisie de l'internaute
        //debug($r); //$r représente le jeu de résultat retourné par la requête sous forme d'objet PDOStatement

    if( $r->rowCount() >= 1 ){ // SI le résultat est supérieur ou égal à 1, c'est que le pseudo est déjà attribué car il aura trouvé une correspondance dans la table 'membre' et renvera donc une ligne de résultat

        $error .= "<div class='alert alert-danger'> Pseudo indisponible </div>";
    } 

    //-------------------------------------------
    //Boucle sur toutes les saisies de l'internaute afin de les passer dans les fonctions htmlentities() et addslashes() :
    foreach( $_POST as $indice => $valeur ){

        $_POST[$indice] = htmlentities(  addslashes( $valeur )  );
    }

    //-------------------------------------------
    //Cryptage du mdp :
    $_POST['mdp'] = password_hash( $_POST['mdp'], PASSWORD_DEFAULT ); 
        //password_hash() : permet de créer une clé de hachage
        //debug( $_POST['mdp'] );

        //-------------------------------------------
    //INSERTION
    if( empty( $error ) ){ //SI la variable '$error' est vide (c'est que le formulaire a été rempli correctement), alors on fait l'insertion 

        execute_requete(" INSERT INTO membre( pseudo, mdp, nom, prenom, email, sexe, adresse, ville, cp ) 
                                    VALUES( 
                                            '$_POST[pseudo]', 
                                            '$_POST[mdp]', 
                                            '$_POST[nom]', 
                                            '$_POST[prenom]', 
                                            '$_POST[email]', 
                                            '$_POST[sexe]', 
                                            '$_POST[adresse]', 
                                            '$_POST[ville]', 
                                            '$_POST[cp]' 
                                        ) 
                        ");

        $content .= "<div class='alert alert-success'> Inscription validée
                        <a href='". URL ."connexion.php'> Cliquez ici pour vous connecter </a>
                    </div>";
    }
}
//---------------------------------------------------------------------------------------------
?>
    <h1>Inscription</h1>

    <?php echo $error; //Affichage de la variable $error ?>

    <?= $content; //affichage de la variable $content ?>

    <form method="post">

        <!-- Penser à créer un <input> pour CHAQUE CHAMPS que l'on doit insérer en BDD -->
        <label>Pseudo</label><br>
        <input type="text" name="pseudo"><br>
    
        <label>Mot de passe</label><br>
        <input type="text" name="mdp"><br>
    
        <label>Nom</label><br>
        <input type="text" name="nom"><br>
    
        <label>Prénom</label><br>
        <input type="text" name="prenom"><br>
    
        <label>Email</label><br>
        <input type="text" name="email"><br>
    
        <label>Civilite</label><br>
        <input type="radio" name="sexe" value="f" checked> Femme <br>
        <input type="radio" name="sexe" value="m"> Homme <br><br>
    
        <label>Adresse</label><br>
        <input type="text" name="adresse"><br>
            
        <label>Ville</label><br>
        <input type="text" name="ville"><br>
            
        <label>Code postal</label><br>
        <input type="text" name="cp"><br><br>
    
        <input type="submit" class="btn btn-secondary" value="S'inscrire">
    
    </form>

<?php require_once "inc/footer.inc.php"; //inclusion du footer ?>
