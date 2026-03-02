<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('N° Commande')->copyable(),
                Tables\Columns\TextColumn::make('shipping_name')->label('Client'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors(['warning' => 'pending', 'info' => 'processing', 'success' => 'delivered', 'danger' => 'cancelled']),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('FCFA'),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i'),
            ])
            ->heading('Dernières commandes');
    }
}
