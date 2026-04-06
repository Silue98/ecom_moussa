<?php

namespace App\Filament\Admin\Widgets;

use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProducts extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->select(
                        // On garde l'id pour que Filament ait une clé primaire valide
                        \DB::raw('MAX(id) as id'),
                        'product_id',
                        'name',
                        \DB::raw('SUM(quantity) as total_qty'),
                        \DB::raw('SUM(total) as total_revenue')
                    )
                    ->groupBy('product_id', 'name')
                    ->orderByDesc('total_qty')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Produit')
                    ->limit(30),
                Tables\Columns\TextColumn::make('total_qty')
                    ->label('Vendus')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('CA')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' FCFA'),
            ])
            ->heading('🏆 Top 10 Produits Vendus')
            ->paginated(false);
    }
}
