<?php

namespace Encore\Admin\Auth\Database;

use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create a user.
        Administrator::truncate();
        Administrator::create([
            'username'  => 'mallto',
            'password'  => bcrypt('owner'),
            'name'      => '墨兔科技',
        ]);
        Administrator::create([
            'username'  => 'admin',
            'password'  => bcrypt('admin'),
            'name'      => '管理员',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name'  => 'Owner',
            'slug'  => '项目拥有者',
        ]);
        Role::create([
            'name'  => 'Administrator',
            'slug'  => '项目管理员',
        ]);

        // add role to user.
        //设置角色项目拥有者给账号mallto
        Administrator::first()->roles()->save(Role::first());
        //设置角色项目管理员设置给账号admin
        Administrator::where('id',2)->first()->roles()->save(Role::where('id',2)->first());

        // add default menus.
        Menu::truncate();
        Menu::insert([
            [
                'parent_id' => 0,
                'order'     => 1,
                'title'     => '控制中心',
                'icon'      => 'fa-bar-chart',
                'route_name'       => 'index',
            ],
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => '管理',
                'icon'      => 'fa-tasks',
                'route_name'       => '',
            ],
            [
                'parent_id' => 2,
                'order'     => 3,
                'title'     => '账号',
                'icon'      => 'fa-users',
                'route_name'       => 'auth/users.index',
            ],
            [
                'parent_id' => 2,
                'order'     => 4,
                'title'     => '角色',
                'icon'      => 'fa-user',
                'route_name'       => 'auth/roles.index',
            ],
            [
                'parent_id' => 2,
                'order'     => 5,
                'title'     => '权限',
                'icon'      => 'fa-user',
                'route_name'       => 'auth/permissions.index',
            ],
            [
                'parent_id' => 2,
                'order'     => 6,
                'title'     => '菜单',
                'icon'      => 'fa-bars',
                'route_name'       => 'auth/menu.index',
            ],
            [
                'parent_id' => 2,
                'order'     => 7,
                'title'     => '操作日志',
                'icon'      => 'fa-history',
                'route_name'       => 'auth/logs.index',
            ],
            [
                'parent_id' => 0,
                'order'     => 8,
                'title'     => 'Helpers',
                'icon'      => 'fa-gears',
                'route_name'       => '',
            ],
            [
                'parent_id' => 8,
                'order'     => 9,
                'title'     => 'Scaffold',
                'icon'      => 'fa-keyboard-o',
                'route_name'       => 'helpers/scaffold.index',
            ],
            [
                'parent_id' => 8,
                'order'     => 10,
                'title'     => 'Database terminal',
                'icon'      => 'fa-database',
                'route_name'       => 'terminal.database',
            ],
            [
                'parent_id' => 8,
                'order'     => 11,
                'title'     => 'Laravel artisan',
                'icon'      => 'fa-terminal',
                'route_name'       => 'terminal.artisan',
            ],
        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
        Menu::find(8)->roles()->save(Role::first());

//        Menu::find(2)->roles()->save(Role::where('id',2)->first());


    }
}
