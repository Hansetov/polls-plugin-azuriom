<?php

namespace Azuriom\Plugin\Polls\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\ActionLog;
use Azuriom\Models\Permission;
use Azuriom\Plugin\Polls\Models\Poll;

class PollsServiceProvider extends BasePluginServiceProvider
{
    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        $this->loadViews();

        $this->loadTranslations();

        $this->loadMigrations();

        $this->registerRouteDescriptions();

        $this->registerUserNavigation();

        $this->registerAdminNavigation();

        Permission::registerPermissions([
            'polls.admin' => 'polls::admin.permissions.manage',
        ]);

        ActionLog::registerLogModels([
            Poll::class,
        ], 'polls::admin.logs');
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array<string, string>
     */
    protected function routeDescriptions(): array
    {
        return [
            'polls.index' => trans('polls::messages.title'),
        ];
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array<string, array<string, string>>
     */
    protected function adminNavigation(): array
    {
        return [
            'polls' => [
                'name' => trans('polls::admin.nav.title'),
                'icon' => 'bi bi-ui-checks-grid',
                'route' => 'polls.admin.index',
                'permission' => 'polls.admin',
            ],
        ];
    }

    /**
     * Return the user navigations routes to register in the user menu.
     *
     * @return array<string, array<string, string>>
     */
    protected function userNavigation(): array
    {
        return [
            'polls' => [
                'route' => 'polls.index',
                'name' => trans('polls::messages.title'),
                'icon' => 'bi bi-bar-chart-line',
            ],
        ];
    }
}
