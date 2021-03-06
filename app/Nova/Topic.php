<?php

namespace App\Nova;

use App\Models\User as UserModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\BelongsTo;
use Ek0519\Quilljs\Quilljs;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Select;
use NovaButton\Button;
use Auth;

class Topic extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Topic';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $group = '内容管理';

    public static $priority = 2;

    public static $title = 'title';

    public static $with = ['user','category','replies'];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title'
    ];

    public static $searchRelations = [
        'user' => ['name', 'email'],
        'category'=>['name']
    ];

    public static function label()
    {
        return '话题';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('标题','title')
                ->onlyOnForms()
                ->showOnDetail()
                ->rules('required', 'min:4', 'max:255'),
            Text::make('标题','title', function () {
                $route = route('topics.show', $this->id);
                return <<<HTML
            <a class="no-underline dim text-primary font-bold" href="{$route}" target="_blank">{$this->title}</a>
HTML;
            })->sortable()->onlyOnIndex()->asHtml(),
            BelongsTo::make('作者','User','App\Nova\User')
                ->hideFromIndex()
                ->hideFromDetail()
                ->showOnUpdating()
                ->showOnCreating()
                ->required(true)
                ->searchable(),
            Text::make('作者', function () {
                $route = route('users.show', $this->user_id);
                return <<<HTML
            <a class="no-underline dim text-primary font-bold" href="{$route}" target="_blank">
                <img src="{$this->user->avatar}" width="30" style="border-radius: 100px;vertical-align: middle;">
                {$this->user->name}
            </a>
HTML;
            })->asHtml(),
            BelongsTo::make('分类','Category','App\Nova\Category')->required(true),
            Text::make('评论数',function(){
                return $this->replies->count();
            })->onlyOnIndex(),

            Button::make('论坛详情')->link(config('app.url').'/topics/'.$this->id)->onlyOnIndex()->style('info'),
            
            Quilljs::make('话题内容','body')
                ->withFiles('minio', 'huangyanhong/topic'.$this->user_id)
                ->height(500)
                ->alwaysShow()
                ->rules('required'),

            HasMany::make('评论内容', 'replies','App\Nova\Reply'),
        ];


        return $fields;
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
        return [
            new Filters\CategoryFilter(),
            new Filters\HasReplyFilter()
        ];
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
