<?php

namespace App\Admin\Resources\Categories\Pages;

use App\Admin\Resources\Categories;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGoodsGroup extends CreateRecord
{
    protected static string $resource = Categories::class;
}
