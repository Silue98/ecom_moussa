<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use App\Services\GreenApiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class WhatsAppSettings extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?string $navigationLabel = 'WhatsApp (Green API)';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.pages.whatsapp-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'greenapi_enabled',
            'greenapi_instance_id',
            'greenapi_api_token',
            'greenapi_default_country_code',
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('📱 Configuration Green API')
                ->description('Green API permet d\'envoyer des messages WhatsApp automatiques depuis votre numéro personnel. Inscrivez-vous sur green-api.com pour obtenir vos identifiants.')
                ->schema([

                    Forms\Components\Toggle::make('greenapi_enabled')
                        ->label('Activer les notifications WhatsApp')
                        ->helperText('Si activé, un message WhatsApp sera envoyé au client après chaque commande confirmée.')
                        ->live(),

                    Forms\Components\Placeholder::make('greenapi_info')
                        ->label('📌 Comment obtenir vos identifiants Green API ?')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:14px;font-size:14px;color:#166534;line-height:1.9;">
                                <strong>Étapes :</strong><br>
                                1. Inscrivez-vous sur <a href="https://green-api.com" target="_blank" style="color:#16a34a;font-weight:600;text-decoration:underline;">green-api.com</a> (plan gratuit disponible)<br>
                                2. Créez une nouvelle instance WhatsApp<br>
                                3. Scannez le QR code avec WhatsApp sur votre téléphone pour connecter votre numéro<br>
                                4. Copiez votre <strong>Instance ID</strong> et votre <strong>API Token</strong> ci-dessous<br>
                                5. ✅ Les messages seront envoyés depuis votre numéro WhatsApp
                            </div>
                        '))
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('greenapi_instance_id')
                            ->label('Instance ID')
                            ->placeholder('Ex : 1101234567')
                            ->helperText('Disponible dans votre tableau de bord Green API')
                            ->required(fn (Forms\Get $get) => $get('greenapi_enabled')),

                        Forms\Components\TextInput::make('greenapi_api_token')
                            ->label('API Token')
                            ->placeholder('Ex : d75b3a66374942c5b3c6c7a5617b6ba9...')
                            ->helperText('Token secret de votre instance Green API')
                            ->password()
                            ->revealable()
                            ->required(fn (Forms\Get $get) => $get('greenapi_enabled')),
                    ]),

                    Forms\Components\TextInput::make('greenapi_default_country_code')
                        ->label('Indicatif pays par défaut')
                        ->placeholder('225')
                        ->helperText('Utilisé pour les numéros locaux sans indicatif (ex: 225 pour la Côte d\'Ivoire)')
                        ->default('225')
                        ->maxWidth('sm'),

                ]),

            Forms\Components\Section::make('🧪 Test d\'envoi')
                ->description('Envoyez un message test pour vérifier que votre configuration fonctionne.')
                ->schema([
                    Forms\Components\TextInput::make('test_phone')
                        ->label('Numéro de test')
                        ->placeholder('Ex : 0777664956 ou 2250777664956')
                        ->helperText('Entrez le numéro WhatsApp sur lequel envoyer le message test')
                        ->statePath(null),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Exclure le champ test_phone des settings à sauvegarder
        $settingsData = collect($data)->except('test_phone')->toArray();

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_bool($value) ? ($value ? '1' : '0') : ($value ?? ''),
                    'group' => 'whatsapp',
                    'type'  => 'string',
                ]
            );
        }

        // Vider les caches
        Cache::forget('app_settings');

        Notification::make()
            ->title('✅ Configuration WhatsApp sauvegardée !')
            ->success()
            ->send();
    }

    public function sendTest(): void
    {
        $data  = $this->form->getState();
        $phone = $data['test_phone'] ?? null;

        if (empty($phone)) {
            Notification::make()
                ->title('⚠️ Entrez un numéro de test.')
                ->warning()
                ->send();
            return;
        }

        // Sauvegarder d'abord les settings courants
        $this->save();

        // Re-instancier le service pour prendre les nouveaux settings
        $greenApi = new GreenApiService();

        if (! $greenApi->isConfigured()) {
            Notification::make()
                ->title('❌ Green API non configuré')
                ->body('Activez Green API et renseignez l\'Instance ID et le Token avant de tester.')
                ->danger()
                ->send();
            return;
        }

        $shopName = setting('shop_name', 'Notre boutique');
        $message  = "✅ *Test Green API — {$shopName}*\n\nCeci est un message test envoyé depuis votre interface d'administration.\n\nSi vous recevez ce message, votre configuration WhatsApp fonctionne correctement ! 🎉";

        $sent = $greenApi->sendMessage($phone, $message);

        if ($sent) {
            Notification::make()
                ->title('✅ Message test envoyé !')
                ->body("Le message a été envoyé au numéro {$phone}.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('❌ Échec de l\'envoi')
                ->body('Vérifiez votre Instance ID, Token et que votre session WhatsApp est bien connectée sur green-api.com.')
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
