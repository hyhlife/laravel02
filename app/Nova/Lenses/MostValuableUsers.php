<?php

namespace App\Nova\Lenses;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Fields\Number;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\LensRequest;

class MostValuableUsers extends Lens
{
    /**
     * 获取 lens 的查询创建器和分页器
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->select([
                        'users.id',
                        'users.name',
                        DB::raw('sum(topics.reply_count) as reply_count')])
                  ->join('topics', 'users.id', '=', 'topics.user_id')
                  ->orderBy('reply_count', 'desc')
                  ->groupBy('users.id', 'users.name')
        ));
    }

    /**
     * 获取 lens 可获取的字段
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'id'),
            Text::make('Name', 'name'),

            Number::make('reply_count', 'reply_count', function ($value) {
                return '$'.number_format($value, 2);
            }),
        ];
    }

    /**
     * 获取 lens 可获取的过滤器
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * 获取 lens 的 URI
     *
     * @return string
     */
    public function uriKey()
    {
        return 'most-profitable-users';
    }
}