# 📱 TrustPhone CI — Boutique iPhone en ligne

Stack: **Laravel 12 · Filament 3 · Tailwind CSS 3 · Vite · MySQL**

Spécialiste iPhone en Côte d'Ivoire. iPhones neufs, débloqués tous opérateurs. Paiement à la réception.

---

## ✅ Fonctionnalités

### 🛒 Boutique Front-end
- Page d'accueil moderne centrée iPhone (hero sombre, badges de confiance)
- Catalogue iPhone avec filtres (gamme, prix, promotion)
- Page produit détaillée avec galerie, variantes, avis
- Panier (persistant, fusion session invité à la connexion)
- Checkout complet — **paiement à la réception uniquement**
- Compte client (commandes, favoris, profil, notifications)
- Livraison gratuite configurable (seuil dans les Settings)
- Retrait en boutique gratuit
- Comparateur de produits
- Mot de passe oublié / réinitialisation par email

### 🔐 Authentification
- Inscription / Connexion avec rate limiting anti-bruteforce
- Gestion de profil

### 🎛️ Administration Filament
- Dashboard avec statistiques (CA, commandes, stock)
- Graphique revenus 30 jours
- Gestion produits iPhone (prix, stock, images, variantes, SEO)
- Gestion catégories
- Gestion commandes (statuts, suivi)
- Gestion utilisateurs
- Paramètres boutique (adresse, horaires, Google Maps)
- Paramètres WhatsApp (GreenAPI)

### 📧 Email (via Resend — GRATUIT jusqu'à 3 000 emails/mois)
- Confirmation de commande client (invités + connectés)
- Notification nouvelle commande aux admins
- Changement de statut de commande
- Email de bienvenue
- Réinitialisation de mot de passe
- Alerte stock bas aux admins

### 📱 WhatsApp (via GreenAPI)
- Confirmation de commande automatique par WhatsApp
- Mise à jour de statut par WhatsApp
- Configuration dans le panel admin

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
# 1. Extraire le projet
cd trustphone-ci

# 2. Dépendances PHP
composer install

# 3. Dépendances JS
npm install

# 4. Environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer .env :
#    - DB_DATABASE=trustphone_ci
#    - DB_USERNAME, DB_PASSWORD
#    - MAIL_PASSWORD= votre clé Resend (re_xxxx...)
#    - MAIL_FROM_ADDRESS= commandes@votredomaine.com

# 6. Créer la base de données
mysql -u root -p -e "CREATE DATABASE trustphone_ci CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Migrations + données de démo
php artisan migrate --seed

# 8. Lier le stockage
php artisan storage:link

# 9. Assets (déjà compilés dans public/build/)
# Si vous voulez recompiler :
npm run build

# 10. Queue worker (emails/WhatsApp)
php artisan queue:work --daemon &

# 11. Démarrer
php artisan serve
```

### Accès
| URL | Description |
|-----|-------------|
| http://localhost:8000 | Boutique TrustPhone CI |
| http://localhost:8000/admin | Panel admin Filament |

### Comptes de test
| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@trustphone-ci.com | password |
| Client | client@trustphone-ci.com | password |

---

## 📧 Configuration email (Resend — gratuit)

1. Compte sur [resend.com](https://resend.com)
2. Ajouter et vérifier votre domaine
3. Générer une clé API
4. Dans `.env` :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="commandes@trustphone-ci.com"
```

---

## 💳 Paiement

**Paiement à la réception uniquement** — le client paie en espèces à la livraison.

Pour ajouter le Mobile Money ultérieurement :
- [CinetPay](https://cinetpay.com) — leader CI (~1.5%)
- [Paydunya](https://paydunya.com) — API Laravel simple

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

**Important** : ne jamais commiter le fichier `public/hot` (créé par `npm run dev`).
