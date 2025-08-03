<?php

namespace App\Admin\Resources\Coupons\Pages;

use App\Admin\Resources\Coupons;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = Coupons::class;
}
