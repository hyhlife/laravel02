<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeedRolesAndPermissionsData extends Migration
{
    public function up()
    {
        // 需清除缓存，否则会报错
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 先创建权限

        // categories
        Permission::create(['name' => 'manage_categories']);
        Permission::create(['name' => 'manage_categories_delete']);
        Permission::create(['name' => 'manage_categories_add']);
        Permission::create(['name' => 'manage_categories_edit']);

        // topics
        Permission::create(['name' => 'manage_topics']);
        Permission::create(['name' => 'manage_topics_delete']);
        Permission::create(['name' => 'manage_topics_add']);
        Permission::create(['name' => 'manage_topics_edit']);

        // replies
        Permission::create(['name' => 'manage_replies']);
        Permission::create(['name' => 'manage_replies_delete']);
        Permission::create(['name' => 'manage_replies_add']);
        Permission::create(['name' => 'manage_replies_edit']);

        // links
        Permission::create(['name' => 'manage_links']);
        Permission::create(['name' => 'manage_links_delete']);
        Permission::create(['name' => 'manage_links_add']);
        Permission::create(['name' => 'manage_links_edit']);

        // users
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_users_delete']);
        Permission::create(['name' => 'manage_users_add']);
        Permission::create(['name' => 'manage_users_edit']);

        // roles
        Permission::create(['name' => 'manage_roles']);
        Permission::create(['name' => 'manage_roles_delete']);
        Permission::create(['name' => 'manage_roles_add']);
        Permission::create(['name' => 'manage_roles_edit']);

        // permissions
        Permission::create(['name' => 'manage_permissions']);
        Permission::create(['name' => 'manage_permissions_delete']);
        Permission::create(['name' => 'manage_permissions_add']);
        Permission::create(['name' => 'manage_permissions_edit']);

        // settings
        Permission::create(['name' => 'manage_settings']);

        // 创建超级管理员角色，并赋予权限
        $founder = Role::create(['name' => '超级管理员']);
        $founder->givePermissionTo('manage_categories');
        $founder->givePermissionTo('manage_categories_delete');
        $founder->givePermissionTo('manage_categories_add');
        $founder->givePermissionTo('manage_categories_edit');

        $founder->givePermissionTo('manage_topics');
        $founder->givePermissionTo('manage_topics_delete');
        $founder->givePermissionTo('manage_topics_add');
        $founder->givePermissionTo('manage_topics_edit');

        $founder->givePermissionTo('manage_replies');
        $founder->givePermissionTo('manage_replies_delete');
        $founder->givePermissionTo('manage_replies_add');
        $founder->givePermissionTo('manage_replies_edit');

        $founder->givePermissionTo('manage_links');
        $founder->givePermissionTo('manage_links_delete');
        $founder->givePermissionTo('manage_links_add');
        $founder->givePermissionTo('manage_links_edit');

        $founder->givePermissionTo('manage_users');
        $founder->givePermissionTo('manage_users_delete');
        $founder->givePermissionTo('manage_users_add');
        $founder->givePermissionTo('manage_users_edit');

        $founder->givePermissionTo('manage_roles');
        $founder->givePermissionTo('manage_roles_delete');
        $founder->givePermissionTo('manage_roles_add');
        $founder->givePermissionTo('manage_roles_edit');

        $founder->givePermissionTo('manage_permissions');
        $founder->givePermissionTo('manage_permissions_delete');
        $founder->givePermissionTo('manage_permissions_add');
        $founder->givePermissionTo('manage_permissions_edit');

        $founder->givePermissionTo('manage_settings');

        // 创建管理员角色，并赋予权限
        $maintainer = Role::create(['name' => '管理员']);
        $maintainer->givePermissionTo('manage_categories');
        $maintainer->givePermissionTo('manage_categories_delete');
        $maintainer->givePermissionTo('manage_categories_add');
        $maintainer->givePermissionTo('manage_categories_edit');

        $maintainer->givePermissionTo('manage_topics');
        $maintainer->givePermissionTo('manage_topics_delete');
        $maintainer->givePermissionTo('manage_topics_add');
        $maintainer->givePermissionTo('manage_topics_edit');

        $maintainer->givePermissionTo('manage_replies');
        $maintainer->givePermissionTo('manage_replies_delete');
        $maintainer->givePermissionTo('manage_replies_add');
        $maintainer->givePermissionTo('manage_replies_edit');

        $maintainer->givePermissionTo('manage_users');
        $maintainer->givePermissionTo('manage_users_delete');
        $maintainer->givePermissionTo('manage_users_add');
        $maintainer->givePermissionTo('manage_users_edit');
    }

    public function down()
    {
        // 需清除缓存，否则会报错
        app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');

        Model::unguard();
        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        Model::reguard();
    }
}