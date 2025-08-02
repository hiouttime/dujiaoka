<?php

namespace App\Admin\Resources\GoodsGroupResource\Pages;

use App\Admin\Resources\GoodsGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGoodsGroup extends CreateRecord
{
    protected static string $resource = GoodsGroupResource::class;
}
