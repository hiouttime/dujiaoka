<?php

namespace App\Admin\Resources\Products\Pages;

use App\Admin\Resources\Products;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGoods extends ListRecords
{
    protected static string $resource = Products::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
