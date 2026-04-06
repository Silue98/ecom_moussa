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
            // Crédit
            'credit_enabled','credit_message','credit_conditions',
            'credit_nb_echeances','credit_pourcentages',
            'credit_taux_interet','credit_montant_min','credit_documents',
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
                            ->placeholder('FCFA')
                            ->helperText('Symbole affiché partout sur le site'),
                        Forms\Components\TextInput::make('free_shipping_threshold')
                            ->label('Livraison gratuite dès')
                            ->numeric()
                            ->suffix('FCFA')
                            ->placeholder('30000')
                            ->helperText('Montant minimum pour la livraison offerte'),
                        Forms\Components\TextInput::make('shipping_price')
                            ->label('Frais de livraison')
                            ->numeric()
                            ->suffix('FCFA')
                            ->placeholder('2000')
                            ->helperText('Montant facturé si sous le seuil'),
                    ]),
                ]),

            // ── Informations boutique ─────────────────────────────────
            Forms\Components\Section::make('🏪 Informations de la boutique')
                ->description('Ces informations s\'affichent sur le site et dans les emails.')
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('shop_name')
                            ->label('Nom de la boutique')
                            ->placeholder('TrustPhone CI')
                            ->required(),
                        Forms\Components\TextInput::make('shop_phone')
                            ->label('Téléphone')
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
                        Forms\Components\TextInput::make('shop_latitude')
                            ->label('Latitude')
                            ->placeholder('5.3041'),
                        Forms\Components\TextInput::make('shop_longitude')
                            ->label('Longitude')
                            ->placeholder('-4.0024'),
                    ]),
                ]),

            // ── Retrait en boutique ───────────────────────────────────
            Forms\Components\Section::make('🚶 Retrait en boutique')
                ->collapsed()
                ->schema([
                    Forms\Components\Toggle::make('pickup_enabled')
                        ->label('Activer le retrait en boutique'),
                    Forms\Components\Textarea::make('pickup_message')
                        ->label('Message affiché aux clients')
                        ->rows(3),
                ]),

            // ── Achat à crédit — Activation & textes ─────────────────
            Forms\Components\Section::make('💳 Achat à crédit — Général')
                ->description('Activez et personnalisez les textes affichés sur le site.')
                ->collapsed()
                ->schema([
                    Forms\Components\Toggle::make('credit_enabled')
                        ->label('Activer l\'achat à crédit')
                        ->helperText('Si désactivé, tout le volet crédit disparaît du site'),
                    Forms\Components\Textarea::make('credit_message')
                        ->label('Message principal')
                        ->rows(2)
                        ->placeholder('Repartez aujourd\'hui avec votre iPhone et payez en plusieurs fois.'),
                    Forms\Components\Textarea::make('credit_conditions')
                        ->label('Conditions générales (optionnel)')
                        ->rows(2)
                        ->placeholder('Sous réserve d\'acceptation. Accord en boutique.'),
                ]),

            // ── Règles de l'échéancier ────────────────────────────────
            Forms\Components\Section::make('📅 Règles de l\'échéancier')
                ->description('Ces règles s\'appliquent automatiquement à TOUS les produits du site.')
                ->collapsed()
                ->schema([

                    Forms\Components\Grid::make(2)->schema([

                        Forms\Components\TextInput::make('credit_nb_echeances')
                            ->label('Nombre d\'échéances')
                            ->numeric()
                            ->minValue(2)
                            ->maxValue(12)
                            ->default(3)
                            ->suffix('versements')
                            ->helperText('Ex : 3 pour payer en 3 fois')
                            ->required(),

                        Forms\Components\TextInput::make('credit_taux_interet')
                            ->label('Taux d\'intérêt')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%')
                            ->helperText('0% = pas d\'intérêt. Ex : 5 pour 5%')
                            ->required(),
                    ]),

                    Forms\Components\TextInput::make('credit_pourcentages')
                        ->label('Pourcentage de chaque versement')
                        ->placeholder('30,40,30')
                        ->helperText('Séparez par des virgules. Le total DOIT faire 100. Ex pour 3 versements : 30,40,30 — pour 4 versements : 25,25,25,25')
                        ->required()
                        ->columnSpanFull()
                        ->rules([
                            function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    $parts = array_map('trim', explode(',', $value));
                                    $sum   = array_sum(array_map('intval', $parts));
                                    if ($sum !== 100) {
                                        $fail("La somme des pourcentages doit faire exactement 100%. Actuellement : {$sum}%");
                                    }
                                };
                            },
                        ]),

                    Forms\Components\TextInput::make('credit_montant_min')
                        ->label('Montant minimum éligible au crédit')
                        ->numeric()
                        ->default(100000)
                        ->suffix('FCFA')
                        ->helperText('Les produits en dessous de ce prix n\'affichent pas l\'option crédit')
                        ->required(),

                    Forms\Components\Placeholder::make('credit_preview')
                        ->label('📌 Rappel — Comment ça marche')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:14px;font-size:13px;color:#92400e;line-height:1.8;">
                                <strong>Exemple avec un iPhone à 1 000 000 FCFA, taux 5%, 3 versements à 30/40/30 :</strong><br>
                                • Total avec intérêts = 1 050 000 FCFA<br>
                                • 1er versement (30%) = 315 000 FCFA — <em>en boutique le jour J</em><br>
                                • 2ème versement (40%) = 420 000 FCFA — <em>mois 2</em><br>
                                • 3ème versement (30%) = 315 000 FCFA — <em>mois 3</em>
                            </div>
                        '))
                        ->columnSpanFull(),

                ]),

            // ── Documents requis ──────────────────────────────────────
            Forms\Components\Section::make('📋 Documents requis pour le crédit')
                ->description('Ces documents seront affichés sur la page de chaque produit.')
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('credit_documents')
                        ->label('Liste des documents (un par ligne)')
                        ->rows(5)
                        ->placeholder("Carte Nationale d'Identité (CNI) valide\nJustificatif de domicile récent\nUne photo d'identité")
                        ->helperText('Chaque ligne = un document. Le client verra cette liste sur la page produit.')
                        ->columnSpanFull(),
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
                            'credit_enabled','credit_message','credit_conditions',
                            'credit_nb_echeances','credit_pourcentages',
                            'credit_taux_interet','credit_montant_min','credit_documents',
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