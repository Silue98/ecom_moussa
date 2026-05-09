<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon  = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?string $navigationLabel = 'Avis clients';
    protected static ?int    $navigationSort  = 4;

    // ─── Formulaire ───────────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('📱 Produit & auteur')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Produit')
                        ->relationship('product', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('user_id')
                        ->label('Client (compte existant)')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->nullable()
                        ->helperText('Laisser vide si le client n\'a pas de compte.'),

                    Forms\Components\TextInput::make('reviewer_name')
                        ->label('Nom affiché (si sans compte)')
                        ->placeholder('Ex : Kouassi Aimé')
                        ->helperText('Utilisé quand le client n\'a pas de compte sur le site.')
                        ->nullable(),

                    Forms\Components\Select::make('source')
                        ->label('Origine de l\'avis')
                        ->options([
                            'site'      => '🌐 Site web',
                            'whatsapp'  => '💬 WhatsApp',
                            'boutique'  => '🏪 Boutique (oral)',
                            'importe'   => '📥 Importé',
                        ])
                        ->default('site')
                        ->required(),
                ]),

            Forms\Components\Section::make('⭐ Note & commentaire')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('rating')
                        ->label('Note')
                        ->options([
                            5 => '⭐⭐⭐⭐⭐ — Excellent',
                            4 => '⭐⭐⭐⭐ — Très bien',
                            3 => '⭐⭐⭐ — Bien',
                            2 => '⭐⭐ — Passable',
                            1 => '⭐ — Mauvais',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('title')
                        ->label('Titre de l\'avis')
                        ->placeholder('Ex : Excellent rapport qualité/prix !'),

                    Forms\Components\Textarea::make('body')
                        ->label('Commentaire')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder('Rédigez ici le commentaire du client tel qu\'il vous l\'a communiqué.')
                        ->required(),
                ]),

            Forms\Components\Section::make('🔧 Statut')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('is_approved')
                        ->label('Avis approuvé (visible en front)')
                        ->default(true)
                        ->helperText('Cocher pour publier immédiatement.'),

                    Forms\Components\Toggle::make('is_verified_purchase')
                        ->label('Achat vérifié')
                        ->default(false)
                        ->helperText('Cocher si le client a bien acheté ce produit chez vous.'),
                ]),
        ]);
    }

    // ─── Tableau ──────────────────────────────────────────────────
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produit')
                    ->searchable()
                    ->sortable()
                    ->limit(28)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('display_name')
                    ->label('Client')
                    ->getStateUsing(fn ($record) => $record->display_name)
                    ->searchable(query: fn ($query, $search) =>
                        $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%$search%"))
                              ->orWhere('reviewer_name', 'like', "%$search%")
                    ),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->limit(35)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('source')
                    ->label('Origine')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'whatsapp' => '💬 WhatsApp',
                        'boutique' => '🏪 Boutique',
                        'importe'  => '📥 Importé',
                        default    => '🌐 Site',
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'whatsapp' => 'success',
                        'boutique' => 'warning',
                        'importe'  => 'info',
                        default    => 'gray',
                    }),

                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Publié'),

                Tables\Columns\IconColumn::make('is_verified_purchase')
                    ->label('Vérifié')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Publié'),

                Tables\Filters\SelectFilter::make('rating')
                    ->label('Note')
                    ->options([5 => '5 ⭐', 4 => '4 ⭐', 3 => '3 ⭐', 2 => '2 ⭐', 1 => '1 ⭐']),

                Tables\Filters\SelectFilter::make('source')
                    ->label('Origine')
                    ->options([
                        'site'     => '🌐 Site',
                        'whatsapp' => '💬 WhatsApp',
                        'boutique' => '🏪 Boutique',
                        'importe'  => '📥 Importé',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    // ── Approuver en masse ──
                    Tables\Actions\BulkAction::make('approve_all')
                        ->label('✅ Approuver la sélection')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn ($r) => $r->update(['is_approved' => true]));
                            Notification::make()
                                ->title('Avis approuvés !')
                                ->body(count($records) . ' avis ont été publiés.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Approuver les avis sélectionnés ?')
                        ->modalDescription('Ces avis seront visibles immédiatement sur le site.'),

                    // ── Désapprouver en masse ──
                    Tables\Actions\BulkAction::make('reject_all')
                        ->label('❌ Masquer la sélection')
                        ->icon('heroicon-o-eye-slash')
                        ->color('danger')
                        ->action(fn ($records) => $records->each(fn ($r) => $r->update(['is_approved' => false])))
                        ->requiresConfirmation(),

                    // ── Marquer "achat vérifié" ──
                    Tables\Actions\BulkAction::make('verify_all')
                        ->label('✔ Marquer comme achat vérifié')
                        ->icon('heroicon-o-shield-check')
                        ->color('info')
                        ->action(fn ($records) => $records->each(fn ($r) => $r->update(['is_verified_purchase' => true]))),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                // ── Bouton "Saisir un avis client" bien visible ──
                Tables\Actions\CreateAction::make()
                    ->label('📝 Saisir un avis client')
                    ->modalHeading('Saisir un avis client (WhatsApp / boutique)')
                    ->modalDescription('Retranscrivez ici l\'avis d\'un client reçu par WhatsApp ou en boutique.')
                    ->createAnother(false),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReviews::route('/'),
            'edit'   => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    // Badge rouge = avis en attente d'approbation
    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('is_approved', false)->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
