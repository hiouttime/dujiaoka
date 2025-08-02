<?php

namespace App\Admin\Resources\GoodsGroupResource\Pages;

use App\Admin\Resources\GoodsGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoodsGroup extends EditRecord
{
    protected static string $resource = GoodsGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
