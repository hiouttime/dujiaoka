<?php

namespace App\Admin\Resources\Users\Pages;

use App\Admin\Resources\Users;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewUser extends ViewRecord
{
    protected static string $resource = Users::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('用户信息')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('email')
                                    ->label('邮箱'),
                                TextEntry::make('nickname')
                                    ->label('昵称')
                                    ->placeholder('未设置'),
                                TextEntry::make('phone')
                                    ->label('手机号码')
                                    ->placeholder('未设置'),
                                TextEntry::make('level.name')
                                    ->label('用户等级')
                                    ->badge(),
                                TextEntry::make('status_text')
                                    ->label('状态')
                                    ->badge()
                                    ->color(fn ($record) => $record->status === 1 ? 'success' : 'danger'),
                                TextEntry::make('last_login_at')
                                    ->label('最后登录')
                                    ->dateTime()
                                    ->placeholder('从未登录'),
                            ]),
                    ]),

                Section::make('账户信息')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('balance')
                                    ->label('账户余额')
                                    ->money('CNY'),
                                TextEntry::make('total_spent')
                                    ->label('累计消费')
                                    ->money('CNY'),
                                TextEntry::make('orders_count')
                                    ->label('订单数量')
                                    ->getStateUsing(fn ($record) => $record->orders()->count()),
                                TextEntry::make('completed_orders_count')
                                    ->label('完成订单')
                                    ->getStateUsing(fn ($record) => $record->orders()->where('status', 4)->count()),
                            ]),
                    ]),

                Section::make('时间信息')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('注册时间')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('最后更新')
                                    ->dateTime(),
                                TextEntry::make('email_verified_at')
                                    ->label('邮箱验证时间')
                                    ->dateTime()
                                    ->placeholder('未验证'),
                                TextEntry::make('last_login_ip')
                                    ->label('最后登录IP')
                                    ->placeholder('无记录'),
                            ]),
                    ]),
            ]);
    }
}