<?php

namespace App\Admin\Resources\PayResource\Pages;

use App\Admin\Resources\PayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPays extends ListRecords
{
    protected static string $resource = PayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}