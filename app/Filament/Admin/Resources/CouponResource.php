<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $navigationLabel = 'Coupons';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code coupon')
                    ->required()
                    ->unique(Coupon::class, 'code', ignoreRecord: true)
                    ->hint('Majuscules recommandées'),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options(['percentage' => 'Pourcentage (%)', 'fixed' => 'Montant fixe (FCFA)'])
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->label('Valeur')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('min_order_amount')
                    ->label('Montant minimum')
                    ->numeric(),
                Forms\Components\TextInput::make('max_discount')
                    ->label('Remise maximum')
                    ->numeric(),
                Forms\Components\TextInput::make('usage_limit')
                    ->label('Limite d\'utilisation')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('starts_at')->label('Date début'),
                Forms\Components\DateTimePicker::make('expires_at')->label('Date expiration'),
                Forms\Components\Textarea::make('description')->label('Description'),
                Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Code')->searchable()->copyable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state === 'percentage' ? 'Pourcentage' : 'Fixe'),
                Tables\Columns\TextColumn::make('value')->label('Valeur')
                    ->formatStateUsing(fn ($record) => $record->type === 'percentage' ? $record->value . '%' : $record->value . ' FCFA'),
                Tables\Columns\TextColumn::make('used_count')->label('Utilisations'),
                Tables\Columns\TextColumn::make('expires_at')->label('Expiration')->dateTime('d/m/Y'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Actif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
