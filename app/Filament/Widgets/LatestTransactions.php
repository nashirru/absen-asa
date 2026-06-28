<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;

class LatestTransactions extends BaseTableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()->latest('date')->latest('id')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                        'transfer' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                        'transfer' => 'Transfer',
                    }),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->placeholder('Tanpa Kategori'),
                Tables\Columns\TextColumn::make('account.name')
                    ->label('Akun'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->moneyIdr()
                    ->weight('bold')
                    ->color(fn(Transaction $record): string => $record->type === 'income' ? 'success' : 'danger'),
            ])
            ->heading('5 Transaksi Terbaru')
            ->paginated(false);
    }
}
