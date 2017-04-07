<?php

namespace Encore\Admin\Seeder;

use Illuminate\Database\Seeder;
use Mallto\Mall\Data\AdminUser;
use Mallto\Mall\Data\Subject;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BaseTablesSeeder::class);
        $this->call(MenuTablesSeeder::class);
        $this->call(PermissionTablesSeeder::class);
    }
}
