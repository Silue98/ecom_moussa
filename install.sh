#!/bin/bash

# Script d'installation automatique
# Usage: bash install.sh

set -e

echo "🛍️ Installation E-Commerce Laravel 12"
echo "======================================"

# Check PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP non trouvé. Installez PHP 8.2+"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✅ PHP $PHP_VERSION détecté"

# Check Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer non trouvé. Installez Composer"
    exit 1
fi
echo "✅ Composer détecté"

# Check Node
if ! command -v node &> /dev/null; then
    echo "❌ Node.js non trouvé. Installez Node.js 18+"
    exit 1
fi
echo "✅ Node.js $(node -v) détecté"

echo ""
echo "📦 Installation des dépendances PHP..."
composer install --no-interaction --prefer-dist

echo ""
echo "📦 Installation des dépendances JavaScript..."
npm install

echo ""
echo "⚙️  Configuration de l'environnement..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
    echo "✅ Fichier .env créé"
else
    echo "ℹ️  Fichier .env existant conservé"
fi

echo ""
echo "🗃️  Configuration de la base de données..."
echo "Entrez vos informations MySQL:"
read -p "Host [127.0.0.1]: " DB_HOST
DB_HOST=${DB_HOST:-127.0.0.1}
read -p "Database [ecommerce_db]: " DB_DATABASE
DB_DATABASE=${DB_DATABASE:-ecommerce_db}
read -p "Username [root]: " DB_USERNAME
DB_USERNAME=${DB_USERNAME:-root}
read -s -p "Password: " DB_PASSWORD
echo ""

# Update .env
sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env

echo ""
echo "🔨 Migration de la base de données..."
php artisan migrate --seed --force

echo ""
echo "🔗 Création du lien de stockage..."
php artisan storage:link

echo ""
echo "🏗️  Compilation des assets..."
npm run build

echo ""
echo "======================================"
echo "✅ Installation terminée !"
echo ""
echo "🚀 Démarrez le serveur:"
echo "   php artisan serve"
echo ""
echo "🌐 Accès:"
echo "   Boutique:     http://localhost:8000"
echo "   Administration: http://localhost:8000/admin"
echo ""
echo "👤 Comptes:"
echo "   Admin:  admin@ecommerce.ma / password"
echo "   Client: client@example.com / password"
echo ""
echo "🎁 Codes promo: BIENVENUE10, SOLDES20, LIVRAISON"
