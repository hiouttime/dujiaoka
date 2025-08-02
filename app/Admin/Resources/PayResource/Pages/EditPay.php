<?php

namespace App\Admin\Resources\PayResource\Pages;

use App\Admin\Resources\PayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPay extends EditRecord
{
    protected static string $resource = PayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }
}