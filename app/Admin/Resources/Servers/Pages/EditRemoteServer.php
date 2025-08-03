<?php

namespace App\Admin\Resources\Servers\Pages;

use App\Admin\Resources\Servers;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemoteServer extends EditRecord
{
    protected static string $resource = Servers::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}