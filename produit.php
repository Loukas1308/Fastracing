<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Volant F1</title>
  <link rel="stylesheet" href="./style.css">
</head>
<body class="fc">

  <header class="fc-topbar">
    <a class="fc-back" href="produits.php">← Retour</a>
    <div class="fc-logo">BOUTIQUE</div>
    <a class="fc-cart" href="#">Panier</a>
  </header>

  <main class="fc-page">
    <section class="fc-product">

      <!-- Galerie -->
      <div class="fc-gallery">
        <div class="fc-main-img">
          <img id="mainImg" src="./images/volant_f1.png" alt="Volant F1">
        </div>

        <div class="fc-thumbs">
          <button class="fc-thumb is-active" onclick="setImg(this,'./images/volant_f1.png')">
            <img src="./images/volant_f1.png" alt="">
          </button>
          <button class="fc-thumb" onclick="setImg(this,'./images/volant_f1.png')">
            <img src="./images/volant_f1.png" alt="">
          </button>
          <button class="fc-thumb" onclick="setImg(this,'./images/volant_f1.png')">
            <img src="./images/volant_f1.png" alt="">
          </button>
          <button class="fc-thumb" onclick="setImg(this,'./images/volant_f1.png')">
            <img src="./images/volant_f1.png" alt="">
          </button>
        </div>
      </div>

      <!-- Infos -->
      <div class="fc-info">
        <div class="fc-badges">
          <span class="fc-badge">SIM RACING</span>
          <span class="fc-badge">FORMULA</span>
          <span class="fc-badge">CARBONE</span>
        </div>

        <h1 class="fc-title">Volant F1</h1>
        <p class="fc-sub">
          Volant simracing type Formula en fibre de carbone, poignées en suédine, avec boutons/molettes et LED.
        </p>

        <div class="fc-priceRow">
          <div class="fc-price">205,68 €</div>
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

</body>
</html>