<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?string $navigationLabel = 'Avis clients';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->label('Produit')
                ->relationship('product', 'name')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('user_id')
                ->label('Client')
                ->relationship('user', 'name')
                ->searchable()
                ->nullable(),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('rating')
                    ->label('Note')
                    ->options([1 => '⭐ 1', 2 => '⭐⭐ 2', 3 => '⭐⭐⭐ 3', 4 => '⭐⭐⭐⭐ 4', 5 => '⭐⭐⭐⭐⭐ 5'])
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Titre'),
            ]),
            Forms\Components\Textarea::make('body')
                ->label('Commentaire')
                ->rows(4),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Toggle::make('is_approved')
                    ->label('Approuvé')
                    ->default(false),
                Forms\Components\Toggle::make('is_verified_purchase')
                    ->label('Achat vérifié')
                    ->default(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produit')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->default('Anonyme'),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->limit(40),
                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Approuvé'),
                Tables\Columns\IconColumn::make('is_verified_purchase')
                    ->label('Vérifié')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('Approuvé'),
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Note')
                    ->options([1 => '1 étoile', 2 => '2 étoiles', 3 => '3 étoiles', 4 => '4 étoiles', 5 => '5 étoiles']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'edit'  => Pages\EditReview::route('/{record}/edit'),
        ];
    }

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
