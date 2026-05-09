<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class BoutiqueSettings extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?string $navigationLabel = 'Boutique & Crédit';
    protected static ?string $title           = 'Boutique & Crédit';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.boutique-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            // Général
            'site_name','site_email','currency',
            'free_shipping_threshold','shipping_price',
            // Boutique
            'shop_name','shop_address','shop_city','shop_phone',
            'shop_hours','shop_gmaps_url','shop_latitude','shop_longitude',
            'pickup_enabled','pickup_message',
            // Crédit — général
            'credit_enabled','credit_message','credit_conditions','credit_documents',
            // Crédit — ruban défilant
            'credit_ticker_text',
            // Crédit — Groupe A
            'credit_groupe_a_keywords','credit_groupe_a_acompte',
            // Crédit — Groupe B
            'credit_groupe_b_keywords','credit_groupe_b_acompte',
            // Crédit — mensualités
            'credit_nb_mois','credit_taux_mensuel',
        ];
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            // ── Paramètres généraux ───────────────────────────────────
            Forms\Components\Section::make('⚙️ Paramètres généraux')
                ->description('Informations globales du site et paramètres de livraison.')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Nom du site')
                            ->placeholder('TrustPhone CI')
                            ->required(),
                        Forms\Components\TextInput::make('site_email')
                            ->label('Email de contact')
                            ->email()
                            ->placeholder('commandes@trustphone-ci.com'),
                    ]),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('currency')
                            ->label('Devise')
                            ->placeholder('FCFA'),
                        Forms\Components\TextInput::make('free_shipping_threshold')
                            ->label('Livraison gratuite dès')
                            ->numeric()
                            ->suffix('FCFA')
                            ->placeholder('30000'),
                        Forms\Components\TextInput::make('shipping_price')
                            ->label('Frais de livraison')
                            ->numeric()
                            ->suffix('FCFA')
                            ->placeholder('2000'),
                    ]),
                ]),

            // ── Informations boutique ─────────────────────────────────
            Forms\Components\Section::make('🏪 Informations de la boutique')
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('shop_name')
                            ->label('Nom de la boutique')
                            ->placeholder('TrustPhone CI')
                            ->required(),
                        Forms\Components\TextInput::make('shop_phone')
                            ->label('Téléphone / WhatsApp')
                            ->placeholder('+225 07 00 00 00 00'),
                    ]),
                    Forms\Components\TextInput::make('shop_address')
                        ->label('Adresse complète')
                        ->placeholder('Quartier, Rue, Numéro — Abidjan')
                        ->columnSpanFull(),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('shop_city')
                            ->label('Ville')
                            ->placeholder('Abidjan, Côte d\'Ivoire'),
                        Forms\Components\TextInput::make('shop_hours')
                            ->label('Horaires d\'ouverture')
                            ->placeholder('Lun – Sam : 8h00 – 19h00'),
                    ]),
                    Forms\Components\TextInput::make('shop_gmaps_url')
                        ->label('Lien Google Maps')
                        ->placeholder('https://maps.google.com/?q=...')
                        ->url()
                        ->columnSpanFull(),
                ]),

            // ── Géolocalisation ───────────────────────────────────────
            Forms\Components\Section::make('📍 Géolocalisation')
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('shop_latitude')->label('Latitude')->placeholder('5.3041'),
                        Forms\Components\TextInput::make('shop_longitude')->label('Longitude')->placeholder('-4.0024'),
                    ]),
                ]),

            // ── Retrait en boutique ───────────────────────────────────
            Forms\Components\Section::make('🚶 Retrait en boutique')
                ->collapsed()
                ->schema([
                    Forms\Components\Toggle::make('pickup_enabled')->label('Activer le retrait en boutique'),
                    Forms\Components\Textarea::make('pickup_message')->label('Message affiché aux clients')->rows(3),
                ]),

            // ══════════════════════════════════════════════════════════
            // ── ACHAT À CRÉDIT ────────────────────────────────────────
            // ══════════════════════════════════════════════════════════

            // ── Activation & ruban ────────────────────────────────────
            Forms\Components\Section::make('💳 Crédit — Activation & Ruban défilant')
                ->description('Activez le crédit et personnalisez le ruban doré qui défile sur tout le site.')
                ->collapsed()
                ->schema([
                    Forms\Components\Toggle::make('credit_enabled')
                        ->label('Activer l\'achat à crédit sur le site')
                        ->helperText('Si désactivé, tout le volet crédit disparaît du site (ruban, bannière, blocs produit).')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('credit_ticker_text')
                        ->label('✨ Texte du ruban défilant (doré)')
                        ->rows(2)
                        ->placeholder('💳 Achetez à crédit · 40% d\'acompte · 12 mensualités · 📱 iPhone XR → 15 Pro · Repartez aujourd\'hui !')
                        ->helperText('Séparez les items par " · " (point centré). Le ruban tourne en boucle sur toutes les pages. Laissez vide pour désactiver le ruban.')
                        ->columnSpanFull(),
                ]),

            // ── Groupe A ──────────────────────────────────────────────
            Forms\Components\Section::make('📱 Groupe A — iPhone XR → 15 Pro')
                ->description('Définissez les mots-clés et l\'acompte pour les modèles d\'entrée/milieu de gamme.')
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('credit_groupe_a_keywords')
                        ->label('Mots-clés des modèles Groupe A')
                        ->rows(2)
                        ->placeholder('xr,11,12,13,14,15 pro')
                        ->helperText(new \Illuminate\Support\HtmlString(
                            '<strong>Important :</strong> Séparez par des virgules. Respectez les espaces : "15 pro" est différent de "15pro". ' .
                            'Le code teste ces mots dans le nom du produit (insensible à la casse).<br>' .
                            '<em>Exemple : si votre produit s\'appelle "iPhone 15 Pro 128Go", mettez "15 pro" (pas "15promax").</em>'
                        ))
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('credit_groupe_a_acompte')
                        ->label('Acompte Groupe A (%)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(99)
                        ->default(40)
                        ->suffix('%')
                        ->helperText('Actuellement : 40% — Le client paie ce % aujourd\'hui en boutique.')
                        ->required(),

                    Forms\Components\Placeholder::make('exemple_a')
                        ->label('📌 Exemple de calcul Groupe A')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:12px;font-size:13px;color:#92400e;line-height:1.9;">
                                <strong>iPhone 15 Pro à 390 000 FCFA, acompte 40% :</strong><br>
                                • Acompte aujourd\'hui = 390 000 × 40% = <strong>156 000 FCFA</strong><br>
                                • Reste = 390 000 − 156 000 = 234 000 FCFA<br>
                                • Mensualité = 234 000 × 1,5 ÷ 12 = <strong>29 250 FCFA/mois</strong><br>
                                • Total remboursé = 156 000 + (29 250 × 12) = <strong>507 000 FCFA</strong>
                            </div>
                        '))
                        ->columnSpanFull(),
                ]),

            // ── Groupe B ──────────────────────────────────────────────
            Forms\Components\Section::make('📱 Groupe B — iPhone 15 Pro Max → 17 Pro Max')
                ->description('Définissez les mots-clés et l\'acompte pour les modèles haut de gamme.')
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('credit_groupe_b_keywords')
                        ->label('Mots-clés des modèles Groupe B')
                        ->rows(2)
                        ->placeholder('15 pro max,16,17')
                        ->helperText(new \Illuminate\Support\HtmlString(
                            '<strong>⚠️ Ces mots-clés sont testés EN PREMIER</strong> (avant le Groupe A) car ils sont plus spécifiques.<br>' .
                            'Exemple : "15 pro max" doit être ici pour ne pas être capté par "15 pro" du Groupe A.'
                        ))
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('credit_groupe_b_acompte')
                        ->label('Acompte Groupe B (%)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(99)
                        ->default(50)
                        ->suffix('%')
                        ->helperText('Actuellement : 50% — Le client paie ce % aujourd\'hui en boutique.')
                        ->required(),

                    Forms\Components\Placeholder::make('exemple_b')
                        ->label('📌 Exemple de calcul Groupe B')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:12px;font-size:13px;color:#92400e;line-height:1.9;">
                                <strong>iPhone 17 Pro Max à 900 000 FCFA, acompte 50% :</strong><br>
                                • Acompte aujourd\'hui = 900 000 × 50% = <strong>450 000 FCFA</strong><br>
                                • Reste = 900 000 − 450 000 = 450 000 FCFA<br>
                                • Mensualité = 450 000 × 1,5 ÷ 12 = <strong>56 250 FCFA/mois</strong><br>
                                • Total remboursé = 450 000 + (56 250 × 12) = <strong>1 125 000 FCFA</strong>
                            </div>
                        '))
                        ->columnSpanFull(),
                ]),

            // ── Mensualités ───────────────────────────────────────────
            Forms\Components\Section::make('📅 Mensualités — règles communes aux 2 groupes')
                ->description('Ces règles s\'appliquent aux deux groupes. La formule est : (Prix − Acompte) × Taux ÷ Nb de mois.')
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('credit_nb_mois')
                            ->label('Nombre de mensualités')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(24)
                            ->default(12)
                            ->suffix('mois')
                            ->helperText('Actuellement : 12 mois')
                            ->required(),

                        Forms\Components\TextInput::make('credit_taux_mensuel')
                            ->label('Taux appliqué au reste')
                            ->numeric()
                            ->minValue(1)
                            ->step(0.1)
                            ->default(1.5)
                            ->suffix('× (multiplicateur)')
                            ->helperText('1,5 = le reste × 1,5 réparti sur les mois. Ce n\'est pas un % mais un multiplicateur.')
                            ->required(),
                    ]),

                    Forms\Components\Placeholder::make('formule_recap')
                        ->label('🧮 Formule complète')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div style="background:#f0f9ff;border:1px solid #7dd3fc;border-radius:8px;padding:12px;font-size:13px;color:#0c4a6e;line-height:2;">
                                <strong>Mensualité = (Prix − Acompte) × Taux ÷ Nb de mois</strong><br>
                                Avec les valeurs par défaut :<br>
                                → Mensualité = (Prix − Acompte) × <strong>1,5</strong> ÷ <strong>12</strong><br><br>
                                <em>Exemple : reste de 234 000 FCFA → 234 000 × 1,5 ÷ 12 = <strong>29 250 FCFA/mois</strong></em>
                            </div>
                        '))
                        ->columnSpanFull(),
                ]),

            // ── Textes & documents ────────────────────────────────────
            Forms\Components\Section::make('📋 Textes & Documents requis')
                ->description('Ces textes s\'affichent sur la page /achat-credit et dans les blocs produit.')
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('credit_message')
                        ->label('Message principal (optionnel)')
                        ->rows(2)
                        ->placeholder('Repartez aujourd\'hui avec votre iPhone et payez en plusieurs fois.'),
                    Forms\Components\Textarea::make('credit_conditions')
                        ->label('Conditions générales (optionnel)')
                        ->rows(2)
                        ->placeholder('Sous réserve d\'acceptation. Accord en boutique. CNI obligatoire.'),
                    Forms\Components\Textarea::make('credit_documents')
                        ->label('Documents requis (un par ligne)')
                        ->rows(4)
                        ->placeholder("Carte Nationale d'Identité (CNI) valide\nJustificatif de domicile récent\nUne photo d'identité")
                        ->helperText('Chaque ligne = un document. Affiché sur la fiche produit et la page crédit.'),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_bool($value) ? ($value ? '1' : '0') : ($value ?? ''),
                    'group' => match(true) {
                        in_array($key, [
                            'credit_enabled','credit_message','credit_conditions','credit_documents',
                            'credit_ticker_text',
                            'credit_groupe_a_keywords','credit_groupe_a_acompte',
                            'credit_groupe_b_keywords','credit_groupe_b_acompte',
                            'credit_nb_mois','credit_taux_mensuel',
                        ]) => 'credit',
                        in_array($key, ['shop_latitude','shop_longitude','shop_gmaps_url']) => 'geo',
                        in_array($key, ['site_name','site_email','currency','free_shipping_threshold','shipping_price']) => 'general',
                        default => 'boutique',
                    },
                ]
            );
        }

        \Illuminate\Support\Facades\Cache::forget('app_settings');

        Notification::make()
            ->title('✅ Paramètres sauvegardés !')
            ->success()
            ->send();
    }
}
