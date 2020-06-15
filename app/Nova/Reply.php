<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Reply extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\\Models\\Reply';

    public static $group = '内容管理';

    public static $priority = 3;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'content';

    public static $name = '回复';

    public static $with = ['user','topic'];

    public static $searchRelations = [
        'user' => ['name', 'email'],
        'topic'=>['title']
    ];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'content',
    ];

    public static function label()
    {
        return '回复';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('评论内容','content')->onlyOnIndex(),
            Textarea::make('评论内容','content')->rules('required', 'min:8', 'max:255')->alwaysShow(),
            BelongsTo::make('作者','User','App\\Nova\\User')
                ->hideFromIndex()
                ->hideFromDetail()
                ->showOnUpdating()
                ->showOnCreating()
                ->required(true)
                ->searchable(),
            Text::make('作者', function () {
                $route = route('users.show', $this->user_id);
                return <<<HTML
                    <a class="no-underline dim text-primary font-bold" href="{$route}" target="_blank">{$this->user->name}</a>
                HTML;
            })->asHtml(),
            BelongsTo::make('话题', 'Topic', 'app\\Nova\\Topic')
                ->hideFromIndex()
                ->hideFromDetail()
                ->showOnUpdating()
                ->showOnCreating()
                ->required(true)
                ->searchable(),

            Text::make('话题', function () {
                $route = route('topics.show', $this->topic_id);
                return <<<HTML
                    <a class="no-underline dim text-primary font-bold" href="{$route}" target="_blank">{$this->topic->title}</a>
                HTML;
            })->asHtml(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
