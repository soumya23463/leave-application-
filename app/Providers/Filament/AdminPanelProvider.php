<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\AdminStatsOverview;
use App\Filament\Widgets\EmployeesOnLeaveWidget;
use App\Filament\Widgets\EmployeeStatsOverview;
use App\Filament\Widgets\MonthlyLeaveChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class AdminPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn () => Blade::render(<<<'HTML'
<script>
function disableNativeValidation() {
    document.querySelectorAll("form:not([novalidate])").forEach(f => f.setAttribute("novalidate", ""));
}
document.addEventListener("DOMContentLoaded", disableNativeValidation);
document.addEventListener("livewire:navigated", disableNativeValidation);
document.addEventListener("livewire:update", disableNativeValidation);
const observer = new MutationObserver(disableNativeValidation);
document.addEventListener("DOMContentLoaded", () => {
    observer.observe(document.body, { childList: true, subtree: true });
});
</script>
HTML),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationGroups([
                'Master',
                'Leave Management',
                'Reports',
                'Settings',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AdminStatsOverview::class,
                EmployeeStatsOverview::class,
                MonthlyLeaveChart::class,
                EmployeesOnLeaveWidget::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
