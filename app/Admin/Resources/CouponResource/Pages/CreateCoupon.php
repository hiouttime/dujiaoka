<?php

namespace App\Admin\Resources\CouponResource\Pages;

use App\Admin\Resources\CouponResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;
}
