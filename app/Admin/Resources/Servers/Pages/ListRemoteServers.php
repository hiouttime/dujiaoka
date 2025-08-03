<?php

namespace App\Admin\Resources\Servers\Pages;

use App\Admin\Resources\Servers;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRemoteServers extends ListRecords
{
    protected static string $resource = Servers::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}