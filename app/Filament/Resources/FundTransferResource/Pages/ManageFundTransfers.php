<?php

namespace App\Filament\Resources\FundTransferResource\Pages;

use App\Filament\Resources\FundTransferResource;
use App\Models\Account;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ManageFundTransfers extends ManageRecords
{
    protected static string $resource = FundTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}