# 🛍️ E-Commerce Laravel 12 — Projet Complet

Stack: **Laravel 12 · Filament 3 · Tailwind CSS 3 · Vite · MySQL**

---

## ✅ Fonctionnalités

### 🛒 Boutique Front-end
- Page d'accueil avec produits en vedette, nouveautés, promotions
- Catalogue produits avec filtres (catégorie, prix, promotion)
- Page produit détaillée avec galerie, variantes, avis
- Panier d'achat (persistant, avec session pour invités)
- Checkout complet (livraison, paiement, récapitulatif)
- Codes promo / coupons
- Compte client (commandes, favoris, profil)
- Livraison gratuite dès 500 MAD

### 🔐 Authentification
- Inscription / Connexion
- Gestion de profil
- Changement de mot de passe

### 🎛️ Administration Filament
- Dashboard avec statistiques (CA, commandes, stock)
- Graphique revenus 30 jours
- Gestion produits (prix, stock, images, variantes, SEO)
- Gestion catégories (hiérarchique)
- Gestion commandes (statuts, suivi)
- Gestion utilisateurs
- Gestion coupons / codes promo

### 🗃️ Base de données
- Users, Categories, Brands, Products, ProductImages, ProductVariants
- Carts, CartItems, Orders, OrderItems
- Reviews, Wishlists, Addresses, Coupons, Settings

---

## 🚀 Installation

### Prérequis
- PHP >= 8.2
- MySQL 8+
- Composer
- Node.js >= 18 + npm

### Étapes

```bash
# 1. Cloner / décompresser le projet
cd ecommerce

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JS
npm install

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_DATABASE=ecommerce_db
# DB_USERNAME=root
# DB_PASSWORD=votre_mot_de_passe

# 6. Créer la base de données MySQL
mysql -u root -p -e "CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Migrer et peupler la base de données
php artisan migrate --seed

# 8. Lier le stockage public
php artisan storage:link

# 9. Compiler les assets
npm run build

# 10. Démarrer le serveur
php artisan serve
```

### Accès
| URL | Description |
|-----|-------------|
| http://localhost:8000 | Boutique front-end |
| http://localhost:8000/admin | Panel admin Filament |

### Comptes de test
| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@ecommerce.ma | password |
| Client | client@example.com | password |

### Codes promo de test
| Code | Réduction |
|------|-----------|
| BIENVENUE10 | 10% |
| SOLDES20 | 20% (min. 500 MAD) |
| LIVRAISON | Livraison gratuite |

---

## 📁 Structure du projet

```
app/
├── Filament/Admin/
│   ├── Resources/          # ProductResource, CategoryResource, OrderResource...
│   └── Widgets/            # StatsOverview, RevenueChart, LatestOrders
├── Http/Controllers/
│   ├── Auth/               # Login, Register
│   └── Shop/               # Home, Product, Cart, Checkout, Account
├── Models/                 # Product, Order, Cart, User...
├── Providers/Filament/     # AdminPanelProvider
└── Services/               # CartService, OrderService
database/
├── migrations/             # Toutes les tables
└── seeders/                # Données de démonstration
resources/
├── css/app.css             # Tailwind CSS
├── js/app.js               # JavaScript
└── views/
    ├── layouts/app.blade.php
    ├── shop/               # Pages boutique
    ├── auth/               # Login / Register
    └── components/         # Composants réutilisables
routes/web.php              # Toutes les routes
```

---

## 🔧 Développement

```bash
# Assets en temps réel
npm run dev

# Queue worker (si emails)
php artisan queue:work

# Vider les caches
php artisan optimize:clear
```

---

## 📦 Technologies

| Package | Version | Usage |
|---------|---------|-------|
| Laravel | ^12 | Framework PHP |
| Filament | ^3.3 | Panel administration |
| Tailwind CSS | ^3.4 | Styles CSS |
| Vite | ^5.4 | Build assets |
| MySQL | 8+ | Base de données |

---

## 🚢 Déploiement Production

```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

---

Développé avec ❤️ — Laravel 12 + Filament 3 + Tailwind CSS 3 + Vite
