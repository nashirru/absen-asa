<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = "heroicon-o-tag";

    protected static ?string $navigationGroup = "Master Data";

    protected static ?string $modelLabel = "Kategori";

    protected static ?string $pluralModelLabel = "Kategori";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make("name")
                    ->label("Nama Kategori")
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make("type")
                    ->label("Tipe")
                    ->options([
                        "income" => "Pemasukan",
                        "expense" => "Pengeluaran",
                    ])
                    ->required(),
                Forms\Components\ColorPicker::make("color")
                    ->label("Warna")
                    ->required()
                    ->default("#6366f1"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make("color")
                    ->label("Warna"),
                Tables\Columns\TextColumn::make("name")
                    ->label("Nama Kategori")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make("type")
                    ->label("Tipe")
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        "income" => "success",
                        "expense" => "danger",
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        "income" => "Pemasukan",
                        "expense" => "Pengeluaran",
                    }),
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
                        "income" => "Pemasukan",
                        "expense" => "Pengeluaran",
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
            "index" => Pages\ManageCategories::route("/"),
        ];
    }
}
