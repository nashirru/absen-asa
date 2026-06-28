<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;

class AccountBalanceList extends BaseTableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = "full";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Account::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make("name")
                    ->label("Akun")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make("type")
                    ->label("Tipe")
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        "cash" => "success",
                        "bank" => "info",
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        "cash" => "Kas",
                        "bank" => "Bank",
                    }),
                Tables\Columns\TextColumn::make("balance")
                    ->label("Saldo")
                    ->moneyIdr()
                    ->sortable(),
                Tables\Columns\TextColumn::make("description")
                    ->label("Keterangan")
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->heading("Saldo Per Akun")
            ->paginated(false);
    }
}
