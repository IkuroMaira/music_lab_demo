<?php
//fonction debugage : (permet de faire un print_r() "amélioré)
function debug( $arg ){

    echo "<div style='background:#fda500; z-index:1000; padding: 20px;' >";
    
        $trace = debug_backtrace();
        //debug_backtrace() : fonction interne de php qui retourne un array avec des infos de l'endroit où l'on fait appel à la fonction

        echo "<p>Debug demandé dans le fichier : ". $trace[0]['file'] ." à la ligne : ". $trace[0]['line'] ."</p>";

        echo "<pre>";
            print_r( $arg );
        echo "</pre>";

    echo "</div>";
}

//----------------------------------------------------------
//Fonction pour exécuter la requête :
function execute_requete( $req ){

    global $pdo;

    $pdostatement = $pdo->query( $req );

    return $pdostatement;
}

//----------------------------------------------------------
//Fonction userConnect() : Si l'internaute est connecté, on renvoie "true", s'il n'est pas connecté, on renvoie "false"
function userConnect(){

    if( !isset( $_SESSION['membre'] ) ){ //SI la session/membre N'EXISTE PAS, cela signifie que 'lon n'est pas connecté et donc on renvoi "false"

        return false;
    }
    else{ //SINON, c'est que session/membre existe et donc que l'on est conencté, on renvoi "true"
        
        return true;
    }
}

//----------------------------------------------------------
//fonction adminConnect() : SI l'admin est connecté, renvoie "true", s'il n'est pas connecté on renvoie "false"
function adminConnect(){

    if( userConnect() && $_SESSION['membre']['statut'] == 1 ){ //SI l'utilisateur est connecté ET qu'il est admin, donc que son statut est égale à 1, on renvoie "true"

        return true;
    }
    else{ // SINON, c'est que son statut est à zero, on renvoie "false

        return false;
    }
}

//----------------------------------------------------------
//Fonction pour créer un panier :
function creation_panier(){ 

    if( !isset( $_SESSION['panier'] ) ){ //SI la session/panier N'EXISTE PAS, on la crée

        $_SESSION['panier'] = array();

            $_SESSION['panier']['titre'] = array();
            $_SESSION['panier']['id_produit'] = array();
            $_SESSION['panier']['quantite'] = array();
            $_SESSION['panier']['prix'] = array();

    }
}

//----------------------------------------------------------
//Fonction d'ajout au panier :
function ajout_panier( $titre, $id_produit, $quantite, $prix ){

    creation_panier(); //Ici, on fait appel à la fonction déclarée ci-dessus
        //SOIT le panier n'existe pas et on le crée (c'est à dire la première fois que l'on tente d'ajouter un produit au panier)
        //SOIT il existe et on l'utilise (puisqu'on ne rentre pas dans la condition de la fonction creation_panier() )

    $index = array_search( $id_produit, $_SESSION['panier']['id_produit'] );
    //array_search( arg1, arg2 );
        //arg1 : ce que l'on recherche
        //arg2 : dans quel tableau on effectue la recherche
    //La valeur de retour de la fonction renverra l'indice (correspondant à l'indice du tableau SI il y a une correspondance de la rechercher) sinon "false"
        debug( $index );

    if( $index !== false ){ //SI $index est strictement différent de "false" c'est que le produit est déjà présent dans le panier car la fonction array_search() aura trouvé un indice correspondant et donc on va ajouter la quantite avec la nouvelle récupérée lors de l'ajout au panier

        $_SESSION['panier']['quantite'][$index] += $quantite;
    }
    else{ //SINON, c'est que le produit n'est pas dans le panier (la fonction array_search() n'a pas trouvé de correspondance) et donc on unsert toutes les infos dans session/panier

        $_SESSION['panier']['titre'][] = $titre;
        $_SESSION['panier']['id_produit'][] = $id_produit;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['prix'][] = $prix;
        //ATTENTION de bien penser à mettre des crochets VIDES ce qui permet d'ajouter une valeur supplémentaire à un tableau !!
    }
}

//----------------------------------------------------------
//fonction montant_total du panier :
function montant_total(){

    $total = 0;

    for( $i = 0; $i < sizeof( $_SESSION['panier']['id_produit'] ); $i++ ){

        $total += ( $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i] );
        //A chque tour de boucle (qui correspond au nobmre de produit dans le panier), on ajoute le montant total (quantite*prix) pour chaque produit dans la variable $total
    }
    return $total;
}


