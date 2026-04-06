#!/bin/bash
# ════════════════════════════════════════════════════
#  Script de déploiement — E-Commerce Trust phone CI
#  Usage : bash deploy.sh
# ════════════════════════════════════════════════════

set -e  # Arrêter si une commande échoue

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

info()    { echo -e "${BLUE}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC}   $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERREUR]${NC} $1"; exit 1; }

echo ""
echo "════════════════════════════════════════"
echo "  Déploiement E-Commerce Trust phone CI "
echo "════════════════════════════════════════"
echo ""

# ── Vérifications préalables ──────────────────────
info "Vérification de l'environnement..."

[ ! -f ".env" ] && error "Fichier .env introuvable. Copiez .env.production en .env et configurez-le."

APP_ENV=$(grep "^APP_ENV=" .env | cut -d= -f2)
APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d= -f2)
APP_URL=$(grep "^APP_URL=" .env | cut -d= -f2)

[ "$APP_ENV" != "production" ] && error "APP_ENV doit être 'production' dans .env"
[ "$APP_DEBUG" != "false" ]   && error "APP_DEBUG doit être 'false' dans .env"
[[ "$APP_URL" == *"localhost"* ]] && error "APP_URL contient encore 'localhost'. Mettez votre vrai domaine."
[[ "$APP_URL" == *"<"* ]]     && error "APP_URL n'a pas été configuré (contient des < >)."

success "Environnement validé (production)"

# ── Dépendances PHP ────────────────────────────────
info "Installation des dépendances PHP (production)..."
composer install --no-dev --optimize-autoloader --no-interaction
success "Composer OK"

# ── Migrations ─────────────────────────────────────
info "Exécution des migrations..."
php artisan migrate --force
success "Migrations OK"

# ── Permissions storage ────────────────────────────
info "Configuration des permissions..."
chmod -R 775 storage bootstrap/cache
success "Permissions OK"

# ── Optimisations Laravel ──────────────────────────
info "Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
success "Cache Laravel OK"

# ── Assets front-end ───────────────────────────────
if [ -f "package.json" ]; then
    info "Compilation des assets front-end..."
    npm ci --silent
    npm run build
    success "Assets compilés"
fi

# ── Vider le cache applicatif ──────────────────────
info "Nettoyage du cache applicatif..."
php artisan cache:clear
success "Cache applicatif vidé"

# ── Vérification queue ─────────────────────────────
echo ""
warn "RAPPEL : La queue doit tourner en arrière-plan."
echo "  Configurez Supervisor avec cette commande :"
echo "  php artisan queue:work --sleep=3 --tries=3 --timeout=90"
echo ""

echo "════════════════════════════════════════"
success "Déploiement terminé avec succès ! 🚀"
echo "════════════════════════════════════════"
echo ""
