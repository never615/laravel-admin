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
//        Administrator::truncate();
        Administrator::create([
            'username'  => 'mallto',
            'password'  => bcrypt('owner'),
            'name'      => '墨兔科技',
            'adminable_id'=>1,
            'adminable_type'=>'subject'
        ]);
        Administrator::create([
            'username'  => 'admin',
            'password'  => bcrypt('admin'),
            'name'      => '管理员',
            'adminable_id'=>1,
            'adminable_type'=>'subject'
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name'  => '项目拥有者',
            'slug'  => 'owner',
        ]);
        Role::create([
            'name'  => '管理员',
            'slug'  => 'administrator',
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
                'title'     => '控制面板',
                'icon'      => 'fa-bar-chart',
                'url'       => 'dashboard',
            ],
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => '管理',
                'icon'      => 'fa-tasks',
                'url'       => '',
            ],
            [
                'parent_id' => 2,
                'order'     => 3,
                'title'     => '账号',
                'icon'      => 'fa-users',
                'url'       => 'admin.index',
            ],
            [
                'parent_id' => 2,
                'order'     => 4,
                'title'     => '角色',
                'icon'      => 'fa-user',
                'url'       => 'roles.index',
            ],
            [
                'parent_id' => 2,
                'order'     => 5,
                'title'     => '权限',
                'icon'      => 'fa-user',
                'url'       => 'permissions.index',
            ],
            [
                'parent_id' => 2,
                'order'     => 6,
                'title'     => '菜单',
                'icon'      => 'fa-bars',
                'url'       => 'menu.index',
            ],
            [
                'parent_id' => 2,
                'order'     => 7,
                'title'     => '操作日志',
                'icon'      => 'fa-history',
                'url'       => 'logs.index',
            ],
            [
                'parent_id' => 0,
                'order'     => 8,
                'title'     => 'Helpers',
                'icon'      => 'fa-gears',
                'url'       => '',
            ],
            [
                'parent_id' => 8,
                'order'     => 9,
                'title'     => 'Scaffold',
                'icon'      => 'fa-keyboard-o',
                'url'       => 'scaffold.index',
            ],
            [
                'parent_id' => 8,
                'order'     => 10,
                'title'     => 'Database terminal',
                'icon'      => 'fa-database',
                'url'       => 'database.index',
            ],
            [
                'parent_id' => 8,
                'order'     => 11,
                'title'     => 'Laravel artisan',
                'icon'      => 'fa-terminal',
                'url'       => 'artisan.index',
            ],
        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
        Menu::find(8)->roles()->save(Role::first());

//        Menu::find(2)->roles()->save(Role::where('id',2)->first());


    }
}
