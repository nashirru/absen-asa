<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;

class CustomDashboard extends BaseDashboard
{
    protected static ?string $title = 'Dasbor';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Ekspor PDF Dashboard')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('dashboard.pdf'))
                ->openUrlInNewTab(),
        ];
    }
}
