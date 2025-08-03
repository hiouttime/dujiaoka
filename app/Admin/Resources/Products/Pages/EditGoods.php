<?php

namespace App\Admin\Resources\Products\Pages;

use App\Admin\Resources\Products;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoods extends EditRecord
{
    protected static string $resource = Products::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
