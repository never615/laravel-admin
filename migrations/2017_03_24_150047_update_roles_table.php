<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('admin.database.roles_table'), function ($table) {
            $table->unsignedInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('CASCADE');

            $table->text('describe')->nullable();

            //索引
            $table->index(['subject_id']);
            $table->unique(['subject_id', 'slug']);
            $table->unique(['subject_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('admin.database.roles_table'), function ($table) {
            $table->dropColumn('subject_id');
            $table->dropColumn('describe');
            $table->dropIndex(['subject_id']);
            $table->dropUnique(['subject_id', 'slug']);
            $table->dropUnique(['subject_id', 'name']);
        });
    }
}
