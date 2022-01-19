<?php

// Création/ouverture du fichier session
session_start();
// PREMIÈRE LIGNE DE CODE, se positionne en haut en premier avant tout traitements php

// ---

//Connexion à la base de données : 'boutique'
$pdo = new PDO('mysql:host=localhost;dbname=boutique', 'root', 'root', array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

// ---
//Définition d'une constante : 
define('URL', "http://localhost:8888/php/boutique/");
// Correspond à l'URL de la racine de notre site

// ---
// Définition des variables : 
$content = ''; // variable prévue pour recevoir du contenu
$error = ''; // variable prévue pour recevoir les messages d'erreur

// ---
// Inclusion des fonctions :
require_once "fonction.inc.php";