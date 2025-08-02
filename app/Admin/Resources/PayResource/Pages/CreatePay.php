<?php

namespace App\Admin\Resources\PayResource\Pages;

use App\Admin\Resources\PayResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePay extends CreateRecord
{
    protected static string $resource = PayResource::class;
}