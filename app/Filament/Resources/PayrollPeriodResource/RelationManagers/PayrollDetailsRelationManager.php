<?php

namespace App\Filament\Resources\PayrollPeriodResource\RelationManagers;

use App\Models\PayrollDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PayrollDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'payrollDetails';

    protected static ?string $title = 'Rincian Gaji Karyawan';

    protected static ?string $modelLabel = 'Rincian Gaji';

    protected static ?string $pluralModelLabel = 'Rincian Gaji';

    public function form(Form $form): Form
    {
        $isDraft = $this->getOwnerRecord()->status === 'draft';

        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_name')
                    ->label('Karyawan')
                    ->placeholder(fn ($record) => $record?->employee?->name)
                    ->disabled()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('base_salary')
                    ->label('Gaji Pokok')
                    ->numeric()
                    ->disabled()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('total_allowance')
                    ->label('Total Tunjangan')
                    ->numeric()
                    ->disabled()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('total_deduction')
                    ->label('Total Potongan')
                    ->numeric()
                    ->disabled()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('bonus')
                    ->label('Bonus Tambahan')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->disabled(!$isDraft)
                    ->prefix('Rp'),
            ]);
    }

    public function table(Table $table): Table
    {
        $isDraft = $this->getOwnerRecord()->status === 'draft';

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_salary')
                    ->label('Gaji Pokok')
                    ->moneyIdr(),
                Tables\Columns\TextColumn::make('total_allowance')
                    ->label('Tunjangan')
                    ->moneyIdr()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_deduction')
                    ->label('Potongan')
                    ->moneyIdr()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bonus')
                    ->label('Bonus')
                    ->moneyIdr(),
                Tables\Columns\TextColumn::make('net_salary')
                    ->label('Gaji Bersih')
                    ->moneyIdr()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tanggal Bayar')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Belum dibayar'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data, PayrollDetail $record): array {
                        $baseSalary = (float) $record->base_salary;
                        $allowances = (float) $record->total_allowance;
                        $deductions = (float) $record->total_deduction;
                        $bonus = (float) $data['bonus'];

                        $data['net_salary'] = $baseSalary + $allowances - $deductions + $bonus;

                        return $data;
                    })
                    ->visible($isDraft),
                Tables\Actions\Action::make('printSlip')
                    ->label('Lihat Slip')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (PayrollDetail $record): string => route('payroll.slip', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('downloadSlip')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (PayrollDetail $record): string => route('payroll.slip', [$record, 'format' => 'pdf']))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->visible($isDraft),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible($isDraft),
                ]),
            ]);
    }
}