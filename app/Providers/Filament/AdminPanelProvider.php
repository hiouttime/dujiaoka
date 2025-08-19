<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Admin\Pages\Login;
use App\Admin\Widgets\StatsOverviewWidget;
use App\Admin\Widgets\EnhancedStatsOverviewWidget;
use App\Admin\Widgets\SalesChartWidget;
use App\Admin\Widgets\PaymentMethodChartWidget;
use App\Admin\Widgets\TopSellingGoodsWidget;
use App\Admin\Widgets\OrderStatusChartWidget;
use App\Admin\Widgets\HourlyOrdersWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->authGuard('admin')
            ->discoverResources(in: app_path('Admin/Resources'), for: 'App\\Admin\\Resources')
            ->discoverPages(in: app_path('Admin/Pages'), for: 'App\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Admin/Widgets'), for: 'App\\Admin\\Widgets')
            ->widgets([
                EnhancedStatsOverviewWidget::class,
                SalesChartWidget::class,
                PaymentMethodChartWidget::class,
                TopSellingGoodsWidget::class,
                OrderStatusChartWidget::class,
                HourlyOrdersWidget::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // 性能优化配置
            ->spa()
            ->unsavedChangesAlerts()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->brandName('独角数卡 DUJIAOKA')
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                '用户管理',
                '商店管理',
                '支付配置',
                '邮件设置',
                '内容管理',
                '外观设置',
                '店铺设置',
                '其他配置'
            ]);  // 导航分组
    }
}
