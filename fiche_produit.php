<?php require_once "inc/header.inc.php"; ?>
<?php

// debug( $_GET );
//---------------------------------------------
//EXERCICE : 
//Création de la page fiche_produit.php

//restreindre l'accès à la page SI on a cliqué sur un lien de la page d'accueil (et donc fait passer l'id dans l'URL) SINON, on le redirige vers la page d'accueil

if( isset( $_GET['id_produit'] ) ){ //SI il y a une 'id_produit' dans l'URL, c'est que on a choisi délibérément d'afficher la fiche d'un produit en particulier et on va donc récupérer les infos en bdd

    $r = execute_requete(" SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]' ");
}
else{

    header('Location:index1.php');
    exit;
}
//---------------------------------------------
//Exploitation des données récupérées :
$produit = $r->fetch( PDO::FETCH_ASSOC );
    debug( $produit );

//créer 2 liens : (file d'ariane)
	//l'un pour permettre de retourner à l'accueil
	//l'autre pour retourner à la catégorie précédente
$content .= "<a href='index1.php'> Accueil </a> / ";

$content .= "<a href='index1.php?categorie=$produit[categorie]' > ". ucfirst( $produit['categorie'] )  . " </a><hr>";

//affichez la liste des informations des produits SAUF l'id_produit et le stock
//Pour l'image, on affichera l'image et non pas l'adresse de la bdd
foreach( $produit as $indice => $valeur ){

    if( $indice == 'photo' ){ //SI l'indice du tableau '$produit' est égal à 'photo', alors on affiche la valeur correspondante dans l'attribut src="" d'une balise <img>

        $content .= "<p> <img src='$valeur' width='200'> </p>";
    }
    elseif( $indice != 'id_produit' && $indice != 'stock' ){ //SINON SI l'indice est différent de 'id_produit' ET de 'stock' alors on affiche les infos

        $content .= "<p> <strong> $indice </strong> : $valeur </p>";
    }    
}

//---------------------------------------------
//gérer le stock à part !
	//SI il est supérieur à ZERO, on affiche le nombre de produits disponibles dans un <select> avec le nombre d'options correspondant au stock
    //SINON, on affiche rupture de stock
if( $produit['stock'] > 0 ){ //Si le stock est supérieur à ZERO on affiche le stock

    $content .= "<form method='post' action='panier.php'>";
    //Ici, l'attribut action="panier.php" : permet d'ete redirigé sur le fichier 'panier.php' lorsque l'on valide le formulaire. Les données récupérées par $_POST seront donc traitées sur le fichier 'panier.php'

        $content .= "<label> <strong> Quantite </strong> : </label>";
        $content .= "<select name='quantite' >";
            for( $i = 1; $i <= $produit['stock']; $i++ ){

                $content .= "<option value='$i' > $i </option>";
            }
        $content .= "</select><br><br>";

        $content .= "<input type='hidden' name='id_produit' value='$produit[id_produit]'>";
        //Ici, on créer un input "caché" qui permet d'envoyer l'id du produit que l'on souhaite ajouter au panier qui servira à récupérer toutes les infos du produit dans "panier.php"

        $content .= "<input type='submit' name='ajout_panier' value='Ajouter au panier' class='btn btn-secondary' >";
    
    $content .= "</form>";
}
else{ //SINON, c'est que le stock est à zero

    $content .= "<p> Rupture de stock </p>";
}

//----------------------------------------------------------------------------------
?>

<h1>Fiche produit</h1>

<?= $content; //affichage du contenu ?>

<?php require_once "inc/footer.inc.php"; ?>