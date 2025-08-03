<?php

namespace App\Admin\Resources\Cards\Pages;

use App\Admin\Resources\Cards;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarmis extends EditRecord
{
    protected static string $resource = Cards::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
