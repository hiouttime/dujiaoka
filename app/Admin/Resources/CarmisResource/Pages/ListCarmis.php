<?php

namespace App\Admin\Resources\CarmisResource\Pages;

use App\Admin\Resources\CarmisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarmis extends ListRecords
{
    protected static string $resource = CarmisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
