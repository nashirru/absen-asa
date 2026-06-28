<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollPeriodResource\Pages;
use App\Filament\Resources\PayrollPeriodResource\RelationManagers;
use App\Models\PayrollPeriod;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;

class PayrollPeriodResource extends Resource
{
    protected static ?string $model = PayrollPeriod::class;

    protected static ?string $navigationIcon = "heroicon-o-credit-card";

    protected static ?string $navigationGroup = "Penggajian";

    protected static ?string $navigationLabel = "Periode Gaji";

    protected static ?string $pluralModelLabel = "Periode Gaji";

    protected static ?string $modelLabel = "Periode Gaji";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make("month")
                    ->label("Bulan")
                    ->options([
                        1 => "Januari",
                        2 => "Februari",
                        3 => "Maret",
                        4 => "April",
                        5 => "Mei",
                        6 => "Juni",
                        7 => "Juli",
                        8 => "Agustus",
                        9 => "September",
                        10 => "Oktober",
                        11 => "November",
                        12 => "Desember",
                    ])
                    ->required()
                    ->disabled(fn ($record) => $record !== null),
                Forms\Components\TextInput::make("year")
                    ->label("Tahun")
                    ->numeric()
                    ->required()
                    ->default(now()->year)
                    ->disabled(fn ($record) => $record !== null),
                Forms\Components\Select::make("status")
                    ->label("Status")
                    ->options([
                        "draft" => "Draft",
                        "processed" => "Diproses",
                        "paid" => "Dibayar",
                    ])
                    ->required()
                    ->default("draft")
                    ->disabled(fn ($record) => $record === null || $record->status === "paid"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("month")
                    ->label("Periode")
                    ->formatStateUsing(function ($record) {
                        $months = [
                            1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
                            5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
                            9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
                        ];
                        return ($months[$record->month] ?? "") . " " . $record->year;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make("status")
                    ->label("Status")
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        "draft" => "warning",
                        "processed" => "info",
                        "paid" => "success",
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        "draft" => "Draft",
                        "processed" => "Diproses",
                        "paid" => "Dibayar",
                    }),
                Tables\Columns\TextColumn::make("payroll_details_sum_net_salary")
                    ->label("Total Gaji Bersih")
                    ->sum("payrollDetails", "net_salary")
                    ->moneyIdr(),
                Tables\Columns\TextColumn::make("payroll_details_count")
                    ->label("Jumlah Karyawan")
                    ->counts("payrollDetails"),
            ])
            ->actions([
                Action::make("processPayroll")
                    ->label("Proses Penggajian")
                    ->icon("heroicon-o-document-check")
                    ->color("success")
                    ->visible(fn (PayrollPeriod $record) => $record->status === "draft")
                    ->form([
                        Forms\Components\Select::make("account_id")
                            ->label("Akun Pengeluaran (Kas/Bank)")
                            ->options(Account::all()->pluck("name", "id"))
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(function (PayrollPeriod $record, array $data) {
                        $totalNetSalary = $record->payrollDetails()->sum("net_salary");

                        if ($totalNetSalary <= 0) {
                            Notification::make()
                                ->title("Gagal")
                                ->body("Tidak ada karyawan yang digaji atau total gaji bersih bernilai 0.")
                                ->danger()
                                ->send();
                            return;
                        }

                        $category = Category::firstOrCreate(
                            ["name" => "Gaji Karyawan", "type" => "expense"],
                            ["color" => "#ef4444"]
                        );

                        $months = [
                            1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
                            5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
                            9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
                        ];
                        $periodName = ($months[$record->month] ?? "") . " " . $record->year;

                        DB::transaction(function () use ($record, $data, $category, $totalNetSalary, $periodName) {
                            $record->update(["status" => "processed"]);

                            Transaction::create([
                                "type" => "expense",
                                "account_id" => $data["account_id"],
                                "category_id" => $category->id,
                                "amount" => $totalNetSalary,
                                "description" => "Penggajian Karyawan Periode {$periodName}",
                                "date" => now(),
                                "ref_payroll_id" => $record->id,
                            ]);
                        });

                        Notification::make()
                            ->title("Sukses")
                            ->body("Penggajian periode {$periodName} telah diproses sebesar Rp " . number_format($totalNetSalary, 0, ",", ".") . ".")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Action::make("payPayroll")
                    ->label("Bayar Gaji")
                    ->icon("heroicon-o-banknotes")
                    ->color("primary")
                    ->visible(fn (PayrollPeriod $record) => $record->status === "processed")
                    ->action(function (PayrollPeriod $record) {
                        $months = [
                            1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
                            5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
                            9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
                        ];
                        $periodName = ($months[$record->month] ?? "") . " " . $record->year;

                        DB::transaction(function () use ($record) {
                            $record->update([
                                "status" => "paid",
                            ]);
                            $record->payrollDetails()->update([
                                "paid_at" => now(),
                            ]);
                        });

                        Notification::make()
                            ->title("Sukses")
                            ->body("Gaji periode {$periodName} telah dibayarkan ke seluruh karyawan.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (PayrollPeriod $record, Tables\Actions\DeleteAction $action) {
                        if ($record->status === 'paid') {
                            Notification::make()
                                ->title('Tidak dapat menghapus')
                                ->body('Periode gaji yang sudah dibayar tidak dapat dihapus.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $hasPaid = $records->contains(fn ($record) => $record->status === 'paid');
                            if ($hasPaid) {
                                Notification::make()
                                    ->title('Tidak dapat menghapus')
                                    ->body('Terdapat periode gaji yang sudah dibayar. Harap hapus secara individual.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $records->each->delete();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PayrollDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListPayrollPeriods::route("/"),
            "create" => Pages\CreatePayrollPeriod::route("/create"),
            "edit" => Pages\EditPayrollPeriod::route("/{record}/edit"),
        ];
    }
}