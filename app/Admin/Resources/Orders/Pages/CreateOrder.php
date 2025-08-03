<?php

namespace App\Admin\Resources\Orders\Pages;

use App\Admin\Resources\Orders;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = Orders::class;
}
