<?php

namespace App\Admin\Resources\RemoteServerResource\Pages;

use App\Admin\Resources\RemoteServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemoteServer extends EditRecord
{
    protected static string $resource = RemoteServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}