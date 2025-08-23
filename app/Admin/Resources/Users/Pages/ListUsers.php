<?php

namespace App\Admin\Resources\Users\Pages;

use App\Admin\Resources\Users;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ListUsers extends ListRecords
{
    protected static string $resource = Users::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('全部用户'),
            'active' => Tab::make('正常用户')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', User::STATUS_ACTIVE)),
            'disabled' => Tab::make('禁用用户')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', User::STATUS_DISABLED)),
            'vip' => Tab::make('VIP用户')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('level', fn($q) => $q->where('min_spent', '>', 0))),
        ];
    }
}