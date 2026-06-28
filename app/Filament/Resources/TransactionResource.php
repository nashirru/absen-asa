<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Category;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = "heroicon-o-arrows-right-left";

    protected static ?string $navigationGroup = "Transaksi";

    protected static ?string $modelLabel = "Transaksi";

    protected static ?string $pluralModelLabel = "Transaksi";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make("type")
                    ->label("Tipe")
                    ->options([
                        "income" => "Pemasukan",
                        "expense" => "Pengeluaran",
                    ])
                    ->live()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set("category_id", null)),
                Forms\Components\Select::make("account_id")
                    ->label("Akun")
                    ->relationship("account", "name")
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make("category_id")
                    ->label("Kategori")
                    ->options(fn (Forms\Get $get) => Category::where("type", $get("type"))
                        ->pluck("name", "id"))
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make("amount")
                    ->label("Jumlah")
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\DatePicker::make("date")
                    ->label("Tanggal")
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make("description")
                    ->label("Keterangan")
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make("attachment")
                    ->label("Lampiran")
                    ->disk("public")
                    ->directory("transactions")
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("type")
                    ->label("Tipe")
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        "income" => "success",
                        "expense" => "danger",
                        "transfer" => "warning",
                        default => "gray",
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        "income" => "Pemasukan",
                        "expense" => "Pengeluaran",
                        "transfer" => "Transfer",
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make("amount")
                    ->label("Jumlah")
                    ->moneyIdr()
                    ->sortable(),
                Tables\Columns\TextColumn::make("account.name")
                    ->label("Akun")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make("category.name")
                    ->label("Kategori")
                    ->searchable(),
                Tables\Columns\TextColumn::make("date")
                    ->label("Tanggal")
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make("description")
                    ->label("Keterangan")
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make("attachment")
                    ->label("Lampiran")
                    ->boolean()
                    ->toggleable(),
            ])
            ->defaultSort("date", "desc")
            ->filters([
                Tables\Filters\SelectFilter::make("type")
                    ->label("Tipe")
                    ->options([
                        "income" => "Pemasukan",
                        "expense" => "Pengeluaran",
                    ]),
                Tables\Filters\SelectFilter::make("account_id")
                    ->label("Akun")
                    ->relationship("account", "name")
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make("category_id")
                    ->label("Kategori")
                    ->relationship("category", "name")
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make("date")
                    ->label("Tanggal")
                    ->form([
                        Forms\Components\DatePicker::make("date_from")
                            ->label("Dari Tanggal"),
                        Forms\Components\DatePicker::make("date_until")
                            ->label("Sampai Tanggal"),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data["date_from"], fn ($q, $date) => $q->where("date", ">=", $date))
                        ->when($data["date_until"], fn ($q, $date) => $q->where("date", "<=", $date))
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (\App\Models\Transaction $record) => $record->ref_payroll_id === null),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (\App\Models\Transaction $record) => $record->ref_payroll_id === null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $hasPayroll = $records->contains(fn ($record) => $record->ref_payroll_id !== null);
                            if ($hasPayroll) {
                                \Filament\Notifications\Notification::make()
                                    ->title("Gagal menghapus")
                                    ->body("Terdapat transaksi penggajian yang terpilih. Transaksi penggajian tidak dapat dihapus.")
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $records->each->delete();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ManageTransactions::route("/"),
        ];
    }
}
