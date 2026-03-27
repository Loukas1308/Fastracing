<?php 
session_start();
require 'connexionDB.php';

// Initialisation du panier
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// ------ GESTION DES ACTIONS DU PANIER ------

// 1. Ajouter un produit
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
    
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id] += $qty;
    } else {
        $_SESSION['panier'][$id] = $qty;
    }
    
    // Si c'est une requête asynchrone AJAX
    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
        header('Content-Type: application/json');
        $cartCount = array_sum($_SESSION['panier']);
        echo json_encode(['status' => 'success', 'cartCount' => $cartCount]);
        exit;
    }
    
    if (isset($_GET['return']) && $_GET['return'] == 'boutique') {
        header('Location: testproduits.php');
        exit;
    }
    
    header('Location: panier.php');
    exit;
}

// 2. Supprimer un produit
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    unset($_SESSION['panier'][$id]);
    header('Location: panier.php');
    exit;
}

// 3. Modifier la quantité (+1 ou -1)
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['qty'])) {
    $id = (int)$_GET['id'];
    $qty = (int)$_GET['qty'];
    
    if ($qty > 0) {
        $_SESSION['panier'][$id] = $qty;
    } else {
        unset($_SESSION['panier'][$id]);
    }
    header('Location: panier.php');
    exit;
}

// ------ RÉCUPÉRATION DES PRODUITS DU PANIER POUR L'AFFICHAGE ------
$produitsPanier = [];
$totalHT = 0;

if (!empty($_SESSION['panier'])) {
    $ids = implode(',', array_keys($_SESSION['panier']));
    
    // Requête sécurisée (puisque les IDs viennent de keys() d'int castés)
    $requete = $pdo->query("SELECT * FROM `produit` WHERE id IN ($ids)");
    $produitsBDD = $requete->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($produitsBDD as $prod) {
        $id = $prod['id'];
        $qty = $_SESSION['panier'][$id]; 
        $imageNom = !empty($prod['image']) ? $prod['image'] : (!empty($prod['photo']) ? $prod['photo'] : 'default.png');
        $prix = floatval($prod['prix_htva_eur']);
        
        $produitAAfficher = [
            'id' => $id,
            'name' => htmlspecialchars($prod['nom_commercial']),
            'category' => 'Boutique',
            'price' => $prix,
            'image' => './images/' . htmlspecialchars($imageNom),
            'qty' => $qty,
            'total_prod' => $prix * $qty
        ];
        
        $totalHT += $produitAAfficher['total_prod'];
        $produitsPanier[] = $produitAAfficher;
    }
}

// Calculs Finaux (exemple avec une TVA à 20% et livraison gratuite)
$tva = $totalHT * 0.20;
$totalTTC = $totalHT + $tva;

function formatPrice($val) {
    return number_format($val, 2, ',', ' ') . ' €';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mon Panier - FastRacers</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#05070d] text-white flex flex-col">

  <!-- Header -->
  <?php include 'header.php'; ?>

  <!-- Contenu Principal -->
  <main class="flex-grow mx-auto max-w-7xl px-6 py-12 lg:px-10 w-full">
    <div class="mb-10">
      <h1 class="text-4xl font-semibold tracking-tight lg:text-5xl">Mon Panier</h1>
      <p class="mt-3 text-white/60">Vérifie tes articles avant de valider ta commande.</p>
    </div>

    <?php if (empty($produitsPanier)): ?>
        <div class="text-center py-24 rounded-[28px] border border-dashed border-white/10 bg-white/[0.02]">
           <svg class="mx-auto h-16 w-16 text-white/20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
           <h2 class="text-2xl font-semibold mb-2">Ton panier est vide</h2>
           <p class="text-white/50 mb-8">Découvre nos équipements SimRacing pour commencer.</p>
           <a href="testproduits.php" class="inline-block rounded-2xl bg-white px-6 py-3 font-semibold text-black transition hover:scale-[0.99] hover:bg-gray-100">Retourner à la boutique</a>
        </div>
    <?php else: ?>

    <div class="grid gap-10 lg:grid-cols-12">
      <!-- Liste des articles -->
      <div class="lg:col-span-8 space-y-6">
        
        <?php foreach ($produitsPanier as $p): ?>
        <article class="flex flex-col sm:flex-row items-center gap-6 rounded-[28px] border border-white/10 bg-white/[0.03] p-5 shadow-[0_20px_80px_rgba(0,0,0,0.45)] transition hover:border-white/20">
          <div class="h-32 w-32 shrink-0 overflow-hidden rounded-2xl bg-[#0a0f1a]">
             <img src="<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>" class="h-full w-full object-cover" />
          </div>
          <div class="flex flex-1 flex-col justify-between h-full w-full">
            <div class="flex justify-between items-start w-full gap-4">
              <div>
                <h3 class="text-xl font-semibold"><?php echo $p['name']; ?></h3>
                <p class="text-sm text-white/50 mt-1"><?php echo $p['category']; ?></p>
              </div>
              <p class="text-xl font-bold whitespace-nowrap"><?php echo formatPrice($p['price']); ?></p>
            </div>
            
            <div class="flex items-center justify-between mt-6 w-full">
              <!-- Quantité -->
              <div class="flex items-center rounded-xl border border-white/10 bg-white/5 p-1">
                <a href="panier.php?action=update&id=<?php echo $p['id']; ?>&qty=<?php echo $p['qty'] - 1; ?>" class="px-3 py-1 text-white/60 hover:text-white transition cursor-pointer">−</a>
                <span class="px-2 text-sm font-medium"><?php echo $p['qty']; ?></span>
                <a href="panier.php?action=update&id=<?php echo $p['id']; ?>&qty=<?php echo $p['qty'] + 1; ?>" class="px-3 py-1 text-white/60 hover:text-white transition cursor-pointer">+</a>
              </div>
              
              <!-- Supprimer -->
              <a href="panier.php?action=remove&id=<?php echo $p['id']; ?>" class="text-sm font-medium text-red-500 hover:text-red-400 transition" aria-label="Supprimer">
                Supprimer
              </a>
            </div>
          </div>
        </article>
        <?php endforeach; ?>

      </div>

      <!-- Résumé de la commande -->
      <aside class="lg:col-span-4 h-fit rounded-[28px] border border-white/10 bg-white/[0.03] p-6 shadow-[0_20px_80px_rgba(0,0,0,0.45)] backdrop-blur-xl">
        <h2 class="text-xl font-semibold mb-6">Résumé de la commande</h2>
        
        <div class="space-y-4 text-sm text-white/70">
          <div class="flex justify-between">
            <span>Sous-total HT</span>
            <span class="text-white font-medium"><?php echo formatPrice($totalHT); ?></span>
          </div>
          <div class="flex justify-between">
            <span>Frais d'expédition</span>
            <span class="text-emerald-400 font-medium tracking-wide uppercase text-xs rounded-full bg-emerald-500/10 px-2 py-1 mt-[-2px]">Offert</span>
          </div>
          <div class="flex justify-between">
            <span>TVA (20%)</span>
            <span class="text-white font-medium"><?php echo formatPrice($tva); ?></span>
          </div>
        </div>

        <div class="my-6 border-t border-white/10"></div>
        
        <div class="flex justify-between mb-8 items-end">
          <span class="text-lg font-semibold">Total TTC</span>
          <span class="text-3xl font-bold tracking-tight"><?php echo formatPrice($totalTTC); ?></span>
        </div>

        <button class="w-full rounded-2xl bg-white px-4 py-4 text-sm font-bold text-black transition hover:scale-[0.99] hover:bg-gray-100 flex justify-center items-center gap-2">
          Commander
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
        
        <div class="mt-5 flex items-center justify-center gap-2 text-xs text-white/40">
           <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
           Paiement 100% sécurisé (Stripe / PayPal)
        </div>
      </aside>
    </div>
    
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>
