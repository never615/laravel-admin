<?php

namespace Encore\Admin\Seeder;

use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;
use Mallto\Mall\Data\AdminUser;
use Mallto\Mall\Data\Subject;

class BaseTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * --------------------   Subject create  -------------------------
         */
        Subject::create([
            'name' => "墨兔科技",
        ]);

        Subject::create([
            'name'      => "招商集团",
            "parent_id" => 1,
        ]);

        Subject::create([
            'name'      => "海上世界",
            "parent_id" => 2,
        ]);

        /**
         * -----------------------  Role create  --------------------------------
         */
        $ownerRole = Role::create([
            "name"       => "项目拥有者",
            "slug"       => "owner",
            "subject_id" => 1,
        ]);

        $bigAdminRole = Role::create([
            "name"       => "招商管理员",
            "slug"       => "admin",
            "subject_id" => 2,
        ]);

        $commonAdminRole = Role::create([
            "name"       => "海上世界管理员",
            "slug"       => "admin",
            "subject_id" => 3,
        ]);

        /**
         * --------------------------------  Admin_user create   ------------------------------
         */
        $mallto = AdminUser::create([
            'username'       => 'mallto',
            'password'       => bcrypt('mallto'),
            'name'           => '墨兔科技管理',
            "subject_id"     => 1,
            "adminable_id"   => 1,
            "adminable_type" => "subject",
        ]);

        $招商 = AdminUser::create([
            'username'       => 'zhaoshang',
            'password'       => bcrypt('zhaoshang'),
            'name'           => '招商地产管理',
            "subject_id"     => 2,
            "adminable_id"   => 2,
            "adminable_type" => "subject",
        ]);

        $seaworld = AdminUser::create([
            'username'       => 'seaworld',
            'password'       => bcrypt('seaworld'),
            'name'           => '海上世界管理',
            "subject_id"     => 3,
            "adminable_id"   => 3,
            "adminable_type" => "subject",
        ]);


        // add role to user.
        $mallto->roles()->save($ownerRole);
        $招商->roles()->save($bigAdminRole);
        $seaworld->roles()->save($commonAdminRole);


    }
}
