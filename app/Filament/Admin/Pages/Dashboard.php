<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Tableau de bord';
    protected static ?string $navigationLabel = 'Tableau de bord';

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
