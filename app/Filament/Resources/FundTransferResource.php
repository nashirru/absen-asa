<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FundTransferResource\Pages;
use App\Models\Account;
use App\Models\FundTransfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FundTransferResource extends Resource
{
    protected static ?string $model = FundTransfer::class;

    protected static ?string $navigationIcon = "heroicon-o-arrows-right-left";

    protected static ?string $navigationGroup = "Transaksi";

    protected static ?string $navigationLabel = "Transfer Dana";

    protected static ?string $pluralModelLabel = "Transfer Dana";

    protected static ?string $modelLabel = "Transfer Dana";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make("from_account_id")
                    ->label("Dari Akun")
                    ->relationship("fromAccount", "name")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                Forms\Components\Select::make("to_account_id")
                    ->label("Ke Akun")
                    ->relationship("toAccount", "name")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->different("from_account_id")
                    ->validationMessages([
                        "different" => "Akun tujuan tidak boleh sama dengan akun asal.",
                    ]),
                Forms\Components\TextInput::make("amount")
                    ->label("Jumlah Transfer")
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(0)
                    ->rules([
                        fn (Forms\Get $get, ?\Illuminate\Database\Eloquent\Model $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            $fromAccountId = $get('from_account_id');
                            if (!$fromAccountId) return;
                            $fromAccount = \App\Models\Account::find($fromAccountId);
                            if (!$fromAccount) return;
                            $available = $fromAccount->balance;
                            if ($record && $record->exists && $record->from_account_id == $fromAccountId) {
                                $available += $record->amount;
                            }
                            if ($value > $available) {
                                $fail("Saldo akun asal tidak mencukupi. Saldo tersedia: Rp " . number_format($available, 0, ',', '.'));
                            }
                        }
                    ]),
                Forms\Components\DatePicker::make("date")
                    ->label("Tanggal")
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make("note")
                    ->label("Catatan")
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("fromAccount.name")
                    ->label("Dari Akun")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make("toAccount.name")
                    ->label("Ke Akun")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make("amount")
                    ->label("Jumlah")
                    ->moneyIdr()
                    ->sortable(),
                Tables\Columns\TextColumn::make("date")
                    ->label("Tanggal")
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make("note")
                    ->label("Catatan")
                    ->limit(50)
                    ->toggleable(),
            ])
            ->defaultSort("date", "desc")
            ->filters([
                Tables\Filters\SelectFilter::make("from_account_id")
                    ->label("Dari Akun")
                    ->relationship("fromAccount", "name")
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make("to_account_id")
                    ->label("Ke Akun")
                    ->relationship("toAccount", "name")
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make("date")
                    ->form([
                        Forms\Components\DatePicker::make("date_from")
                            ->label("Tanggal Dari"),
                        Forms\Components\DatePicker::make("date_until")
                            ->label("Tanggal Sampai"),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data["date_from"], fn ($q, $date) => $q->where("date", ">=", $date))
                        ->when($data["date_until"], fn ($q, $date) => $q->where("date", "<=", $date))
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ManageFundTransfers::route("/"),
        ];
    }
}