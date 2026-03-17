<?php

$host = 'localhost';

$dbname = 'nheqhpjvcd_FastRacers';

$username = 'nheqhpjvcd_Ceo';

$password = 'H>EV%:|q6|';


$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// 1. Connexion  la base
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, $options);
}
catch (PDOException $e) {
    die("Erreur de connexion  la base de donnes : " . $e->getMessage());
}

// AUCUN HTML ICI ! Ce fichier ne sert qu'à initialiser la connexion ($pdo).
// Les requêtes et l'affichage (ex: SELECT * FROM produit) doivent être faits 
// directement dans le fichier de la page (ex: testproduits.php, produits.php...)

?>
