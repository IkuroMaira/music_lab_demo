<?php require_once "inc/header.inc.php"; //inclusion du fichier header ?>
<?php
//AFFICHAGE DES PRODUITS : 

//Je récupère les différentes catégories de la table 'produit' :
$r = execute_requete(" SELECT DISTINCT categorie FROM produit ");

$content .= "<div class='row'>";

	//Affichage des catégories
	$content .= "<div class='col-3'>";
		$content .= "<div class='list-group-item'>";

			while( $categorie = $r->fetch( PDO::FETCH_ASSOC ) ){

				//debug( $categorie );
				$content .= "<a href='?categorie=$categorie[categorie]' class='list-group-item' >
								$categorie[categorie]
							</a>";
			}

		$content .= "</div>";
	$content .= "</div>";

	//EXERCICE : Affichez les produits correpondants à la catégorie cliquée
	//debug( $_GET );

	$content .= "<div class='col-8 offset-1'>";
		$content .= "<div class='row'>";

		if( isset($_GET['categorie'] ) ){ //Si il existe 'categorie' dans l'URL, c'est que l'on a forcément cliquée sur une catégorie du menu !

			$r = execute_requete(" SELECT * FROM produit WHERE categorie = '$_GET[categorie]' ");

			while( $produit = $r->fetch(PDO::FETCH_ASSOC) ){

				//debug( $produit );

				$content .= "<div class='col-2'>";
					$content .= "<div class='thumbnail' style='border:1px solid #eee'>";

						$content .= "<a href='fiche_produit.php?id_produit=$produit[id_produit]'>";
						//Ici, je créer un lien <a> pour accéder au fichier fiche_produit.php' et pour récupérer les indois du produit sur lequel on a cliqué, on fait passer l'id dans l'URL

							$content .= "<img src='$produit[photo]' width='80'>";

							$content .= "<p> $produit[titre]</p>";
							$content .= "<p> $produit[prix]</p>";

						$content .= "<a>";

					$content .= '</div>';
				$content .= '</div>';
			}
		}
		else{ //SINON, c'est que l'on arrive sur la page la première fois (et donc que l'on a pas cliqué sur une des catégories du menu)

			$content .= "<h3> ON AFFICHE CE QUE L'ON VEUT (par défaut) </h3>";
		}

		$content .= '</div>';
	$content .= '</div>';

$content .= "</div>";

//---------------------------------------------------------------------------------
?>
<h1>Bienvenu sur mon site</h1>

<?= $content //affichage du contenu ?>
    
<?php require_once "inc/footer.inc.php"; //inclusion du fichier footer ?>