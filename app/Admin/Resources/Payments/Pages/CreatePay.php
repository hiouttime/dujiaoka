<?php

namespace App\Admin\Resources\Payments\Pages;

use App\Admin\Resources\Payments;
use Filament\Resources\Pages\CreateRecord;

class CreatePay extends CreateRecord
{
    protected static string $resource = Payments::class;
}