<?php

namespace Encore\Admin\Seeder;

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
        $this->call(BaseTablesSeeder::class);
        $this->call(MenuTablesSeeder::class);
        $this->call(PermissionTablesSeeder::class);
    }
}
