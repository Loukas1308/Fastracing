<?php
require 'connexionDB.php'; // On se connecte à la base de données

try {
  $requete = $pdo->query('SELECT * FROM `produit`');
  $produitsBDD = $requete->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
  die("Erreur de BDD : " . $e->getMessage());
}

// Nettoyage et préparation des données BDD pour le Javascript
$produitsFormates = [];
foreach ($produitsBDD as $produit) {
  // On extrait l'image si elle existe, sinon image par défaut
  $imageNom = !empty($produit['image']) ? $produit['image'] : (!empty($produit['photo']) ? $produit['photo'] : 'default.png');

  // On rajoute le produit dans le format attendu par le code JS en le sécurisant
  $produitsFormates[] = [
    'id' => $produit['id'] ?? rand(1000, 9999),
    'name' => $produit['nom_commercial'] ?? 'Produit Sans Nom',
    'category' => 'Boutique', // Ou $produit['categorie'] si ça existe
    'subcategory' => '',
    'material' => '-', // Ou $produit['matiere']
    'price' => floatval($produit['prix_htva_eur'] ?? 0),
    'stock' => true,
    'rating' => 5.0,
    'image' => './images/' . $imageNom,
    'description' => $produit['description'] ?? ($produit['desc'] ?? ''),
    'badge' => 'Disponible'
  ];
}

$produitsJSON = json_encode($produitsFormates, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Boutique - Tous les produits</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="min-h-screen bg-[#05070d] text-white">
    <div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
      <header class="mb-8 border-b border-white/10 pb-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <div class="mb-2 text-sm uppercase tracking-[0.35em] text-white/55">BOUTIQUE</div>
            <h1 class="text-4xl font-semibold tracking-tight lg:text-5xl">Tous les produits</h1>
            <p class="mt-3 max-w-2xl text-base text-white/70">
              Une page catalogue premium inspirée de ton design sombre, avec recherche, filtres et tri.
            </p>
          </div>

          <div class="flex items-center gap-3 self-start rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70 shadow-2xl shadow-black/30">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
            <span id="productCount">0 produit</span>
          </div>
        </div>
      </header>

      <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <button
            id="toggleFilters"
            class="inline-flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
          >
            <span id="toggleFiltersText">Afficher les filtres</span>
            <span id="toggleFiltersIcon" class="text-white/50">+</span>
          </button>

          <div class="text-sm text-white/55">Utilise les filtres seulement si nécessaire.</div>
        </div>

        <aside id="filtersPanel" class="hidden rounded-[28px] border border-white/10 bg-white/[0.03] p-5 shadow-[0_20px_80px_rgba(0,0,0,0.45)] backdrop-blur-xl">
          <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold">Filtres</h2>
            <button id="resetFilters" class="text-sm text-white/60 transition hover:text-white">Réinitialiser</button>
          </div>

          <div class="grid gap-5 lg:grid-cols-2 xl:grid-cols-5">
            <div class="xl:col-span-2">
              <label class="mb-2 block text-sm text-white/65">Recherche</label>
              <div class="rounded-2xl border border-white/10 bg-[#0a0f1a] px-4 py-3">
                <input
                  id="searchInput"
                  placeholder="Rechercher un produit..."
                  class="w-full bg-transparent text-sm outline-none placeholder:text-white/30"
                />
              </div>
            </div>

            <div>
              <label class="mb-2 block text-sm text-white/65">Catégorie</label>
              <select id="categorySelect" class="w-full rounded-2xl border border-white/10 bg-[#0a0f1a] px-4 py-3 text-sm outline-none"></select>
            </div>

            <div>
              <label class="mb-2 block text-sm text-white/65">Trier par</label>
              <select id="sortSelect" class="w-full rounded-2xl border border-white/10 bg-[#0a0f1a] px-4 py-3 text-sm outline-none">
                <option value="featured">Mis en avant</option>
                <option value="price-asc">Prix croissant</option>
                <option value="price-desc">Prix décroissant</option>
                <option value="rating">Mieux notés</option>
                <option value="name">Nom A-Z</option>
              </select>
            </div>

            <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-white/10 bg-[#0a0f1a] px-4 py-3 text-sm lg:self-end">
              <span class="text-white/80">En stock uniquement</span>
              <input id="stockOnly" type="checkbox" class="h-4 w-4 accent-white" />
            </label>
          </div>

          <div class="mt-5">
            <label class="mb-2 block text-sm text-white/65">Matière</label>
            <div id="materialButtons" class="flex flex-wrap gap-2"></div>
          </div>
        </aside>

        <section>
          <div id="categoryChips" class="mb-5 flex flex-wrap items-center gap-3"></div>
          <div id="productsGrid" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3"></div>
          <div id="emptyState" class="hidden rounded-[28px] border border-dashed border-white/10 bg-white/[0.02] px-8 py-16 text-center">
            <h3 class="text-2xl font-semibold">Aucun produit trouvé</h3>
            <p class="mt-3 text-white/60">Essaie de modifier la recherche ou les filtres pour afficher plus de résultats.</p>
          </div>
        </section>
      </div>
    </div>

    <script>
      // Récupération dynamique des produits depuis PHP
      const products = <?php echo $produitsJSON; ?>;

      const state = {
        search: '',
        selectedCategory: 'Toutes',
        selectedMaterial: 'Tous',
        stockOnly: false,
        sortBy: 'featured',
        filtersOpen: false,
      };

      const categories = ['Toutes', ...new Set(products.map((p) => p.category))];
      const materials = ['Tous', ...new Set(products.map((p) => p.material))];

      const elements = {
        productCount: document.getElementById('productCount'),
        toggleFilters: document.getElementById('toggleFilters'),
        toggleFiltersText: document.getElementById('toggleFiltersText'),
        toggleFiltersIcon: document.getElementById('toggleFiltersIcon'),
        filtersPanel: document.getElementById('filtersPanel'),
        resetFilters: document.getElementById('resetFilters'),
        searchInput: document.getElementById('searchInput'),
        categorySelect: document.getElementById('categorySelect'),
        sortSelect: document.getElementById('sortSelect'),
        stockOnly: document.getElementById('stockOnly'),
        materialButtons: document.getElementById('materialButtons'),
        categoryChips: document.getElementById('categoryChips'),
        productsGrid: document.getElementById('productsGrid'),
        emptyState: document.getElementById('emptyState'),
      };

      function formatPrice(price) {
        return price.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
      }

      function buildSelectOptions() {
        elements.categorySelect.innerHTML = categories.map((category) => `<option value="${category}">${category}</option>`).join('');
      }

      function buildMaterialButtons() {
        elements.materialButtons.innerHTML = materials.map((material) => {
          const active = state.selectedMaterial === material;
          return `<button data-material="${material}" class="rounded-full border px-4 py-2 text-sm transition ${active ? 'border-white/30 bg-white text-black' : 'border-white/10 bg-white/[0.03] text-white/70 hover:bg-white/10'}">${material}</button>`;
        }).join('');

        elements.materialButtons.querySelectorAll('button').forEach((button) => {
          button.addEventListener('click', () => {
            state.selectedMaterial = button.dataset.material;
            render();
          });
        });
      }

      function buildCategoryChips() {
        elements.categoryChips.innerHTML = categories.slice(1).map((category) => {
          const active = state.selectedCategory === category;
          return `<button data-category="${category}" class="rounded-full border px-4 py-2 text-sm font-medium uppercase tracking-wide transition ${active ? 'border-white/30 bg-white text-black' : 'border-white/10 bg-white/[0.03] text-white/75 hover:bg-white/10'}">${category}</button>`;
        }).join('');

        elements.categoryChips.querySelectorAll('button').forEach((button) => {
          button.addEventListener('click', () => {
            state.selectedCategory = button.dataset.category;
            elements.categorySelect.value = state.selectedCategory;
            render();
          });
        });
      }

      function getFilteredProducts() {
        const normalizedSearch = state.search.trim().toLowerCase();
        const result = products.filter((product) => {
          const matchesSearch = !normalizedSearch || product.name.toLowerCase().includes(normalizedSearch) || product.description.toLowerCase().includes(normalizedSearch) || product.subcategory.toLowerCase().includes(normalizedSearch);
          const matchesCategory = state.selectedCategory === 'Toutes' || product.category === state.selectedCategory;
          const matchesMaterial = state.selectedMaterial === 'Tous' || product.material === state.selectedMaterial;
          const matchesStock = !state.stockOnly || product.stock;
          return matchesSearch && matchesCategory && matchesMaterial && matchesStock;
        });

        if (state.sortBy === 'price-asc') result.sort((a, b) => a.price - b.price);
        if (state.sortBy === 'price-desc') result.sort((a, b) => b.price - a.price);
        if (state.sortBy === 'rating') result.sort((a, b) => b.rating - a.rating);
        if (state.sortBy === 'name') result.sort((a, b) => a.name.localeCompare(b.name));

        return result;
      }

      function renderProducts(filteredProducts) {
        elements.productsGrid.innerHTML = filteredProducts.map((product) => `
          <article class="group overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.03] shadow-[0_20px_80px_rgba(0,0,0,0.45)] transition hover:-translate-y-1 hover:border-white/20">
            <div class="relative aspect-[1.15/1] overflow-hidden border-b border-white/10 bg-[#070b14]">
              <img src="${product.image}" alt="${product.name}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
              <div class="absolute inset-0 bg-gradient-to-t from-[#05070d] via-transparent to-transparent"></div>
              <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                <span class="rounded-full border border-white/10 bg-black/35 px-3 py-1 text-xs font-semibold uppercase tracking-wide backdrop-blur-md">${product.category}</span>
                <span class="rounded-full border border-white/10 bg-black/35 px-3 py-1 text-xs font-semibold uppercase tracking-wide backdrop-blur-md">${product.subcategory}</span>
              </div>
              <div class="absolute right-4 top-4 rounded-full border border-white/10 bg-black/35 px-3 py-1 text-xs font-semibold backdrop-blur-md">${product.badge}</div>
            </div>
            <div class="p-5">
              <h3 class="text-2xl font-semibold tracking-tight">${product.name}</h3>
              <p class="mt-2 text-sm leading-6 text-white/68">${product.description}</p>
              <div class="mb-5 mt-4 flex items-center gap-2 text-xs uppercase tracking-wide text-white/60">
                <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1">${product.material}</span>
                <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1">★ ${product.rating}</span>
              </div>
              <div class="mb-5 flex items-center justify-between gap-3">
                <div class="text-4xl font-semibold tracking-tight">${formatPrice(product.price)}</div>
                <span class="rounded-full px-4 py-2 text-sm font-medium ${product.stock ? 'border border-emerald-500/20 bg-emerald-500/10 text-emerald-300' : 'border border-red-500/20 bg-red-500/10 text-red-300'}">${product.stock ? 'En stock' : 'Indisponible'}</span>
              </div>
              <div class="flex gap-3">
                <button class="flex-1 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-black transition hover:scale-[0.99]">Voir le produit</button>
                <button class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Ajouter</button>
              </div>
            </div>
          </article>
        `).join('');
      }

      function render() {
        const filteredProducts = getFilteredProducts();
        elements.productCount.textContent = `${filteredProducts.length} produit${filteredProducts.length > 1 ? 's' : ''}`;
        elements.toggleFiltersText.textContent = state.filtersOpen ? 'Masquer les filtres' : 'Afficher les filtres';
        elements.toggleFiltersIcon.textContent = state.filtersOpen ? '−' : '+';
        elements.filtersPanel.classList.toggle('hidden', !state.filtersOpen);
        elements.searchInput.value = state.search;
        elements.categorySelect.value = state.selectedCategory;
        elements.sortSelect.value = state.sortBy;
        elements.stockOnly.checked = state.stockOnly;
        buildMaterialButtons();
        buildCategoryChips();
        renderProducts(filteredProducts);
        elements.emptyState.classList.toggle('hidden', filteredProducts.length !== 0);
      }

      elements.toggleFilters.addEventListener('click', () => {
        state.filtersOpen = !state.filtersOpen;
        render();
      });

      elements.resetFilters.addEventListener('click', () => {
        state.search = '';
        state.selectedCategory = 'Toutes';
        state.selectedMaterial = 'Tous';
        state.stockOnly = false;
        state.sortBy = 'featured';
        render();
      });

      elements.searchInput.addEventListener('input', (e) => {
        state.search = e.target.value;
        render();
      });

      elements.categorySelect.addEventListener('change', (e) => {
        state.selectedCategory = e.target.value;
        render();
      });

      elements.sortSelect.addEventListener('change', (e) => {
        state.sortBy = e.target.value;
        render();
      });

      elements.stockOnly.addEventListener('change', (e) => {
        state.stockOnly = e.target.checked;
        render();
      });

      buildSelectOptions();
      render();
    </script>
  </body>
</html>
