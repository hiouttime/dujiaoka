<?php

namespace App\Admin\Resources\Payments\Pages;

use App\Admin\Resources\Payments;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPay extends EditRecord
{
    protected static string $resource = Payments::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }
}