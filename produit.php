<?php
require 'connexionDB.php';

// Vérification de l'ID du produit dans l'URL (ex: produit.php?id=1)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: testproduits.php');
    exit;
}

$idProduit = $_GET['id'];
$requete = $pdo->prepare('SELECT * FROM `produit` WHERE id = :id');
$requete->execute(['id' => $idProduit]);
$leProduit = $requete->fetch();

// Si le produit n'existe pas dans la base
if (!$leProduit) {
    echo "<h2 style='text-align:center; color:white; padding:50px;'>Erreur : Ce produit n'existe pas.</h2><a href='testproduits.php' style='display:block; text-align:center; color:gray;'>Retour à la boutique</a>";
    exit;
}

// Gestion de l'image
$imageNom = !empty($leProduit['image']) ? $leProduit['image'] : (!empty($leProduit['photo']) ? $leProduit['photo'] : 'default.png');
$cheminImage = './images/' . htmlspecialchars($imageNom);

// Protection des variables texte pour l'affichage
$nomProduit = htmlspecialchars($leProduit['nom_commercial'] ?? 'Produit Sans Nom');
$prixProduit = htmlspecialchars($leProduit['prix_htva_eur'] ?? '0');
$descProduit = htmlspecialchars($leProduit['description'] ?? ($leProduit['desc'] ?? 'Aucune description.'));
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $nomProduit; ?> - FastRacers</title>
  <link rel="stylesheet" href="./style.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="fc">

  <?php include 'header.php'; ?>

  <main class="fc-page">
    <section class="fc-product">

      <!-- Galerie -->
      <div class="fc-gallery">
        <div class="fc-main-img">
          <img id="mainImg" src="<?php echo $cheminImage; ?>" alt="<?php echo $nomProduit; ?>">
        </div>

        <div class="fc-thumbs">
          <button class="fc-thumb is-active" onclick="setImg(this,'<?php echo $cheminImage; ?>')">
            <img src="<?php echo $cheminImage; ?>" alt="">
          </button>
        </div>
      </div>

      <!-- Infos -->
      <div class="fc-info">
        <div class="fc-badges">
          <span class="fc-badge">BOUTIQUE</span>
        </div>

        <h1 class="fc-title"><?php echo $nomProduit; ?></h1>
        <p class="fc-sub">
          <?php echo $descProduit; ?>
        </p>

        <div class="fc-priceRow">
          <div class="fc-price"><?php echo $prixProduit; ?> €</div>
          <div class="fc-availability ok">En stock</div>
        </div>

        <div class="fc-actions">
          <div class="fc-qty">
            <button type="button" class="fc-qtyBtn" onclick="decQty()">−</button>
            <input id="qty" class="fc-qtyInput" type="number" min="1" value="1">
            <button type="button" class="fc-qtyBtn" onclick="incQty()">+</button>
          </div>

          <button class="fc-ctaPrimary">Ajouter au panier</button>
          <button class="fc-ctaGhost">Acheter maintenant</button>
        </div>

        <div class="fc-shipping">
          <div class="fc-shipItem"><span>✓</span> Expédition rapide</div>
          <div class="fc-shipItem"><span>✓</span> Paiement sécurisé</div>
          <div class="fc-shipItem"><span>✓</span> Retour 14 jours</div>
        </div>

        <div class="fc-accordion">
          <details open>
            <summary>Description</summary>
            <div class="fc-accBody">
              Conçu pour la course : forme Formula, prise en main ferme, accès rapide aux réglages via boutons et molettes, LED pour un retour visuel en action.
            </div>
          </details>

          <details>
            <summary>Caractéristiques</summary>
            <div class="fc-accBody">
              <ul class="fc-list">
                <li>Face : fibre de carbone</li>
                <li>Poignées : suédine</li>
                <li>Commandes : boutons, molettes, sélecteurs</li>
                <li>LED : barre supérieure</li>
              </ul>
            </div>
          </details>

          <details>
            <summary>Compatibilité</summary>
            <div class="fc-accBody">
              <div class="fc-compat">
                <span class="fc-chip">PC</span>
                <span class="fc-chip">Xbox*</span>
                <span class="fc-chip">PlayStation*</span>
              </div>
              <div class="fc-note">* À adapter selon votre base / écosystème.</div>
            </div>
          </details>
        </div>

      </div>
    </section>
  </main>

  <script>
    function setImg(btn, src){
      document.getElementById('mainImg').src = src;
      document.querySelectorAll('.fc-thumb').forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
    }
    function incQty(){
      const el = document.getElementById('qty');
      el.value = Math.max(1, (parseInt(el.value||"1",10) + 1));
    }
    function decQty(){
      const el = document.getElementById('qty');
      el.value = Math.max(1, (parseInt(el.value||"1",10) - 1));
    }
  </script>

  <?php include 'footer.php'; ?>
</body>
</html>