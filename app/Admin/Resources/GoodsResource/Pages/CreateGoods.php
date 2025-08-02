<?php

namespace App\Admin\Resources\GoodsResource\Pages;

use App\Admin\Resources\GoodsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGoods extends CreateRecord
{
    protected static string $resource = GoodsResource::class;
}
