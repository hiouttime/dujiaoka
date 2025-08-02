<?php

namespace App\Admin\Resources\GoodsGroupResource\Pages;

use App\Admin\Resources\GoodsGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGoodsGroups extends ListRecords
{
    protected static string $resource = GoodsGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
