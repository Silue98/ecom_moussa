<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?string $navigationLabel = 'Produits';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Produit')->tabs([

                // ── Onglet 1 : Informations générales ─────────────────────
                Forms\Components\Tabs\Tab::make('Informations générales')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du produit')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie')
                            ->options(Category::where('is_active', true)->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('brand_id')
                            ->label('Marque')
                            ->options(Brand::where('is_active', true)->pluck('name', 'id'))
                            ->searchable(),
                    ]),
                    Forms\Components\Textarea::make('short_description')
                        ->label('Description courte')
                        ->rows(3),
                    Forms\Components\RichEditor::make('description')
                        ->label('Description complète')
                        ->columnSpanFull(),
                ]),

                // ── Onglet 2 : Prix, Remise & Stock ───────────────────────
                Forms\Components\Tabs\Tab::make('Prix & Stock')->schema([

                    // Ligne 1 : Prix
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Prix de vente (XOF)')
                            ->required()
                            ->numeric()
                            ->prefix('XOF')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                $pct = floatval($get('discount_percent'));
                                if ($pct > 0 && $pct < 100 && floatval($state) > 0) {
                                    $set('compare_price', round(floatval($state) / (1 - $pct / 100)));
                                }
                            }),
                        Forms\Components\TextInput::make('compare_price')
                            ->label('Prix barré (XOF)')
                            ->numeric()
                            ->prefix('XOF')
                            ->helperText('Prix original affiché barré sur la fiche produit'),
                        Forms\Components\TextInput::make('cost_price')
                            ->label('Prix de revient (XOF)')
                            ->numeric()
                            ->prefix('XOF'),
                    ]),

                    // Ligne 2 : Remise
                    Forms\Components\Section::make('🏷️ Remise')
                        ->description('Entrez un pourcentage : le prix barré est calculé automatiquement.')
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('discount_percent')
                                    ->label('Remise (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->helperText('Ex : 20 pour -20%')
                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                        $price = floatval($get('price'));
                                        $pct   = floatval($state);
                                        if ($price > 0 && $pct > 0 && $pct < 100) {
                                            $set('compare_price', round($price / (1 - $pct / 100)));
                                            $set('on_sale', true);
                                        } elseif ($pct == 0) {
                                            $set('compare_price', null);
                                            $set('on_sale', false);
                                        }
                                    }),
                                Forms\Components\Placeholder::make('apercu_remise')
                                    ->label('Aperçu')
                                    ->content(function (Forms\Get $get) {
                                        $price   = floatval($get('price'));
                                        $compare = floatval($get('compare_price'));
                                        if ($compare > 0 && $compare > $price && $price > 0) {
                                            $pct      = round(($compare - $price) / $compare * 100);
                                            $economie = number_format($compare - $price, 0, ',', ' ');
                                            return "✅ -{$pct}%  ·  Économie : {$economie} XOF";
                                        }
                                        return '—  Aucune remise';
                                    }),
                                Forms\Components\Toggle::make('on_sale')
                                    ->label('Activer badge « Solde »')
                                    ->helperText("S'active automatiquement quand une remise est saisie"),
                            ]),
                        ]),

                    // Ligne 3 : Stock
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->unique(Product::class, 'sku', ignoreRecord: true),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantité en stock')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('low_stock_threshold')
                            ->label('Seuil alerte stock')
                            ->numeric()
                            ->default(5),
                    ]),
                ]),

                // ── Onglet 3 : Images ──────────────────────────────────────
                Forms\Components\Tabs\Tab::make('Images')->schema([
                    Forms\Components\Repeater::make('images')
                        ->relationship()
                        ->schema([
                            Forms\Components\FileUpload::make('image_path')
                                ->label('Image')
                                ->image()
                                ->disk('public')
                                ->directory('products')
                                ->visibility('public')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('1:1')
                                ->maxSize(2048)
                                ->required(),
                            Forms\Components\TextInput::make('alt_text')
                                ->label('Texte alternatif'),
                            Forms\Components\Toggle::make('is_main')
                                ->label('Image principale'),
                        ])
                        ->columns(3)
                        ->label('Images du produit'),
                ]),

                // ── Onglet 4 : SEO & Options ───────────────────────────────
                Forms\Components\Tabs\Tab::make('SEO & Options')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Titre SEO'),
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags'),
                    ]),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Description SEO'),
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('En vedette'),
                        Forms\Components\Toggle::make('is_new')
                            ->label('Nouveau'),
                        Forms\Components\Toggle::make('on_sale')
                            ->label('En solde'),
                    ]),
                ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('mainImage.image_path')
                    ->label('Image')
                    ->disk('public')
                    ->size(50),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('compare_price')
                    ->label('Prix barré')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', ' ') . ' XOF' : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn ($record) => $record->quantity <= $record->low_stock_threshold ? 'danger' : 'success'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Actif'),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Vedette'),
                Tables\Columns\ToggleColumn::make('on_sale')
                    ->label('Solde'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->options(Category::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')->label('Actif'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('En vedette'),
                Tables\Filters\TernaryFilter::make('on_sale')->label('En solde'),
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
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
