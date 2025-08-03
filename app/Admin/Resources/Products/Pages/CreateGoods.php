<?php

namespace App\Admin\Resources\Products\Pages;

use App\Admin\Resources\Products;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGoods extends CreateRecord
{
    protected static string $resource = Products::class;
}
