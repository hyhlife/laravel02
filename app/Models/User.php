<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Spatie\Permission\Traits\HasRoles;
use Auth;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmailTrait;

    // 活跃用户
    use Traits\ActiveUserHelper;

    // 最后登录时间
    use Traits\LastActivedAtHelper;

    use HasRoles;

    use Notifiable {
        notify as protected laravelNotify;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar','remember_token','last_actived_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }

        // 只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }


    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! \Str::startsWith($path, 'http')) {
            // 拼接完整的 URL
            if (\Str::startsWith($path, 'nova')) {
                $path = config('app.url') . "/storage/$path";
            } else {
                $path = config('app.url') . "/uploads/images/avatars/$path";
            }
        }
        $this->attributes['avatar'] = $path;
    }


    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if(trim($value) == ''){
            $this->attributes['password'] = bcrypt('123456');
        } else {
            if (strlen($value) != 60) {
                $this->attributes['password'] = bcrypt($value);
            } else {
                $this->attributes['password'] = $value;
            }
        }
        // if (strlen($value) != 60) {

        //     // 不等于 60，做密码加密处理
        //     $value = bcrypt($value);
        // }

        // $this->attributes['password'] = $value;
    }
}