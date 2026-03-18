# 🛍️ E-Commerce Laravel 12 — Projet Complet

Stack: **Laravel 12 · Filament 3 · Tailwind CSS 3 · Vite · MySQL**

---

## ✅ Fonctionnalités

### 🛒 Boutique Front-end
- Page d'accueil avec produits en vedette, nouveautés, promotions
- Catalogue produits avec filtres (catégorie, prix, promotion)
- Page produit détaillée avec galerie, variantes, avis
- Panier d'achat (persistant, avec session pour invités + fusion à la connexion)
- Checkout complet (livraison, paiement à la livraison, récapitulatif)
- Codes promo / coupons
- Compte client (commandes, favoris, profil, notifications)
- Livraison gratuite dès le seuil configuré dans les Settings
- Mot de passe oublié / réinitialisation par email

### 🔐 Authentification
- Inscription / Connexion avec rate limiting anti-bruteforce
- Mot de passe oublié (email de réinitialisation)
- Gestion de profil

### 🎛️ Administration Filament
- Dashboard avec statistiques (CA, commandes, stock)
- Graphique revenus 30 jours
- Gestion produits (prix, stock, images, variantes, SEO)
- Gestion catégories (hiérarchique)
- Gestion commandes (statuts, suivi)
- Gestion utilisateurs
- Gestion coupons / codes promo
- Alertes automatiques stock bas

### 📧 Email (via Resend — GRATUIT jusqu'à 3 000 emails/mois)
- Confirmation de commande client
- Notification nouvelle commande aux admins
- Changement de statut de commande
- Email de bienvenue à l'inscription
- Réinitialisation de mot de passe
- Alerte stock bas aux admins

---

## 🚀 Installation

### Prérequis
- PHP >= 8.2
- MySQL 8+
- Composer
- Node.js >= 18 + npm
- Compte Resend gratuit sur resend.com

### Étapes

```bash
# 1. Décompresser le projet
cd ecommerce

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JS
npm install

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer dans .env :
#    - DB_DATABASE, DB_USERNAME, DB_PASSWORD
#    - MAIL_PASSWORD= votre clé API Resend (re_xxxx...)
#    - MAIL_FROM_ADDRESS= votre email expéditeur

# 6. Créer la base de données MySQL
mysql -u root -p -e "CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Migrer et peupler la base de données
php artisan migrate --seed

# 8. Lier le stockage public
php artisan storage:link

# 9. Compiler les assets
npm run build

# 10. Démarrer le worker de queue (emails en arrière-plan)
php artisan queue:work --daemon &

# 11. Démarrer le serveur
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
| Admin | admin@ecommerce.ci | password |
| Client | client@ecommerce.ci | password |

### Codes promo de test
| Code | Réduction |
|------|-----------|
| BIENVENUE10 | 10% |
| SOLDES20 | 20% (min. 500 XOF) |
| LIVRAISON | Livraison gratuite |

---

## 📧 Configuration email (Resend — gratuit)

1. Créer un compte sur [resend.com](https://resend.com) (gratuit, pas de CB)
2. Ajouter et vérifier votre domaine
3. Générer une clé API
4. Dans `.env` :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxx   # votre clé API
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="commandes@votredomaine.com"
```

---

## 💳 Mode de paiement

Actuellement : **paiement à la livraison uniquement** (adapté au marché ivoirien).

Pour ajouter le Mobile Money (Wave CI, Orange Money, MTN CI) ultérieurement :
- [CinetPay](https://cinetpay.com) — leader en Côte d'Ivoire (~1.5% de commission)
- [Paydunya](https://paydunya.com) — alternative avec API Laravel simple

---

## 🔧 Développement

```bash
# Assets en temps réel
npm run dev

# Queue worker (emails)
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
php artisan queue:work --daemon
```

---

Développé avec ❤️ — Laravel 12 + Filament 3 + Tailwind CSS 3 + Vite
