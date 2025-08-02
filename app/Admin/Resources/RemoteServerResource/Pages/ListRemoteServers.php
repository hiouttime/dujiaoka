<?php

namespace App\Admin\Resources\RemoteServerResource\Pages;

use App\Admin\Resources\RemoteServerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRemoteServers extends ListRecords
{
    protected static string $resource = RemoteServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}