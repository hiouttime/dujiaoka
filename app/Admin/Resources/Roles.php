<?php

namespace App\Admin\Resources;

use App\Admin\Resources\Roles\Pages;
use Filament\Forms\{Form, Components\TextInput, Components\Select};
use Filament\Resources\Resource;
use Filament\Tables\{Table, Columns\TextColumn};
use Filament\Tables;
use Spatie\Permission\Models\Role;

class Roles extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationGroup = '系统管理';
    
    protected static ?string $navigationLabel = '角色';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = '角色';
    
    protected static ?string $pluralModelLabel = '角色管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('角色名称')->required()->unique(ignoreRecord: true),
                Select::make('permissions')->label('权限')->multiple()
                    ->relationship('permissions', 'id')
                    ->preload()
                    ->options(function () {
                        return \Spatie\Permission\Models\Permission::all()
                            ->mapWithKeys(function ($permission) {
                                return [$permission->id => static::translatePermission($permission->name)];
                            })->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): string {
                        $permission = \Spatie\Permission\Models\Permission::find($value);
                        return $permission ? static::translatePermission($permission->name) : $value;
                    })
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('角色名称')->searchable(),
                TextColumn::make('permissions')
                    ->label('权限')
                    ->formatStateUsing(function ($record) {
                        return $record->permissions->take(3)->map(function ($permission) {
                            return static::translatePermission($permission->name);
                        })->join('、') . ($record->permissions->count() > 3 ? ' 等' : '');
                    })
                    ->wrap(),
                TextColumn::make('permissions_count')->label('权限数量')->counts('permissions'),
                TextColumn::make('created_at')->label('创建时间')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage_roles') || auth()->user()?->hasRole('super-admin') || false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }

    protected static function translatePermission(string $permission): string
    {
        $translations = [
            // 系统管理
            'manage_admins' => '管理员管理',
            'manage_roles' => '角色权限管理',
            
            // 用户相关
            'manage_users' => '用户管理',
            'manage_user_levels' => '用户等级管理',
            
            // 商店管理
            'manage_products' => '商品管理',
            'manage_categories' => '商品分类管理',
            'manage_cards' => '卡密管理',
            'manage_coupons' => '优惠券管理',
            'manage_servers' => '服务器管理',
            
            // 订单管理
            'manage_orders' => '订单管理',
            'view_orders' => '查看订单',
            
            // 支付管理
            'manage_payments' => '支付配置管理',
            
            // 内容管理
            'manage_articles' => '文章管理',
            'manage_article_categories' => '文章分类管理',
            'manage_email_templates' => '邮件模板管理',
            
            // 系统配置
            'manage_settings' => '系统设置管理',
        ];

        return $translations[$permission] ?? $permission;
    }

    protected static function getPermissionGroup(string $permission): string
    {
        $groups = [
            // 系统管理
            'manage_admins' => '系统管理',
            'manage_roles' => '系统管理',
            
            // 用户相关
            'manage_users' => '用户管理',
            'manage_user_levels' => '用户管理',
            
            // 商店管理
            'manage_products' => '商店管理',
            'manage_categories' => '商店管理',
            'manage_cards' => '商店管理',
            'manage_coupons' => '商店管理',
            'manage_servers' => '商店管理',
            
            // 订单管理
            'manage_orders' => '订单管理',
            'view_orders' => '订单管理',
            
            // 支付管理
            'manage_payments' => '支付管理',
            
            // 内容管理
            'manage_articles' => '内容管理',
            'manage_article_categories' => '内容管理',
            'manage_email_templates' => '内容管理',
            
            // 系统配置
            'manage_settings' => '系统配置',
        ];

        return $groups[$permission] ?? '其他';
    }
}
