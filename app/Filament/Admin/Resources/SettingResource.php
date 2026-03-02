<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?string $navigationLabel = 'Paramètres du site';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Clé')
                    ->required()
                    ->unique(Setting::class, 'key', ignoreRecord: true)
                    ->disabled(fn ($context) => $context === 'edit'),
                Forms\Components\Select::make('group')
                    ->label('Groupe')
                    ->options([
                        'general'  => 'Général',
                        'shop'     => 'Boutique',
                        'mail'     => 'Email',
                        'social'   => 'Réseaux sociaux',
                        'seo'      => 'SEO',
                    ])
                    ->default('general')
                    ->required(),
            ]),
            Forms\Components\Textarea::make('value')
                ->label('Valeur')
                ->rows(3),
            Forms\Components\Select::make('type')
                ->label('Type')
                ->options([
                    'string'  => 'Texte',
                    'integer' => 'Entier',
                    'boolean' => 'Booléen',
                    'json'    => 'JSON',
                ])
                ->default('string'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Clé')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('group')
                    ->label('Groupe')
                    ->colors([
                        'primary' => 'general',
                        'success' => 'shop',
                        'warning' => 'mail',
                        'info'    => 'social',
                    ]),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valeur')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Groupe')
                    ->options([
                        'general' => 'Général',
                        'shop'    => 'Boutique',
                        'mail'    => 'Email',
                        'social'  => 'Réseaux sociaux',
                        'seo'     => 'SEO',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('group');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit'   => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
