<?php

namespace Encore\Admin\Seeder;

use Encore\Admin\Auth\Database\Permission;
use Illuminate\Database\Seeder;
use Mallto\Mall\Data\AdminUser;
use Mallto\Mall\Data\Subject;

class PermissionTablesSeeder extends Seeder
{

    protected $routeNames = [
        "index"   => "查看",  //列表页/详情页
        "create"  => "创建/修改", //创建页/保存/修改
        "destroy" => "删除", //删除权限
    ];

    protected $order = 0;

    private function createPermissions($name, $slug, $sub = true, $parentId = 0, $isCommom = false)
    {
        $temp = Permission::create([
            "parent_id" => $parentId,
            'order'     => $this->order += 1,
            "name"      => $name."管理",
            "slug"      => $slug,
            "common"    => $isCommom,
        ]);

        $parentId = $temp->id;

        if ($sub) {
            foreach ($this->routeNames as $routeName => $permissionName) {
                Permission::create([
                    'parent_id' => $parentId,
                    'order'     => $this->order += 1,
                    "name"      => $name.$permissionName,
                    "slug"      => $slug.".".$routeName,
                    "common"    => $isCommom,
                ]);
            }
        }

        return $parentId;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * ------------------------  主体  ---------------------------
         */
        $this->createPermissions("主体", "subjects", true, 0, true);

        /**
         * ------------------------  账户  ---------------------------
         */
        $this->createPermissions("账户", "admins", true, 0, true);


        /**
         * ------------------------  角色  ---------------------------
         */
        $this->createPermissions("角色", "roles", true, 0, true);


        /**
         * ------------------------  权限  ---------------------------
         */
//        $this->createPermissions("权限", "permissions");


        /**
         * ------------------------  菜单  ---------------------------
         */
//        $this->createPermissions("菜单", "menus");

        /**
         * ------------------------  报表  ---------------------------
         */
        $this->createPermissions("报表", "reports");

        /**
         * ------------------------  操作日志  ---------------------------
         */
//        $this->createPermissions("操作日志", "logs");
    }
}
