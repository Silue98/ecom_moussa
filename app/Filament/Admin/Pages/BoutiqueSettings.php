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
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.boutique-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'shop_name','shop_address','shop_city','shop_phone',
            'shop_hours','shop_gmaps_url',
            'shop_latitude','shop_longitude',   // ← GÉOLOCALISATION
            'pickup_enabled','pickup_message',
            'credit_enabled','credit_message','credit_conditions',
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            // ── Informations boutique ─────────────────────────────────
            Forms\Components\Section::make('🏪 Informations de la boutique')
                ->description('Ces informations s\'affichent sur le site et dans les emails.')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('shop_name')
                            ->label('Nom de la boutique')
                            ->placeholder('Phone Store CI')
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
                        ->label('Lien Google Maps (bouton de redirection)')
                        ->helperText('Lien de partage depuis Google Maps — utilisé pour le bouton "Ouvrir dans Google Maps"')
                        ->placeholder('https://maps.google.com/?q=...')
                        ->url()
                        ->columnSpanFull(),
                ]),

            // ── Géolocalisation ───────────────────────────────────────
            Forms\Components\Section::make('📍 Géolocalisation — Carte interactive')
                ->description('Ces coordonnées affichent votre boutique sur une carte interactive visible par les clients sur le site.')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('shop_latitude')
                            ->label('Latitude')
                            ->placeholder('Ex : 5.3041')
                            ->helperText('Exemple Treichville : 5.3041'),
                        Forms\Components\TextInput::make('shop_longitude')
                            ->label('Longitude')
                            ->placeholder('Ex : -4.0024')
                            ->helperText('Exemple Treichville : -4.0024'),
                    ]),
                    Forms\Components\Placeholder::make('geo_help')
                        ->label('📌 Comment trouver mes coordonnées GPS ?')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:14px;font-size:14px;color:#166534;line-height:1.8;">
                                <strong>Étapes simples :</strong><br>
                                1. Ouvrez <a href="https://maps.google.com" target="_blank" style="color:#16a34a;font-weight:600;text-decoration:underline;">Google Maps</a> dans votre navigateur<br>
                                2. Recherchez l\'adresse exacte de votre boutique<br>
                                3. Faites un <strong>clic droit</strong> sur le point exact sur la carte<br>
                                4. Les coordonnées apparaissent tout en haut du menu (ex: <code style="background:#dcfce7;padding:1px 5px;border-radius:4px;">5.3041, -4.0024</code>)<br>
                                5. Cliquez dessus pour les copier, puis collez-les dans les champs ci-dessus
                            </div>
                        '))
                        ->columnSpanFull(),
                ]),

            // ── Retrait en boutique ───────────────────────────────────
            Forms\Components\Section::make('🚶 Retrait en boutique')
                ->description('Activez pour permettre aux clients de venir récupérer leur commande.')
                ->schema([
                    Forms\Components\Toggle::make('pickup_enabled')
                        ->label('Activer le retrait en boutique')
                        ->helperText('Si désactivé, l\'option retrait ne s\'affiche plus sur le site'),
                    Forms\Components\Textarea::make('pickup_message')
                        ->label('Message affiché aux clients')
                        ->rows(3)
                        ->placeholder('Venez récupérer votre commande directement en boutique. Gratuit et disponible sous 24h.'),
                ]),

            // ── Achat à crédit ────────────────────────────────────────
            Forms\Components\Section::make('💳 Achat à crédit')
                ->description('Activez pour afficher la bannière crédit sur le site.')
                ->schema([
                    Forms\Components\Toggle::make('credit_enabled')
                        ->label('Activer l\'achat à crédit')
                        ->helperText('Si désactivé, la bannière crédit disparaît du site'),
                    Forms\Components\Textarea::make('credit_message')
                        ->label('Message principal')
                        ->rows(3)
                        ->placeholder('Repartez aujourd\'hui avec votre smartphone et payez en plusieurs fois.'),
                    Forms\Components\Textarea::make('credit_conditions')
                        ->label('Conditions du crédit')
                        ->rows(3)
                        ->placeholder('CNI requise. Acompte minimum. Durée selon accord.'),
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
                        in_array($key, ['credit_enabled','credit_message','credit_conditions'])     => 'credit',
                        in_array($key, ['shop_latitude','shop_longitude','shop_gmaps_url'])         => 'geo',
                        default                                                                      => 'boutique',
                    },
                ]
            );
        }

        // Vider le cache des settings
        \Illuminate\Support\Facades\Cache::forget('app_settings');

        Notification::make()
            ->title('✅ Paramètres sauvegardés !')
            ->success()
            ->send();
    }
}
