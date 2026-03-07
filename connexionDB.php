<?php

$host = 'localhost'; // Habituellement "localhost" quand on dveloppe sur sa machine (ou via l'hbergeur)
$dbname = 'nheqhpjvcd_FastRacers'; // Le nom de ta base de donnes
$username = 'nheqhpjvcd_Ceo'; // Sous XAMPP/WAMP, par dfaut c'est 'root' ( changer si tu es chez un hbergeur comme O2Switch, Hostinger, etc.)
$password = 'H>EV%:|q6|'; // Sous XAMPP/WAMP, par dfaut c'est vide ( changer aussi selon l'hbergeur)

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

// 2. Rcupration des donnes de la table "nos produits"
// (Attention : en SQL, les espaces dans les noms de tables sont rares. Si ta table 
// s'appelle exactement "nos produits", il faut utiliser des `backticks` en SQL)
try {
    // Si ta table s'appelle "nos_produits" avec un tiret (ce qui est plus classique), modifie la ligne en-dessous en "FROM nos_produits"
    $requete = $pdo->query('SELECT `nom_commercial`, `prix_htva_eur` FROM `produits`');
    $lesProduits = $requete->fetchAll();
}
catch (PDOException $e) {
    die("Erreur lors de la rcupration des produits : " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nos Produits FastRacers</title>
    <link rel="stylesheet" href="style.css"> <!-- On lie le style si besoin -->
    <style>
        /* Un peu de style rapide pour que ce soit joli */
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;}
        .produit { background: white; padding: 15px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); max-width: 400px; }
        .nom { font-weight: bold; font-size: 1.2em; color: #333; }
        .prix { color: #e74c3c; font-size: 1.1em; margin-top: 5px; }
    </style>
</head>
<body>

    <h1>Nos supers produits</h1>

    <?php if (empty($lesProduits)): ?>
        <p>Aucun produit trouv pour le moment.</p>
    <?php
else: ?>
        <!-- On fait une boucle pour afficher chaque produit un par un -->
        <?php foreach ($lesProduits as $unProduit): ?>
            
            <div class="produit">
                <!-- htmlspecialchars sert  scuriser l'affichage -->
                <div class="nom">
                    <?php echo htmlspecialchars($unProduit['nom_commercial'] ?? 'Nom inconnu'); ?>
                </div>
                <div class="prix">
                    <?php echo htmlspecialchars($unProduit['prix_htva_eur'] ?? '0'); ?> 
                </div>
            </div>

        <?php
    endforeach; ?>
    <?php
endif; ?>

</body>
</html>
