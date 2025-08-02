<?php

namespace App\Admin\Resources\GoodsResource\Pages;

use App\Admin\Resources\GoodsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGoods extends ListRecords
{
    protected static string $resource = GoodsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
