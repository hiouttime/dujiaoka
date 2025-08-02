<?php

namespace App\Admin\Resources\GoodsResource\Pages;

use App\Admin\Resources\GoodsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoods extends EditRecord
{
    protected static string $resource = GoodsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
