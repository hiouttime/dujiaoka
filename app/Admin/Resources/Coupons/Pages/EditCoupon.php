<?php

namespace App\Admin\Resources\Coupons\Pages;

use App\Admin\Resources\Coupons;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCoupon extends EditRecord
{
    protected static string $resource = Coupons::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
