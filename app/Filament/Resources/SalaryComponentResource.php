<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryComponentResource\Pages;
use App\Models\SalaryComponent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalaryComponentResource extends Resource
{
    protected static ?string $model = SalaryComponent::class;

    protected static ?string $navigationIcon = "heroicon-o-calculator";

    protected static ?string $navigationGroup = "Master Data";

    protected static ?string $modelLabel = "Komponen Gaji";

    protected static ?string $pluralModelLabel = "Komponen Gaji";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make("employee_id")
                    ->label("Karyawan")
                    ->relationship("employee", "name")
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make("name")
                    ->label("Nama Komponen")
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make("type")
                    ->label("Tipe")
                    ->options([
                        "allowance" => "Tunjangan",
                        "deduction" => "Potongan",
                    ])
                    ->required(),
                Forms\Components\TextInput::make("amount")
                    ->label("Jumlah")
                    ->numeric()
                    ->required()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("employee.name")
                    ->label("Karyawan")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make("name")
                    ->label("Nama Komponen")
                    ->searchable(),
                Tables\Columns\TextColumn::make("type")
                    ->label("Tipe")
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        "allowance" => "success",
                        "deduction" => "danger",
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        "allowance" => "Tunjangan",
                        "deduction" => "Potongan",
                    }),
                Tables\Columns\TextColumn::make("amount")
                    ->label("Jumlah")
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
                        "allowance" => "Tunjangan",
                        "deduction" => "Potongan",
                    ]),
                Tables\Filters\SelectFilter::make("employee_id")
                    ->label("Karyawan")
                    ->relationship("employee", "name")
                    ->searchable()
                    ->preload(),
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
            "index" => Pages\ManageSalaryComponents::route("/"),
        ];
    }
}
