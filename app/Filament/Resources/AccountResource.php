<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = "heroicon-o-banknotes";

    protected static ?string $navigationGroup = "Master Data";

    protected static ?string $modelLabel = "Akun";

    protected static ?string $pluralModelLabel = "Akun";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make("name")
                    ->label("Nama Akun")
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make("type")
                    ->label("Tipe")
                    ->options([
                        "cash" => "Kas",
                        "bank" => "Bank",
                    ])
                    ->required(),
                Forms\Components\TextInput::make("balance")
                    ->label("Saldo")
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\Textarea::make("description")
                    ->label("Keterangan")
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("name")
                    ->label("Nama Akun")
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
                Tables\Columns\TextColumn::make("created_at")
                    ->label("Dibuat Pada")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("type")
                    ->label("Tipe")
                    ->options([
                        "cash" => "Kas",
                        "bank" => "Bank",
                    ]),
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
            "index" => Pages\ManageAccounts::route("/"),
        ];
    }
}
