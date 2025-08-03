<?php

namespace App\Admin\Resources\Categories\Pages;

use App\Admin\Resources\Categories;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoodsGroup extends EditRecord
{
    protected static string $resource = Categories::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
