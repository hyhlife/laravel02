<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;
use Laravel\Nova\Fields\Text;

class SiteInsights extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            //
        ];
    }

    public function fields(Request $request){
        return [
            Text::make('contact_email',setting('contact_email')),
        ];
    }

    public static function label()
    {
        return '站点设置';
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'site-insights';
    }
}
