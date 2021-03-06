<?php

namespace App\Nova;

use App\Models\User as UserModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Vyuldashev\NovaPermission\RoleSelect;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphToMany;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\HasMany;
use NovaButton\Button;
use Auth;
// use Ctessier\NovaAdvancedImageField\AdvancedImage;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\User';

    public static $group = '角色及权限';

    public static $title = 'name';

    public static $name = '用户';

    public static $search = [
        'id', 'name', 'email'
    ];

    public static function label()
    {
        return '用户';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $fields =  [
            ID::make('用户id','id')->sortable(),

            Avatar::make('头像','avatar', function () {
                return <<<HTML
            <img src="{$this->user->avatar}" width="30" style="border-radius: 100px;">
HTML;
            })->disk('minio')->path('huangyanhong/avatar/'.$request->user()->id),

            Text::make('用户名','name', function () {
                $route = route('users.show', $this->id);
                return <<<HTML
            <a class="no-underline dim text-primary font-bold" href="{$route}" target="_blank">{$this->name}</a>
HTML;
            })->asHtml()->sortable()->onlyOnIndex(),

            Text::make('用户名','name')
            ->onlyOnForms()
            ->showOnDetail()
            ->rules('required', 'max:255')
            ->creationRules('unique:users,name')
            ->updateRules('unique:users,name,{{resourceId}}'),

            Text::make('邮箱','email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('密码','password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            DateTime::make('注册时间','created_at')->onlyOnIndex(),

            Text::make('话题数量',function(){
                return $this->topics->count();
            })->onlyOnIndex()
        ];

        if(Auth::user()->hasRole('超级管理员')){
            $fields[] = HasMany::make('话题', 'topics','App\Nova\Topic');
            $fields[] = MorphToMany::make('角色', 'roles', 'Vyuldashev\NovaPermission\Role');
            $fields[] = RoleSelect::make('角色', 'roles');
        } else {
            $fields[] = RoleSelect::make('角色', 'roles')->onlyOnIndex();
            $fields[] = HasMany::make('话题', 'topics','App\Nova\Topic');
            if(Auth::user()->can('manage_roles')){
                $fields[] = MorphToMany::make('角色', 'roles','Vyuldashev\NovaPermission\Role');
            }
        }

        $fields[] = Button::make('用户详情')->link(config('app.url').'/users/'.$this->id)->onlyOnIndex()->style('info');

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
            new Filters\StartDataFilter(),
            new Filters\EndDataFilter(),
            new Filters\UserRoleFilter()
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