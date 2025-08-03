<?php

namespace App\Admin\Resources\Cards\Pages;

use App\Admin\Resources\Cards;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarmis extends ListRecords
{
    protected static string $resource = Cards::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
