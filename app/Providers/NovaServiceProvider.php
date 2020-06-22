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
use App\Nova\Metrics\TotalUsers;

use App\Nova\Metrics\TopicsPerDay;
use App\Nova\Metrics\NewTopics;
use App\Nova\Metrics\TotalTopics;
use Beyondcode\CustomDashboardCard\NovaCustomDashboard;
use Coroowicaksono\ChartJsIntegration\StackedChart;
use Coroowicaksono\ChartJsIntegration\LineChart;

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

        // NovaCustomDashboard::cards([
        //     (new NewUsers())->title('用户量'),
        //     (new UsersPerDay()),
        //     (new NewTopics()),
        //     (new TopicsPerDay()),
        //     (new StackedChart())
        //         ->title('用户量')
        //         ->model('App\Models\User')
        //         ->options([
        //         'btnFilter' => true,
        //         'btnFilterDefault' => 'YTD',
        //         'btnFilterList' => [
        //             30 => '30 Days',
        //             60 => '60 Days',
        //             365 => '365 Days',
        //             1 => '1 Day',
        //             'MTD' => 'Month To Date',
        //             'QTD' => 'Quarter To Date',
        //             'YTD' => 'Year To Date',
        //         ],
        //     ])
        // ]);
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
            return $user->can('manage_users') || $user->can('manage_contents') || $user->can('edit_settings');
        });

    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {   
        $per_day = per_day();
        return [
            // new Help,
            // new \Beyondcode\CustomDashboardCard\CustomDashboard,
            (new TotalUsers())->width('1/6'),
            (new NewUsers())->width('1/6'),
            (new LineChart())
                ->title('用户增长趋势')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])
                ->series(array([
                    'barPercentage' => 0.5,
                    'label' => $per_day['series_label_01'],
                    'borderColor' => '#f7a35c',
                    'data' => $per_day['users_per_day']['series_data_01'],
                ],
                [
                    'barPercentage' => 0.5,
                    'label' => $per_day['series_label_02'],
                    'borderColor' => '#90ed7d',
                    'data' => $per_day['users_per_day']['series_data_02'],
                ]
                ))
                ->options([
                    'xaxis' => [
                        'categories' => $per_day['xaxis']
                    ],
                ])
                ->width('2/3'),
            (new TotalTopics())->width('1/6'),
            (new NewTopics())->width('1/6'),
            (new LineChart())
                ->title('话题增长趋势')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])
                ->series(array([
                    'barPercentage' => 0.5,
                    'label' => $per_day['series_label_01'],
                    'borderColor' => '#f7a35c',
                    'data' => $per_day['topics_per_day']['series_data_01'],
                ],
                [
                    'barPercentage' => 0.5,
                    'label' => $per_day['series_label_02'],
                    'borderColor' => '#90ed7d',
                    'data' => $per_day['topics_per_day']['series_data_02'],
                ]
            ))
                ->options([
                    'xaxis' => [
                        'categories' => $per_day['xaxis']
                    ],
                ])
                ->width('2/3'),
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
