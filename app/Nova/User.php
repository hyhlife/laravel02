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
// use Ctessier\NovaAdvancedImageField\AdvancedImage;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\\Models\\User';

    public static $group = '角色及权限';
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static $name = '用户';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
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
        return [
            ID::make('用户id','id')->sortable(),

            // Gravatar::make('头像','avatar')->path('nova/images/avatars/'.$request->user()->id),
            // Avatar::make('头像','avatar')->path('nova/images/avatars/'.$request->user()->id)->onlyOnForms(),
            // AdvancedImage::make('头像','avatar')->croppable(),
            Avatar::make('头像','avatar')->path('nova/images/avatars/'.$request->user()->id),

            Text::make('用户名','name')
                ->sortable()
                ->rules('required', 'max:255')
                ->creationRules('unique:users,name')
                ->updateRules('unique:users,name,{{resourceId}}'),

            Text::make('用户名', function () {
                $route = route('users.show', $this->id);
                return <<<HTML
                    <a class="no-underline dim text-primary font-bold" href="{$route}" target="_blank">{$this->name}</a>
                HTML;
            })->asHtml(),

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

            RoleSelect::make('Role', 'roles'),

            HasMany::make('Topic', 'topics'),

            MorphToMany::make('Roles', 'roles', \Vyuldashev\NovaPermission\Role::class),
            // MorphToMany::make('Permissions', 'permissions', \Vyuldashev\NovaPermission\Permission::class),
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
        return [
            new Filters\StartDataFilter(),
            new Filters\EndDataFilter(),
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
        return [
            // (new Lenses\MostValuableUsers)->canSee(function ($request) {
            //     return $request->user()->can(
            //         'manage_users', User::class
            //     );
            // }),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            // (new Actions\EmailAccountProfile)->confirmText('Are you sure you want to activate this user?')
            // ->confirmButtonText('Activate')
            // ->cancelButtonText("Don't activate"),
            // (new Actions\DeleteUserData)
        ];
    }

    public function title()
    {
        return $this->name;
    }
}