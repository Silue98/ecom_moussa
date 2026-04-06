<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Ventes';
    protected static ?string $navigationLabel = 'Commandes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Statut de la commande')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('order_number')->label('N° Commande')->disabled(),
                    Forms\Components\Select::make('status')
                        ->label('Statut livraison')
                        ->options([
                            'pending'    => '⏳ En attente',
                            'processing' => '⚙️ En traitement',
                            'shipped'    => '🚚 Expédié',
                            'delivered'  => '✅ Livré',
                            'cancelled'  => '❌ Annulé',
                            'refunded'   => '↩️ Remboursé',
                        ])->required(),
                    Forms\Components\Select::make('payment_status')
                        ->label('Statut paiement')
                        ->options([
                            'pending'  => '⏳ En attente',
                            'paid'     => '✅ Payé',
                            'failed'   => '❌ Échoué',
                            'refunded' => '↩️ Remboursé',
                        ])->required(),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('tracking_number')->label('Numéro de suivi'),
                    Forms\Components\Select::make('payment_method')
                        ->label('Mode de paiement')
                        ->options([
                            'cod'           => 'Paiement à la livraison',
                            'card'          => 'Carte bancaire',
                            'bank_transfer' => 'Virement bancaire',
                        ]),
                ]),
                Forms\Components\Textarea::make('admin_notes')->label('Notes internes')->rows(3),
            ]),
            Forms\Components\Section::make('Adresse de livraison')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('shipping_name')->label('Nom complet'),
                    Forms\Components\TextInput::make('shipping_email')->label('Email'),
                    Forms\Components\TextInput::make('shipping_phone')->label('Téléphone'),
                    Forms\Components\TextInput::make('shipping_address')->label('Adresse'),
                    Forms\Components\TextInput::make('shipping_city')->label('Ville'),
                    Forms\Components\TextInput::make('shipping_zip')->label('Code postal'),
                ]),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Résumé')->schema([
                Infolists\Components\Grid::make(4)->schema([
                    Infolists\Components\TextEntry::make('order_number')->label('N° Commande')->copyable()->weight('bold'),
                    Infolists\Components\TextEntry::make('status')->label('Statut')->badge()
                        ->color(fn ($state) => match($state) {
                            'pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary',
                            'delivered' => 'success', 'cancelled' => 'danger', default => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => match($state) {
                            'pending' => '⏳ En attente', 'processing' => '⚙️ Traitement',
                            'shipped' => '🚚 Expédié', 'delivered' => '✅ Livré',
                            'cancelled' => '❌ Annulé', 'refunded' => '↩️ Remboursé', default => $state,
                        }),
                    Infolists\Components\TextEntry::make('payment_status')->label('Paiement')->badge()
                        ->color(fn ($state) => match($state) {
                            'paid' => 'success', 'pending' => 'warning', 'failed' => 'danger', default => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => match($state) {
                            'pending' => '⏳ En attente', 'paid' => '✅ Payé',
                            'failed' => '❌ Échoué', 'refunded' => '↩️ Remboursé', default => $state,
                        }),
                    Infolists\Components\TextEntry::make('delivery_type')->label('Mode de livraison')
                        ->formatStateUsing(fn ($state) => match($state) {
                            'pickup'   => '🏪 Retrait en boutique',
                            'delivery' => '🚚 Livraison à domicile',
                            default    => ucfirst($state ?? '-'),
                        })->badge()->color(fn ($state) => $state === 'pickup' ? 'warning' : 'info'),
                    Infolists\Components\TextEntry::make('payment_method')->label('Mode paiement')
                        ->formatStateUsing(fn ($state) => match($state) {
                            'cod' => '💵 Livraison', 'card' => '💳 Carte', 'bank_transfer' => '🏦 Virement', default => $state,
                        }),
                ]),
                Infolists\Components\Grid::make(5)->schema([
                    Infolists\Components\TextEntry::make('subtotal')->label('Sous-total')->money('FCFA'),
                    Infolists\Components\TextEntry::make('discount_amount')->label('Réduction')->money('FCFA'),
                    Infolists\Components\TextEntry::make('tax_amount')->label('TVA')->money('FCFA'),
                    Infolists\Components\TextEntry::make('shipping_amount')->label('Livraison')->money('FCFA'),
                    Infolists\Components\TextEntry::make('total')->label('TOTAL')->money('FCFA')->weight('bold')->size('lg'),
                ]),
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('tracking_number')->label('N° Suivi')->placeholder('Non défini')->copyable(),
                    Infolists\Components\TextEntry::make('created_at')->label('Passée le')->dateTime('d/m/Y à H:i'),
                ]),
            ]),

            Infolists\Components\Section::make('🛍️ Articles commandés')->schema([
                Infolists\Components\RepeatableEntry::make('items')->label('')->schema([
                    Infolists\Components\Grid::make(5)->schema([
                        Infolists\Components\TextEntry::make('name')->label('Produit')->weight('semibold'),
                        Infolists\Components\TextEntry::make('sku')->label('SKU')->placeholder('-'),
                        Infolists\Components\TextEntry::make('quantity')->label('Qté')->alignCenter(),
                        Infolists\Components\TextEntry::make('price')->label('Prix unit.')->money('FCFA'),
                        Infolists\Components\TextEntry::make('total')->label('Total ligne')->money('FCFA')->weight('bold'),
                    ]),
                ]),
            ]),

            Infolists\Components\Grid::make(2)->schema([
                Infolists\Components\Section::make('📦 Livraison')->schema([
                    Infolists\Components\TextEntry::make('shipping_name')->label('Nom'),
                    Infolists\Components\TextEntry::make('shipping_email')->label('Email'),
                    Infolists\Components\TextEntry::make('shipping_phone')->label('Tél')->placeholder('-'),
                    Infolists\Components\TextEntry::make('shipping_address')->label('Adresse'),
                    Infolists\Components\TextEntry::make('shipping_city')->label('Ville'),
                    Infolists\Components\TextEntry::make('shipping_zip')->label('Code postal'),
                    Infolists\Components\TextEntry::make('shipping_country')->label('Pays'),
                ]),
                Infolists\Components\Section::make('💳 Facturation')->schema([
                    Infolists\Components\TextEntry::make('billing_name')->label('Nom')->placeholder('Idem livraison'),
                    Infolists\Components\TextEntry::make('billing_email')->label('Email')->placeholder('-'),
                    Infolists\Components\TextEntry::make('billing_address')->label('Adresse')->placeholder('-'),
                    Infolists\Components\TextEntry::make('billing_city')->label('Ville')->placeholder('-'),
                    Infolists\Components\TextEntry::make('billing_zip')->label('Code postal')->placeholder('-'),
                    Infolists\Components\TextEntry::make('billing_country')->label('Pays')->placeholder('-'),
                ]),
            ]),

            Infolists\Components\Section::make('📝 Notes')->schema([
                Infolists\Components\TextEntry::make('notes')->label('Notes client')->placeholder('Aucune'),
                Infolists\Components\TextEntry::make('admin_notes')->label('Notes internes')->placeholder('Aucune'),
            ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('N° Commande')->searchable()->copyable()->weight('bold'),
                Tables\Columns\TextColumn::make('user.name')->label('Client')->searchable()->default('Invité'),
                Tables\Columns\BadgeColumn::make('status')->label('Statut')
                    ->colors([
                        'warning' => 'pending', 'info' => 'processing', 'primary' => 'shipped',
                        'success' => 'delivered', 'danger' => 'cancelled', 'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => '⏳ En attente', 'processing' => '⚙️ Traitement',
                        'shipped' => '🚚 Expédié', 'delivered' => '✅ Livré',
                        'cancelled' => '❌ Annulé', 'refunded' => '↩️ Remboursé', default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('payment_status')->label('Paiement')
                    ->colors(['warning' => 'pending', 'success' => 'paid', 'danger' => 'failed', 'gray' => 'refunded'])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => '⏳ Attente', 'paid' => '✅ Payé', 'failed' => '❌ Échoué', default => $state,
                    }),
                Tables\Columns\TextColumn::make('items_count')->label('Articles')->counts('items')->alignCenter(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('FCFA')->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')
                    ->options(['pending' => 'En attente', 'processing' => 'Traitement', 'shipped' => 'Expédié', 'delivered' => 'Livré', 'cancelled' => 'Annulé']),
                Tables\Filters\SelectFilter::make('payment_status')->label('Paiement')
                    ->options(['pending' => 'En attente', 'paid' => 'Payé', 'failed' => 'Échoué']),
                Tables\Filters\Filter::make('today')->label("Aujourd'hui")
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
                Tables\Filters\Filter::make('this_week')->label('Cette semaine')
                    ->query(fn ($query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Détails'),
                Tables\Actions\EditAction::make()->label('Modifier'),
                Tables\Actions\Action::make('mark_processing')
                    ->label('En traitement')->icon('heroicon-o-arrow-path')->color('info')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'processing'])),
                Tables\Actions\Action::make('mark_shipped')
                    ->label('Expédier')->icon('heroicon-o-truck')->color('primary')
                    ->visible(fn ($record) => $record->status === 'processing')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'shipped', 'shipped_at' => now()])),
                Tables\Actions\Action::make('mark_delivered')
                    ->label('Livré')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn ($record) => $record->status === 'shipped')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'delivered', 'delivered_at' => now()])),
                Tables\Actions\Action::make('cancel')
                    ->label('Annuler')->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'processing']))
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'cancelled'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_processing')->label('Passer en traitement')
                        ->requiresConfirmation()->action(fn ($records) => $records->each->update(['status' => 'processing'])),
                    Tables\Actions\BulkAction::make('mark_shipped')->label('Marquer expédiés')
                        ->requiresConfirmation()->action(fn ($records) => $records->each->update(['status' => 'shipped', 'shipped_at' => now()])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view'  => Pages\ViewOrder::route('/{record}'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }
}
