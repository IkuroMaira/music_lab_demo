<?php require_once "../inc/header.inc.php"; ?>
<?php 


// Restriction d'accés à la page administrative
if(!adminConnect()){ //SI l'Admin N'EST PAS connecté, on le redirige sur la page connexion.php

    header('location:../connexion.php');
    exit;
}
// --------------------------------------------
    debug($_GET); // $_GET permet de récupérer l'URL. Et donc peut nous servir également à montrer en debug notre suppression et notre affichage où on utilise (if(isset($_GET['action'] etc ))).

    // SUPPRESSION : 
    if(isset($_GET['action']) && $_GET['action'] == "suppression"){ // SI il y a une 'action' dans l'URL ET que cette 'action' est égale à 'suppression'

        // Je vais récupérer l'information => le chemin pour récupérer la photo + base de données

        // Ici, on va voir tout le CRUD - 300 lignes : Affichage - Modification - Insertion
        // SUPPRESSION DE LA PHOTO :
        // 1. Récupération de la colonne 'photo'en BDD : 
        $r = execute_requete(" SELECT photo FROM produit WHERE id_produit = '$_GET[id_produit]' ");

        $photo_a_supprimer = $r->fetch(PDO::FETCH_ASSOC);
            // debug($photo_a_supprimer);

        $chemin_photo_a_supprimer = str_replace('http://localhost:8888', $_SERVER['DOCUMENT_ROOT'], $photo_a_supprimer['photo']);
            // debug($chemin_photo_a_supprimer);

        //str_replace( arg1, arg2, arg3 )! fonction de php qui permet de remplacer des occurences dans une chaines
            //arg1 : la chaine que l'on souhaite remplacer
            //arg2 : la chaine de remplacement
            //arg3 : la chaine sur laquelle on veut effectuer les changements

        /*Ici, je remplace : 'http://localhost'
                    par : $_SERVER['DOCUMENT_ROOT'] <=> "C:/xampp/htdocs
                    dans : $photo_a_supprimer['photo'] (l'adresse de la photo récupérée en BDD)
        */

    if( !empty( $chemin_photo_a_supprimer ) && file_exists( $chemin_photo_a_supprimer ) ){

        unlink( $chemin_photo_a_supprimer );
        //unlink( $arg ) : permet de supprimer un fichier (ici, $arg correspond au chemin du fichier)
    }

    //La portion de code ci-dessous (la suppression) DOIT IMPERATIVEMENT se trouve APRES la gestion de la suppression du fichier "physique" car si on supprime ava,t le produit en BDD, on ne pourrait plus récupérer l'adresse de la photo en base.

        execute_requete("DELETE FROM produit WHERE id_produit = '$_GET[id_produit]'");
        // SUPPRESSION dans la table 'produit' À CONDITION que dans le colonne 'id_produit' soit égale à l'id_produit que l'on récupère dans l'URL (celle passée lorsque l'on clique sur la corbeille)

    }

// --------------------------------------------
// Gesttion de produit : INSERTION DES PRODUITS

if(!empty($_POST)){ // Si le formulaire est validé et pas vide

    // debug($_POST);

    // CONTROLES sur les saisies ( il faudrait en faire pour chaque <input>)

    //EXERCICE : Faites en sortes d'afficher un message d'erreur SI la référence postée existe déjà :

    $r = execute_requete(" SELECT reference FROM produit WHERE reference = '$_POST[reference]' ");
    // Ici, on sélectionne la référence de la table 'produit' À CONDITION que dans la colonne 'reference', ce soit égal à ce que l'utilisateur a saisi. 

    if( $r->rowCount() >= 1 ){ // SI le résultat est supérieur ou égal à 1, c'est que la référence est déjà attribuée car il aura trouvé une correspondance dans la table 'produit' et renvera donc une ligne de résultat (que retourne rowCount())

        $error .= "<div class='alert alert-danger'> Référence indisponible </div>";
        
    }
// -------------------------------------------- 
    // Ici, je passe toutes les infos postées par l'admin dans les fonctions addlashes() et htmlentities() :
    foreach($_POST as $indice => $valeur){

        $_POST[$indice] = htmlentities(addslashes($valeur));
    }
// --------------------------------------------   

    if(isset($_GET['action']) && $_GET['action'] == 'modification'){ // SI je suis dans le cadre d'une modification, je récupère le chemin en BDD de la photo du produit à modifier (grâce à la value de l'input type='hidden') et je le stocke dans la variable $photo_bdd

        $photo_bdd = $_POST['photo_actuelle'];
    }

// --------------------------------------------
// GESTION DE LA PHOTO : 
// debug($_FILES);
// debug($_SERVER); // => permet d'afficher toutes les informations du serveur

        // ATTENTION ! Pensez au lien photo localhost

        // /Applications/MAMP/htdocs/php/boutique/admin/gestion_produit.php : RACINE DU SERVEUR['photo']['name']
        // Et l'adresse de notre serveur et du dossie photo : /Applications/MAMP/htdocs/php/boutique/photo
// --------------------------------------------
// 
if(!empty($_FILES['photo']['name'])){ // SI le nom de la photo dans $_FILES N'EST PAS VIDE, c'est que l'on a téléchargé un fichier
    
    // Ici, je renomme la photo (avec la reference)
    $nom_photo = $_POST['reference'].'_'.$_FILES['photo']['name'];
    //    debug($nom_photo);

    // Chemin pour accéder à la photo (à insérer en BDD)
    $photo_bdd = URL."photo/".$nom_photo;
        // Rappel : la constante URL <=> http://localhost/PHP/boutique/
        //debug($photo_bdd);

    // Chemin où l'on souhaite enregistrer le fichier "physique" de la photo
    $photo_dossier = $_SERVER['DOCUMENT_ROOT']."/PHP/boutique/photo/".$nom_photo;
    // $_SERVER = superglobale de PHO qui retournera un tableau avec des infos sur le serveur courant
        // ICI : $_SERVER['DOCUMENT_ROOT'] <=> /Applications/MAMP/htdocs (le chemin qui correspond à MAC et l'application MAMP)
        // debug($photo_dossier);

        // Enregistrement (le fichier physique) de la photo dans le dossier 'photo' de notre serveur
        copy($_FILES['photo']['tmp_name'], $photo_dossier);
            // copy (arg1, arg2)
                // arg1 : chemin du fichier source
                // arg2 : chemin de destination

}
else{ // SINON, c'est que l'on a pas télécharger de fichier et donc on affiche un message d'erreur
    $error .= "<div class='alert alert-danger'> Vous n'avez pas uploader de photo </div>";
}


// --------------------------------------------
// MODIFICATION et INSERTION :
if(isset($_GET['action']) && $_GET['action'] == 'modification'){ // SI il y a une 'action' dans l'URL ET quue cette 'action' est égale à "modification", alor son effectue une requete UPDATE

    execute_requete("UPDATE produit SET reference = '$_POST[reference]',
                                        categorie = '$_POST[categorie]',
                                        titre = '$_POST[titre]',
                                        description = '$_POST[description]',
                                        couleur = '$_POST[couleur]',
                                        taille = '$_POST[taille]',
                                        sexe = '$_POST[sexe]',
                                        photo = '$photo_bdd',
                                        prix = '$_POST[prix]',
                                        stock = '$_POST[stock]'
                    WHERE id_produit = $_GET[id_produit]
                    ");
    // Redirection vers l'affichage produit : ``
    header('location:gestion_produit.php?action=affichage');
}
elseif(empty($error)){ // SI la variable '$error' est vide, je fais l'insertion 
    // SINON(c'est dans le cadres d'un "ajout") SI la variable '$error' est vide, je fais l'insertion

        execute_requete("INSERT INTO produit(reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock)

                                    VALUES(
                                        '$_POST[reference]',
                                        '$_POST[categorie]',
                                        '$_POST[titre]',
                                        '$_POST[description]',
                                        '$_POST[couleur]',
                                        '$_POST[taille]',
                                        '$_POST[sexe]',
                                        '$photo_bdd',
                                        '$_POST[prix]',
                                        '$_POST[stock]'
                                    ) 
                        ");
        // FAIRE ATTENTION À LA DERNIÈRE VIRGULE !!! NE PAS LA METTRE !!! => SINON L'INSERTION NE S'EFFECTUE PAS !
        // Et le bug affichera l'erreur dans comme étant dans le fichier "fonction.inc.php"
        // Faire attention également aux quotes
    }

}

// --------------------------------------------
//AFFICHAGE des produits : (toujours après l'insertion pour pouvoir voir le dernier produit inséré, meme si ici, on sépare l'affichage de l'ajout)
if( isset( $_GET['action'] ) && $_GET['action']  == 'affichage' ){ //SI il existe une 'action' dans l'URL ET que cette 'action' est égale à "affichage", alors on affiche la liste des produits :

    //EXERCICE : Affichez le nombre de produits et la liste des produits sous forme de tableau et faites en sorte d'afficher l'image !!
    $r = execute_requete(" SELECT * FROM produit ");

    $content .= "<h2>Liste des produits</h2>";
    $content .= "<p>Nombre de produits dans la boutique : ". $r->rowCount() ."</p>";
    
    $content .= "<table class='table table-bordered'>";
        $content .= "<tr>";

            $nombre_colonne = $r->columnCount();
            //columnCount() : retourne le nombre de colonnes issues du jeu de résultat retourné par la requête ($r)
        
            for( $i = 0; $i < $nombre_colonne; $i++ ){

                $titre = $r->getColumnMeta( $i );
                //getColumnMeta( $int ) : retourne des informations sur les colonnes (de la table) du jeu de résultat retourné par la requête 
                    //debug( $titre );

                $content .= "<th> $titre[name] </th>";
            }
            $content .= "<th>Suppression</th>"; // ICI, on rajoute manuellement une colonne suppression
            // Pour chaque ligne je veux ajouter un bouton "supprimer"
            $content .= "<th>Modification</th>"; // ICI, on rajoute manuellement colonne modification

        $content .= "</tr>";
            // debug($ligne);

        while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
			//fetch() : permet de retourner un tableau (ici, $ligne) avec les valeurs de la BDD, indéxé par les champs de la table 'produit' grâce au paramètre PDO::FETCH_ASSOC
				//Ici, '$ligne' va donc retourner UN tableau correspondant à UNE LIGNE de résultat issu du jeu de résultat de la requête ($r = object PDOStatement)
			//On utilise la boucle while pour afficher TOUTES les lignes TANT QU'il y en a à afficher car fetch(), retourne LA ligne suivante d'un jeu de résultat

			$content .= "<tr>";
				//debug( $ligne );

				foreach( $ligne as $indice => $valeur ){

					if( $indice == 'photo' ){ //SI l'indice '$indice' (du tableau '$ligne' retourné par le fetch()) est égal à 'photo' ALORS, on affiche une cellule avec une balise <img> et dans l'attribut 'src', on y met la valeur correspondante '$valeur' qui représente l'adresse pour accéder à l'image en BDD

						$content .= "<td> <img src='$valeur' width='80'> </td>";
					}
					else{ //SINON, c'est que les indices sont différents de 'photo' et donc on affiche les valeurs dans des cellules simples

						$content .= "<td> $valeur </td>";
					}
				}

            $content .= '<td class="text-center">
                            <a href="?action=suppression&id_produit='.$ligne['id_produit'].'" onclick="return( confirm(\'Voulez-vous supprimer le produit :'. $ligne['titre'] .'\') )">
                                <i class="fas fa-trash-restore-alt"></i>
                            </a>
                        </td>';

            $content .= '<td class="text-center">
                            <a href="?action=modification&id_produit='.$ligne['id_produit'].'">
                                <i class="far fa-edit"></i>
                            </a>
                        </td>';

			$content .= "</tr>";
		}
    $content .= "</table>";
}

// --------------------------------------------
// --------------------------------------------
?>

<h1>Gestion de produit</h1>

<!-- 2 liens -->
<a href="?action=ajout">Ajout produit</a><br>
<a href="?action=affichage">Affichage produit</a><hr>

<?php echo $error; // Affichage de la variable '$error'?> 

<?php echo $content; // Affichage de la variable '$content' ?>

<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification') ) : //SI il existe 'action' dans l'URL ET que cette 'action' est égale à "ajout" OU à 'modification', alors on affiche le formulaire 
    
    if(isset($_GET['id_produit'] ) ){ // SI il esxiste un 'id_produit' dans l'URL, c'est que l'on est dans le acdre d'une modification 

        // Récupération des infos à afficher pour pré-remplir le formulaire :
        $r = execute_requete("SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]' ");
        
        // exploitation des données:
        $article_actuel = $r->fetch( PDO::FETCH_ASSOC );
            debug( $article_actuel );
    }

    // ------------------------------------------------------
    if(isset($article_actuel['reference'])){ // SI il existe $article_actuel['reference'] c'est que l'on est dans le cadre d'une modification et la condition précédente aura été exécutée

        $reference = $article_actuel['reference']; // On stocke dans une variable la valeur récupérée en BDD que l'on affichera dans l'attribut value="" de l'input correcpondant (ici, reference)
    }
    else{ // SINON, c'est que je ne suis pas dans le cadre d'une modification (donc d'un ajout!) alors je stocke du "vide" dans la même variable qui sera afffiché dans l'attribut value="" de l'input correspondant (ici, reference)

        $reference ="";
    }
    
    //version ternaire
    $categorie = ( isset( $article_actuel['categorie'] ) ) ? $article_actuel['categorie'] : "";
    $titre = ( isset( $article_actuel['titre'] ) ) ? $article_actuel['titre'] : "";
    $description = ( isset( $article_actuel['description'] ) ) ? $article_actuel['description'] : "";
    $couleur = ( isset( $article_actuel['couleur'] ) ) ? $article_actuel['couleur'] : "";
    $prix = ( isset( $article_actuel['prix'] ) ) ? $article_actuel['prix'] : "";
    $stock = ( isset( $article_actuel['stock'] ) ) ? $article_actuel['stock'] : "";

    // Taille (select/option) :
    if(isset($article_actuel['taille']) && $article_actuel['taille'] == 'S'){

        $taille_s = "selected";
    }
    else{

        $taille_s = "";
    }

    $taille_m = (isset($article_actuel['taille']) && $article_actuel['taille'] == 'M') ? "selected" : "";
    $taille_l = (isset($article_actuel['taille']) && $article_actuel['taille'] == 'L') ? "selected" : "";
    $taille_xl = (isset($article_actuel['taille']) && $article_actuel['taille'] == 'XL') ? "selected" : "";

    // EXERCICE faire la même chose pour la civilités :
    if(isset($article_actuel['sexe']) && $article_actuel['sexe'] == 'm'){
        $sexe_m = "checked";
        $sexe_f = "";
    }
    else{
        $sexe_f = "checked";
        $sexe_m = "";
    }

    // Version ternaire :
    // $sexe_m = (isset($article_actuel['sexe']) && $article_actuel['sexe'] == 'm') ? "checked" : "";
    // $sexe_f = (isset($article_actuel['sexe']) && $article_actuel['sexe'] == 'f') ? "checked" : "";

    // Gestion de la photo :
    if(isset($article_actuel['photo'])){ // Si il existe $article_actuel['photo'], c'est que l'on est dans le cadre d'une modification et donc j'affiche l'image dans le formulaire grâce au chemin récupérer en BDD

        $info_photo = "<i>Vous pouvez uploader une nouvelle photo</i>";

        $info_photo .= "<img src='$article_actuel[photo]' width='100'><br> ";

        $info_photo .= "<input type = 'hidden' name='photp_actuelle' value='$article_actuel[photo]'>";
        // ICI, je créer un <input type='hidden'>  donc un input "caché" avec une value l'adresse de la photo récupérée en BDD pour poouvoir la récupérer lors de la modification dans le ca sou l'on ne télécharge pas de photo.

    }
    else{ // SINON, c'est que l'on est dans le cadre d'un "ajout" et donc on affichera

        $info_photo = "<br>";
    }

?> 


<form method="post" enctype="multipart/form-data">
<!--  enctype="multipart/form-data" : cet attribut est OBLIGATOIRE lorsque l'on souhasite uploader des fichiers et les récupérer via $_FILES -->

    <label>Référence</label><br>
    <input type="text" name="reference" value="<?php echo $reference ?>" ><br>

    <label>Catégorie</label><br>
    <input type="text" name="categorie" value="<?= $categorie ?>" ><br>

    <label>Titre</label><br>
    <input type="text" name="titre" value="<?= $titre ?>" ><br>

    <label>Description</label><br>
    <input type="text" name="description" value="<?= $description ?>" ><br>

    <label>Couleur</label><br>
    <input type="text" name="couleur" value="<?= $couleur ?>" ><br>

    <label>Taille</label><br>
    <select name="taille" >
        <option value="S" <?= $taille_s ?> > S </option>
        <option value="M" <?= $taille_m ?> > M </option>
        <option value="L" <?= $taille_l ?> > L </option>
        <option value="XL" <?= $taille_xl ?> > XL </option>
    </select><br><br>

    <label>Civilite</label><br>
    <input type="radio" name="sexe" value="m" <?= $sexe_m ?> > Homme <br>
    <input type="radio" name="sexe" value="f" <?= $sexe_f ?> > Femme <br><br>

    <label>Photo</label><br>
    <input type="file" name="photo"><br>

    <?php echo $info_photo; // Affichage de la photo (SI modification ?>)

    <label>Prix</label><br>
    <input type="text" name="prix" value="<?= $prix ?>" ><br>

    <label>Stock</label><br>
    <input type="text" name="stock" value="<?= $stock ?>" ><br><br>

    <input type="submit" class="btn btn-secondary" value="<?php echo ucfirst($_GET['action']); ?>" >
    <!-- Affichage de la valeur de l'action passée dans l'URL ('ajout' ou 'modification') dans l'attribut value="" de l'input typr='submit'
        ucfirst() : fonction de php qui permet de passer la première lettre en majuscule -->

</form>

<?php endif; // Fermeture de la condition 'if' pour gérer l'affichage du formulaire si on clique sur le lien ?>

<!-- -------------------------------------------- -->

<?php require_once "../inc/footer.inc.php"; ?>