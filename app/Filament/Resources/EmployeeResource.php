<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = "heroicon-o-users";

    protected static ?string $navigationGroup = "Master Data";

    protected static ?string $modelLabel = "Karyawan";

    protected static ?string $pluralModelLabel = "Karyawan";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make("name")
                    ->label("Nama Karyawan")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make("position")
                    ->label("Jabatan")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make("department")
                    ->label("Departemen")
                    ->maxLength(255),
                Forms\Components\TextInput::make("base_salary")
                    ->label("Gaji Pokok")
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\DatePicker::make("join_date")
                    ->label("Tanggal Bergabung")
                    ->required(),
                Forms\Components\Select::make("status")
                    ->label("Status")
                    ->options([
                        "active" => "Aktif",
                        "inactive" => "Nonaktif",
                    ])
                    ->required()
                    ->default("active"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("name")
                    ->label("Nama Karyawan")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make("position")
                    ->label("Jabatan")
                    ->searchable(),
                Tables\Columns\TextColumn::make("department")
                    ->label("Departemen")
                    ->searchable(),
                Tables\Columns\TextColumn::make("base_salary")
                    ->label("Gaji Pokok")
                    ->moneyIdr()
                    ->sortable(),
                Tables\Columns\TextColumn::make("join_date")
                    ->label("Tanggal Bergabung")
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make("status")
                    ->label("Status")
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        "active" => "success",
                        "inactive" => "danger",
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        "active" => "Aktif",
                        "inactive" => "Nonaktif",
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("status")
                    ->label("Status")
                    ->options([
                        "active" => "Aktif",
                        "inactive" => "Nonaktif",
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
            "index" => Pages\ManageEmployees::route("/"),
        ];
    }
}
