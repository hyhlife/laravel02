<?php

use Illuminate\Support\Facades\DB;

// 将当前请求的路由名称转换为 CSS 类名称
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function category_nav_active($category_id)
{
    return active_class((if_route('categories.show') && if_route_param('category', $category_id)));
}

function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return Str::limit($excerpt, $length);
}

function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '')
{
    // 获取数据模型的复数蛇形命名
    $model_name = model_plural_name($model);

    // 初始化前缀
    $prefix = $prefix ? "/$prefix/" : '/';

    // 使用站点 URL 拼接全量 URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    // 拼接 HTML A 标签，并返回
    return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
}

function model_plural_name($model)
{
    // 从实体中获取完整类名，例如：App\Models\User
    $full_class_name = get_class($model);

    // 获取基础类名，例如：传参 `App\Models\User` 会得到 `User`
    $class_name = class_basename($full_class_name);

    // 蛇形命名，例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
    $snake_case_name = Str::snake($class_name);

    // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
    return Str::plural($snake_case_name);
}

function per_day(){
    $per_day = array();
    $end_day = date("Y-m-d",time());
    $start_day = date("Y-m-d",strtotime("-7 day"));
    $dif_date = printDates($start_day,$end_day);
    $per_day['xaxis'] = $dif_date;
    $per_day['series_label_01'] = $start_day.'-'.$end_day;

    $last_start_day = date("Y-m-d",strtotime("-15 day"));
    $last_end_day = date("Y-m-d",strtotime("-8 day"));
    $per_day['series_label_02'] = $last_start_day.'-'.$last_end_day;
    $last_dif_date = printDates($last_start_day,$last_end_day);
    $users_per_day = users_per_day($last_start_day,$end_day);
    $topics_per_day = topics_per_day($last_start_day,$end_day);
    $per_day['users_per_day'] = $users_per_day;
    $per_day['topics_per_day'] = $topics_per_day;
    return $per_day;
}

function printDates($start,$end){
    $dif_date = array();
    $dt_start = strtotime($start);
    $dt_end = strtotime($end);
    while ($dt_start<=$dt_end){
        $dif_date[] = date('Y-m-d',$dt_start);
        $dt_start = strtotime('+1 day',$dt_start);
    }
    return $dif_date;
}

function users_per_day($last_start_day,$end_day){
    $all_date = printDates($last_start_day,$end_day);
    $all_date_user_count = array();
    foreach ($all_date as $key => $value) {
        $all_date_user_count[$value] = 0;
    }
    $all_users = Db::table('users')->whereBetween('created_at',array($last_start_day.' 00:00:00',$end_day.' 23:59:59'))->orderBy('created_at','asc')->pluck('created_at');
    foreach ($all_users as $key => $value) {
        $cur_date = date("Y-m-d",strtotime($value));
        $all_date_user_count[$cur_date] = $all_date_user_count[$cur_date]+1;
    }
    $chunk_all_date_user_count = array_chunk($all_date_user_count, 8);
    return array('series_data_01'=>$chunk_all_date_user_count[0],'series_data_02'=>$chunk_all_date_user_count[1]);
}

function topics_per_day($last_start_day,$end_day){
    $all_date = printDates($last_start_day,$end_day);
    $all_date_user_count = array();
    foreach ($all_date as $key => $value) {
        $all_date_user_count[$value] = 0;
    }
    $all_users = Db::table('topics')->whereBetween('created_at',array($last_start_day.' 00:00:00',$end_day.' 23:59:59'))->orderBy('created_at','asc')->pluck('created_at');
    foreach ($all_users as $key => $value) {
        $cur_date = date("Y-m-d",strtotime($value));
        $all_date_user_count[$cur_date] = $all_date_user_count[$cur_date]+1;
    }
    $chunk_all_date_user_count = array_chunk($all_date_user_count, 8);
    return array('series_data_01'=>$chunk_all_date_user_count[0],'series_data_02'=>$chunk_all_date_user_count[1]);
}

