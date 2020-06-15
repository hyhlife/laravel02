<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use App\Models\User;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use App\Nova\Dashboards\SiteInsights;
use Bakerkretzmar\NovaSettingsTool\SettingsTool;
use App\Nova\Metrics\UsersPerDay;
use App\Nova\Metrics\NewUsers;

use App\Nova\Metrics\TopicsPerDay;
use App\Nova\Metrics\NewTopics;
use Beyondcode\CustomDashboardCard\NovaCustomDashboard;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::serving(function () {
            \App\Models\User::observe(\App\Observers\UserObserver::class);
            \App\Models\Topic::observe(\App\Observers\TopicObserver::class);
            \App\Models\Link::observe(\App\Observers\LinkObserver::class);
            \App\Models\Reply::observe(\App\Observers\ReplyObserver::class);
        });

        Nova::sortResourcesBy(function ($resource) {
            return $resource::$priority ?? 9999;
        });

        NovaCustomDashboard::cards([
            (new NewUsers)->withMeta([
                'card-name' => '用户数量'
            ]),
            (new UsersPerDay)->withMeta([
                'card-name' => '用户增长率'
            ]),
            (new NewTopics)->withMeta([
                'card-name' => '话题数量'
            ]),
            (new TopicsPerDay)->withMeta([
                'card-name' => '话题增长率'
            ]),
        ]);
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                '875986139@qq.com',
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            // new Help,
            new \Beyondcode\CustomDashboardCard\CustomDashboard,
            // new UsersPerDay,
            // new NewUsers
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            // (new SiteInsights)->canSeeWhen('edit_settings', User::class)
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            (\Vyuldashev\NovaPermission\NovaPermissionTool::make())->canSee(function ($request){
                return $request->user()->can('manage_users');
            }),
            (new SettingsTool)->canSee(function ($request){
                return $request->user()->can('manage_users');
            }),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
